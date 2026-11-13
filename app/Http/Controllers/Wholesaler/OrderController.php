<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\OrderTracking;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Notification;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class OrderController extends Controller
{
    //
    public function index()
    {
        // echo "string";exit();
        
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("wholesaler.order.list");
    }

    public function wholesalerorderUpdateAll(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
                // echo "<pre>";print_r($ids);exit();
                Order::wherein('id', $ids)->update(['order_status' => $status]);

                foreach ($ids as $check_status) {

                    $uniqid = uniqid();
                    $order_tracking = new OrderTracking();

                    $order_tracking->uniqid = $uniqid;
                    $order_tracking->order_id = $check_status;
                    $order_tracking->order_status = $status;
                    $order_tracking->status = 1;
                    $order_tracking->save();

                    $orderData = DB::table('order')->where('id',$check_status)->first();

                    $userData = DB::table('main_users')->where('id',$orderData->user_id)->first();
                    // echo "<pre>";print_r($userData);exit();

                    if ($orderData->order_status == 1) {
                        $orderStatus = "Your package will be dispatched";
                    }elseif ($orderData->order_status == 2) {
                        $orderStatus = "Order completed";
                        
                    }elseif ($orderData->order_status == 3) {
                        $orderStatus = "Order cancel";
                        
                    }else{
                        $orderStatus = "Order pending";

                    }

                    $title = $orderStatus;
                    $message = $orderStatus;
                    $remember_token = "f9FMF8ZF5kkno3HGTFCzxn:APA91bHYVjZFR79puFDSZQgx2gjGfoCaKmnZIZRlqaTN4guWZBUod0BkQDjqvCBx9m3xkGFwPWLahInE33dqEg9AEZbdV9jOZfP-7Jwuyp2s-1gIoX2Og47uuqLiieZ8SlOiM2dgN5lN";
                    $device_token = "test";
                    // echo "<pre>";print_r($device_token);exit();
                    $device_type = 1;

                    $notification = new Notification();

                    $notification->sender_id = $userData->id;
                    $notification->receiver_id = $userData->id;
                    $notification->notification_type = 2;
                    $notification->title = @$title;
                    $notification->message = @$message;
                    $notification->is_read = 0;
                    $notification->save();

                        
                     $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
                    $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);
                }

               
                if($status == 0){
                    return response()->json(['success' => true,'msg'=>'Order pending successfully']);
                  }else if($status == 1){
                   return response()->json(['success' => true,'msg'=>'Order dispatched successfully']);
                  }else if($status == 2){
                   return response()->json(['success' => true,'msg'=>'Order completed successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Order cancelled successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function show($id)
    {
        // echo "<pre>";print_r($id);exit();
        $supplier_id = auth()->guard('main_user')->user()->id;
        $orderData = Order::leftjoin('main_users','main_users.id','=','order.user_id')->select('order.*','main_users.first_name','main_users.last_name','main_users.email','main_users.phone')->where('order.supplier_id',$supplier_id)->where('order.id',$id)->first();

        $settings = Setting::find(1);
        // echo "<pre>";print_r($orderData->toArray());
        $orderDetails = DB::table('order_detail')->leftjoin('order','order.id','=','order_detail.order_id')->leftJoin('product','product.id','=','order_detail.product_id')->select('order_detail.*','product.product_name','product.retail_price')->where('order_detail.order_id',$id)->get();
        // echo "<pre>";print_r($orderDetails->toArray());exit();
        return view('wholesaler.order.show', compact('orderData','orderDetails','settings'));
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
        $supplier_id = auth()->guard('main_user')->user()->id;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==1) {
            $sort='order.order_id';
        }elseif ($columnIndex==2) {
             $sort='order.supplier_id';
        }elseif ($columnIndex==3) {
            $sort='order.order_type';
        }elseif ($columnIndex==4) {
            $sort='order.total_amount';
        }elseif ($columnIndex==5) {
            $sort='order.payable_amount';
        }elseif ($columnIndex==6) {
            $sort='order.order_date';
        }else{
            $sort='order.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Order::leftjoin('main_users','main_users.id','=','order.user_id')->select('order.*','main_users.first_name','main_users.last_name')->where('order.supplier_id',$supplier_id);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                 ->orWhere('order.total_amount', 'like', '%' . $searchValue . '%')
                 ->orWhere('order.order_id', 'like', '%' . $searchValue . '%')
                 ->orWhere('order.uniqid', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $orderShow =  route('order.show',['id'=>$data->id]);
            // $categpryEdit =  route('category.edit',['id'=>$data->uniqid]);

            $username = urldecode(@$data->first_name).' '.urldecode(@$data->last_name);
            // $productname = urldecode(@$data->product_name);

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            // }

            if ($data->order_status == 2) {
                $status = '<button type="button" class="btn btn-success pointer_button">
                <span class="badge  badge-success">Completed</span>
              </button>';
            } elseif ($data->order_status ==0) {
                 $status = '<button type="button" class="btn btn-warning pointer_button">
                <span class="badge  badge-danger">Pending</span>
              </button>';
            }elseif ($data->order_status ==1) {
                 $status = '<button type="button" class="btn btn-info pointer_button">
                <span class="badge  badge-danger">Dispatched</span>
               </button>';
            }else {
                $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Cancel</span>
              </button>';
            }

            if ($data->order_type == 1) {
                $order_type = '
                <span class="badge  badge-success">Online</span>
              ';
            } elseif ($data->order_type == 2) {
                 $order_type = '
                <span class="badge  badge-danger">In Store</span>
              ';
            }else {
                $order_type = '
                <span class="badge  badge-danger">In Store</span>
              ';
            }

            $date = \Helper::converttimeTozone($data->order_date);
            $settings = Setting::find(1);

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$orderShow.'" title="View"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-category" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category"  data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->order_id) ? $data->order_id : '' ,
              "username" =>   isset($username) ? $username : '' ,
              "order_type" =>   isset($order_type) ? $order_type : '' ,
              "total_amount" =>    @$settings->currency_symbol.' '.@$data->total_amount,
              "payable_amount" =>    @$settings->currency_symbol.' '.@$data->payable_amount,
              "order_date" =>   @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
