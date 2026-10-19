<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\PromotedProduct;
use App\Models\StoreDetails;
use App\Models\Setting;
use App\Models\StoreTimingWeek;
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

class StoreController extends Controller
{
    //
    public function index()
    {
        // echo "string";exit();
        $supplier_id = auth()->guard('main_user')->user()->id;
        $font_icon = DB::table('font_icon')->where('status',1)->get();
        $productData = DB::table('product')->where('supplier_id',$supplier_id)->where('status',1)->get();
        // echo "<pre>";print_r($productData->toArray());exit();
        return view("wholesaler.store.list",compact('font_icon','productData'));
    }

    public function create()
    {
        $categories = Categories::where('status',1)->orderby('title','ASC')->get();
        $settings = Setting::find(1);
        return view("wholesaler.store.create",compact('categories','settings'));
    }

    public function get_formatted_address(Request $request)
    {
        $content = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $request->latitude . "," . $request->longitude . "&sensor=false&key=" . 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4');
        $content = json_decode($content, true);
       // echo "<pre>";print_r($content);exit();

        
        return $content['results'][0]['formatted_address'];
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
         $this->validateRequest();
          $uniqid = uniqid();
          $supplier_id = auth()->guard('main_user')->user()->id;
          $product = new StoreDetails();

          $product->uniqid = $uniqid;
          $product->wholesaler_id = $supplier_id;
          $product->country = $request->country;
          $product->state = $request->state;
          $product->zip_code = $request->zip_code;
          $product->street_address = $request->street_address;
          $product->address = $request->address;
          $product->latitude = $request->property_latitude;
          $product->longitude = $request->property_longitude;
          $product->status = 1;

          $product->save();

          $mondayTiming = new StoreTimingWeek();
          $mondayTiming->store_id = $product->id;
          $mondayTiming->week_id = 1;
          $mondayTiming->start_time = isset($request->monday_opening_time) ? $request->monday_opening_time : '00:00';
          $mondayTiming->end_time = isset($request->monday_close_time) ? $request->monday_close_time : '00:00';
          $mondayTiming->status = 1;

          $mondayTiming->save();

          $tuesdayTiming = new StoreTimingWeek();
          $tuesdayTiming->store_id = $product->id;
          $tuesdayTiming->week_id = 2;
          $tuesdayTiming->start_time = isset($request->tuesday_opening_time) ? $request->tuesday_opening_time : '00:00';
          $tuesdayTiming->end_time = isset($request->tuesday_close_time) ? $request->tuesday_close_time : '00:00';
          $tuesdayTiming->status = 1;

          $tuesdayTiming->save();

          $wednesdayTiming = new StoreTimingWeek();
          $wednesdayTiming->store_id = $product->id;
          $wednesdayTiming->week_id = 3;
          $wednesdayTiming->start_time = isset($request->wednesday_opening_time) ? $request->wednesday_opening_time : '00:00';
          $wednesdayTiming->end_time = isset($request->wednesday_close_time) ? $request->wednesday_close_time : '00:00';
          $wednesdayTiming->status = 1;

          $wednesdayTiming->save();

          $thursdayTiming = new StoreTimingWeek();
          $thursdayTiming->store_id = $product->id;
          $thursdayTiming->week_id = 4;
          $thursdayTiming->start_time = isset($request->thursday_opening_time) ? $request->thursday_opening_time : '00:00';
          $thursdayTiming->end_time = isset($request->thursday_close_time) ? $request->thursday_close_time : '00:00';
          $thursdayTiming->status = 1;

          $thursdayTiming->save();

          $fridayTiming = new StoreTimingWeek();
          $fridayTiming->store_id = $product->id;
          $fridayTiming->week_id = 5;
          $fridayTiming->start_time = isset($request->friday_opening_time) ? $request->friday_opening_time : '00:00';
          $fridayTiming->end_time = isset($request->friday_close_time) ? $request->friday_close_time : '00:00';
          $fridayTiming->status = 1;

          $fridayTiming->save();

           $saturdayTiming = new StoreTimingWeek();
          $saturdayTiming->store_id = $product->id;
          $saturdayTiming->week_id = 6;
          $saturdayTiming->start_time = isset($request->saturday_opening_time) ? $request->saturday_opening_time : '00:00';
          $saturdayTiming->end_time = isset($request->saturday_close_time) ? $request->saturday_close_time : '00:00';
          $saturdayTiming->status = 1;

          $saturdayTiming->save();

          $sundayTiming = new StoreTimingWeek();
          $sundayTiming->store_id = $product->id;
          $sundayTiming->week_id = 7;
          $sundayTiming->start_time = isset($request->sunday_opening_time) ? $request->sunday_opening_time : '00:00';
          $sundayTiming->end_time = isset($request->sunday_close_time) ? $request->sunday_close_time : '00:00';
          $sundayTiming->status = 1;

          $sundayTiming->save();


          return redirect()->route('wholesalerstore')->with('doneMessage', 'Store create successfully.');
    }

    public function validateRequest($id="")
    {
        if($id !="")
        {
            $validateData =request()->validate([
                'country' => 'required|max:200',
                'state' => 'required|max:200',
                'zip_code' => 'required|min:4|max:200',
                'street_address' => 'required|max:200',
                'address' => 'required',
            ]);

        }else{

            $validateData =request()->validate([
                'country' => 'required|max:200',
                'state' => 'required|max:200',
                'zip_code' => 'required|min:4|max:200',
                'street_address' => 'required|max:200',
                'address' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function edit($id)
    {
            $product_id = $id;

            $StoreDetails = DB::table('store_details')->where('id',$product_id)->first();
            $StoreTimingWeek = DB::table('store_timing_week')->leftjoin('week_list','week_list.id','=','store_timing_week.week_id')->select('store_timing_week.*','week_list.name','week_list.week_name')->where('store_timing_week.store_id',$product_id)->get();

            return view('wholesaler.store.edit', compact('StoreDetails','StoreTimingWeek'));
      

    }

    public function update(Request $request,$id)
    {
        // echo "<pre>";print_r($request->toArray())yy;exit();
        $this->validateRequest($id);
        $store_id = $id;
        $product = StoreDetails::where('id', $store_id)->update(array(
                'country' => $request->country,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'street_address' => $request->street_address,
                'address' => $request->address,
                'latitude' => $request->property_latitude,
                'longitude' => $request->property_longitude,
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',1)->update(array(
                'start_time' =>  isset($request->monday_opening_time) ? $request->monday_opening_time : '00:00',
                'end_time' =>  isset($request->monday_close_time) ? $request->monday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',2)->update(array(
                'start_time' => isset($request->tuesday_opening_time) ? $request->tuesday_opening_time : '00:00',
                'end_time' => isset($request->tuesday_close_time) ? $request->tuesday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',3)->update(array(
                'start_time' => isset($request->wednesday_opening_time) ? $request->wednesday_opening_time : '00:00',
                'end_time' => isset($request->wednesday_close_time) ? $request->wednesday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',4)->update(array(
                'start_time' => isset($request->thursday_opening_time) ? $request->thursday_opening_time : '00:00',
                'end_time' => isset($request->thursday_close_time) ? $request->thursday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',5)->update(array(
                'start_time' => isset($request->friday_opening_time) ? $request->friday_opening_time : '00:00',
                'end_time' => isset($request->friday_close_time) ? $request->friday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',6)->update(array(
                'start_time' => isset($request->saturday_opening_time) ? $request->saturday_opening_time : '00:00',
                'end_time' => isset($request->saturday_close_time) ? $request->saturday_close_time : '00:00',
            ));

        $time = StoreTimingWeek::where('store_id', $store_id)->where('week_id',7)->update(array(
                'start_time' => isset($request->sunday_opening_time) ? $request->sunday_opening_time : '00:00',
                'end_time' => isset($request->sunday_close_time) ? $request->sunday_close_time : '00:00',
            ));

        return redirect()->route('wholesalerstore')->with('doneMessage', 'Store updated successfully.');
    }

    public function show($id)
    {
        $store_id = $id;
        $StoreDetails = DB::table('store_details')->where('id',$store_id)->first();
        $StoreTimingWeek = DB::table('store_timing_week')->leftjoin('week_list','week_list.id','=','store_timing_week.week_id')->select('store_timing_week.*','week_list.name')->where('store_timing_week.store_id',$store_id)->get();

        // echo "<pre>";print_r($StoreTimingWeek);exit();

        return view('wholesaler.store.show', compact('StoreDetails','StoreTimingWeek'));
    }

    public function wholesalerstoreUpdateAll(Request $request)
    {
        // echo "string";exit();
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                StoreDetails::wherein('id', $ids)->update(['status' => $status]);
               
                if($status == 2){
                    // Alert::success('Success', __('backend.Record_deleted_sucessfully'));
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                    // Alert::success('Success', __('backend.Product_deactive_sucessfully'));
                   return response()->json(['success' => true,'msg'=>'Store deactive successfully']);
                  }else{
                    // Alert::success('Success', __('backend.Product_active_sucessfully'));
                   return response()->json(['success' => true,'msg'=>'Store active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
    }

    public function status_active(Request $request){
        $store_id = $request->store_id;
         StoreDetails::where('id', $store_id)->update(['status' => 0]);

         Alert::success('Success', __('backend.Store_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        
        $store_id = $request->store_id;
         StoreDetails::where('id', $store_id)->update(['status' => 1]);
         
         Alert::success('Success', __('backend.Store_active_sucessfully'));
         return response()->json(['success' => 'true']);
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
            $sort='store_details.id';
        }elseif ($columnIndex==1) {
             $sort='store_details.country';
        }elseif ($columnIndex==2) {
             $sort='store_details.state';
        }elseif ($columnIndex==3) {
             $sort='store_details.zip_code';
        }elseif ($columnIndex==4) {
             $sort='store_details.street_address';
        }else{
            $sort='store_details.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }


        $totalAr = StoreDetails::where('status','!=',2)->where('wholesaler_id',$supplier_id);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('store_details.country', 'like', '%' . $searchValue . '%');
                 $query->orWhere('store_details.state', 'like', '%' . $searchValue . '%');
                 $query->orWhere('store_details.zip_code', 'like', '%' . $searchValue . '%');
                 $query->orWhere('store_details.street_address', 'like', '%' . $searchValue . '%');
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
            $categoryShow =  route('wholesalerstore.show',['id'=>$data->id]);
            $categpryEdit =  route('wholesalerstore.edit',['id'=>$data->id]);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }
             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }


            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$categoryShow.'" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "country" =>   isset($data->country) ? $data->country : '' ,
              "state" =>   isset($data->state) ? $data->state : '' ,
              "zip_code" =>   isset($data->zip_code) ? $data->zip_code : '' ,
              "street_address" =>   isset($data->street_address) ? $data->street_address : '' ,
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
