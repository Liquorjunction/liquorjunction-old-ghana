<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use App\Models\DanceClass;
use App\Models\DanceCategory;
use Yajra\Datatables\Datatables;
use \Carbon\Carbon;
use App\Models\MainUser;
use App\Models\WithdrawHistory;
use App\Models\ClassPurchaseHistory;
use App\Models\Setting;
use PDF;


class GenerateReportController extends Controller
{
    // Define Default Variables
    public $currency;

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,15,'read');
        if($check_view_permission==false){
            abort(404);
        } 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.report.users", compact("start", "end"));
    }

    public function usersExportPdf (Request $request) 
    {
        if (!empty($request->startdate && $request->enddate)) {
            $setting = Setting::find(1);
            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.status', 'main_users.created_at', 'main_users.user_type','class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id')->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.user-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'user_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        } else 
        {
            $setting = Setting::find(1);
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.status', 'main_users.created_at', 'main_users.user_type', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id')->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2)->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.user-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'user_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        }

    }

    public function usersExport(Request $request)
    {
        if (!empty($request->startdate1 && $request->enddate1)) {
            
            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate1)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate1)->format('Y-m-d');


            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code', 'main_users.user_type', 'main_users.user_type','main_users.profile', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id') ->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        } else {
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.user_type', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id') ->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2)->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        }


        $setting = Setting::find(1);
        $utype = "";
        $filename = 'user_report' .'-'. date('d/m/Y') . '.xlsx';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('User Type', 'Name', 'Email', 'Phone', 'Total Class Purchase', 'Created Date/Time', 'Total Earns from User'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $user_type = isset($data->user_type) ? $data->user_type : '';
            $fullname = isset($data->name) ? $data->name : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $email = isset($data->email) ? $data->email : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $phone = '+'.$country_code.' '.$mobile_number;

            if($user_type == 2)
            {
                $utype = 'Normal';
            }
            elseif($user_type == 3)
            {
                $utype = 'Instructor';
            }
            else
            {
                $utype = 'Admin';
            }

            $tot_class_sub = ClassPurchaseHistory::where('purchase_user_id',$data->purchase_user_id)->count();
            $tot_amt = \DB::table('class_purchase_history')
                        ->where('purchase_user_id',$data->purchase_user_id)
                        ->sum('admin_commission_amount');
            $amt = $tot_amt.'.00';            

            $createddate = isset($data->created_at) ? $data->created_at : '';
            $date = \Helper::converttimeTozone($createddate);
          //  $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));
            fputcsv($file, array($utype, $fullname, $email, $phone, $tot_class_sub, $date, $amt));

            $i++;
        }
    }

    public function userAnyData(Request $request)
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
            $sort = 'main_users.id';
        } elseif ($columnIndex == 1) {
            $sort = 'main_users.user_type';
        } elseif ($columnIndex == 2) {
            $sort = 'main_users.name';
        } elseif ($columnIndex == 3) {
            $sort = 'main_users.email';
        } elseif ($columnIndex == 4) {
            $sort = 'main_users.phone';
        } elseif ($columnIndex == 5) {
            $sort = 'class_purchase_history.class_id';
        } elseif ($columnIndex == 6) {
            $sort = 'class_purchase_history.created_at';
        } elseif ($columnIndex == 7) {
            $sort = 'class_purchase_history.admin_commission_amount';
        } else {
            $sort = 'main_users.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        /*$data = MainUser::where('user_type',1)->where('status','!=',2);*/
        if (!empty($request->startdate)) {


            $totalAr = \DB::table('main_users')->select('main_users.id', 'main_users.name', 'main_users.email', 'main_users.phone', 'main_users.status', 'main_users.country_code', 'main_users.profile', 'main_users.created_at', 'main_users.user_type', 'class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.purchase_user_id','class_purchase_history.admin_commission_amount') ->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2);
            // $frm_date = date('Y-m-d',strtotime($request->startdate));
            // $to_date = date('Y-m-d', strtotime($request->enddate . ' +1 day'));

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);


            if ($searchValue != "") {
                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $search = "";
                    if($searchValue == 'Normal' || $searchValue == 'normal')
                    {
                        $search = '2';
                        $query->orWhere('main_users.user_type', 'like', '%' . $search . '%')
                                ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                                ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                            ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%');
                    }
                    else
                    {
                        $query->orWhere('main_users.user_type', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                            ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%');
                    }
                });
            }


            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();

        } else {

            $totalAr = \DB::table('main_users')->select('main_users.id', 'main_users.name', 'main_users.email', 'main_users.phone', 'main_users.status', 'main_users.country_code', 'main_users.profile', 'main_users.created_at', 'main_users.user_type', 'class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.purchase_user_id','class_purchase_history.admin_commission_amount')
            ->join('class_purchase_history', 'class_purchase_history.purchase_user_id', '=', 'main_users.id')
            ->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 2);
               
                if ($searchValue != "") 
                {
                    $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $search = "";
                    if($searchValue == 'Normal' || $searchValue == 'normal')
                    {
                        $search = '2';
                       
                        $query->orWhere('main_users.user_type', 'like', '%' . $search . '%')
                                ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                                ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                                ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                            ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%');
                    }
                    else
                    {
                        $query->orWhere('main_users.user_type', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                               ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                            ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%');
                    }
                });
            }


            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();

        }

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $image = "";
        $imagefile = "";
        $tot_class_sub = "";
        $status = "";
        $utype = "";
        if ($start == 0) {
            $i = 1;
        } else {
            $i = $start + 1;
        }
        $i;
       //dd($totalAr);
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $user_type = isset($data->user_type) ? $data->user_type : '';
            $fullname = isset($data->name) ? $data->name : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $email = isset($data->email) ? $data->email : '';
            $commission_amount = isset($data->admin_commission_amount) ? $data->admin_commission_amount : '0.0';
            $amount = (int)$commission_amount;

            if($user_type == 2)
            {
                $utype = 'Normal';
            }
            elseif($user_type == 3)
            {
                $utype = 'Instructor';
            }
            else
            {
                $utype = 'Admin';
            }

            $tot_amt = \DB::table('class_purchase_history')
                        ->where('purchase_user_id',$data->purchase_user_id)
                        ->sum('admin_commission_amount');
            // dd($tot_amt);            
           // dd($data->profile);
            if($data->profile !=""){
                $imagefile= asset('uploads/website_users/').'/'.$data->profile;
               // dd($imagefile);
                $image = '<img  src="'.$imagefile.'" class="thumbnail" width="100px" height="100px"/>';
               // dd($image);
            }
            else{
                $imagefile =  asset('uploads/contacts/noimage.png');
                $image = '<img  src="'.$imagefile.'" class="thumbnail" width="100px" height="100px;"/>';
            }
           // dd("dssads");
            $cid = isset($data->class_id) ? $data->class_id : '';
            $uid = isset($data->purchase_user_id) ? $data->purchase_user_id : '';
            // if($uid == $data->purchase_user_id)
            // {
            //dd(1);    
            $tot_class_sub = ClassPurchaseHistory::where('purchase_user_id',$uid)->count();
                // $class_sub = $tcs->class_id;
                // $tot_class_sub = count($class_sub);
            // }
            //dd($tot_class_sub);
            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

           // $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
            $createddate = \Helper::converttimeTozone($data->created_at);
            if ($data->status == 3) {
                $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            } else if($data->status == 4) {

                $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            }

            //dd($data->status);
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
                "user_type" =>   $utype,
                "fullname" =>   $fullname,
                "email" => $email,
                "mobile_number" => '+'.$country_code.' '.$mobile_number,
                "tot_class_sub" => isset($tot_class_sub) ? $tot_class_sub : '0',
                "createddate" => $createddate,
                "tot_earns" => isset($tot_amt) ? $currency.' '.$tot_amt.'.00' : '0.0',
                // "status" => $status
            );
          //  $i++;
        }
        
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );
       
        echo json_encode($response);
    }


    // Instructor Reoprt

    public function instructor(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.report.instructor", compact("start", "end"));
    }

    public function instructorExportPdf (Request $request) 
    {
        if (!empty($request->startdate && $request->enddate)) {
            $setting = Setting::find(1);
            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.status', 'main_users.created_at', 'main_users.user_type','class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.instructor-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'instructor_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        } else 
        {
            $setting = Setting::find(1);
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.status', 'main_users.created_at', 'main_users.user_type','class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id', 'class_purchase_history.instructor_amount', 'class_purchase_history.class_id','class_purchase_history.user_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3)->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.instructor-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'instructor_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        }

    }

    public function instructorExport(Request $request)
    {
        if (!empty($request->startdate1 && $request->enddate1)) {

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate1)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate1)->format('Y-m-d');


            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.user_type','main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        } else {
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.user_type','main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id', 'class_purchase_history.instructor_amount', 'class_purchase_history.class_id','class_purchase_history.user_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        }


        $setting = Setting::find(1);
        $gender = '';
        $popular = '';
        $utype = '';
        $filename = 'instructor_report' .'-'. date('d/m/Y') . '.xlsx';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        // fputcsv($file, array('User Type', 'Name', 'Email', 'Phone', 'Joined Since', 'Gender', 'Dance Category', 'Dance Group Name', 'Facebook Link', 'Instagram Link', 'Tiktok Link', 'Web Link', 'Location', 'Popular Instructor', 'Total Class Added', 'Total Earns from Platform'));

        fputcsv($file, array('User Type', 'Name', 'Email', 'Phone', 'Created Date/Time', 'Dance Category', 'Total Class Added', 'Total Earns from Platform'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $user_type = isset($data->user_type) ? $data->user_type : '';
            $fullname = isset($data->name) ? $data->name : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $address = isset($data->address) ? $data->address : '';
            $email = isset($data->email) ? $data->email : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $phone = '+'.$country_code.' '.$mobile_number;

            if($user_type == 2)
            {
                $utype = 'Normal';
            }
            elseif($user_type == 3)
            {
                $utype = 'Instructor';
            }
            else
            {
                $utype = 'Admin';
            }

            $dance_group_name = isset($data->dance_group_name) ? $data->dance_group_name : '';
            $instructor_location = isset($data->instructor_location) ? $data->instructor_location : '';

            $category_dance_instructor = isset($data->category_dance_instructor) ? $data->category_dance_instructor : '';

            $is_popular_instructor = isset($data->is_popular_insructor) ? $data->is_popular_insructor : '';

            if($is_popular_instructor == 1)
            {
                $popular = "Yes";
            }
            else
            {
                $popular = "No";
            }

            $instructor_facebook_link = isset($data->instructor_facebook_link) ? $data->instructor_facebook_link : '';
            $instructor_instagram_link = isset($data->instructor_instagram_link) ? $data->instructor_instagram_link : '';
            $instructor_tiktok_link = isset($data->instructor_tiktok_link) ? $data->instructor_tiktok_link : '';
            $instructor_web_link = isset($data->instructor_web_link) ? $data->instructor_web_link : '';

            if($category_dance_instructor == 1)
            {
                $gender = "Male";
            }
            else
            {
                $gender = "Female";
            }

            $dance_class = DanceClass::where('id',$data->class_id)->first(); 
            $dance_category = DanceCategory::where('id',$dance_class->dance_category_id)->first();
            $cname = $dance_category->category_name;

            $tot_class_sub = ClassPurchaseHistory::where('user_id',$data->user_id)->count();
            $tot_amt = \DB::table('class_purchase_history')
                            ->where('user_id',$data->user_id)
                            ->sum('instructor_amount');
            $amt = $tot_amt.'.00';                

            $createddate = isset($data->created_at) ? $data->created_at : '';
            $date = \Helper::converttimeTozone($createddate);
           // $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));

            // fputcsv($file, array($i, $fullname, $email, $phone, $date, $gender, $cname, $dance_group_name, $instructor_facebook_link, $instructor_instagram_link, $instructor_tiktok_link, $instructor_web_link, $instructor_location, $popular, $tot_class_sub, $amt));

            fputcsv($file, array($utype, $fullname, $email, $phone, $date, $cname, $tot_class_sub, $amt));

            $i++;
        }
    }

    public function instructorAnyData(Request $request)
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
            $sort = 'main_users.id';
        } elseif ($columnIndex == 1) {
            $sort = 'main_users.user_type';
        } elseif ($columnIndex == 2) {
            $sort = 'main_users.name';
        } elseif ($columnIndex == 3) {
            $sort = 'main_users.email';
        } elseif ($columnIndex == 4) {
            $sort = 'main_users.phone';
        } elseif ($columnIndex == 5) {
            $sort = 'main_users.created_at';
        } elseif ($columnIndex == 6) {
            $sort = 'dance_category.category_name';
        } elseif ($columnIndex == 7) {
            $sort = 'class_purchase_history.class_id';
        } elseif ($columnIndex == 8) {
            $sort = 'class_purchase_history.instructor_amount';
        } else {
            $sort = 'main_users.name';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        /*$data = MainUser::where('user_type',1)->where('status','!=',2);*/
        if (!empty($request->startdate)) {


            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.user_type','main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.user_id','class_purchase_history.class_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3);
            // $frm_date = date('Y-m-d',strtotime($request->startdate));
            // $to_date = date('Y-m-d', strtotime($request->enddate . ' +1 day'));

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $search = "";
                    if($searchValue == 'Instructor' || $searchValue == 'instructor')
                    {
                        $search = '3';
                        $query->orWhere('main_users.user_type', 'like', '%' . $search . '%')
                           ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('dance_category.category_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%');
                    }
                    else
                    {
                        $query->orWhere('main_users.user_type', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('dance_category.category_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%');
                    }
                });
            }


            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();
        } else {

            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.email', 'main_users.phone', 'main_users.country_code','main_users.profile', 'main_users.user_type', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.user_id','class_purchase_history.class_id','main_users.about_me','main_users.instructor_since','main_users.category_dance_instructor','main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_tiktok_link','main_users.instructor_web_link','main_users.instructor_location','main_users.dance_group_name','main_users.instructor_portfolio_image','main_users.instructor_portfolio_video','main_users.is_verify_instructor','main_users.is_verify_instructor','main_users.is_popular_insructor')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->where('main_users.user_type', '=', 3);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $search = "";
                    if($searchValue == 'Instructor' || $searchValue == 'instructor')
                    {
                        $search = '3';
                        $query->orWhere('main_users.user_type', 'like', '%' . $search . '%')
                           ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('dance_category.category_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%');
                    }
                    else
                    {
                        $query->orWhere('main_users.user_type', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.email', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.phone', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('dance_category.category_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%');
                    }
                });
            }






            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();
        }

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $utype = "";
        $image = "";
        $imagefile = "";
        $tot_class_sub = "";
        $status = "";
        if ($start == 0) {
            $i = 1;
        } else {
            $i = $start + 1;
        }
        $i;
       //dd($totalAr);
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $user_type = isset($data->user_type) ? $data->user_type : '';
            $fullname = isset($data->name) ? $data->name : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $email = isset($data->email) ? $data->email : '';
            $commission_amount = isset($data->instructor_amount) ? $data->instructor_amount : '0.0';
            $amount = (int)$commission_amount;

            $tot_amt = \DB::table('class_purchase_history')
                        ->where('user_id',$data->user_id)
                        ->sum('instructor_amount');

            if($user_type == 2)
            {
                $utype = 'Normal';
            }
            elseif($user_type == 3)
            {
                $utype = 'Instructor';
            }
            else
            {
                $utype = 'Admin';
            }            
            // dd($tot_amt);            
           // dd($data->profile);
            if($data->profile !=""){
                $imagefile= asset('uploads/website_users/').'/'.$data->profile;
               // dd($imagefile);
                $image = '<img  src="'.$imagefile.'" class="thumbnail" width="100px" height="100px"/>';
               // dd($image);
            }
            else{
                $imagefile =  asset('uploads/contacts/noimage.png');
                $image = '<img  src="'.$imagefile.'" class="thumbnail" width="100px" height="100px;"/>';
            }
           // dd("dssads");
            $cid = isset($data->class_id) ? $data->class_id : '';
            $uid = isset($data->user_id) ? $data->user_id : '';
            // if($uid == $data->purchase_user_id)
            // {
            //dd(1);    
            $tot_class_sub = ClassPurchaseHistory::where('user_id',$uid)->count();
                // $class_sub = $tcs->class_id;
                // $tot_class_sub = count($class_sub);
            // }
            //dd($tot_class_sub);
            $dance_class = DanceClass::where('id',$cid)->first();
            $dance_category = DanceCategory::where('id',$dance_class->dance_category_id)->first();
            $dc = $dance_category->category_name;

            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

           // $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
            $createddate = \Helper::converttimeTozone($data->created_at);
            if ($data->status == 3) {
                $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            } else if($data->status == 4) {

                $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            }

            //dd($data->status);
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
                "user_type" =>   $utype,
                "fullname" =>   $fullname,
                "email" => $email,
                "mobile_number" => '+'.$country_code.' '.$mobile_number,
                "createddate" => $createddate,
                "dance_category" => $dc,
                "tot_class_sub" => isset($tot_class_sub) ? $tot_class_sub : '0',
                "tot_earns" => isset($tot_amt) ? $currency.' '.$tot_amt.'.00' : '0.0',
            );
          //  $i++;
        }
        
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );
       
        echo json_encode($response);
    }


    //Earning Report

    public function earning(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.report.earning", compact("start", "end"));
    }

    public function earningExportPdf (Request $request) 
    {
        if (!empty($request->startdate && $request->enddate)) {
            $setting = Setting::find(1);
            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.earning-export-pdf', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'earning_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        } else 
        {
            $setting = Setting::find(1);
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2)->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.earning-export-pdf', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'earning_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        }

    }

    public function earningExport(Request $request)
    {
        if (!empty($request->startdate1 && $request->enddate1)) {

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate1)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate1)->format('Y-m-d');


            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        } else {
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->orderBy('main_users.id', 'DESC')->groupby('main_users.id')->get();
        }



        $filename = 'earning_report' .'-'. date('d/m/Y') . '.xlsx';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('Class Name', 'Instructor Name', 'Purchase User', 'Purchase Date', 'Instructor Amount', 'Commission Amount', 'Class Price'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $class_name = isset($data->class_id) ? $data->class_id : '';
            
            $user_name = isset($data->user_id) ? $data->user_id : '';

            $purchase_user_name = isset($data->purchase_user_id) ? $data->purchase_user_id : '';
            
            $price = isset($data->total_amount) ? $data->total_amount : '0.0';
            
            $admin_commission_amount = isset($data->admin_commission_amount) ? $data->admin_commission_amount : '0.0';

            $tot_class_sub = ClassPurchaseHistory::where('purchase_user_id',$data->purchase_user_id)->count();

            $instructor_amount = isset($data->instructor_amount) ? $data->instructor_amount : '0.0';

            $cl_name = DanceClass::where('id',$class_name)->first();
            $cname = isset($cl_name->class_name) ? $cl_name->class_name : '';

            $cprice = isset($cl_name->price) ? $cl_name->price : '';
            
            $u_name = MainUser::where('id',$user_name)->first();
            $uname = isset($u_name->name) ? $u_name->name : '';

            $all_u_name = MainUser::where('id',$purchase_user_name)->first();
            $alluname = isset($all_u_name->name) ? $all_u_name->name : '';

            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;


            $createddate = isset($data->purchase_date) ? $data->purchase_date : '';
            $date = \Helper::converttimeTozone($createddate);
          //  $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));
            fputcsv($file, array($cname, $uname, $alluname, $date, $instructor_amount.'.00', $admin_commission_amount.'.00', $cprice.'.00'));

            $i++;
        }
    }

    public function earningAnyData(Request $request)
    {
        $draw = $request->get('draw');

        $start = $request->get("start");

        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        // echo "<pre>";print_r($request->all());exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'main_users.id';
        } elseif ($columnIndex == 1) {
            $sort = 'class.class_name';
        } elseif ($columnIndex == 2) {
            $sort = 'main_users.name';
        } elseif ($columnIndex == 3) {
            $sort = 'class_purchase_history.created_at';
        } elseif ($columnIndex == 4) {
            $sort = 'class_purchase_history.instructor_amount';
        } elseif ($columnIndex == 5) {
            $sort = 'class_purchase_history.admin_commission_amount';
        } elseif ($columnIndex == 6) {
            $sort = 'class.price';
        } else {
            $sort = 'class.class_name';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        /*$data = MainUser::where('user_type',1)->where('status','!=',2);*/
        if (!empty($request->startdate)) {
            $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2);

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            $totalAr = $totalAr->whereDate('main_users.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('main_users.created_at', '<=', $to_date);


            if ($searchValue != "") {
                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('class.class_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('class.price', 'like', '%' . $searchValue . '%')
                           ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.total_amount', 'like', '%' . $searchValue . '%');
                });
            }


            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();
        } else {

             $totalAr = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.status', 'main_users.created_at', 'class_purchase_history.admin_commission_amount','class_purchase_history.purchase_user_id','class_purchase_history.instructor_amount','class_purchase_history.class_id','class_purchase_history.user_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '!=', 2);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('class.class_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                           ->orWhere('main_users.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                           ->orWhere('class.price', 'like', '%' . $searchValue . '%')
                           ->orWhere('class_purchase_history.admin_commission_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('class_purchase_history.total_amount', 'like', '%' . $searchValue . '%');
                });
            }

            $totalRecords = $totalAr->groupby('main_users.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('main_users.id')
                ->get();
        }

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        if ($start == 0) {
            $i = 1;
        } else {
            $i = $start + 1;
        }
        $i;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $class_name = isset($data->class_id) ? $data->class_id : '';
            
            $user_name = isset($data->user_id) ? $data->user_id : '';

            $purchase_user_name = isset($data->purchase_user_id) ? $data->purchase_user_id : '';
            
            $price = isset($data->total_amount) ? $data->total_amount : '0.0';
            
            $admin_commission_amount = isset($data->admin_commission_amount) ? $data->admin_commission_amount : '0.0';

            $tot_class_sub = ClassPurchaseHistory::where('purchase_user_id',$data->purchase_user_id)->count();

            $instructor_amount = isset($data->instructor_amount) ? $data->instructor_amount : '0.0';

            $cl_name = DanceClass::where('id',$class_name)->first();
            $cname = isset($cl_name->class_name) ? $cl_name->class_name : '';

            $cprice = isset($cl_name->price) ? $cl_name->price : '';
            
            $u_name = MainUser::where('id',$user_name)->first();
            $uname = isset($u_name->name) ? $u_name->name : '';

            $all_u_name = MainUser::where('id',$purchase_user_name)->first();
            $alluname = isset($all_u_name->name) ? $all_u_name->name : '';

            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

            //$createddate = isset($data->purchase_date) ? date('m-d-Y H:i:s', strtotime($data->purchase_date)) : '';
            $createddate = \Helper::converttimeTozone($data->created_at);

            if ($data->status == 3) {
                $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            } else if($data->status == 4) {

                $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            }



            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "class_name" => $cname,
                "user_name" => $uname,
                "purchase_user" => $alluname,
                "created" => $createddate,
                "instructor_amount" => $currency.' '.$instructor_amount.'.00',
                "admin_commission_amount" => $currency.' '.$admin_commission_amount.'.00',
                "cprice" => $currency.' '.$cprice.'.00'
                // "total_amount" => $currency.' '.$price.'.00',
                // "tot_class_sub" => isset($tot_class_sub) ? $tot_class_sub : '0'
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );

        echo json_encode($response);
    }


    //Withdraw History Report

    public function withdrawHistory(Request $request)
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.report.history", compact("start", "end"));
    }

    public function withdrawHistoryExportPdf (Request $request) 
    {
        if (!empty($request->startdate && $request->enddate)) {
            $setting = Setting::find(1);
            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');
            $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('withdraw_history.id', 'DESC')->groupby('withdraw_history.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.history-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'withdraw_history_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        } else 
        {
            $setting = Setting::find(1);
            $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2)->orderBy('withdraw_history.id', 'DESC')->groupby('withdraw_history.id')->get()->toArray();
            view()->share ('totalAr', $totalAr);
            $pdf = PDF::loadView ('dashboard.history-export-pdf-view', $totalAr)->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            $filename = 'withdraw_history_report' .'-'. date('d/m/Y') . '.pdf';
            return $pdf->download ($filename);
        }

    }

    public function withdrawHistoryExport(Request $request)
    {
        if (!empty($request->startdate1 && $request->enddate1)) {

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate1)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate1)->format('Y-m-d');

           $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '<=', $to_date);
            $totalAr = $totalAr->orderBy('withdraw_history.id', 'DESC')->groupby('withdraw_history.id')->get();

        } else {
            $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2);
            $totalAr = $totalAr->orderBy('withdraw_history.id', 'DESC')->groupby('withdraw_history.id')->get();
            
        }


        $rs = '';
        $filename = 'withdraw_history_report' .'-'. date('d/m/Y') . '.xlsx';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('Instructor Name', 'Requested Date', 'Requested Amount', 'Account Balance', 'Status'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $instructor_id = isset($data->instructor_id) ? $data->instructor_id : '';
            $balance = isset($data->balance) ? $data->balance : '0.0';
            $amount = isset($data->amount) ? $data->amount : '0.0';

            $users = MainUser::where('id',$instructor_id)->first();
            $uname = isset($users->name) ? $users->name : ''; 

            $request_status = isset($data->request_status) ? $data->request_status : '';
            if($request_status == 0)
            {
                $rs = "Requested";
            }
            elseif($request_status == 1)
            {
                $rs = "Paid";
            }
            else
            {
                $rs = "Denied";
            }

            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

            $createddate = isset($data->created_at) ? $data->created_at : '';
            $date = \Helper::converttimeTozone($createddate);
           // $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));

            fputcsv($file, array($uname, $date, $amount, $balance, $rs));

            $i++;
        }
    }

    public function withdrawHistoryAnyData(Request $request)
    {
        $draw = $request->get('draw');

        $start = $request->get("start");

        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        // echo "<pre>";print_r($request->all());exit;
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

        /*$data = MainUser::where('user_type',1)->where('status','!=',2);*/
        if (!empty($request->startdate)) {
            
            $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2);

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('withdraw_history.created_at', '<=', $to_date);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.request_status', 'like', '%' . $searchValue . '%');
                });
            }


            $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('withdraw_history.id')
                ->get();
        } else {

           $totalAr = \DB::table('withdraw_history')->select('withdraw_history.id','withdraw_history.instructor_id', 'withdraw_history.amount', 'withdraw_history.created_at','withdraw_history.balance','withdraw_history.request_status','main_users.name')->join('main_users', 'main_users.id', '=', 'withdraw_history.instructor_id')->where('main_users.status', '!=', 2);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('main_users.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.balance', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('withdraw_history.request_status', 'like', '%' . $searchValue . '%');
                });
            }

            $totalRecords = $totalAr->groupby('withdraw_history.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('withdraw_history.id')
                ->get();
        }

        // echo "<pre>";print_r($totalAr->toArray());exit;
        $data_arr = [];
        $ps = '';
        if ($start == 0) {
            $i = 1;
        } else {
            $i = $start + 1;
        }
        $i;
        foreach ($totalAr as $key => $data) {
            $no = $i;
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

            $data_arr[] = array(
                 "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                // "no" => $no,
                "instructor_name" =>   $uname,
                "created_date" => $createddate,
                "balance" => $currency.' '.$balance.'.00',
                "amount" => $currency.' '.$amount.'.00',
                "status" => $ps,
            );
            // $i++;
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
