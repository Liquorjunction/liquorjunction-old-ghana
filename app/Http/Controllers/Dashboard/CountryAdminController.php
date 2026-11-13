<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Country;
use App\Models\MainUser;
use App\Models\User;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Mail;
use Session;
use Alert;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Profiler\Profile;

class CountryAdminController extends Controller
{
    private $uploadPath = "uploads/customer/";
    
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }

    public function __construct()
    {
        $this->middleware('auth');   
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,3,'read');
        if($check_view_permission==false){
            abort(404);
        }     
    }

    public function index()
    {
        $countries = Country::where('status', 1)->orderby('name', 'ASC')->get();
        return view("dashboard.country-admin.list",compact('countries'));
    }

    public function subadminUpdateAll(Request $request)
    {       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;               
                User::wherein('id', $ids)->update(['status' => $status]);               
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record(s) delete successfully']);
                }else if($status == 0){
                    return response()->json(['success' => true,'msg'=>'Record(s) deactive successfully']);
                }else{
                    return response()->json(['success' => true,'msg'=>'Record(s) active successfully']);
                }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);        
    }

    public function edit(Request $request)
    {
        if ($request->ajax())
        {   
            $customer_id = $request->customer_id;
            $countries = Country::where('status', 1)->orderby('name', 'ASC')->get();
            $customerData = User::where('id',$customer_id)->where('status','!=',2)->first();
            if(!empty($customerData))
            {
                $html =  view('dashboard.country-admin.edit')->with(['customerData' => $customerData,'countries'=>$countries])->render();
                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
        }
    }

    public function store(Request $request)
    {
        $customer_id = $request->customer_id;
        if (empty($request->customer_id)) {
            $validator = \Validator::make($request->all(), 
                    [
                        'fullname' => 'required|max:40',
                        'email' =>  ['required','email',
                                'regex:/(.+)@(.+)\.(.+)/i',
                                Rule::unique('users')
                                ->where(function ($query){
                                        return $query->where('status','!=',2);
                                    })
                                ],
                        'phone' =>  [
                                      'required',
                                      'digits_between:8,15',  
                                    ],
                        'country_id' => 'required',
                        'profile' => ['required',
                                'mimes:png,jpeg,jpg',
                                        'max:2048'],
                    ],
                    [
                       'country_id.required' => 'The country field is required',
                       'fullname.required' => 'The name field is required', 
                       'fullname.max' =>'The full name must not be greater than 40 characters',
                       'phone.required'=>'The phone number field is required',
                       'phone.digits_between'=>'Phone number field should allow 8 to 15 digits ',
                       'profile.required'=>'Profile photo field is required.',
                       'profile.mimes'=>'The profile photo must be in .png,.jpg or.jpeg format.',
                       'profile.max'=>'The profile photo should be less than 2 MB .',
                       'email.regex' =>'Please enter valid email address.',


                    ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $subadmin = new User();
            $formFileName = "profile";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(1111,
                        9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadPath();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
            }
            $password = Str::random(10);
            $subadmin->name = $request->fullname;
            $subadmin->email = $request->email;
            $subadmin->password = Hash::make($password);
            $subadmin->phone = $request->phone;
            $subadmin->photo = $fileFinalName_ar;
            $subadmin->status = 1;
            $subadmin->user_type = 2;
            $subadmin->country_id = $request->country_id;
            $subadmin->save();
            $ismail = $this->send_email_countryadmin($request->email,$password,$request->fullname);
            Alert::success('Success', __('backend.New_country_admin_created_successfully'));
            return response()->json(['success' => 'true']);
        }else{
            $customer_id = $request->customer_id;
            $validator = \Validator::make($request->all(), 
                    [
                        'fullname' => ['required','max:40'],
                        'email' =>  ['required','email',
                                     'regex:/(.+)@(.+)\.(.+)/i',
                                    Rule::unique('users')
                                ->ignore($customer_id)->where(function ($query){
                                        return $query->where('status','!=',2);
                                    })],
                        'phone' => [
                            'required',
                            'digits_between:8,15'
                        ],  
                        'country_id' => 'required',
                        'profile' => 'mimes:png,jpeg,jpg|max:2048',
                    ],
                    [
                       'country_id.required' => 'The country field is required',
                       'fullname.required' => 'The name field is required', 
                       'fullname.max' =>'The full name must not be greater than 40 characters',
                       'phone.required'=> 'The phone number field is required',
                        'phone.digits_between'=>'Phone number field should allow 8 to 15 digits ',
                        'profile.required'=>'Profile photo field is required.',
                        'profile.mimes'=>'The profile photo must be in .png,.jpg or.jpeg format.',
                        'email.regex' =>'Please enter valid email address.',
                        'profile.max'=>'The profile photo should be less than 2 MB .',
                         'phone.required'=>'The phone number field is required',


                      
 
                    ]

            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $formFileName = "profile";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(1111, 9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
                $customer = User::where('id', $customer_id)->update(array(
                'photo' => $fileFinalName_ar,
            ));
            }
             $customer = User::where('id', $customer_id)->update(array(
                'name' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_id'=>$request->country_id,
            ));
            Alert::success('Success', __('backend.Country_admin_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }
    }

    public function send_email_countryadmin($email,$password,$fullname) {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('4');
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'password' => $password,'fullname' => $fullname, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        // echo "<pre>";print_r($data);exit();
        Mail::send('dashboard.send_subadmin_register', $data, function ($message) use ($data) {

        $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

    }

    public function show(Request $request)
    {
        $customer_id = $request->customer_id;
        $customerData = User::where('id',$customer_id)->where('status','!=',2)->first();    
        $country_info = Country::where('id',$customerData->country_id)->first();    
        if(!empty($customerData))
        {
            $html =  view('dashboard.country-admin.show')->with(['customerData' => $customerData,'country_info'=>$country_info])->render();
            return response()->json(['success' => true,'html'=> $html]);
        }
        return response()->json(['success' => false,'msg'=> 'something wrong.']);
    }

    public function destroy($id)
    {
        $customer = User::find($id);
        $customer->status = 2;
        $customer->save();

        return redirect()->route('country-admin')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request){
        $customer_id = $request->customer_id;
         User::where('id', $customer_id)->update(['status' => 0]);
         Alert::success('Success', __('backend.countryAdmin_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        $customer_id = $request->customer_id;
         User::where('id', $customer_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.countryAdmin_active_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function anyData(Request $request)
    {   
        // echo "<pre>";print_r($request->toArray());exit;
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='id';
        }elseif ($columnIndex==1) {
             $sort='name';
        }elseif ($columnIndex==2) {
            $sort='email';
        }elseif ($columnIndex==3) {
            $sort='phone';
        }elseif($columnIndex==4){
            $sort = 'created_at';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = User::leftJoin('countries','countries.id','=','users.country_id')
                ->select('users.*','countries.name as country')
                ->where('users.status','!=','2')
                ->where('users.user_type',2);

        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('created_at', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('created_at', '<=', $min_date . ' 23:59:59');
        }
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('users.name', 'like', '%' . $searchValue . '%')
                     // ->orWhere('last_name', 'like', '%' . $searchValue . '%')
                     ->orWhere('users.email', 'like', '%' . $searchValue . '%')
                     ->orWhere('users.phone', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $categoryShow =  route('country-admin.show',['id'=>$data->id]);
            $categpryEdit =  route('country-admin.edit',['id'=>$data->id]);

             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }

            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-subadmin"  data-id="'.$data->id.'" title="Show"> </a>';

            
            $options .= '<a class="btn btn-sm success paddingset edit-subadmin" data-id="'.$data->id.'"  title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $date = \Helper::converttimeTozone($data->created_at);   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "customer_name" =>   $data->name ,
              "customer_email" =>   isset($data->email) ? $data->email : '' ,
              "customer_phone" =>   isset($data->phone) ? $data->phone : '' ,
              "country" =>   isset($data->country) ? $data->country : '' ,
              // "customer_join_date" =>   isset($data->created_at) ? $data->created_at : '' ,
              "customer_join_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
              "status" =>   isset($status) ? $status : '' ,
              "options" => isset($options) ? $options : '' ,
            );
          
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
         );
        echo json_encode($response);

    }
}