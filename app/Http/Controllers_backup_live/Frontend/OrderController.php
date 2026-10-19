<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderInfo;
use App\Models\OrderTracking;
use App\Models\ProductVariants;
use App\Models\Setting;
use App\Models\Rating;
use DateTime;
use Illuminate\Support\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Helper;
use Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LoyaltyPoints;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()
            ->guard('user')
            ->user()
            ->id) ? auth()
            ->guard('user')
            ->user()->id : '';
    }

    public function index(){
        $user_id = $this->user_id;
        $orderInfo = Order::with('order_details','orderInfo')->where([['user_id',$user_id]])->orderBy('id','desc')->get();
        // dd($orderInfo);
        return view("frontend.order.order-list", compact('orderInfo'));
    }

    public function myOrderDetails(Request $request,$id){
        $user_id = $this->user_id;
        $id = base64_decode($id);
        $orderInformation = Order::with('order_details','orderInfo','transcations')->where('id',$id)->where('user_id',$user_id)->first();
        $orderData=Order::where('id',$id)->first();

        return view("frontend.order.order-details",compact('orderInformation','orderData'));
    }

    public function printMyOrder($id)
    {
        $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
        ->join('order_info', 'order_info.order_id', '=', 'order.id')
        ->join('order_status', 'order_status.id', '=', 'order.status')
        ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
        ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
        ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount', 'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo', 'order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee','main_users.first_name','main_users.last_name','main_users.email','main_users.phone','main_users.phone_code')->where('order.id', $id)->first();

    $settings = Setting::find(1);
    
    $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
        ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
        ->where('order_detail.order_id', $id)
        ->get();

    $taxes=DB::table('tax_masters')->get();

    // Bogo Discount Calculation
    $bogoDiscount = 0;
    $totalAmount=0;

    foreach ($orderDetails as $data) {
        if ($data->is_bogo) {
            $display_qty = floor($data->quantity / 2);

            $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
                ? $data->product_total_amount
                : $data->product_original_amount;

            $bogoDiscount += $display_qty * $unit_price;
        }
    }

    foreach ($orderDetails as $data) {
        $display_qty =  $data->quantity;

        $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
            ? $data->product_total_amount
            : $data->product_original_amount;

        $totalAmount += $display_qty * $unit_price;
    }

    // Tax calculation
    $discountAmount = $orderData->discount_amount ?? 0;
    $cartDiscount = $orderData->cart_discount ?? 0;
    $rewardAmount = $orderData->reward_amount ?? 0;

    $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount ;

    // $subTotal = ($orderData->total_amount - $totalDiscount) / 1.219;
    $subTotal = ( $totalAmount - $totalDiscount) * 0.833333;

    //$covidLevy=($taxes[0]->tax_value/100)*($subTotal);
    $nhil=($taxes[1]->tax_value/100)*($subTotal);
    $getFund=($taxes[2]->tax_value/100)*($subTotal);
    $vat=($taxes[3]->tax_value/100)*( $subTotal );

    $pdf = Pdf::loadView('frontend.layouts.orders', compact('orderData','orderDetails','settings','nhil','getFund','vat','subTotal','bogoDiscount','totalAmount'));

    return $pdf->stream('order_' . $id . '.pdf');
    }

    // public function cancelMyorder(Request $request,$id){
    //     $order_id = $id;
    //     $orderInformation = Order::with('order_details','orderInfo','transcations')->where('uniqid',$order_id)->first();
    //   // dd($orderInformation);
    //     if(!empty($orderInformation)){
    //         foreach ($orderInformation->order_details as $key => $order_detail) {
    //             $order_quantity = $order_detail->quantity;
    //             $varinat_id = $order_detail->variant_id;
                
    //             $varinat_info = ProductVariants::where('id',$varinat_id)->first();
    //             if($varinat_info!=""){
    //                 $varinat_info->sold_qty = ($varinat_info->sold_qty -  $order_quantity);
    //                 $varinat_info->available_qty = ($varinat_info->available_qty +  $order_quantity);
    //                 $varinat_info->save();
    //             }

    //         }
    //         $order_status = Order::where('id', $orderInformation->id)->update(['order_status' =>4 ]);

    //         $customer_details =DB::table('main_users')->find($orderInformation->user_id);
    //         $setting = Setting::find(1);
    //         $template_id = 20;
    //         $emaildetail = EmailTemplate::find($template_id);
    //       // $from_email = $setting['mail_username'];
    //       $fromEmail = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com'); 
    //         // echo "<pre>";print_r($setting->toArray());exit();
    //         // $from_email = 'ritesh.m@vrinsoft.com';
    //         $logo = asset('assets/dashboard/images/Logo/logo.png');
    //         $sendemail = @$customer_details->email;
    //         // $sendemail = 'tester.liquor@yopmail.com';
    //         $order = Order::leftjoin('order_status','order_status.id','=','order.id')
    //                     ->select('order.*','order_status.name as order_status',)
    //                     ->where('order.id',$order_id)->first();
    //         $message = "Online Order Cancelled";
    //         $data = array(
    //             'user_name' => @$customer_details->first_name, 
    //             'sendname' => @$customer_details->first_name,
    //             'sendemail'=>@$sendemail,
    //             'id'=>$template_id,
    //             'order' => $order,
    //             'order_status' => $message ,
    //             'from_email' =>config('mail.from.address'),
    //         );
    //         try {
    //             Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emaildetail) {
    //                 $message->to($data['sendemail'], 'Liquor')->subject($emaildetail->subject);
    //                 $message->from($data['from_email'], $emaildetail->title);
    //             });
              
    //         } catch (\Throwable $th) {
    //             //throw $th;
    //         }
           
    //         Alert(Helper::language('order_status'),'Your order has been cancelled');
    //         return back();
    //     }else{
    //         Alert::error('Error', __(Helper::language('something_went_wrong')));
    //         return back('/');
    //     }
    // }
  public function cancelMyorder(Request $request, $id)
{
    $order_id = $id;
    $user_id = $this->user_id;


    // Retrieve the order using the correct identifier
    $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')
        ->where('uniqid', $order_id)
        ->first();
    // Check if the order was found
    if ($orderInformation) {

        if ($orderInformation->order_status != 1) {
            Alert::error('Error', 'This order cannot be cancelled.');
            return back();
        }

        // Process order details
        foreach ($orderInformation->order_details as $order_detail) {
            $order_quantity = $order_detail->quantity;
            $variant_id = $order_detail->variant_id;

            $variant_info = ProductVariants::where('id', $variant_id)->first();
            if ($variant_info) {
                $variant_info->sold_qty -= $order_quantity;
                $variant_info->available_qty += $order_quantity;
                $variant_info->save();
            }
        }

        // Update the order status to cancelled (4)
        $userDetails = DB::table('main_users')->where('id', $user_id)->first();

        if($userDetails->user_type==1)
        {
            $currentUser='Customer';
        }
        else if($userDetails->user_type==2)
        {
            $currentUser='Wholesaler';
        }
        else
        {
            $currentUser='SubAdmin';
        }

        // $order_status = Order::where('id', $orderInformation->id)->update(['order_status' => 4]);
        $order_status = Order::where('id', $orderInformation->id)->update([
            'order_status' => 4,
            'cancelled_by' => $currentUser,
            'cancelled_user' => $user_id
        ]);

        // Retrieve customer details
        $customer_details = DB::table('main_users')->find($orderInformation->user_id);
        if (!$customer_details) {
            Alert::error('Error', 'Customer details not found');
            return back('/');
        }

        // Prepare email data
        $setting = Setting::find(1);
        $template_id = 20;
        $emaildetail = EmailTemplate::find($template_id);
        $fromEmail = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $sendemail = @$customer_details->email;

        // Fetch the order for email
        $order = Order::leftjoin('order_status', 'order_status.id', '=', 'order.id')
            ->select('order.*', 'order_status.name as order_status')
            ->where('order.id', $orderInformation->id) // Use the ID of the order found
            ->first();

        // Check if the order was found for email
        if (!$order) {
            Alert::error('Error', 'Order not found for email notification');
            return back('/');
        }

        // Prepare the email data
        $message = "Online Order Cancelled";
        $data = array(
            'user_name' => @$customer_details->first_name,
            'sendname' => @$customer_details->first_name,
            'sendemail' => @$sendemail,
            'id' => $template_id,
            'order' => $order,
            'order_status' => $message,
            'from_email' => $fromEmail,
        );

        // Send the cancellation email
        $this->order_cancel_email($data['user_name'], $data['sendname'], $data['sendemail'], $data['order'], $data['order_status']);
        Alert(Helper::language('order_status'), 'Your order has been cancelled');
        return back();
    } else {
        Alert::error('Error', __(Helper::language('something_went_wrong')));
        return back('/');
    }
}

    
    public function order_cancel_email($user_name,$sendname,$sendemail,$order, $order_status)
    {
        try
        {
            $setting = Setting::find(1);
            $from_email = $setting['mail_no_replay'];
            $emailtemp = Emailtemplate::find('20');
            // echo "<pre>";print_r($setting->toArray());exit();
            // $from_email = $setting['from_email'];
            $data = array('user_name'=>$user_name,'sendname'=>$sendname,'sendemail' => $sendemail, 'order' => $order, 'order_status' => $order_status, 'id' => '20', 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);
    
            Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data) {
    
                $message->to($data['sendemail'], $data['title'])->subject($data['subject']);
                //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');
    
                $message->from($data['from_email'], $data['support_name']);
            });
        }
        catch (\Exception $e) {
            \Log::error('Order cancel email failed: ' . $e->getMessage());

        }
       
    }

}
