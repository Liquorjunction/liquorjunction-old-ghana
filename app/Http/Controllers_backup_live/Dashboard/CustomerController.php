<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\MainUser;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use App\Models\Country;
use Mail;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;
use App\Models\LoyaltyPoints;


class CustomerController extends Controller
{
    //
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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'read');
        if($check_view_permission==false){
            abort(404);
        }        
    }

    public function index()
    {
        // echo "string";exit();
        return view("dashboard.customer.list");
    }

    public function indexpending()
    {
        // echo "string";exit();
        return view("dashboard.customer.pendinglist");
    }

     public function store(Request $request)
    {
        $customer_id = $request->customer_id;
        // echo "<pre>";print_r($request->toArray());exit;
        $validator = \Validator::make($request->all(), [
                    'first_name' => [
                        'required',
                        'max:30',
                    ],
                    'last_name' => [
                        'required',
                        'max:30',
                    ],
                    'email' => [
                                'required',
                                    Rule::unique('main_users')
                                    ->ignore($customer_id)
                                    ->where(function ($query) use ($customer_id) {
                                     return $query->where('status', '!=', '2');
                        })],
                    'phone' => ['required',
                        'max:15',             
                        Rule::unique('main_users')->ignore($customer_id)
                        ->where(function ($query) use ($customer_id) {
                            return $query->where('status', '!=', '2');
                    })],
                    // 'profile' => ['mimes:png,jpeg,jpg','max:2048'],
                    // 'phonecode'=>['required'],
                    
                ],
                [
                    // 'profile.mimes'=>'The profile photo must be in .png,.jpg or.jpeg format.',
                    // 'profile.max'=>'The profile photo size less than 2MB .'
                ],);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
            
            // $formFileName = "profile";
            // $fileFinalName_ar = "";
            // if ($request->$formFileName != "") {
            //     $fileFinalName_ar = time() . rand(1111,
            //             9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            //         $path = $this->getUploadPath();
            //         // echo "<pre>";print_r($path);exit();
            //         $request->file($formFileName)->move($path, $fileFinalName_ar);

            //         $customer = MainUser::where('id', $customer_id)->update(array(
            //     'profile' => $fileFinalName_ar,
            // ));
            // }

             $customer = MainUser::where('id', $customer_id)->update(array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'phone_code'=>$request->phone_code,
            ));
            // dd($customer);
            Alert::success('Success', __('backend.Customer_has_been_updated_successfully'));
             return response()->json(['success' => 'true']);
        
    }

    public function customerUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;

                $orderData = DB::table('order')->wherein('user_id',$ids)->where('order_status','in',[0,1])->get();
                // echo "<pre>";print_r($orderData);exit();
                if (!empty($orderData->toArray())) {
                    return response()->json(['success' => true,'msg'=>'This user order is ongoing so you can not delete and inactive.']);
                }
                // echo "<pre>";print_r($orderData);exit();
                
               
                if($status == 2){
                    MainUser::wherein('id', $ids)->update(['status' => $status,'deleted_by'=>2]);
                    return response()->json(['success' => true,'msg'=>'Record(s) delete successfully']);
                  }else if($status == 0){
                    MainUser::wherein('id', $ids)->update(['status' => $status]);
                   return response()->json(['success' => true,'msg'=>'Record(s) deactive successfully']);
                  }else{
                    MainUser::wherein('id', $ids)->update(['status' => $status]);
                   return response()->json(['success' => true,'msg'=>'Record(s) active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function customerPendingUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;

                // echo "<pre>";print_r($orderData);exit();
                MainUser::wherein('id', $ids)->update(['technician_status' => $status]);

                foreach ($ids as $data) {
                    // echo "<pre>";print_r($data);
                    // $product_data = DB::table('product')->where('id',$data)->first();
                    $user_data = DB::table('main_users')->where('id',$data)->first();
                    // echo "<pre>";print_r($user_data);exit();
                    $name = $user_data->first_name.' '.$user_data->last_name;
                    if ($status==1) {
                        
                    $ismail = $this->attachment_approve_email($user_data->email,$name);
                    }else{
                    $ismail = $this->attachment_reject_email($user_data->email,$name);

                    }
                }
               
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record(s) reject successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Record(s) deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Record(s) approve successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function attachment_approve_email($email,$name) {

       
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('12');
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'name' => $name, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        Mail::send('dashboard.technician_approve', $data, function ($message) use ($data) {

         $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

    }

    public function attachment_reject_email($email,$name) {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('13');
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'name' => $name, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        Mail::send('dashboard.technician_reject', $data, function ($message) use ($data) {

        $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

    }

    public function edit(Request $request)
    {
        if ($request->ajax())
        {   
            $customer_id = $request->customer_id;   
            $customerData = MainUser::where('id',$customer_id)->where('status','!=',2)->first();
            if(!empty($customerData))
            {
                $phonecode = Country::where('status','!=',2)
                            ->orderBy('phonecode','ASC')
                            ->get();    
                $html =  view('dashboard.customer.edit')->with(['customerData' => $customerData,'phonecode' => $phonecode])->render();
                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
        }
    }

    public function show($id)
    {
        $customer_id = $id;
       $customerData = MainUser::leftjoin('countries','countries.phonecode','=','main_users.phone_code')->select('main_users.*','countries.phonecode as phonecode')->where('main_users.id',$customer_id)->where('main_users.status','!=',2)->first();
       $phonecode = Country::where('status','!=',2)->get();
        // echo "<pre>";print_r($customerData->toArray());exit();
        return view('dashboard.customer.show', compact('customerData'));
    }

    public function pendinglistshow($id)
    {
        $customer_id = $id;
       $customerData = MainUser::leftjoin('categories','categories.id','=','main_users.category_id')->select('main_users.*','categories.title as category_name')->where('main_users.id',$customer_id)->where('main_users.status','!=',2)->first();
        // echo "<pre>";print_r($customerData->toArray());exit();
        return view('dashboard.customer.pendingshow', compact('customerData'));
    }

    public function destroy($id)
    {
        $customer = MainUser::find($id);
        $customer->status = 2;
        $customer->deleted_by = 2;
        $customer->save();
        return redirect()->route('customer')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request){
        $customer_id = $request->customer_id;
         $orderData = DB::table('order')->where('user_id',$customer_id)->where('order_status','in',[0,1])->get();
                // echo "<pre>";print_r($orderData);exit();
                if (!empty($orderData->toArray())) {
                    
                    Alert::warning('Warning', __('This user order is ongoing so you can not delete and inactive'));
         return response()->json(['success' => 'true']);
                }else{
         MainUser::where('id', $customer_id)->update(['status' => 0]);
                Alert::success('Success', __('backend.Customer_deactive_sucessfully'));
         return response()->json(['success' => 'true']); 
                }
         
    }

    public function status_inactive(Request $request){
        $customer_id = $request->customer_id;
         MainUser::where('id', $customer_id)->update(['status' => 1]);
         Alert::success('Success', __('backend.Customer_active_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function anyData(Request $request)
    {   
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $status_dropdown = $request->get('status');
        
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        // if ($columnIndex==0) {
        //     $sort='id';
        // }elseif ($columnIndex==1) {
        //      $sort='first_name';
        // }elseif ($columnIndex==2) {
        //     $sort='email';
        // }elseif ($columnIndex==3) {
        //     $sort='phone';
        // }
        // elseif($columnIndex==4){
        //     $sort='created_at';
        // }else{
        //     $sort='id';
        // }

        $columnMap = [
            1 => 'first_name',
            2 => 'email',
            3 => 'phone',
            4 => 'earned_points',
            5 => 'spent_points',
            6 => 'remaining_points',
            8 => 'created_at'
        ];
        
        $sort = $columnMap[$columnIndex] ?? 'id';


        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }
        // $totalAr = DB::table('main_users')
        //             ->leftjoin('countries','countries.phonecode','=','main_users.phone_code')
        //             ->select('main_users.*','countries.phonecode as phonecode')
        //             ->where('main_users.user_type',1)
        //             ->where('main_users.status','!=',2)
        //             ->groupBy('main_users.id');

        $totalAr = DB::table('main_users')
                ->leftJoin('countries', 'countries.phonecode', '=', 'main_users.phone_code')
                ->leftJoin(DB::raw('(SELECT user_id, SUM(points) as earned_points FROM loyalty_points WHERE type = "credit" AND order_id IN (SELECT order_id FROM `order` WHERE order_status = 3) GROUP BY user_id) as earned'), 'earned.user_id', '=', 'main_users.id')
                ->leftJoin(DB::raw('(SELECT user_id, SUM(points) as spent_points FROM loyalty_points WHERE type = "debit" GROUP BY user_id) as spent'), 'spent.user_id', '=', 'main_users.id')
                ->select(
                    'main_users.*',
                    'countries.phonecode as phonecode',
                    DB::raw('COALESCE(earned.earned_points, 0) as earned_points'),
                    DB::raw('COALESCE(spent.spent_points, 0) as spent_points'),
                    DB::raw('(COALESCE(earned.earned_points, 0) - COALESCE(spent.spent_points, 0)) as remaining_points')
        );


        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('main_users.created_at', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('main_users.created_at', '<=', $min_date . ' 23:59:59');
        }
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('first_name', 'like', '%' . $searchValue . '%')
                     ->orWhere('email', 'like', '%' . $searchValue . '%')
                     ->orWhere('countries.phonecode', 'like', '%' . $searchValue . '%')
                     ->orWhere('phone', 'like', '%' . $searchValue . '%');
            });
        }
        if ($status_dropdown != "") {
            $totalAr->where('main_users.status',$status_dropdown);
        }
        // $totalRecords = $totalAr->get()->count();
        $totalRecords = $totalAr->count();


         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr=[];
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'read'); 
        $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'create'); 
        $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'update');
        $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'delete');
        $active_class = "";
        $inactive_class = "role_status_inactive";
        if(isset($check_updation_permission) && $check_updation_permission){
            $active_class = "status_active";
            $inactive_class = "status_inactive";
        }
        foreach ($totalAr as $key => $data) 
        {
            $categoryShow =  route('customer.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->id]);

            $earnedPoints = LoyaltyPoints::where('user_id', $data->id)
            ->where('type', 'credit')
            ->where('status', 1)
            ->whereIn('order_id', function($subQuery) {
                $subQuery->select('order_id')
                    ->from('order') 
                    ->where('order_status', 3);
            })
            ->sum('points');
            
            $spentPoints = LoyaltyPoints::where('user_id', $data->id)
                ->where('type', 'debit')
                ->where('status', 1)
                ->whereIn('order_id', function($subQuery) {
                    $subQuery->select('order_id')
                        ->from('order') 
                        ->where('order_status', 3);
                })
                ->sum('points');

            $remainingPoints = $earnedPoints - $spentPoints;

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline '.$active_class.'" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline '.$inactive_class.'" title="Deactive" data-id="'.$data->id.'"></i>';
            }

            $options = '';
            if(isset($check_view_permission) && $check_view_permission){
                $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$categoryShow.'" title="Show"> </a>';
            }
            if(isset($check_updation_permission) && $check_updation_permission){
                $options .= '<a class="btn btn-sm success paddingset edit-customer" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            }
            if(isset($check_deletion_permission) && $check_deletion_permission){
                $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   
            }
            if($data->label_type == 1){
                $browser_type = "Web";
            }else{
                $browser_type = "Mobile";

            }

            $date = \Helper::converttimeTozone($data->created_at);
            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
            //   "id" =>   isset($data->id) ? $data->id : '' ,
              "customer_name" =>   ucfirst($data->first_name.' '.$data->last_name) ,
              "customer_email" =>   isset($data->email) ? $data->email : '' ,
              "customer_phone" => $data->phonecode ? '+' . $data->phonecode . ' ' . $data->phone : $data->phone,
              "earned_points" => isset($earnedPoints) ? rtrim(rtrim(number_format($earnedPoints, 2, '.', ''), '0'), '.') : '0',
              "spent_points" => isset($spentPoints) ? rtrim(rtrim(number_format($spentPoints, 2, '.', ''), '0'), '.') : '0',
              "remaining_points" => isset($remainingPoints) ? rtrim(rtrim(number_format($remainingPoints, 2, '.', ''), '0'), '.') : '0',
              "browser_type" =>   isset($browser_type) ? $browser_type : '' ,
              // "customer_join_date" =>   isset($data->created_at) ? $data->created_at : '' ,
              "customer_join_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
              "status" =>   isset($status) ? $status : '' ,
              //"is_technician" =>   isset($is_technician) ? $is_technician : '' ,
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

    public function anyDataPending(Request $request)
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
             $sort='first_name';
        }elseif ($columnIndex==2) {
            $sort='email';
        }elseif ($columnIndex==3) {
            $sort='phone';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = MainUser::where('status','!=','2')->where('technician_status','!=','3')->where('is_otp_verify',1);


        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('main_users.created_at', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('main_users.created_at', '<=', $min_date . ' 23:59:59');
        }
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('first_name', 'like', '%' . $searchValue . '%')
                     ->orWhere('email', 'like', '%' . $searchValue . '%')
                     ->orWhere('phone', 'like', '%' . $searchValue . '%');
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
            // $categoryShow =  route('category.show',['id'=>$data->id]);
            $categoryShow =  route('customer.pendinglistshow',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->id]);

            if ($data->technician_status == 1) {
                $is_technician = '<i class="fa fa-check text-success inline"><spna class="hide">Yes</span></i>';
            } else {
                $is_technician = '<i class="fa fa-times text-danger inline"><span class="hide">No</span></i>';
            }

             if ($data->technician_status == 1) {
                $status = '<button type="button" class="btn btn-success pointer_button">
                <span class="badge  badge-success">Approve</span>
              </button>';
            } elseif ($data->technician_status ==2) {
                 $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Reject</span>
              </button>';
            }else {
                $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Not Verify</span>
              </button>';
            }
         


            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-customer" data-id="'.$data->id.'" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$categoryShow.'" title="Show"> </a>';
            
            // $options .= '<a class="btn btn-sm success paddingset edit-customer" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $date = \Helper::converttimeTozone($data->created_at);

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "customer_name" =>   $data->first_name.' '.$data->last_name ,
              "customer_email" =>   isset($data->email) ? $data->email : '' ,
              "customer_phone" =>   isset($data->phone) ? $data->phone : '' ,
            //   "browser_type" =>   isset($browser_type ) ? $browser_type : '' ,
              // "customer_join_date" =>   isset($data->created_at) ? $data->created_at : '' ,
              "customer_join_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
              "status" =>   isset($status) ? $status : '' ,
              "is_technician" =>   isset($is_technician) ? $is_technician : '' ,
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