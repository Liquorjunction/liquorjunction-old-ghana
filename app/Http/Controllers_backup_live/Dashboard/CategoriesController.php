<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use Auth;
use Illuminate\Config;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;
use Alert;
use App;
use Helper;

class CategoriesController extends Controller
{
    //
    private $uploadPath = "uploads/category/";
    private $uploadPath1 = "uploads/categoryback/";

    public function getUploadPath()
    {
        return $this->uploadPath;
    }
    public function getUploadPath1()
    {
        return $this->uploadPath1;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }
    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'read');
        if ($check_view_permission == false) {
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
        return view("dashboard.category.list", compact('font_icon'));
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;
        // dd(1);
        if (empty($request->category_id)) {
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => [
                        'required',
                        'max:30',
                        Rule::unique('categories')->where(function ($query) {
                            return $query->where('status', '!=', 2);
                        }),
                    ],
                    // 'url' => 'url',
                    'imagefile' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate image
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate image

                ],
                [
                    'imagefile.max' => 'The image should be less than 2 MB.',
                    'imagefile.image' => 'Image file type must be jpeg,png,jpg.',
                    'imagefile.mimes' => 'Image file type must be jpeg,png,jpg.',
                    'photo.max' => 'The image should be less than 2 MB.',
                    'photo.image' => 'Image file type must be jpeg,png,jpg.',
                    'photo.mimes' => 'Image file type must be jpeg,png,jpg.',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $category = new Categories();
            $category->uniqid = uniqid();
            $category->title = $request->title;
            $category->title_fr = $request->title_fr;
            $category->url = $request->url;
            // $category->icon_id = $request->icon_id;
            // $category->description = $request->description;
            // $category->description_fr = $request->description_fr;
            $category->status = 1;


            if ($request->hasFile('imagefile')) {
                $image = $request->file('imagefile');
                $imageName = time() . '_imagefile.' . $image->getClientOriginalExtension();
                $path = $this->getUploadPath();
                $image->move($path, $imageName);
                $category->imagefile = $imageName;
            }

            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $imageName = time() . '_photo.' . $image->getClientOriginalExtension();
                $path = $this->getUploadPath1();
                $image->move($path, $imageName);
                $category->photo = $imageName;
            }

            $category->save();

            Alert::success('Success', __('backend.New_Category_created_successfully'));
            return response()->json(['success' => 'true']);
        } else {
            // dd(1);
            $id = $request->category_id;
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => [
                        'required',
                        Rule::unique('categories')->ignore($id)->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],

                    'imagefile' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate image
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate image
                ],
                [
                    'imagefile.max' => 'The profile photo should be less than 2 MB.',
                    'imagefile.image' => 'Image file type must be jpeg,png,jpg.',
                    'imagefile.mimes' => 'Image file type must be jpeg,png,jpg',
                    'photo.max' => 'The profile photo should be less than 2 MB.',
                    'photo.image' => 'Image file type must be jpeg,png,jpg.',
                    'photo.mimes' => 'Image file type must be jpeg,png,jpg',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $category = Categories::find($id);
            // dd($category);

            if ($category) {

                if ($request->hasFile('imagefile')) {
                    // dd(1);
                    $image = $request->file('imagefile');
                    $imageName = time() . '_imagefile.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/category'), $imageName);

                    if (!empty($category->imagefile)) {
                        $oldImageFilePath = public_path('uploads/category/') . $category->imagefile;
                        if (file_exists($oldImageFilePath)) {
                            unlink($oldImageFilePath);
                        }
                    }
                    $category->update([
                        'title' => $request->title,
                        'title_fr' => $request->title_fr,
                        'imagefile' => $imageName,
                    ]);
                } else {
                    // dd($request->all());
                    $category->update([
                        'title' => $request->title,
                        'title_fr' => $request->title_fr,
                        'url' => $request->url,

                    ]);
                }
            }

            $category = Categories::find($id);

            if ($category) {
                if ($request->hasFile('photo')) {
                    $image = $request->file('photo');
                    $imageName = time() . '_photo.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/categoryback'), $imageName);

                    if (!empty($category->photo)) {
                        $oldImageFilePath = public_path('uploads/categoryback/') . $category->photo;
                        if (file_exists($oldImageFilePath)) {
                            unlink($oldImageFilePath);
                        }
                    }
                    $category->update([
                        'title' => $request->title,
                        'title_fr' => $request->title_fr,
                        'photo' => $imageName,
                    ]);
                } else {
                    // dd($request->all());
                    
                    $category->update([

                        'title' => $request->title,
                        'title_fr' => $request->title_fr,
                        'url' => $request->url,
                    ]);
                    // dd($category);
                }
            }
        
            Alert::success('Success', __('backend.Category_has_been_updated_successfully'));
            return response()->json(['success' => 'true']);
        }
    }



    public function validateRequest($id = "")
    {

        if ($id != "") {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => [
                    'required',
                    'max:30',
                    Rule::unique('categories')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],

            ]);
        } else {
            $validateData = request()->validate([
                // 'name' => 'required|unique:vehicle_model||max:30',
                'title' => 'required|unique:categories,title,NULL,id,status,1',


            ]);
        }
        // return $validateData;
    }

    public function categoryUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Categories::wherein('id', $ids)->update(['status' => $status]);
                $category = SubCategories::wherein('category_id', $ids)->where('status', '!=', 2)->update(
                    array(
                        'status' => $status,
                    )
                );
                if ($status != 1) {
                    $product = Product::wherein('category_id', $ids)->update(
                        array(
                            'status' => $status,
                        )
                    );
                }
                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'All products will be deactive under category']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'All products will be active under category']);
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
            $category_id = $request->category_id;
            $categoryData = Categories::where('id', $category_id)->where('status', '!=', 2)->first();
            if (!empty($categoryData)) {
                $font_icon = DB::table('font_icon')->where('status', 1)->get();
                // echo "<pre>";print_r($font_icon->toArray());exit();
                $html = view('dashboard.category.edit')->with(['categoryData' => $categoryData])->render();



                return response()->json(['success' => true, 'html' => $html]);
            }
            return response()->json(['success' => false, 'msg' => 'something wrong.']);
        }
        // echo "<pre>";print_r($category);exit();

    }

    public function show(Request $request)
    {
        $category_id = $request->category_id;
        $categoryData = Categories::where('id', $category_id)->where('status', '!=', 2)->first();

        if (!empty($categoryData)) {
            $icon_name = DB::table('font_icon')->where('id', $categoryData->icon_id)->first();
            $html = view('dashboard.category.show')->with(['categoryData' => $categoryData])->render();

            return response()->json(['success' => true, 'html' => $html]);
        }
        return response()->json(['success' => false, 'msg' => 'something wrong.']);
    }

    public function destroy($id)
    {
        $category = Categories::find($id);

        $subcategory = SubCategories::where('category_id', $id)->where('status', '!=', 2)->update(
            array(
                'status' => 0,
            )
        );
        $product = Product::where('category_id', $id)->update(
            array(
                'status' => 0,

            )
        );
        $category->status = 2;
        $category->save();

        return redirect()->route('category')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function status_active(Request $request)
    {
        $category_id = $request->category_id;
        Categories::where('id', $category_id)->update(['status' => 0]);
        $category = SubCategories::where('category_id', $category_id)->where('status', '!=', 2)->update(
            array(
                'status' => 0,
            )
        );
        $product = Product::where('category_id', $category_id)->update(
            array(
                'status' => 0,
            )
        );
        Alert::success('Success', __('All products will be deactive under category'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $category_id = $request->category_id;
        Categories::where('id', $category_id)->update(['status' => 1]);
        $category = SubCategories::where('category_id', $category_id)->where('status', '!=', 2)->update(
            array(
                'status' => 1,
            )
        );
        // $product = Product::where('category_id', $category_id)->update(array(
        //     'status' => 1,
        // ));
        Alert::success('Success', __('All products will be active under category'));
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
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Categories::where('status', '!=', '2');

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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'read');
        $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'create');
        $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'update');
        $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type, 6, 'delete');
        $active_class = "";
        $inactive_class = "role_status_inactive";
        if (isset($check_updation_permission) && $check_updation_permission) {
            $active_class = "status_active";
            $inactive_class = "status_inactive";
        }
        foreach ($totalAr as $key => $data) {
            $categoryShow = route('category.show', ['id' => $data->id]);
            $categpryEdit = route('category.edit', ['id' => $data->uniqid]);

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline ' . $active_class . '" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline ' . $inactive_class . '" title="Deactive" data-id="' . $data->id . '"></i>';
            }

            $options = '';
            if (isset($check_view_permission) && $check_view_permission) {
                $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="' . $data->id . '" title="show"> </a>';
            }

            if (isset($check_updation_permission) && $check_updation_permission) {
                $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            }
            if (isset($check_deletion_permission) && $check_deletion_permission) {
                $options .= '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }


            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" => isset($data->id) ? $data->id : '',
                "category_name" => isset($data->title) ? $data->title : '',
                "status" => isset($status) ? $status : '',
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
