<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

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
        // Check if user is authenticated under 'user' guard
        // dd(isset(auth()->guard('user')->user()->id));
        if (!Auth::guard('user')->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue.',
                    'redirect' => route('websitelogin')
                ], 401);
            }

            // Save intended URL for redirecting back after login (if GET request)
            if ($request->isMethod('get')) {
                session()->put('url.intended', $request->fullUrl());
            }

            Alert::warning('Login Required', 'Please login to access this page.');
            return redirect()->route('websitelogin');
        }

        $user = Auth::guard('user')->user();

        // Check if user status is active (status 1 = active)
        if ($user->status != 1) {
            Auth::guard('user')->logout();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('backend.your_account_is_inactive'),
                    'redirect' => route('websitelogin')
                ], 403);
            }

            Alert::warning('Warning', __('backend.your_account_is_inactive'));
            return redirect()->route('websitelogin');
        }

        return $next($request);
    }
}

