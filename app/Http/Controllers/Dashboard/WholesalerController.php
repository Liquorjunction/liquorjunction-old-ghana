<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\MainUser;
use App\Models\WholesalerInviteLink;
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

class WholesalerController extends Controller
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
        
    }
    public function index()
    {
        // echo "string";exit();
        return view("dashboard.wholesaler.list");
    }

    public function indexinvite()
    {
        // echo "string";exit();
        return view("dashboard.wholesaler.invitelist");
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;
        $validator = \Validator::make($request->all(), [
                    'first_name' => ['required','regex:/^[a-zA-Z]+$/u'],'last_name' => ['required','regex:/^[a-zA-Z]+$/u'],]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
            $customer_id = $request->customer_id;
            $formFileName = "profile";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(1111,
                        9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                    $path = $this->getUploadPath();
                    // echo "<pre>";print_r($path);exit();
                    $request->file($formFileName)->move($path, $fileFinalName_ar);

                    $customer = MainUser::where('id', $customer_id)->update(array(
                'profile' => $fileFinalName_ar,
            ));
            }
             $customer = MainUser::where('id', $customer_id)->update(array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ));
             Alert::success('Success', __('backend.Wholesaler_has_been_updated_successfully'));
             return response()->json(['success' => 'true']);
        
    }

    public function invitelink(Request $request){
        $email = explode(',', $request->email);
        // echo "<pre>";print_r($email);exit();
        foreach ($email as $data) {
            // $uniqid = uniqid();
            $invite_user = new WholesalerInviteLink();
            $validator = \Validator::make($request->all(), [
                'email'=>'required|regex:/(.+)@(.+)\.(.+)/i'
            ]);
            if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
            $uniqid = uniqid();
            $invite_user->email = $data;
            $invite_user->uniqid = $uniqid;

            $invite_user->status = 1;
            $invite_user->save();
            $ismail = $this->attachment_invitelink_email($data,$invite_user->id);
        }
        Alert::success('Success', __('backend.New_Wholesaler_invite_send_successfully'));
            return response()->json(['success' => 'true']);
    }

    public function attachment_invitelink_email($email,$supplier_id) {

       
        $setting = Setting::find(1);
         $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('3');
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'supplier_id' => $supplier_id, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        Mail::send('dashboard.invite_link', $data, function ($message) use ($data) {

        $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

    }

    public function edit(Request $request)
    {
        // $Faq = Categories::find($id);
        if ($request->ajax())
        {   
            $customer_id = $request->customer_id;
            $customerData = MainUser::where('id',$customer_id)->where('status','!=',2)->first();
            if(!empty($customerData))
            {
                $html =  view('dashboard.wholesaler.edit')->with(['customerData' => $customerData])->render();

 

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
        }
    }

    public function show(Request $request)
    {
        $customer_id = $request->customer_id;
       $customerData = MainUser::where('id',$customer_id)->where('status','!=',2)->first();
        
         if(!empty($customerData))
            {
                $html =  view('dashboard.wholesaler.show')->with(['customerData' => $customerData])->render();

 

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
    }

    public function wholesalerUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                MainUser::wherein('id', $ids)->update(['status' => $status]);
               
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Wholesaler deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Wholesaler active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function destroy($id)
    {
        $customer = MainUser::find($id);
        $customer->status = 2;
        $customer->save();

        return redirect()->route('wholesaler')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request){
        $customer_id = $request->customer_id;
         MainUser::where('id', $customer_id)->update(['status' => 0]);
         Alert::success('Success', __('backend.Wholesaler_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        $customer_id = $request->customer_id;
         MainUser::where('id', $customer_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.Wholesaler_active_sucessfully'));
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
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = MainUser::where('status','!=','2')->where('user_type',2);
        // $totalAr = MainUser::where('main_users.status','!=','2')->leftjoin('product','product.supplier_id','=','main_users.id')->select('main_users.*',DB::raw('COUNT(product.id) as total_product'))->where('main_users.user_type',2);

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
                 $query->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                     // ->orWhere('last_name', 'like', '%' . $searchValue . '%')
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
            $productCount = DB::table('product')->where('supplier_id',$data->id)->where('status','!=',2)->count();
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->id]);

             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }

            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-wholesaler" data-id="'.$data->id.'" title="Show"> </a>';

            
            $options .= '<a class="btn btn-sm success paddingset edit-wholesaler" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $date = \Helper::converttimeTozone($data->created_at);    

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "customer_name" =>   $data->first_name.' '.$data->last_name ,
              "customer_email" =>   isset($data->email) ? $data->email : '' ,
              "customer_phone" =>   isset($data->phone) ? $data->phone : '' ,
              "productCount" =>   isset($productCount) ? $productCount : '' ,
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

    public function anyDataInvite(Request $request)
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
             $sort='email';
        }elseif ($columnIndex==2) {
            $sort='created_at';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = DB::table('wholesaler_invite_link')->where('status','!=','2');

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
                 $query->orWhere('email', 'like', '%' . $searchValue . '%');
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
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->id]);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" data-id="'.$data->id.'"></i>';
            // }

            // if ($data->status == 1) {
            //     $status = '<button type="button" class="btn btn-success">
            //     <span class="badge  badge-success">Accepted</span>
            //   </button>';
            // } else {
            //     $status = '<button type="button" class="btn btn-danger">
            //     <span class="badge  badge-danger">Pending</span>
            //   </button>';
            // }

            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-wholesalerinvite" data-id="'.$data->id.'" title="Show"> </a>';

            $date = \Helper::converttimeTozone($data->created_at);
            // $options .= '<a class="btn btn-sm success paddingset edit-wholesaler" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "email" =>   isset($data->email) ? $data->email : '' ,
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
