<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\Setting;
use Helper;
use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    public function index()
    {
        return view('dashboard.payment-log.list');
    }

    public function anyData(Request $request)
    {
        PaymentLog::ensureTable();

        $draw = $request->get('draw');
        $start = (int) $request->get('start');
        $rowperpage = (int) $request->get('length');
        if ($rowperpage <= 0) {
            $rowperpage = 10;
        }

        $orderArr = $request->get('order');
        $columnSortOrder = $orderArr[0]['dir'] ?? 'desc';
        $columnIndex = (int) ($orderArr[0]['column'] ?? 0);
        $searchValue = (string) ($request->input('search.value') ?? '');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $resultFilter = $request->get('result_filter');
        $orderIdFilter = trim((string) $request->get('order_id_filter'));

        $sortColumns = [
            0 => 'payment_logs.created_at',
            1 => 'order.order_id',
            2 => 'order_info.customer_name',
            3 => 'order.payable_amount',
            4 => 'payment_logs.event',
            5 => 'payment_logs.result',
            6 => 'payment_logs.detail',
            7 => 'payment_logs.source',
        ];
        $sort = $sortColumns[$columnIndex] ?? 'payment_logs.created_at';

        $base = PaymentLog::query()
            ->leftJoin('order', 'order.id', '=', 'payment_logs.order_id')
            ->leftJoin('order_info', 'order_info.order_id', '=', 'order.id');

        $totalRecords = (clone $base)->count();
        $filtered = clone $base;
        $this->applyFilters($filtered, $fromDate, $toDate, $resultFilter, $orderIdFilter, $searchValue);
        $totalFiltered = (clone $filtered)->count();

        $rows = $filtered
            ->select(
                'payment_logs.id',
                'payment_logs.order_id',
                'payment_logs.event',
                'payment_logs.result',
                'payment_logs.detail',
                'payment_logs.source',
                'payment_logs.created_at',
                'order.order_id as public_order_id',
                'order.payable_amount',
                'order_info.customer_name'
            )
            ->orderBy($sort, $columnSortOrder === 'asc' ? 'asc' : 'desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $settings = Setting::find(1);
        $currency = $settings->currency_symbol ?? '';
        $data = [];

        foreach ($rows as $row) {
            $orderLabel = e($row->public_order_id ?: $row->order_id);
            if ($row->public_order_id) {
                $orderLabel = '<a href="' . e(route('adminorder.show', $row->order_id)) . '">' . $orderLabel . '</a>';
            }

            $amount = $row->payable_amount !== null
                ? e(trim(Helper::numberFormat($row->payable_amount) . ' ' . $currency))
                : '';

            $data[] = [
                'created_at' => e(optional($row->created_at)->format('d M Y, g:i a')),
                'order_id' => $orderLabel,
                'customer_name' => e($row->customer_name ?: ''),
                'amount' => $amount,
                'event' => e($this->eventLabel($row->event)),
                'result' => $this->resultBadge($row->result),
                'detail' => e($row->detail ?: ''),
                'source' => e($this->sourceLabel($row->source)),
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    private function applyFilters($query, $fromDate, $toDate, $resultFilter, $orderIdFilter, $searchValue): void
    {
        if ($fromDate) {
            $query->whereDate('payment_logs.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('payment_logs.created_at', '<=', $toDate);
        }
        if (in_array($resultFilter, ['pending', 'success', 'failed'], true)) {
            $query->where('payment_logs.result', $resultFilter);
        }
        if ($orderIdFilter !== '') {
            $query->where(function ($inner) use ($orderIdFilter) {
                $inner->where('order.order_id', 'like', '%' . $orderIdFilter . '%')
                    ->orWhere('payment_logs.order_id', $orderIdFilter);
            });
        }
        if ($searchValue !== '') {
            $query->where(function ($inner) use ($searchValue) {
                $inner->where('order.order_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_info.customer_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('payment_logs.detail', 'like', '%' . $searchValue . '%')
                    ->orWhere('payment_logs.event', 'like', '%' . $searchValue . '%');
            });
        }
    }

    private function eventLabel(?string $event): string
    {
        $labels = [
            'checkout_started' => 'Checkout started',
            'customer_returned' => 'Customer returned',
            'customer_cancelled' => 'Customer cancelled',
            'webhook' => 'Webhook',
        ];

        return $labels[$event] ?? (string) $event;
    }

    private function sourceLabel(?string $source): string
    {
        $labels = [
            'website' => 'Website',
            'app' => 'App',
            'return' => 'Return page',
            'cancel' => 'Cancel page',
            'webhook' => 'Webhook',
        ];

        return $labels[$source] ?? (string) $source;
    }

    private function resultBadge(?string $result): string
    {
        if ($result === 'success') {
            return '<span class="badge badge-success">Success</span>';
        }
        if ($result === 'failed') {
            return '<span class="badge badge-danger">Failed</span>';
        }

        return '<span class="badge badge-warning">Pending</span>';
    }
}
