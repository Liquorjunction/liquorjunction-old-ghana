<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\MaterialCategory;
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


class MaterialCategoryController extends Controller
{
    //

     public function index()
    {
        // echo "string";exit();
        $font_icon = DB::table('font_icon')->where('status',1)->get();
        return view("dashboard.material_category.list",compact('font_icon'));
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        if (empty($request->material_category_id)) {

             $validator = \Validator::make($request->all(), [
                    'name' => ['required',Rule::unique('material_category')->where(function ($query){
            return $query->where('status','!=','2');
                })],

            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
            
         $material_category = new MaterialCategory();
        $material_category->name = $request->name;
        $material_category->status = 1;

        $material_category->save();

        Alert::success('Success', __('backend.New_Material_category_created_successfully'));
        return response()->json(['success' => 'true']);
        }else{
             $material_category_id = $request->material_category_id;
             $validator = \Validator::make($request->all(), [
                    'name' => ['required',Rule::unique('material_category')->ignore($material_category_id)->where(function ($query){
            return $query->where('status','!=','2');
                })],

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $time_frame = MaterialCategory::where('id', $material_category_id)->update(array(
                'name' => $request->name,
            ));
             Alert::success('Success', __('backend.Material_category_has_been_updated_successfully'));
             return response()->json(['success' => 'true']);
        }
    }

     public function edit(Request $request)
    {
        // $Faq = Categories::find($id);
        if ($request->ajax())
        {   
            $material_category_id = $request->material_category_id;
            $materialData = MaterialCategory::where('id',$material_category_id)->where('status','!=',2)->first();
            if(!empty($materialData))
            {
               
                $html =  view('dashboard.material_category.edit')->with(['materialData' => $materialData])->render();

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);

 

        }
        // echo "<pre>";print_r($category);exit();

    }

     public function show(Request $request)
    {
        $material_category_id = $request->material_category_id;
       $materialData = MaterialCategory::where('id',$material_category_id)->where('status','!=',2)->first();
        
         if(!empty($materialData))
            {
                
                $html =  view('dashboard.material_category.show')->with(['materialData' => $materialData])->render();

 

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
    }

    public function materialcategoryUpdateAll(Request $request)
    {
         if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                MaterialCategory::wherein('id', $ids)->update(['status' => $status]);
               
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Material Category deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Material Category active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
    }

     public function status_active(Request $request){
        $material_category_id = $request->material_category_id;
         MaterialCategory::where('id', $material_category_id)->update(['status' => 0]);
         
         Alert::success('Success', __('backend.MaterialCategory_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        $material_category_id = $request->material_category_id;
         MaterialCategory::where('id', $material_category_id)->update(['status' => 1]);
         
        Alert::success('Success', __('backend.MaterialCategory_active_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function destroy($id)
    {
        $category = MaterialCategory::find($id);

        
        $category->status = 2;
        $category->save();

        return redirect()->route('material_category')
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

        $totalAr = MaterialCategory::where('status','!=','2');
               
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
        // echo "<pre>";print_r($totalAr);exit();
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
           

             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-materialcategory" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-materialcategory"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "name" =>   isset($data->name) ? $data->name : '' ,
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
