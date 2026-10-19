<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Yajra\Datatables\Datatables;

class LevelController extends Controller
{


    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,17,'read');
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

        $levels = Level::orderby('id', 'desc')->get();

        $level = count($levels);

        return view("dashboard.level.list",compact("levels","level"));


    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $webmasterId
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          // General for all pages
        return view("dashboard.level.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       

        $label = new Level();
        $this->validateRequest();
        $label->title = $request->title;
        $label->status = 1;
        $label->created_at = date('Y-m-d H:i:s');
        $label->updated_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('level')
            ->with('doneMessage', 'Record created successfully.');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $level = Level::find($id);

        return view('dashboard.level.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->validateRequest($id);
        $label = Level::find($id);
        $label->title = $request->title;
        $label->status = 1;
        $label->updated_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('level')->with('doneMessage', 'Record updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $label = Level::find($id);
        $label->delete();
        
        return redirect()->route('level')
            ->with('doneMessage', 'Level deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $level = Level::find($id);


        return view('dashboard.level.show', compact('level'));
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'title' => 'required',
            ], [
                    'title.required' => 'The level name field is required.'
                ]);

        }else{

            $validateData =request()->validate([
                'title' => 'required',
            ], [
                    'title.required' => 'The level name field is required.'
                ]);
            
        }

        return $validateData;
    }


    public function levelUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Level::wherein('id', $ids)->update(['status' => $status]);
               
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
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Level::where('status','!=','2');
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('title', 'like', '%' . $searchValue . '%');
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
            $levelShow =  route('level.show',['id'=>$data->id]);
            $levelEdit =  route('level.edit',['id'=>$data->id]);

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$levelShow.'" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="'.$levelEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "title" =>   isset($data->title) ? $data->title : '' ,
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
