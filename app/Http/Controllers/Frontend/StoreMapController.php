<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Models\Setting;
use Illuminate\Http\Request;
use Session;
use DB;
// use Illuminate\Session\Store;
use Storage;

class StoreMapController extends Controller
{
    //

    public function storeSessionLat(Request $request)
    {
        // echo "<pre>";print_r($request->lat);exit();
       //  $current_lat = Session::set('current_lat', $request->lat);
       // $current_long = Session::set('current_long', $request->lng);
        \Session::put('current_lat', $request->lat);
        \Session::put('current_long', $request->lng);
        return response()->json(['success' => true]);
    }

   public function StoreMap()
   {
    // echo "string";exit();
    $current_lat = Session::get('current_lat');
    $current_long = Session::get('current_long');
    // echo "<pre>";print_r($current_lat);exit();
    // $current_lat = isset($current_lat) ? $current_lat : '23.0736';
    // $current_long = Session::get('current_long');
    // $current_long = isset($current_long) ? $current_long : '72.5258';

    // if (empty($current_lat)) {
    //     Alert::success('error',__('backend.quote_added_successfully'));
    // }
    // echo "<pre>";print_r($current_lat);
    $setting = Setting::find(1);
    $diff = $setting->map_distance;
    // print_r($diff);exit();
    // $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile',DB::raw("(3959 * acos(cos(radians('" . $current_lat . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $current_long . "')) + sin(radians('" . $current_lat . "')) * sin(radians(store_details.latitude))))* 0.621371 as distance"))->where('store_details.status',1)->havingRaw('distance <='.$diff)->orderby('distance','DESC')->get();
    // // echo "<pre>";print_r($storeMapData);exit();
    // if (empty($storeMapData->toArray())) {
    //     // echo "string";exit();
    //     $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile')->orderby('store_details.id','DESC')->where('store_details.status',1)->get();
    // }

    if ($current_lat) {
        // echo "string";exit;
         $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile',DB::raw("(3959 * acos(cos(radians('" . $current_lat . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $current_long . "')) + sin(radians('" . $current_lat . "')) * sin(radians(store_details.latitude))))* 0.621371 as distance"))->where('store_details.status',1)->havingRaw('distance <='.$diff)->orderby('distance','DESC')->get();
    }else{
        // echo "string2";exit;
        $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile')->orderby('store_details.id','DESC')->where('store_details.status',1)->get();
    }

    
    $locations = [];
    foreach ($storeMapData as $key => $test) {
        $locations[] = array("lat" => $test->latitude,"lng" => $test->longitude,"id" => $test->id);
    }
    // echo "<pre>";print_r($storeMapData);exit();
     if (empty($current_lat)) {
          $current_lat = $storeMapData[0]->latitude;
     }

     if (empty($current_long)) {
          $current_long = $storeMapData[0]->longitude;
         
     }
          // $storeMapData = $storeMapData->get();
    // echo "<pre>";print_r($storeMapData);exit();
    return view('frontEnd.store-map.list',compact('storeMapData','locations','current_lat','current_long'));
   }

   public function storeMapDetail(Request $request)
   {
    // echo "<pre>";print_r($request->toArray());exit();
    $store_id = $request->id;

        $storeData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile')->where('store_details.id',$store_id)->where('store_details.status',1)->first();

        $storeTiming = DB::table('store_timing_week')->leftjoin('week_list','week_list.id','=','store_timing_week.week_id')->where('store_timing_week.status',1)->where('store_timing_week.store_id',$store_id)->select('store_timing_week.*','week_list.name as week_name')->get();

        // echo "<pre>";print_r($storeTiming);exit();
        $html = view('frontEnd.store-map.detail')->with(['storeData' => $storeData,'storeTiming' => $storeTiming])->render();
         return response()->json(['success' => true,'html' => $html]);
   }
}
