<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Alert;
use DB;
use App\Models\MainUser;

class wholesalerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id =  isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';

        $userData = DB::table('main_users')->where('id',$user_id)->first();
        // echo "<pre>";print_r($user_id);exit;
        if (!empty($user_id)) {
            if ($userData->status != 1) {
           // echo "string";exit();
             Alert::warning('Warning',__('backend.your_account_is_inactive'));
           // return route('user-logout'); 
             // return redirect()->route('user-logout');
             Auth::guard('main_user')->logout();

             // return route('frontend.home');
             return redirect()->route('wholesalerlogin');
        }else{
           // echo "string3";exit();
            return $next($request);
        }
        }else{
            Alert::warning('Warning',__('backend.your_account_is_inactive'));
           // return route('user-logout'); 
             // return redirect()->route('user-logout');
             Auth::guard('main_user')->logout();

             // return route('frontend.home');
             return redirect()->route('wholesalerlogin');
        }
           // echo "string2";exit();
            return $next($request);
        
       
       
    }

}
