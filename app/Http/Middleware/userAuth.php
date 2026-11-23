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
            $isGuestMode = $request->get('mode') === 'guest'
                || $request->input('mode') === 'guest'
                || session()->get('is_guest_checkout')
                || str_contains($request->header('referer') ?? '', 'mode=guest')
                || $request->route('id') === 'guest'
                || $request->route('id') === 'guest_bill'
                || $request->route('id') === '0'
                || $request->route('id') === 0;

            if ($isGuestMode) {
                session()->put('is_guest_checkout', true);

                $guestAllowedRoutes = [
                    'checkout',
                    'checkout/*',
                    'store-checkout',
                    'user-area-tax',
                    'selected-address',
                    'store-address',
                    'store-bill-address',
                    'get-address-details/*',
                    'getsubcatlist',
                    'getarealist',
                    'upadte-address',
                    'update-bill-address',
                    'apply-coupon',
                    'apply-reward',
                    'buy-now/*',
                    'move-buy-now-to-cart',
                ];
                foreach ($guestAllowedRoutes as $pattern) {
                    if ($request->is($pattern)) {
                        return $next($request);
                    }
                }
            }

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

            if ($request->is('checkout*') || session()->has('buy_now_info')) {
                session()->put('checkout_value', 1);
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

