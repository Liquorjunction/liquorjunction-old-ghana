<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\EmailTemplate;
use App\Models\SubCategories;
use App\Models\Setting;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Promocode;
use App\Models\ProductVariants;
use App\Models\User;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Carbon\Carbon;
use Mail;
use Yajra\Datatables\Datatables;
use GuzzleHttp\Client;
use App\Models\Notification;
use App\Models\AdminNotifications;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LoyaltyPoints;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 9, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index(Request $request)
    {
        // $order = Order::where('id',$request->id)->get();
        //  dd($order);
        // $productIds = $order->orderDetails->pluck('product_id');
        // dd($productIds);
        return view("dashboard.order.list");
    }

    public function orderstatusUpdateAll(Request $request)
    {
        //    dd($request->all());
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                // dd($ids);
                $status = $request->status;
                //    dd($status);
                // $order_status=DB::table('order_tracking')->where('id', $ids)->update(['order_status' => $status]);
                $order_status = Order::wherein('id', $ids)->update(['order_status' => $status]);
                //    dd($order_status);   
                if ($status == 6) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) out for delivery successfully']);
                } else if ($status == 5) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) ready to pick up successfully']);
                } else if ($status == 3) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delivered successfully']);
                } else if ($status == 4) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) cancelled successfully']);
                } elseif ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Order status has been changed to accepted successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Order status has been changed to pending successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);

    }

    public function show($id)
    {

        // $admin_notifycation = AdminNotifications :: where('order_id',$id)->first();

         DB::table('admin_notifications')-> where('order_id',$id)->update(['is_read' => 1]);

        $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
            ->join('order_info', 'order_info.order_id', '=', 'order.id')
            ->join('order_status', 'order_status.id', '=', 'order.status')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
            ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount', 'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit','order_detail.is_bogo', 'order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee')->where('order.id', $id)->first();

            // Cancellation Detail
            $userName='';
            if($orderData->cancelled_by=='Admin' || $orderData->cancelled_by=='Country Admin'  || $orderData->cancelled_by=='Country Sub Admin' )
            {
                $userDetails = DB::table('users')->where('id', $orderData->cancelled_user)->first();
                $userName=$userDetails->name;
            }
            else if($orderData->cancelled_by=='Customer' || $orderData->cancelled_by=='Wholesaler' || $orderData->cancelled_by=='SubAdmin')
            {
                $userDetails = DB::table('main_users')->where('id', $orderData->cancelled_user)->first();
                $userName=$userDetails->first_name.' '.$userDetails->last_name;
            }


        $settings = Setting::find(1);

        $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
            ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
            ->where('order_detail.order_id', $id)
            ->get();

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
        return view('dashboard.order.show', compact('orderData', 'orderDetails', 'settings','userName','totalAmount','bogoDiscount'));
    }


    public function print($id)
    {
        $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
            ->join('order_info', 'order_info.order_id', '=', 'order.id')
            ->join('order_status', 'order_status.id', '=', 'order.status')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
            ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount' ,'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo','order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee','main_users.first_name','main_users.last_name','main_users.email','main_users.phone','main_users.phone_code')->where('order.id', $id)->first();

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

        $pdf = Pdf::loadView('dashboard.layouts.orders', compact('orderData','orderDetails','settings','nhil','getFund','vat','subTotal','bogoDiscount','totalAmount'));

        return $pdf->stream('order_' . $id . '.pdf');
    }

    public function updateStockOnCancelOrder($order_id)
    {
        $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('id', $order_id)->first();

        if (!empty($orderInformation)) {
            foreach ($orderInformation->order_details as $key => $order_detail) {
                $order_quantity = $order_detail->quantity;
                $varinat_id = $order_detail->variant_id;
                $varinat_info = ProductVariants::where('id', $varinat_id)->first();
                if ($varinat_info != "") {
                    $varinat_info->sold_qty = ($varinat_info->sold_qty - $order_quantity);
                    $varinat_info->available_qty = ($varinat_info->available_qty + $order_quantity);
                    $varinat_info->save();
                }
            }
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $order_id = $request->input('order_id');
        $status = $request->input('order_status');
        $note = $request->input('note');
 
        // Send Invoice to Client
        $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
        ->join('order_info', 'order_info.order_id', '=', 'order.id')
        ->join('order_status', 'order_status.id', '=', 'order.status')
        ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
        ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
        ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount' ,'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo','order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee','main_users.first_name','main_users.last_name','main_users.email','main_users.phone','main_users.phone_code')->where('order.id', $order_id)->first();

        $settings = Setting::find(1);

        $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
        ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
        ->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
        ->where('order_detail.order_id', $order_id)
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

        $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount;

        $subTotal = ($totalAmount - $totalDiscount) * 0.833333;

        //$covidLevy=($taxes[0]->tax_value/100)*($subTotal);
        $nhil=($taxes[1]->tax_value/100)*($subTotal);
        $getFund=($taxes[2]->tax_value/100)*($subTotal);
        $vat=($taxes[3]->tax_value/100)*( $subTotal );

        // Create PDF from view (only for status 3 - Delivered)
        $pdfFilePath = null;
        if ($status == 3) {
            $pdf = Pdf::loadView('dashboard.layouts.orders', compact('orderData', 'orderDetails', 'settings','nhil','getFund','vat','subTotal','bogoDiscount','totalAmount'));

            $directory = storage_path('app/public/uploads/invoices');

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            // Store the PDF in a variable
            $pdfContent = $pdf->output();

            $pdfFilePath = storage_path('app/public/uploads/invoices/order_' . $order_id . '.pdf');

            file_put_contents($pdfFilePath, $pdfContent);


            // Update the loyalty points status
            $rewardCreditPoints = LoyaltyPoints::where('user_id', $orderData->user_id)->where('id',$orderData->loyalty_credit_point_id)->where('type', 'credit')->first();

            if ($rewardCreditPoints) {
                $rewardCreditPoints->status = 1;
                $rewardCreditPoints->save();
            }

            $rewardDebitPoints = LoyaltyPoints::where('user_id', $orderData->user_id)->where('id',$orderData->loyalty_debit_point_id)->where('type', 'debit')->first();

            if ($rewardDebitPoints) {
                $rewardDebitPoints->status = 1;
                $rewardDebitPoints->save();
            }
    
        }

        $order = Order::leftjoin('order_status', 'order_status.id', '=', 'order.id')
            ->select('order.*', 'order_status.name as order_status', )
            ->where('order.id', $order_id)->first();

        if (!$order) {
            return response()->json(['success' => false, 'msg' => 'something wrong.']);
        }

        // save the cancellation user Details
        $authData = Auth::user();

        if($authData->user_type==1)
        {
            $currentUser='Admin';
        }
        else if($authData->user_type==2)
        {
            $currentUser='Country Admin';
        }
        else
        {
            $currentUser='Country Sub Admin';
        }

        if ($status == 4) {
            $order->cancelled_by= $currentUser;
            $order->cancelled_user= $authData->id;
            $this->updateStockOnCancelOrder($order_id);
        }

        $order->order_status = $status;
        if ($status == 2) {
            $order->note = $note;
        }
        $order_number = $order->order_id;

        if ($status == 3 || $status == 4 || $status == 5 || $status == 6) {
            // Send Notification code
            $user_id = $order->user_id;
            $userData = DB::table('main_users')->where('id', $user_id)->first();

            if ($status == 3) {
                $title = "Delivered";
                $message = "Online Order delivered";
                $template_id = 16; // Use the appropriate template ID for order delivered
            } else if ($status == 5) {
                $title = "Ready to Pick Up";
                $message = "Online Order ready to pick up";
                $template_id = 21; // Use the appropriate template ID for order delivered
            } else if ($status == 6) {
                $title = "Out for Delivery";
                $message = "Online Order out for delivery";
                $template_id = 22; // Use the appropriate template ID for order delivered
            } else {
                $title = "Cancelled";
                $message = "Online Order cancelled";
                $template_id = 20; // Use the appropriate template ID for order cancelled
            }
            // $remember_token = "fYPZqDsO90pgmZAzTKD0ow:APA91bEF6Pv3waTLBb8lRSUCrjz_M3Vxf14zF3IDHBckRoI79Ojw66aRbuDNhuHWT21qsPYwhOvXxGhILv-nJ0ZCNWzEEuo2tWQ1pDULgeBkPPoAbPaV6ulPJ0W1H8zhA2IcPn8zHl8P";

            $device_type = 1;

            $notification = new Notification();
            $notification->order_id = @$order->id;
            $notification->sender_id = @$user_id;
            $notification->receiver_id = @$user_id;
            $notification->notification_type = 1;
            $notification->title = @$title;
            $notification->message = @$message;
            $notification->is_read = 0;
            $notification->save();

            if ($status == 3 && $userData->device_token != "") {

                $device_token = $userData->device_token;
                $response = \Helper::sendNotification($device_token, $title, $message, @$order->id, @$order_number);

            }
        }
        if ($order->save()) {
            if ($status == 3 || $status == 4 || $status == 5 || $status == 6) {
                $customer_details = DB::table('main_users')->find($order->user_id);
                $setting = Setting::find(1);
                if ($status == 3) {
                    $template_id = 16;
                } elseif ($status == 4) {
                    $template_id = 20; // Assuming you want to use the same template as for other unspecified statuses (20 or 22 can be assigned here depending on the default you need).
                } elseif ($status == 5) {
                    $template_id = 21; // I assume there was an intended difference between status 4 and 5 that was not specified earlier.
                } else {
                    $template_id = 22; // Default case if needed
                }

                $emaildetail = EmailTemplate::find($template_id);
                $fromEmail = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com'); 
                $logo = asset('assets/dashboard/images/Logo/logo.png');
                $sendemail = @$customer_details->email;
                $data = array(
                    'user_name' => @$customer_details->first_name,
                    'sendname' => @$customer_details->first_name,
                    'sendemail' => @$sendemail,
                    'id' => $template_id,
                    'order' => $order,
                    'order_status' => $message,
                    'from_email' =>config('mail.from.address'),
                );

                try {

                    Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emaildetail,$pdfFilePath,$status) {
                        $message->to($data['sendemail'], 'Liquor')->subject($emaildetail->subject);
                        $message->from($data['from_email'], $emaildetail->title);

                        // if (file_exists($pdfFilePath)) {
                        //     $message->attach($pdfFilePath);
                        //     // unlink($pdfFilePath);
                        // } else {
                        //     // Log or handle the error if the file doesn't exist
                        //     \Log::error("PDF file not found: " . $pdfFilePath);
                        // }

                        if ($status == 3 && file_exists($pdfFilePath)) {
                            $message->attach($pdfFilePath);
                        }   

                    });
                } catch (\Throwable $th) {
                    //throw $th;
                    \Log::error("Email failed for Order ID: $order_id | Error: " . $th->getMessage());
                }
            }
            return response()->json(['success' => true, 'msg' => 'Order Status Changed.']);

        } else {
            return response()->json(['success' => false, 'msg' => 'something went wrong.']);
        }

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
        $order_type_status = $request->get('order_type_status');
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        // $supplier_id = auth()->guard('main_user')->user()->id;
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 1) {
            $sort = 'order.order_id';
        } elseif ($columnIndex == 2) {
            $sort = 'order_info.customer_name';
        } elseif ($columnIndex == 3) {
            $sort = 'order_info.customer_mobile';
        } elseif ($columnIndex == 4) {
            $sort = 'order_info.country_id';
        } elseif ($columnIndex == 5) {
            $sort = 'order.order_type';
        } elseif ($columnIndex == 6) {
            $sort = 'order.payable_amount';
        } elseif ($columnIndex == 7) {
            $sort = 'order_info.promocode_name';
        }elseif ($columnIndex == 8) {
            $sort = 'order.order_date';
        } elseif ($columnIndex == 9) {
            $sort = 'order.order_status';
        } elseif ($columnIndex == 10) {
            $sort = 'transactions.payment_type';
        } elseif ($columnIndex == 11) {
            $sort = 'transactions.payment_status';
        } else {
            $sort = 'order.id';
        }


        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }


        $totalAr = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
            ->join('order_info', 'order_info.order_id', '=', 'order.id')
            ->join('order_status', 'order_status.id', '=', 'order.order_status')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
            ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile','order_info.promocode_name' ,'countries.name as country_name', 'transactions.payment_type', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status')
            // ->where('order.status', 1)
            ->whereIn('order.status', [1, 2]) 
            ->groupby('order.order_id');

        if ($searchValue != "") {
            $typeArray = [
                [
                    'key' => 1,
                    'name' => 'Online',
                ],
                [
                    'key' => 2,
                    'name' => 'In Store',
                ],
            ];

            $payment_type = [
                [
                    'key' => 1,
                    'name' => 'Online Payment',
                ],
                [
                    'key' => 2,
                    'name' => 'Online Payment',
                ],
                [
                    'key' => 3,
                    'name' => 'Cash On Delivery',
                ],

            ];
            $payment_status = [
                [
                    'key' => 1,
                    'name' => 'Pending',
                ],
                [
                    'key' => 2,
                    'name' => 'Success',
                ],
                [
                    'key' => 3,
                    'name' => 'Failed',
                ],
            ];
            $searchResults = [];
            $searchResults1 = [];
            $searchResults2 = [];

            $pattern = '/' . preg_quote($searchValue, '/') . '/i';
            $pattern1 = '/' . preg_quote($searchValue, '/') . '/i';
            $pattern2 = '/' . preg_quote($searchValue, '/') . '/i';



            foreach ($typeArray as $item) {
                if (preg_grep($pattern, $item)) {
                    $searchResults[] = $item['key'];
                }
            }

            foreach ($typeArray as $item2) {
                if (preg_grep($pattern1, $item2)) {
                    $searchResults1[] = $item2['key'];
                }
            }
            foreach ($payment_status as $item3) {
                if (preg_grep($pattern2, $item3)) {
                    $searchResults2[] = $item3['key'];
                }
            }

            $totalAr = $totalAr->where(function ($query) use ($searchValue, $searchResults, $searchResults1, $searchResults2) {

                return $query->orWhere('order.total_amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('order.payable_amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('main_users.first_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('order.order_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.customer_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.promocode_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.customer_mobile', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_status.name', 'like', '%' . $searchValue . '%')
                    // ->orWhere('transactions.payment_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('transactions.payment_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('countries.name', 'like', '%' . $searchValue . '%')

                    // ->orWhere('order.order_type', 'like', '%' . $searchValue . '%')
                    ->when((count($searchResults) > 0), function ($type_q) use ($searchResults) {
                        return $type_q->orWhereIn('order.order_type', $searchResults);
                    })
                    ->when((count($searchResults1) > 0), function ($type_q) use ($searchResults1) {
                        return $type_q->orWhereIn('transactions.payment_type', $searchResults1);
                    })
                    ->when((count($searchResults2) > 0), function ($type_q) use ($searchResults2) {
                        return $type_q->orWhereIn('transactions.payment_status', $searchResults2);
                    });

            });
        }


        if (!empty($order_type_status)) {
            $totalAr->where('order.order_type', $order_type_status);
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr = [];
        foreach ($totalAr as $key => $data) {

            $orderShow = route('adminorder.show', ['id' => $data->id]);

            $username = urldecode(@$data->first_name) . ' ' . urldecode(@$data->last_name);
            $supplername = urldecode(@$data->suppler_first_name) . ' ' . urldecode(@$data->suppler_last_name);

            if ($data->order_status == 4) {
                $status = '<button type="button" class="btn btn-default pointer_button" style="width: 130px !important;">
                <span class="badge  badge-default">Cancel</span>
              </button>';
            } elseif ($data->order_status == 5) {
                $status = '<button type="button" class="btn btn-info pointer_button" style="width: 130px !important;">
               <span class="badge  badge-danger">Ready to Pick Up</span>
             </button>';
            } elseif ($data->order_status == 6) {
                $status = '<button type="button" class="btn btn-warning pointer_button" style="width: 130px !important;">
               <span class="badge  badge-warning">Out for Delivery</span>
             </button>';
            } elseif ($data->order_status == 3) {
                $status = '<button type="button" class="btn btn-success pointer_button" style="width: 130px !important;">
                <span class="badge  badge-success">Delivered</span>
              </button>';
            } elseif ($data->order_status == 2) {
                $status = '<button type="button" class="btn btn-success pointer_button" style="width: 130px !important;">
                <span class="badge  badge-success">Accepted</span>
              </button>';

            } else {
                $status = '<button type="button" class="btn btn-danger pointer_button" style="width: 130px !important;">
                <span class="badge  badge-danger">Pending</span>
              </button>';
            }

            if ($data->order_type == 1) {
                $order_type = '
                <span class="badge  badge-success">Online</span>
              ';
            } else {
                $order_type = '
                <span class="badge  badge-danger">Pickup Order</span>
              ';
            }
            if ($data->payment_type == 1) {
                $payment_type = '
                <span class="badge  badge-success">Online Payment</span>
              ';
            } elseif ($data->payment_type == 3) {
                $payment_type = '
                 <span class="badge  badge-danger">Cash On Delivery</span>
                 ';
            } else {
                $payment_type = '
                    <span class="badge  badge-danger">Online Payment</span>
              ';
            }
            // Conditionally set the payment status with HTML span elements for badge
            if ($data->payment_type == 3) { // Cash On Delivery
                if ($data->order_status == 3) {
                    $payment_status = '
            <span class="badge badge-success">Success</span>
        ';
                } else {
                    if ($data->payment_status == 1) {
                        $payment_status = '
                <span class="badge badge-success">Pending</span>
            ';
                    } elseif ($data->payment_status == 2) {
                        $payment_status = '
                <span class="badge badge-success">Success</span>
            ';
                    } else {
                        $payment_status = '
                <span class="badge badge-danger">Failed</span>
            ';
                    }
                }
            } else { // Other payment types
                if ($data->payment_status == 1) {
                    $payment_status = '
            <span class="badge badge-success">Success</span>
        ';
                } else if($data->payment_status == 2) {
                    $payment_status = '
            <span class="badge badge-danger">Failed</span>
        ';
                }
                else
                {
                $payment_status = '
                    <span class="badge badge-success">Pending</span>';
                }
            }


            // $date = \Helper::converttimeTozone($data->order_date);
            $date = $data->order_date;
            $settings = Setting::find(1);
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $orderShow . '" title="show"> </a>';

            $update_status_option = "";
            // dd($data->user_id);
            // if ($data->order_status != 4) {
            if ($data->order_status != 4  && $data->order_status != 3) {

                $update_status_option = '<div class="status-dropdown">
                <div class="status-dropdown-button btn" type="button" onclick="toggleDropdown(' . ($key + 1) . ')">
                    <span class="material-icons" style="transform: rotate(0deg); color: rgb(0, 0, 0); font-size: 30px;"> arrow_drop_down </span>
                </div>
                <div id="statusDropdown' . ($key + 1) . '" class="status-dropdown-content">';

                if ($data->order_status == 1) {
                    $update_status_option .= '<a class="status-dropdown-item" data-id="' . $data->id . '" data-orderid="' . (isset($data->order_id) ? $data->order_id : '') . '" onclick="updateStatus(this, `accepted`, 2,`' . $data->customer_mobile . '`)" >Accepted</a>';
                }
                if (($data->order_status == 1 || $data->order_status == 2) && $data->order_type !=1  ) {
                    $update_status_option .= '<a class="status-dropdown-item" data-id="' . $data->id . '" data-orderid="' . (isset($data->order_id) ? $data->order_id : '') . '" onclick="updateStatus(this, `ready to pickup`, 5,`' . $data->customer_mobile . '`)" >Ready to pickup</a>';
                }
                if (($data->order_status == 1 || $data->order_status == 2 || $data->order_status == 5) && $data->order_type==1) {
                    $update_status_option .= '<a class="status-dropdown-item" data-id="' . $data->id . '" data-orderid="' . (isset($data->order_id) ? $data->order_id : '') . '" onclick="updateStatus(this, `out of delivery`, 6,`' . $data->customer_mobile . '`)" >Out for delivery</a>';
                }
                if ($data->order_status == 2 || $data->order_status == 1 || $data->order_status == 5 || $data->order_status == 6) {
                    $update_status_option .= '<a class="status-dropdown-item" data-id="' . $data->id . '" data-orderid="' . (isset($data->order_id) ? $data->order_id : '') . '" onclick="updateStatus(this, `delivered`, 3, `' . $data->customer_mobile . '`)">Delivered</a>';
                }

                // if ($data->order_status != 4)
                if ($data->order_status != 4  && $data->order_status != 3)
                    $update_status_option .= '<a class="status-dropdown-item" data-id="' . $data->id . '" data-orderid="' . (isset($data->order_id) ? $data->order_id : '') . '" onclick="updateStatus(this, `Cancelled`, 4)">Cancel</a>';

                $update_status_option .= '</div></div>';
            }

            $options .= $update_status_option;
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" => isset($data->order_id) ? $data->order_id : '',
                "customer_name" => isset($data->customer_name) ? ucfirst($data->customer_name) : '',
                "customer_mobile" => @'+'. $data->customer_mobile,
                "country_name" => isset($data->country_name) ? $data->country_name : '-',
                "order_type" => isset($order_type) ? $order_type : '',
                "cancelled_by"=>isset($data->cancelled_by) ? $data->cancelled_by : '',
                "grand_total_amount" => @Helper::numberFormat($data->payable_amount) . ' ' . @$settings->currency_symbol,
                "promo_code" => isset($data->promocode_name) ? ucfirst($data->promocode_name) : '',
                "order_date" => @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
                "delivery_status" => isset($status) ? $status : '',
                "payment_type" => isset($payment_type) ? $payment_type : '',
                "payment_status" => isset($payment_status) ? $payment_status : '',
                "options" => isset($options) ? $options : '',
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
