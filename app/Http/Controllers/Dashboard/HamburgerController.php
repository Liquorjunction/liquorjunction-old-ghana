<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\Advertise;
use App\Models\SubCategories;
use App\Models\Quote;
use App\Models\Blog;
use App\Models\Hamburger;
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


class HamburgerController extends Controller
{
    //
    private $uploadPath = "/uploads/hamburger";
    private $uploadDataPath = "uploads/hamburger/";

    public function getUploadDataPath()
    {
        return $this->uploadDataPath;
    }

    public function index()
    {
        // echo "string";exit();
        return view("dashboard.hamburger.list");
    }

    public function create()
    {
        return view("dashboard.hamburger.create");
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $this->validateRequest();

        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadDataPath();
            // echo "<pre>";print_r($path);exit();
            $request->file($formFileName)->move($path, $fileFinalName_ar);
        }

        $blog = new Hamburger();
        $blog->title = $request->title;
        $blog->short_description = $request->short_description;
        $blog->long_description = $request->long_description;
        $blog->image = $fileFinalName_ar;
        $blog->status = 1;

        $blog->save();
        return redirect()->route('hamburger')->with('doneMessage', 'Hamburger created successfully.');

    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'title' => 'required|max:40',
                'short_description' => 'required|max:250',
                // 'image' => 'required',
                'long_description' => 'required',
            ]);

        }else{

            $validateData =request()->validate([
                'title' => 'required|max:40',
                'short_description' => 'required|max:250',
                'image' => 'required',
                'long_description' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function edit($id)
    {
        $blog_id = $id;

        $blogData = DB::table('hamburger')->where('id',$blog_id)->first();

        return view('dashboard.hamburger.edit', compact('blogData'));
    }

    public function update(Request $request,$id)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $this->validateRequest($id);

        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadDataPath();
            // echo "<pre>";print_r($path);exit();
            $request->file($formFileName)->move($path, $fileFinalName_ar);
            $category = Hamburger::where('id', $id)->update(array(
                
                'image' => $fileFinalName_ar,
            ));
        }
         $blog = Hamburger::find($id);
         $blog->title = $request->title;
         $blog->short_description = $request->short_description;
         $blog->long_description = $request->long_description;
         // $blog->image = $fileFinalName_ar;
         $blog->status = 1;

         $blog->save();
         return redirect()->route('hamburger')->with('doneMessage', 'Hamburger updated successfully.');
    }

    public function hamburgerUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Hamburger::wherein('id', $ids)->update(['status' => $status]);
               
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'hamburger deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'hamburger active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
    }

    public function destroy($id)
    {
        $advertise = Hamburger::find($id);
        $advertise->status = 2;
        $advertise->save();

        return redirect()->route('hamburger')
            ->with('doneMessage', 'Hamburger deleted successfully');
    }

    public function show($id)
    {
        $blog_id = $id;

        $blogData = DB::table('hamburger')->where('id',$blog_id)->first();

        return view('dashboard.hamburger.show', compact('blogData'));
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
             $sort='title';
        }elseif ($columnIndex==2) {
             $sort='short_description';
        }elseif ($columnIndex==3) {
            $sort='image';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        // $totalAr = Advertise::where('status','!=','2');
        $totalAr = DB::table('hamburger')->where('status','!=','2');
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('title', 'like', '%' . $searchValue . '%')
                        // ->orWhere(DB::raw("CONCAT(`main_users.first_name`, '+', `main_users.last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                    ->orWhere('short_description', 'like', '%' . $searchValue . '%');
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
           
           

             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }

            if($data->image){
                $checkFile = $this->uploadPath . '/' . $data->image;
                $image = $checkFile;
            }

            $image_show = '<div class="category-img"><a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a></div>';

           $categoryShow =  route('hamburger.show',['id'=>$data->id]);
            $categpryEdit =  route('hamburger.edit',['id'=>$data->id]);

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$categoryShow.'" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-advertise" data-id="'.$data->id.'" title="Show"> </a>';
            
                
            $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
        
            // $options .= '<a class="btn btn-sm success paddingset edit-advertise" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "title" =>   isset($data->title) ? $data->title : '' ,
              "short_description" =>   isset($data->short_description) ? $data->short_description : '' ,
              "image" =>   isset($image_show) ? $image_show : '' ,
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
