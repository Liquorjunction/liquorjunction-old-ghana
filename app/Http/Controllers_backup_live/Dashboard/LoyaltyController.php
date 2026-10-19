<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\Advertise;
use App\Models\SubCategories;
use App\Models\Loyalty;
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

class LoyaltyController extends Controller
{
     public function index()
    {
        return view("dashboard.loyalty.list");
    }

    public function store(Request $request)
    {
        if (empty($request->loyalty_id)) {
            $uniqid = uniqid();
        $loyalty = new Loyalty();
        
        $loyalty->uniqid = $uniqid;
        $loyalty->minimum_purchase_amount = $request->minimum_purchase_amount;
        $loyalty->loyalty_percentage = $request->loyalty_percentage;
        $loyalty->maximum_points = $request->maximum_points;
        $loyalty->points_per_ghs = $request->points_per_ghs;
        $loyalty->redeem_ghs_value = $request->redeem_ghs_value;
        $loyalty->max_redeem_percentage = $request->max_redeem_percentage;
        $loyalty->status = 1;
        $loyalty->save();

        Alert::success('Success', __('backend.New_Loyalty_created_successfully'));
        return response()->json(['success' => 'true']);
        }else{
            $loyalty_id = $request->loyalty_id;
            
             $category = Loyalty::where('id', $loyalty_id)->update(array(
                'minimum_purchase_amount' => $request->minimum_purchase_amount,
                'loyalty_percentage' => $request->loyalty_percentage,
                'maximum_points' => $request->maximum_points,
                'points_per_ghs' => $request->points_per_ghs,
                'redeem_ghs_value' => $request->redeem_ghs_value,
                'max_redeem_percentage' => $request->max_redeem_percentage,
            ));
             Alert::success('Success', __('backend.Loyalty_has_been_updated_successfully'));
             return response()->json(['success' => 'true']);
        }
        
    }

    public function edit(Request $request)
    {
        if ($request->ajax())
        {   
            $loyalty_id = $request->loyalty_id;
            $loyaltyData = Loyalty::where('id',$loyalty_id)->where('status','!=',2)->first();
            if(!empty($loyaltyData))
            {
                $html =  view('dashboard.loyalty.edit')->with(['loyaltyData' => $loyaltyData])->render();
                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
        }
    }

    public function show(Request $request)
    {
        $loyalty_id = $request->loyalty_id;
       $loyaltyData = Loyalty::where('id',$loyalty_id)->where('status','!=',2)->first();
        
         if(!empty($loyaltyData))
            {
                $html =  view('dashboard.loyalty.show')->with(['loyaltyData' => $loyaltyData])->render();

                return response()->json(['success' => true,'html'=> $html]);
            }
            return response()->json(['success' => false,'msg'=> 'something wrong.']);
    }

    public function loyaltyUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Loyalty::wherein('id', $ids)->update(['status' => $status]);
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Loyalty deactive successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Loyalty active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function status_active(Request $request){
        $loyalty_id = $request->loyalty_id;
         Loyalty::where('id', $loyalty_id)->update(['status' => 0]);
        
         Alert::success('Success', __('backend.Loyalty_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        $loyalty_id = $request->loyalty_id;
         Loyalty::where('id', $loyalty_id)->update(['status' => 1]);
         Alert::success('Success', __('backend.Loyalty_active_sucessfully'));
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
             $sort='minimum_purchase_amount';
        }elseif ($columnIndex==2) {
            $sort='loyalty_percentage';
        }elseif ($columnIndex==3) {
            $sort='maximum_points';
        }elseif ($columnIndex==4) {
            $sort='points_per_ghs';
        }elseif ($columnIndex==5) {
            $sort='redeem_ghs_value';
        }elseif ($columnIndex==6) {
            $sort='max_redeem_percentage';
        }elseif ($columnIndex==7) {
            $sort='created_at';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Loyalty::where('status','!=','2');
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('minimum_purchase_amount', 'like', '%' . $searchValue . '%')
                     ->orWhere('loyalty_percentage', 'like', '%' . $searchValue . '%')
                     ->orWhere('maximum_points', 'like', '%' . $searchValue . '%')
                     ->orWhere('points_per_ghs', 'like', '%' . $searchValue . '%')
                     ->orWhere('redeem_ghs_value', 'like', '%' . $searchValue . '%')
                     ->orWhere('max_redeem_percentage', 'like', '%' . $searchValue . '%')
                     ->orWhere('created_at', 'like', '%' . $searchValue . '%');
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
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->uniqid]);

             if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }


            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-loyalty" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-loyalty" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "minimum_purchase_amount" =>   isset($data->minimum_purchase_amount) ? $data->minimum_purchase_amount.' GH₵' : '' ,
              "loyalty_percentage" =>   isset($data->loyalty_percentage) ? $data->loyalty_percentage : '' ,
              "maximum_points" =>   isset($data->maximum_points) ? $data->maximum_points.' GH₵' : '' ,
              "points_per_ghs" =>   isset($data->points_per_ghs) ? $data->points_per_ghs : '' ,
              "redeem_ghs_value" =>   isset($data->redeem_ghs_value) ? $data->redeem_ghs_value.' GH₵' : '' ,
              "max_redeem_percentage" =>   isset($data->max_redeem_percentage) ? $data->max_redeem_percentage : '' ,
              "created_at" => date('Y-m-d', strtotime($data->created_at)),
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
