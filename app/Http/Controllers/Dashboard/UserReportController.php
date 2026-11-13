<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\MainUser;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use PDF;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class UserReportController extends Controller
{
    //

    public function index()
    {
        // echo "string";exit();
        
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("dashboard.userreport.list");
    }

    public function export_userreport(Request $request){
        if(!empty($request->startdate && $request->enddate)) {
            // echo "<pre>";print_r($request->toArray());
            if (isset($request->startdate) && isset($request->enddate)) {
                $min_date = Carbon::parse($request->startdate)->format('Y-m-d');
                $max_date = Carbon::parse($request->enddate)->format('Y-m-d');
            }
            // $frm_date = Carbon::createFromFormat('d-m-Y',$request->startdate)->format('Y-m-d');
                
            // $to_date = Carbon::createFromFormat('d-m-Y',$request->enddate)->format('Y-m-d'); 

            $user_report =  DB::table('main_users')
            ->leftjoin('countries','countries.id','=','main_users.phone_code')
            ->select('main_users.*','countries.phonecode as phonecode')
            ->where('main_users.user_type',1)
            ->whereBetween('main_users.created_at', [$min_date . ' 00:00:00', $max_date . ' 23:59:59'])
            ->where('main_users.status','!=','2')->get();
        }else {
            // $supplier_id = auth()->guard('main_user')->user()->id;

            $user_report = DB::table('main_users')
                        ->leftjoin('countries','countries.id','=','main_users.phone_code')
                        ->select('main_users.*','countries.phonecode as phonecode')
                        ->where('main_users.user_type',1)->where('main_users.status','!=','2')->get();
        }
        // echo "<pre>";print_r($user_report->toArray());exit();
         $filename = 'userreport' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $file = fopen('php://output', 'w');

        fputcsv($file,array('No', 'Name','Email','Phone','Join Date'));
        $i = 1;
        foreach ($user_report as $key=>$data) {
            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            $email = @$data->email;
            $phone = $data->phonecode ? '+' . $data->phonecode . ' ' . $data->phone : $data->phone;
            // $phone = '+'.@$data->phonecode.' '. @$data->phone;

            $date = \Helper::converttimeTozone($data->created_at);

            // $payment_mode = "";
            // if ($data->user_type==1) {
            //     $user_type ="Customer";
            // }elseif($data->user_type==2){

            //     $user_type ="Wholesaler";
            // }else{
            //     $user_type ="Subadmin";

            // }

            $no = $i;
            $username = isset($username) ? $username : '';
            $email = isset($email) ? $email : '';
            $phone = isset($phone) ? $phone : '';
            $join_date = @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-";

            fputcsv($file,array($i,$username,$email,$phone,$join_date));
            
        $i++;
        }
    }

    public function export_userpdf(Request $request)
    {

        $start_date = $request->startdate;
        $end_date = $request->enddate;
        // echo "<pre>";print_r($request->toArray());exit();
       // $totalAr = DB::table('main_users')->where('status','!=','2');
        $totalAr = DB::table('main_users')
                    ->leftjoin('countries','countries.id','=','main_users.phone_code')
                    ->select('main_users.*','countries.phonecode as phonecode')
                    ->where('main_users.user_type',1)
                    ->where('main_users.status','!=',2);

        if ($start_date) {
             $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
             $totalAr->where('created_at', '>=', $min_date);
        }

        if ($end_date) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('created_at', '<=', $min_date . ' 23:59:59');
        }

        $totalAr = $totalAr->get()->toArray();
        // echo "<pre>";print_r($totalAr);exit();
        view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.userreport.user-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'user_report' .'-'. date('d/m/Y') . '.pdf';
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
        }elseif ($columnIndex==4) {
            $sort='created_at';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        //$totalAr = MainUser::where('status','!=','2')->where('user_type',1);
        // $totalAr = DB::table('main_users')
        //             ->leftjoin('countries','countries.id','=','main_users.phone_code')
        //             ->select('main_users.*','countries.phonecode as phonecode')
        //             ->where('main_users.user_type',1)
        //             ->where('main_users.status','!=',2);

        $totalAr = DB::table('main_users')
        ->leftjoin('countries','countries.id','=','main_users.phone_code')
        ->select('main_users.*','countries.phonecode as phonecode')
        ->where('main_users.user_type',1)
        ->where('main_users.status','!=',2);

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
            $date = \Helper::converttimeTozone($data->created_at);
            $data_arr[] =array(
              "id" =>   isset($data->id) ? $data->id : '' ,
              "customer_name" =>   $data->first_name.' '.$data->last_name ,
              "customer_email" =>   isset($data->email) ? $data->email : '' ,
              //"customer_phone" =>   isset($data->phone) ? $data->phone : '' ,
              "customer_phone" => $data->phonecode ? '+' . $data->phonecode . ' ' . $data->phone : $data->phone,
            //   "customer_phone" =>   '+'. @$data->phonecode.'  '. @$data->phone  ,
              "customer_join_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
