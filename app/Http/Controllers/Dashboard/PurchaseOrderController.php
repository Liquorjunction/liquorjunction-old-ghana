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
use Alert;
use Carbon\Carbon;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PurchaseOrderController extends Controller
{
    //
    public function index()
    {
        // echo "string";exit();
        
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("dashboard.purchase-order.list");
    }

    public function create()
    {
        return view("dashboard.purchase-order.create");
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $validator = \Validator::make($request->all(), [ 'uploaded_file' => 'required|file|mimes:xls,xlsx,csv' ]); 
        if ($validator->fails()) { 
            return response()->json($validator->errors(), 422);
         }

         $filePath = $request->file('uploaded_file'); 
         $import = new UsersImport;

         Excel::import($import, $filePath);

        if ($import->no_data == 0) 
        { 
            Alert::error('Error', 'Import file will be in right format.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -1)
        {
            Alert::error('Error', 'This category is not our system.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -2)
        {
            Alert::error('Error', 'This product is not our system.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -3)
        {
            Alert::error('Error', 'This order is not our system.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -4)
        {
            Alert::error('Error', 'The total amount should not be zero.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -5)
        {
            Alert::error('Error', 'This product price is not our system.');
            return response()->json(['success' => 'false']);
        }
        else
        {
            Alert::success('Success', 'Data import has been successfully uploaded.');
            return response()->json(['success' => 'true']);
        }


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
        // $supplier_id = auth()->guard('main_user')->user()->id;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==1) {
            $sort='order.order_id';
        }elseif ($columnIndex==2) {
             $sort='order.user_id';
        }elseif ($columnIndex==3) {
             $sort='order.supplier_id';
        }elseif ($columnIndex==4) {
            $sort='order.order_date';
        }else{
            $sort='order.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Order::leftjoin('main_users','main_users.id','=','order.user_id')->leftjoin('main_users as supplier_data','supplier_data.id','=','order.supplier_id')->where('order.order_type','=',3)->where('order.order_status',0)->select('order.*','main_users.first_name','main_users.last_name','supplier_data.first_name as suppler_first_name','supplier_data.last_name as suppler_last_name');

        // $totalAr = DB::table('order')->leftjoin('main_users','main_users.id','=','order.user_id')->leftjoin('')
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('order.order_id', 'like', '%' . $searchValue . '%');
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
              "order_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
