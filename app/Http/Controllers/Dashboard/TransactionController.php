<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Activity;
use App\Models\Transaction;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->date_filter != "") {

            $parts = explode(' - ', $request->date_filter);
            $filterdate = $request->date_filter;

            $start = Carbon::createFromFormat('m/d/Y', $parts[0])->format('m-d-Y');
            $end = Carbon::createFromFormat('m/d/Y', $parts[1])->format('m-d-Y');
            $is_filter = 1;
        } else {
            $is_filter = 0;

            // $start = Carbon::now()->format('m-d-Y');
            // $end = Carbon::now()->format('m-d-Y');

            $start = date('m-01-Y');
            $end = date('m-d-Y', strtotime('now'));
        }
        $transactions = Transaction::orderby('id', 'desc')->get();

        return view("dashboard.transaction.list", compact('start', 'end', 'is_filter', 'transactions'));
    }

    public function show($id)
    {
        $id = base64_decode($id);
        $transactions = Transaction::select('transactions.id', 'transactions.transaction_id', 'transactions.created_at', 'transactions.status', 'b.name AS booking_name', 'u.name AS user_name', 'pu.name AS provider_name', 'transactions.amount AS trans_amount', 'transactions.status AS trans_status','i.item_name')->leftJoin('users as u', 'u.id', '=', 'transactions.user_id')->leftJoin('users as pu', 'pu.id', '=', 'transactions.service_provider_id')->leftJoin('bookings as b', 'b.id', '=', 'transactions.booking_id')->leftJoin('item as i', 'i.id', '=', 'b.item_id')->where('transactions.id', $id)->first();
        $setting = Setting::find(1);

        if ($transactions) {
            return view('dashboard.transaction.show', compact('transactions', 'setting'));
        }
    }

    public function anyData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $start_date = $request->get('startdate');
        $end_date = $request->get('enddate');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value

        if ($columnIndex == 1) {
            $sort = 'transactions.transaction_id';
        } elseif ($columnIndex == 2) {
            $sort = 'transactions.created_at';
        } elseif ($columnIndex == 3) {
            $sort = 'u.name';
        } elseif ($columnIndex == 4) {
            $sort = 'pu.name';
        } elseif ($columnIndex == 5) {
            $sort = 'transactions.amount';
        } else {
            $sort = 'transactions.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = Transaction::select('transactions.id', 'transactions.transaction_id AS transaction_id', 'transactions.created_at', 'transactions.status', 'b.name AS booking_name', 'u.name AS user_name', 'pu.name AS provider_name', 'transactions.amount AS trans_amount', 'transactions.status AS trans_status')
            ->leftJoin('users as u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('users as pu', 'pu.id', '=', 'transactions.service_provider_id')
            ->leftJoin('bookings as b', 'b.id', '=', 'transactions.booking_id');

        if (isset($start_date) && isset($end_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d');
            $max_date = Carbon::parse($end_date)->format('Y-m-d');

            $totalAr->whereBetween('transactions.created_at', [$min_date . ' 00:00:00', $max_date . ' 23:59:59']);
        }

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                return $query->where('transactions.transaction_id', 'LIKE', '%' . $searchValue . '%')
                    ->orWhereDate('transactions.created_at', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('b.name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('u.name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('pu.name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('transactions.amount', 'LIKE', '%' . $searchValue . '%');
            });
        }

        $setting = Setting::first();

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $transaction_id = isset($data->transaction_id) ? $data->transaction_id : '';
            $booking_name = isset($data->booking_name) ? $data->booking_name : '';
            $trans_date = isset($data->created_at) ? Helper::converttimeTozone($data->created_at) : '';

            $user_name = isset($data->user_name) ? $data->user_name : '';

            $provider_name = isset($data->provider_name) ? $data->provider_name : '';

            $amount = isset($data->trans_amount) ? $data->trans_amount : '';

            $new_id = base64_encode($data->id);
            $show = route('transaction.show', $new_id);

            $options = "";

            $options = '<a class="btn btn-sm show-eyes list box-shadow" href="' . $show . '" title="Show"> </a>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value checkbox" data-id="' . $data->trans_id . '" onclick="checkcheckbox();"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->trans_id . '"> </label>',
                "transaction_id" => $transaction_id,
                "created_at" => $trans_date,
                "booking_name" => $booking_name,
                "user_name" => $user_name,
                "provider_name" => $provider_name,
                "amount" => $setting->currency . ' ' . number_format($amount, 2),
                "status" => $data->status,
                "options" => $options,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr,
        );
        echo json_encode($response);
    }
}
