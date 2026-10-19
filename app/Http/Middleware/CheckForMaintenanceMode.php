<?php

namespace App\Http\Middleware;
use Closure;
use Auth;
use App\Models\Setting;
use Illuminate\Http\Request;


class CheckForMaintenanceMode 
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::find(1);

        // Allow all admin routes to work
        if ($request->is('admin/*')) {
            return $next($request);
        }


        if ($setting && $setting->site_maintenance) {
            $user = Auth::user();
    
            // Allow logged-in admin-type users
            if ($user && in_array($user->user_type, [1, 2, 3])) {
                return $next($request);
            }
    
            // Show maintenance page to others
            return response()->view('maintenance');
        }
    
        return $next($request);
    }
}
