<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Yajra\Datatables\Datatables;

class FaqController extends Controller
{


    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,19,'read');
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

        $Faqs = Faq::orderby('id', 'desc')->get();

        $Faq = count($Faqs);

        return view("dashboard.faq.list",compact("Faqs","Faq"));


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
         return view("dashboard.faq.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       

        $faq = new Faq();
        $this->validateRequest();
       // $faq->type = $request->type;
        $faq->question_name = $request->question;
        $faq->question_name_fr = $request->question_fr;
        $faq->answer = $request->answer;
        $faq->answer_fr = $request->answer_fr;
        $faq->status = 1;
        $faq->create_by = Auth::user()->id;
        $faq->created_at = date('Y-m-d H:i:s');
        $faq->save(); 

        return redirect()->route('faq')->with('doneMessage', 'Record created successfully.');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Faq = Faq::find($id);

        return view('dashboard.faq.edit',compact('Faq'));
    }

    public function faqUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Faq::wherein('id', $ids)->update(['status' => $status]);
               
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
        $faq = Faq::find($id);
        //$faq->type = $request->type;
        $faq->question_name = $request->question;
        $faq->question_name_fr = $request->question_fr;
        $faq->answer = $request->answer;
        $faq->answer_fr = $request->answer_fr;
        $faq->status = 1;
        $faq->create_by = Auth::user()->id;
        $faq->created_at = date('Y-m-d H:i:s');
        $faq->save(); 
 

        return redirect()->route('faq')->with('doneMessage', 'Record updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::find($id);
        $faq->delete();

        return redirect()->route('faq')->with('doneMessage', 'Record deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Faq = Faq::find($id);
        
        return view('dashboard.faq.show', compact('Faq'));
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                // 'type' => 'required',
                'question' => 'required',
                'question_fr' => 'required',
                'answer' => 'required',
                'answer_fr' => 'required',

            ]);

        }else{

            $validateData =request()->validate([
                // 'type' => 'required',
                'question' => 'required',
                'question_fr' => 'required',
                'answer' => 'required',
                'answer_fr' => 'required',

            ]);
            
        }

        return $validateData;
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
             $sort='question_name';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Faq::where('status','!=','2');
               
        if ($searchValue!="") {
                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('question_name', 'like', '%' . $searchValue . '%');
               });
        }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr=[];
        $i=1;
        foreach ($totalAr as $key => $data) 
        {
            $faqShow =  route('faq.show',['id'=>$data->id]);
            $faqEdit =  route('faq.edit',['id'=>$data->id]);

            $options = '<div style="display: flex;">';
            $options .= '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$faqShow.'" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="'.$faqEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>'; 
            $options .= '</div>'; 

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "Id" =>   isset($i) ? $i : '' ,
              "question" =>   isset($data->question_name) ? $data->question_name : '' ,
              "options" => isset($options) ? $options : '' ,
            );
          $i++;
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
