<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $type = (string) ($event['type'] ?? '');
        if ($type === 'checkout_session.succeeded') {
            $fulfillment->markPaid($order);
        } elseif ($type === 'checkout_session.failed') {
            $fulfillment->markFailed($order);
        }

        return response('OK', 200);
    }

    public function returned(string $order, MojoPay $mojoPay, OnlineOrderFulfillment $fulfillment)
    {
        $orderData = $this->findOrder($order);
        if (!$orderData) {
            abort(404);
        }

        $status = $mojoPay->paymentStatus((string) $orderData->id);
        if ($status === 'succeeded') {
            $fulfillment->markPaid($orderData);
            $orderData = Order::with('user')->find($orderData->id);
            $earnedpoints = $this->earnedPoints($orderData->id);
            $this->clearCheckoutSession();

            return view('frontend.checkout.order-success', compact('orderData', 'earnedpoints'));
        }

        if ($status === 'failed') {
            $fulfillment->markFailed($orderData);
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
            $status = $mojoPay->paymentStatus((string) $orderData->id);
            if ($status === 'succeeded') {
                $fulfillment->markPaid($orderData);
                $orderData = Order::with('user')->find($orderData->id);
                $earnedpoints = $this->earnedPoints($orderData->id);
                $this->clearCheckoutSession();

                return view('frontend.checkout.order-success', compact('orderData', 'earnedpoints'));
            }
        }

        return view('frontend.checkout.payment-pending', [
            'title' => 'Payment cancelled',
            'message' => 'The payment was cancelled. Your order has not been marked as paid.',
        ]);
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
