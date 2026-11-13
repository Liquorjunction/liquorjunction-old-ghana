<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\MainUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
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
use App\Exports\OrderReportExport;
use Maatwebsite\Excel\Facades\Excel;

class LoyaltyReportController extends Controller
{


    public function index()
    {
        $loyalty_report = DB::table('loyalty_points')
                        ->leftjoin('main_users', 'main_users.id', '=', 'loyalty_points.user_id')
                        ->leftjoin('order', 'order.order_id', '=', 'loyalty_points.order_id')
                        ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
                        ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
                        ->select('loyalty_points.*','order.order_id', 'order.order_date', 'order.payable_amount', 'order_info.customer_name','order.order_status','order_info.customer_email','order_info.customer_mobile')
                        ->groupBy('order_info.customer_mobile')
                        ->where('loyalty_points.status',1)->get();

        return view("dashboard.loyaltyreport.list", compact('loyalty_report'));

    }

    public function export_loyaltyreport(Request $request){

        $min_date = $request->startdate ? Carbon::parse($request->startdate)->format('Y-m-d') . ' 00:00:00' : null;
        $max_date = $request->enddate ? Carbon::parse($request->enddate)->format('Y-m-d') . ' 23:59:59' : null;
        $namefilter = $request->input('customername');
        $emailfilter = $request->input('customeremail');

        $loyalty_report =  DB::table('loyalty_points')
                ->leftjoin('main_users', 'main_users.id', '=', 'loyalty_points.user_id')
                ->leftjoin('order', 'order.order_id', '=', 'loyalty_points.order_id')
                ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
                ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
                ->select('loyalty_points.*','order.order_id', 'order.order_date', 'order.payable_amount', 'order_info.customer_name','order.order_status', 'order_info.customer_email')
                ->when($min_date && $max_date, function ($query) use ($min_date, $max_date) {
                    return $query->whereBetween('order.order_date', [$min_date, $max_date]);
                })
                ->when($namefilter, function ($query) use ($namefilter) {
                    return $query->where('main_users.id', $namefilter);
                })
                ->when($emailfilter, function ($query) use ($emailfilter) {
                    return $query->where('main_users.id', $emailfilter);
                })
                ->where('loyalty_points.status',1)->get();


        $filename = 'loyaltyreport' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $file = fopen('php://output', 'w');

        fputcsv($file,array('No', 'Order Id','Customer Name','Customer Email','Earned Points','Spent Points','Order Amount','Order Date','Order Status'));
        $i = 1;
        foreach ($loyalty_report as $key=>$data) {
            $date = \Helper::converttimeTozone($data->created_at);

            
            if(strtolower($data->type)=='credit')
            {
                $creditPoints='+'.$data->points;
            }
            else
            {
                $creditPoints= '';
            }

            if(strtolower($data->type)=='debit')
            {
                $debitPoints='-'.$data->points;
            }
            else
            {
                $debitPoints= '';
            }

            if ($data->order_status == 4) {
                $status='Cancelled';
            }
            else if ($data->order_status == 3) {
                $status='Delivered';
            }
            else if ($data->order_status == 2) {
                $status='Accepted';
            }
            else {
                $status='Pending';
            }

            $no = $i;
            $customername = isset($data->customer_name) ? $data->customer_name : '';
            $customeremail = isset($data->customer_email) ? $data->customer_email : '';
            $order_id =  isset($data->order_id) ? $data->order_id : '';
            $earned_points =   isset($creditPoints) ? $creditPoints : '';
            $spent_points =   isset($debitPoints) ? $debitPoints : '';
            $order_amount =   isset($data->payable_amount) ? $data->payable_amount.' GH' : '';
            $order_date = @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-";
            $order_status =   isset($status) ? $status : '';


            fputcsv($file,array($no, $order_id,$customername,$customeremail,$earned_points,$spent_points, $order_amount,$order_date,$order_status));
            
        $i++;
        }
    }

      public function loyaltyreportanyData(Request $request)
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
        $namefilter = $request->get('namefilter');
        $emailfilter = $request->get('emailfilter');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        $supplier_id = $request->get('supplier_id');
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 1) {
            $sort = 'order.id';
        }elseif($columnIndex == 2){
            $sort = 'order_info.customer_name';
        }elseif($columnIndex == 3){
            $sort = 'order_info.customer_email';
        }elseif ($columnIndex == 4) {
            $sort = 'loyalty_points.points';
        } elseif ($columnIndex == 5) {
            $sort = 'loyalty_points.points';
        } elseif ($columnIndex == 6) {
            $sort = 'order.payable_amount';
        }  elseif ($columnIndex == 7) {
            $sort = 'order.order_date';
        } 
        else {
            $sort = 'order.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // Start building the query
        $totalAr = DB::table('loyalty_points')
            ->leftjoin('main_users', 'main_users.id', '=', 'loyalty_points.user_id')
            ->leftjoin('order', 'order.order_id', '=', 'loyalty_points.order_id')
            ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
            ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
            ->select('loyalty_points.*','order.order_id', 'order.order_date', 'order.payable_amount', 'order_info.customer_name','order.order_status','order_info.customer_email')
            ->where('loyalty_points.status',1);

        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('order_date', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('order_date', '<=', $min_date . ' 23:59:59');
        }

        if ($namefilter != "") {
            $totalAr->where('main_users.id', $namefilter);
        }

         if ($emailfilter != "") {
            $totalAr->where('main_users.id', $emailfilter);
        }

        // Apply search filter if a search value is provided

        if ($searchValue !== "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('order.order_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.customer_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.customer_email', 'like', '%' . $searchValue . '%')
                    ->orWhere('order.payable_amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_status.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('loyalty_points.points', 'like', '%' . $searchValue . '%');
            });
        }

        // Get the total number of records before pagination
        $totalRecords = $totalAr->count();

        // Apply sorting, pagination, and get the final data
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        $settings = Setting::find(1);
        foreach ($totalAr as $key => $data) {
            $date = \Helper::converttimeTozone($data->created_at);

            if(strtolower($data->type)=='credit')
            {
                $creditPoints='<span style="color:green;font-weight:bold">+'.$data->points. '</span>';
            }
            else
            {
                $creditPoints= '-';
            }

            if(strtolower($data->type)=='debit')
            {
                $debitPoints='<span style="color:red;font-weight:bold">-'.$data->points. '</span>';
            }
            else
            {
                $debitPoints= '-';
            }

            // status
            if ($data->order_status == 4) {
                $status = '<button type="button" class="btn btn-success pointer_button bt-can">
                <span class="badge  badge-success">Cancelled</span>
              </button>';
            } elseif ($data->order_status == 3) {
                $status = '<button type="button" class="btn btn-warning pointer_button bt_war">
                <span class="badge  badge-danger">Delivered</span>
              </button>';
            } elseif ($data->order_status == 2) {
                $status = '<button type="button" class="btn btn-info pointer_button">
                <span class="badge  badge-danger">Accepted</span>
              </button>';
            } else {
                $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Pending</span>
              </button>';
            }


            $data_arr[] = [
                "order_id" =>   isset($data->order_id) ? $data->order_id : '',
                "customer_name"=>isset($data->customer_name) ? $data->customer_name:'',
                "customer_email"=>isset($data->customer_email) ? $data->customer_email:'',
                "earned_points" =>   isset($creditPoints) ? $creditPoints : '',
                "spent_points" =>   isset($debitPoints) ? $debitPoints : '',
                "order_amount" =>  isset($data->payable_amount) ? $data->payable_amount. ' ' . @$settings->currency_symbol : '',
                "order_date" =>  @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
                "order_status" =>  isset($status) ? $status : '',
            ];
        }

        // Build the response array
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        // Return the response as JSON
        return response()->json($response);
    }
 
  
}
