<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\MainUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use PDF;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use App\Exports\OrderReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportController extends Controller
{
    //
    private $uploadPath = "/uploads/product";
    protected $image_uri = "";
    protected $no_image = "";
    protected $business_owner_id = 57;
    private $uploadDataPath = "uploads/product/";

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function getUploadDataPath()
    {
        return $this->uploadDataPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = env('APP_URL') . $uploadPath;
    }
    public function setUploadDataPath($uploadPath)
    {
        $this->uploadDataPath = Config::get('app.APP_URL') . $uploadDataPath;
    }

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 15, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        // echo "string";exit();

        // echo "<pre>";print_r($settings->toArray());exit();
        $order_report = Order::leftjoin('main_users', 'main_users.id', '=', 'order.user_id')
            ->leftjoin('main_users as supplier_data', 'supplier_data.id', '=', 'order.supplier_id')
            ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
            ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
            ->leftjoin('users', 'users.id', '=', 'order.id')
            ->leftjoin('transactions', 'transactions.id', '=', 'order.transaction_id')
            ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name', 'transactions.payment_type', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status')
            // ->groupBy('order_info.id')
            ->groupBy('order_info.customer_mobile')
            ->orderBy('order.id', 'ASC')
            ->where('order.status', 1)
            ->get();
         
        return view("dashboard.orderreport.list", compact('order_report'));
    }

 
    public function export_orderreport(Request $request)
    {
        // if (!empty($request->startdate && $request->enddate)) {
        //     $min_date = Carbon::parse($request->startdate)->format('Y-m-d');
        //     $max_date = Carbon::parse($request->enddate)->format('Y-m-d');

        //     $user_report = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
        //         ->join('order_info', 'order_info.order_id', '=', 'order.id')
        //         ->join('order_status', 'order_status.id', '=', 'order.order_status')
        //         ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        //         ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
        //         ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
        //         ->leftjoin('users', 'users.id', '=', 'order.id')
        //          ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
        //          ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name', 'order_detail.product_original_amount as mrp','order_detail.quantity','order_detail.variant_size as attribute','order_detail.variant_unit as size','transactions.payment_type', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status','order_info.store_pickup_address as store_address','order_info.delivery_fee')
        //         ->whereBetween('order.created_at', [$min_date . ' 00:00:00', $max_date . ' 23:59:59'])
        //         ->orderBy('order.id', 'ASC')
        //         ->where('order.status', 1)
        //         ->get();
        // }
        //  elseif ($request->input('datahidden') != "") {
        //     $datafilter = $request->input('datahidden');

        //     $user_report = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
        //         ->join('order_info', 'order_info.order_id', '=', 'order.id')
        //         ->join('order_status', 'order_status.id', '=', 'order.order_status')
        //         ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        //         ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
        //         ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
        //         ->leftjoin('users', 'users.id', '=', 'order.id')
        //         ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
        //         ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name', 'order_detail.product_original_amount as mrp','order_detail.quantity','order_detail.variant_size as attribute','order_detail.variant_unit as size','transactions.payment_type', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status','order_info.store_pickup_address as store_address','order_info.delivery_fee')
        //         ->where('order_info.customer_mobile', $datafilter)
        //         ->orderBy('order.id', 'ASC')
        //         ->where('order.status', 1)
        //         ->get();
        // } else {
        //     $user_report = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
        //     ->join('order_info', 'order_info.order_id', '=', 'order.id')
        //     ->join('order_status', 'order_status.id', '=', 'order.order_status')
        //     ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        //     ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
        //     ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
        //     ->leftjoin('users', 'users.id', '=', 'order.id')
        //     ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
        //     ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name','order_detail.product_original_amount as mrp','order_detail.quantity','order_detail.variant_size as attribute' ,'order_detail.variant_unit as size','transactions.payment_type', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status','order_info.store_pickup_address as store_address','order_info.delivery_fee')
        //     ->orderBy('order.id', 'ASC')
        //     ->where('order.status', 1)
        //     ->get();
        // }


        $min_date = $request->startdate ? Carbon::parse($request->startdate)->format('Y-m-d') . ' 00:00:00' : null;
        $max_date = $request->enddate ? Carbon::parse($request->enddate)->format('Y-m-d') . ' 23:59:59' : null;
        $datafilter = $request->input('datahidden');

        $user_report = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
            ->join('order_info', 'order_info.order_id', '=', 'order.id')
            ->join('order_status', 'order_status.id', '=', 'order.order_status')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
            ->leftjoin('users', 'users.id', '=', 'order.id')
            ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
            ->select(
                'order.*',
                'order_info.customer_name as customer_name',
                'order_info.customer_mobile as customer_mobile',
                'countries.name as country_name',
                'product.product_name as product_name',
                'order_detail.product_original_amount as mrp',
                'order_detail.product_total_amount as total_amount',
                'order_detail.quantity',
                'order_detail.is_bogo',
                'order_detail.variant_size as attribute',
                'order_detail.variant_unit as size',
                'transactions.payment_type',
                'transactions.payment_status as payment_status',
                'order_status.name as delivery_status',
                'order_info.store_pickup_address as store_address',
                'order_info.delivery_fee',
                'order_info.reward_amount'
            )
            ->where('order.status', 1)
            ->when($min_date && $max_date, function ($query) use ($min_date, $max_date) {
                return $query->whereBetween('order.created_at', [$min_date, $max_date]);
            })
            ->when($datafilter, function ($query) use ($datafilter) {
                return $query->where('order_info.customer_mobile', $datafilter);
            })
            ->orderBy('order.id', 'ASC')
            ->get();

        $order_totals = [];
        $grouped_orders = [];

        foreach ($user_report as $data) {
            $orderId = $data->order_id;
            
            // Bogo check
            // $effectiveQuantity = $data->is_bogo ? $data->quantity / 2 : $data->quantity;
            $effectiveQuantity = $data->quantity;
            
            // $itemTotal = $data->quantity * $data->mrp;
            // $itemTotal = $effectiveQuantity * $data->mrp;
            $unit_price = ($data->total_amount && $data->total_amount > 0) ? $data->total_amount : $data->mrp;
            $itemTotal = $effectiveQuantity * $unit_price;


            if (!isset($order_totals[$orderId])) {
                $order_totals[$orderId] = 0;
                $grouped_orders[$orderId] = [];
            }

            $data->effective_quantity = $effectiveQuantity;
            $data->item_total = $itemTotal;

            $order_totals[$orderId] += $itemTotal;
            $grouped_orders[$orderId][] = $data;
        }

        $export_data = [];

        foreach ($grouped_orders as $orderId => $items) {
            $total_order_value = $order_totals[$orderId] ?? 1;
            $summary = [
                'quantity' => 0,
                'mrp' => 0,
                'gross' => 0,
                'promo' => 0,
                'loyalty' => 0,
                'bogo' => 0,
                'cart' => 0,
                'discount' => 0,
                'delivery' => 0,
                'gift' => 0,
                'paid' => 0,
            ];

            foreach ($items as $item) {
                // $item_total = $item->quantity * $item->mrp;
                $item_total = $item->item_total;

                $discount_amount = $item->discount_amount ?? 0;
                $delivery_fee = $item->delivery_fee ?? 0;
                $gift_card = $item->gift_card ?? 0;
                $cart_discount = $item->cart_discount ?? 0;
                $loyalty_discount = $item->reward_amount ?? 0;

                $item_discount = ($item_total / $total_order_value) * $discount_amount;
                $item_delivery = ($item_total / $total_order_value) * $delivery_fee;
                $item_gift = ($item_total / $total_order_value) * $gift_card;
                $item_cart_discount = ($item_total / $total_order_value) * $cart_discount;
                $item_loyalty_discount = ($item_total / $total_order_value) * $loyalty_discount;
                $bogo_discount = ($item->is_bogo) ? ($item_total / 2) : 0;

                $total_discount = $item_discount +  $item_cart_discount + $item_loyalty_discount + $bogo_discount;
                $grand_total = $item_total - $total_discount + $item_delivery + $item_gift;

                $summary['quantity'] += $item->quantity;
                // $summary['quantity'] += $item->effective_quantity;
                $summary['mrp'] += $item->mrp;
                $summary['gross'] += $item_total;
                $summary['promo'] += $item_discount;
                $summary['bogo'] += $bogo_discount;
                $summary['cart'] += $item_cart_discount;
                $summary['loyalty'] += $item_loyalty_discount;
                $summary['discount'] += $total_discount;
                $summary['delivery'] += $item_delivery;
                $summary['gift'] += $item_gift;
                $summary['paid'] += $grand_total;                

                $export_data[] = [
                    'is_summary' => false,
                    'data' => $item,
                    'computed' => [
                        'item_total' => $item_total,
                        'promo_discount' => $item_discount,
                        'loyalty_discount' => $item_loyalty_discount,
                        'bogo_discount' => $bogo_discount,
                        'cart_discount' => $item_cart_discount,
                        'total_discount' => $total_discount,
                        'delivery_charge' => $item_delivery,
                        'gift_card' => $item_gift,
                        'grand_total' => $grand_total
                    ]
                ];
            }

            $export_data[] = ['is_summary' => true, 'summary' => $summary];
            $export_data[] = ['is_blank' => true];
        }

        return Excel::download(new OrderReportExport($export_data), 'orderreport-' . now()->format('d-m-Y') . '.xlsx');

    }
        
    public function anyData(Request $request)
    {
        $setting = Setting::find(1);
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $start_date = $request->get('startdate');
        $order_type_status = $request->get('order_type_status');
        $datafilter = $request->get('datafilter');
        $end_date = $request->get('enddate');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 1) {
            $sort = 'order.id';
        } elseif ($columnIndex == 2) {
            $sort = 'order_info.customer_name';
        } elseif ($columnIndex == 3) {
            $sort = 'order_info.customer_mobile';
        } elseif ($columnIndex == 4) {
            $sort = 'order_info.country_id';
        } elseif ($columnIndex == 5) {
            $sort = 'product.product_name';
        } elseif ($columnIndex == 6) {
            $sort = 'order.order_type';
        } elseif ($columnIndex == 7) {
            $sort = 'order.payable_amount';
        } elseif ($columnIndex == 8) {
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

        $totalAr = Order::leftjoin('main_users', 'main_users.id', '=', 'order.user_id')
            ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
            ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
            ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
            ->Join('order_detail', 'order_detail.order_id', '=', 'order.id')
            ->Join('product', 'product.id', '=', 'order_detail.product_id')
            ->leftjoin('transactions', 'transactions.order_id', '=', 'order.id')
            ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name', 'transactions.payment_type', 'transactions.payment_status as payment_status')
            ->where('order.status', 1)
            ->groupBy('order_info.id');
        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('order.created_at', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('order.created_at', '<=', $min_date . ' 23:59:59');
        }

        // if ($datafilter != "") {
        //     $totalAr = Order::leftjoin('main_users', 'main_users.id', '=', 'order.user_id')
        //         ->leftjoin('order_info', 'order_info.order_id', '=', 'order.id')
        //         ->leftjoin('order_status', 'order_status.id', '=', 'order.id')
        //         ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
        //         ->leftJoin('order_detail', 'order_detail.order_id', '=', 'order.id')
        //         ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
        //         ->leftjoin('transactions', 'transactions.id', '=', 'order.transaction_id')
        //         ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'product.product_name as product_name', 'transactions.payment_type', 'transactions.payment_status as payment_status')
        //         ->where('order_info.customer_mobile', $datafilter);
        // }

        if ($datafilter != "") {
            $totalAr->where('order_info.customer_mobile', $datafilter);
        }

        if ($searchValue != "") {

            $typeArray = [
                [
                    'key' => 1,
                    'name' => 'Online',
                ],
                [
                    'key' => 2,
                    'name' => 'Pickup Order',
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
            foreach ($payment_type as $item2) {
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
                    ->orWhere('order_info.customer_mobile', 'like', '%' . $searchValue . '%')
                    ->orWhere('order_status.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('transactions.payment_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('product.product_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('countries.name', 'like', '%' . $searchValue . '%')
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


        $totalRecords = $totalAr->get()->count();
        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->groupBy('order_info.id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        if (!empty($order_type_status)) {
            $totalAr->where('order.order_type', $order_type_status);
        }
        $settings = Setting::find(1);

        $data_arr = [];
        foreach ($totalAr as $key => $data) {
            $categoryShow = route('category.show', ['id' => $data->id]);
            $categpryEdit = route('category.edit', ['id' => $data->id]);


            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
            }
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-customer" data-id="' . $data->id . '" title="Show"> </a>';
            $options .= '<a class="btn btn-sm success paddingset edit-customer" data-id="' . $data->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            if ($data->order_type == 1) {
                $order_type = '
                <span class="badge badge-success">Online</span>
              ';
            } elseif ($data->order_type == 3) {
                $order_type = '
                <span class="badge  badge-danger">In Store</span>
              ';
            } else {
                $order_type = '
                <span class="badge  badge-danger">Pickup Order</span>
              ';
            }


            if ($data->order_type == 1) {
                $order_type = '
                <span class="badge  badge-success">Online</span>
              ';
            } elseif ($data->order_type == 3) {
                $order_type = '
                <span class="badge  badge-danger">In Store</span>
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
                } else {
                    $payment_status = '
            <span class="badge badge-danger">Failed</span>
        ';
                }
            }
            if ($data->order_status == 4) {
                $status = '<button type="button" class="btn btn-success pointer_button bt-can">
                <span class="badge  badge-success">Cancel</span>
              </button>';
            } elseif ($data->order_status == 3) {
                $status = '<button type="button" class="btn btn-warning pointer_button bt_war">
                <span class="badge  badge-danger">Delivered</span>
              </button>';
            } elseif ($data->order_status == 2) {
                $status = '<button type="button" class="btn btn-info pointer_button">
                <span class="badge  badge-danger">Accepted</span>
              </button>';
            } else {
                $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Pending</span>
              </button>';
            }
            $date = \Helper::converttimeTozone($data->created_at);

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" => isset($data->order_id) ? $data->order_id : '',
                "customer_name" => ucfirst(isset($data->customer_name)) ? ucfirst($data->customer_name) : '',
                "customer_mobile" => isset($data->customer_mobile) ? '+' . $data->customer_mobile : '',
                "country_name" => isset($data->country_name) ? $data->country_name : '-',
                "product_name" => isset($data->product_name) ? $data->product_name : '',
                "order_type" => isset($order_type) ? $order_type : '',
                "delivery_status" => isset($status) ? $status : '',
                "payment_type" => isset($payment_type) ? $payment_type : '',
                "payment_status" => isset($payment_status) ? $payment_status : '',
                "grand_total_amount" => @$data->payable_amount . ' ' . @$settings->currency_symbol,
                "order_date" => @$date ? Carbon::parse($date)->format(env('DATE_FORMAT', 'Y-m-d') . ' h:i A') : "-",
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
