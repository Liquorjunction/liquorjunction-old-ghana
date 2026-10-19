<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class CartApiController extends Controller
{
    // Return cart items and count for AJAX updates
    public function getCartData(Request $request)
    {
        $items = Helper::getUserCartItems();
        $count = count($items);
        return response()->json([
            'items' => $items,
            'count' => $count,
        ]);
    }

        // Delete a cart item by ID (for AJAX)
public function deleteCartItem($id)
{
    $user = auth()->guard('user')->user();

    // =========================
    // 🟢 LOGGED-IN USER
    // =========================
    if ($user) {
        $cart = \App\Models\Cart::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($cart) {
            $cart->delete();
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }

    // =========================
    // 🟡 GUEST USER (SESSION)
    // =========================
    $sessionCart = session()->get('cart_info', []);

    // ID format: productId_variantId
    if (strpos($id, '_') !== false) {
        list($product_id, $variant_id) = explode('_', $id);

        if (isset($sessionCart[$product_id][$variant_id])) {

            unset($sessionCart[$product_id][$variant_id]);

            // Remove product if no variants left
            if (empty($sessionCart[$product_id])) {
                unset($sessionCart[$product_id]);
            }

            session()->put('cart_info', $sessionCart);

            return response()->json(['success' => true]);
        }
    }

    return response()->json([
        'success' => false,
        'message' => 'Item not found'
    ], 404);
}
}
