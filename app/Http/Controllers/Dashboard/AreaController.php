<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Region;
use App\Models\Country;
use Auth;
use Illuminate\Config;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;
use Alert;
use App; 
use Helper;

class AreaController extends Controller
{
    private $uploadPath = "uploads/Area/";
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }
    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,12,'read');
        if($check_view_permission==false){
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // echo "string";exit();

        $region = Region::where('status',1)->orderby('title','ASC')->get();
        // dd($region);
        return view("dashboard.area.list", compact('region'));
    }
    public function store(Request $request)
    {
        if (empty($request->area_id)) {
            $uniqid = uniqid();
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => [
                        'required', 'max:30',
                        Rule::unique('area')->where(function ($query) {
                            return $query->where('status', '!=', 2);
                        }),
                    ],
                   
                ]   
            );
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $area = new Area();
            $area->region_id = $request->input('region_id');
            $area->title = $request->input('title');
            $area->title_fr = $request->input('title_fr');
            // $area->rate = $request->input('rate');
            $area->delivery_fee = $request->input('delivery_fee');
            $area->delivery_amount = $request->input('delivery_amount');
            $area->status = 1;
            $area->save();
    

            Alert::success('Success', __('backend.New_area_created_successfully'));
            return response()->json(['success' => 'true']);
        } else {

            $id = $request->area_id;
            $validator = \Validator::make($request->all(),    [
                'region_id'=>'required',
                'title' => [
                    'required',
                    'max:45',
                    Rule::unique('area')
                        ->where(function ($query) use ($request) {
                            return $query->where('status', '!=', '2');
                        })
                        ->where('region_id', $request->input('region_id'))
                        ->ignore($id), // Add the ID to ignore for updates
                ],
              
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }


            $area_id = $request->input('area_id');
            $brand = Area::where('id', $area_id)->update(array(
                'region_id'=> $request->region_id,
                'title' => $request->title,
                'title_fr' => $request->title_fr,
                // 'rate'=>$request->rate,
                'delivery_fee'=>$request->delivery_fee,
                'delivery_amount'=>$request->delivery_amount,

            ));
            Alert::success('Success', __('backend.area_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }
    }
    


    public function validateRequest($id = "")
    {

        if ($id != "") {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('area')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        } else {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('area')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        }
        // return $validateData;
    }

    public function areaUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;
                
                if($status==1){
                    $area_info = Area::wherein('id',$ids)->pluck('region_id');   
                    $region = Region::wherein('id', $area_info)->where('status','!=',1)->pluck('country_id');                           
                    $countryStatus = Country::whereIn('id',$region)->where('status','!=',1)->get();
                    $county_count = count($countryStatus);
                    $region_count = count($region);
                    // echo   $county_count;
                    //  echo '<br>';
                    //  echo $region_count;
                    // die;
                    // DD($region_count);
                    if($county_count > 0 || $region_count > 0){
                        return response()->json(['success' => true,'msg'=>'Region or country is inactive, Please first active it']);                      
                    }
                    else{
                        Area::wherein('id', $ids)->update(['status' => $status]);
                    }
                }else{
                    Area::wherein('id', $ids)->update(['status' => $status]);
                }                
                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deactive successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Record(s) active successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }

    public function edit(Request $request)
    {
        // $Faq = Area::find($id);
        if ($request->ajax()) {
            $area_id = $request->id;
            
            
            if ($area_id) {
                $editData = Area::where('id', $area_id)->where('status', '!=', 2)->first();
                $areaData = Region::where('status', '!=', 2)->orderby('title', 'ASC')->get();
                $html = view('dashboard.area.edit', ['editData'=>$editData, 'areaData' => $areaData])->render();
               
                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong.']);
        }
        // echo "<pre>";print_r($area);exit();
    }
    

    public function show(Request $request)
    {
        $area_id = $request->id;
        $areaData = Area::where('id', $area_id)->where('status', '!=', 2)->first();

        if (!empty($areaData)) {

            $html =  view('dashboard.area.show')->with(['areaData' => $areaData])->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }

    public function destroy($id)
    {
        $area = Area::find($id);
        $area->status = 2;
        $area->save();

        return redirect()->route('area')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $area_id = $request->id;
        Area::where('id', $area_id)->update(['status' => 0]);
        Alert::success('Success', __('backend.area_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $area_id = $request->id;
        $area_info = Area::where('id', $area_id)->first();
        $region_info = Region::where('id',$area_info->region_id)->first();
        $country_info = Country::where('id',$region_info->country_id)->first();
        if($region_info->status!=1){
            Alert::warning('Warning', __('Region is inactive, Please first active the region'));
            return response()->json(['success' => 'true']);
        }
        if($country_info->status!=1){
            Alert::warning('Warning', __('Country is inactive, Please first active the country'));
            return response()->json(['success' => 'true']);
        } 
        Area::where('id', $area_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.area_active_sucessfully'));
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
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'id';
        }elseif ($columnIndex == 1) {
            $sort = 'regions.title';
        }
         elseif ($columnIndex == 2) {
            $sort = 'area.title';
        }
        elseif ($columnIndex == 3) {
            $sort = 'area.delivery_amount';
        } 
        elseif ($columnIndex == 4) {
            $sort = 'area.delivery_fee';
        } 
        else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Area::rightJoin('regions', 'regions.id', '=', 'area.region_id')
        ->select('area.*', 'regions.title as region_name', 'regions.id as region_id')
        ->where('area.status', '!=', 2);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('regions.title', 'like', '%' . $searchValue . '%');
                $query->orWhere('area.title', 'like', '%' . $searchValue . '%');
                // $query->orWhere('area.rate', 'like', '%' . $searchValue . '%');
                $query->orWhere('area.delivery_amount', 'like', '%' . $searchValue . '%');
                $query->orWhere('area.delivery_fee', 'like', '%' . $searchValue . '%');
            });
        }
        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr);exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $areaShow =  route('area.show', ['id' => $data->id]);
            $areaEdit =  route('area.edit', ['id' => $data->uniqid]);

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
            }
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options =   '<a class="btn btn-sm show-eyes list box-shadow paddingset show-area" data-id="' . $data->id . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-area"  data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   


            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                // "id" => isset($data->id) ? $data->id : '',
                "region_id"=> isset($data->region_name) ? $data->region_name : '',
                "area_name" => isset($data->title) ? $data->title : '',
                "area_title_fr" => isset($data->title_fr) ? $data->title_fr : '',
                // "rate" => isset($data->rate) ? $data->rate : '',
                "amount" => isset($data->delivery_amount) ? $data->delivery_amount : '',
                "fee" => isset($data->delivery_fee) ? $data->delivery_fee : '',
                "status" =>   isset($status) ? $status : '',
                "options" => isset($options) ? $options : '',
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
