<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use Auth;
use Illuminate\Config;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;
use Alert;
use App;
use Helper;

class BrandController extends Controller
{
    private $uploadPath = "uploads/brand/";
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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'read');
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
        $font_icon = DB::table('font_icon')->where('status', 1)->get();
        return view("dashboard.brand.list", compact('font_icon'));
    }

    public function store(Request $request)
    {

        if (empty($request->brand_id)) {
            $uniqid = uniqid();
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => [
                        'required', 'max:30',
                        Rule::unique('brand')->where(function ($query) {
                            return $query->where('status', '!=', 2);
                        }),
                    ],
                ]
            );
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $brand = new Brand();
            $brand->title = $request->input('title');
            $brand->title_fr = $request->input('title_fr');
            $brand->status = 1;
            $brand->save();

            Alert::success('Success', __('backend.New_brand_created_successfully'));
            return response()->json(['success' => 'true']);
        } 
        else {
            $id = $request->brand_id;
            $validator = \Validator::make($request->all(),    [
                'title' => [
                    'required',
                    Rule::unique('brand')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }


            $brand_id = $request->input('brand_id');
            $brand = Brand::where('id', $brand_id)->update(array(
                'title' => $request->title,
                'title_fr' => $request->title_fr,

            ));
            Alert::success('Success', __('backend.brand_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }



        return response()->json(['success' => true]);
    }



    public function validateRequest($id = "")
    {

        if ($id != "") {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('brand')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        } else {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => ['required', 'max:30', Rule::unique('brand')->where(function ($query) {
                    return $query->where('status', '!=', '3');
                })],

            ]);
        }
        // return $validateData;
    }

    public function brandUpdateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;
                Brand::wherein('id', $ids)->update(['status' => $status]);
                if ($status == 2) {
                    Product::where('status',1)->whereIN('brand_id', $ids)->update(array(
                        'status' => 0,
                    ));
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    Product::where('status',1)->whereIN('brand_id', $ids)->update(array(
                        'status' => 0,
                    ));
                    return response()->json(['success' => true, 'msg' => 'All products will be deactive under brand']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'All products will be active under brand']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }

    public function edit(Request $request)
    {
        // $Faq = Brand::find($id);
        if ($request->ajax()) {
            $brand_id = $request->id;
            $brandData = Brand::where('id', $brand_id)->where('status', '!=', 2)->first();
            if (!empty($brandData)) {

                $html = view('dashboard.brand.edit')->with(['brandData' => $brandData])->render();



                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'something wrong.']);
        }
        // echo "<pre>";print_r($brand);exit();

    }

    public function show(Request $request)
    {
        $brand_id = $request->id;
        $brandData = Brand::where('id', $brand_id)->where('status', '!=', 2)->first();

        if (!empty($brandData)) {

            $html =  view('dashboard.brand.show')->with(['brandData' => $brandData])->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        $brand->status = 2;
        $brand->save();

        return redirect()->route('brand')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $brand_id = $request->id;
        Brand::where('id', $brand_id)->update(['status' => 0]);
        Product::where('status',1)->where('brand_id', $brand_id)->update(array(
            'status' => 0,
        ));
        Alert::success('Success', __('All products will be deactive under brand'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $brand_id = $request->id;
        Brand::where('id', $brand_id)->update(['status' => 1]);
        Alert::success('Success', __('All products will be active under brand'));
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
            $sort = 'title';
        } elseif ($columnIndex == 2) {
            $sort = 'title_fr';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Brand::where('status', '!=', '2');

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'read'); 
        $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'create'); 
        $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'update');
        $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type,5,'delete');

        $active_class = "";
        $inactive_class = "role_status_inactive";
        if(isset($check_updation_permission) && $check_updation_permission){
            $active_class = "status_active";
            $inactive_class = "status_inactive";
        }
        foreach ($totalAr as $key => $data) {
            $brandShow =  route('brand.show', ['id' => $data->id]);
            $brandEdit =  route('brand.edit', ['id' => $data->uniqid]);
            
            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline '.$active_class.'" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline '.$inactive_class.'" title="Deactive" data-id="' . $data->id . '"></i>';
            }
            $options = '';
            if(isset($check_view_permission) && $check_view_permission){
                $options =   '<a class="btn btn-sm show-eyes list box-shadow paddingset show-brand" data-id="' . $data->id . '" title="Show"> </a>';
            }
            if(isset($check_updation_permission) && $check_updation_permission){
                $options .= '<a class="btn btn-sm success paddingset edit-brand"  data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            }
            if(isset($check_deletion_permission) && $check_deletion_permission){
                $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "brand_id" =>   isset($data->id) ? $data->id : '',
                "brand_name" => isset($data->title) ? $data->title : '',
                "brand_title_fr" => isset($data->title_fr) ? $data->title_fr : '',
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
