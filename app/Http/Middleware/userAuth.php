<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Alert;
use DB;
use App\Models\MainUser;

class userAuth
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
        $user_id =  isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';

        $userData = DB::table('main_users')->where('id',$user_id)->first();
        if (!empty($user_id)) {
            if ($userData->status != 1) {           
                Alert::warning('Warning',__('backend.your_account_is_inactive'));
                // return route('user-logout'); 
                // return redirect()->route('user-logout');
                Auth::guard('user')->logout();
                // return route('frontend.home');
                return redirect()->route('frontend.home');
            }else{
                return $next($request);
            }
        }
        // else{
        //     return redirect()->route('frontend.home');
        // }
        return $next($request);
    }

}
