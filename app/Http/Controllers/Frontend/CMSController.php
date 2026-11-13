<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MainUser;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\Cms;
use App\Models\Faq;
use App\Models\EmailTemplate;
use App\Models\Country;
use App\Models\InquiryReason;
use Mail;
use DB;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\OrderInfo;
use Carbon\Carbon;
use Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Helper;
use App\Models\AdminNotifications;
use App\Models\Transactions;

class CMSController extends Controller
{

    public $setting;
    public function __construct()
    {
        $this->setting = Setting::find(1);
    }

    public function aboutUs(Request $request)
    {
        $setting = $this->setting;
        if ($request->segment(1) == 'about-us') {
            $pageInfo = Cms::find(1);
        } elseif ($request->segment(1) == 'our-company') {
            $pageInfo = Cms::find(24);
            // dd($pageInfo);
        } elseif ($request->segment(1) == 'our-history') {
            $pageInfo = Cms::find(25);
        } elseif ($request->segment(1) == 'responsible-drinking') {
            $pageInfo = Cms::find(26);
        } elseif ($request->segment(1) == 'privacy-policy') {
            $pageInfo = Cms::find(29);
        }
        return view("frontend.cms.about_us.common_cms", compact('pageInfo', 'setting'));
    }

    public function faq()
    {
        $faqData = Faq::where('status', 1)->orderBy('id', 'DESC')->get();
        $pageInfo = Cms::find(19);
        return view("frontend.cms.help_and_support.faq", compact('faqData', 'pageInfo'));
    }

    // public function termsandConditions()
    // {
    //     $setting = $this->setting;
    //     $termsandConditions = Cms::find(3);
    //     return view("frontEnd.cms.terms&conditions", compact('termsandConditions', 'setting'));
    // }

    public function privacyPolicy()
    {
        $setting = $this->setting;
        $privacy = Cms::find(2);
        return view("frontEnd.cms.privacy-policy", compact('privacy', 'setting'));
    }

    public function contactUs()
    {
        $setting = $this->setting;

        return view("frontEnd.cms.contact-us", compact('setting'));
    }

    // public function contactUsStore(Request $request)
    // {

    //     dd($request);
    //     $request->validate(
    //         [
    //             'fname'=>'required|regex:/^[A-Z]+$/i|max:20',
    //             'lname'=>'required|regex:/^[A-Z]+$/i|max:20',
    //             'email'=>'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:inquiry,email',
    //             'phone'=>'required|numeric|unique:inquiry,phone',
    //             'message'=>'required'
    //         ], [
    //                 'fname.required' => 'The first name field is required.',
    //                 'fname.max' => 'The first name may not be greater than 20 characters.',
    //                 'lname.max' => 'The last name may not be greater than 20 characters.',
    //                 'fname.regex' => 'The first name should be only alphabetic characters.',
    //                 'lname.regex' => 'The last name should be only alphabetic characters.',
    //                 'lname.required' => 'The last name field is required.'
    //         ]);

    //     $inquiry = new Inquiry;
    //     $inquiry->name = isset($request->fname) && isset($request->lname) ? $request->fname .' '. $request->lname : '';
    //     $inquiry->email = isset($request->email) ? $request->email : '';
    //     $inquiry->phone = isset($request->phone) ? $request->phone : '';
    //     $inquiry->phone_code = isset($request->phone_code) ? $request->phone_code : '';
    //     $inquiry->message = isset($request->message) ? $request->message : '';

    //     $inquiry->status = 1;

    //     $inquiry->save();

    //     $logo = \Config::get('app.url').'public/assets/dashboard/images/liquor.png';

    //     $url_link = \URL::to("/");
    //     $url = $url_link . '/';
    //     $name = $inquiry->name;
    //     $email = $inquiry->email;
    //     $phone = $inquiry->phone;
    //     $msg = $inquiry->message;


    //     $ismail = $this->attachment_email($name, $email, $phone, $msg, $url, $logo);


    //     Alert::success('Success', 'Thank you for Contact Us');
    //     return back();
    //     // return redirect()->route('websitelogin')->with("success", "User Registration Successfully");
    // }

    // public function attachment_email($name, $email, $phone, $msg, $url, $logo) {


    //     $setting = Setting::find(1);
    //     $from_email = 'admin@vrinsoft.com';

    //    // $from_email = $setting['from_email'];
    //     $data = array('name' => $name, 'email' => $email, 'phone' => $phone, 'msg' => $msg, 'url' => $url,'id'=>'4','logo' => $logo, 'from_email' => $from_email);

    //     \Mail::send('contactus', $data, function ($message) use ($data) {

    //     $message->to('rutvik@mailinator.com', 'OnlyDance')->subject('Thank you for Contact Us!');
    //     //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //     $message->from($data['from_email'], 'OnlyDance');
    //     });

    // }

    public function customerSupport()
    {
        return view("frontend.cms.customer_support");
    }

    public function deliveryInformation()
    {
        $pageInfo = Cms::find(7);
        return view("frontend.cms.delivery_and_returns.delivery-information", compact('pageInfo'));
    }

    public function returnsCancellation()
    {
        $pageInfo = Cms::find(8);
        return view("frontend.cms.delivery_and_returns.returns-and-cancellation", compact('pageInfo'));
    }

    public function damagesWrongGoods()
    {
        $pageInfo = Cms::find(11);
        return view("frontend.cms.delivery_and_returns.damage", compact('pageInfo'));
    }

    public function ourPackaging()
    {
        $pageInfo = Cms::find(13);
        return view("frontend.cms.delivery_and_returns.our-packaging", compact('pageInfo'));
    }

    public function paymentOption()
    {
        $pageInfo = Cms::find(17);
        return view("frontend.cms.shopping_with_us.payment-option", compact('pageInfo'));
    }

    public function placingOrder()
    {
        $pageInfo = Cms::find(16);
        return view("frontend.cms.shopping_with_us.placing-order", compact('pageInfo'));
    }

    public function securityPrivacy()
    {
        $pageInfo = Cms::find(2);
        // dd($pageInfo);
        return view("frontend.cms.shopping_with_us.security-privacy", compact('pageInfo'));
    }

    public function termsCondition()
    {
        $pageInfo = Cms::find(3);
        return view("frontend.cms.shopping_with_us.terms-condition", compact('pageInfo'));
    }

    public function headOffice()
    {
        $WebmasterSetting = Setting::find(1);
        $pageInfo = Cms::find(30);
        return view("frontend.cms.contact_us.head_office", compact('WebmasterSetting', 'pageInfo'));
    }

    public function orderByPhone()
    {
        $pageInfo = Cms::find(21);
        return view("frontend.cms.contact_us.order_by_phone", compact('pageInfo'));
    }

    public function tradeEnquieries()
    {
        $pageInfo = Cms::find(22);
        return view("frontend.cms.contact_us.trade_enquiries", compact('pageInfo'));
    }

    public function pressEnquieries()
    {
        $pageInfo = Cms::find(23);
        // dd($pageInfo);
        return view("frontend.cms.contact_us.press_enquiries", compact('pageInfo'));
    }


    public function trackOrder()
    {

        $pageInfo = Cms::find(6);
        // dd($pageInfo);
        return view("frontend.cms.help_and_support.trackorder", compact('pageInfo'));
    }

    public function checkOrderStatus(Request $request)
    {

        $request->validate(
            [
                'order_number' => 'required'
            ],
            [
                'order_number.required' => \Helper::language('the_order_number_field_is_required.')
            ]
        );
        $orderDetails = Order::where('order_id', $request->order_number)->first();

        // Transaction Details
        $transactions = Transactions::where('order_id', $orderDetails->id)->first();

        $payment_type=null;
        // Payment Type
        if ($transactions->payment_type == 1) {
            $payment_type = 'Online Payment';
        } elseif ($transactions->payment_type == 3) {
            $payment_type = 'Cash On Delivery';
        } else {
            $payment_type = 'Online Payment';
        }

        $order_type=null;
        if ($orderDetails->order_type == 1) {
            $order_type = 'Online';
        } else {
            $order_type = 'Pickup Order';
        }
        $address = OrderInfo::where('order_id', $orderDetails->id)->value('store_pickup_address');

        if ($orderDetails) {
            $order_status = DB::table('order_status')->where('id', $orderDetails->order_status)->first();

            if ($orderDetails->order_type == 1) {
                // Online Order
                $status_steps = ['Pending', 'Accepted', 'Out for Delivery', 'Delivered'];
                $icon_classes = ['fa-box', 'fa-check', 'fa-shipping-fast', 'fa-home'];
            } else {
                // Pickup Order 
                $status_steps = ['Pending', 'Accepted', 'Ready to Pick Up', 'Delivered'];
                $icon_classes = ['fa-box', 'fa-check', 'fa-truck', 'fa-home'];
            }

            
            if ($orderDetails->order_status == 4 || strtolower($order_status->name) === 'cancelled') {
                $status_steps[] = 'Cancelled';
                $icon_classes[] = 'fa-ban';
            }

            $currentStatusIndex = array_search(strtolower($order_status->name), array_map('strtolower', $status_steps));

            // if (($orderDetails->order_type == 2 || $orderDetails->order_type == 3) && $orderDetails->order_status == 2) {
            if (($orderDetails->order_type == 2 || $orderDetails->order_type == 3) && $orderDetails->order_status != 4) {
                $address = $address ?? 'Not Available'; 
            } else {
                $address = 'Not Available';
            }
    
            return response()->json([
                'status' => 'success',
                'order_id' => $orderDetails->order_id,
                'current_status' => $order_status->name,
                'status_steps' => $status_steps,
                'icon_classes' => $icon_classes,
                'currentStatusIndex' => $currentStatusIndex,
                'address' => $address,
                'shipping_address'=>$orderDetails->delivery_address,
                'order_date'=>$orderDetails->order_date,
                'amount'=>$orderDetails->payable_amount,
                'order_type'=>$order_type,
                'order_num'=>$orderDetails->order_type,
                'payment_type'=>$payment_type,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => Helper::language('invalid_order_number')
            ]);
        }


    }

    public function queries()
    {
        $pageInfo = Cms::find(18);
        // dd($pageInfo);
        $countryInfo = Country::orderBy('phonecode', 'ASC')->get();
        $inquiryReason = InquiryReason::where('status', 1)->get();
        return view("frontend.cms.help_and_support.queries", compact('pageInfo', 'countryInfo', 'inquiryReason'));
    }

    public function queriesStore(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|max:40',
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                'phone_code' => 'required|numeric',
                'phone_number' => 'required|digits_between:8,15|numeric',
                'message_title' => 'required|max:60',
                'message_description' => 'required|max:750',
                'reason' => 'required',
            ],
            [
                'name.required' => \Helper::language('the_name_field_is_required'),
                'name.max' => \Helper::language('the_name_may_not_be_greater_than_40_characters'),
                'name.regex' => \Helper::language('the_name_should_be_only_alphabetic_characters'),

                'email.required' => \Helper::language('email_field_required'),
                'email.email' => \Helper::language('enter_valid_email_validation'),
                'email.regex' => \Helper::language('the_email_format_is_invalid'),

                'phone_code.required' => \Helper::language('phone_code_field_is_required'),
                'phone_code.numeric' => \Helper::language('phone_code_must_be_numeric'),

                'phone_number.required' => \Helper::language('phone_number_field_is_required'),
                'phone_number.digits_between' => \Helper::language('phone_number_min_max'),

                'message_title.required' => \Helper::language('the_message_title_field_is_required.'),
                'message_title.max' => \Helper::language('the_message_title_should_be_60_characters'),

                'message_description.required' => \Helper::language('the_message_description_field_is_required'),
                'message_description.max' => \Helper::language('the_message_description_should_be_750_characters'),
                'reason.required' => \Helper::language('reason_field_required'),
            ]
        );

        $inquiry = new Inquiry;
        $inquiry->name = isset($request->name) ? $request->name : '';
        $inquiry->email = isset($request->email) ? $request->email : '';
        $inquiry->phone = isset($request->phone_number) ? $request->phone_number : '';
        $inquiry->phone_code = isset($request->phone_code) ? $request->phone_code : '';
        $inquiry->message = isset($request->message_title) ? $request->message_title : '';
        $inquiry->message_description = isset($request->message_description) ? $request->message_description : '';
        $inquiry->reason_id = isset($request->reason) ? $request->reason : '';
        $inquiry->status = 1;

        $inquiry->save();


        $admin_notification = new AdminNotifications();
        $admin_notification->inquiry_id = @$inquiry->id;
      
        $admin_notification->notification_type = 2;
    
        $admin_notification->save();
        // $logo = \Config::get('app.url').'public/assets/dashboard/images/liquor.png';

        // $url_link = \URL::to("/");
        // $url = $url_link . '/';
        // $name = $inquiry->name;
        // $email = $inquiry->email;
        // $phone = $inquiry->phone;
        // $msg = $inquiry->message;
        // $ismail = $this->attachment_email($name, $email, $phone, $msg, $url, $logo);

        $query_reason = InquiryReason::where('id', $inquiry->reason_id)->first();

        $adminEmail = 'info@liquorjunctionghana.com';
        $adminSubject = 'New Inquiry Received';
        $adminEmailContent = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .content { background-color: #f1f1f1; padding: 15px; border-radius: 5px; }
                .item { margin-bottom: 10px; display: flex; }
                .label { font-weight: bold; width: 150px; }
                .value { margin-left: 10px; flex: 1; }
            </style>
        </head>
        <body>
              <div class='container' style='background-color: #f1f1f1; width: 100%; padding: 20px;margin-top: -130px;'>
                <div class='content'>
                  <div class='header' style='font-size: 18px; font-weight: bold; margin-bottom: 10px;'><b>A new inquiry has been received</b></div>
                    <div class='item'><span class='label'><b>Inquiry ID:</b></span> <span class='value'>{$inquiry->id}</span></div>
                    <div class='item'><span class='label'><b>Name:</b></span> <span class='value'>{$inquiry->name}</span></div>
                    <div class='item'><span class='label'><b>Email:</b></span> <span class='value'>{$inquiry->email}</span></div>
                    <div class='item'><span class='label'><b>Phone:</b></span> <span class='value'>+{$inquiry->phone_code} {$inquiry->phone}</span></div>
                    <div class='item'><span class='label'><b>Message Title:</b></span> <span class='value'>{$inquiry->message}</span></div>
                    <div class='item'><span class='label'><b>Message Description:</b></span> <span class='value'>{$inquiry->message_description}</span></div>
                    <div class='item'><span class='label'><b>Reason:</b></span> <span class='value'>{$query_reason->title}</span></div>
                </div>
            </div>
        </body>
        </html>";
//  print_r( $adminEmailContent);
//  die;
        
        Mail::raw($adminEmailContent, function ($message) use ($adminEmail, $adminSubject) {
            $message->to($adminEmail)
                ->subject($adminSubject)
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')); // Ensure this is properly configured in your .env file
        });
    
        Alert::success(\Helper::language('success'), @Helper::language('queries_message'));
        return back();
        // return redirect()->route('websitelogin')->with("success", "User Registration Successfully");
    }

    public function ourStore()
    {
        $pageInfo = Cms::find(28);
        // dd($pageInfo);
        return view("frontend.cms.our_shops.our_shop", compact('pageInfo'));
    }

}
