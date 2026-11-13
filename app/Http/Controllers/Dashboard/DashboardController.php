<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\MainUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\Notification;
use App\Models\AdminNotifications;
use Illuminate\Support\Facades\DB;
use Auth;
use Helper;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */  

    public function __construct()
    {
        $this->middleware('auth');      
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,1,'read');
        if($check_view_permission==false){
            abort(404);
        } 
    }

    public function index(Request $request)
    {        
        
        if ($request->date_filter != "") 
        {
            $filterdate = $request->date_filter;
            $parts = explode(' - ' , $filterdate);
            $start = Carbon::createFromFormat('d-m-Y',$parts[0])->format('Y-m-d 00:00:00');            
            $end = Carbon::createFromFormat('d-m-Y',$parts[1])->format('Y-m-d 23:59:59');
            $total_customer = MainUser::where('status','!=','2')->where('user_type',1)->where('created_at','>=',$start)->where('created_at','<=',$end)->count();
            $total_product = Product::where('status','=','2')->where('created_at','>=',$start)->where('created_at','<=',$end)->count();
            $total_deliver_order = Order::where('order_status','=',3)->where('created_at','>=',$start)->where('created_at','<=',$end)->count();
            $order_inprogress = Order::where('order_status','=',2)->where('created_at','>=',$start)->where('created_at','<=',$end)->count();
            $start = Carbon::createFromFormat('d-m-Y',$parts[0])->format('m-d-Y');
            $end = Carbon::createFromFormat('d-m-Y',$parts[1])->format('m-d-Y');
            return view('dashboard.home', compact('start', 'end','total_customer','total_product','total_deliver_order','order_inprogress','filterdate'));
        }
        else
        {
            $start = '';
            $end = '';
            $start = Carbon::now()->format('m-d-Y');
            $end = Carbon::now()->format('m-d-Y');
            $total_customer = MainUser::where('status','!=','2')->where('user_type',1)->count();
            $total_product = Product::where('status','!=',2)->count();
            $total_deliver_order = Order::where('order_status','=',3)->count();
            $order_inprogress = Order::where('order_status','=',2)->count();
            return view('dashboard.home', compact('start', 'end','total_customer','total_product','total_deliver_order','order_inprogress'));
        }
            // return view('dashboard.home');
        
    }
    // public function readNotification(Request $request)
    // {
    //     // echo "string";exit();

    //     $notificationList = DB::table('admin_notifications')->join('order','order.id','admin_notifications.order_id')->select('admin_notifications.*', 'order.id as orderId','order.order_id as order_id' )->orderby('admin_notifications.id','DESC')->get();
    //      //dd($notificationList);

    //     $html  = view('dashboard.notification')->with(['notificationList' => $notificationList])->render();

    //     return response()->json(['success' => true,'html' => $html]);
    // }
    
    public function readNotification(Request $request)
{
    // Mark all notifications as read
    // DB::table('admin_notifications')->update(['is_read' => 1]);

    // Fetch the updated notification list
    // $notificationList = DB::table('admin_notifications')
    //     ->join('order', 'order.id', '=', 'admin_notifications.order_id')
    //     ->select('admin_notifications.*', 'order.id as orderId', 'order.order_id as order_id')
    //     ->orderby('admin_notifications.id', 'DESC')
    //     ->get();

    $notificationList = DB::table('admin_notifications')
    ->leftJoin('order', function($join) {
        $join->on('order.id', '=', 'admin_notifications.order_id')
             ->where('admin_notifications.notification_type', '=', 1);
    })
    ->leftJoin('inquiry', function($join) {
        $join->on('inquiry.id', '=', 'admin_notifications.inquiry_id')
             ->where('admin_notifications.notification_type', '=', 2);
    })
    ->select('admin_notifications.*', 
             'order.id as orderId', 
             'order.order_id as order_id', 
             'inquiry.id as inquiryId', 
             'admin_notifications.message as order_message',
             'inquiry.message as inquiry_message'
             )
    ->orderby('admin_notifications.id', 'DESC')
    ->get();

    
 

    // echo "<pre>";

    // print_r($notificationList);
    // die;


    // Render the updated notification list
    $html = view('dashboard.notification')->with(['notificationList' => $notificationList])->render();

    // Return the updated notification count and HTML
    $notificationListCount = DB::table('admin_notifications')->where('is_read', 0)->count();

    return response()->json(['success' => true, 'html' => $html, 'notificationListCount' => $notificationListCount]);
}


    
}