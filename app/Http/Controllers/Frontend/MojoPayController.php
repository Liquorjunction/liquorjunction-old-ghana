<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Services\MojoPay;
use App\Services\OnlineOrderFulfillment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MojoPayController extends Controller
{
    public function webhook(Request $request, MojoPay $mojoPay, OnlineOrderFulfillment $fulfillment)
    {
        $payload = $request->getContent();
        if ((string) config('services.mojopay.webhook_secret') === '') {
            logger()->warning('Mojo webhook secret is not configured');
            return response('Webhook secret is not configured', 503);
        }

        if (!$mojoPay->verifyWebhook($payload, $request->header('MojoPay-Signature'))) {
            return response('Invalid signature', 401);
        }

        $event = json_decode($payload, true);
        if (!is_array($event)) {
            return response('Invalid payload', 400);
        }

        $eventId = (string) ($event['id'] ?? '');
        if ($eventId !== '' && !Cache::add('mojopay.event.' . $eventId, 1, now()->addDays(7))) {
            return response('OK', 200);
        }

        $reference = $event['data']['object']['merchant_reference'] ?? null;
        $order = $this->findOrder($reference);
        if (!$order) {
            return response('OK', 200);
        }

        $sessionId = (string) ($event['data']['object']['id'] ?? '');
        if ($sessionId !== '') {
            $mojoPay->rememberSession((int) $order->id, $sessionId);
        }

        $type = (string) ($event['type'] ?? '');
        if ($type === 'checkout_session.succeeded') {
            $fulfillment->markPaid($order);
            PaymentLog::record((int) $order->id, 'webhook', 'success', $type, 'webhook');
        } elseif (in_array($type, ['checkout_session.failed', 'checkout_session.cancelled', 'checkout_session.canceled', 'checkout_session.expired'], true)) {
            $fulfillment->markFailed($order);
            PaymentLog::record((int) $order->id, 'webhook', 'failed', $type, 'webhook');
        } elseif ($type !== '') {
            PaymentLog::record((int) $order->id, 'webhook', 'pending', $type, 'webhook');
        }

        return response('OK', 200);
    }

    public function returned(string $order, MojoPay $mojoPay, OnlineOrderFulfillment $fulfillment)
    {
        $orderData = $this->findOrder($order);
        if (!$orderData) {
            abort(404);
        }

        $status = $this->statusForOrder($orderData, $mojoPay);
        if ($status === 'succeeded') {
            PaymentLog::record((int) $orderData->id, 'customer_returned', 'success', 'Mojo session succeeded', 'return');
            return $this->paidView($orderData, $fulfillment);
        }

        if ($status === 'failed') {
            $fulfillment->markFailed($orderData);
            PaymentLog::record((int) $orderData->id, 'customer_returned', 'failed', 'Mojo session failed', 'return');
        } else {
            PaymentLog::record((int) $orderData->id, 'customer_returned', 'pending', 'Payment not confirmed yet', 'return');
        }

        return view('frontend.checkout.payment-pending', [
            'title' => 'Payment not confirmed',
            'message' => 'We have not confirmed this payment yet. If you completed it, wait a moment and refresh this page.',
        ]);
    }

    public function cancelled(string $order, MojoPay $mojoPay, OnlineOrderFulfillment $fulfillment)
    {
        $orderData = $this->findOrder($order);
        if ($orderData) {
            $status = $this->statusForOrder($orderData, $mojoPay);
            if ($status === 'succeeded') {
                PaymentLog::record((int) $orderData->id, 'customer_cancelled', 'success', 'Payment already succeeded', 'cancel');
                return $this->paidView($orderData, $fulfillment);
            }

            if ($status !== 'unknown') {
                $fulfillment->markFailed($orderData);
                PaymentLog::record((int) $orderData->id, 'customer_cancelled', 'failed', 'Cancelled on Mojo page', 'cancel');
            } else {
                PaymentLog::record((int) $orderData->id, 'customer_cancelled', 'pending', 'Status could not be checked', 'cancel');
            }
        }

        return view('frontend.checkout.payment-pending', [
            'title' => 'Payment cancelled',
            'message' => 'The payment was cancelled. This order is marked as failed, not paid.',
        ]);
    }

    private function statusForOrder(Order $order, MojoPay $mojoPay): string
    {
        $sessionId = $mojoPay->sessionIdForOrder((int) $order->id);
        if ($sessionId === null) {
            return 'unknown';
        }

        return $mojoPay->sessionStatus($sessionId);
    }

    private function paidView(Order $order, OnlineOrderFulfillment $fulfillment)
    {
        $fulfillment->markPaid($order);
        $orderData = Order::with('user')->find($order->id);
        $earnedpoints = $this->earnedPoints($orderData->id);
        $this->clearCheckoutSession();

        return view('frontend.checkout.order-success', compact('orderData', 'earnedpoints'));
    }

    private function findOrder($reference): ?Order
    {
        if ($reference === null || !ctype_digit((string) $reference)) {
            return null;
        }

        return Order::find((int) $reference);
    }

    private function clearCheckoutSession(): void
    {
        Session::forget('buy_now_info');
        Session::forget('checkout_in_progress');
        Session::forget('checkout_page_load_time');
    }

    private function earnedPoints(int $orderId): int
    {
        return (int) DB::table('loyalty_points')
            ->where('order_ref_id', $orderId)
            ->where('type', 'credit')
            ->value('points');
    }
}
