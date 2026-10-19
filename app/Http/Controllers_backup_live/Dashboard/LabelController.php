<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Yajra\Datatables\Datatables;

class LabelController extends Controller
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

        $labels = Label::orderby('id', 'desc')->get();

        $label = count($labels);

        return view("dashboard.label.list",
            compact("labels","label"));


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
        return view("dashboard.label.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       

        $label = new Label();
        $this->validateRequest();
        $label->language_id = 2;
        $label->label_name = $request->label_name;
        $label->label_value = $request->label_value;
        $label->label_value_fr = $request->label_value_fr;
        $label->label_type = $request->label_type;
        $label->status = 1;
        //$label->created_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('label')
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
        $label = Label::find($id);
            return view('dashboard.label.edit', compact('label'));
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
        $label = Label::find($id);
        //$label->labelname = $request->label_key;
        $label->label_value = $request->label_value;
        $label->label_value_fr = $request->label_value_fr;
        $label->label_type = $request->label_type;
        $label->status = 1;
        $label->updated_at = date('Y-m-d H:i:s');
        $label->save(); 

        return redirect()->route('label')->with('doneMessage', 'Record updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $label = Label::find($id);
        $label->delete();
        
        return redirect()->route('label')
            ->with('doneMessage', 'Label deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $label = Label::find($id);


        return view('dashboard.label.show', compact('label'));
    }

    public function validateRequest($id="")
    {
        
        if($id !="")
        {
            $validateData =request()->validate([
              //  'label_name'=>'required',
                'label_value' => 'required',
                'label_value_fr'=>'required',
                // 'label_type'=>'required'
            ],
            [
               // 'label_name.required'=>'The key Field is required'
               'label_type.required'=>'The label type field is required.'

            ]
        );

        }else{

            $validateData =request()->validate([
                'label_name' => 'required',
                'label_value'=>'required',
                'label_value_fr' => 'required',
                'label_type'=>'required'

            ],
            [
                'label_name.required'=>'This key Field is required',
               'label_type.required'=>'The label type field is required.'

            ]
        );
            
        }

        return $validateData;
    }


    public function labelUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Label::wherein('id', $ids)->update(['status' => $status]);
               
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
             $sort='label_name';
        }elseif ($columnIndex==2) {
            $sort='label_value';
        }elseif($columnIndex==3){
            $sort='label_type';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Label::where('status','!=','2');
        
        if ($searchValue!="") {

            $totalAr = $totalAr->where(function ($query) use ($searchValue) {

                $search = "";

                if($searchValue == 'Mobile' || $searchValue == 'mobile' )
                {
                  $search = '0';
                  $query->orWhere('label_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('label_value', 'like', '%' . $searchValue . '%')
                  ->orWhere('label_type', 'like', '%' . $search . '%');
                }

                else if($searchValue == 'Web' || $searchValue == 'web' ){

                    $search = '1';
                    $query->orWhere('label_name', 'like', '%' . $searchValue . '%')

                    ->orWhere('label_value', 'like', '%' . $searchValue . '%')

                    ->orWhere('label_type', 'like', '%' . $search . '%');
                }
                else
                {
                    $query->orWhere('label_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('label_value', 'like', '%' . $searchValue . '%')
                    ->orWhere('label_type', 'like', '%' . $searchValue . '%');
                }

            });

        }
        // if ($searchValue!="") {
        //     $totalAr = $totalAr->where(function ($query) use ($searchValue) {
        //          $query->orWhere('label_name', 'like', '%' . $searchValue . '%')
        //              ->orWhere('label_value', 'like', '%' . $searchValue . '%')
        //              ->orWhere('label_type', 'like', '%' . $searchValue . '%');

        //     });
        // }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $labelShow =  route('label.show',['id'=>$data->id]);
            $labelEdit =  route('label.edit',['id'=>$data->id]);
            if($data->label_type == 0){
                $type = "Mobile";
            }else{
                $type = "Web";
            }

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$labelShow.'" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="'.$labelEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "labelname" =>   isset($data->label_name) ? $data->label_name : '' ,
              "labelvalue" =>   isset($data->label_value) ? $data->label_value : '' ,
              "label_type" => isset($type) ? $type : '' ,
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
