<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
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
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use App\Models\Product;
// use App\Helpers\ Helper;

class SubCategoriesController extends Controller
{
    //
    //
    private $uploadPath = "uploads/subcategory/";
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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'read');
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
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        return view("dashboard.subcategory.list", compact('categories'));
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;

        if (empty($request->subcategory_id)) {
            $uniqid = uniqid();
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => [
                        'required',
                        'max:45',
                        Rule::unique('sub_categories', 'title')
                            ->where(function ($query) use ($request) {
                                $query->where('status', '!=', '2')
                                    ->where('category_id', $request->category_id);
                            }),
                    ],
                ],
            [
            ]
        );
            if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $subcategory = new SubCategories();
        $subcategory->uniqid = $uniqid;
        $subcategory->category_id = $request->category_id;
        $subcategory->title = $request->title;
        $subcategory->title_fr = $request->title_fr;
        // $subcategory->image = $fileFinalName_ar;
        $subcategory->status = 1;
        $subcategory->save();
       
         Alert::success('Success', __('backend.New_SubCategory_created_successfully'));
        return response()->json(['success' => 'true']);
        }else{
            $id = $request->subcategory_id;
             $validator = \Validator::make($request->all(), [
                'category_id'=>'required',
                'title' => [
                    'required',
                    'max:45',
                    Rule::unique('sub_categories')
                        ->where(function ($query) use ($request) {
                            return $query->where('status', '!=', '2');
                        })
                        ->where('category_id', $request->input('category_id'))
                        ->ignore($id), // Add the ID to ignore for updates
                ],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $subcategory_id = $request->subcategory_id;
             $category = SubCategories::where('id', $subcategory_id)->update(array(
                'title' => $request->title,
                'title_fr' => $request->title_fr,
                'category_id' => $request->category_id,
            ));
            Alert::success('Success', __('backend.SubCategory_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }
    }

    public function getsubcategories($category_id)
    {

        $categoryCheck = Categories::where('status', 1)->find($category_id);
        if (!$categoryCheck) {
            return response()->json(['success' => true, 'code' => 201]);
        }
        $subcategories = SubCategories::where('category_id', $category_id)
        ->where('status', 1)
        ->orderBy('title', 'asc')
        ->get()
        ->toArray();
        return response()->json(['success' => true, 'code' => 200, 'data' => $subcategories]);
    }



    public function validateRequest($id = "")
    {

        if ($id != "") {
            $validateData = request()->validate([
                'title' => [
                    'required',
                    'max:45',
                    Rule::unique('subcategories', 'title')
                        ->where(function ($query) {
                            $query->where('status', '!=', '2')
                                ->where('category_id');
                        })
                ],
            ]);
        } else {

            $validateData = request()->validate([
                'title' => 'required',
                'image' => 'required',
            ]);
        }

        return $validateData;
    }

    public function subcategoryUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;
                if($status==1){
                    $subcategory = SubCategories::wherein('id', $ids)->pluck('category_id'); 
                    $categoryData = Categories::whereIn('id',$subcategory)->where('status','!=',1)->get();
                   
                    $count = count($categoryData);
                    if($count > 0){
                        return response()->json(['success' => true,'msg'=>'Category is inactive, Please first active the category']);                      
                    }
                    // $product = Product::wherein('id', $ids)->update(array(
                    //     'status' => $status,
                    // ));
                }
                SubCategories::wherein('id', $ids)->update(['status' => $status]);
                if($status != 1){
                $product = Product::wherein('subcategory_id', $ids)->update(array(
                    'status' => $status,
                ));
                }else{
                    $product = Product::wherein('subcategory_id', $ids)->update(array(
                        'status' => 0,
                    )); 
                }

                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'All products will be deactive under subcategory']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'All products will be active under subcategory']);
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
            $subcategory_id = $request->subcategory_id;
            $subcategoryData = SubCategories::where('id', $subcategory_id)->where('status', '!=', 2)->first();
            if (!empty($subcategoryData)) {

                $categories = Categories::where('status', '!=', 2)->orderby('title', 'ASC')->get();
                $html =  view('dashboard.subcategory.edit')->with(['subcategoryData' => $subcategoryData, 'categories' => $categories])->render();

                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'something wrong.']);
        }

    }

    public function show(Request $request)
    {
        $subcategory_id = $request->subcategory_id;
        // $categoryData = Categories::where('id',$category_id)->where('status','!=',2)->first();
        $subcategoryData = SubCategories::with(['category'])->where('id', $subcategory_id)->where('status', '!=', '2')->first();
        // echo "<pre>";print_r($subcategoryData->toArray());exit();

        if (!empty($subcategoryData)) {
            $html =  view('dashboard.subcategory.show')->with(['subcategoryData' => $subcategoryData])->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }

    public function destroy($id)
    {
        $subcategory = SubCategories::find($id);
        $subcategory->status = 2;
        $subcategory->save();

        return redirect()->route('subcategory')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $subcategory_id = $request->subcategory_id;
        SubCategories::where('id', $subcategory_id)->update(['status' => 0]);
        $product = Product::where('subcategory_id', $subcategory_id)->update(array(
            'status' => 0,
        ));
        \Alert::success('Success', __('All products will be deactive under subcategory'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $subcategory_id = $request->subcategory_id;
        $subcategory = SubCategories::where('id', $subcategory_id)->first();
        $category = Categories::where('id',$subcategory->category_id)->first();
        if($category->status!=1){
            Alert::warning('Warning', __('Category is inactive, Please first active the category'));
            return response()->json(['success' => 'true']);
        } 
        SubCategories::where('id', $subcategory_id)->update(['status' => 1]);
        \Alert::success('Success', __('All products will be active under subcategory'));
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
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'sub_categories.id';
        } elseif ($columnIndex == 1) {
            $sort = 'categories.title';
        } elseif ($columnIndex == 2) {
            $sort = 'sub_categories.title';
        } 
        else {
            $sort = 'sub_categories.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // $totalAr = SubCategories::with(['category'])->where('status','!=','2');
        $totalAr = DB::table('sub_categories')
        ->leftjoin('categories', 'categories.id', '=', 'sub_categories.category_id')
        ->select('sub_categories.*', 'categories.title as category_name', 'categories.id as category_id')
        ->where('sub_categories.status', '!=', 2);

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('sub_categories.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('categories.title', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr = [];
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'read'); 
        $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'create'); 
        $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'update');
        $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type,7,'delete');
        $active_class = "";
        $inactive_class = "role_status_inactive";
        if(isset($check_updation_permission) && $check_updation_permission){
            $active_class = "status_active";
            $inactive_class = "status_inactive";
        }
        foreach ($totalAr as $key => $data) {
            $categoryShow =  route('category.show', ['id' => $data->id]);
            $categpryEdit =  route('category.edit', ['id' => $data->uniqid]);

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline '.$active_class.'" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline '.$inactive_class.'" title="Deactive" data-id="' . $data->id . '"></i>';
            }

            if(isset($check_view_permission) && $check_view_permission){
                $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-subcategory" data-id="' . $data->id . '" title="Show"> </a>';
            }
            if(isset($check_updation_permission) && $check_updation_permission){
                $options .= '<a class="btn btn-sm success paddingset edit-subcategory" data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            }
            if(isset($check_deletion_permission) && $check_deletion_permission){
                $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" =>   isset($data->id) ? $data->id : '',
                "category_name" =>   isset($data->category_name) ? $data->category_name : '',
                "subcategory_name" =>   isset($data->title) ? $data->title : '',
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
