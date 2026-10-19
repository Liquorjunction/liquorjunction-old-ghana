<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use App\Models\MainUser;
use Yajra\Datatables\Datatables;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {

        $inquiry = Inquiry::orderby('id', 'desc')->get();

        $inquiry_count = count($inquiry);

        return view("dashboard.inquiry.list",
            compact("inquiry","inquiry_count"));
    }

    public function subscribe()
    {

        $subscribe = \DB::table('subscribe_email')->orderby('id', 'desc')->get();

        $subscribe_count = count($subscribe);

        return view("dashboard.inquiry.subscribe-list",
            compact("subscribe","subscribe_count"));
    }

    public function create()
    {
          // General for all pages
         return view("dashboard.inquiry.create");
    }

    public function store(Request $request)
    {       

        $inquiry = new Inquiry();
        $this->validateRequest();
       // $faq->type = $request->type;
        $inquiry->name = $request->name;
        $inquiry->email = $request->email;
        $inquiry->phone = $request->phone;
        $inquiry->message = $request->message;
        $inquiry->status = 1;
        $inquiry->created_at = date('Y-m-d H:i:s');
        $inquiry->save(); 

        return redirect()->route('inquiry')
            ->with('doneMessage', 'Inquiry created successfully.');
    }

    public function edit($id)
    {
        $inquiry = Inquiry::find($id);

        return view('dashboard.inquiry.edit', compact('inquiry'));
    }

    public function inquiryUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Inquiry::wherein('id', $ids)->update(['status' => $status]);
               
                return response()->json(['success' => true,'msg'=>'Record(s) delete successfully.']);
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function subscribeUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                \DB::table('subscribe_email')->wherein('id', $ids)->update(['status' => $status]);
               
                return response()->json(['success' => true,'msg'=>'Subscribe email delete successfully.']);
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
        $inquiry = Inquiry::find($id);
        //$faq->type = $request->type;
        $inquiry->name = $request->name;
        $inquiry->email = $request->email;
        $inquiry->phone = $request->phone;
        $inquiry->message = $request->message;
        $inquiry->status = 1;
        $inquiry->updated_at = date('Y-m-d H:i:s');
        $inquiry->save(); 

        return redirect()->route('inquiry')->with('doneMessage', 'Inquiry updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inquiry = Inquiry::find($id);
        $inquiry->status = 2;
        
        return redirect()->route('inquiry')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function subscribeDestroy($id)
    {
        $subscribe = \DB::table('subscribe_email')->find($id);
        $subscribe->status = 2;
        
        return redirect()->route('subscribe')
            ->with('doneMessage', 'Record deleted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        DB::table('admin_notifications')->where('inquiry_id', $id)->update(['is_read' => 1]);

        $inquiry = Inquiry::with('inquiryReason')->where('id',$id)->first();
        return view('dashboard.inquiry.show', compact('inquiry'));
    }

    public function validateRequest($id="")
    {

        if($id !="")
        {
            $validateData =request()->validate([
                // 'type' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'message' => 'required',
            ]);

        }else{

            $validateData =request()->validate([
                // 'type' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'message' => 'required',
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
             $sort='name';
        }elseif ($columnIndex==2) {
            $sort='email';
        }elseif ($columnIndex==3) {
            $sort='phone';
        }elseif ($columnIndex==4) {
            $sort='message';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Inquiry::where('status','!=','2');
               
        if ($searchValue!="") {
                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%')
                    ->orWhere('phone', 'like', '%' . $searchValue . '%')
                    ->orWhere('message', 'like', '%' . $searchValue . '%');
               });
        }

        $totalRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr=[];
        $i=1;
        $phone_no = '';
        foreach ($totalAr as $key => $data) 
        {
            $inquiryShow =  route('inquiry.show',['id'=>$data->id]);
            $inquiryEdit =  route('inquiry.edit',['id'=>$data->id]);
            $phone_code ="";
            if($data->phone_code){
                $phone_code =  Helper::country($data->phone_code);
                $phone_code = $phone_code->phonecode;
            }

            // $phone = isset($data->phone) ? $data->phone : '';

            // $country_code = '1';

            // $phone_no = '+'.$country_code.' '.$phone;

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$inquiryShow.'" title="Show"> </a>';

         //   $options .= '<a class="btn btn-sm success paddingset" href="'.$inquiryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'"> <small><i class="material-icons">&#xe872;</i> </small> </button>';  

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "Id" =>   isset($i) ? $i : '' ,
              "name" =>   isset($data->name) ? $data->name : '' ,
              "email" =>   isset($data->email) ? $data->email : '' ,
              "phone" =>   isset($data->phone) ? '+'.$phone_code.' '.$data->phone : ' ' ,
              "message" =>   isset($data->message) ? $data->message : '' ,
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

    public function subscribeAnyData(Request $request)
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
            $sort='email';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = \DB::table('subscribe_email')->where('status','!=','2');
               
        if ($searchValue!="") {
                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('email', 'like', '%' . $searchValue . '%');
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

         //   $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$inquiryShow.'" title="Show"> </a>';

         //   $options .= '<a class="btn btn-sm success paddingset" href="'.$inquiryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options =  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'"> <small><i class="material-icons">&#xe872;</i> </small> </button>';  

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "Id" =>   isset($i) ? $i : '' ,
              "email" =>   isset($data->email) ? $data->email : '' ,
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
