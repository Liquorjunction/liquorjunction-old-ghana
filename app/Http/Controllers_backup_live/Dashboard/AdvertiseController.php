<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\Advertise;
use App\Models\SubCategories;
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

class AdvertiseController extends Controller
{
    //
    private $uploadPath = "/uploads/advertise";
    private $uploadDataPath = "uploads/advertise/";
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function getUploadDataPath()
    {
        return $this->uploadDataPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = env('APP_URL') . $uploadPath;
    }
    public function setUploadDataPath($uploadPath)
    {
        $this->uploadDataPath = Config::get('app.APP_URL') . $uploadDataPath;
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // echo "string";exit();
        $wholesalerData = DB::table('main_users')->where('user_type', 2)->where('status', 1)->orderby('name', 'ASC')->get();
        return view("dashboard.advertise.list", compact('wholesalerData'));
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;

        if (empty($request->advertise_id)) {
            $uniqid = uniqid();
            $advertise = new Advertise();
            // $this->validateRequest();

            $validator = \Validator::make($request->all(), [
                'website_banner' => ['required', 'dimensions:min_width=1400,min_height=300'],
                'mobile_banner' => ['required', 'dimensions:min_width=1400,min_height=300'],

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $formFileName = "website_banner";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadDataPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
            }

            $formFileName1 = "mobile_banner";
            $fileFinalName_ar1 = "";
            if ($request->$formFileName1 != "") {
                $fileFinalName_ar1 = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName1)->getClientOriginalExtension();
                $path = $this->getUploadDataPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName1)->move($path, $fileFinalName_ar1);
            }


            $advertise->uniqid = $uniqid;
            $advertise->wholesaler_id = $request->wholesaler_id;
            // $advertise->description = $request->description;
            $advertise->image = $fileFinalName_ar;
            $advertise->mobile_banner = $fileFinalName_ar1;
            $advertise->status = 1;
            $advertise->save();
            // echo "string";exit();
            // return view("dashboard.category.list");
            // return redirect()->route('category');
            Alert::success('Success', __('backend.New_Advertise_created_successfully'));
            return response()->json(['success' => 'true']);
        } else {

            $validator = \Validator::make($request->all(), [
                'website_banner_edit' => ['dimensions:min_width=300,min_height=200'],
                'mobile_banner_edit' => ['dimensions:min_width=300,min_height=200'],

            ], [
                'website_banner_edit.dimensions' => "The website banner has invalid image dimensions.",
                'mobile_banner_edit.dimensions' => "The mobile banner has invalid image dimensions.",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $advertise_id = $request->advertise_id;
            $formFileName = "website_banner_edit";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadDataPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
                $category = Advertise::where('id', $advertise_id)->update(array(

                    'image' => $fileFinalName_ar,
                ));
            }

            $formFileName1 = "mobile_banner_edit";
            $fileFinalName_ar1 = "";
            if ($request->$formFileName1 != "") {
                $fileFinalName_ar1 = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName1)->getClientOriginalExtension();
                $path = $this->getUploadDataPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName1)->move($path, $fileFinalName_ar1);
                $category = Advertise::where('id', $advertise_id)->update(array(

                    'mobile_banner' => $fileFinalName_ar1,
                ));
            }

            $category = Advertise::where('id', $advertise_id)->update(array(
                'wholesaler_id' => $request->wholesaler_id,
            ));
            Alert::success('Success', __('backend.Advertise_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }
    }

    public function advertiseUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Advertise::wherein('id', $ids)->update(['status' => $status]);
                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record deleted successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'Advertise deactive successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Advertise active successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }

    public function edit(Request $request)
    {
        // $Faq = Categories::find($id);
        if ($request->ajax()) {
            $advertise_id = $request->advertise_id;
            $advertiseData = Advertise::where('id', $advertise_id)->where('status', '!=', 2)->first();
            if (!empty($advertiseData)) {
                $wholesalerData = DB::table('main_users')->where('user_type', 2)->where('status', 1)->orderby('name', 'ASC')->get();
                $html =  view('dashboard.advertise.edit')->with(['advertiseData' => $advertiseData, 'wholesalerData' => $wholesalerData])->render();



                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'something wrong.']);
        }
    }

    public function show(Request $request)
    {
        $advertise_id = $request->advertise_id;
        $advertiseData = Advertise::where('id', $advertise_id)->where('status', '!=', 2)->first();

        if (!empty($advertiseData)) {
            if (!empty($advertiseData->wholesaler_id)) {

                $wholesalerData = DB::table('main_users')->where('id', $advertiseData->wholesaler_id)->first();
                $store_name = $wholesalerData->store_name;
            } else {
                $store_name = "Not Assign";
            }
            $html =  view('dashboard.advertise.show')->with(['advertiseData' => $advertiseData, 'store_name' => $store_name])->render();



            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }

    public function destroy($id)
    {
        $advertise = Advertise::find($id);
        $advertise->status = 2;
        $advertise->save();

        return redirect()->route('advertise')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $advertise_id = $request->advertise_id;
        Advertise::where('id', $advertise_id)->update(['status' => 0]);

        Alert::success('Success', __('backend.Advertise_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $advertise_id = $request->advertise_id;
        Advertise::where('id', $advertise_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.Advertise_active_sucessfully'));
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
            $sort = 'main_users.store_name';
        } elseif ($columnIndex == 2) {
            $sort = 'advertise.image';
        } elseif ($columnIndex == 3) {
            $sort = 'advertise.mobile_banner';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Advertise::leftjoin('main_users', 'main_users.id', '=', 'advertise.wholesaler_id')->select('advertise.*', 'main_users.store_name')->where('advertise.status', '!=', '2');

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('main_users.store_name', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $categoryShow =  route('category.show', ['id' => $data->id]);
            $categpryEdit =  route('category.edit', ['id' => $data->uniqid]);

            if (!empty($data->wholesaler_id)) {
                $store_name = $data->store_name;
            } else {
                $store_name = "Not Assign";
            }

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
            }

            if ($data->mobile_banner) {
                $checkFile = $this->uploadPath . '/' . $data->mobile_banner;
                $image = $checkFile;
            }
            $image_show_mobile = '<div class="category-img"><a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a></div>';

            if ($data->image) {
                $checkFile = $this->uploadPath . '/' . $data->image;
                $image = $checkFile;
            }
            $image_show = '<div class="category-img"><a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a></div>';

            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-advertise" data-id="' . $data->id . '" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-advertise" data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" =>   isset($data->id) ? $data->id : '',
                "advertise_name" =>   isset($store_name) ? $store_name : '',
                "advertise_description" =>   isset($image_show_mobile) ? $image_show_mobile : '',
                "image" =>   isset($image_show) ? $image_show : '',
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
