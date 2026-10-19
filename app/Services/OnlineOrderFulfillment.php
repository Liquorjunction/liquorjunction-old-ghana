<?php

namespace App\Services;

use App\Http\Controllers\Frontend\CheckoutController;
use App\Models\AdminNotifications;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ProductVariants;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;

class OnlineOrderFulfillment
{
    public function markPaid(Order $order): bool
    {
        $fulfilled = false;

        DB::transaction(function () use ($order, &$fulfilled) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$lockedOrder) {
                return;
            }

            $transaction = Transactions::where('order_id', $lockedOrder->id)->lockForUpdate()->first();
            if ($transaction && (string) $transaction->payment_status === '1') {
                return;
            }

            $lockedOrder->status = 1;
            $lockedOrder->save();

            if ($transaction) {
                $transaction->status = 1;
                $transaction->payment_status = '1';
                $transaction->save();
            }

            if ((int) $lockedOrder->is_stock_updated !== 1) {
                $orderDetails = OrderDetails::where('order_id', $lockedOrder->id)->get();
                foreach ($orderDetails as $item) {
                    $existing = ProductVariants::where('id', $item->variant_id)->lockForUpdate()->first();
                    if (!$existing) {
                        continue;
                    }
                    $existing->available_qty = $existing->available_qty - $item->quantity;
                    $existing->sold_qty = $existing->sold_qty + $item->quantity;
                    $existing->save();
                }
                $lockedOrder->is_stock_updated = 1;
                $lockedOrder->save();
            }

            $fulfilled = true;
        });

        if (!$fulfilled) {
            return false;
        }

        $paidOrder = Order::find($order->id);
        $userId = $paidOrder->user_id;

        $notification = new Notification();
        $notification->order_id = $paidOrder->id;
        $notification->sender_id = $userId;
        $notification->receiver_id = $userId;
        $notification->notification_type = 1;
        $notification->title = 'Online Order Confirm';
        $notification->message = 'Online Order Confirm';
        $notification->is_read = 0;
        $notification->save();

        $adminNotification = new AdminNotifications();
        $adminNotification->sender_id = $userId;
        $adminNotification->order_id = $paidOrder->id;
        $adminNotification->receiver_id = $userId;
        $adminNotification->notification_type = 1;
        $adminNotification->title = 'Online Order Confirm';
        $adminNotification->message = 'Online Order Confirm';
        $adminNotification->is_read = 0;
        $adminNotification->save();

        try {
            app(CheckoutController::class)->sendOrderConfirmationEmail($userId, $paidOrder);
        } catch (\Throwable $exception) {
            logger()->error('Order confirmation email failed', [
                'order_id' => $paidOrder->id,
                'message' => $exception->getMessage(),
            ]);
        }

        Cart::where('user_id', $userId)->update(['status' => 2]);

        return true;
    }

    public function markFailed(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $transaction = Transactions::where('order_id', $order->id)->lockForUpdate()->first();
            if (!$transaction || (string) $transaction->payment_status === '1') {
                return;
            }
            $transaction->payment_status = '2';
            $transaction->save();
        });
    }
}
