<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Auth;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use DB;
use Session;


class DeliveryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 30, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        $wholesalerData = DB::table('main_users')->where('status', '!=', '2')->where('user_type', 2)->get();
        return view("dashboard.product.list", compact('wholesalerData'));
    }

  
   
}
