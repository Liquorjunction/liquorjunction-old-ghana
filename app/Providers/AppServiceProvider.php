<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\Cart;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // \URL::forceScheme('https');
       Paginator::useBootstrap();

    View::composer('*', function ($view) {
        // Only proceed if buy_now_info and checkout_in_progress exist AND not on checkout page
        if (
            Session::has('buy_now_info') &&
            Session::has('checkout_in_progress') &&
            !Request::is('checkout*')
        ) {
            $user = Auth::guard('user')->user();
            $buyNowData = Session::get('buy_now_info');

            $product_id_encoded = $buyNowData['product_id'] ?? null;
            $variantId = $buyNowData['variantId'] ?? null;
            $quantity = (int)($buyNowData['quantity'] ?? 1);
            $is_bogo = isset($buyNowData['is_bogo']) && is_numeric($buyNowData['is_bogo']) ? (int)$buyNowData['is_bogo'] : 0;
            $is_offer = isset($buyNowData['is_offer']) && is_numeric($buyNowData['is_offer']) ? (int)$buyNowData['is_offer'] : 0;
            $offer_type = $buyNowData['offer_type'] ?? null;
            $discount_amount = $buyNowData['discount_amount'] ?? null;

            $product_id = base64_decode($product_id_encoded);

            if ($variantId && $product_id) {
                if (!$user) {
                    $cart = session()->get('cart_info', []);
                    $new_item = ['quantity' => $quantity, 'is_bogo' => $is_bogo];

                    // offer section
                    if (!$is_bogo && $is_offer) {
                        $new_item['offer_type'] = $offer_type;
                        $new_item['discount_amount'] = $discount_amount;
                        $new_item['is_offer'] = $is_offer;
                    }


                    if (isset($cart[$product_id][$variantId])) {
                        $cart[$product_id][$variantId]['quantity'] += $quantity;
                    } else {
                        $cart[$product_id][$variantId] = $new_item;
                    }
                    session(['cart_info' => $cart]);
                } else {
                    $variant = DB::table('product')
                        ->leftJoin('product_variants', 'product_variants.product_id', '=', 'product.id')
                        ->where('product.id', $product_id)
                        ->where('product_variants.id', $variantId)
                        ->select('product_variants.*')
                        ->first();

                    if ($variant) {
                        $price = $variant->variant_discounted_price > 0 ? $variant->variant_discounted_price : $variant->variant_price;
                        $total_price = $quantity * $price;

                        $cart = Cart::where('user_id', $user->id)
                            ->where('product_id', $product_id)
                            ->where('product_variant_id', $variantId)
                            ->where('status', 1)
                            ->first();

                        if ($cart) {
                            $cart->update([
                                'quantity' => $cart->quantity + $quantity,
                                'product_price' => $variant->variant_price,
                                'offer_price' => $variant->variant_discounted_price,
                                'total_price' => $total_price,
                                'is_bogo' => $is_bogo,
                                'is_offer' => $is_offer,
                                'offer_type' => $offer_type,
                                'discount_amount' => $discount_amount
                            ]);
                        } else {
                            Cart::create([
                                'uniqid' => uniqid(),
                                'product_id' => $product_id,
                                'product_variant_id' => $variantId,
                                'product_price' => $variant->variant_price,
                                'offer_price' => $variant->variant_discounted_price,
                                'quantity' => $quantity,
                                'total_price' => $total_price,
                                'user_id' => $user->id,
                                'order_type' => 1,
                                'is_bogo' => $is_bogo,
                                'is_offer' => $is_offer,
                                'offer_type' => $offer_type,
                                'discount_amount' => $discount_amount,
                                'status' => 1
                            ]);
                        }
                    }
                }
            }

            // Clear both session values
            Session::forget('buy_now_info');
            Session::forget('checkout_in_progress');

        }
    });
    }
}
