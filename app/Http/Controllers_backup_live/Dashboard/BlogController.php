<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Advertise;
use App\Models\Blog;
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

class BlogController extends Controller
{
    //
    private $uploadPath = "/uploads/blog";
    private $uploadDataPath = "uploads/blog/";

    public function getUploadDataPath()
    {
        return $this->uploadDataPath;
    }

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,14,'read');
        if($check_view_permission==false){
            abort(404);
        }
    }

    public function index()
    {
        // echo "string";exit();
        return view("dashboard.blog.list");
    }

    public function create()
    {
        return view("dashboard.blog.create");
    }

    public function store(Request $request)
    {
        $this->validateRequest();
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(
                1111,
                9999
            ) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadDataPath();
            $request->file($formFileName)->move($path, $fileFinalName_ar);
        }

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->title_fr = $request->title_fr;
        $blog->short_description = $request->short_description;
        $blog->short_description_fr = $request->short_description_fr;
        $blog->long_description = $request->long_description;
        $blog->long_description_fr = $request->long_description_fr;
        $blog->image = $fileFinalName_ar;
        $blog->status = 1;
        $blog->save();
        return redirect()->route('blog')->with('doneMessage', 'Blog created successfully.');
    }

    public function validateRequest($id = "")
    { 

        if ($id != "") {
            $validateData = request()->validate([
                'title' => 'required|max:40',
                'title_fr' => 'required|max:40',
                'short_description' => 'required|max:250',
                'short_description_fr' => 'required|max:250',
                'long_description' => 'required',
                'long_description_fr' => 'required',
                'image' =>'image|mimes:jpeg,png,jpg|max:2048',  
            ],
            [
                'image.required' => 'The image field is required.',
                'image.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                'image.max' => 'The image should be less than 2 MB.',
                'image.image'=>'The image must be a file of type: jpg, jpeg, png.',
            ]
        
        );
        } else {
            $validateData = request()->validate([
                'title' => 'required|max:40',
                'title_fr' => 'required|max:40',
                'short_description' => 'required|max:250',
                'short_description_fr' => 'required|max:250',
                'long_description' => 'required',
                'long_description_fr' => 'required',
                'image' =>'required|image|mimes:jpeg,png,jpg|max:2048',                
            ],[
                'image.required' => 'The image field is required.',
                'image.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                'image.max' => 'The image should be less than 2 MB.',
                'image.image'=>'The image must be a file of type: jpg, jpeg, png.',
            ]
        
        );
        }

        return $validateData;
    }

    public function edit($id)
    {
        $blog_id = $id;

        $blogData = DB::table('blog')->where('id', $blog_id)->first();

        return view('dashboard.blog.edit', compact('blogData'));
    }

    public function update(Request $request,$id)
    {
        $this->validateRequest($id);
        $blog = Blog::find($id);

        $formFileName = "image";
        $fileFinalName_ar = "";
        if($blog){
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(1111,
                        9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadDataPath();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
                $update_data['image'] = $fileFinalName_ar;
            }
            $update_data['title'] = $request->title;
            $update_data['title_fr'] = $request->title_fr;
            $update_data['short_description'] = $request->short_description;
            $update_data['short_description_fr'] = $request->short_description_fr;
            $update_data['long_description'] = $request->long_description;
            $update_data['long_description_fr'] = $request->long_description_fr;
            $update_data['status'] = 1;
            $blogdata= Blog::where('id', $id)->update($update_data);
        }       
        return redirect()->route('blog')->with('doneMessage', 'Blog updated successfully.');
    }
    public function blogUpdateAll(Request $request)
    {

        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Blog::wherein('id', $ids)->update(['status' => $status]);

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
    public function status_active(Request $request)
    {
        $blog_id = $request->id;
        Blog::where('id', $blog_id)->update(['status' => 0]);
        \Alert::success('Success', __('backend.blog_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $blog_id = $request->id;
        Blog::where('id', $blog_id)->update(['status' => 1]);
        \Alert::success('Success', __('backend.blog_active_sucessfully'));
        return response()->json(['success' => 'true']);
    }
    public function destroy($id)
    {
        $advertise = Blog::find($id);
        $advertise->status = 2;
        $advertise->save();

        return redirect()->route('blog')
            ->with('doneMessage', 'Blog deleted successfully');
    }

    public function show($id)
    {
        $blog_id = $id;

        $blogData = DB::table('blog')->where('id', $blog_id)->first();

        return view('dashboard.blog.show', compact('blogData'));
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
            $sort = 'short_description';
        }  else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        // $totalAr = Advertise::where('status','!=','2');
        $totalAr = DB::table('blog')->where('status', '!=', '2');

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('title', 'like', '%' . $searchValue . '%')
                    // ->orWhere(DB::raw("CONCAT(`main_users.first_name`, '+', `main_users.last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                    ->orWhere('short_description', 'like', '%' . $searchValue . '%');
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



            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
            }

            if ($data->image) {
                $checkFile =  asset('uploads/blog/' . $data->image);
                $image = '<img src="' .$checkFile. '" alt="' .$data->image. '"  style="width:100px !important; height:100px !important;" >';
            }else{
                $checkFile =  asset('uploads/contacts/noimage.png');
                $image = '<img  src="'.$checkFile.'" class="thumbnail"  style="width:100px !important; height:100px !important; max-width:none !important;" />';
            }

            $image_show = '<a href="' . $checkFile . '" alt="' .$data->image . '" target="_blank" style="cursor: pointer;"> '.$image.'</a>';

            $categoryShow =  route('blog.show', ['id' => $data->id]);
            $categpryEdit =  route('blog.edit', ['id' => $data->id]);

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $categoryShow . '" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-advertise" data-id="'.$data->id.'" title="Show"> </a>';


            $options .= '<a class="btn btn-sm success paddingset" href="' . $categpryEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';

            // $options .= '<a class="btn btn-sm success paddingset edit-advertise" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" =>   isset($data->id) ? $data->id : '',
                "title" =>   isset($data->title) ? $data->title : '',
                "short_description" =>   isset($data->short_description) ? $data->short_description : '',
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
