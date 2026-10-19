<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\Advertise;
use App\Models\SubCategories;
use App\Models\Quote;
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

class QuoteController extends Controller
{
    //
    // private $uploadPath = "/uploads/quote";

    public function index()
    {
        // echo "string";exit();
        return view("dashboard.Quote.list");
    }

    public function show($id)
    {
        $quoteData = DB::table('quote')->leftjoin('time_frame','time_frame.id','=','quote.time_frame_id')->leftJoin('material_category','material_category.id','=','quote.material_id')->leftjoin('categories','categories.id','=','quote.category_id')->leftjoin('main_users','main_users.id','=','quote.user_id')->leftjoin('main_users as user_assign','user_assign.id','=','quote.assign_user_id')->select('quote.*','time_frame.name as time_frame_name','material_category.name as material_category_name','categories.title as category_name','main_users.first_name as create_user_first_name','main_users.last_name as create__user_last_name','user_assign.first_name as create_user_assign_first_name','user_assign.last_name as create__user_assign_last_name')->where('quote.status','!=','2')->where('quote.id',$id)->first();
        // echo "<pre>";print_r($quoteData);exit();
        return view('dashboard.Quote.show', compact('quoteData'));
    }

    public function edit($id)
    {
        $quoteData = DB::table('quote')->leftjoin('time_frame','time_frame.id','=','quote.time_frame_id')->leftJoin('material_category','material_category.id','=','quote.material_id')->leftjoin('categories','categories.id','=','quote.category_id')->leftjoin('main_users','main_users.id','=','quote.user_id')->leftjoin('main_users as user_assign','user_assign.id','=','quote.assign_user_id')->select('quote.*','time_frame.name as time_frame_name','material_category.name as material_category_name','categories.title as category_name','main_users.first_name as create_user_first_name','main_users.last_name as create__user_last_name','user_assign.first_name as create_user_assign_first_name','user_assign.last_name as create__user_assign_last_name')->where('quote.status','!=','2')->where('quote.id',$id)->first();

        $assignData = DB::table('main_users')->where('is_technician',1)->select('main_users.post_code','main_users.first_name','main_users.last_name','main_users.id')->orderby('first_name','ASC')->get();
        // echo "<pre>";print_r($assignData->toArray());exit();

        return view('dashboard.Quote.edit', compact('quoteData','assignData'));
    }

    public function update(Request $request,$id)
    {   
        // echo "<pre>";print_r($request->toArray());exit();
        $quote_id = $id;

        $quote = Quote::where('id', $quote_id)->update(array(
                'assign_user_id' => $request->assign_user_id,
                'quote_status' => 1,
            ));
        $deleteQuote = DB::table('quote_send')->where('quote_id',$quote_id)->delete();
        return redirect()->route('quote')->with('doneMessage', 'Quote Assign updated successfully.');
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
             $sort='main_users.first_name';
        }elseif ($columnIndex==2) {
             $sort='categories.title';
        }elseif ($columnIndex==3) {
            $sort='time_frame.name';
        }elseif ($columnIndex==4) {
            $sort='material_category.name';
        }elseif ($columnIndex==5) {
            $sort='quote.post_code';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        // $totalAr = Advertise::where('status','!=','2');
        $totalAr = DB::table('quote')->leftjoin('time_frame','time_frame.id','=','quote.time_frame_id')->leftJoin('material_category','material_category.id','=','quote.material_id')->leftjoin('categories','categories.id','=','quote.category_id')->leftjoin('main_users','main_users.id','=','quote.user_id')->select('quote.*','time_frame.name as time_frame_name','material_category.name as material_category_name','categories.title as category_name','main_users.first_name','main_users.last_name')->where('quote.status','!=','2');
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                    ->orWhere('material_category.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('time_frame.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('categories.title', 'like', '%' . $searchValue . '%')
                     ->orWhere('quote.post_code', 'like', '%' . $searchValue . '%');
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
           
            $create_user_name = @$data->first_name .' '. @$data->last_name;
            // $create_assign_name = @$data->create_user_assign_first_name .' '. @$data->create__user_assign_last_name;

             if ($data->quote_status == 1) {
                $quote_status = '<i class="fa fa-check text-success inline"><spna class="hide">Assign</span></i>';
            } else {
                $quote_status = '<i class="fa fa-times text-danger inline"><span class="hide">Not Assign</span></i>';
            }

           $categoryShow =  route('quote.show',['id'=>$data->id]);
            $categpryEdit =  route('quote.edit',['id'=>$data->id]);

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$categoryShow.'" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-advertise" data-id="'.$data->id.'" title="Show"> </a>';
            if ($data->quote_status != 1) {
                
            $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            }
            // $options .= '<a class="btn btn-sm success paddingset edit-advertise" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "create_user_name" =>   isset($create_user_name) ? $create_user_name : '' ,
              // "create_assign_name" =>   isset($data->create_assign_name) ? $data->create_assign_name : '' ,
              "category_name" =>   isset($data->category_name) ? $data->category_name : '' ,
              "time_frame_name" =>   isset($data->time_frame_name) ? $data->time_frame_name : '' ,
              "material_category_name" =>   isset($data->material_category_name) ? $data->material_category_name : '' ,
              "post_code" =>   isset($data->post_code) ? $data->post_code : '' ,
              "status" =>   isset($quote_status) ? $quote_status : '' ,
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
