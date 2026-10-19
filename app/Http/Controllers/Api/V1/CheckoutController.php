<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\Label;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\EmailTemplate;
use App\Models\UserAddress;
use App\Models\UserBillAddress;
use App\Models\Order;
use App\Models\OrderInfo;
use App\Models\OrderTracking;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Transactions;
use App\Models\UsersPayments;
use App\Models\Notification;
use App\Models\ProductVariants;
use App\Models\Promocode;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\AdminNotifications;
use App\Models\Discount;
use App\Models\LoyaltyPoints;
use App\Models\Loyalty;
use App\Events\NewOrderPlaced;
use App\Models\Offers;
use Mail;
use Hash;
use Helper;
use Auth;
use DB;
use Carbon\Carbon;
use App\Models\TransactionTokens;


class CheckoutController extends Controller
{
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

    // public function sendOrderConfirmationEmail($user_id, $order)
    // {
    //         $order_details = OrderDetails::where('order_id', $order->id)->get(); // Get all order details
    //         $transactions = Transactions::where('order_id', $order->id)->first();
    //         $order_info = OrderInfo::where('order_id', $order->id)->first();
    //         $storePickupAddress = $order_info->store_pickup_address ?? 'Not provided';

    //         $customerDetails = DB::table('main_users')->find($user_id);
    //         $setting = Setting::find(1);
    //         $templateId = 17; // Determine the appropriate template ID
    //         $emailDetail = EmailTemplate::find($templateId);
    //         $fromEmail = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com'); 
    //         $sendEmail = $customerDetails->email;

    //         // Prepare email data
    //         $data = [
    //             'user_name' => $customerDetails->first_name,
    //             'sendname' => $customerDetails->first_name,
    //             'sendemail' => $customerDetails->email,
    //             'id' => $templateId,
    //             'order' => $order,
    //             'order_status' => "Place Order",
    //             'from_email' => config('mail.from.address'),
    //             'store_pickup_address' => $storePickupAddress,
    //         ];

    //         $order_amount = Order::with('transcations')->where('id', $order->id)->first();

    //         try {
    //             // Send email to customer
    //             Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data,  $emailDetail) {
    //                 $message->to($data['sendemail'], 'Liquor')->subject($emailDetail->subject);
    //                 $message->from($data['from_email'], $emailDetail->title);
    //             });

    //             // Send email to admin
    //             $adminEmail = 'info@liquorjunctionghana.com';
    //             $adminSubject = 'New Order Received';

    //             if ($order->order_type == 1) {
    //                             $order_type_name = "Online Order";
    //                         } elseif ($order->order_type == 2) {
    //                             $order_type_name = "In-store";
    //                         } elseif ($order->order_type == 3) {
    //                             $order_type_name = "Purchase Order";
    //                         } else {
    //                             $order_type_name = "Unknown Order Type";
    //                         }

    //                         if ($transactions->payment_type == 1) {
    //                             $payment_type_name = "Momo Pay";
    //                         } elseif ($transactions->payment_type == 2) {
    //                             $payment_type_name = "Card (Debit/Credit)";
    //                         } elseif ($transactions->payment_type == 3) {
    //                             $payment_type_name = "Cash on Delivery";
    //                         } else {
    //                             $payment_type_name = "Unknown Payment Type";
    //                         }

    //                         if ($order_info) {
    //                             if ($order_info->order_from == 1) {
    //                                 $order_type_from = 'Web';
    //                             } elseif ($order_info->order_from == 2) {
    //                                 $order_type_from = 'Android';
    //                             } elseif ($order_info->order_from == 3) {
    //                                 $order_type_from = 'iOS';
    //                             } else {
    //                                 $order_type_from = 'Unknown Source';
    //                             }
    //                         } else {
    //                             $order_type_from = 'Unknown Source';
    //                         }
    //             // Initialize the admin email content with the basic details
    //             $adminEmailContent = "
    //         <html>
    //         <head>
    //             <style>
    //                 body { font-family: Arial, sans-serif; }
    //                 .container { width: 100%; padding: 20px; }
    //                 .content { background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
    //                 .header { font-size: 18px; font-weight: bold; margin-bottom: 90px; }
    //                 .item { margin-bottom: 10px; display: flex; }
    //                 .label { font-weight: bold; width: 150px; }
    //                 .value { margin-left: 10px; flex: 1; }
    //                 .table { width: 100%; border-collapse: collapse;}
    //                 .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //                 .table th { background-color: #f2f2f2; }
    //                 .total-row { font-weight: bold; }
    //                 .amount-cell { text-align: right; }
    //                 br { display: none; } 
    //             </style>
    //         </head>
    //         <body>
    //         <div class='container'>
    //             <div class='content'>
    //                 <div class='header'><b>A new order has been received.</b></div>
    //                 <div class='item'><span class='label'><b>Customer Details:</b></span></div>
    //                 <div class='item'><span class='label'>Name:</span>  <span class='value'>{$customerDetails->first_name} {$customerDetails->last_name}</span></div>
    //                 <div class='item'><span class='label'>Email:</span>  <span class='value'>{$customerDetails->email}</span></div>
    //                 <div class='item'><span class='label'>Phone:</span>  <span class='value'>+{$customerDetails->phone_code}{$customerDetails->phone}</span></div>";

    //             // Conditionally display the delivery address
    //             if (!empty($storePickupAddress) && $storePickupAddress !== 'Not provided') {
    //                 $adminEmailContent .= "
    //                 <div class='item'><span class='label'><b>Store Address:</b></span>  <span class='value'>{$storePickupAddress}</span></div>";
    //             } else {
    //                 $adminEmailContent .= "
    //                 <div class='item'><span class='label'>Delivery Address:</span>  <span class='value'>{$order->delivery_address}</span></div>";
    //             }

    //             $adminEmailContent .= "
    //                 <div class='item'><span class='label'><b>Order Details:</b></span></div>
    //                 <div class='item'><span class='label'>Order ID:</span>  <span class='value'>{$order->order_id}</span></div>
    //                 <div class='item'><span class='label'>Order Type:</span>  <span class='value'>{$order_type_name}</span></div>
    //                 <div class='item'><span class='label'>Order From:</span>  <span class='value'>{$order_type_from}</span></div>
    //                 <div class='item'><span class='label'>Order Date:</span>  <span class='value'>{$order->order_date}</span></div>
    //                 <div class='item'><span class='label'>Order Time:</span>  <span class='value'>{$order->order_time}</span></div>
    //                 <div class='item'><span class='label'>Delivery Status:</span>  <span class='value'>" . ($order->order_status == 1 ? 'Pending' : 'Other') . "</span></div>
    //                 <div class='item'><span class='label'>Payment Method:</span>  <span class='value'>{$payment_type_name}</span></div>
    //                 <div class='item' style='margin-bottom: 0;'>
    //                     <span class='label'><b>Product Details:</b></span>
    //             </div>
    //                 <table class='table'>
    //                     <tr>
    //                         <th style='padding: 5px;'>Product Name</th>
    //                         <th style='padding: 5px;'>Product Size</th>
    //                         <th style='padding: 5px;'>Qty</th>
    //                         <th style='padding: 5px;' class='amount-cell'>Amount</th>
    //                         <th style='padding: 5px;' class='amount-cell'>Total</th>
    //                     </tr>";

    //             // Loop through each product in the order
    //             foreach ($order_details as $order_detail) {
    //                 $product = Product::where('id', $order_detail->product_id)->first();
    //                 $total_amount = $order_detail->quantity * $order_detail->product_original_amount;

    //                 $adminEmailContent .= "
    //                 <tr>
    //                     <td style='padding: 10px;'>{$product->product_name}</td>
    //                     <td style='padding: 10px;'>{$order_detail->variant_size}ML</td>
    //                     <td style='padding: 15px;'>{$order_detail->quantity}</td>
    //                     <td style='padding: 10px;' class='amount-cell'>{$order_detail->product_original_amount} GH₵</td>
    //                     <td style='padding: 10px;' class='amount-cell'>{$total_amount} GH₵</td>
    //                 </tr>";
    //             }

    //             // Add subtotal, discount, tax, delivery fee, and total amount
    //             $adminEmailContent .= "
    //             <tr class='total-row'>
    //                 <td style='padding: 10px;' colspan='7'>Sub Amount</td>
    //                 <td style='padding: 10px;' class='amount-cell'>{$order_amount->total_amount} GH₵</td>
    //             </tr>
    //             <tr class='total-row'>
    //                         <td style='padding: 10px;' colspan='7'>Discount Amount</td>
    //                         <td style='padding: 10px;' class='amount-cell'>- {$order_amount->discount_amount} GH₵ ({$order_info->promocode_name})</td>
    //                     </tr>
    //                     <tr class='total-row'>
    //                         <td style='padding: 10px;' colspan='7'>Tax</td>
    //                         <td style='padding: 10px;' class='amount-cell'>{$order_amount->tax} GH₵</td>
    //                     </tr>
    //                     <tr class='total-row'>
    //                         <td style='padding: 10px;' colspan='7'>Delivery Fee</td>
    //                         <td style='padding: 10px;' class='amount-cell'>{$order_info->delivery_fee} GH₵</td>
    //                     </tr>
    //                     <tr class='total-row'>
    //                         <td style='padding: 10px;' colspan='7'>Total Amount</td>
    //                         <td style='padding: 10px;' class='amount-cell'>{$order_amount->payable_amount} GH₵</td>
    //                     </tr>
    //                         </table>
    //                     </div>
    //                 </div>
    //             </body>
    //             </html>";


    //             Mail::raw($adminEmailContent, function ($message) use ($adminEmail, $adminSubject) {
    //                 $message->to($adminEmail)
    //                     ->subject($adminSubject)
    //                     ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')); // Ensure this is properly configured in your .env file
    //             });

    //         } catch (\Throwable $th) {
    //             // Log the error for debugging
    //             \Log::error('Email sending failed: ' . $th->getMessage());
    //             return response()->json(['success' => false, 'msg' => 'Something went wrong while sending email.']);
    //         }

    // }

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
        $subTotal = ($totalAmount - $totalDiscount) / 1.219;

        $covidLevy = ($taxes[0]->tax_value / 100) * ($subTotal);
        $nhil = ($taxes[1]->tax_value / 100) * ($subTotal);
        $getFund = ($taxes[2]->tax_value / 100) * ($subTotal);
        $vat = ($taxes[3]->tax_value / 100) * ($subTotal + $covidLevy + $nhil + $getFund);

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
                $payment_type_name = "Momo Pay";
            } elseif ($transactions->payment_type == 2) {
                $payment_type_name = "Card (Debit/Credit)";
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
                                                <tr><td>(iv) Covid-19 Levy (1%)</td><td align='right'>" . number_format($covidLevy, 2) . " GH₵</td></tr>
                                                <tr><td>(v) Total Levy Inclusive Value (i + ii + iii + iv)</td><td align='right'>" . number_format($subTotal + $nhil + $getFund + $covidLevy, 2) . " GH₵</td></tr>
                                                <tr><td>(vi) VAT 15% of (v)</td><td align='right'>" . number_format($vat, 2) . " GH₵</td></tr>
                                                <tr><td colspan='2'><hr></td></tr>
                                                <tr><td><strong>Total Tax Inclusive Value (v + vi)</strong></td><td align='right'><strong>" . number_format($subTotal + $nhil + $getFund + $covidLevy + $vat, 2) . " GH₵</strong></td></tr>
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

            $finalTotal = $subTotal + $nhil + $getFund + $covidLevy + $vat + $order_amount->gift_card;
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
    }


    public function order_success_email($user_name, $sendname, $sendemail, $order, $order_status)
    {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('17');
        // echo "<pre>";print_r($setting->toArray());exit();
        // $from_email = $setting['from_email'];
        $data = array('user_name' => $user_name, 'sendname' => $sendname, 'sendemail' => $sendemail, 'order' => $order, 'order_status' => $order_status, 'id' => '17', 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data) {

            $message->to($data['sendemail'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }



    public function applyCouponCodeById($id, $type, $tempTotal = null, $user_id = null)
    {

        if ($id != "") {
            if ($type == "2") {
                $promocode_data = Promocode::where('promo_name', $id)->where('status', 1)->first();
            } else {
                $promocode_data = Promocode::where('id', $id)->where('status', 1)->first();
            }

            if ($promocode_data == "") {
                $response = [
                    'code'  => strval(0),
                    'message' => 'invalid_coupon_code'
                ];
            } elseif ($promocode_data->start_date >= Carbon::now()) {
                $response = [
                    'code'  => strval(0),
                    'message' => 'coupon_code_currently_not_active'
                ];
            } elseif (date('Y-m-d') > $promocode_data->end_date) {
                $response = [
                    'code'  => strval(0),
                    'message' => 'coupon_code_expired'
                ];
            } elseif (!is_null($tempTotal) && $tempTotal < $promocode_data->minimum_amount) {
                $difference = number_format($promocode_data->minimum_amount - $tempTotal, 2);
                $response = [
                    'code'  => strval(0),
                    'message' => 'You need to add items worth at least ' . $difference . 'GH₵ more in your cart to apply this coupon.'
                ];
            } else {

                if ($user_id) {
                    $userCouponUsage = \DB::table('coupon_user')
                        ->where('user_id', $user_id)
                        ->where('coupon_id', $promocode_data->id)
                        ->sum('usage_count');

                    if ($promocode_data->total_usage > 0 && $userCouponUsage >= $promocode_data->total_usage) {
                        return [
                            'code'  => strval(0),
                            'message' => 'You have reached the usage limit for this coupon.'
                        ];
                    }
                }

                $couponPercentage = $promocode_data->discount_percentage;
                $couponName = $promocode_data->promo_name;
                $code_id = $promocode_data->id;

                // extra Calculation
                $coupon_code_price = "";
                if ($tempTotal) {
                    $coupon_code_price = ($couponPercentage / 100) * $tempTotal;
                    $coupon_code_price = round($coupon_code_price, 2);
                }

                $response = [
                    'code'  => strval(1),
                    'percentage' =>  strval($couponPercentage),
                    'coupon_id' =>  strval($code_id),
                    'coupon_discount_amount' => strval($coupon_code_price),
                    // 'message' => 'coupon_code_applied_successfully',
                    'title' => $couponName . ' Applied!',
                    'message' => 'Hurray! You saved ' . $coupon_code_price . 'GH₵ from this coupon.',
                    'coupon_message' => 'You just saved ' . $coupon_code_price . 'GH₵ on your order'
                ];
            }
        } else {
            $response = [
                'code'  => strval(0),
                'message' => 'not_use'
            ];
        }
        return $response;
    }

    public function appliedCoupon(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'coupon_code' => 'required',
            'uniqid' => 'required',
            'grand_total_amount' => 'required',
            'delivery_charge' => 'required',
            'gift_amount' => 'required',
            'bogoCheck' => 'required',
            'offerCheck' => 'required',
            'cart_discount_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }


        $bogoCheck = filter_var($request->bogoCheck, FILTER_VALIDATE_BOOLEAN);
        $offerCheck = filter_var($request->offerCheck, FILTER_VALIDATE_BOOLEAN);
        $cartDiscountCheck = filter_var($request->cart_discount_status, FILTER_VALIDATE_BOOLEAN);

        if ($bogoCheck) {
            return response()->json([
                'code' => '0',
                'message' => 'An existing offer is already applied to your cart. Promo codes cannot be used with other offers.'
            ], 200);
        }

        if ($offerCheck) {
            return response()->json([
                'code' => '0',
                'message' => 'An existing offer is already applied to your cart. Promo codes cannot be used with other offers.'
            ], 200);
        }


        if ($cartDiscountCheck) {
            return response()->json([
                'code' => '0',
                'message' => 'A discount is already applied to your cart. You cannot apply a promo code.'
            ], 200);
        }



        $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
        if (!$userData) {
            return response()->json([
                'code' => '0',
                'message' => 'Invalid user.'
            ], 200);
        }
        $user_id = $userData->id;

        $coupon_code = $request->coupon_code;
        $total_amount = (float) $request->grand_total_amount;
        $delivery_charge = (float) $request->delivery_charge;
        $gift_amount = (float) $request->gift_amount;

        $total_amount = $total_amount - $delivery_charge - $gift_amount;

        $coupon_info = $this->applyCouponCodeById($coupon_code, '2', $total_amount, $user_id);

        $mainResult   =   $coupon_info;
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }

    public function applyRewardByID($reward = null, $tempTotal = null, $user_id = null)
    {
        if ($reward) {
            $loyaltyInfo = Loyalty::where('status', '=', '1')->first();

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

            if (!$loyaltyInfo) {
                $response = [
                    'code'  => strval(0),
                    'message' => 'Loyalty program details are currently unavailable.'
                ];
            } elseif ($tempTotal < $loyaltyInfo->minimum_purchase_amount) {
                $response = [
                    'code'  => strval(0),
                    'message' => 'Your order total must be at least GH₵' . $loyaltyInfo->minimum_purchase_amount . ' to redeem reward points.'
                ];
            } elseif (!is_null($reward) && $reward > $totalPoints) {
                $response = [
                    'code'  => strval(0),
                    'message' => 'You do not have enough reward points to redeem this amount'
                ];
            } else {

                // Calculate maximum redeemable amount
                $rewardPercentageAmount = (($loyaltyInfo->max_redeem_percentage) * $tempTotal) / 100;

                // Cap for usage
                $rewardPercentageAmount = min($rewardPercentageAmount, $loyaltyInfo->maximum_points);

                // Points to currency conversion rate
                $conversionRate = ($loyaltyInfo && $loyaltyInfo->points_per_ghs > 0)
                    ? $loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs
                    : 0;

                if (($reward * $conversionRate) >  $rewardPercentageAmount) {
                    return [
                        'code'  => strval(0),
                        'message' => 'The reward amount exceeds the allowed redemption limit for this purchase.'
                    ];
                }

                $rewardAmount = round($reward * $conversionRate, 2);

                $response = [
                    'code'  => strval(1),
                    'reward' =>  strval($reward),
                    'reward_id' =>  strval($loyaltyInfo->id),
                    'rewardAmount' => strval($rewardAmount),
                    'conversionRate' => strval($conversionRate),
                    'message' => 'Reward points applied successfully'
                ];
            }
        } else {
            $response = [
                'code'  => strval(0),
                'message' => 'not_use'
            ];
        }
        return $response;
    }


    public function appliedReward(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'reward_points' => 'required',
            'uniqid' => 'required',
            'grand_total_amount' => 'required',
            'delivery_charge' => 'required',
            'gift_amount' => 'required',
            'cart_discount_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $cartDiscountCheck = filter_var($request->cart_discount_status, FILTER_VALIDATE_BOOLEAN);

        if ($cartDiscountCheck) {
            return response()->json([
                'code' => '2',
                'message' => 'A discount is already applied to your cart. You cannot apply reward Points.'
            ], 200);
        }

        $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
        if (!$userData) {
            return response()->json([
                'code' => '0',
                'message' => 'Invalid user.'
            ], 200);
        }
        $user_id = $userData->id;

        $reward = (int) $request->reward_points;

        if ($reward <= 0) {
            return response()->json([
                'code' => '0',
                'message' => 'Reward points must be greater than 0.'
            ], 200);
        }

        $total_amount = (float) $request->grand_total_amount;
        $delivery_charge = (float) $request->delivery_charge;
        $gift_amount = (float) $request->gift_amount;

        $total_amount = $total_amount - $delivery_charge - $gift_amount;

        $reward_info = $this->applyRewardByID($reward, $total_amount, $user_id);

        $mainResult   =   $reward_info;
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }

    // public function checkout(Request $request)
    // {

    //     $result = [];
    //     $finalArr = [];
    //     $validator = \Validator::make($request->all(), [
    //         'purchase_order_type' => 'required',
    //         'purchase_type' => 'required',
    //         'uniqid' => 'required',
    //         'token' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'code' => strval(0),
    //             'error' => $validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }

    //     $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
    //     if ($response['code'] != 1) {
    //         $mainResult   =   $response;
    //         return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    //     $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
    //     $user_id = $userData->id;

    //     $purchase_type = $request->purchase_type;
    //     $coupon_id = $request->coupon_id;
    //     $language = $request->language;
    //     $store_id =  $request->store_id;
    //     $user_address_id = $request->user_address_id;
    //     $purchase_order_type = $request->purchase_order_type;

    //     $is_buy_now = $request->is_buy_now;

    //     if (!empty($user_address_id) && $purchase_order_type == 1) {
    //         $validator = \Validator::make($request->all(), [
    //             'user_address_id' => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => strval(0),
    //                 'error' => $validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }
    //     }

    //     if (!empty($store_id) && $purchase_order_type == 2) {
    //         $validator = \Validator::make($request->all(), [
    //             'store_id' => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => strval(0),
    //                 'error' => $validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }
    //     }
    //     //1 for cart data
    //     if ($is_buy_now == 0) {
    //         $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
    //         $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $userData->id)->groupBy('user_id')->where('status', 1)->first();



    //         if (empty($cartData)) {
    //             $result['code']     =  strval(1);
    //             $result['message']  =  'no_data_found';
    //             $result['result']   =  NUll;

    //             $mainResult   =   $result;
    //             return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }
    //         $variantIds = explode(',', $cartData->variantIds);
    //     }

    //     if ($is_buy_now == 1) {
    //         $validator = \Validator::make($request->all(), [
    //             'buy_product_variant_id' => 'required',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => strval(0),
    //                 'error' => $validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }
    //         $variantIds = array($request->buy_product_variant_id);
    //     }

    //     // $productData = ProductVariants::getProductDetalsBasedOnVariant()->whereIn('id', $variantIds)->get();
    //             $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();



    //     //             logger()->info("+++++++++++++++++++++++++++ productData+++++++++++++++++++++++");
    //     // logger()->info($productData);

    //     // logger()->info("-----------------------------------------");



    //     if (!empty($productData)) {
    //         $productDataArr = array();
    //         $org_price = 0;
    //         $total_discount_price = 0;
    //         $is_product_discount = [];
    //         foreach ($productData as $data) {
    //             if ($is_buy_now == 1) {
    //                 $variantIds = $request->buy_product_variant_id;
    //                 $get_qty = $request->buy_quantity;
    //             } else {
    //                 $cartInfo = Cart::select('*')->where(['user_id' => $userData->id, 'product_variant_id' => $data->id])->where('status', 1)->first();
    //                 $get_qty = $cartInfo->quantity;
    //             }

    //             if ($request->language == 1) {
    //                 $title = ($data->get_product_details->product_name_fr) ? $data->get_product_details->product_name_fr : $data->get_product_details->product_name;
    //             } else {
    //                 $title = $data->get_product_details->product_name ?: '';
    //             }
    //             $product_image = $data->get_product_details->get_product_images->first();
    //             //$product_variant = $data->get_product_details->get_product_variants->first();

    //             $product_unit = Helper::getUnitById($data->variant_uof);
    //             // $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
    //             $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '';
    //             if (file_exists(public_path() . '/uploads/product/' . $product_image->image)) {
    //                 $image_path =  asset('uploads/product/' . $product_image->image);
    //             } else {
    //                 $image_path =  asset('assets/frontend/images/image-not-avilable.png');
    //             }

    //             $original_price = ($data->variant_price) ? $data->variant_price * $get_qty : 0;
    //             $org_price += $original_price;

    //             // if ($data->variant_discounted_price != '' && $data->variant_discounted_price != 0.00) {
    //             //     $is_product_discount[] = true;
    //             //     $discounted_price  = $data->variant_discounted_price  * $get_qty;
    //             //     $total_discount_price += $original_price - $discounted_price;
    //             // }
    //           $discounted_price = ($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00 && $data->variant_discounted_price!=NULL)? $data->variant_discounted_price :$data->variant_price;
    //     if ($discounted_price != '' && $discounted_price != 0.00) {
    //         $is_product_discount[] = true;
    //         $discounted_price  = $discounted_price  * $get_qty;
    //         $total_discount_price += $original_price - $discounted_price;
    //     }
    //             if ($purchase_type == 1) {
    //                 $cart_id =  strval(@$cartInfo->id);
    //             } else {
    //                 $cart_id = NULL;
    //             }

    //             $productDataArr['cart_list'][] = [
    //                 'product_id' => strval(@$data->get_product_details->id),
    //                 'product_title' => strval(@$title),
    //                 'product_size' => strval(@$product_size),
    //                 'product_image' => $image_path,
    //                 'product_orignal_price' => strval(@$original_price),
    //                 'product_discounted_price' => strval(@$discounted_price),
    //                 'cart_id' => $cart_id,
    //                 'variant_id' => strval(@$data->id),
    //                 'product_qty' => $get_qty,
    //             ];
    //         }


    //         $total_products_price = $org_price;
    //         if (in_array(true, $is_product_discount)) {
    //             $final_amount = ($total_products_price - $total_discount_price);
    //         } else {
    //             $final_amount =  $total_products_price;
    //         }

    //         $productDataArr['coupon_discount_amount'] = NULL;
    //         if ($this->applyCouponCodeById($coupon_id, '1')) {
    //             $coupon_info = $this->applyCouponCodeById($coupon_id, '1');

    //             if (isset($coupon_info['percentage'])) {
    //                 $coupon_percentage = $coupon_info['percentage'];
    //                 $coupon_code_price = ($coupon_percentage / 100) * $final_amount;
    //                 $coupon_code_price = round($coupon_code_price, 2);
    //                 $final_amount = round(($final_amount - $coupon_code_price), 2);
    //                 $productDataArr['coupon_discount_amount'] = strval(@$coupon_code_price);
    //             }
    //             $productDataArr['coupon_status'] =  $coupon_info;
    //         }

    //         $tax_amount = 0;
    //         $tax_percentage = 0;
    //         if (Helper::Settings('tax') != 0) {
    //             $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
    //             $tax_percentage = Helper::Settings('tax');
    //             $total_amount = $final_amount + $tax_amount;
    //         } else {
    //             $total_amount = $final_amount;
    //         }

    //         if (!empty($user_address_id) && $purchase_order_type == 1) {
    //             $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();
    //             $area_tax_rate = round($user_address_data->area->rate, 2);
    //             $total_amount = round($total_amount + $area_tax_rate, 2);
    //             $productDataArr['delivery_charge'] = strval(@$area_tax_rate);
    //         }


    //         $productDataArr['total_orignal_price'] = strval(@$org_price);
    //         $productDataArr['total_discount_price'] = strval(@$total_discount_price);
    //         $productDataArr['tax_percentage'] = strval(@$tax_percentage);
    //         $productDataArr['tax_amount'] = strval(@$tax_amount);
    //         $productDataArr['grand_total_amount'] = strval(@$total_amount);


    //         $result['code']     =    strval(1);
    //         $result['message']  =   'success';
    //         $result['result']   =   $productDataArr;
    //         $mainResult   =   $result;
    //         return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     } else {
    //         $result['code']     =  strval(0);
    //         $result['message']  =  'no_data_found';
    //         $result['result']   =  [];

    //         $mainResult   =   $result;
    //         return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    // }


    public function checkout(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'purchase_order_type' => 'required',
            'purchase_type' => 'required',
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
        $user_id = $userData->id;

        $purchase_type = $request->purchase_type;
        $coupon_id = $request->coupon_id;
        if (empty($coupon_id) || strtolower($coupon_id) === 'null') {
            $coupon_id = null;
        }
        $reward = (float) $request->reward;
        $language = $request->language;
        $store_id =  $request->store_id;
        $user_address_id = $request->user_address_id;
        $purchase_order_type = $request->purchase_order_type;

        $is_buy_now = $request->is_buy_now;

        // Check the flow of discount
        $session = DB::table('checkout_sessions')->where('user_id', $user_id)->first();

        if (!$session) {
            DB::table('checkout_sessions')->insert([
                'user_id' => $user_id,
                'apply_order' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $session = DB::table('checkout_sessions')->where('user_id', $user_id)->first();
        }


        if (empty($coupon_id) && $reward > 0) {
            $applyOrder = 'reward_first';
        } elseif (!empty($coupon_id) && $reward <= 0) {
            $applyOrder = 'coupon_first';
        } elseif (!empty($coupon_id) && $reward > 0) {
            // Keep previous apply order if already set
            $applyOrder = $session->apply_order ?? 'coupon_first';
        } else {
            $applyOrder = null;
        }

        DB::table('checkout_sessions')->where('user_id', $user_id)->update([
            'apply_order' => $applyOrder,
            'updated_at' => now(),
        ]);

        // Fallback if still null
        $applyOrder = $applyOrder ?? 'coupon_first';

        if (!empty($user_address_id) && $purchase_order_type == 1) {
            $validator = \Validator::make($request->all(), [
                'user_address_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
        }

        if (!empty($store_id) && $purchase_order_type == 2) {
            $validator = \Validator::make($request->all(), [
                'store_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
        }
        //1 for cart data
        if ($is_buy_now == 0) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $userData->id)->groupBy('user_id')->where('status', 1)->first();

            if (empty($cartData)) {
                $result['code']     =  strval(1);
                $result['message']  =  'no_data_found';
                $result['result']   =  NUll;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            $variantIds = explode(',', $cartData->variantIds);
        }

        if ($is_buy_now == 1) {
            $validator = \Validator::make($request->all(), [
                'buy_product_variant_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
            $variantIds = array($request->buy_product_variant_id);
        }

        $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

        // Cart Discount Details
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


        if (!empty($productData)) {
            $productDataArr = array();
            $org_price = 0;
            $total_discount_price = 0;
            $product_discount_price = 0;
            $is_product_discount = false;
            $freeItems = [];
            $offerCheck = false;
            $bogoCheck = false;
            $discountFlag = false;
            $discountTotal = 0;
            $total_quantity = 0;
            foreach ($productData as $data) {
                if ($is_buy_now == 1) {
                    $variantIds = $request->buy_product_variant_id;
                    $get_qty = $request->buy_quantity;
                    // $is_bogo = $request->is_bogo ?? 0;
                    // $is_offer = $request->is_offer ?? 0;
                    // $discount_amount = $request->discount_amount ?? null;
                    // $offer_type = $request->offer_type ?? null;

                    $is_offer = (int) filter_var($request->offer_status, FILTER_VALIDATE_BOOLEAN) ?? 0;
                    $is_bogo  = (int) filter_var($request->bogo_status, FILTER_VALIDATE_BOOLEAN) ?? 0;

                    $currentOffer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->first();
                    $offer_type = null;
                    $discount_amount = null;

                    if ($currentOffer && $is_offer) {
                        $offer_type = $currentOffer->offer_type;
                        $discount_amount = $currentOffer->dis_amount;
                    }
                } else {
                    $cartInfo = Cart::select('*')->where(['user_id' => $userData->id, 'product_variant_id' => $data->id])->where('status', 1)->first();
                    $get_qty = $cartInfo->quantity;

                    // $discount_cart = $data->cart->where('user_id', $user_id)->sortByDesc('id')->first();
                    // $is_bogo = $discount_cart ? $discount_cart->is_bogo : 0;
                    // $is_offer = $discount_cart ? $discount_cart->is_offer : 0;
                    // $discount_amount = $discount_cart ? $discount_cart->discount_amount : null;
                    // $offer_type = $discount_cart ? $discount_cart->offer_type : null;

                    $is_bogo =  $cartInfo->is_bogo ?? 0;
                    $is_offer =  $cartInfo->is_offer ?? 0;
                    $discount_amount =  $cartInfo->discount_amount ?? null;
                    $offer_type =  $cartInfo->offer_type ?? null;
                }

                if ($request->language == 1) {
                    $title = ($data->get_product_details->product_name_fr) ? $data->get_product_details->product_name_fr : $data->get_product_details->product_name;
                } else {
                    $title = $data->get_product_details->product_name ?: '';
                }
                $product_image = $data->get_product_details->get_product_images->first();

                $product_unit = Helper::getUnitById($data->variant_uof);
                $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '';
                if (file_exists(public_path() . '/uploads/product/' . $product_image->image)) {
                    $image_path =  asset('uploads/product/' . $product_image->image);
                } else {
                    $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                }

                $display_price = ($data->variant_price) ? $data->variant_price : 0;
                $display_discount_price = $display_price;
                $display_discount_amount = $discount_amount;

                // Apply offer discount for 1 unit
                if (!$is_bogo && $is_offer) {
                    $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                    if ($offer_type == 'flat') {
                        $display_discount_price = max(0, $display_price - $discount_amount);
                    } elseif ($offer_type == 'percentage') {
                        $display_discount_price = max(0, $display_price - ($display_price * $discount_amount / 100));
                        $display_discount_amount = max(0, $display_price * $formatted_discount / 100);
                    }
                } elseif ($data->variant_discounted_price && $data->variant_discounted_price != 0) {
                    $display_discount_price = $data->variant_discounted_price;
                }


                $original_price = ($data->variant_price) ? $data->variant_price * $get_qty : 0;
                // $org_price += $original_price;
                $discount_price = $original_price;

                // ✅ Apply offer or variant discount
                if (!$is_bogo && $is_offer) {
                    $offerCheck = true;
                    if ($offer_type == 'flat') {
                        $discount_price = max(0, $original_price - ($discount_amount * $get_qty));
                    } elseif ($offer_type == 'percentage') {
                        $discount_price = max(0, $original_price - ($original_price * $discount_amount / 100));
                    }
                    $is_product_discount = true;
                } elseif ($data->variant_discounted_price && $data->variant_discounted_price != 0) {
                    $discount_price = $data->variant_discounted_price * $get_qty;
                    $is_product_discount = true;
                }

                // ✅ Accumulate totals
                $org_price += $original_price;
                $product_discount_price += $discount_price;
                $total_discount_price += ($original_price - $discount_price);

                if ($purchase_type == 1) {
                    $cart_id =  strval(@$cartInfo->id);
                } else {
                    $cart_id = NULL;
                }

                $item_quantity = $get_qty;
                if ($is_bogo) {
                    $item_quantity = $get_qty * 2;
                }
                $total_quantity += $item_quantity;

                if ($is_bogo) {
                    $bogoCheck = true;
                    $freeItems[] = [
                        'product_id' => strval($data->get_product_details->id),
                        'product_title' => strval($title),
                        'variant_id' => strval($data->id),
                        'product_size' => strval($product_size),
                        'product_image' => $image_path,
                        'product_qty' => $get_qty,
                        'cart_id' => $cart_id,
                        'product_orignal_price' => '0',
                        'product_discounted_price' => '0',
                    ];
                }

                $productDataArr['cart_list'][] = [
                    'product_id' => strval(@$data->get_product_details->id),
                    'product_title' => strval(@$title),
                    'product_size' => strval(@$product_size),
                    'product_image' => $image_path,
                    // 'product_orignal_price' => strval(@$original_price),
                    // 'product_discounted_price' => strval(@$discount_price),
                    'product_orignal_price' => strval(@$display_price),
                    'product_discounted_price' => strval(@$display_discount_price),
                    'cart_id' => $cart_id,
                    'variant_id' => strval(@$data->id),
                    'product_qty' => $get_qty,
                    'bogo_status' => (bool) $is_bogo,
                    'offer_status' => (bool) $is_offer,
                    'discount_amount' => strval($display_discount_amount),
                    'offer_type' => $offer_type
                ];
            }


            // Final amount calculation
            $final_amount = $org_price - $total_discount_price;

            // Cart Discount Check
            $finalPrice = 0;
            if (isset($discountDetails['minimum_amount'], $discountDetails['discount_type'], $discountDetails['discount_value']) && empty($freeItems) && !$offerCheck) {
                $min = $discountDetails['minimum_amount'];
                $upto = $discountDetails['upto_amount'];
                $type = $discountDetails['discount_type'];
                $value = $discountDetails['discount_value'];

                if ($product_discount_price >= $min) {
                    $discountFlag = true;

                    if ($type == 'flat') {
                        $discountTotal = $value;
                    } elseif ($type == 'percentage') {
                        $rawDiscount = ($value / 100) * $product_discount_price;
                        $discountTotal = $rawDiscount <= $upto ? $rawDiscount : $upto;
                    }
                }

                $finalPrice = $product_discount_price - $discountTotal;
                // After cart amount deduction
                $final_amount = $finalPrice;
            }

            $amountAfterFirstDiscount = $final_amount;

            // Order
            if ($applyOrder === 'reward_first') {
                // 1. Apply Reward
                if (!$discountFlag && $reward > 0) {
                    $productDataArr['rewardAmount'] = NULL;
                    $reward_info = $this->applyRewardByID($reward, $amountAfterFirstDiscount, $user_id);

                    if (isset($reward_info['rewardAmount']) && !empty($reward_info) && $reward_info['code'] == '1') {
                        $rewardAmount = $reward_info['rewardAmount'] ?? 0;
                        $amountAfterFirstDiscount = round($amountAfterFirstDiscount - $rewardAmount, 2);

                        $productDataArr['rewardAmount'] = strval($rewardAmount);
                    }
                    $productDataArr['reward_status'] = $reward_info;
                }

                //2. Apply Coupon
                if (!$discountFlag && !$bogoCheck && !$offerCheck && !empty($coupon_id)) {
                    $productDataArr['coupon_discount_amount'] = NULL;
                    $coupon_info = $this->applyCouponCodeById($coupon_id, '1', $amountAfterFirstDiscount, $user_id);

                    if (isset($coupon_info['percentage']) && $coupon_info['code'] == '1') {
                        $coupon_percentage = $coupon_info['percentage'];
                        $couponAmount = isset($coupon_info['percentage']) ? round(($coupon_info['percentage'] / 100) * $amountAfterFirstDiscount, 2) : 0;
                        $amountAfterFirstDiscount = round($amountAfterFirstDiscount - $couponAmount, 2);
                        $productDataArr['coupon_discount_amount'] = strval($couponAmount);
                    }

                    $productDataArr['coupon_status'] = $coupon_info;
                }
            } else {
                // Default → coupon first
                if (!$discountFlag && !$bogoCheck && !$offerCheck && !empty($coupon_id)) {
                    $productDataArr['coupon_discount_amount'] = NULL;
                    $coupon_info = $this->applyCouponCodeById($coupon_id, '1', $amountAfterFirstDiscount, $user_id);

                    if (isset($coupon_info['percentage']) && $coupon_info['code'] == '1') {
                        $couponAmount = isset($coupon_info['percentage']) ? round(($coupon_info['percentage'] / 100) * $amountAfterFirstDiscount, 2) : 0;
                        $amountAfterFirstDiscount = round($amountAfterFirstDiscount - $couponAmount, 2);
                        $productDataArr['coupon_discount_amount'] = strval($couponAmount);
                    }

                    $productDataArr['coupon_status'] = $coupon_info;
                }

                if (!$discountFlag && $reward > 0) {
                    $productDataArr['rewardAmount'] = NULL;
                    $reward_info = $this->applyRewardByID($reward, $final_amount, $user_id);

                    if (isset($reward_info['rewardAmount']) && !empty($reward_info) && $reward_info['code'] == '1') {
                        $rewardAmount = $reward_info['rewardAmount'] ?? 0;
                        $amountAfterFirstDiscount = round($amountAfterFirstDiscount - $rewardAmount, 2);
                        $productDataArr['rewardAmount'] = strval($rewardAmount);
                    }
                    $productDataArr['reward_status'] = $reward_info;
                }
            }

            $final_amount = $amountAfterFirstDiscount;

            $tax_amount = 0;
            $tax_percentage = 0;
            if (Helper::Settings('tax') != 0) {
                $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
                $tax_percentage = Helper::Settings('tax');
                $total_amount = $final_amount + $tax_amount;
            } else {
                $total_amount = $final_amount;
            }

            if (!empty($user_address_id) && $purchase_order_type == 1) {
                $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();
                $area_tax_rate = round($user_address_data->area->rate, 2);
                $deliveryAmount = round($user_address_data->area->delivery_amount, 2);
                $deliveryFee = round($user_address_data->area->delivery_fee, 2);

                if ($total_amount > $deliveryAmount) {
                    $area_tax_rate = 0;
                } else {
                    $area_tax_rate = $deliveryFee;
                }

                $total_amount = round($total_amount + $area_tax_rate, 2);
                $productDataArr['delivery_charge'] = strval(@$area_tax_rate);

                $DeliveryMessage = '';
                if ($area_tax_rate == 0) {
                    $DeliveryMessage = 'Yay! free delivery';
                }
                $productDataArr['delivery_message'] = $DeliveryMessage;
            }


            // Loyalty Info
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
            // $conversionRate = number_format($loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs, 3, '.', '');
            if ($loyaltyInfo) {
                $conversionRate = number_format($loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs, 3, '.', '');
            } else {
                $conversionRate = 0;
            }

            $productDataArr['total_orignal_price'] = strval(@$org_price);
            $productDataArr['total_quantity'] = $total_quantity;
            $productDataArr['total_discount_price'] = strval(@$total_discount_price);
            $productDataArr['tax_percentage'] = strval(@$tax_percentage);
            $productDataArr['tax_amount'] = strval(@$tax_amount);
            $productDataArr['grand_total_amount'] = strval(@$total_amount);
            $productDataArr['freeItems'] = $freeItems;
            $productDataArr['cart_discount_status'] = $discountFlag;
            $productDataArr['cart_discount_amount'] =  strval($discountTotal);
            $productDataArr['totalPoints'] =  strval($totalPoints);
            $productDataArr['conversionRate'] =  strval($conversionRate);
            $productDataArr['bogoCheck'] = (bool) $bogoCheck;
            $productDataArr['offerCheck'] = (bool) $offerCheck;

            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr;
            $mainResult   =   $result;


            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {
            $result['code']     =  strval(0);
            $result['message']  =  'no_data_found';
            $result['result']   =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }


    //If admin change the price and user are going to place order after error price will updated
    public function updateCartProductPriceByVariantIds($variantIds, $user_id)
    {
        if ($variantIds != "") {
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
    }

    public function placeOrder(Request $request)
    {


        logger()->info("#################################placeOrder - CheckoutControlrer #################################");
        logger()->info($request->all());
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'purchase_order_type' => 'required',
            'purchase_type' => 'required',
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
        $user_id = $userData->id;

        $purchase_type = $request->purchase_type;
        $coupon_id = $request->coupon_id;
        $coupon_percentage = $request->coupon_percentage;
        $language = $request->language;
        $store_id =  $request->store_id;
        $user_address_id = $request->user_address_id;
        $user_bill_address_id = $request->user_bill_address_id;
        $purchase_order_type = $request->purchase_order_type;
        $payment_method = $request->payment_method;
        $delivery_charge =  (float) $request->delivery_charge;
        $tax = $request->purchase_tax_percentage;
        $buy_org_price = $request->buy_org_price;
        $buy_discounted_price = $request->buy_discounted_price;
        $buy_quantity = $request->buy_quantity;
        $offer_status = (int) filter_var($request->offer_status, FILTER_VALIDATE_BOOLEAN);
        $bogo_status  = (int) filter_var($request->bogo_status, FILTER_VALIDATE_BOOLEAN);

        //************************** */ get Offer Details
        $today = Carbon::now()->toDateString();
        $currentOffer = Offers::where('status', 1)
            ->whereDate('expiry_date', '>=', $today)
            ->first();
        $offer_type = null;
        $discount_amount = null;

        if ($currentOffer) {
            $offer_type = $currentOffer->offer_type;
            $discount_amount = $currentOffer->dis_amount;
        }
        // *******************************************************

        $giftRecipient = $request->giftRecipient;
        $giftMessage = $request->giftMessage;
        $giftAmount = (float) $request->giftAmount;
        $cartDiscountFlag = (int) $request->discountFlag;
        $cartDiscountAmount = (float) $request->discountAmount;
        $currentTotal =  (float) $request->currentTotal;
        // $coupon_discount_amount = (float) $request->coupon_discount_amount;  
        $coupon_discount_amount = 0;

        // Calculate the Coupon Discount Amount
        $tempTotal = $currentTotal - $giftAmount - $delivery_charge;
        if ($tempTotal) {
            $coupon_per = floatval($coupon_percentage);

            $coupon_code_price = ($coupon_per / 100) * $tempTotal;
            $coupon_discount_amount = round($coupon_code_price, 2);
        }

        // Rewards
        $reward_id = $request->reward_id;
        $conversion_rate = (float) $request->conversion_rate;
        $reward_points = (float) $request->reward_points;

        // currentTotal
        $currentTotal = $currentTotal - $giftAmount - $delivery_charge + $coupon_discount_amount;

        $order_from = $request->order_from ? $request->order_from : 2;
        $is_buy_now = $request->is_buy_now;
        $isSameAddress = filter_var($request->input('isSameAddress'), FILTER_VALIDATE_BOOLEAN);

        $user_address_data = null;
        $delivery_address = '';
        $billing_address = '';
        if (!empty($user_address_id) && $purchase_order_type == 1) {
            $validator = \Validator::make($request->all(), [
                'user_address_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
            $user_address_data = UserAddress::with(['country', 'region', 'area'])->where([['user_id', $user_id], ['status', 1], ['id', $user_address_id]])->first();

            logger()->info('user_address_data: ' . json_encode($user_address_data));


            if ($user_address_data->area->status == 1) {
                // if ($user_address_data->area->rate != $delivery_charge) {

                //     $result['code']     =  strval(0);
                //     $result['message']  =  'something_went_wrong';
                //     $result['error_type']  =  'address';
                //     $result['result']   =  NULL;

                //     $mainResult   =   $result;
                //     return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                // }
                $deliveryAmount = round($user_address_data->area->delivery_amount, 2);
                $deliveryFee = round($user_address_data->area->delivery_fee, 2);

                $currentTotal = floatval($currentTotal);
                $givenRate = floatval($delivery_charge);

                if ($currentTotal > $deliveryAmount && $givenRate == 0) {
                    // Allow zero delivery charge
                } elseif ($givenRate != $deliveryFee) {
                    $result['code']     =  strval(0);
                    $result['message']  =  'something_went_wrong';
                    $result['error_type']  =  'address';
                    $result['result']   =  NULL;

                    $mainResult   =   $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
            } else {
                $result['code']     =  strval(0);
                $result['message']  =  'something_went_wrong_in_the_delivery_address';
                $result['error_type']  =  'address';
                $result['result']   =  NULL;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            $delivery_address = "";
            ($user_address_data->name) ? $delivery_address .= $user_address_data->name : '';
            ($user_address_data->address) ? $delivery_address .= ',| ' . $user_address_data->address : '';
            ($user_address_data->country->name) ? $delivery_address .= ',| ' . $user_address_data->country->name : '';
            ($user_address_data->region->title) ? $delivery_address .= ',| ' . $user_address_data->region->title : '';
            ($user_address_data->area->title) ? $delivery_address .= ',| ' . $user_address_data->area->title : '';
            ($user_address_data->phone) ? $delivery_address .= ',| +' . $user_address_data->phonecode . ' ' . $user_address_data->phone : '';
        }

        if (!empty($user_bill_address_id) && $purchase_order_type == 1) {
            $validator = \Validator::make($request->all(), [
                'user_bill_address_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
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

        logger()->info('billing_address: ' . json_encode($billing_address));


        $store_address = '';
        if (!empty($store_id) && $purchase_order_type == 2) {
            $validator = \Validator::make($request->all(), [
                'store_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }

            if ($store_id != "") {
                $store_data = DB::table('store_details')->where('id', $store_id)->where('status', 1)->first();
                if (empty($store_data)) {
                    $result['code']     =  strval(0);
                    $result['message']  =  'something_went_wrong';
                    $result['error_type']  =  'store_inactive';
                    $result['result']   =  NULL;

                    $mainResult   =   $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
                $store_id = $store_data->id;
                $store_address = $store_data->store_name . ',| ' . $store_data->address;
            }
        } else {
            $store_id = "";
        }
        if (!empty(Helper::Settings('tax')) && $tax != Helper::Settings('tax')) {
            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  =  'tax_percentage';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if (!empty($coupon_id)) {
            $promocode_data = Promocode::where('status', 1)->where('id', $coupon_id)->first();

            if (empty($promocode_data) || $promocode_data->discount_percentage != $coupon_percentage) {

                $result['code']     =  strval(0);
                $result['message']  =  'something_went_wrong';
                $result['error_type']  =  'coupon';
                $result['result']   =  NULL;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }

        if (!empty($reward_id)) {
            $loyaltyInfo = Loyalty::where('status', 1)->where('id', $reward_id)->first();

            if (empty($loyaltyInfo) || $loyaltyInfo->id != $reward_id) {

                $result['code']     =  strval(0);
                $result['message']  =  'something_went_wrong';
                $result['error_type']  =  'reward';
                $result['result']   =  NULL;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }

        //1 for cart data
        if ($is_buy_now == 0) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $userData->id)->groupBy('user_id')->where('status', 1)->first();

            if (empty($cartData)) {
                $result['code']     =  strval(1);
                $result['message']  =  'no_data_found';
                $result['result']   =  NUll;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            $variantIds = explode(',', $cartData->variantIds);
        }
        if ($is_buy_now == 1) {
            $validator = \Validator::make($request->all(), [
                'buy_product_variant_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
            $variantIds = array($request->buy_product_variant_id);
        }

        // $productData = ProductVariants::getProductDetalsBasedOnVariant()->whereIn('id', $variantIds)->get();
        $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

        $pcount = count($productData);
        $vcount = count($variantIds);

        if ($vcount != $pcount) {
            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  =  'product';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
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
                $cart_qty = $buy_quantity;
                $bogoStatus = $bogo_status;


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

                $cart_info = Cart::where(['product_variant_id' => $variant->id, 'user_id' => $user_id, 'status' => 1])->first();
                //,'order_type'=>1
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

            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  = 'product_price';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            if ($is_buy_now == 1) {
                $this->updateCartProductPriceByVariantIds($variantIds, $user_id);
            }
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if (in_array('true', $check_stock)) {
            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  =  'stock';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if (in_array('false', $is_product_active)) {
            return response()->json(['error' => true, 'message' => @Helper::language('something_went_wrong'), 'type' => 'product_active']);

            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  =  'product_active';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        $order_custom_id = $this->geneateOrderId();
        $order_uid = uniqid();
        $order = new Order();
        $order->uniqid = @$order_uid;
        $order->user_id = @$user_id;
        $order->supplier_id = @$store_id;
        $order->order_id = $order_custom_id;
        $order->delivery_address = @$delivery_address;

        if ($isSameAddress) {
            $order->billing_address = $delivery_address;
            $order->isSameAddress = 1;
        } else {
            $order->billing_address = $billing_address;
            $order->isSameAddress = 0;
        }

        $order->order_type = @$purchase_order_type;

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

        if (@$user_address_data) {
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
        $order_tracking->order_id = @$order->id;
        $order_tracking->uniqid = @$tracking_id;
        $order_tracking->order_status = 1;
        $order_tracking->status = 1;
        $order_tracking->save();


        $order_info = new OrderInfo();
        $order_info->order_id = $order->id;
        if ($purchase_order_type == 1) {
            $order_info->country_id = @$user_address_data->country_id;
            $order_info->region_id = @$user_address_data->region_id;
            $order_info->area_id = @$user_address_data->area_id;
        }

        $order_info->customer_name = @$userData->first_name . ' ' . $userData->last_name;
        $order_info->customer_mobile = @$userData->phone_code . ' ' . $userData->phone;
        $order_info->customer_email = @$userData->email;
        $order_info->customer_country = (@$userData->country_code) ?: '';
        $order_info->order_from = $order_from;
        if ($purchase_order_type == 2) {
            $order_info->store_pickup_address = @$store_address;
        }
        $order_info->save();

        $original_price = 0;
        $total_discount_price = 0;
        $product_discount_price = 0;
        $is_product_discount[] =  false;
        $offer_type_store = '';
        $discount_amount_store = 0;

        foreach ($productData as $variant_product) {
            $cartEntry = $variant_product->cart->where('user_id', $user_id)->sortByDesc('id')->first();
            $bogoStatus = $cartEntry ? $cartEntry->is_bogo : false;

            if ($is_buy_now == 1) {
                $cart_qty = $buy_quantity;
                $bogoStatus = $bogo_status;
                $is_offer = $offer_status;
                $offer_type = $offer_type;
                $discount_amount = $discount_amount;
                $offer_type_store = $offer_type;
                $discount_amount_store = $discount_amount;

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                $variant_orginal_price = $buy_org_price;

                if ($is_offer) {
                    if ($offer_type == 'flat') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($discount_amount));
                    } elseif ($offer_type == 'percentage') {
                        $variant_offer_price = max(0, $variant_orginal_price - ($variant_orginal_price * $discount_amount / 100));
                    }
                } else {
                    $variant_offer_price = '0';
                }

                // $variant_offer_price = $buy_discounted_price;
            } else {
                $cart_qty = Helper::getUserCartQuantity($variant_product->id, $user_id);

                if ($bogoStatus) {
                    $cart_qty *= 2;
                }

                //using for variant new price
                $cart_info = Cart::where(['product_variant_id' => $variant_product->id, 'user_id' => $user_id, 'status' => 1, 'order_type' => 1])->first();
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

            $product_unit = Helper::getUnitById($variant_product->variant_uof);
            //$product_total_price = ($cart_qty * $result->variant_discounted_price);
            $order_detail = new OrderDetails();
            $order_detail->order_id = @$order->id;
            $order_detail->product_id = @$variant_product->get_product_details->id;
            $order_detail->variant_id = @$variant_product->id;
            $order_detail->customer_id = @$user_id;
            $order_detail->product_original_amount = @$variant_orginal_price;
            $order_detail->product_total_amount = @$variant_offer_price;
            $order_detail->quantity = @$cart_qty;
            $order_detail->variant_size = @$variant_product->variant_size;
            $order_detail->variant_unit = @$variant_product->variant_uof;
            $order_detail->status = 1;
            $order_detail->is_bogo = (int) $bogoStatus;
            $order_detail->save();


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
                $available_qty = @$variant_product->available_qty - $cart_qty_for_price;
                $sold_qty = @$variant_product->sold_qty + $cart_qty_for_price;
                ProductVariants::where('id', $variant_product->id)->update(array('available_qty' => $available_qty, 'sold_qty' => $sold_qty));
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
            $coupon_code_price = ($couponPercentage / 100) * $grand_total_amount;
            $sub_total_amount = ($grand_total_amount - round($coupon_code_price, 2));
            OrderInfo::where('order_id', $order->id)
                ->update(array(
                    'promocode_name' => $promo_title,
                    'promocode_percentage' => $couponPercentage
                ));
            $grand_total_amount = $sub_total_amount;

            // insert Data in coupon_user table
            \DB::table('coupon_user')->insert([
                'user_id' => $user_id,
                'coupon_id' => $coupon_id,
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
            $tax_amount =  round($tax, 2);
            Order::where('id', $order->id)->update(array('tax' => $tax_amount));
            $grand_total_amount = round(($grand_total_amount + $tax_amount), 2);
        }

        if (!empty($user_address_id) && $purchase_order_type == 1) {
            $area_tax_rate = round($user_address_data->area->rate, 2);
            $deliveryAmount = round($user_address_data->area->delivery_amount, 2);
            $deliveryFee = round($user_address_data->area->delivery_fee, 2);


            if ($grand_total_amount > $deliveryAmount) {
                $area_tax_rate = 0;
            } else {
                $area_tax_rate = $deliveryFee;
            }

            $grand_total_amount = round($grand_total_amount + $area_tax_rate, 2);
            OrderInfo::where('order_id', $order->id)->update(array('delivery_fee' => $area_tax_rate));
        }


        $transactions = new Transactions();
        $transactions->trans_no = $this->geneateTranscationId();
        $transactions->user_id = $user_id;
        $transactions->order_id = @$order->id;
        $transactions->payment_type = @$payment_method;
        $transactions->payment_status = $payment_method == 1 ? "" : '1';
        $transactions->amount = @$grand_total_amount;
        $transactions->transaction_date = date('Y-m-d H:i:s');
        if ($payment_method == 1) {
            $transactions->status = 2;
        } else {
            $transactions->status = 1;
        }
        $transactions->save();

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
                'points'     => (int) round($earnedpoints),
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
            $redirectUrl = route('orderSuccessCard', ['userid' => $user_id, 'orderid' => $order->id, 'amount' => $grand_total_amount, 'earnedpoints' => (int) round($earnedpoints)]);
            $backUrl = url('/callBackUrl');
            $xmlPayload = '<?xml version="1.0" encoding="utf-8"?><API3G><CompanyToken>4CF16A78-27EA-47A7-B1D4-6E52343C8DC1</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>' . $grand_total_amount . '</PaymentAmount><PaymentCurrency>GHS</PaymentCurrency><CompanyRef>49FKEOA</CompanyRef><RedirectURL>' . $redirectUrl . '</RedirectURL><BackURL>' . $backUrl . '</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>87197</ServiceType><ServiceDescription>Food And Beverages</ServiceDescription><ServiceDate>' . date('Y-m-d H:i:s') . '</ServiceDate></Service></Services></API3G>';

            $getToken = \Helper::createToken($xmlPayload, $grand_total_amount, $redirectUrl, $backUrl, $user_id, $user_address_id, $order->id);

            logger()->info("+++++++++++++++++++++++++++checkoutMobile - createTokenMobile+++++++++++++++++++++++");
            logger()->info($getToken);

            logger()->info("-----------------------------------------");
            $result = json_decode(json_encode($getToken), true);
            if (@$result['original']['error']) {
                $result['code']     =  strval(0);
                $result['message']  =  'something_went_wrong';
                $result['error_type']  =  'payment_failed';
                $result['result']   =  NULL;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }

            if (@$result['original']['token']) {
                $token = $result['original']['token'][0];
                $paymentUrl = 'https://secure.3gdirectpay.com/payv3.php?ID=' . $token;
                // dd($paymentUrl);

                $response_data['order_number'] = $order_custom_id;
                $response_data['payment_url'] = $paymentUrl;

                $result['code']     =  strval(1);
                $result['message']  =  'order_sucessfully_placed';
                $result['result']   =  $response_data;
                $result['generate_token']    =  $token;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }

        $title = "Online Order Confirm";
        $message = "Online Order Confirm";
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


        DB::table('checkout_sessions')->where('user_id', $user_id)->update([
            'apply_order' => null,
            'updated_at' => now(),
        ]);
        
        $data = [
            'message' => 'New Order Received',
            'order_number' => $order->order_id,
            'id' => $order->id,
            'total_amount' => $product_sub_total_amount,
            'user_name' => $order->user->first_name . ' ' . $order->user->last_name,
            'order_type' => match ($order->order_type) {
                '1' => 'Online',
                '2' => 'Cash On Delivery',
                '3' => 'Purchase Order',
                default => 'Unknown',
            },
        ];

        //  \Helper::sendNotification($device_token, $title, $message, @$order->id, @$order_custom_id);
        broadcast(new NewOrderPlaced($data));

        $this->sendOrderConfirmationEmail($user_id, $order);


        if ($is_buy_now != 1) {
            $updatepsw = Cart::where('user_id', $user_id)->update(array('status' => 2));
        }

        $response_data['order_number'] = $order_custom_id;
        $response_data['earnedpoints'] = (int) round($earnedpoints);

        $result['code']     =  strval(1);
        $result['message']  =  'order_sucessfully_placed';
        $result['result']   =  $response_data;

        $mainResult   =   $result;
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }

    // callback added on 11 sept 2025
    public function callBackUrl()
    {
        $result = [];
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
        } else {
            echo "<pre>Transaction Failed</pre>";
            Transactions::where('order_id', $order_id)->update(['payment_status' => '2']);
        }

        $result['code']     =  strval(0);
        $result['message']  =  'something_went_wrong';
        $result['error_type']  =  'payment_failed';
        $result['result']   =  NULL;

        $mainResult   =   $result;
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }



    public function save_payment(Request $request)
    {


        logger()->info("+++++++++++++++++++++++++++        CheckoutController - save_payment+++++++++++++++++++++++");
        logger()->info($request->all());

        logger()->info("-----------------------------------------");

        $result = [];
        $validator = \Validator::make($request->all(), [
            'order_id' => 'required',
            'transaction_id' => 'required',
            'user_id' => 'required',
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }



        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);


        logger()->info("+++++++++++++++++++++++++++request - token+++++++++++++++++++++++");
        logger()->info($response);

        logger()->info("-----------------------------------------");
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        $orderId = $request->order_id;
        $userId = $request->user_id;



        logger()->info("+++++++++++++++++++++++++++request - token+++++++++++++++++++++++");
        logger()->info($request->all());

        logger()->info("-----------------------------------------");

        $checkTransaction = \Helper::TransactionStatus($request->generate_token);

        logger()->info("+++++++++++++++++++++++++++CheckoutController - checkTransaction+++++++++++++++++++++++");
        logger()->info($checkTransaction);

        logger()->info("-----------------------------------------");
        if ($checkTransaction != false) {
            $orderData = Order::find($orderId);
            $orderData->status = 1;
            if ($orderData->save()) {
                try {
                    // $tranId = @$request->transaction_id ?: $this->geneateTranscationId();
                    // $transactions = Transactions::where('order_id', $orderId)->update(['status' => 1]);

                    $orderDetails = OrderDetails::where('order_id', $orderData->id)->get();

                    if (!empty($orderDetails) && $orderData->is_stock_updated != 1) {
                        // foreach ($orderDetails as $item) {
                        //     $existing = ProductVariants::find($item->variant_id);
                        //     $available_qty = $existing->available_qty - $item->quantity;
                        //     $sold_qty = $existing->sold_qty + $item->quantity;

                        //     $existing->available_qty = $available_qty;
                        //     $existing->sold_qty = $sold_qty;
                        //     $existing->save();
                        // }
                        // $updatStockStatus = Order::find($orderData->id);
                        // $updatStockStatus->is_stock_updated = 1;
                        // $updatStockStatus->save();

                        // $userData = DB::table('main_users')->where('id', $userId)->first();
                        $title = "Online Order Confirm";
                        $message = "Online Order Confirm";
                        // $remember_token = "fYPZqDsO90pgmZAzTKD0ow:APA91bEF6Pv3waTLBb8lRSUCrjz_M3Vxf14zF3IDHBckRoI79Ojw66aRbuDNhuHWT21qsPYwhOvXxGhILv-nJ0ZCNWzEEuo2tWQ1pDULgeBkPPoAbPaV6ulPJ0W1H8zhA2IcPn8zHl8P";
                        // $device_token = $userData->device_token;
                        // $device_type = 1;

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
                        $admin_notification->order_id = @$orderData->id;
                        $admin_notification->sender_id = @$userId;
                        $admin_notification->receiver_id = @$userId;
                        $admin_notification->notification_type = 1;
                        $admin_notification->title = @$title;
                        $admin_notification->message = @$message;
                        $admin_notification->is_read = 0;
                        $admin_notification->save();

                        // \Helper::sendNotification($device_token, $title, $message, @$orderData->id, @$orderData->order_id);


                        DB::table('checkout_sessions')->where('user_id', $userId)->update([
                            'apply_order' => null,
                            'updated_at' => now(),
                        ]);

                        $this->sendOrderConfirmationEmail($userId, $orderData);

                        Transactions::where('order_id', $orderId)->update(['payment_status' => '1']);
                        Cart::where('user_id', $userId)->update(array('status' => 2));

                        $response_data['order_number'] = @$orderData->order_id;
                        $result['code']     =  strval(1);
                        $result['message']  =  'success';
                        $result['result']   =  $response_data;

                        $mainResult   =   $result;
                        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                    }
                } catch (\Throwable $th) {
                    Transactions::where('order_id', $orderId)->update(['payment_status' => '2']);

                    $result['code']     =  strval(0);
                    $result['message']  =  'something_went_wrong';
                    $result['error_type']  =  'payment_failed';
                    $result['result']   =  NULL;

                    $mainResult   =   $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
            }
        } else {

            Transactions::where('order_id', $orderId)->update(['payment_status' => '2']);

            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['error_type']  =  'payment_failed';
            $result['result']   =  NULL;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }
}
