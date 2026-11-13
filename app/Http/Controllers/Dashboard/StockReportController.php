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
use App\Models\Product;
use App\Models\Brand;
use App\Models\Uofs;
use App\Exports\StockExport;

class StockReportController extends Controller
{
    public function index()
    {
        return view("dashboard.stockreport.list");
    }

    public function show($id)
    {
        $supplier_id = $id;
        return view('dashboard.stockreport.productlist', compact('supplier_id'));
    }
    public function export_stockreport(Request $request)
    {
        if (!empty($request->startdate) && !empty($request->enddate)) {
            $frm_date = Carbon::createFromFormat('d-m-Y', $request->startdate)->format('Y-m-d');
            // Query to fetch stock report based on startdate and enddate
            $stock_report = DB::table('product')
                ->leftjoin('main_users', 'main_users.id', '=', 'product.supplier_id')
                ->leftjoin('brand', 'brand.id', '=', 'product.brand_id')
                ->leftjoin('categories', 'categories.id', '=', 'product.category_id')
                ->leftjoin('sub_categories', 'sub_categories.id', '=', 'product.subcategory_id')
                ->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')
                ->select('product.*', 'main_users.first_name', 'main_users.last_name', 'categories.title as product_category', 'brand.title as brand_name', 'sub_categories.title as product_subcategory', 'product_variants.variant_qty as qty','product_variants.sold_qty as sold_qty','product_variants.available_qty as available_qty')
                ->where('product.status', '!=', '2')
                ->get();
        } else {
            // Default query to fetch stock report when startdate and enddate are not provided
            $stock_report = DB::table('product')
                ->leftjoin('main_users', 'main_users.id', '=', 'product.supplier_id')
                ->leftjoin('brand', 'brand.id', '=', 'product.brand_id')
                ->leftjoin('categories', 'categories.id', '=', 'product.category_id')
                ->leftjoin('sub_categories', 'sub_categories.id', '=', 'product.subcategory_id')
                ->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')
                ->select('product.*', 'main_users.first_name', 'main_users.last_name', 'categories.title as product_category', 'brand.title as brand_name', 'sub_categories.title as product_subcategory', 'product_variants.variant_qty as qty','product_variants.sold_qty as sold_qty','product_variants.available_qty as available_qty')
                ->where('product.status', '!=', '2')
                ->get();
        }

        $filename = 'stockreport' . date('d-m-Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $file = fopen('php://output', 'w');
        $i = 1;

        fputcsv($file, ['No','Sku','Product Name','Brand','Category','Subcategory','Total Qty','Sold Qty','Available Qty']);

        foreach ($stock_report as $data) {
            $data_arr = [
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "sku" => isset($data->sku) ? $data->sku : '',
                "product_name"=>isset($data->product_name) ? $data->product_name:'',
                "brand" => isset($data->brand_name) ? $data->brand_name : '',
                "category" => isset($data->product_category) ? $data->product_category : '',
                "subcategory" => isset($data->product_subcategory) ? $data->product_subcategory : '',
                "total_qty" =>  isset($data->qty) ? $data->qty : '',
                "sold_qty" => isset($data->sold_qty) ? $data->sold_qty : '',
                "available_qty" => isset($data->available_qty) ? $data->available_qty : '',
                "options" => isset($options) ? $options : '', // You should define $options somewhere in your code
            ];
            fputcsv($file, [$i,$data_arr["sku"],$data_arr["product_name"],$data_arr["brand"], $data_arr["category"], $data_arr["subcategory"], $data_arr["total_qty"], $data_arr["sold_qty"],$data_arr["available_qty"]]);

            $i++;
        }

        fclose($file);
    }
 
    public function export_stockpdf(Request $request)
    {

        $start_date[] = $request->startdate;
        $end_date[] = $request->enddate;
        // echo "<pre>";print_r($request->toArray());exit();
        $totalAr = DB::table('main_users')->leftjoin('transactions', 'transactions.supplier_id', '=', 'main_users.id')->leftjoin('users_payments', 'users_payments.supplier_id', '=', 'main_users.id')->where('main_users.user_type', 2)->groupby('main_users.id')->select('main_users.*', DB::raw('SUM(transactions.amount) as total_auction_amount'));


        $totalAr = $totalAr->get()->toArray();

        // echo "<pre>";print_r($start_date);exit();
        view()->share('totalAr', $totalAr);
        $pdf = PDF::loadView('dashboard.stockreport.stock-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $filename = 'stock_report' . '-' . date('d/m/Y') . '.pdf';
        return $pdf->download($filename);

        // echo "<pre>";print_r($request->toArray());exit();
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
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        $supplier_id = $request->get('supplier_id');
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'product.sku';
        }elseif($columnIndex == 1){
            $sort = 'product.product_name';
        }elseif ($columnIndex == 2) {
            $sort = 'brand.title';
        } elseif ($columnIndex == 3) {
            $sort = 'categories.title';
        } elseif ($columnIndex == 4) {
            $sort = 'sub_categories.title';
        }  elseif ($columnIndex == 5) {
            $sort = 'product_variants.variant_qty';
        } elseif ($columnIndex == 6) {
            $sort = 'product_variants.sold_qty';
        } elseif ($columnIndex == 7) {
            $sort = 'product_variants.available_qty';
        }  
        else {
            $sort = 'product.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // Start building the query
        $totalAr = DB::table('product')
            ->leftjoin('brand', 'brand.id', '=', 'product.brand_id')
            ->leftjoin('categories', 'categories.id', '=', 'product.category_id')
            ->leftjoin('sub_categories', 'sub_categories.id', '=', 'product.subcategory_id')
            ->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')
            ->select('product.*','categories.title as product_category', 'brand.title as brand_name', 'sub_categories.title as product_subcategory', 'product_variants.variant_qty as qty','product_variants.sold_qty as sold_qty','product_variants.available_qty as available_qty')
            ->where('product.status', '!=', '2');
        // Apply search filter if a search value is provided

        if ($searchValue !== "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('product.product_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('brand.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('categories.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('sub_categories.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('product_variants.variant_qty', 'like', '%' . $searchValue . '%')
                    ->orWhere('product.sku', 'like', '%' . $searchValue . '%');
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
        foreach ($totalAr as $key => $data) {
            // Process data and build your $data_arr here
            // ...
            $orderShow =  route('stockreport.show', ['id' => $data->id]);

            $date = \Helper::converttimeTozone($data->created_at);
            $settings = Setting::find(1);
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $orderShow . '" title="View"> </a>';

            $data_arr[] = [
                // "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "sku" =>   isset($data->sku) ? $data->sku : '',
                "product_name"=>isset($data->product_name) ? $data->product_name:'',
                "brand" =>   isset($data->brand_name) ? $data->brand_name : '',
                "category" =>   isset($data->product_category) ? $data->product_category : '',
                "subcategory" =>  isset($data->product_subcategory) ? $data->product_subcategory : '',
                "total_qty" =>  isset($data->qty) ? $data->qty : '',
                'sold_qty' => isset($data->sold_qty) ? $data->sold_qty : '',
                'available_qty' => isset($data->available_qty) ? $data->available_qty : '',

                "options" => isset($options) ? $options : '',
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
