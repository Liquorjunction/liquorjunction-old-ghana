<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Setting;
use App\Models\Order;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use PDF;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class EarningReportController extends Controller
{
    //

    public function index()
    {
        // echo "string";exit();
        
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("dashboard.earningreport.list");
    }

     public function show($id)
    {
        // $label = Label::find($id);
        $supplier_id = $id;

        return view('dashboard.earningreport.productlist',compact('supplier_id'));
    }


    public function export_earningreport(Request $request){
        if(!empty($request->startdate && $request->enddate)) {
            // echo "<pre>";print_r($request->toArray());
           
            $frm_date = Carbon::createFromFormat('d-m-Y',$request->startdate)->format('Y-m-d');
                
            $to_date = Carbon::createFromFormat('d-m-Y',$request->enddate)->format('Y-m-d');
            
    $earning_report =  DB::table('main_users')->where('user_type',2)->where('status','!=','2')->get();

            
        }else {
            // $supplier_id = auth()->guard('main_user')->user()->id;

            $earning_report = DB::table('main_users')->where('user_type',2)->where('status','!=','2')->get();
        }
        // echo "<pre>";print_r($user_report->toArray());exit();
         $filename = 'earningreport' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $file = fopen('php://output', 'w');

        fputcsv($file,array('No', 'Name','Email','Phone','User Type','Total Earning','Join Date'));
        $i = 1;
        foreach ($earning_report as $key=>$data) {

            $total_earning = DB::table('transactions')->where('supplier_id',$data->id);

            if (isset($request->startdate)) {

            $min_date = Carbon::parse($request->startdate)->format('Y-m-d H:i:s');
            $total_earning->where('transaction_date', '>=', $min_date);
        }

        if (isset($request->enddate)) {
            $min_date = Carbon::parse($request->enddate)->format('Y-m-d');
            $total_earning->where('transaction_date', '<=', $min_date . ' 23:59:59');
        }
        $total_earning = $total_earning->sum('amount');
            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            $email = @$data->email;
            $phone = @$data->phone;

            $date = \Helper::converttimeTozone($data->created_at);

            $payment_mode = "";
            if ($data->user_type==1) {
                $user_type ="Customer";
            }elseif($data->user_type==2){

                $user_type ="Wholesaler";
            }else{
                $user_type ="Subadmin";

            }

            $no = $i;
            $username = isset($username) ? $username : '';
            $email = isset($email) ? $email : '';
            $phone = isset($phone) ? $phone : '';
            $user_type = isset($user_type) ? $user_type : '';
            $total_earning = isset($total_earning) ? $total_earning : '';
            $join_date = @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-";

            fputcsv($file,array($i,$username,$email,$phone,$user_type,$total_earning,$join_date));
            
        $i++;
        }
    }

    public function export_earningpdf(Request $request)
    {

        $start_date[] = $request->startdate;
        $end_date[] = $request->enddate;
        // echo "<pre>";print_r($request->toArray());exit();
        $totalAr = DB::table('main_users')->leftjoin('transactions','transactions.supplier_id','=','main_users.id')->leftjoin('users_payments','users_payments.supplier_id','=','main_users.id')->where('main_users.user_type',2)->groupby('main_users.id')->select('main_users.*',DB::raw('SUM(transactions.amount) as total_auction_amount'));

        

        $totalAr = $totalAr->get()->toArray();

        // echo "<pre>";print_r($start_date);exit();
        view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.earningreport.earning-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'earning_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);

        // echo "<pre>";print_r($request->toArray());exit();
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
        // $supplier_id = auth()->guard('main_user')->user()->id;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==1) {
            $sort='main_users.first_name';
        }elseif($columnIndex==2){
            $sort = 'main_users.email';
        }elseif($columnIndex==3){
            $sort = 'transactions.amount';
        }else{
            $sort='main_users.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        // $totalAr = Order::leftjoin('main_users','main_users.id','=','order.user_id')->leftjoin('main_users as supplier_data','supplier_data.id','=','order.supplier_id')->select('order.*','main_users.first_name','main_users.last_name','supplier_data.first_name as suppler_first_name','supplier_data.last_name as suppler_last_name');

        $totalAr = DB::table('main_users')
                ->leftjoin('transactions','transactions.supplier_id','=','main_users.id')
                ->leftjoin('users_payments','users_payments.supplier_id','=','main_users.id')
                ->where('main_users.user_type',2)->select('main_users.*');
        // echo "<pre>";print_r($totalAr);exit();
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                     // ->orWhere('last_name', 'like', '%' . $searchValue . '%')
                     ->orWhere('main_users.email', 'like', '%' . $searchValue . '%');
            });
        }



        $totalRecords = $totalAr->groupby('main_users.id')->get()->count();
         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->groupby('main_users.id')
            ->get();
        $data_arr=[];
        // echo "<pre>";print_r($totalAr->toArray());exit();
        foreach ($totalAr as $key => $data) 
        {
           $orderShow =  route('earningreport.show',['id'=>$data->id]);

            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            $total_earning = DB::table('transactions')->where('supplier_id',$data->id);

            if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $total_earning->where('transaction_date', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $total_earning->where('transaction_date', '<=', $min_date . ' 23:59:59');
        }
        $total_earning = $total_earning->sum('amount');
            // $supplername = urldecode(@$data->suppler_first_name).' '.urldecode(@$data->suppler_last_name);
            // $productname = urldecode(@$data->product_name);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }

            
            // $date = \Helper::converttimeTozone($data->order_date);
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$orderShow.'" title="Order List"> </a>';
            $settings = Setting::find(1);
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "username" =>   isset($username) ? $username : '' ,
              "email" =>   isset($data->email) ? $data->email : '' ,
              "total_amount" =>   @$settings->currency_symbol.' '. @$total_earning,
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

    public function productreportanyData(Request $request)
    {   
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        $supplier_id = $request->get('supplier_id');
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==1) {
            $sort='order.uniqid';
        }elseif ($columnIndex==1) {
             $sort='order.user_id';
        }elseif ($columnIndex==2) {
             $sort='order.supplier_id';
        }elseif ($columnIndex==3) {
            $sort='order.order_type';
        }elseif ($columnIndex==4) {
            $sort='order.total_amount';
        }elseif ($columnIndex==5) {
            $sort='order.payable_amount';
        }elseif ($columnIndex==6) {
            $sort='order.order_date';
        }else{
            $sort='order.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Order::leftjoin('main_users','main_users.id','=','order.user_id')->leftjoin('main_users as supplier_data','supplier_data.id','=','order.supplier_id')->select('order.*','main_users.first_name','main_users.last_name','supplier_data.first_name as suppler_first_name','supplier_data.last_name as suppler_last_name')->where('order.supplier_id',$supplier_id)->where('total_amount','!=',0);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('order.total_amount', 'like', '%' . $searchValue . '%')
                 ->orWhere('order.payable_amount', 'like', '%' . $searchValue . '%')
                 ->orWhere('order.uniqid', 'like', '%' . $searchValue . '%');
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
           $orderShow =  route('adminorder.show',['id'=>$data->id]);

            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            $supplername = urldecode(@$data->suppler_first_name).' '.urldecode(@$data->suppler_last_name);
            // $productname = urldecode(@$data->product_name);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }

            if ($data->status == 2) {
                $status = '<button type="button" class="btn btn-success">
                <span class="badge  badge-success">Complated</span>
              </button>';
            } elseif ($data->status ==0) {
                 $status = '<button type="button" class="btn btn-warning">
                <span class="badge  badge-danger">Pending</span>
              </button>';
            }elseif ($data->status ==1) {
                 $status = '<button type="button" class="btn btn-info">
                <span class="badge  badge-danger">Dispatched</span>
              </button>';
            }else {
                $status = '<button type="button" class="btn btn-danger">
                <span class="badge  badge-danger">Cancel</span>
              </button>';
            }

            if ($data->order_type == 1) {
                $order_type = '
                <span class="badge  badge-success">In Store</span>
              ';
            } elseif ($data->order_type ==2) {
                 $order_type = '
                <span class="badge  badge-danger">Online</span>
              ';
            }else {
                $order_type = '
                <span class="badge  badge-danger">In Store</span>
              ';
            }

            $date = \Helper::converttimeTozone($data->order_date);
            $settings = Setting::find(1);
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$orderShow.'" title="View"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->order_id) ? $data->order_id : '' ,
              "username" =>   isset($username) ? $username : '' ,
              "supplername" =>   isset($supplername) ? $supplername : '' ,
              "order_type" =>   isset($order_type) ? $order_type : '' ,
              "total_amount" =>   @$settings->currency_symbol.' '. @$data->total_amount,
              "payable_amount" =>    @$settings->currency_symbol.' '.@$data->payable_amount,
              "order_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
