<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Session\Store;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderInfo;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\Promocode;
use App\Models\Transactions;
use App\Models\TransactionTokens;
use App\Models\UsersPayments;
use App\Models\UserAddress;
use App\Models\UserBillAddress;
use App\Models\Notification;
use App\Models\AdminNotifications;
use App\Models\Discount;
use App\Models\LoyaltyPoints;
use App\Models\Loyalty;
use App\Models\Offers;

use Mail;
use Carbon\Carbon;
use Cookie;
use Storage;
use DB;
use Session;
use File;
use Helper;
use Redirect;
use App\Models\Area;
use App\Models\Country;
use App\Models\Region;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    //
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

    public function Checkout(Request $request)
    {
        $user_id = $this->user_id;
        $is_buy_now = 0;

        if ($this->user_id == '') {
            Session::put('checkout_value', 1);
            Session::put('pack_size', $request->pack_size);
            Session::put('counter', $request->counter);
            return redirect('login');
        }

        if (Session::has('checkout_in_progress') && !$request->has('from_cart')) {
            Session::forget('checkout_in_progress');
        }

        if (Session::get('checkout_value') == 1) {
            $variantIds = $request->pack_size ? $request->pack_size : Session::get('pack_size');
            $variant_quantity = $request->counter ? $request->counter : Session::get('counter');
        } else {
            $variantIds = $request->pack_size;
            $variant_quantity = $request->counter;
        }

        $is_page_reload = $request->input('page_reload_flag') == '1';
        // Handle Buy Now session - load once
        if ((empty($variantIds) || empty($variant_quantity)) && Session::has('buy_now_info')  && !$is_page_reload) {
            $buyNowData = Session::get('buy_now_info');
            $variantIds = $buyNowData['variantId'] ?? null;
            $variant_quantity = $buyNowData['quantity'] ?? null;

            $is_buy_now = 1;
        }

        // Clear session values related to cart selection
        Session::forget('checkout_value');
        Session::forget('pack_size');
        Session::forget('counter');
        Session::put('checkout_page_load_time', now()->timestamp);
        Session::put('checkout_in_progress', true);

        $setting = $this->setting;

        if ($is_buy_now == 1 && Session::has('buy_now_info')) {
            // Only show Buy Now item
            $variantIds = [$variantIds]; // already handled
        } else {
            // Normal cart-based checkout
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))
                ->where('user_id', $user_id)
                ->groupBy('user_id')
                ->where('status', 1)
                ->first();

            if (empty($cartData->variantIds)) {
                return Redirect::to('cart');
            }

            $variantIds = explode(',', $cartData->variantIds);
            $data = Cart::where('user_id', $user_id)->groupBy('user_id')->where('status', 1)->first();
            $productData = ProductVariants::whereIn('id', $variantIds)->get();

            foreach ($productData as $result) {
                $cart_data = Cart::where('product_variant_id', $result->id)->where('user_id', $user_id)->where('status', 1)->first();
                $offer_price = $result->variant_discounted_price;
                $varint_pro_price = $result->variant_price;

                if ($result->variant_discounted_price != '' && $result->variant_discounted_price != 0.00) {
                    $tcart_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * $cart_data->quantity) : 0;
                } else {
                    $tcart_price = @$result->variant_price ? ($result->variant_price * $cart_data->quantity) : 0;
                }

                $cart_data->product_price = $varint_pro_price;
                $cart_data->offer_price = $offer_price;
                $cart_data->total_price = $tcart_price;
                $cart_data->save();
            }
        }

        $userData = \DB::table('main_users')->where('id', $user_id)->where('status', 1)->first();
        $countryData = \DB::table('countries')->orderby('phonecode', 'ASC')->where('status', 1)->get();
        $region = Region::where('status', 1)->orderby('title', 'ASC')->get();
        $area = Area::where('status', 1)->orderby('title', 'ASC')->get();

        $UserAddressData = UserAddress::withWhereHas('country', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('region', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('area', function ($query) {
            $query->where('status', 1);
        })->where([['user_id', $user_id], ['status', 1]])->get();

        $UserBillAddressData = UserBillAddress::withWhereHas('country', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('region', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('area', function ($query) {
            $query->where('status', 1);
        })->where([['user_id', $user_id], ['status', 1], ['default', 1]])->get();

        $store_address = DB::table('store_details')->where('id', $user_id)->where('status', 1)->get();
        $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

        // Buy Now Add-on BOGO logic
        if ($is_buy_now == 1 && Session::has('buy_now_info')) {
            $buyNowData = Session::get('buy_now_info');
            $bogo_status_raw = $buyNowData['is_bogo'] ?? 0;
            $bogo_status = is_numeric($bogo_status_raw) ? (int)$bogo_status_raw : 0;

            // offer details
            $offer_status_raw = $buyNowData['is_offer'] ?? 0;
            $offer_status = is_numeric($offer_status_raw) ? (int)$offer_status_raw : 0;
            $offer_type = $buyNowData['offer_type'] ?? null;
            $discount_amount = $buyNowData['discount_amount'] ?? null;

            foreach ($productData as $product) {
                $product->is_bogo = $bogo_status;
                $product->is_offer = $offer_status;
                $product->offer_type = $offer_type;
                $product->discount_amount = $discount_amount;
            }
        }

        if ($is_buy_now == 0 && !Session::has('checkout_value')) {
            Session::forget('buy_now_info');
        }


        if (count($productData) == 0) {
            return redirect()->back();
        }

        $current_lat = Session::get('current_lat');
        $current_long = Session::get('current_long');
        $setting = Setting::find(1);
        $diff = $setting->map_distance;

        if ($current_lat) {
            $storeMapData = DB::table('store_details')->select('store_details.*', DB::raw("(6371 * acos(cos(radians('" . $current_lat . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $current_long . "')) + sin(radians('" . $current_lat . "')) * sin(radians(store_details.latitude)))) as distance"))->where('store_details.status', 1)
                ->havingRaw('distance <=' . $diff)->orderBy('distance', 'ASC')->get();
        } else {
            $storeMapData = DB::table('store_details')->orderby('store_details.id', 'DESC')->where('store_details.status', 1)->get();
        }

        $locations = [];
        foreach ($storeMapData as $test) {
            $locations[] = [
                "lat" => $test->latitude,
                "lng" => $test->longitude,
                "id" => $test->id
            ];
        }

        if (empty($current_lat)) {
            $current_lat = $storeMapData[0]->latitude ?? 0;
        }
        if (empty($current_long)) {
            $current_long = $storeMapData[0]->longitude ?? 0;
        }

        // Discount section
        $today = Carbon::now()->toDateString();
        $discount = Discount::whereNotIn('status', [0, 2])
            ->whereDate('expiry_date', '>=', $today)
            ->first();

        $discountDetails = [];
        if ($discount) {
            if ($discount->discount_type == 'flat') {
                $discountDetails = [
                    'discount_type' => 'flat',
                    'discount_value' => $discount->discount_amount,
                    'minimum_amount' => $discount->min_amount,
                    'upto_amount' => $discount->upto_amount,
                ];
            } elseif ($discount->discount_type == 'percentage') {
                $discountDetails = [
                    'discount_type' => 'percentage',
                    'discount_value' => $discount->discount_percentage,
                    'minimum_amount' => $discount->min_amount,
                    'upto_amount' => $discount->upto_amount,
                ];
            }
        }

        // Loyalty Points
        $points = LoyaltyPoints::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Credit points: order must be delivered
                    $q->where('type', 'credit')
                        ->where('status', 1)
                        ->whereIn('order_id', function ($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                })->orWhere(function ($q) {
                    // Debit points: order must be delivered
                    $q->where('type', 'debit')
                        ->where('status', 1)
                        ->whereIn('order_id', function ($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPoints = 0;
        foreach ($points as $point) {
            if ($point->type === 'credit') {
                $totalPoints += $point->points;
            } elseif ($point->type === 'debit') {
                $totalPoints -= $point->points;
            }
        }

        // Loyalty Info
        $loyaltyInfo = Loyalty::where('status', '=', '1')->first();

        return view("frontend.checkout.checkout", compact(
            'userData',
            'countryData',
            'area',
            'region',
            'UserAddressData',
            'UserBillAddressData',
            'store_address',
            'storeMapData',
            'locations',
            'current_lat',
            'current_long',
            'productData',
            'user_id',
            'variant_quantity',
            'is_buy_now',
            'discountDetails',
            'totalPoints',
            'loyaltyInfo'
        ));
    }

    public function moveBuyNowToCart(Request $request)
    {
        $user = Auth::guard('user')->user();

        if ($request->input('source') !== 'beacon') {
            return response()->json(['status' => 'ignored']);
        }

        if (!Session::has('buy_now_info')) {
            return response()->json(['status' => 'already_moved_or_invalid']);
        }


        // Trigger only if Buy Now was in progress
        if (Session::has('checkout_in_progress') && Session::has('buy_now_info')) {

            $buyNowData = Session::get('buy_now_info');

            $product_id_encoded = $buyNowData['product_id'] ?? null;
            $variantId = $buyNowData['variantId'] ?? null;
            $quantity = (int) ($buyNowData['quantity'] ?? 1);
            $is_bogo = isset($buyNowData['is_bogo']) && is_numeric($buyNowData['is_bogo']) ? (int)$buyNowData['is_bogo'] : 0;
            $is_offer = isset($buyNowData['is_offer']) && is_numeric($buyNowData['is_offer']) ? (int)$buyNowData['is_offer'] : 0;
            $offer_type = $buyNowData['offer_type'] ?? null;
            $discount_amount = $buyNowData['discount_amount'] ?? null;

            $product_id = base64_decode($product_id_encoded);

            if ($variantId && $product_id) {
                if (!$user) {
                    // Guest cart
                    $cart = session()->get('cart_info', []);
                    $new_item = ['quantity' => $quantity, 'is_bogo' => $is_bogo];

                    if (!$is_bogo && $is_offer) {
                        $new_item['offer_type'] = $offer_type;
                        $new_item['discount_amount'] = $discount_amount;
                        $new_item['is_offer'] = $is_offer;
                    }

                    if (isset($cart[$product_id][$variantId])) {
                        $cart[$product_id][$variantId]['quantity'] += $quantity;
                    } else {
                        $cart[$product_id][$variantId] = $new_item;
                    }

                    session(['cart_info' => $cart]);
                } else {
                    // Logged-in user cart
                    $user_id = $user->id;
                    $product_variant_details = DB::table('product')
                        ->leftJoin('product_variants', 'product_variants.product_id', '=', 'product.id')
                        ->where('product.id', $product_id)
                        ->where('product_variants.id', $variantId)
                        ->select('product_variants.*')
                        ->first();

                    if ($product_variant_details) {
                        $price = $product_variant_details->variant_discounted_price > 0
                            ? $product_variant_details->variant_discounted_price
                            : $product_variant_details->variant_price;

                        $total_price = $quantity * $price;

                        $cart = Cart::where('user_id', $user_id)
                            ->where('product_id', $product_id)
                            ->where('product_variant_id', $variantId)
                            ->where('status', 1)
                            ->first();

                        if ($cart) {
                            $cart->update([
                                'quantity' => $cart->quantity + $quantity,
                                'product_price' => $product_variant_details->variant_price,
                                'offer_price' => $product_variant_details->variant_discounted_price,
                                'total_price' => $total_price,
                                'is_bogo' => $is_bogo,
                                'is_offer' => $is_offer,
                                'offer_type' => $offer_type,
                                'discount_amount' => $discount_amount
                            ]);
                        } else {
                            Cart::create([
                                'uniqid' => uniqid(),
                                'product_id' => $product_id,
                                'product_variant_id' => $variantId,
                                'product_price' => $product_variant_details->variant_price,
                                'offer_price' => $product_variant_details->variant_discounted_price,
                                'quantity' => $quantity,
                                'total_price' => $total_price,
                                'user_id' => $user_id,
                                'order_type' => 1,
                                'is_bogo' => $is_bogo,
                                'is_offer' => $is_offer,
                                'offer_type' => $offer_type,
                                'discount_amount' => $discount_amount,
                                'status' => 1
                            ]);
                        }
                    }
                }
            }

            // Clear both flags
            Session::forget('checkout_in_progress');
            Session::forget('buy_now_info');

            return response()->json(['status' => 'moved']);
        }

        return response()->json(['status' => 'skipped']);
    }

    public function updateBuyNowQuantity(Request $request)
    {
        if (!Session::has('buy_now_info')) {
            return response()->json(['status' => false, 'message' => 'Buy Now data not found.']);
        }

        $quantity = (int) $request->input('quantity');

        if ($quantity < 1) {
            return response()->json(['status' => false, 'message' => 'Invalid quantity']);
        }

        $buyNowData = Session::get('buy_now_info');
        $buyNowData['quantity'] = $quantity;
        Session::put('buy_now_info', $buyNowData);

        return response()->json(['status' => true, 'message' => 'Quantity updated']);
    }


    public function add_address(Request $request)
    {
        // $settings = Setting::find(1);
        // $key = $request->count;
        // $uofs = Uofs::where('status', 1)->orderBy('id', 'asc')->get();
        // $html = view('dashboard.product.addMoreVariant')->with(['key' => $key, 'settings' => $settings, 'uofs' => $uofs])->render();
        return response()->json(['success' => true, 'html' => $html]);
    }
    public function getSubcatlist(Request $request)
    {
        $id = $request->id;
        $data['sub'] = Region::where('country_id', '=', $id)->where('status', 1)
            ->get(["title", "id"]);
        return response()
            ->json($data);
    }
    public function getArealist(Request $request)
    {
        $id = $request->id;
        $data['sub'] = Area::where('region_id', '=', $id)->where('status', 1)
            ->get(["title", "id"]);
        return response()
            ->json($data);
    }

    public function checkouteditAddress($id)
    {
        $user_id = $this->user_id;
        $UserAddressData = DB::table('user_address')->where('user_id', $user_id)->where('id', $id)->where('status', 1)
            ->first();
        $countryData = \DB::table('countries')->where('status', 1)
            ->orderby('phonecode', 'ASC')
            ->get();
        $region = Region::where('status', 1)->orderby('title', 'ASC')
            ->get();
        // $area = Area::where('status', 1)->orderby('title', 'ASC')
        //     ->get();

        $area = [];

        if ($UserAddressData && $UserAddressData->region_id) {
            $area = Area::where('status', 1)
                ->where('region_id', $UserAddressData->region_id)
                ->orderby('title', 'ASC')
                ->get();
        }

        $html = view('frontend.checkout.edit-address')->with(['UserAddressData' => $UserAddressData, 'countryData' => $countryData, 'region' => $region, 'area' => $area])->render();

        return response()
            ->json(['success' => true, 'html' => $html]);
    }

    public function checkouteditBillAddress($id)
    {
        $user_id = $this->user_id;
        $UserAddressData = DB::table('user_bill_address')->where('user_id', $user_id)->where('id', $id)->where('status', 1)
            ->first();
        $countryData = \DB::table('countries')->where('status', 1)
            ->orderby('phonecode', 'ASC')
            ->get();
        $region = Region::where('status', 1)->orderby('title', 'ASC')
            ->get();
        // $area = Area::where('status', 1)->orderby('title', 'ASC')
        //     ->get();

        $area = [];

        if ($UserAddressData && $UserAddressData->region_id) {
            $area = Area::where('status', 1)
                ->where('region_id', $UserAddressData->region_id)
                ->orderby('title', 'ASC')
                ->get();
        }

        $html = view('frontend.checkout.edit-bill-address')->with(['UserAddressData' => $UserAddressData, 'countryData' => $countryData, 'region' => $region, 'area' => $area])->render();

        return response()
            ->json(['success' => true, 'html' => $html]);
    }


    public function geneateOrderId()
    {
        $uid = Order::orderBy('created_at', 'DESC')->first();
        if ($uid && $uid->id != null) {
            $temp_uid = (int)str_replace('LQ', '', $uid->id) + 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        } else {
            $temp_uid = 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        }
        return 'LQOID' . $temp_uid;
    }

    public function geneateTranscationId()
    {
        $uid = Transactions::orderBy('created_at', 'DESC')->first();
        if ($uid && $uid->id != null) {
            $temp_uid = (int)str_replace('LQTC', '', $uid->id) + 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        } else {
            $temp_uid = 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        }
        return 'LQTC' . $temp_uid;
    }



    public function sendOrderConfirmationEmail($user_id, $order)
    {
        $order_details = OrderDetails::where('order_id', $order->id)->get(); // Get all order details
        $transactions = Transactions::where('order_id', $order->id)->first();
        $order_info = OrderInfo::where('order_id', $order->id)->first();
        $storePickupAddress = $order_info->store_pickup_address ?? 'Not provided';

        // Fetch customer details
        $customerDetails = DB::table('main_users')->find($user_id);
        $setting = Setting::find(1);
        $templateId = 17; // Determine the appropriate template ID
        $emailDetail = EmailTemplate::find($templateId);
        $fromEmail = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $sendEmail = $customerDetails->email;

        // Prepare email data
        $data = [
            'user_name' => $customerDetails->first_name,
            'sendname' => $customerDetails->first_name,
            'sendemail' => $customerDetails->email,
            'id' => $templateId,
            'order' => $order,
            'order_status' => "Place Order",
            'from_email' => config('mail.from.address'),
            'store_pickup_address' => $storePickupAddress,
        ];

        $order_amount = Order::with('transcations')->where('id', $order->id)->first();
        $taxes = DB::table('tax_masters')->get();

        // Bogo Discount Calculation
        $bogoDiscount = 0;
        $totalAmount = 0;

        foreach ($order_details as $order) {
            if ($order->is_bogo) {
                $display_qty = floor($order->quantity / 2);

                $unit_price = ($order->product_total_amount && $order->product_total_amount > 0)
                    ? $order->product_total_amount
                    : $order->product_original_amount;

                $bogoDiscount += $display_qty * $unit_price;
            }
        }

        foreach ($order_details as $order) {
            $display_qty =  $order->quantity;

            $unit_price = ($order->product_total_amount && $order->product_total_amount > 0)
                ? $order->product_total_amount
                : $order->product_original_amount;

            $totalAmount += $display_qty * $unit_price;
        }

        // Tax calculation
        $discountAmount = $order_amount->discount_amount ?? 0;
        $cartDiscount = $order_amount->cart_discount ?? 0;
        $rewardAmount = $order_info->reward_amount ?? 0;

        $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount;
        // $subTotal = ($order_amount->total_amount - $totalDiscount) / 1.219;
        $subTotal = ($totalAmount - $totalDiscount) * 0.83333;

        //$covidLevy = ($taxes[0]->tax_value / 100) * ($subTotal);
        $nhil = ($taxes[1]->tax_value / 100) * ($subTotal);
        $getFund = ($taxes[2]->tax_value / 100) * ($subTotal);
        $vat = ($taxes[3]->tax_value / 100) * ($subTotal);

        try {
            // Send email to customer
            Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emailDetail) {
                $message->to($data['sendemail'], 'Liquor')->subject($emailDetail->subject);
                $message->from($data['from_email'], $emailDetail->title);
            });

            // Send email to admin
            $adminEmail = 'info@liquorjunctionghana.com';
            $adminSubject = 'New Order Received';

            // Determine order type name
            if ($order_amount->order_type == 1) {
                $order_type_name = "Online Order";
            } elseif ($order_amount->order_type == 2) {
                $order_type_name = "In-store";
            } elseif ($order_amount->order_type == 3) {
                $order_type_name = "Purchase Order";
            } else {
                $order_type_name = "Unknown Order Type";
            }

            // Determine payment type name
            if ($transactions->payment_type == 1) {
                $payment_type_name = "Online Payment";
            } elseif ($transactions->payment_type == 2) {
                $payment_type_name = "Online Payment";
            } elseif ($transactions->payment_type == 3) {
                $payment_type_name = "Cash on Delivery";
            } else {
                $payment_type_name = "Unknown Payment Type";
            }

            // Determine order type from
            if ($order_info) {
                if ($order_info->order_from == 1) {
                    $order_type_from = 'Web';
                } elseif ($order_info->order_from == 2) {
                    $order_type_from = 'Android';
                } elseif ($order_info->order_from == 3) {
                    $order_type_from = 'iOS';
                } else {
                    $order_type_from = 'Unknown Source';
                }
            } else {
                $order_type_from = 'Unknown Source';
            }

            // Initialize the admin email content with the basic details
            $adminEmailContent = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { width: 100%; padding: 20px; }
                    .content { background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
                    .header { font-size: 18px; font-weight: bold; margin-bottom: 90px; }
                    .item { margin-bottom: 10px; display: flex; }
                    .label { font-weight: bold; width: 150px; }
                    .value { margin-left: 10px; flex: 1; }
                    .table th { background-color: #f2f2f2; }
                    .total-row { font-weight: bold; }
                    .table {width: 100%;border-collapse: collapse;font-family: Arial, sans-serif;}
                    .table th, .table td {padding: 10px;border: 1px solid #ddd;text-align: left;vertical-align: top}
                    .amount-cell {text-align: right;white-space: nowrap}

                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='content'>
                        <div class='header'><b>A new order has been received.</b></div>
                        <div class='item'><span class='label'><b>Customer Details:</b></span></div>
                        <div class='item'><span class='label'>Name:</span>  <span class='value'>{$customerDetails->first_name} {$customerDetails->last_name}</span></div>
                        <div class='item'><span class='label'>Email:</span>  <span class='value'>{$customerDetails->email}</span></div>
                        <div class='item'><span class='label'>Phone:</span>  <span class='value'>+{$customerDetails->phone_code}{$customerDetails->phone}</span></div>";

            // Conditionally display the delivery address
            if (!empty($storePickupAddress) && $storePickupAddress !== 'Not provided') {
                $adminEmailContent .= "
                        <div class='item'><span class='label'><b>Store Address:</b></span>  <span class='value'>{$storePickupAddress}</span></div>";
            } else {
                $adminEmailContent .= "
                        <div class='item'><span class='label'>Delivery Address:</span>  <span class='value'>{$order->delivery_address}</span></div>";
            }

            $adminEmailContent .= "
                        <div class='item'><span class='label'><b>Order Details:</b></span></div>
                        <div class='item'><span class='label'>Order ID:</span>  <span class='value'>{$order_amount->order_id}</span></div>
                        <div class='item'><span class='label'>Order Type:</span>  <span class='value'>{$order_type_name}</span></div>
                        <div class='item'><span class='label'>Order From:</span>  <span class='value'>{$order_type_from}</span></div>
                        <div class='item'><span class='label'>Order Date:</span>  <span class='value'>{$order->order_date}</span></div>
                        <div class='item'><span class='label'>Order Time:</span>  <span class='value'>{$order->order_time}</span></div>
                        <div class='item'><span class='label'>Delivery Status:</span>  <span class='value'>" . ($order->order_status == 1 ? 'Pending' : 'Other') . "</span></div>
                        <div class='item'><span class='label'>Payment Method:</span>  <span class='value'>{$payment_type_name}</span></div>
                        <div class='item' style='margin-bottom: 0;'>
                            <span class='label'><b>Product Details:</b></span>
                    </div>
                        <table class='table'>
                            <tr>
                                <th style='padding: 5px;'>Product Name</th>
                                <th style='padding: 5px;'>Product Size</th>
                                <th style='padding: 5px;'>Qty</th>
                                <th style='padding: 5px;' class='amount-cell'>Amount</th>
                                <th style='padding: 5px;' class='amount-cell'>Total</th>
                            </tr>";

            // Loop through each product in the order
            foreach ($order_details as $order_detail) {
                $product = Product::where('id', $order_detail->product_id)->first();
                $unit = ($order_detail->variant_unit == 1) ? 'ML' : 'L';
                // $display_qty = $order_detail->is_bogo ? floor($order_detail->quantity / 2) : $order_detail->quantity;
                $display_qty = $order_detail->quantity;

                // $unit_price = $order_detail->product_original_amount ?? 0;
                $unit_price = ($order_detail->product_total_amount && $order_detail->product_total_amount > 0)
                    ? $order_detail->product_total_amount
                    : $order_detail->product_original_amount;

                $total_amount = $display_qty * $unit_price;

                $formattedOriginal = number_format($order_detail->product_original_amount, 2);
                $formattedTotal = number_format($order_detail->product_total_amount, 2);
                $formattedUnit = number_format($unit_price, 2);

                $unitPriceHtml = ($order_detail->product_total_amount && $order_detail->product_total_amount < $order_detail->product_original_amount) ?
                    "<span style='text-decoration: line-through;'>{$formattedOriginal}</span>&nbsp;<span>{$formattedTotal} GH₵</span>" :
                    "{$formattedUnit} GH₵";

                $adminEmailContent .= "
                        <tr>
                            <td>{$product->product_name}</td>
                            <td>{$order_detail->variant_size}{$unit}</td>
                            <td>{$order_detail->quantity}</td>
                            <td class='amount-cell'>{$unitPriceHtml}</td>
                            <td class='amount-cell'>{$total_amount} GH₵</td>
                        </tr>";
            }

            $adminEmailContent .= "
                        </table>
                      </div>
                    </div>";


            // Add subtotal, discount, tax, delivery fee, and total amount
            $adminEmailContent .= "
                    <table style='width: 100%; margin-top: 8px; border-collapse: collapse;'>
                    <tr>
                        <td style='width: 50%; vertical-align: top;'>
                            <strong>Billing Summary</strong><br>
                            <table style='width: 100%; margin-top: 2px;'>
                                <tr>
                                    <td>Total Sale</td>
                                    <td align='right'>" . number_format($totalAmount, 2) . " GH₵</td>
                                </tr>";

            if ($discountAmount > 0) {
                $promoName = $order_info->promocode_name ? " ({$order_info->promocode_name})" : '';
                $adminEmailContent .= "
                                        <tr>
                                            <td>Coupon Discount{$promoName}</td>
                                            <td align='right'>(-) " . number_format($discountAmount, 2) . " GH₵</td>
                                        </tr>";
            }

            if ($bogoDiscount > 0) {
                $adminEmailContent .= "
                                        <tr>
                                            <td>Bogo Discount</td>
                                            <td align='right'>(-) " . number_format($bogoDiscount, 2) . " GH₵</td>
                                        </tr>";
            }

            if ($cartDiscount > 0) {
                $adminEmailContent .= "
                                        <tr>
                                            <td>Cart Discount</td>
                                            <td align='right'>(-) " . number_format($cartDiscount, 2) . " GH₵</td>
                                        </tr>";
            }

            if ($rewardAmount > 0) {
                $adminEmailContent .= "
                                        <tr>
                                            <td>Reward Discount</td>
                                            <td align='right'>(-) " . number_format($rewardAmount, 2) . " GH₵</td>
                                        </tr>";
            }

            if ($totalDiscount == 0) {
                $adminEmailContent .= "
                                        <tr>
                                            <td>Discount</td>
                                            <td align='right'>0 GH₵</td>
                                        </tr>";
            }

            $adminEmailContent .= "
                                            <tr><td colspan='2'><hr></td></tr>
                                            <tr>
                                                <td><strong>Total (After Discount)</strong></td>
                                                <td align='right'><strong>" . number_format(($totalAmount - $totalDiscount), 2) . " GH₵</strong></td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td style='width: 50%; vertical-align: top;'>
                                            <strong>Tax Summary</strong><br>
                                            <table style='width: 100%; margin-top: 2px;'>
                                                <tr><td>(i) Tax Exclusive Value</td><td align='right'>" . number_format($subTotal, 2) . " GH₵</td></tr>
                                                <tr><td>(ii) NHIL (2.5%)</td><td align='right'>" . number_format($nhil, 2) . " GH₵</td></tr>
                                                <tr><td>(iii) Get Funds Levy (2.5%)</td><td align='right'>" . number_format($getFund, 2) . " GH₵</td></tr>
                                                <tr><td>(iv) VAT 15%</td><td align='right'>" . number_format($vat, 2) . " GH₵</td></tr>
                                                <tr><td colspan='2'><hr></td></tr>
                                                <tr><td><strong>Total Tax Inclusive Value (i+ii+iii+iv)</strong></td><td align='right'><strong>" . number_format($subTotal + $nhil + $getFund + $vat, 2) . " GH₵</strong></td></tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                                <td colspan='2' style='padding-top: 2px;'>
                                                    <table style='width: 100%;'>
                                                        <tr><td>Gift Card</td><td align='right'>" . number_format($order_amount->gift_card, 2) . " GH₵</td></tr>";

            if ($order_amount->order_type == 1) {
                $adminEmailContent .= "
                                                        <tr><td>Delivery Fee</td><td align='right'>" . number_format($order_info->delivery_fee, 2) . " GH₵</td></tr>";
            }

            $finalTotal = $subTotal + $nhil + $getFund + $vat + $order_amount->gift_card;
            if ($order_amount->order_type == 1) {
                $finalTotal += $order_info->delivery_fee;
            }

            $adminEmailContent .= "
                                                        <tr><td colspan='2'><hr></td></tr>
                                                        <tr><td><strong>Total (paid by client taxes inclusive)</strong></td>
                                                        <td align='right'><strong>" . number_format($finalTotal, 2) . " GH₵</strong></td></tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </body>
                                    </html>";

            Mail::raw($adminEmailContent, function ($message) use ($adminEmail, $adminSubject) {
                $message->to($adminEmail)
                    ->subject($adminSubject)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')); // Ensure this is properly configured in your .env file
            });
        } catch (\Throwable $th) {
            // Log the error for debugging
            \Log::error('Email sending failed: ' . $th->getMessage());
            return response()->json(['success' => false, 'msg' => 'Something went wrong while sending email.']);
        }

        return response()->json(['success' => true, 'msg' => 'Emails sent successfully.']);
    }


    public function orderSuccess(Request $request, $id, $earnedpoints)
    {
        $orderData = Order::where('id', base64_decode($id))->first();
        return view('frontend.checkout.order-success', compact('orderData', 'earnedpoints'));
    }

    public function orderSuccessCard(Request $request, $userid, $orderid, $amount, $earnedpoints)
    {
        // echo "string";exit;
        $orderId = $orderid;
        $userId = $userid;
        $Amount = $amount;
        $earnedpoints = (int)($earnedpoints ?: 0);


        $orderData = Order::find($orderid);
        $orderData->status = 1;
        if ($orderData->save()) {
            try {
                $tranId = @$_GET['TransID'] ?: $this->geneateTranscationId();
                $transactions = Transactions::where('order_id', $orderId)->update(['status' => 1, 'payment_status' => 1]);

                $orderDetails = OrderDetails::where('order_id', $orderData->id)->get();

                if (!empty($orderDetails) && $orderData->is_stock_updated != 1) {
                    foreach ($orderDetails as $item) {
                        $existing = ProductVariants::find($item->variant_id);
                        $available_qty = $existing->available_qty - $item->quantity;
                        $sold_qty = $existing->sold_qty + $item->quantity;

                        $existing->available_qty = $available_qty;
                        $existing->sold_qty = $sold_qty;
                        $existing->save();
                    }
                    $updatStockStatus = Order::find($orderData->id);
                    $updatStockStatus->is_stock_updated = 1;
                    $updatStockStatus->save();

                    $userData = DB::table('main_users')->where('id', $userId)->first();
                    $title = "Online Order Confirm";
                    $message = "Online Order Confirm";
                    $remember_token = "fYPZqDsO90pgmZAzTKD0ow:APA91bEF6Pv3waTLBb8lRSUCrjz_M3Vxf14zF3IDHBckRoI79Ojw66aRbuDNhuHWT21qsPYwhOvXxGhILv-nJ0ZCNWzEEuo2tWQ1pDULgeBkPPoAbPaV6ulPJ0W1H8zhA2IcPn8zHl8P";
                    $device_token = $userData->device_token;
                    // echo "<pre>";print_r($device_token);exit();
                    $device_type = 1;

                    $notification = new Notification();
                    $notification->order_id = @$orderData->id;
                    $notification->sender_id = @$userId;
                    $notification->receiver_id = @$userId;
                    $notification->notification_type = 1;
                    $notification->title = @$title;
                    $notification->message = @$message;
                    $notification->is_read = 0;
                    $notification->save();


                    $admin_notification = new AdminNotifications();
                    $admin_notification->sender_id = @$userId;
                    $admin_notification->order_id = @$orderData->id;
                    $admin_notification->receiver_id = @$userId;
                    $admin_notification->notification_type = 1;
                    $admin_notification->title = @$title;
                    $admin_notification->message = @$message;
                    $admin_notification->is_read = 0;
                    $admin_notification->save();

                    $this->sendOrderConfirmationEmail($userId, $orderData);
                }

                // update cart status
                $updatepsw = Cart::where('user_id', $userId)->update(array('status' => 2));

                Session::forget('buy_now_info');
                Session::forget('checkout_in_progress');
                Session::forget('checkout_page_load_time');

                return view('frontend.checkout.order-success', compact('orderData', 'earnedpoints'));
            } catch (\Throwable $th) {
                abort(404);
            }
        }
    }


    public function SelectedAddress(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $user_id = $this->user_id;
        $address_id = $request->address_id;
        // dd($address_id);
        $updatepsw = UserAddress::where('user_id', $user_id)->update(array(
            'is_selected_address_id' => 0,
        ));

        $updateaddress = UserAddress::where('user_id', $user_id)->where('id', $address_id)->update(array(
            'is_selected_address_id' => 1,
        ));

        Alert::success(\Helper::language('success'), __('backend.address_change_successfully'));
        return response()
            ->json(['success' => 'true']);
    }

    public function storeSessionLat(Request $request)
    {
        // echo "<pre>";
        // print_r($request->lat);
        // exit();
        //  $current_lat = Session::set('current_lat', $request->lat);
        // $current_long = Session::set('current_long', $request->lng);
        \Session::put('current_lat', $request->lat);
        \Session::put('current_long', $request->lng);
        return response()
            ->json(['success' => true]);
    }

    public function getUserAreaTax(Request $request)
    {
        $user_id = $this->user_id;
        $user_address_id = $request->user_address_id;
        $coupon_code_id = $request->coupon_code_id;

        //For buy-now
        $is_buy_now = $request->is_buy_now;
        if ($is_buy_now == 1) {
            $variantIds = array($request->pvariant_Ids);
        } else {
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $user_id)->groupBy('user_id')->where('status', 1)->first();
            $variantIds = explode(',', $cartData->variantIds);
        }

        $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

        $area_tax_rate = 0;
        $min_purchase_amount = 0;
        $product_discounted_price = 0;
        $coupon_code_price = 0;
        $grand_total_amount = 0;
        $tax_amount = 0;
        $original_price = 0;
        $total_discount_price = 0;
        $is_product_discount[] =  false;
        foreach ($productData as $result) {
            if ($is_buy_now == 1) {
                $org_price = @$result->variant_price ? ($result->variant_price * $request->buy_quantity) : 0;
                $original_price += $org_price;
                $product_discounted_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * $request->buy_quantity) : 0;
            } else {
                $org_price = @$result->variant_price ? ($result->variant_price *  Helper::getUserCartQuantity($result->id, $user_id)) : 0;
                $original_price += $org_price;
                $product_discounted_price += @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id, $user_id)) : 0;
            }

            if ($result->variant_discounted_price != '' && $result->variant_discounted_price != 0.00) {
                $total_discount_price += ($org_price - $product_discounted_price);
                $is_product_discount[] = true;
            }
        }
        $total_products_price = @Helper::numberFormat($original_price);

        if (in_array(true, $is_product_discount)) {
            $grand_total_amount =  ($total_products_price -  $total_discount_price);
        } else {
            $grand_total_amount =  $total_products_price;
        }

        // $tempTotal=$grand_total_amount;

        // Condition for discounts(Note:- maybe add discount Details to calculate the final price here)
        $grand_total_amount = $request->currentTotal - ($request->previousDeliveryFee ?? 0);

        //Discount is apply only product price not with including delivery charege
        if (!empty($coupon_code_id)) {
            $promocode_data = Promocode::where('id', $coupon_code_id)->where('status', 1)->first();
            $coupon_code_price = ($promocode_data->discount_percentage / 100) * $grand_total_amount;
            $coupon_code_price = @Helper::numberFormat($coupon_code_price);
            $grand_total_amount = ($grand_total_amount - $coupon_code_price);
        }

        //Tax 
        if (Helper::Settings('tax') != 0) {
            $tax_amount =  @Helper::numberFormat((Helper::Settings('tax') / 100) * $grand_total_amount);
            $grand_total_amount = $grand_total_amount + $tax_amount;
        }

        if (!empty($user_address_id)) {
            $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();
            $area_tax_rate = @Helper::numberFormat($user_address_data->area->rate);
            $deliveryAmount = @Helper::numberFormat($user_address_data->area->delivery_amount);
            $deliveryFee = @Helper::numberFormat($user_address_data->area->delivery_fee);
            $min_purchase_amount = $deliveryAmount;

            // if($tempTotal > $deliveryAmount)
            if ($grand_total_amount > $deliveryAmount) {
                $area_tax_rate = 0;
            } else {
                $area_tax_rate = $deliveryFee;
            }

            $grand_total_amount = @Helper::numberFormat($grand_total_amount + $area_tax_rate);
        }

        return response()->json(['message' => '', 'coupon_code_price' => @Helper::numberFormat($coupon_code_price), 'delivery_fee' => @Helper::numberFormat($area_tax_rate), 'grand_total_amount' => @Helper::numberFormat($grand_total_amount), 'min_purchase_amount' => @Helper::numberFormat($min_purchase_amount),]);
    }

    public function applyCoupon(Request $request)
    {
        $user_id = $this->user_id;
        $code = $request->coupon_code;
        $user_address_id = $request->user_address_id;
        $purchase_type = $request->purchase_type;
        //For buy-now
        $is_buy_now = $request->is_buy_now;

        $promocode_data = Promocode::where('promo_name', $code)->where('status', 1)->first();
        $percentage = '';
        $code_id = '';
        $coupon_code_price = '';
        $area_tax_rate = '';
        $grand_total_amount = '';
        $tax_amount = 0;
        $couponPercentage = "";
        $tempTotal = $request->currentTotal;
        if ($promocode_data == "") {
            $msg = @Helper::language('Invalid_coupon_code');
        } elseif ($promocode_data->start_date >= Carbon::now()) {
            $msg =  @Helper::language('this_coupon_code_is_currently_not_active');
        } elseif (date('Y-m-d') > $promocode_data->end_date) {
            $msg =  @Helper::language('this_coupon_code_is_expired');
        } elseif ($tempTotal < $promocode_data->minimum_amount) {
            $difference = number_format($promocode_data->minimum_amount - $tempTotal, 2);
            return response()->json([
                'message' => "You need to add items worth at least $difference GH₵ more in your cart to apply this coupon.",
                'code_id' => '',
            ]);
        } else {
            // Coupon Limit check
            $userCouponUsage = \DB::table('coupon_user')
                ->where('user_id', $user_id)
                ->where('coupon_id', $promocode_data->id)
                ->sum('usage_count');

            if ($promocode_data->total_usage > 0 && $userCouponUsage >= $promocode_data->total_usage) {
                return response()->json([
                    'message' => 'You have reached the usage Limit for this coupon.',
                    'code_id' => '',
                ]);
            }


            $couponPercentage = $promocode_data->discount_percentage;
            $code_id = $promocode_data->id;
            $msg =  @Helper::language('coupon_code_apply_successfully');

            if ($is_buy_now == 1) {
                $variantIds = array($request->pvariant_Ids);
            } else {
                $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $user_id)->groupBy('user_id')
                    ->where('status', 1)
                    ->first();
                $variantIds = explode(',', $cartData->variantIds);
            }

            $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

            $product_discounted_price = 0;
            $original_price = 0;
            $total_discount_price = 0;
            $is_product_discount[] =  false;
            foreach ($productData as $result) {
                if ($is_buy_now == 1) {
                    $org_price = @$result->variant_price ? ($result->variant_price * $request->buy_quantity) : 0;
                    $original_price += $org_price;
                    $product_discounted_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * $request->buy_quantity) : 0;
                } else {
                    $org_price = @$result->variant_price ? ($result->variant_price *  Helper::getUserCartQuantity($result->id, $user_id)) : 0;
                    $original_price += $org_price;
                    $product_discounted_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id, $user_id)) : 0;
                }

                if ($result->variant_discounted_price != '' && $result->variant_discounted_price != 0.00) {
                    $total_discount_price += ($org_price - $product_discounted_price);
                    $is_product_discount[] = true;
                }
            }
            $total_products_price = @Helper::numberFormat($original_price);

            if (in_array(true, $is_product_discount)) {
                $grand_total_amount =  ($total_products_price -  $total_discount_price);
            } else {
                $grand_total_amount =  $total_products_price;
            }

            // added coupon on current amount
            $grand_total_amount = $request->currentTotal;

            $coupon_code_price = ($couponPercentage / 100) * $grand_total_amount;
            $coupon_code_price = @Helper::numberFormat($coupon_code_price);
            $grand_total_amount = @Helper::numberFormat($grand_total_amount - $coupon_code_price);

            if (Helper::Settings('tax') != 0) {
                $tax = (Helper::Settings('tax') / 100) * $grand_total_amount;
                $tax_amount =  @Helper::numberFormat($tax);
                $grand_total_amount = @Helper::numberFormat($grand_total_amount + $tax_amount);
            }

            if ($purchase_type == 1) {
                if (!empty($user_address_id)) {
                    $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();
                    $area_tax_rate = @Helper::numberFormat($user_address_data->area->rate);
                    $deliveryAmount = @Helper::numberFormat($user_address_data->area->delivery_amount);
                    $deliveryFee = @Helper::numberFormat($user_address_data->area->delivery_fee);

                    // if($tempTotal>$deliveryAmount)
                    if ($grand_total_amount > $deliveryAmount) {
                        $area_tax_rate = 0;
                    } else {
                        $area_tax_rate = $deliveryFee;
                    }

                    $grand_total_amount = @Helper::numberFormat($grand_total_amount + $area_tax_rate);
                }
            }
        }
        $data = ['message' => $msg, 'coupon_percentage' => $couponPercentage, 'coupon_code_price' => @Helper::numberFormat($coupon_code_price), 'delivery_fee' => @Helper::numberFormat($area_tax_rate), 'grand_total_amount' => @Helper::numberFormat($grand_total_amount), 'tax_amount' => @Helper::numberFormat($tax_amount), 'code_id' => $code_id];
        // dd($data);
        return response()->json($data);
    }

    public function applyReward(Request $request)
    {
        $user_id = $this->user_id;
        $reward = $request->reward_points;
        $user_address_id = $request->user_address_id;
        $purchase_type = $request->purchase_type;

        // Loyalty Points user has
        $loyaltyPoints = LoyaltyPoints::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Credit points: order must be delivered
                    $q->where('type', 'credit')
                        ->where('status', 1)
                        ->whereIn('order_id', function ($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                })->orWhere(function ($q) {
                    // Debit points: order must be delivered
                    $q->where('type', 'debit')
                        ->where('status', 1)
                        ->whereIn('order_id', function ($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPoints = 0;
        foreach ($loyaltyPoints as $point) {
            if ($point->type === 'credit') {
                $totalPoints += $point->points;
            } elseif ($point->type === 'debit') {
                $totalPoints -= $point->points;
            }
        }

        // Loyalty Data
        $loyaltyInfo = Loyalty::where('status', '=', '1')->first();

        $reward_id = '';
        $area_tax_rate = '';
        $grand_total_amount = '';
        $tax_amount = 0;
        $tempTotal = $request->currentTotal;

        if (!$loyaltyInfo) {
            return response()->json([
                'message' => "Loyalty program details are currently unavailable.",
                'reward_id' => '',
            ]);
        } elseif ($tempTotal < $loyaltyInfo->minimum_purchase_amount) {
            return response()->json([
                'message' => "Your order total must be at least GH₵{$loyaltyInfo->minimum_purchase_amount} to redeem reward points.",
                'reward_id' => '',
            ]);
        } elseif ($reward > $totalPoints) {
            return response()->json([
                'message' => "You do not have enough reward points to redeem this amount.",
                'reward_id' => '',
            ]);
        } else {
            // Calculate maximum redeemable amount
            $rewardPercentageAmount = (($loyaltyInfo->max_redeem_percentage) * $tempTotal) / 100;

            // Cap for usage
            $rewardPercentageAmount = min($rewardPercentageAmount, $loyaltyInfo->maximum_points);

            // Points to currency conversion rate
            $conversionRate = $loyaltyInfo ? $loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs : 0;

            if (($reward * $conversionRate) >  $rewardPercentageAmount) {
                return response()->json([
                    'message' => 'The reward amount exceeds the allowed redemption limit for this purchase.',
                    'reward_id' => '',
                ]);
            }

            $rewardAmount = @Helper::numberFormat($reward * $conversionRate);
            $reward_id = $loyaltyInfo->id;
            $msg = 'Reward points applied successfully';

            $grand_total_amount = $tempTotal;
            $grand_total_amount = @Helper::numberFormat($grand_total_amount - $rewardAmount);


            if (Helper::Settings('tax') != 0) {
                $tax = (Helper::Settings('tax') / 100) * $grand_total_amount;
                $tax_amount =  @Helper::numberFormat($tax);
                $grand_total_amount = @Helper::numberFormat($grand_total_amount + $tax_amount);
            }


            if ($purchase_type == 1) {
                if (!empty($user_address_id)) {
                    $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();
                    $area_tax_rate = @Helper::numberFormat($user_address_data->area->rate);
                    $deliveryAmount = @Helper::numberFormat($user_address_data->area->delivery_amount);
                    $deliveryFee = @Helper::numberFormat($user_address_data->area->delivery_fee);

                    // if($tempTotal>$deliveryAmount)
                    if ($grand_total_amount > $deliveryAmount) {
                        $area_tax_rate = 0;
                    } else {
                        $area_tax_rate = $deliveryFee;
                    }

                    $grand_total_amount = @Helper::numberFormat($grand_total_amount + $area_tax_rate);
                }
            }
        }

        $data = [
            'message' => $msg,
            'conversionRate' => $conversionRate,
            'rewardAmount' => @Helper::numberFormat($rewardAmount),
            'delivery_fee' => @Helper::numberFormat($area_tax_rate),
            'grand_total_amount' => @Helper::numberFormat($grand_total_amount),
            'tax_amount' => @Helper::numberFormat($tax_amount),
            'reward_id' => $reward_id,
            'reward' => $reward
        ];

        return response()->json($data);
    }

    public function storePlaceOrder(Request $request)
    {
        logger()->info("storeplaceorder");

        logger()->info($request->all());

        $user_id = $this->user_id;
        $user_address_id = $request->user_address_id;
        $user_bill_address_id = $request->user_bill_address_id;
        $payment_method = $request->payment_method;
        $purchase_type = $request->purchase_type;
        $delivery_charge = $request->delivery_charge;
        $store_location_id = $request->store_location_id;
        $coupon_code_id = $request->coupon_code_id;
        $coupon_percentage = $request->coupon_percentage;
        // Rewards
        $reward_id = $request->reward_id;
        $conversion_rate = $request->conversion_rate;
        $reward_points = $request->reward_points;

        $tax = $request->tax;
        $buy_org_price = $request->buy_org_price;
        $buy_discounted_price = $request->buy_discounted_price;
        $giftRecipient = $request->giftRecipient;
        $giftMessage = $request->giftMessage;
        $giftAmount = $request->giftAmount;
        $cartDiscountFlag = $request->discountFlag;
        $cartDiscountAmount = $request->discountAmount;
        $currentTotal = $request->currentTotal;
        // $shippingDetail = $request->shippingDetail;
        // $isSameAddress = $request->isSameAddress;
        $isSameAddress = filter_var($request->input('isSameAddress'), FILTER_VALIDATE_BOOLEAN);

        //If User apply promo code
        if (!empty($coupon_code_id)) {
            $promocode_data = Promocode::where('status', 1)->where('id', $coupon_code_id)->first();
            if (!empty($promocode_data)) {
                if ($promocode_data->discount_percentage != $coupon_percentage) {
                    return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'coupon']);
                }
            } else {
                return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'coupon']);
            }
        }

        // if user has applied Reward Points
        if (!empty($reward_id)) {
            $loyaltyInfo = Loyalty::where('status', 1)->where('id', $reward_id)->first();
            if (!empty($loyaltyInfo)) {
                if ($loyaltyInfo->id != $reward_id) {
                    return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'reward']);
                }
            } else {
                return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'reward']);
            }
        }

        $delivery_address = '';
        $user_address_data = null;

        if (!empty($user_address_id) && $purchase_type == 1) {
            $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();

            $status = $user_address_data->area->status;
            if ($status == 1) {
                // if ($user_address_data->area->rate != $delivery_charge) {
                //     return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong_in_delivery_fee'), 'type' => 'address']);
                // }
                $deliveryAmount = @Helper::numberFormat($user_address_data->area->delivery_amount);
                $deliveryFee = @Helper::numberFormat($user_address_data->area->delivery_fee);

                $currentTotal = floatval($currentTotal);
                // $expectedRate = floatval($user_address_data->area->rate);
                $givenRate = floatval($delivery_charge);

                if ($currentTotal > $deliveryAmount && $givenRate == 0) {
                    // Allow zero delivery charge
                } elseif ($givenRate != $deliveryFee) {
                    return response()->json([
                        'error' => true,
                        'message' => @Helper::language('something_went_wrong_in_delivery_fee'),
                        'type' => 'address'
                    ]);
                }
            } else {
                return response()->json(['error' => true, 'message' => @Helper::language('Something_went_wrong_in_the_delivery_address'), 'type' => 'address']);
            }
            $delivery_address = "";
            ($user_address_data->name) ? $delivery_address .= $user_address_data->name : '';
            ($user_address_data->address) ? $delivery_address .= ',| ' . $user_address_data->address : '';
            ($user_address_data->country->name) ? $delivery_address .= ',| ' . $user_address_data->country->name : '';
            ($user_address_data->region->title) ? $delivery_address .= ',| ' . $user_address_data->region->title : '';
            ($user_address_data->area->title) ? $delivery_address .= ',| ' . $user_address_data->area->title : '';
            ($user_address_data->phone) ? $delivery_address .= ',| +' . @$user_address_data->phonecode . ' ' . @$user_address_data->phone : '';
            ($user_address_data->city) ? $delivery_address .= ',| ' . @$user_address_data->city . ' ' . @$user_address_data->city : '';
            ($user_address_data->zip_code) ? $delivery_address .= ',| ' . @$user_address_data->zip_code . ' ' . @$user_address_data->zip_code : '';
        }

        // Billing Address
        $billing_address = '';
        if (!empty($user_bill_address_id) && $purchase_type == 1) {
            $user_bill_address_data = UserBillAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_bill_address_id]])->first();

            $billing_address = '';
            ($user_bill_address_data->name) ? $billing_address .= $user_bill_address_data->name : '';
            ($user_bill_address_data->address) ? $billing_address .= ',| ' . $user_bill_address_data->address : '';
            ($user_bill_address_data->country->name) ? $billing_address .= ',| ' . $user_bill_address_data->country->name : '';
            ($user_bill_address_data->region->title) ? $billing_address .= ',| ' . $user_bill_address_data->region->title : '';
            ($user_bill_address_data->area->title) ? $billing_address .= ',| ' . $user_bill_address_data->area->title : '';
            ($user_bill_address_data->phone) ? $billing_address .= ',| +' . @$user_bill_address_data->phonecode . ' ' . @$user_bill_address_data->phone : '';
            ($user_bill_address_data->city) ? $billing_address .= ',| ' . @$user_bill_address_data->city . ' ' . @$user_bill_address_data->city : '';
            ($user_bill_address_data->zip_code) ? $billing_address .= ',| ' . @$user_bill_address_data->zip_code . ' ' . @$user_bill_address_data->zip_code : '';
        }

        //tax
        if (!empty(Helper::Settings('tax')) && $tax != Helper::Settings('tax')) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'tax']);
        }

        $store_id = '';
        $store_address = '';
        if ($store_location_id != "" && $purchase_type == 2) {

            $store_data = DB::table('store_details')->where('id', $store_location_id)->where('status', 1)->first();
            if (empty($store_data)) {
                return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'store_inactive']);
            }
            $store_id = $store_data->id;
            $store_phone_number =

                $contactNumber = '';
            if ($store_data->phone_code) {
                $contactNumber .= '+' . $store_data->phone_code . ' ';
            }
            if ($store_data->contact_number) {
                $contactNumber .= $store_data->contact_number;
            }
            $store_address = $store_data->store_name . ',| ' . $store_data->address . ',| ' . $contactNumber;
        }

        //For buy-now
        $is_buy_now = $request->is_buy_now;
        if ($is_buy_now == 1) {
            $variantIds = array($request->pvariant_Ids);
        } else {
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $user_id)->groupBy('user_id')->where('status', 1)->first();
            if (empty($cartData)) {
                return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'cart']);
            }
            $variantIds = explode(',', $cartData->variantIds);
        }

        $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

        /// dd($productData);
        $pcount = count($productData);
        $vcount = count($variantIds);
        // dd($pcount,$vcount);
        if ($vcount != $pcount) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'product']);
        }

        $is_product_active = array();
        $check_stock = array();
        $is_product_price_change = array();
        foreach ($productData as $variant) {
            $cartEntry = $variant->cart->where('user_id', $user_id)->sortByDesc('id')->first();
            $bogoStatus = $cartEntry ? $cartEntry->is_bogo : false;

            if ($variant->get_product_details->status == 1 && $variant->get_product_details->get_category->status == 1 && $variant->get_product_details->get_subcategory->status == 1 && $variant->get_product_details->get_brand_details->status == 1) {
                $is_product_active[] =  false;
            }
            if ($is_buy_now == 1) {
                $cart_qty = $request->buy_quantity;
                $bogoStatus = (int) $request->bogo_status;

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                if ($variant->variant_price != $buy_org_price || $variant->variant_discounted_price != $buy_discounted_price) {
                    $is_product_price_change[] = true;
                }
            } else {
                $cart_qty = Helper::getUserCartQuantity($variant->id, $user_id);

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                $cart_info = Cart::where(['product_variant_id' => $variant->id, 'user_id' => $user_id, 'status' => 1, 'order_type' => 1])->first();
                if ($variant->variant_price != $cart_info->product_price || $cart_info->offer_price != $variant->variant_discounted_price) {
                    $is_product_price_change[] = true;
                }
            }

            //This for both cart & buy now
            if ($variant->available_qty < $cart_qty) {
                $check_stock[] = true;
            }
        }

        if (in_array('true', $is_product_price_change)) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'product_price']);
        }

        if (in_array('true', $check_stock)) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'stock']);
        }

        if (in_array('false', $is_product_active)) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'product_s']);
        }

        $userData = \DB::table('main_users')->where('id', $user_id)->where('status', 1)->first();
        ///dd($userData);
        $order_uid = uniqid();
        $order = new Order();
        $order->uniqid = $order_uid;
        $order->user_id = $user_id;
        $order->supplier_id = $store_id;
        $order->order_id = $this->geneateOrderId();
        $order->delivery_address = $delivery_address;

        if ($isSameAddress) {
            $order->billing_address = $delivery_address;
            $order->isSameAddress = 1;
        } else {
            $order->billing_address = $billing_address;
            $order->isSameAddress = 0;
        }

        $order->order_type = $purchase_type;
        date_default_timezone_set('Africa/Accra');
        $order->order_date = date('Y-m-d H:i:s');
        $order->order_time = date('Y-m-d H:i:s');
        $order->order_status = 1; //Pending
        if ($payment_method == 1) {
            $order->status = 2;
        } else {
            $order->status = 1;
        }

        // Gift message
        $order->recipientName = $giftRecipient;
        $order->giftMessage = $giftMessage;
        // $order->shipping_method=$shippingDetail;

        // Delivery instruction
        // $order->delivery_instructions = $user_address_data->delivery_instructions;
        // $order->delivery_options = $user_address_data->delivery_options;
        if ($user_address_data) {
            $order->delivery_instructions = $user_address_data->delivery_instructions;
            $order->delivery_options = $user_address_data->delivery_options;
        }

        $order->save();

        //updating the delivery instruction in user_address 
        if ($user_address_data) {
            $user_address_data->delivery_instructions = null;
            $user_address_data->delivery_options = null;
            $user_address_data->save();
        }


        $tracking_id = uniqid();
        $order_tracking = new OrderTracking();
        $order_tracking->order_id = $order->id;
        $order_tracking->uniqid = $tracking_id;
        $order_tracking->order_status = 1;
        $order_tracking->status = 1;
        $order_tracking->save();

        $order_info = new OrderInfo();
        $order_info->order_id = $order->id;
        if ($purchase_type == 1) {
            $order_info->country_id = $user_address_data->country_id;
            $order_info->region_id = $user_address_data->region_id;
            $order_info->area_id = $user_address_data->area_id;
        }
        $order_info->customer_name = $userData->first_name . ' ' . $userData->last_name;
        $order_info->customer_mobile = $userData->phone_code . ' ' . $userData->phone;
        $order_info->customer_email = $userData->email;
        $order_info->customer_country = ($userData->country_code) ?: '';
        $order_info->order_from = '1';
        if ($purchase_type == 2) {
            $order_info->store_pickup_address = $store_address;
        }
        $order_info->save();

        $original_price = 0;
        $total_discount_price = 0;
        $product_discount_price = 0;
        $is_product_discount[] =  false;
        $offer_type_store = '';
        $discount_amount_store = 0;
        foreach ($productData as $result) {
            $cartEntry = $result->cart->where('user_id', $user_id)->sortByDesc('id')->first();
            $bogoStatus = $cartEntry ? $cartEntry->is_bogo : false;

            if ($is_buy_now == 1) {
                $cart_qty = $request->buy_quantity;
                $bogoStatus = (int) $request->bogo_status;
                $is_offer = (int) $request->offer_status;
                $offer_type = (string) $request->offer_type;
                $discount_amount = (float) $request->discount_amount;
                $offer_type_store = $offer_type;
                $discount_amount_store = $discount_amount;

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                $variant_orginal_price = $buy_org_price;
                //using for variant new price

                if ($is_offer) {
                    if ($offer_type == 'flat') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($discount_amount));
                    } elseif ($offer_type == 'percentage') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($variant_orginal_price * $discount_amount / 100));
                    }
                } else {
                    $variant_offer_price = '0';
                }
            } else {
                $cart_qty = Helper::getUserCartQuantity($result->id, $user_id);

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                //using for variant new price
                $cart_info = Cart::where(['product_variant_id' => $result->id, 'user_id' => $user_id, 'status' => 1, 'order_type' => 1])->first();
                $variant_orginal_price = $cart_info->product_price;
                $offer_type_store = $cart_info->offer_type;
                $discount_amount_store = $cart_info->discount_amount;

                if ($cart_info->is_offer) {
                    if ($cart_info->offer_type == 'flat') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($cart_info->discount_amount));
                    } elseif ($cart_info->offer_type == 'percentage') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($variant_orginal_price * $cart_info->discount_amount / 100));
                    }
                } else {
                    $variant_offer_price = '0';
                }
            }

            $product_unit = Helper::getUnitById($result->variant_uof);
            //$product_total_price = ($cart_qty * $result->variant_discounted_price);
            $order_detail = new OrderDetails();
            $order_detail->order_id = @$order->id;
            $order_detail->product_id = $result->get_product_details->id;
            $order_detail->variant_id = @$result->id;
            $order_detail->customer_id = $user_id;
            $order_detail->product_original_amount = $variant_orginal_price;
            $order_detail->product_total_amount = $variant_offer_price;
            $order_detail->quantity = $cart_qty;
            $order_detail->variant_size = $result->variant_size;
            $order_detail->variant_unit = $result->variant_uof;
            $order_detail->status = 1;
            $order_detail->is_bogo = (int) $bogoStatus;
            $order_detail->save();

            // $org_price = @$result->variant_price ? ($result->variant_price * $cart_qty) : 0;
            // $original_price += $org_price;      
            // $product_discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * $cart_qty) : 0;
            // if($result->variant_discounted_price !='' && $result->variant_discounted_price != 0.00){ 
            //     $total_discount_price += ($org_price - $product_discount_price);
            //     $is_product_discount[] = true;
            // }

            $cart_qty_for_price = $cart_qty;
            if ($bogoStatus) {
                $cart_qty_for_price = floor($cart_qty / 2);
            }

            $org_price = @$variant_orginal_price ? ($variant_orginal_price * $cart_qty_for_price) : 0;
            $original_price += $org_price;
            $product_discount_price = @$variant_offer_price ? ($variant_offer_price * $cart_qty_for_price) : 0;
            if ($variant_offer_price  != '' && $variant_offer_price != '0') {
                $total_discount_price += ($org_price - $product_discount_price);
                $is_product_discount[] = true;
            }

            if ($payment_method == 3) {
                $available_qty = $result->available_qty - $cart_qty;
                $sold_qty = $result->sold_qty + $cart_qty;

                ProductVariants::where('id', $result->id)->update(array('available_qty' => $available_qty, 'sold_qty' => $sold_qty));
            }
        }


        $total_products_price = $original_price;
        if (in_array(true, $is_product_discount)) {
            $product_sub_total_amount = ($total_products_price -  $total_discount_price);
            $grand_total_amount =  ($total_products_price -  $total_discount_price);
        } else {
            $product_sub_total_amount = $total_products_price;
            $grand_total_amount =  $total_products_price;
        }


        // Cart Discount
        if ($cartDiscountFlag == 1) {
            $grand_total_amount = $grand_total_amount - $cartDiscountAmount;
        }

        $coupon_code_price = '';
        $promo_title = '';
        $couponPercentage = '';

        if (!empty($promocode_data)) {
            $promo_title = $promocode_data->promo_name;
            $couponPercentage = $promocode_data->discount_percentage;
            $coupon_code_price = @Helper::numberFormat(($couponPercentage / 100) * $grand_total_amount);
            $sub_total_amount = ($grand_total_amount - $coupon_code_price);
            OrderInfo::where('order_id', $order->id)
                ->update(array(
                    'promocode_name' => $promo_title,
                    'promocode_percentage' => $couponPercentage
                ));
            $grand_total_amount = $sub_total_amount;


            // insert Data in coupon_user table
            \DB::table('coupon_user')->insert([
                'user_id' => $user_id,
                'coupon_id' => $coupon_code_id,
                'order_id' => $order->id,
                'usage_count' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Reward Points Used
        $reward_price = 0;
        if (!empty($loyaltyInfo)) {
            $reward_price = $conversion_rate * $reward_points;
            $sub_total_amount = ($grand_total_amount - $reward_price);

            OrderInfo::where('order_id', $order->id)
                ->update(array(
                    'reward_amount' => $reward_price
                ));

            $grand_total_amount = $sub_total_amount;

            $loyalty_id = DB::table('loyalty_points')->insertGetId([
                'user_id'    => $user_id,
                'order_id'   => $order->order_id,
                'order_ref_id'   => $order->id,
                'points'     => $reward_points,
                'type'       => 'debit',
                'status'     => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Order::where('user_id', $user_id)->where('id', $order->id)->update([
                'loyalty_debit_point_id' => $loyalty_id
            ]);
        }

        // Gift Amount added
        if ($giftAmount) {
            $grand_total_amount = $grand_total_amount + $giftAmount;
        }


        //Tax 
        if (Helper::Settings('tax') != 0 && $tax != "") {
            $tax  = ($tax / 100) * $grand_total_amount;
            $tax_amount =  @Helper::numberFormat($tax);
            Order::where('id', $order->id)->update(array('tax' => $tax_amount));
            $grand_total_amount = @Helper::numberFormat($grand_total_amount + $tax_amount);
        }

        if (!empty($user_address_id) && $purchase_type == 1) {
            $area_tax_rate = @Helper::numberFormat($user_address_data->area->rate);
            $deliveryAmount = @Helper::numberFormat($user_address_data->area->delivery_amount);
            $deliveryFee = @Helper::numberFormat($user_address_data->area->delivery_fee);

            if ($grand_total_amount > $deliveryAmount) {
                $area_tax_rate = 0;
            } else {
                $area_tax_rate = $deliveryFee;
            }

            $grand_total_amount = @Helper::numberFormat($grand_total_amount + $area_tax_rate);
            OrderInfo::where('order_id', $order->id)->update(array('delivery_fee' => $area_tax_rate));
        }

        $transactions = new Transactions();
        $transactions->trans_no = $this->geneateTranscationId();
        $transactions->user_id = $user_id;
        $transactions->order_id = @$order->id;
        $transactions->payment_type = $payment_method;
        $transactions->payment_status = $payment_method == 1 ? "" : '1';
        $transactions->amount = $grand_total_amount;
        $transactions->transaction_date = date('Y-m-d H:i:s');
        if ($payment_method == 1) {
            $transactions->status = 2;
        } else {
            $transactions->status = 1;
        }
        $transactions->save();


        //  $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
        // $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);
        //300+30 = 330

        //dd($product_sub_total_amount);

        $updatepsw = Order::where('user_id', $user_id)->where('id', $order->id)
            ->update(array(
                'total_amount' => $product_sub_total_amount,
                'payable_amount' => $grand_total_amount,
                'discount_amount' => $coupon_code_price,
                'cart_discount' => $cartDiscountFlag == 1 ? $cartDiscountAmount : 0,
                'gift_card' => !empty($giftAmount) ? $giftAmount : 0,
            ));


        // Store offer used details
        if (!empty($offer_type_store) && $discount_amount_store != 0) {
            $offerData = Offers::where('offer_type', $offer_type_store)
                ->where('dis_amount', $discount_amount_store)
                ->where('status', 1)
                ->first();

            \DB::table('offer_user')->insert([
                'user_id' => $user_id,
                'offer_id' => $offerData->id,
                'order_id' => $order->id,
                'usage_count' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // reward Points
        $loyaltySetting = DB::table('loyalty')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->first();

        $earnedpoints = 0;
        if ($loyaltySetting && $grand_total_amount >= $loyaltySetting->minimum_purchase_amount) {
            $percentage = $loyaltySetting->loyalty_percentage;

            // Calculate points
            $awardedPoints = round(($grand_total_amount * $percentage) / 100, 2);
            // $earnedpoints = min($awardedPoints, $loyaltySetting->maximum_points);
            $earnedpoints = $awardedPoints;
            // Save points
            $loyalty_id = DB::table('loyalty_points')->insertGetId([
                'user_id'    => $user_id,
                'order_id'   => $order->order_id,
                'order_ref_id'   => $order->id,
                'points'     => $earnedpoints,
                'type'       => 'credit',
                'status'     => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Order::where('user_id', $user_id)->where('id', $order->id)->update([
                'loyalty_credit_point_id' => $loyalty_id
            ]);
        }

        if ($payment_method == 1) {
            $grand_total_amount = $grand_total_amount;
            $redirectUrl = route('orderSuccessCard', ['userid' => $user_id, 'orderid' => $order->id, 'amount' => $grand_total_amount, 'earnedpoints' => $earnedpoints]);
            $backUrl = url('/callBackUrl');
            $xmlPayload = '<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>4CF16A78-27EA-47A7-B1D4-6E52343C8DC1</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>' . $grand_total_amount . '</PaymentAmount><PaymentCurrency>GHS</PaymentCurrency><CompanyRef>49FKEOA</CompanyRef><RedirectURL>' . $redirectUrl . '</RedirectURL><BackURL>' . $backUrl . '</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL>
            </Transaction><Services><Service><ServiceType>87197</ServiceType><ServiceDescription>Food And Beverages</ServiceDescription><ServiceDate>' . date('Y-m-d H:i:s') . '</ServiceDate></Service></Services>
            </API3G>';

            $getToken = \Helper::createToken($xmlPayload, $grand_total_amount, $redirectUrl, $backUrl, $user_id, $user_address_id, $order->id);
            logger()->info("+++++++++++++++++++++++++++checkout - createToken+++++++++++++++++++++++");
            logger()->info($getToken);

            logger()->info("-----------------------------------------");
            $result = json_decode(json_encode($getToken), true);

            if (@$result['original']['error']) {
                return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'payment_error']);
            }

            if (@$result['original']['token']) {
                // if ($is_buy_now != 1) {
                //     $updatepsw = Cart::where('user_id', $user_id)->update(array('status' => 2));
                // }
                $token = $result['original']['token'][0];
                $paymentUrl = 'https://secure.3gdirectpay.com/payv3.php?ID=' . $token;
                return response()->json(['success' => true, 'redirect_url' => $paymentUrl]);
            }
        }

        $userData = DB::table('main_users')->where('id', $user_id)->first();
        $title = "Online Order Confirm";
        $message = "Online Order Confirm";
        $remember_token = "fYPZqDsO90pgmZAzTKD0ow:APA91bEF6Pv3waTLBb8lRSUCrjz_M3Vxf14zF3IDHBckRoI79Ojw66aRbuDNhuHWT21qsPYwhOvXxGhILv-nJ0ZCNWzEEuo2tWQ1pDULgeBkPPoAbPaV6ulPJ0W1H8zhA2IcPn8zHl8P";
        $device_token = $userData->device_token;
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

        $admin_notification = new AdminNotifications();
        $admin_notification->order_id = @$order->id;
        $admin_notification->sender_id = @$user_id;
        $admin_notification->receiver_id = @$user_id;
        $admin_notification->notification_type = 1;
        $admin_notification->title = @$title;
        $admin_notification->message = @$message;
        $admin_notification->is_read = 0;
        $admin_notification->save();

        // send confirmation mail
        $this->sendOrderConfirmationEmail($user_id, $order);

        if ($is_buy_now != 1) {
            $updatepsw = Cart::where('user_id', $user_id)->update(array('status' => 2));
        }

        Session::forget('buy_now_info');
        Session::forget('checkout_in_progress');
        Session::forget('checkout_page_load_time');

        return response()->json(['success' => true, 'order_id' => Helper::encodeUrl($order->id), 'earnedpoints' => $earnedpoints]);
    }

    public function callBackUrl()
    {
        $TransactionToken = request('TransactionToken');
        $response = \Helper::TransactionStatus($TransactionToken);

        logger()->info("-------------------Callback response----------------------");
        logger()->info($response);

        // Find order by token
        $transaction = TransactionTokens::where('transaction_token', $TransactionToken)->first();

        logger()->info("..................transaction........................................");
        logger()->info($transaction);

        if (!$transaction) {
            return response()->json(['error' => true, 'message' => 'Transaction not found']);
        }

        $order_id = $transaction->order_id;

        if ($response['success'] && $response['status'] === 'APPROVED') {
            echo "<pre>Transaction Success</pre>";
            Transactions::where('order_id', $order_id)->update(['payment_status' => '1']);
        } else {
            echo "<pre>Transaction Failed</pre>";
            Transactions::where('order_id', $order_id)->update(['payment_status' => '2']);
        }

        // return response()->json($response);
        return redirect('cart');
    }
}
