<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Services\GuestUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GuestAuthController extends Controller
{
    /**
     * Check whether an email or phone number is registered to an existing account.
     */
    public function checkExistingUser(Request $request, GuestUserService $guestUserService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'exists'        => false,
                    'is_registered' => false,
                    'email_exists'  => false,
                    'phone_exists'  => false,
                    'matched_by'    => null,
                    'message'       => null,
                ]);
            }

            $email = $request->input('email');
            $phone = $request->input('phone');

            $result = $guestUserService->checkExistingUser($email, $phone);

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Error checking existing user during guest checkout: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'exists'        => false,
                'is_registered' => false,
                'email_exists'  => false,
                'phone_exists'  => false,
                'matched_by'    => null,
                'message'       => null,
            ]);
        }
    }

    /**
     * Inline login from checkout modal without leaving the checkout page.
     */
    public function inlineLogin(Request $request, GuestUserService $guestUserService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => ['required', 'string', 'max:255'],
                'password' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide valid credentials.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $loginInput = strtolower(trim($request->input('email')));
            $password   = $request->input('password');

            $user = MainUser::where(function ($q) use ($loginInput) {
                $q->whereRaw('LOWER(email) = ?', [$loginInput])
                  ->orWhere('phone', $loginInput);
            })
            ->where('user_type', 1)
            ->first();

            if ($user && $user->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is deactivated. Please contact support.',
                ], 403);
            }

            // Attempt authentication via email or phone
            $authenticated = Auth::guard('user')->attempt([
                'email'     => $loginInput,
                'password'  => $password,
                'status'    => 1,
                'user_type' => 1,
            ]) || Auth::guard('user')->attempt([
                'phone'     => $loginInput,
                'password'  => $password,
                'status'    => 1,
                'user_type' => 1,
            ]);

            if ($authenticated) {
                $request->session()->regenerate();
                $authenticatedUser = Auth::guard('user')->user();

                // Link previous guest orders matching this user's email
                try {
                    $guestUserService->linkGuestOrdersToUser($authenticatedUser);
                } catch (\Throwable $e) {
                    Log::error('Error linking guest orders on inline login: ' . $e->getMessage());
                }

                // Merge session cart items into DB cart
                try {
                    \Helper::afterLoginAddUserCartItemData();
                } catch (\Throwable $e) {
                    Log::warning('Cart merge error after inline login: ' . $e->getMessage());
                }

                return response()->json([
                    'success'  => true,
                    'message'  => 'Logged in successfully.',
                    'redirect' => route('checkout'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Your email or password is invalid.',
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Inline login error: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
