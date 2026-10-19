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

class RegionController extends Controller
{
    private $uploadPath = "uploads/region/";
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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,11,'read');
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

        $countries = Country::where('status', 1)->orderby('name', 'ASC')->get();
        // dd($country);
        return view("dashboard.region.list", compact('countries'));
    }
    public function store(Request $request)
    {
        if (empty($request->region_id)) {
            $uniqid = uniqid();
            $validator = \Validator::make(
                $request->all(),
                [
                    'country_id' => 'required',
                    'title' => [
                        'required', 'max:30',
                        Rule::unique('regions')->where(function ($query) {
                            return $query->where('status', '!=', 2);
                        }),
                    ],
                ]
            );
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $region = new Region();
            $region->country_id = $request->input('country_id');
            $region->title = $request->input('title');
            $region->title_fr = $request->input('title_fr');
            $region->status = 1;
            $region->save();

            Alert::success('Success', __('backend.New_region_created_successfully'));
            return response()->json(['success' => 'true']);
        }
        else {
            $id = $request->region_id;
            $validator = \Validator::make($request->all(),    [
                    'country_id' => 'required',
                    'title' => [
                        'required',
                        'max:45',
                        Rule::unique('regions')
                            ->where(function ($query) use ($request) {
                                return $query->where('status', '!=', '2');
                            })
                            ->where('country_id', $request->input('country_id'))
                            ->ignore($id), // Add the ID to ignore for updates
                    ],
    
                ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
          

            $region_id = $request->input('region_id');
            $region = Region::where('id', $region_id)->update(array(
                'country_id'=>$request->country_id,
                'title' => $request->title,
                'title_fr' => $request->title_fr,
              
            ));
            Alert::success('Success', __('backend.region_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }


        return response()->json(['success' => true]);
    }



    public function validateRequest($id = "")
    {

        if ($id != "") {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('regions')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        } else {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('regions')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        }
        // return $validateData;
    }

    public function regionUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;
                if($status==1){
                    $region = Region::wherein('id', $ids)->pluck('country_id'); 
                    $countryData = Country::whereIn('id',$region)->where('status','!=',1)->get();
                    $count = count($countryData);
                    if($count > 0){
                        return response()->json(['success' => true,'msg'=>'Country is inactive, Please first active the country']);                      
                    }
                }
                               
                Region::wherein('id', $ids)->where('status','!=',2)->update(['status' => $status]);
                Area::wherein('region_id', $ids)->where('status',1)->update(['status' =>  $status]);
                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deleted successfully']);
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
        // $Faq = region::find($id);
        if ($request->ajax()) {
            $region_id = $request->id;


            if ($region_id) {
                $editData = Region::where('id', $region_id)->where('status', '!=', 2)->first();
                $countryData = Country::where('status', '!=', 2)->orderby('name', 'ASC')->get();
                $html = view('dashboard.region.edit', ['editData' => $editData, 'countryData' => $countryData])->render();

                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong.']);
        }
        // echo "<pre>";print_r($region);exit();
    }


    public function show(Request $request)
    {
        $region_id = $request->id;
        $regionData = Region::where('id', $region_id)->where('status', '!=', 2)->first();

        if (!empty($regionData)) {

            $html =  view('dashboard.region.show')->with(['regionData' => $regionData])->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }
    public function getregion($country_id)
    {

        $countryCheck = Country::where('status', 1)->find($country_id);
        if (!$countryCheck) {
            return response()->json(['success' => true, 'code' => 201]);
        }
        $region = Region::where('country_id', $country_id)
        ->where('status', 1)
        ->orderBy('title', 'asc')
        ->get()
        ->toArray();
        return response()->json(['success' => true, 'code' => 200, 'data' => $region]);
    }
    public function destroy($id)
    {
        $region = region::find($id);
        $region->status = 2;
        $region->save();

        return redirect()->route('region')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $region_id = $request->id;
        Region::where('id', $region_id)->where('status',1)->update(['status' => 0]);
        Area::where('region_id', $region_id)->where('status',1)->update(['status' => 0]);
        Alert::success('Success', __('backend.region_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $region_id = $request->id;
        $region = Region::where('id', $region_id)->first();
        $country_info = Country::where('id',$region->country_id)->first();
        if($country_info->status!=1){
            Alert::warning('Warning', __('Country is inactive, Please first active the country'));
            return response()->json(['success' => 'true']);
        } 
        Region::where('id', $region_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.region_active_sucessfully'));
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
        } elseif ($columnIndex == 1) {
            $sort = 'countries.id';
        } elseif ($columnIndex == 2) {
            $sort = 'title';
        } elseif ($columnIndex == 3) {
            $sort = 'title_fr';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Region::rightJoin('countries', 'countries.id', '=', 'regions.country_id')
            ->select('regions.*', 'countries.name as country_name', 'countries.id as country_id')
            ->where('regions.status', '!=', 2);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('countries.name', 'like', '%' . $searchValue . '%');
                $query->orWhere('title', 'like', '%' . $searchValue . '%');
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
            $regionShow =  route('region.show', ['id' => $data->id]);
            $regionEdit =  route('region.edit', ['id' => $data->uniqid]);

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
            }
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options =   '<a class="btn btn-sm show-eyes list box-shadow paddingset show-region" data-id="' . $data->id . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-region"  data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';


            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "region_id" => isset($data->id) ? $data->id : '',
                "country_id" => isset($data->country_name) ? $data->country_name : '',
                "region_name" => isset($data->title) ? $data->title : '',
                "region_title_fr" => isset($data->title_fr) ? $data->title_fr : '',
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
