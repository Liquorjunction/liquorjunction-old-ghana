<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Alert;
use DB;
use App\Models\MainUser;

class subAdminAuth
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
        $user_id =  isset(auth()->guard('web')->user()->id) ? auth()->guard('web')->user()->id : '';

        $userData = DB::table('users')->where('id',$user_id)->first();
        // echo "<pre>";print_r($user_id);exit;
        if (!empty($user_id)) {
            if ($userData->status != 1) {
           // echo "string";exit();
             Alert::warning('Warning',__('backend.your_account_is_inactive'));
           // return route('user-logout'); 
             // return redirect()->route('user-logout');
             Auth::guard('web')->logout();

             // return route('frontend.home');
             return redirect()->route('login');
        }else{
           // echo "string3";exit();
            return $next($request);
        }
        }else{
            Alert::warning('Warning',__('backend.your_account_is_inactive'));
           // return route('user-logout'); 
             // return redirect()->route('user-logout');
             Auth::guard('web')->logout();

             // return route('frontend.home');
             return redirect()->route('login');
        }
           // echo "string2";exit();
            return $next($request);
        
       
       
    }

}
