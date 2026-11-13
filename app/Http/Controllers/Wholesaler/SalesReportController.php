<?php

namespace App\Http\Controllers\Wholesaler;

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
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class SalesReportController extends Controller
{
    //
     public function index()
    {
        // echo "string";exit();
        
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("wholesaler.salesreport.list");
    }

   public function export_salesreport(Request $request)
   {
    // echo "<pre>";print_r($request->toArray());exit();
    if(!empty($request->startdate && $request->enddate)) {

            $supplier_id = auth()->guard('main_user')->user()->id;
            $frm_date = Carbon::createFromFormat('m-d-Y',$request->startdate)->format('Y-m-d');
                
            $to_date = Carbon::createFromFormat('m-d-Y',$request->enddate)->format('Y-m-d');

          
            
            $sales_report = DB::table('transactions')->leftjoin('users_payments','users_payments.order_id','=','transactions.order_id')->leftjoin('order','order.id','=','transactions.order_id')->leftjoin('main_users','main_users.id','=','transactions.user_id')->select('transactions.*','users_payments.payment_mode','main_users.first_name','main_users.last_name','order.uniqid as order_id')->where('transactions.supplier_id',$supplier_id)->whereDate('transactions.created_at','>=',$frm_date)->whereDate('transactions.created_at','<=',$to_date)->get();

            
        }else {
            $supplier_id = auth()->guard('main_user')->user()->id;

            $sales_report = DB::table('transactions')->leftjoin('users_payments','users_payments.order_id','=','transactions.order_id')->leftjoin('order','order.id','=','transactions.order_id')->leftjoin('main_users','main_users.id','=','transactions.user_id')->select('transactions.*','users_payments.payment_mode','main_users.first_name','main_users.last_name','order.uniqid as order_id')->where('transactions.supplier_id',$supplier_id)->get();
        }

         $filename = 'salesreport' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $file = fopen('php://output', 'w');

        fputcsv($file,array('No', 'Transaction ID','Order Id','Name of customer','Total amount','Payment method','Transaction date'));
        $i = 1;
        foreach ($sales_report as $key=>$data) {
            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);

            $date = \Helper::converttimeTozone($data->transaction_date);

            $payment_mode = "";
            if ($data->payment_mode==1) {
                $payment_mode ="Online";
            }else{
                $payment_mode ="Cash";

            }
            $no = $i;
            $transaction_id = isset($data->trans_no) ? $data->trans_no : '';
            $order_id = isset($data->order_id) ? $data->order_id : '';
            $customer_name = isset($username) ? $username : '';
            $total_amount = isset($data->amount) ? $data->amount : '';
            $payment_mode = isset($payment_mode) ? $payment_mode : '';
            $transaction_date = @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-";

            fputcsv($file,array($i,$transaction_id,$order_id,$customer_name,$total_amount,$payment_mode,$transaction_date));
            
        $i++;
        }
   }

    public function show(Request $request)
    {
        $product_id = $request->product_id;
       $productData = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftjoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title','main_users.email','main_users.phone')->where('product.status','!=','2')->where('product.id',$product_id)->first();
        
         if(!empty($productData))
            {
                $settings = Setting::find(1);
                $html =  view('wholesaler.product.show')->with(['productData' => $productData,'settings' => $settings])->render();

 

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
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
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        $supplier_id = auth()->guard('main_user')->user()->id;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==1) {
            $sort='order.uniqid';
        }elseif ($columnIndex==2) {
             $sort='main_users.first_name';
        }elseif ($columnIndex==3) {
            $sort='transactions.trans_no';
        }elseif ($columnIndex==4) {
            $sort='transactions.amount';
        }elseif ($columnIndex==5) {
            $sort='transactions.transaction_date';
        }else{
            $sort='transactions.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }


        $totalAr = DB::table('transactions')->leftjoin('users_payments','users_payments.order_id','=','transactions.order_id')->leftjoin('order','order.id','=','transactions.order_id')->leftjoin('main_users','main_users.id','=','transactions.user_id')->select('transactions.*','users_payments.payment_mode','main_users.first_name','main_users.last_name','order.order_id as order_id')->where('transactions.supplier_id',$supplier_id);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                 ->orWhere('transactions.amount', 'like', '%' . $searchValue . '%')
                 ->orWhere('order.order_id', 'like', '%' . $searchValue . '%')
                 ->orWhere('transactions.trans_no', 'like', '%' . $searchValue . '%')
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
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->id]);

            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            // $productname = urldecode(@$data->product_name);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }


            if ($data->payment_mode == 1) {
                $payment_mode = '
                <span class="badge  badge-success">Online</span>
              ';
            } elseif ($data->payment_mode ==2) {
                 $payment_mode = '
                <span class="badge  badge-danger">Cash</span>
              ';
            }else {
                $payment_mode = '
                <span class="badge  badge-danger">Online</span>
              ';
            }

            $date = \Helper::converttimeTozone($data->transaction_date);
            $settings = Setting::find(1);
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->order_id) ? $data->order_id : '' ,
              "username" =>   isset($username) ? $username : '' ,
              "trans_no" =>   isset($data->trans_no) ? $data->trans_no : '' ,
              "amount" =>    @$settings->currency_symbol.' '.@$data->amount,
              "payment_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
              "payment_mode" =>   isset($payment_mode) ? $payment_mode : '' ,
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
