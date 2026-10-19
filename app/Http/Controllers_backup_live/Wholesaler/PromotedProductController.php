<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\PromotedProduct;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;

class PromotedProductController extends Controller
{
    //
    public function index()
    {
        // echo "string";exit();
        $supplier_id = auth()->guard('main_user')->user()->id;
        $font_icon = DB::table('font_icon')->where('status',1)->get();
        $productData = DB::table('product')->where('supplier_id',$supplier_id)->where('status',1)->orderby('product_name','ASC')->get();
        // echo "<pre>";print_r($productData->toArray());exit();
        return view("wholesaler.promoted-product.list",compact('font_icon','productData'));
    }

    public function store(Request $request)
    {
        $supplier_id = auth()->guard('main_user')->user()->id;

        $validator = \Validator::make($request->all(), [
                    'product_id' => ['required',Rule::unique('promoted_product')->where(function ($query){
            return $query->where('status','!=','2');
                })],

            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // echo "string";exit();
        $promoted_product = new PromotedProduct();
        $promoted_product->product_id = $request->product_id;
        $promoted_product->supplier_id = $supplier_id;
        $promoted_product->status = 1;

        $promoted_product->save();

        Alert::success('Success', __('backend.New_Promoted_product_created_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function wholesalerpromotedproductUpdateAll(Request $request)
    {
        // echo "<pre>";print_r($id)
        // $promoted_product = PromotedProduct::find($id);
            if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                
        $promoted_product = PromotedProduct::wherein('id', $ids)->update(['status' => $status]);
                

                
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Promoted product deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Promoted product active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
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
        $supplier_id = auth()->guard('main_user')->user()->id;
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='promoted_product.id';
        }elseif ($columnIndex==1) {
             $sort='product.product_name';
        }else{
            $sort='promoted_product.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = PromotedProduct::leftjoin('product','product.id','=','promoted_product.product_id')->where('promoted_product.status','!=','2')->where('promoted_product.supplier_id',$supplier_id)->select('promoted_product.*','product.product_name');
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('product.product_name', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr);exit();
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->uniqid]);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }
            if ($data->status == 1) {
                $status = '<i class="fa fa-check text-success inline"><spna class="hide">Active</span></i>';
            } else {
                $status = '<i class="fa fa-times text-danger inline"><span class="hide">InActive</span></i>';
            }
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            // // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options =  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "product_name" =>   isset($data->product_name) ? $data->product_name : '' ,
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
