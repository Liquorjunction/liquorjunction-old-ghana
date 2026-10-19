<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\Frontend\UsersController;
use App\Http\Controllers\Frontend\WebsiteLoginController;
use App\Http\Controllers\Frontend\CMSController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\StoreController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\MyProfileController;
use App\Http\Controllers\Frontend\StoreMapController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/phpinfo', function () {
    print_r(phpinfo());
    exit;
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

// Backend Routes
Route::get('/admin/login', function () {
   
    /*return redirect('/admin/login');*/
     return redirect()->route('login');
});


Route::get('/admin', function () {
   
    /*return redirect('/admin/login');*/
     return redirect()->route('login');
});

// Route::get('/wholesaler', function () {
   
//     /*return redirect('/admin/login');*/
//      return redirect()->route('wholesalerlogin');
// });

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/special-offers', [HomeController::class, 'special'])->name('special');
Route::get('/load-offer-list', [HomeController::class, 'loadfilterofferlist'])->name('loadfilterofferlist');
Route::get('/load-bogo-list', [HomeController::class, 'loadfilterbogolist'])->name('loadfilterbogolist');

Route::post('/change-language', [HomeController::class, 'changeLanguage'])->name('frontend.changeLanguage');
Route::post('/search-auto-suggestion',[HomeController::class, 'autoSuggestion'])->name('searchAutoSuggestion');
Route::post('/set-timezone', [HomeController::class, 'setTimeZone'])->name('frontend.setTimeZone');
// Route::get('/send-notification',[HomeController::class, 'sendNotification'])->name('sendNotification');




// Route::get('/', function () {
//     return view('frontEnd.layouts.app');
// });


/*// Social Auth
Route::get('/oauth/{driver}', [SocialAuthController::class, 'redirectToProvider'])->name('social.oauth');
Route::get('/oauth/{driver}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');*/

Route::Group(['prefix' => env('BACKEND_PATH')], function () {
    Route::get('/login', [LoginController::class, 'showMainuserLoginForm'])->name('login');
    Route::post('/adminlogin', [LoginController::class, 'adminLogin'])->name('adminlogin');
    Route::post('/', [LoginController::class, 'adminLogin'])->name('adminlogin');
    Route::post('/admin', [LoginController::class, 'adminLogin'])->name('adminlogin');
     Route::get('/forgot-password', [LoginController::class, 'forgotpass']);
     Route::post('/forgot/user', [LoginController::class, 'mainuserforgot']);
     Route::post('/main-user-logout', [LoginController::class, 'logoutMainUser'])->name('main-user-logout');
     // Route::get('/wholesaleradmin', [LoginController::class, 'showWholesalerLoginForm'])->name('wholesalerlogin');
     // Route::post('/wholesaler', [LoginController::class, 'adminWholesalerLogin'])->name('adminwholesalerlogin');
});

// Route::Group(['prefix' => env('WHOLESALER_BACKEND_PATH')], function () {
  
//      //Wholesaler Route Here
//     Route::get('/', [LoginController::class, 'showWholesalerLoginForm'])->name('wholesalerlogin');
//     Route::get('/login', [LoginController::class, 'showWholesalerLoginForm'])->name('wholesalerlogin');
//      Route::post('/wholesalerpost', [LoginController::class, 'adminWholesalerLogin'])->name('adminwholesalerlogin');
//      Route::post('/wholesalerregister', [LoginController::class, 'adminWholesalerRegister'])->name('adminwholesalerregister');
//      Route::post('/main-wholesaler-logout', [LoginController::class, 'logoutMainWholesaler'])->name('main-wholesaler-logout');
//      Route::get('/forgot-password', [LoginController::class, 'forgotpasswholesaler']);
//      Route::post('/forgot/user', [LoginController::class, 'wholesalerforgot']);
//      Route::get('/register/{id}', [LoginController::class, 'wholesalerregister']);
//     // Route::post('/', [LoginController::class, 'adminWholesalerLogin'])->name('adminwholesalerlogin');
// });

Route::middleware(['PreventBackHistory'])->group(function () {
Route::middleware(['userAuth'])->group(function () {
/*---- New code divyanshu ----*/
Route::get('/product-list/{id?}/', [ProductController::class, 'list'])->name('productlist');
Route::post('/product-list/{id?}/', [ProductController::class, 'list'])->name('productFillterlist');
Route::get('/filter-prodcut', [ProductController::class, 'filterData'])->name('productFilterData');
Route::get('/prodcut-sortby', [ProductController::class, 'sortByList'])->name('productSortByList');
Route::get('/variant-price', [ProductController::class, 'productVariantPrice'])->name('productVariantPrice');
Route::get('/filter-product-list', [ProductController::class, 'filterproductlist'])->name('filterproductlist');
Route::get('/load-product-list', [ProductController::class, 'loadfilterproductlist'])->name('loadfilterproductlist');
Route::get('/brand-list', [ProductController::class, 'filterbrandlist'])->name('filterbrandlist');
Route::get('/load-brand-list', [ProductController::class, 'loadbrandlist'])->name('loadbrandlist');

Route::get('/products-list/{id?}/', [ProductController::class, 'productListview'])->name('productlistview');
Route::post('/product-filter', [ProductController::class, 'productFilterCategory'])->name('productfilter');
Route::post('/product-mostviewfilter', [ProductController::class, 'productFilterMostView'])->name('mostviewfilter');
Route::post('/product-fav', [ProductController::class, 'productFavourite'])->name('productfav');

Route::post('/productresponse-filter', [ProductController::class, 'productResponseFilterCategory'])->name('productresponsefilter');
Route::post('/productresponse-pricefilter', [ProductController::class, 'productResponsepriceFilterCategory'])->name('productresponsepricefilter');
Route::post('/productresponse-mostview', [ProductController::class, 'productResponsepriceFilterMostView'])->name('productresponsemostview');
Route::post('/productresponse-mostviewremove', [ProductController::class, 'productResponsepriceFilterMostViewRemove'])->name('productresponsemostviewremove');

Route::get('/product-details/{id}', [ProductController::class, 'productDetails'])->name('productdetails');

Route::get('/store-listing/store', [StoreController::class, 'listingOnline'])->name('store.listing-online');
Route::get('/store-listing/in-store', [StoreController::class, 'listingInStore'])->name('store.listing-offline');

Route::get('/store-listing/filter', [StoreController::class, 'listingFilter'])->name('store.filter');

Route::post('/product-pdfview', [ProductController::class, 'productPdfView'])->name('productpdfview');

Route::post('/product-addtocart', [ProductController::class, 'productCartAdd'])->name('productcartadd');

Route::get('/check-qty', [ProductController::class, 'checkProductQty'])->name('checkQty');

Route::get('/cart', [CartController::class, 'Cart'])->name('cart');
Route::post('/cart-increment', [CartController::class, 'productCartIncrement'])->name('cartincrement');
Route::post('/cart-remove', [CartController::class, 'productCartRemove'])->name('cartremove');
Route::post('/cart-ordertype', [CartController::class, 'productCartOrderType'])->name('cartordertype');
Route::post('/buy-now', [CartController::class, 'productBuyNow'])->name('buy-now');
Route::post('/buy-now-session', [CartController::class, 'buyNowSession'])->name('buyNowSession');


Route::get('/checkout-cancel.', [CheckoutController::class, 'Checkout'])->name('checkout.cancel');


Route::get('/checkout', [CheckoutController::class, 'Checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'Checkout'])->name('checkout');
Route::post('/move-buy-now-to-cart', [CheckoutController::class, 'moveBuyNowToCart'])->name('moveBuyNowToCart');
Route::post('/buy-now/update-quantity', [CheckoutController::class, 'updateBuyNowQuantity'])->name('buyNow.updateQuantity');
Route::get('/checkoutedit-address/{id}',[CheckoutController::class, 'checkouteditAddress'])->name('checkoutedit-address');
Route::get('/checkoutedit-bill-address/{id}',[CheckoutController::class, 'checkouteditBillAddress'])->name('checkoutedit-bill-address');
Route::post('/store-checkout',[CheckoutController::class, 'storePlaceOrder'])->name('storeCheckout');
Route::post('/apply-coupon',[CheckoutController::class, 'applyCoupon'])->name('applyCoupon');
Route::post('/apply-reward',[CheckoutController::class, 'applyReward'])->name('applyReward');
Route::post('/user-area-tax',[CheckoutController::class, 'getUserAreaTax'])->name('userAreaTax');
Route::get('/thankyou/{id}/{earnedpoints}', [CheckoutController::class, 'orderSuccess'])->name('orderSuccess');
Route::get('/thankyou-card/{userid}/{orderid}/{amount}/{earnedpoints}', [CheckoutController::class, 'orderSuccessCard'])->name('orderSuccessCard');

Route::post('/getsubcatlist', [CheckoutController::class, 'getSubcatlist'])->name('getsubcatlist');
Route::post('/getarealist', [CheckoutController::class, 'getArealist'])->name('getarealist');
Route::post('/add-address', [CheckoutController::class, 'add_address'])->name('addAddress');
Route::get('/store-map', [CheckoutController::class, 'StoreMap'])->name('storemap');
Route::post('/store-map/detail', [CheckoutController::class, 'storeMapDetail'])->name('storemap-detail');
Route::post('/session-latitude', [CheckoutController::class, 'storeSessionLat'])->name('sessionlatitude');


Route::post('/selected-address', [CheckoutController::class, 'SelectedAddress'])->name('selected-address');

Route::get('/callBackUrl', [CheckoutController::class, 'callBackUrl'])->name('callBackUrl');


Route::get('/my-account',[MyProfileController::class, 'index'])->name('my-account');
Route::get('/my-address',[MyProfileController::class, 'myaddress'])->name('my-address');
Route::get('/edit-profile',[MyProfileController::class, 'edit'])->name('edit-profile');
Route::post('/upadte-profile',[MyProfileController::class, 'update'])->name('upadte-profile');
Route::get('/favorite-list',[MyProfileController::class, 'favorite'])->name('favorite-list');
Route::get('/reward-points',[MyProfileController::class, 'points'])->name('reward-points');
Route::post('/favorite/status', [MyProfileController::class, 'statusUpdate'])->name('favorite-status');
Route::get('/edit-address/{id}',[MyProfileController::class, 'editAddress'])->name('edit-address');
Route::get('/edit-bill-address/{id}',[MyProfileController::class, 'editBillAddress'])->name('edit-bill-address');
Route::get('/add-address',[MyProfileController::class, 'addAddress'])->name('add-address');
Route::get('/add-bill-address',[MyProfileController::class, 'addBillAddress'])->name('add-bill-address');
Route::post('/store-address',[MyProfileController::class, 'storeAddress'])->name('store-address');
Route::post('/store-bill-address',[MyProfileController::class, 'storeBillAddress'])->name('store-bill-address');
Route::post('/upadte-address',[MyProfileController::class, 'updateAddress'])->name('upadte-address');
Route::post('/update-bill-address',[MyProfileController::class, 'updateBillAddress'])->name('update-bill-address');
Route::post('/address-remove', [MyProfileController::class, 'addressRemove'])->name('addressremove');
Route::post('/billaddressremove', [MyProfileController::class, 'billaddressremove'])->name('billaddressremove');
Route::get('/change-password',[MyProfileController::class, 'changePassword'])->name('userchange-password');
Route::post('/update-password',[MyProfileController::class, 'updatePassword'])->name('update-password');
Route::post('/user-logout', [MyProfileController::class, 'logoutUser'])->name('user-logout');


Route::get('/request-quote',[MyProfileController::class, 'requestQuote'])->name('request-quote');
Route::get('/how-it`s-works',[MyProfileController::class, 'howItWorks'])->name('how-it-works');
Route::post('/getsubcatlist', [MyProfileController::class, 'getSubcatlist'])->name('getsubcatlist');
Route::post('/getarealist', [MyProfileController::class, 'getArealist'])->name('getarealist');


Route::post('/quote', [HomeController::class, 'storeQuote'])->name('store-quote');
Route::get('/promoted-product',[HomeController::class, 'UserPromotedProduct'])->name('userpromoted-product');

Route::get('/my-order',[OrderController::class, 'index'])->name('myOrder');
Route::get('/order-detail/{id}',[OrderController::class, 'myOrderDetails'])->name('order-detail');
Route::get('/print-order/{id}', [OrderController::class, 'printMyOrder'])->name('printMyOrder');
Route::get('/cancel-order/{id}',[OrderController::class, 'cancelMyorder'])->name('cancelMyorder');

Route::post('/re-order',[MyProfileController::class, 'reOrder'])->name('re-order');
Route::post('/add-rating',[MyProfileController::class, 'addRating'])->name('addRating');

// Route::post('/dashboard-filter', [HomeController::class, 'dashboardFilterCategory'])->name('dashboardfilter');
Route::get('/dashboard-category/{id}',[HomeController::class, 'dashboardFilterCategory'])->name('dashboard-category');

Route::get('/community',[MyProfileController::class, 'community'])->name('community');
Route::post('/read-notification', [MyProfileController::class, 'readNotification'])->name('read-notification');
});
});

//New Routes For Frontend
Route::middleware(['PreventBackHistory'])->group(function () {
Route::get('/login', [WebsiteLoginController::class, 'websiteLoginForm'])->name('websitelogin');
Route::get('/register', [WebsiteLoginController::class, 'websiteRegisterForm'])->name('websiteregister');
Route::post('/websiteregister', [WebsiteLoginController::class, 'websiteRegister'])->name('websiteregisterpost');
Route::post('/websitelogin', [WebsiteLoginController::class, 'webisteLogin'])->name('websiteloginpost');
Route::get('/send-otp', [WebsiteLoginController::class, 'websiteSendOtpForm'])->name('websitesendotp');
Route::get('/send-otp-login', [WebsiteLoginController::class, 'websiteSendOtplogin'])->name('websitesendotplogin');
Route::post('/resend-otp', [WebsiteLoginController::class, 'websiteResendOtpForm'])->name('websiteresendotp');
Route::post('/forgot-resend-otp', [WebsiteLoginController::class, 'websiteForgotResendOtpForm'])->name('websiteforgotresendotp');

Route::post('/websiteotpverification', [WebsiteLoginController::class, 'webisteOtpVerification'])->name('websiteotpverificationpost');

Route::post('/save-token',[WebsiteLoginController::class, 'saveToken'])->name('save-token');

Route::get('/userforgot-password', [WebsiteLoginController::class, 'forgotpassword'])->name('userforgotpassword');
Route::post('/forgotpasswordpost', [WebsiteLoginController::class, 'forgotPasswordForm'])->name('forgotpasswordpost');
Route::get('/forgot-otp', [WebsiteLoginController::class, 'websiteForgotOtpForm'])->name('websiteforgototp');
Route::post('/websiteotpforgotverification', [WebsiteLoginController::class, 'webisteOtpForgotVerification'])->name('websiteotpforgotverificationpost');
Route::get('/forgot-password', [WebsiteLoginController::class, 'changePassword'])->name('forgotchange-password');
Route::post('/changepasswordpost', [WebsiteLoginController::class, 'changePasswordForm'])->name('changepasswordpost');
});


Route::post('/forgot/user', [WebsiteLoginController::class, 'userforgotpassword'])->name('forgotuser');


Route::get('auth/google', [WebsiteLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [WebsiteLoginController::class, 'handleGoogleCallback']);
Route::get('auth/facebook', [WebsiteLoginController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback', [WebsiteLoginController::class, 'handleFacebookCallback']);
Route::get('auth/apple', [WebsiteLoginController::class, 'redirectToApple'])->name('auth.apple');
Route::post('applecallbackliquor', [WebsiteLoginController::class, 'handleAppleCallback']);
// Route::post('/user/logout', [WebsiteLoginController::class, 'logoutUser'])->name('user-logout');
Route::get('/profile', [WebsiteLoginController::class, 'websiteProfile'])->name('websiteprofile');
//Route::get('/instructor-profile', [WebsiteLoginController::class, 'websiteInstructorProfile'])->name('websiteinstructorprofile');
//Route::post('/instructor-profile/edit/{id}', [WebsiteLoginController::class, 'websiteInstructorProfileEdit'])->name('websiteinstructorprofileedit');
Route::get('/user-profile', [UsersController::class, 'websiteUserProfile'])->name('websiteuserprofile');
Route::post('/user-profile/edit/{id}', [UsersController::class, 'websiteUserProfileEdit'])->name('websiteuserprofileedit');

// Route::post('/update-password', [UsersController::class, 'updatePassword'])->name('user-update-password');
Route::post('/image-file-upload', [UsersController::class, 'imageFileUpload'])->name('imagefileupload');
Route::post('/video-file-upload', [UsersController::class, 'videoFileUpload'])->name('videofileupload');

//search
Route::get('/search', [HomeController::class, 'search_index'])->name('search');
Route::get('/search-index-new', [HomeController::class, 'search_index_new'])->name('search_index_new');
Route::get('autocomplete', [HomeController::class, 'autocomplete'])->name('frontend.autocomplete');
Route::get('/search/{slug}',[HomeController::class, 'searchDetails'])->name('search-details');

Route::post('/subscribe-email', [HomeController::class, 'subscribeEmail'])->name('subscribeemail');

//Instructor Management
Route::get('/instructor', [UsersController::class, 'instructor_index'])->name('instructor');
Route::get('/instructor-profile/{id}', [UsersController::class, 'instructor_profile'])->name('instructorprofile');

Route::get('/become-an-instructor', [UsersController::class, 'becomeAnInstructor'])->name('become-an-instructor');
Route::post('/become-an-instructor', [UsersController::class, 'editBecomeAnInstructor'])->name('edit_become_an_instructor');

Route::get('/my-favourite', [WebsiteLoginController::class, 'myFavourite'])->name('my-favourite');
Route::get('/my-library', [WebsiteLoginController::class, 'myLibrary'])->name('mylibrary');
Route::get('/my-library-detail/{id}', [WebsiteLoginController::class, 'myLibraryDetail'])->name('mylibrarydetail');
Route::get('/purchase-class', [WebsiteLoginController::class, 'purchaseClass'])->name('purchase-class');
Route::get('/my-earning', [UsersController::class, 'myEarning'])->name('myearning');
Route::get('/my-earning-detail', [UsersController::class, 'myEarningDetail'])->name('myearningdetail');
Route::post('/my-earning-detail', [UsersController::class, 'myEarningDetailAnyData'])->name('myearningdetail.anydata');
Route::post('/request-amount', [WebsiteLoginController::class, 'requestAmount'])->name('request-amount');
Route::post('/my-review', [WebsiteLoginController::class, 'myReview'])->name('myreview');
Route::post('/my-library/favourite', [WebsiteLoginController::class, 'favouriteStatus'])->name('my-library.favourite.status');


//cms Pages

// Route::get('/terms-conditions',[CMSController::class, 'termsandConditions'])->name('termsandconditions');
// Route::get('/privacy-policy',[CMSController::class, 'privacyPolicy'])->name('privacypolicy');
// Route::get('/contact-us',[CMSController::class, 'contactUs'])->name('contactus');
// Route::post('/contact-us/add',[CMSController::class, 'contactUsStore'])->name('contactusadd');
Route::get('/blogs',[BlogController::class, 'index'])->name('frontend.blog');
Route::get('/blog-details/{id}', [BlogController::class, 'blogDetails'])->name('blogdetails');
Route::get('/faqs',[CMSController::class, 'faq'])->name('faqs');
Route::get('/queries',[CMSController::class, 'queries'])->name('queries');
Route::post('/store-queries',[CMSController::class, 'queriesStore'])->name('queriesStore');
Route::get('/track-order',[CMSController::class, 'trackOrder'])->name('trackOrder');
Route::post('/check-order-status',[CMSController::class, 'checkOrderStatus'])->name('checkOrderStatus');
Route::get('/delivery-information',[CMSController::class, 'deliveryInformation'])->name('deliveryInformation');
Route::get('/returns-cancellation',[CMSController::class, 'returnsCancellation'])->name('returnsCancellation');
Route::get('/damages-wrong-goods',[CMSController::class, 'damagesWrongGoods'])->name('damagesWrongGoods');
Route::get('/our-packaging',[CMSController::class, 'ourPackaging'])->name('ourPackaging');

Route::get('/payment-options',[CMSController::class, 'paymentOption'])->name('paymentOption');
Route::get('/placing-order',[CMSController::class, 'placingOrder'])->name('placingOrder');
Route::get('/security-privacy',[CMSController::class, 'securityPrivacy'])->name('securityPrivacy');
Route::get('/terms-condition',[CMSController::class, 'termsCondition'])->name('termsCondition');
Route::get('/customer-support',[CMSController::class, 'customerSupport'])->name('customerSupport');

Route::get('/head-office',[CMSController::class, 'headOffice'])->name('headOffice');
Route::get('/order-by-phone',[CMSController::class, 'orderByPhone'])->name('orderByPhone');
Route::get('/trade-enquieries',[CMSController::class, 'tradeEnquieries'])->name('tradeEnquieries');
Route::get('/press-enquieries',[CMSController::class, 'pressEnquieries'])->name('pressEnquieries');
Route::get('/about-us',[CMSController::class, 'aboutUs'])->name('aboutUs');
Route::get('/our-company',[CMSController::class, 'aboutUs'])->name('ourCompany');
Route::get('/our-history',[CMSController::class, 'aboutUs'])->name('ourHistory');
Route::get('/responsible-drinking',[CMSController::class, 'aboutUs'])->name('responsibleDrinking');
Route::get('/our-store',[CMSController::class, 'ourStore'])->name('ourStore');
Route::get('/privacy-policy',[CMSController::class, 'aboutUs'])->name('privacyPolicy');


/*Route::get('/logout', function () {
    Auth::logout();
    return redirect('/admin');
})->name('logout');

// Start of Frontend Routes
// ../site map
Route::get('/sitemap.xml', [SiteMapController::class, 'siteMap'])->name('siteMap');
Route::get('/{lang}/sitemap', [SiteMapController::class, 'siteMap'])->name('siteMapByLang');
//Site url


/*Auth::routes();*/
 Route::get('/403', function () {
    return view('errors.403');
})->name('frontend.NoPermission');

Route::get('/404', function () { 
    return view('frontend.page-not-found');
}); 


Route::get('/phpinfo', function () {
    phpinfo();
    exit;
});

Route::fallback(function() {
    return view('frontend.page-not-found');
});


