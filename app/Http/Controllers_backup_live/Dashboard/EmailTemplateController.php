<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Yajra\Datatables\Datatables;

class EmailTemplateController extends Controller
{

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,20,'read');
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

        $emails = EmailTemplate::get();

        $emailtemplate = count($emails);

        

        return view("dashboard.emailtemplate.list",
            compact("emails","emailtemplate"));

    }

    public function create()
    {
        return view("dashboard.emailtemplate.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       
        //dd($request);
        $this->validateRequest();
        $emailtemplate = new EmailTemplate();
        $emailtemplate->language_id = 2;
        $emailtemplate->title = $request->title;
        $emailtemplate->subject = $request->subject;
        $emailtemplate->content = $request->content;
        $emailtemplate->status = 1;
        $emailtemplate->created_at = date('Y-m-d H:i:s');
        $emailtemplate->save(); 

        return redirect()->route('emailtemplate')
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
        $emailtemplate = EmailTemplate::find($id);

        return view('dashboard.emailtemplate.edit', compact('emailtemplate'));
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
        $this->validateRequest();
        $emailtemplate = EmailTemplate::find($id);
        $emailtemplate->language_id = 2;
        $emailtemplate->title = $request->title;
        $emailtemplate->subject = $request->subject;
        $emailtemplate->content = $request->content;
        $emailtemplate->status = 1;
        $emailtemplate->updated_at = date('Y-m-d H:i:s');
        $emailtemplate->save(); 

        return redirect()->route('emailtemplate')->with('doneMessage', 'Record updated successfully.');
    }


    public function emailt_UpdateAll(Request $request)
    {
       
        
        //
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                EmailTemplate::wherein('id', $ids)->update(['status' => $status]);
               
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

    

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                'title' => 'required',
                'subject' => 'required',
                'content' => 'required',
            ]);

        }else{

            $validateData =request()->validate([
                'title' => 'required',
                'subject' => 'required',
                'content' => 'required',
            ]);
            
        }

        return $validateData;
    }

    public function show($id)
    {
        $emailtemplate = EmailTemplate::find($id);

        return view('dashboard.emailtemplate.show', compact('emailtemplate'));
    }
    
    public function anyDataold(Request $request) 
    {

        $data = EmailTemplate::where('status','!=',2)->orderby('id', 'desc')->get();
       
        return Datatables::of($data)
            ->addColumn('title', function ($data) {
                $title =    isset($data->title) ? $data->title: ''  ;
                return $title;
            })
          
            ->addColumn('subject', function ($data) {
                $subject =   isset($data->subject) ? $data->subject : '' ;
                return $subject;
            })
           
            ->addColumn('options', function ($data) {

             
                $emailtemplateEdit =  route('emailtemplate.edit',['id'=>$data->id]);
                $emailtemplateShow =  route('emailtemplate.show',['id'=>$data->id]);

                $x = '<a class="btn btn-sm show-eyes list" href="'.$emailtemplateShow.'" title="Show"> </a> ';
                $x .= '<a class="btn btn-sm success paddingset" href="'.$emailtemplateEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                                  
                                   
               
                return $x;
                
            })
            
            ->rawColumns(['checkbox', 'title', 'subject','options'])
            ->make(true);
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
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='emailtemplates.id';
        }elseif ($columnIndex==1) {
            $sort='emailtemplates.title';
        }elseif ($columnIndex==2) {
            $sort='emailtemplates.subject';
        }else{
            $sort='emailtemplates.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = EmailTemplate::where('status','!=',2);
       
          if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('title', 'like', '%' . $searchValue . '%')
                      ->orWhere('subject', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

       /* print_r($totalAr);
        exit;*/
        $data_arr=[];

        foreach ($totalAr as $key => $data) 
        {   
            $emailtemplateEdit =  route('emailtemplate.edit',['id'=>$data->id]);
            $emailtemplateShow =  route('emailtemplate.show',['id'=>$data->id]);

            $options = '<a class="btn btn-sm show-eyes list" href="'.$emailtemplateShow.'" title="Show"> </a> ';
            $options .= '<a class="btn btn-sm success paddingset" href="'.$emailtemplateEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id: '',
              "title" =>   isset($data->title) ? $data->title: '',
              "subject" =>  isset($data->subject) ? $data->subject : '',
              "options" => $options,
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
