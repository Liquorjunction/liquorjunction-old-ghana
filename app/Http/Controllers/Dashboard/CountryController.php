<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Region;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');   
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,10,'read');
        if($check_view_permission==false){
            abort(404);
        }     
    }

    public function index()
    {
        return view("dashboard.country.list");
    }

    public function show(Request $request)
    {
        $country_id = $request->country_id;
        $countryData = Country::where('id',$country_id)->where('status','!=',2)->first();
        
        if(!empty($countryData))
        {

            $html =  view('dashboard.country.show')->with(['countryData' => $countryData])->render();
            return response()->json(['success' => true,'html'=> $html]);
        }
        return response()->json(['success' => false,'msg'=> 'something wrong.']);
    }

    public function countryUpdateAll(Request $request)
    {
       if($request->ajax())
       {
        if ($request->ids != "") {
            $ids= explode(",", $request->ids);
            $status = $request->status;

            Country::whereIn('id', $ids)->update(['status' => $status]);
            $RegionData = Region::whereIn('country_id',$ids)->get();
            foreach ($RegionData as $result) {
                Region::whereIn('country_id', $ids)->where('status',1)->update(['status' => $status]);
                Area::where('region_id', $result->id)->where('status',1)->update(['status' => $status]);
            } 
            // $stateData = State::wherein('country_id',$ids)->get();

            // foreach ($stateData as $state) {
            //     State::wherein('country_id', $ids)->update(['status' => $status]);
            //     City::where('state_id', $state->id)->update(['status' => $status]);

            // }

            if($status == 2){
                return response()->json(['success' => true,'msg'=>'Record(s) delete successfully']);
            }else if($status == 0){
             return response()->json(['success' => true,'msg'=>'Record(s) deactive successfully']);
         }else{
             return response()->json(['success' => true,'msg'=>'Record(s) active successfully']);
         }
     }
     return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
 }
 abort(404);
}

public function status_active(Request $request){
    $country_id = $request->country_id;
    Country::where('id', $country_id)->update(['status' => 0]);
    $RegionData = Region::where('country_id',$country_id)->get();
    foreach ($RegionData as $result) {
        Region::where('country_id', $country_id)->where('status',1)->update(['status' => 0]);
        Area::where('region_id', $result->id)->where('status',1)->update(['status' => 0]);
    }      

    Alert::success('Success', __('backend.country_deactive_successfully'));
    return response()->json(['success' => 'true']);
}

public function status_inactive(Request $request){
    $country_id = $request->country_id;
    Country::where('id', $country_id)->update(['status' => 1]);
    
    // $stateData = State::where('country_id',$country_id)->get();

    // foreach ($stateData as $state) {
    //     State::where('country_id', $country_id)->update(['status' => 1]);
    //     City::where('state_id', $state->id)->update(['status' => 1]);
    // }
    $RegionData = Region::where('country_id',$country_id)->get();
    foreach ($RegionData as $result) {
        Region::where('country_id', $country_id)->where('status',1)->update(['status' => 1]);
        Area::where('region_id', $result->id)->where('status',1)->update(['status' => 1]);
    }   
    Alert::success('Success', __('backend.country_active_successfully'));
    return response()->json(['success' => 'true']);
}

public function destroy($id)
{
    $category = Country::find($id);

    $category->status = 2;
    $category->save();

    return redirect()->route('country')
    ->with('doneMessage', 'Record deleted successfully');
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
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='id';
        }elseif ($columnIndex==1) {
           $sort='name';
       }else{
        $sort='id';
    }

    $sortBy='DESC';
    if ($columnSortOrder!="") {
        $sortBy=$columnSortOrder;
    }

    $totalAr = Country::where('status','!=','2');

    if ($searchValue!="") {
        $totalAr = $totalAr->where(function ($query) use ($searchValue) {
           $query->orWhere('name', 'like', '%' . $searchValue . '%');
       });
    }


    $totalRecords = $totalAr->get()->count();

    $totalAr = $totalAr->orderBy($sort,$sortBy)
    ->skip($start)
    ->take($rowperpage)
    ->get();

    $data_arr=[];
    foreach ($totalAr as $key => $data) 
    {
        $countryShow =  route('country.show',['id'=>$data->id]);

        $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-country" data-id="'.$data->id.'" title="Show"> </a>';

        if ($data->status == 1) {
            $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
        } else {
            $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
        }


        $data_arr[] =array(
          "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
          "id" =>   isset($data->id) ? $data->id : '' ,
          "title" =>   isset($data->name) ? $data->name : '' ,
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
