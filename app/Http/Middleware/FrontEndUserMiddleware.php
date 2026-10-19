<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\MainUser;
use App\Models\SchoolLocation;
use App\Models\StaffDetails;

class FrontEndUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if(Auth::check())
        {

            $user_status=Auth::user()->status;

            if($user_status!=1)
            {
                Auth::guard('main_user')->logout();
                return redirect()->route('frontend.loginpage')->with('error','Your accout has been Inactivated.');
            }
            $authID = Auth::user()->id;
            $user = MainUser::where('id',$authID)->where('status','!=',2)->first();

            if(isset($user) && !empty($user))
            {
                $user_type = $user->user_type;
                if($user_type == 1)
                {
                    $schoolLocationCheck = SchoolLocation::where('school_admin_id',$authID)->where('status','!=',2)->get();
                    if(isset($schoolLocationCheck) && count($schoolLocationCheck) > 0)
                    {
                        return $next($request);
                    }
                    else
                    {
                        Auth::guard('main_user')->logout();
                        return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                    }
                }
                else
                {
                    $staffDetails = StaffDetails::where('user_id',$authID)->where('status',1)->first();
                     
                    if(isset($staffDetails) && !empty($staffDetails))
                    {
                        $schoolLocationCheck = SchoolLocation::where('id',$staffDetails->location_id)->where('status','!=',2)->get();

                        if(isset($schoolLocationCheck) && count($schoolLocationCheck) > 0)
                        {
                            return $next($request);
                        }
                        else
                        {
                            Auth::guard('main_user')->logout();
                            return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                        }
                    }
                    else
                    {
                        Auth::guard('main_user')->logout();
                        return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                    }

                }
            }
            else
            {
                $user_type = $user->user_type;
                if($user_type == 1)
                {
                    $schoolLocationCheck = SchoolLocation::where('school_admin_id',$authID)->where('status','!=',2)->get();
                    if(isset($schoolLocationCheck) && count($schoolLocationCheck) > 0)
                    {
                        return $next($request);
                    }
                    else
                    {
                        Auth::guard('main_user')->logout();
                        return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                    }
                }
                else
                {
                    $staffDetails = StaffDetails::where('user_id',$authID)->where('status',1)->first();
                     
                    if(isset($staffDetails) && !empty($staffDetails))
                    {
                        $schoolLocationCheck = SchoolLocation::where('id',$staffDetails->location_id)->where('status','!=',2)->first();

                        if(isset($schoolLocationCheck) && count($schoolLocationCheck) > 0)
                        {
                            return $next($request);
                        }
                        else
                        {
                            Auth::guard('main_user')->logout();
                            return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                        }
                    }
                    else
                    {
                        Auth::guard('main_user')->logout();
                        return redirect()->route('frontend.loginpage')->with('error','Your location has been deleted. please contact to onlydance admin.');
                    }

                }
            }
            
        }
    }
}
