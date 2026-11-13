<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DanceClass;
use App\Models\DanceCategory;
use App\Models\WithdrawHistory;
use App\Models\ClassType;
use App\Models\MainUser;
use App\Models\ClassLession;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Storage;

class WithdrawRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
       
       return view("dashboard.withdraw-request.list", compact("start", "end"));
       
    }

    public function indexWithdrawHistory(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
       
       return view("dashboard.withdraw-request.history_list", compact("start", "end"));
       
    }

    
    public function show($id)
    {
        $withdraw_history = WithdrawHistory::find($id);
        $Users = MainUser::where('user_type',3)->get();
        $setting = Setting::find(1);
    
        return view("dashboard.withdraw-request.show", compact("withdraw_history","Users","setting"));
    }

    public function showWithdrawHistory($id)
    {
        $withdraw_history = WithdrawHistory::find($id);
        $Users = MainUser::where('user_type',3)->get();
        $setting = Setting::find(1);
    
        return view("dashboard.withdraw-request.history_show", compact("withdraw_history","Users","setting"));
    }

    public function withdrawRequestUpdateAll(Request $request)
    {
        //
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $request_status = $request->request_status;

               
                if ($request_status == 2) {
                    WithdrawHistory::wherein('id', $ids)->update(['request_status' => 2]);
                    return response()->json(['success' => true, 'msg' => 'Request status will be denied successfully']);
                } else if ($request_status == 0) {
                    WithdrawHistory::wherein('id', $ids)->update(['request_status' => 0]);
                    return response()->json(['success' => true, 'msg' => 'Request status will be requested successfully']);
                } else {
                    WithdrawHistory::wherein('id', $ids)->update(['request_status' => 1]);
                    return response()->json(['success' => true, 'msg' => 'Request status will be paid successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
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

        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'withdraw_history.id';
        } elseif ($columnIndex == 1) {
            $sort = 'main_users.name';
        } elseif ($columnIndex == 2) {
            $sort = 'withdraw_history.created_at';
        } elseif ($columnIndex == 3) {
            $sort = 'withdraw_history.balance';
        } elseif ($columnIndex == 4) {
            $sort = 'withdraw_history.amount';
        } elseif ($columnIndex == 5) {
            $sort = 'withdraw_history.request_status';
        } else {
            $sort = 'withdraw_history.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }


                if (!empty($request->startdate)) {


                    $totalAr = \DB::table('withdraw_history')
                        ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
                        ->select('withdraw_history.*','main_users.name')
                        ->where('main_users.user_type','=',3)
                        ->where('withdraw_history.request_status', '!=', 1)
                        ->where('main_users.status', '!=', 2);
                    // $frm_date = date('Y-m-d',strtotime($request->startdate));
                    // $to_date = date('Y-m-d', strtotime($request->enddate . ' +1 day'));
        
                    $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
        
                    $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
        
                    $totalAr = $totalAr->whereDate('withdraw_history.created_at', '>=', $frm_date);
                    $totalAr = $totalAr->whereDate('withdraw_history.created_at', '<=', $to_date);
        
        
                    
                    if ($searchValue != "") {

                            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                                $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%');
                            });
                        
                    }
        
        
                    $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

                    $totalAr = $totalAr->orderBy($sort, $sortBy)
                        ->skip($start)
                        ->take($rowperpage)
                        ->groupby('withdraw_history.id')
                        ->get();

                } else {
        
                    $totalAr = \DB::table('withdraw_history')
                        ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
                        ->select('withdraw_history.*','main_users.name')
                        ->where('main_users.user_type','=',3)
                        ->where('withdraw_history.request_status', '!=', 1)
                        ->where('main_users.status', '!=', 2);
        
        
        
                    if ($searchValue != "") {

                            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                                $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%');
                            });
                        
                    }
        
        
                    $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

                    $totalAr = $totalAr->orderBy($sort, $sortBy)
                        ->skip($start)
                        ->take($rowperpage)
                        ->groupby('withdraw_history.id')
                        ->get();
                }        
        

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $dl = '';
        $ps = '';
        $pc = '';
        $ctname = '';
    
        foreach ($totalAr as $key => $data) {
            $instructor_id = isset($data->instructor_id) ? $data->instructor_id : '';
           // $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
            $createddate = \Helper::converttimeTozone($data->created_at);
           
            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

            $balance = isset($data->balance) ? $data->balance : '0.0';
            $amount = isset($data->amount) ? $data->amount : '0.0';

            $users = MainUser::where('id',$instructor_id)->first();
            $uname = isset($users->name) ? $users->name : ''; 

            $request_status = isset($data->request_status) ? $data->request_status : '';
           
            // if ($data->status == 3) {
            //     $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            // } elseif($data->status == 4) {

            //     $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            // }

            if($request_status == 0)
            {
                $ps = '<button type="button" class="btn btn-warning" style="cursor:text;">Requested</button>';
            }
            elseif($request_status == 1)
            {
                $ps = '<button type="button" class="btn btn-success" style="cursor:text;">Paid</button>';
            }
            else
            {
                $ps = '<button type="button" class="btn btn-danger" style="cursor:text;">Denied</button>';
            }


            $classShow =  route('withdraw-request.show', ['id' => $data->id]);
           // $classEdit =  route('withdraw-request.edit', ['id' => $data->id]);


            $options = '<a  class="btn btn-sm show-eyes list" href="' . $classShow . '" title="View"> </a>';

        //    $options .= '<a  class="btn btn-sm success paddingset" href="' . $classEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
        //    $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "uname" =>   $uname,
                "createddate" => $createddate,
                "balance" => $currency.' '.$balance.'.00',
                "amount" => $currency.' '.$amount.'.00',
                "request_status" => $ps,
                "options" => $options
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

    public function anyDataWithdrawHistory(Request $request)
    {

        $draw = $request->get('draw');

        $start = $request->get("start");

        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'withdraw_history.id';
        } elseif ($columnIndex == 1) {
            $sort = 'main_users.name';
        } elseif ($columnIndex == 2) {
            $sort = 'withdraw_history.created_at';
        } elseif ($columnIndex == 3) {
            $sort = 'withdraw_history.balance';
        } elseif ($columnIndex == 4) {
            $sort = 'withdraw_history.amount';
        } elseif ($columnIndex == 5) {
            $sort = 'withdraw_history.request_status';
        } else {
            $sort = 'main_users.name';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }


                if (!empty($request->startdate)) {


                    $totalAr = \DB::table('withdraw_history')
                        ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
                        ->select('withdraw_history.*','main_users.name', 'main_users.user_type', 'main_users.status')
                        ->where('main_users.user_type','=',3)
                        ->where('withdraw_history.request_status', '=', 1)
                        ->where('main_users.status', '!=', 2);
                    // $frm_date = date('Y-m-d',strtotime($request->startdate));
                    // $to_date = date('Y-m-d', strtotime($request->enddate . ' +1 day'));
        
                    $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
        
                    $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
        
                    $totalAr = $totalAr->whereDate('withdraw_history.created_at', '>=', $frm_date);
                    $totalAr = $totalAr->whereDate('withdraw_history.created_at', '<=', $to_date);
        
        
                    
                    if ($searchValue != "") {

                            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                                $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%');
                            });
                        
                    }
        
        
                    $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

                    $totalAr = $totalAr->orderBy($sort, $sortBy)
                        ->skip($start)
                        ->take($rowperpage)
                        ->groupby('withdraw_history.id')
                        ->get();

                } else {
        
                    $totalAr = \DB::table('withdraw_history')
                        ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
                        ->select('withdraw_history.*','main_users.name', 'main_users.user_type', 'main_users.status')
                        ->where('main_users.user_type','=',3)
                        ->where('withdraw_history.request_status', '=', 1)
                        ->where('main_users.status', '!=', 2);
        
        
        
                    if ($searchValue != "") {

                            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                                $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%');
                            });
                        
                    }
        
        
                    $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

                    $totalAr = $totalAr->orderBy($sort, $sortBy)
                        ->skip($start)
                        ->take($rowperpage)
                        ->groupby('withdraw_history.id')
                        ->get();

                       // dd($totalAr);
                }        
        

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $dl = '';
        $ps = '';
        $pc = '';
        $ctname = '';
    
        foreach ($totalAr as $key => $data) {
            $instructor_id = isset($data->instructor_id) ? $data->instructor_id : '';
           // $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
            $createddate = \Helper::converttimeTozone($data->created_at);
           
            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

            $balance = isset($data->balance) ? $data->balance : '0.0';
            $amount = isset($data->amount) ? $data->amount : '0.0';

            $users = MainUser::where('id',$instructor_id)->first();
            $uname = isset($users->name) ? $users->name : ''; 

            $request_status = isset($data->request_status) ? $data->request_status : '';
           
            // if ($data->status == 3) {
            //     $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            // } elseif($data->status == 4) {

            //     $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            // }

            if($request_status == 0)
            {
                $ps = '<button type="button" class="btn btn-warning" style="cursor:text;">Requested</button>';
            }
            elseif($request_status == 1)
            {
                $ps = '<button type="button" class="btn btn-success" style="cursor:text;">Paid</button>';
            }
            else
            {
                $ps = '<button type="button" class="btn btn-danger" style="cursor:text;">Denied</button>';
            }


            $classShow =  route('withdraw-history.show', ['id' => $data->id]);
           // $classEdit =  route('withdraw-request.edit', ['id' => $data->id]);


            $options = '<a  class="btn btn-sm show-eyes list" href="' . $classShow . '" title="View"> </a>';

        //    $options .= '<a  class="btn btn-sm success paddingset" href="' . $classEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
        //    $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "uname" =>   $uname,
                "createddate" => $createddate,
                "balance" => $currency.' '.$balance.'.00',
                "amount" => $currency.' '.$amount.'.00',
                "request_status" => $ps,
                "options" => $options
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
