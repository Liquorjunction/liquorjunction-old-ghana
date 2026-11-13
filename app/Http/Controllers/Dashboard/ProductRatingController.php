<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Setting;
use DB;
use Helper;
use Carbon\Carbon;


class ProductRatingController extends Controller
{
    
    public function index(Request $request)
    {   
        $productid = $request->id;
        // dd($productid);
        $productInfo =  Product::where('id',$productid)->first();
        return view("dashboard.product-rating.list",compact('productInfo'));
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
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        $productid = $request->get('product_id');
        // echo $productid;exit;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'ratings.id';
        } elseif ($columnIndex == 1) {
            $sort = 'main_users.first_name'; 
        } elseif ($columnIndex == 2) {
            $sort = 'ratings.ratings'; 
        }elseif ($columnIndex == 3) {
            $sort = 'ratings.review'; 
        }else {
            $sort = 'ratings.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = DB::table('ratings')
            ->leftjoin('main_users', 'main_users.id', '=', 'ratings.user_id')
            ->select(DB::raw('CONCAT(main_users.first_name, " ", main_users.last_Name) AS customer_name'),'ratings.ratings','ratings.review','ratings.created_at as rating_date')
            ->where('ratings.product_id', '=', $productid);
        // dd($totalAr);
        
        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('main_users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('main_users.last_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('ratings.ratings', 'like', '%' . $searchValue . '%')
                    ->orWhere('ratings.review', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)->skip($start)->take($rowperpage)->get();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {           
            $date = \Helper::converttimeTozone($data->rating_date);
            
            $settings = Setting::find(1);
            $data_arr[] = array(                
                "id" => isset($data->id) ? ucfirst($data->id) : '',
                "customer_name" => isset($data->customer_name) ? ucfirst($data->customer_name) : '',
                "rating" => isset($data->ratings) ? $data->ratings : '',
                "review" => isset($data->review) ? $data->review : '',
                "review_date" => @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
