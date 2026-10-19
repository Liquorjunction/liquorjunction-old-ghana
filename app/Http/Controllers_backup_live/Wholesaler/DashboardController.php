<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\User;
// use App\Models\MainUser;
use App\Models\ClassType;
use App\Models\DanceClass;
use App\Models\MainUser;
// use App\Models\User;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\DanceCategory;
use App\Models\ClassPurchaseHistory;
use App\Charts\DataChart;
use Illuminate\Support\Facades\DB;
use Chartisan\PHP\Chartisan;
use Chartisan\PHP\ServerData;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   

    // public function __construct()
    // {
    //     $this->middleware('auth');
      
    // }

    public function index(Request $request)
    {
        // echo "string";exit();
           

        // $start = '';
        // $end = '';
        // $start = Carbon::now()->format('m-d-Y');
        // $end = Carbon::now()->format('m-d-Y');
    
        // return view('dashboard.home', compact('users_count','normal_users_count','instructor_users_count','dance_class_count', 'class_purcahse_count', 'total_revenue', 'dance_category_count', 'start','end', 'instructor_request_count'));
       
        if ($request->date_filter != "") 
        {
            // echo "<pre>";print_r($request->toArray());exit();
             //dd($request->date_filter);

            // $parts = explode(' - ', $request->date_filter);

            // $filterdate = $request->date_filter;

            // $start = Carbon::createFromFormat('m-d-Y', $parts[0])->format('Y-m-d');

            // $end = Carbon::createFromFormat('m-d-Y', $parts[1])->format('Y-m-d');
            $start = '';
            $end = '';
            $start = Carbon::now()->format('m-d-Y');
            $end = Carbon::now()->format('m-d-Y');

           

           
            return view('wholesaler.home', compact('start', 'end'));

        }
        else
        {
            $start = '';
            $end = '';
            $start = Carbon::now()->format('m-d-Y');
            $end = Carbon::now()->format('m-d-Y');

           


            return view('wholesaler.home', compact('start', 'end'));
        }

            // return view('dashboard.home');
        
    }

    public function filterClass(Request $request)
    {
        $draw = $request->get('draw');

        $start = $request->get("start");

        // $rowperpage = $request->get("length");
        // $columnIndex_arr = $request->get('order');
        // $columnName_arr = $request->get('columns');
        // $order_arr = $request->get('order');
        // $search_arr = $request->get('search');

        // //echo "<pre>";print_r($order_arr);exit;
        // $columnIndex = $columnIndex_arr[0]['column']; // Column index
        // $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        // if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
        //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

           $totalAr = \DB::table('class')->select('class_type.title', 'class.*','class_purchase_history.*')->join('class_purchase_history', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('class_type', 'class_type.id', '=', 'class.class_type')->where('class.status', '=', 3);


            $totalRecords = $totalAr->groupby('class_purchase_history.class_id')->get()->count();

            $totalAr = $totalAr->orderBy('class_purchase_history.class_id', $sortBy)
                ->skip($start)
                ->take(5)
                ->groupby('class_purchase_history.class_id')
                ->get();

            $totalAr1 = \DB::table('class')->select('class_type.title', 'class.*','class_purchase_history.*')->join('class_purchase_history', 'class.id', '=', 'class_purchase_history.class_id')->join('class_type', 'class_type.id', '=', 'class.class_type')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('class.status', '=', 3)->groupby('class_purchase_history.class_id')->count('class_purchase_history.class_id');
            
            $data_arr = [];
            $dl = '';
            $ps = '';
            $pc = '';
            $ctname = '';

            if($totalAr1 >= 1)
            {

            /* print_r($totalAr);
            exit;*/
            
        
            foreach ($totalAr as $key => $data) {
                $class_type = isset($data->class_type) ? $data->class_type : '';
                $class_name = isset($data->class_name) ? $data->class_name : '';
                $dance_level = isset($data->dance_level) ? $data->dance_level : '';
                $payment_status = isset($data->payment_status) ? $data->payment_status : '';

                $popular_dance_class = isset($data->is_popular_dance_class) ? $data->is_popular_dance_class : '';

                $dance_class_type = ClassType::all();

                $dance_category = DanceCategory::where('id', $data->dance_category_id)->first();
                
                $category = $dance_category->category_name;
                foreach($dance_class_type as $ct)
                {
                    if($ct->id == $data->class_type)
                    {
                        $ctname = $ct->title;
                    }
                }

                if($dance_level == 1)
                {
                    $dl = 'Beginner';
                }
                elseif($dance_level == 2)
                {
                    $dl = 'Intermediate';
                }
                else
                {
                    $dl = 'Expert';
                }
                $setting = Setting::find(1);
                $currency = $setting->currency_symbol;
                $price = isset($data->price) ? $data->price : '0.0';
               // $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
                $createddate = \Helper::converttimeTozone($data->created_at);

                $data_arr[] = array(
                    "class_category" => $category,
                    "class_type" =>   $ctname,
                    "class_name" => $class_name,
                    "dance_level" => $dl
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
        else
        {
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecords,
                "aaData" => $data_arr
            );

            echo json_encode($response);
        }
    }

    public function filterCategory(Request $request)
    {
        $draw = $request->get('draw');

        $start = $request->get("start");

        // $rowperpage = $request->get("length");
        // $columnIndex_arr = $request->get('order');
        // $columnName_arr = $request->get('columns');
        // $order_arr = $request->get('order');
        // $search_arr = $request->get('search');

        // //echo "<pre>";print_r($order_arr);exit;
        // $columnIndex = $columnIndex_arr[0]['column']; // Column index
        // $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        // if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
        //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

           $totalAr = \DB::table('class')->select('class_type.title', 'class.*','class_purchase_history.*')->join('class_purchase_history', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('class_type', 'class_type.id', '=', 'class.class_type')->where('class.status', '=', 3);

          // $totalAr = \DB::table('dance_category')->where('status', '!=', 2);


            $totalRecords = $totalAr->groupby('class_purchase_history.class_id')->get()->count();

            $totalAr = $totalAr->orderBy('class_purchase_history.class_id', $sortBy)
                ->skip($start)
                ->take(5)
                ->groupby('class_purchase_history.class_id')
                ->get();
        

        $totalAr1 = \DB::table('class')->select('class_type.title', 'class.*','class_purchase_history.*')->join('class_purchase_history', 'class.id', '=', 'class_purchase_history.class_id')->join('class_type', 'class_type.id', '=', 'class.class_type')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('class.status', '=', 3)->groupby('class_purchase_history.class_id')->count('class_purchase_history.class_id');


        /* print_r($totalAr);
            exit;*/
            $data_arr = [];
            $dl = '';
            $ps = '';
            $pc = '';
            $ctname = '';

        if($totalAr1 >= 1)
        {
            foreach ($totalAr as $key => $data) {

                $dance_category = DanceCategory::where('id', $data->dance_category_id)->first();
                $category = $dance_category->category_name;
               // $category_name = isset($data->category_name) ? $data->category_name : '';

                $popular_dance_category = isset($data->is_popular_Category) ? $data->is_popular_Category : '';
              //  $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
                $createddate = \Helper::converttimeTozone($data->created_at);

                $data_arr[] = array(
                    "category_name" => $category,
                    "createddate" =>   $createddate
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
        else
        {
            
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecords,
                "aaData" => $data_arr
            );

            echo json_encode($response);
        }
    }

    
}
