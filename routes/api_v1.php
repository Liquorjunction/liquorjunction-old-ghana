<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CommonController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'headerauth'], function () {
    Route::get('getuser', 'SettingController@index');
});

Route::get('special-offers', [ProductController::class, 'special']);


Route::group(['middleware' => 'headerauthwithoutlogin'], function () {

    Route::post('label', 'SettingController@label');

    Route::post('general_settings', 'SettingController@general_settings');
    Route::get('version','SettingController@version');

    Route::get('banner-list', [CommonController::class, 'getBannerList']);
    Route::match(['GET', 'POST'],'categories-list', [CommonController::class, 'getCategoryList']);
    Route::get('search', [CommonController::class, 'searchList']);
    Route::get('product-filtter', [ProductController::class, 'productsfiltter']);
    Route::post('cart-list', [ProductController::class, 'cartList']);
    //UserController API
    //Date:18-01-2023
    Route::post('register', 'UserController@register');
    Route::post('/social_register', [UserController::class, 'social_register'])->name('social_register');
    Route::post('login', 'UserController@login');
    Route::post('otpVerification', 'UserController@otpVerification');
    Route::post('resendOtp', 'UserController@resendOtp');
    Route::post('forgotPassword', 'UserController@forgotPassword');
    Route::post('forgototpVerification', 'UserController@forgototpVerification');
    Route::post('resendforgotOtp', 'UserController@resendforgotOtp');
    Route::post('resetPassword', 'UserController@resetPassword');
    // Route::post('viewProfile', 'UserController@viewProfile');
    Route::post('updateProfile', 'UserController@updateProfile');
    //Date:19-01-2023   

    Route::match(['GET', 'POST'], 'deleteFavorite', 'ProductController@deleteFavorite')->name('deleteFavorite');
    Route::match(['GET', 'POST'], 'addFavorite', 'ProductController@addFavorite')->name('addFavorite');
    //Route::match(['GET', 'POST'], 'productListing', 'ProductController@productListing')->name('productListing');
    Route::match(['GET', 'POST'], 'product-list', 'ProductController@getProductList')->name('getProductList');
    Route::match(['GET', 'POST'], 'product-detail', 'ProductController@productDetail')->name('productDetail');
    Route::match(['GET', 'POST'], 'product-variants', 'ProductController@getProductVariant')->name('productVariant');
    Route::match(['GET', 'POST'], 'related-products', 'ProductController@getRealtedProduct')->name('realtedProduct');
    Route::match(['GET', 'POST'], 'common-products', 'ProductController@getCommonProducts')->name('recommendedProduct');
    Route::match(['GET', 'POST'], 'favorite-list', 'ProductController@favoriteList')->name('favoriteList');
    Route::match(['GET', 'POST'], 'reward-list', 'ProductController@rewardPoint')->name('rewardPoint');
    Route::match(['GET', 'POST'], 'addReview', 'ProductController@addReview')->name('addReview');
    Route::match(['GET', 'POST'], 'reviewList', 'ProductController@productReviewList')->name('productReviewList');
    Route::match(['GET', 'POST'], 'addMostviewProduct', 'ProductController@addMostViewProduct')->name('addMostViewProduct');
    Route::match(['GET', 'POST'], 'viewProfile', 'UserController@viewProfile')->name('viewProfile');


    Route::match(['GET', 'POST'], 'store-listing', 'CartController@storeListing')->name('storeListing');

    Route::post('removeProductFromCart', 'ProductController@removeProductFromCart');
    Route::match(['GET', 'POST'], 'viewCart', 'ProductController@viewCart')->name('viewCart');
    Route::match(['GET', 'POST'], 'myOrder', 'ProductController@myOrder')->name('myOrder');
    Route::match(['GET', 'POST'], 'address_manager', 'UserController@addressManager')->name('addressManager');
    Route::match(['GET', 'POST'], 'myAddressList', 'UserController@myAddressList')->name('myAddressList');

    // Billing address
    Route::match(['GET', 'POST'], 'bill_address_manager', 'UserController@billAddressManager')->name('billAddressManager');

    //Route::post('addToCart', 'CartController@addToCart');
    Route::match(['GET', 'POST'], 'cart-manager', 'CartController@cartManager')->name('cartManager');
    Route::get('cart-count', 'CartController@getCartCount')->name('getCartCount');
    Route::get('countries-list', 'CommonController@getCountrires')->name('getCountrires');
    Route::get('regions-list', 'CommonController@getRegion')->name('getRegion');
    Route::get('area-list', 'CommonController@getArea')->name('getArea');


    Route::post('incrementCart', 'CartController@incrementCart');
    Route::post('decrementCart', 'CartController@decrementCart');

    Route::post('removeAllFav', 'ProductController@removeAllFav');
    Route::post('place-order', 'CheckoutController@placeOrder');
    Route::post('callBackUrl', 'CheckoutController@callBackUrl');
    Route::post('orderSuccessfully', 'CartController@orderSuccessfully');

    // Hiren-Dev 21 Feb 2024
    Route::post('save-payment', 'CheckoutController@save_payment');


    Route::match(['GET', 'POST'], 'applied-coupon', 'CheckoutController@appliedCoupon')->name('appliedCoupon');
    Route::match(['GET', 'POST'], 'applied-reward', 'CheckoutController@appliedReward')->name('appliedReward');

    Route::match(['GET', 'POST'], 'checkout', 'CheckoutController@checkout')->name('checkout');
    //Route::match(['GET', 'POST'], 'place-order', 'CheckoutController@placeOrder')->name('placeOrder');


    Route::match(['GET', 'POST'], 'pickUpOrder', 'CartController@pickUpOrder')->name('pickUpOrder');
    Route::match(['GET', 'POST'], 'selectAddress', 'CartController@selectAddress')->name('selectAddress');

    Route::match(['GET', 'POST'], 'notificationList', 'ProductController@notificationList')->name('notificationList');
    Route::match(['GET', 'POST'], 'notificationCount', 'ProductController@notificationCount')->name('notificationCount');

    Route::match(['GET', 'POST'], 'contactUs', 'UserController@contactUs')->name('contactUs');

    Route::match(['GET', 'POST'], 'getCount', 'UserController@getCount')->name('getCount');


    Route::match(['GET', 'POST'], 'blog', 'CommonController@Blog')->name('blog');
    Route::match(['GET', 'POST'], 'blog-details', 'CommonController@blogDetails')->name('blogDetails');
    Route::match(['GET', 'POST'], 'faq', 'CommonController@faq')->name('faq');
    Route::post('cms', 'CommonController@cms');
    Route::match(['GET', 'POST'], 'shopList', 'ProductController@shopList')->name('shopList');
    Route::match(['GET', 'POST'], 'storeMap', 'ProductController@storeMap')->name('storeMap');
    Route::match(['GET', 'POST'], 'deleteAccount', 'UserController@deleteAccount')->name('deleteAccount');

    Route::get('queries-reason', [CommonController::class, 'getInquiryReason']);
    Route::post('save-queries', [CommonController::class, 'saveQueries']);


    //Order
    Route::get('order-list', [UserController::class, 'orderList']);
    Route::get('order-details', [UserController::class, 'orderDetail']);
    Route::post('order-cancel', [UserController::class, 'orderStatus']);
    Route::get('track-order', [UserController::class, 'trackOrder']);
    Route::post('apply-coupon', [UserController::class, 'applyCouponCode']);


    Route::post('changePassword', 'UserController@changePassword');
    Route::post('logout', 'UserController@logout');

    // Invoice
    // Route::get('print-order', [UserController::class, 'getInvoice']);

});
