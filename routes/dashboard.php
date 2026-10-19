<?php

use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\UsersAdminController;
use App\Http\Controllers\Dashboard\WithdrawRequestController;
use App\Http\Controllers\Dashboard\InstructorRequestController;
use App\Http\Controllers\Dashboard\CategoryAdminController;
use App\Http\Controllers\Dashboard\QuestionAdminController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\Dashboard\LevelController;
use App\Http\Controllers\Dashboard\WebmasterSettingsController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\SubCategoriesController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\WholesalerController;
use App\Http\Controllers\Dashboard\SubadminController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\RequestProductController;
use App\Http\Controllers\Dashboard\AdvertiseController;
use App\Http\Controllers\Dashboard\QuoteController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\HamburgerController;
use App\Http\Controllers\Dashboard\TimeFrameController;
use App\Http\Controllers\Dashboard\MaterialCategoryController;
use App\Http\Controllers\Dashboard\LoyaltyController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\PurchaseOrderController;
use App\Http\Controllers\Dashboard\UserReportController;
use App\Http\Controllers\Dashboard\EarningReportController;
use App\Http\Controllers\Dashboard\RegionController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\CountryAdminController;
use App\Http\Controllers\Dashboard\AreaController;
use App\Http\Controllers\Dashboard\PromocodeController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\ProductRatingController; 
use App\Http\Controllers\Dashboard\CmsController;
use App\Http\Controllers\Dashboard\StockReportController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\OrderReportController;
use App\Http\Controllers\Dashboard\LoyaltyReportController;
use App\Http\Controllers\Dashboard\TrackOrderController;
use App\Http\Controllers\Dashboard\DeliveryInformationController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\StoreController;
use App\Http\Controllers\Dashboard\NewsLatterController;
use App\Http\Controllers\Dashboard\DeliveryController;
use App\Http\Controllers\Dashboard\OfferController;
use App\Http\Controllers\Dashboard\DiscountController;
use App\Http\Controllers\Dashboard\BogoController;

use App\Models\Product;
use Illuminate\Support\Facades\Route;


// No Permission
Route::get('/403', function () {
    return view('errors.403');
})->name('NoPermission');

// Not Found
Route::get('/404', function () {
    return view('errors.404');
})->name('NotFound');

//police Users Route
Route::middleware(['subAdminAuth'])->group(function () {
    Route::get('filter', [DashboardController::class, 'index']);
    Route::post('/read-admin-notification', [DashboardController::class, 'readNotification'])->name('read-admin-notification');
    Route::get('/users', [UsersAdminController::class, 'index'])->name('users');
    Route::post('/userlist/anydata', [UsersAdminController::class, 'anydata'])->name('userlist.data');
    Route::post('/userlist/updateAll', [UsersAdminController::class, 'updateAll'])->name('userlistUpdateAll');
    //Route::get('/usercreate', [UsersAdminController::class, 'create'])->name('usercreate');
    //Route::post('/userstore', [UsersAdminController::class, 'store'])->name('userstore');
    Route::get('/usersShow/{id}/show', [UsersAdminController::class, 'show'])->name('usersShow');
    Route::get('/userEdit/{id}/edit', [UsersAdminController::class, 'edit'])->name('userEdit');
    Route::post('/userUpdate/{id}/update', [UsersAdminController::class, 'update'])->name('userUpdate');
    Route::post('/filteruser', [UsersAdminController::class, 'index'])->name('filteruser');
    Route::post('/userexport', [UsersAdminController::class, 'userexport'])->name('userexport');
    Route::get('/user/delete/{id}', [UsersAdminController::class, 'destroy'])->name('user.delete');
    Route::post('/userlist/updateAll', [UsersAdminController::class, 'updateAll'])->name('userlistUpdateAll');



    //Instructor Request Management
    Route::get('/instructor-request', [InstructorRequestController::class, 'index'])->name('instructor-request');
    Route::get('/instructor-request/create', [InstructorRequestController::class, 'create'])->name('instructor-request.create');
    Route::post('/instructor-request/store', [InstructorRequestController::class, 'store'])->name('instructor-request.store');
    Route::get('/instructor-request/delete/{id}', [InstructorRequestController::class, 'destroy'])->name('instructor-request.delete');
    Route::get('/instructor-request/show/{id}', [InstructorRequestController::class, 'show'])->name('instructor-request.show');
    Route::get('/instructor-request/edit/{id}', [InstructorRequestController::class, 'edit'])->name('instructor-request.edit');
    Route::post('/instructor-request/update/{id}', [InstructorRequestController::class, 'update'])->name('instructor-request.update');
    Route::post('/instructor-request/anyData', [InstructorRequestController::class, 'anyData'])->name('instructor-request.data');
    Route::post('/instructor-request/UpdateAll', [InstructorRequestController::class, 'instructorRequestUpdateAll'])->name('instructor-requestUpdateAll');
    Route::post('/instructor-request/UpdateAllStatus', [InstructorRequestController::class, 'instructorRequestStatusUpdateAll'])->name('instructor-requestStatusUpdateAll');



    //Withdraw Management
    Route::get('/user-withdraw-request', [WithdrawRequestController::class, 'index'])->name('user-withdraw-request');
    Route::get('/withdraw-request', [WithdrawRequestController::class, 'create'])->name('withdraw-request.create');
    Route::post('/withdraw-request', [WithdrawRequestController::class, 'store'])->name('withdraw-request.store');
    Route::get('/withdraw-request/delete/{id}', [WithdrawRequestController::class, 'destroy'])->name('withdraw-request.delete');
    Route::get('/withdraw-request/show/{id}', [WithdrawRequestController::class, 'show'])->name('withdraw-request.show');
    Route::get('/withdraw-request/edit/{id}', [WithdrawRequestController::class, 'edit'])->name('withdraw-request.edit');
    Route::post('/withdraw-request/update/{id}', [WithdrawRequestController::class, 'update'])->name('withdraw-request.update');
    Route::post('/withdraw-request/anyData', [WithdrawRequestController::class, 'anyData'])->name('withdraw-request.data');
    Route::post('/withdraw-request/UpdateAll', [WithdrawRequestController::class, 'withdrawRequestUpdateAll'])->name('withdraw-requestUpdateAll');
    Route::post('/filter-requestdate', [WithdrawRequestController::class, 'index'])->name('filterrequestdate');

    //Withdraw History Management
    Route::get('/user-withdraw-history', [WithdrawRequestController::class, 'indexWithdrawHistory'])->name('user-withdraw-history');
    Route::get('/withdraw-history/show/{id}', [WithdrawRequestController::class, 'showWithdrawHistory'])->name('withdraw-history.show');
    Route::post('/withdraw-history/anyData', [WithdrawRequestController::class, 'anyDataWithdrawHistory'])->name('withdraw-history.data');

    //Level
    Route::get('/level', 'LevelController@index')->name('level');
    Route::get('/level/create', 'LevelController@create')->name('level.create');
    Route::post('/level/store', 'LevelController@store')->name('level.store');
    Route::get('/level/delete/{id}', 'LevelController@destroy')->name('level.delete');
    Route::get('/level/show/{id}', 'LevelController@show')->name('level.show');
    Route::get('level/edit/{id}', 'LevelController@edit')->name('level.edit');
    Route::post('level/update/{id}', 'LevelController@update')->name('level.update');
    Route::post('level/anyData', 'LevelController@anyData')->name('level.anyData');
    Route::post('/level/levelUpdateAll', 'LevelController@levelUpdateAll')->name('levelUpdateAll');


    //View Transactions
    Route::get('/transactions', 'ViewTransactionsController@index')->name('transactions');
    Route::get('/transactions/show/{id}', 'ViewTransactionsController@show')->name('transactions.show');
    Route::post('transactions/anyData', 'ViewTransactionsController@anyData')->name('transactions.data');

    //Disputes Management
    Route::get('/dispute', 'DisputeController@index')->name('dispute');
    Route::get('/dispute/show/{id}', 'DisputeController@show')->name('dispute.show');
    Route::post('dispute/anyData', 'DisputeController@anyData')->name('dispute.data');
    Route::post('/dispute/UpdateAll', 'DisputeController@disputeUpdateAll')->name('disputeUpdateAll');

    //Shubham Development Route

    //Category Management
    Route::get('/category', [CategoriesController::class, 'index'])->name('category');
    Route::post('/category/anyData', [CategoriesController::class, 'anyData'])->name('category.anyData');
    // Route::get('/category/create', [CategoriesController::class,'create'])->name('category.create');
    Route::post('/category/store', [CategoriesController::class, 'store'])->name('category.store');
    Route::post('/category/UpdateAll', 'CategoriesController@categoryUpdateAll')->name('categoryUpdateAll');
    Route::post('category/edit', 'CategoriesController@edit')->name('category.edit');
    Route::post('category/show', 'CategoriesController@show')->name('category.show');
    Route::get('/category/delete/{id}', 'CategoriesController@destroy')->name('category.delete');
    Route::post('/category/status_active', [CategoriesController::class, 'status_active'])->name('category.status_active');
    Route::post('/category/status_inactive', [CategoriesController::class, 'status_inactive'])->name('category.status_inactive');


    //SubCategory Management
    Route::get('/sub-category', [SubCategoriesController::class, 'index'])->name('subcategory');
    Route::post('/subcategory/anyData', [SubCategoriesController::class, 'anyData'])->name('subcategory.anyData');
    Route::post('/subcategory/store', [SubCategoriesController::class, 'store'])->name('subcategory.store');
    Route::post('/subcategory/UpdateAll', 'SubCategoriesController@subcategoryUpdateAll')->name('subcategoryUpdateAll');
    Route::post('subcategory/edit', 'SubCategoriesController@edit')->name('subcategory.edit');
    Route::post('subcategory/show', 'SubCategoriesController@show')->name('subcategory.show');
    Route::get('/subcategory/delete/{id}', 'SubCategoriesController@destroy')->name('subcategory.delete');
    Route::post('/subcategory/status_active', [SubCategoriesController::class, 'status_active'])->name('subcategory.status_active');
    Route::post('/subcategory/status_inactive', [SubCategoriesController::class, 'status_inactive'])->name('subcategory.status_inactive');
    Route::post('/{id}/get-subcategories', [SubCategoriesController::class, 'getsubcategories'])->name('getsubcategories');

    //Brand Management
    Route::get('/brand', [BrandController::class, 'index'])->name('brand');
    Route::post('/brand/anyData', [BrandController::class, 'anyData'])->name('brand.anyData');
    Route::post('/brand/store', [BrandController::class, 'store'])->name('brand.store');
    Route::post('/brand/UpdateAll', 'BrandController@brandUpdateAll')->name('brandUpdateAll');
    Route::post('brand/edit', 'BrandController@edit')->name('brand.edit');
    Route::post('brand/show', 'BrandController@show')->name('brand.show');
    Route::get('/brand/delete/{id}', 'BrandController@destroy')->name('brand.delete');
    Route::post('/brand/status_active', [BrandController::class, 'status_active'])->name('brand.status_active');
    Route::post('/brand/status_inactive', [BrandController::class, 'status_inactive'])->name('brand.status_inactive');

    // Area Management
    Route::get('/area', 'AreaController@index')->name('area');
    Route::post('/area/store', 'AreaController@store')->name('area.store');
    Route::get('/area/delete/{id}', 'AreaController@destroy')->name('area.delete');
    Route::post('/area/show', 'AreaController@show')->name('area.show');
    Route::post('area/edit/', 'AreaController@edit')->name('area.edit');
    Route::post('area/anyData', 'AreaController@anyData')->name('area.anyData');
    Route::post('/area/updateAll', 'AreaController@areaupdateAll')->name('areaUpdateAll');
    Route::post('/area/status_active', [AreaController::class, 'status_active'])->name('area.status_active');
    Route::post('/area/status_inactive', [AreaController::class, 'status_inactive'])->name('area.status_inactive');

    //Region Management
    Route::get('/region', [RegionController::class, 'index'])->name('region');
    Route::post('/region/anyData', [RegionController::class, 'anyData'])->name('region.anyData');
    Route::post('/region/s  tore', [RegionController::class, 'store'])->name('region.store');
    Route::post('/region/UpdateAll', 'RegionController@regionUpdateAll')->name('regionUpdateAll');
    Route::post('region/edit', 'RegionController@edit')->name('region.edit');
    Route::post('region/show', 'RegionController@show')->name('region.show');
    Route::get('/region/delete/{id}', 'RegionController@destroy')->name('region.delete');
    Route::post('/region/status_active', [RegionController::class, 'status_active'])->name('region.status_active');
    Route::post('/region/status_inactive', [RegionController::class, 'status_inactive'])->name('region.status_inactive');
    Route::post('/{id}/get-region', [RegionController::class, 'getregion'])->name('getregion');
    //track order
 
        Route::get('/track-order', [TrackOrderController::class, 'index'])->name('trackorder');
        Route::post('/track-order/anyData', [TrackOrderController::class, 'anyData'])->name('track-order.anyData');
        Route::post('/track-order/store', 'TrackOrderController@store')->name('track-order.store');
 
    //Customer Management
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::post('/customer/anyData', [CustomerController::class, 'anyData'])->name('customer.anyData');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('/customer/UpdateAll', 'CustomerController@customerUpdateAll')->name('customerUpdateAll');
    Route::post('customer/edit', 'CustomerController@edit')->name('customer.edit');
    Route::get('/customer/delete/{id}', 'CustomerController@destroy')->name('customer.delete');
    Route::post('/customer/status_active', [CustomerController::class, 'status_active'])->name('customer.status_active');
    Route::post('/customer/status_inactive', [CustomerController::class, 'status_inactive'])->name('customer.status_inactive');
    Route::get('/customer/show/{id}', 'CustomerController@show')->name('customer.show');
    Route::get('/customer/pendinglistshow/{id}', 'CustomerController@pendinglistshow')->name('customer.pendinglistshow');

    //country Management
    Route::get('/country', [CountryController::class, 'index'])->name('country');
    Route::post('/country/anyData', [CountryController::class, 'anyData'])->name('country.anyData');
    Route::post('/country/store', [CountryController::class, 'store'])->name('country.store');
    Route::post('/country/UpdateAll', 'CountryController@countryUpdateAll')->name('countryUpdateAll');
    Route::post('country/edit', 'CountryController@edit')->name('country.edit');
    Route::get('country/show', 'CountryController@show')->name('country.show');
    Route::get('/country/delete/{id}', 'CountryController@destroy')->name('country.delete');
    Route::post('/country/status_active', [CountryController::class, 'status_active'])->name('country.status_active');
    Route::post('/country/status_inactive', [CountryController::class, 'status_inactive'])->name('country.status_inactive');

    //Wholesaler Management
    Route::get('/wholesaler', [WholesalerController::class, 'index'])->name('wholesaler');
    Route::post('/wholesaler/anyData', [WholesalerController::class, 'anyData'])->name('wholesaler.anyData');
    Route::post('/wholesaler/store', [WholesalerController::class, 'store'])->name('wholesaler.store');
    Route::post('wholesaler/edit', 'WholesalerController@edit')->name('wholesaler.edit');
    Route::post('wholesaler/show', 'WholesalerController@show')->name('wholesaler.show');
    Route::post('/wholesaler/UpdateAll', 'WholesalerController@wholesalerUpdateAll')->name('wholesalerUpdateAll');
    Route::get('/wholesaler/delete/{id}', 'WholesalerController@destroy')->name('wholesaler.delete');
    Route::post('/wholesaler/status_active', [WholesalerController::class, 'status_active'])->name('wholesaler.status_active');
    Route::post('/wholesaler/status_inactive', [WholesalerController::class, 'status_inactive'])->name('wholesaler.status_inactive');
    Route::post('/wholesaler/invitelink', [WholesalerController::class, 'invitelink'])->name('wholesaler.invitelink');

    Route::get('/wholesaler-invite', [WholesalerController::class, 'indexinvite'])->name('wholesalerinvite');
    Route::post('/wholesaler-invite/anyData', [WholesalerController::class, 'anyDataInvite'])->name('wholesalerinvite.anyData');

    Route::get('/technician-pending', [CustomerController::class, 'indexpending'])->name('technicianpending');
    Route::post('/technician-pending/anyData', [CustomerController::class, 'anyDataPending'])->name('technicianpending.anyData');
    Route::post('/technician-pending/UpdateAll', 'CustomerController@customerPendingUpdateAll')->name('customerPendingUpdateAll');

    //SubAdmin Management
    Route::get('/sub-admin', [SubadminController::class, 'index'])->name('subadmin');
    Route::post('/subadmin/anyData', [SubadminController::class, 'anyData'])->name('subadmin.anyData');
    Route::post('/subadmin/store', [SubadminController::class, 'store'])->name('subadmin.store');
    Route::post('/subadmin/UpdateAll', 'SubadminController@subadminUpdateAll')->name('subadminUpdateAll');
    Route::post('/subadmin/store', [SubadminController::class, 'store'])->name('subadmin.store');
    Route::post('subadmin/edit', 'SubadminController@edit')->name('subadmin.edit');
    Route::post('subadmin/show', 'SubadminController@show')->name('subadmin.show');
    Route::post('/subadmin/status_active', [SubadminController::class, 'status_active'])->name('subadmin.status_active');
    Route::post('/subadmin/status_inactive', [SubadminController::class, 'status_inactive'])->name('subadmin.status_inactive');
    Route::get('/subadmin/delete/{id}', 'SubadminController@destroy')->name('subadmin.delete');

    //Country Amin
    Route::get('/country-admin', [CountryAdminController::class, 'index'])->name('country-admin');
    Route::post('/country-admin/anyData', [CountryAdminController::class, 'anyData'])->name('country-admin.anyData');
    Route::post('/country-admin/store', [CountryAdminController::class, 'store'])->name('country-admin.store');
    Route::post('/country-admin/UpdateAll', 'CountryAdminController@subadminUpdateAll')->name('countryAdminUpdateAll');
    Route::post('country-admin/edit', 'CountryAdminController@edit')->name('country-admin.edit');
    Route::post('country-admin/show', 'CountryAdminController@show')->name('country-admin.show');
    Route::post('/country-admin/status_active', [CountryAdminController::class, 'status_active'])->name('country-admin.status_active');
    Route::post('/country-admin/status_inactive', [CountryAdminController::class, 'status_inactive'])->name('country-admin.status_inactive');
    Route::get('/country-admin/delete/{id}', 'CountryAdminController@destroy')->name('country-admin.delete');

    //Product Management
    Route::get('/product', [ProductController::class, 'index'])->name('product');
    Route::post('/product/anyData', [ProductController::class, 'anyData'])->name('product.anyData');
    Route::get('product/show/{id}', [ProductController::class,'show'])->name('product.show');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/product/updateAll', [ProductController::class, 'updateAll'])->name('product.updateAll');
    Route::post('/product/add-more-variant', [ProductController::class, 'add_more_variant'])->name('product.add_more_variant');
    Route::post('/product/variant/remove', [ProductController::class, 'remove_variant'])->name('product.variant.remove');
    Route::post('/product/status_active', [ProductController::class, 'status_active'])->name('product.status_active');
    Route::post('/product/status_inactive', [ProductController::class, 'status_inactive'])->name('product.status_inactive');
    Route::post('product/getsubcatlist', [ProductController::class, 'getSubcatlist'])->name('product.getsubcatlist');
    Route::post('/product/bestseller', [ProductController::class, 'ischecked'])->name('product.ischecked');
    Route::post('/product/offer', [ProductController::class, 'offervalue'])->name('product.offervalue');


    // Delivery Management
    // Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery');
    // Route::post('/delivery/store', [DeliveryController::class, 'deliverystore'])->name('deliverystore');
    // Route::post('delivery/edit/', [DeliveryController::class, 'deliveryedit'])->name('deliveryedit');
    // Route::post('/delivery/show', 'DeliveryController@show')->name('delivery.show');
    // Route::post('/delivery/status_active', [DeliveryController::class, 'status_active'])->name('delivery.status_active');
    // Route::post('/delivery/status_inactive', [DeliveryController::class, 'status_inactive'])->name('delivery.status_inactive');
    // Route::post('delivery/anyData', 'DeliveryController@anyData')->name('delivery.anyData');
    // Route::post('/delivery/updateAll', 'DeliveryController@deliveryupdateAll')->name('deliveryupdateAll');

    // Offer Management
    Route::get('/offer', [OfferController::class, 'index'])->name('offer');
    Route::post('/offer/store', [OfferController::class, 'offerstore'])->name('offerstore');
    Route::get('/offer/delete/{id}', 'OfferController@destroy')->name('offer.delete');
    Route::post('offer/edit/', [OfferController::class, 'offeredit'])->name('offeredit');
    Route::post('/offer/show', 'OfferController@show')->name('offer.show');
    Route::post('/offer/status_active', [OfferController::class, 'status_active'])->name('offer.status_active');
    Route::post('/offer/status_inactive', [OfferController::class, 'status_inactive'])->name('offer.status_inactive');
    Route::post('offer/anyData', 'OfferController@anyData')->name('offer.anyData');
    Route::post('/offer/updateAll', 'OfferController@offerUpdateAll')->name('offerUpdateAll');
    Route::get('/offer/sendMail', 'OfferController@sendMail')->name('sendMail');


    // Discount Management
    Route::get('/discount', [DiscountController::class, 'index'])->name('discount');
    Route::post('/discount/store', [DiscountController::class, 'discountstore'])->name('discountstore');
    Route::get('/discount/delete/{id}', 'DiscountController@destroy')->name('discount.delete');
    Route::post('/discount/updateAll', 'DiscountController@discountUpdateAll')->name('discountUpdateAll');
    Route::post('discount/anyData', 'DiscountController@anyData')->name('discount.anyData');
    Route::post('discount/edit/', [DiscountController::class, 'discountedit'])->name('discountedit');
    Route::post('/discount/show', 'DiscountController@show')->name('discount.show');
    Route::post('/discount/status_active', [DiscountController::class, 'status_active'])->name('discount.status_active');
    Route::post('/discount/status_inactive', [DiscountController::class, 'status_inactive'])->name('discount.status_inactive');

    
    // Bogo Management
    Route::get('/bogo', 'BogoController@index')->name('bogo');
    Route::get('/bogo/create', 'BogoController@create')->name('bogo.create');
    Route::post('/bogo/store', 'BogoController@store')->name('bogo.store');
    Route::get('bogo/edit/{id}', 'BogoController@edit')->name('bogo.edit');
    Route::post('bogo/update/{id}', 'BogoController@update')->name('bogo.update');
    Route::get('/bogo/show/{id}', 'BogoController@show')->name('bogo.show');
    Route::post('/bogo/status_active', [BogoController::class, 'status_active'])->name('bogo.status_active');
    Route::post('/bogo/status_inactive', [BogoController::class, 'status_inactive'])->name('bogo.status_inactive');
    Route::post('/bogo/updateAll', 'BogoController@updateAll')->name('bogoUpdateAll');
    Route::post('bogo/anyData', 'BogoController@anyData')->name('bogo.anyData');
    

    //Product Rating 
    Route::get('/product-rating/{id}', [ProductRatingController::class, 'index'])->name('product.rating');
    Route::post('/product-rating/anyData', [ProductRatingController::class, 'anyData'])->name('productRating.anyData');

    //Request Product Management
    Route::get('/request-product', [RequestProductController::class, 'index'])->name('request_product');
    Route::post('/request_product/anyData', [RequestProductController::class, 'anyData'])->name('request_product.anyData');
    Route::post('/requestproduct/UpdateAll', 'RequestProductController@requestproductUpdateAll')->name('requestproductUpdateAll');

    //Advertise Management
    Route::get('/advertise', [AdvertiseController::class, 'index'])->name('advertise');
    Route::post('/advertise/anyData', [AdvertiseController::class, 'anyData'])->name('advertise.anyData');
    Route::post('/advertise/store', [AdvertiseController::class, 'store'])->name('advertise.store');
    Route::post('/advertise/UpdateAll', 'AdvertiseController@advertiseUpdateAll')->name('advertiseUpdateAll');
    Route::post('advertise/edit', 'AdvertiseController@edit')->name('advertise.edit');
    Route::post('advertise/show', 'AdvertiseController@show')->name('advertise.show');
    Route::post('/advertise/status_active', [AdvertiseController::class, 'status_active'])->name('advertise.status_active');
    Route::post('/advertise/status_inactive', [AdvertiseController::class, 'status_inactive'])->name('advertise.status_inactive');
    Route::get('/advertise/delete/{id}', 'AdvertiseController@destroy')->name('advertise.delete');

    //Quote Management
    Route::get('/quote', [QuoteController::class, 'index'])->name('quote');
    Route::post('/quote/anyData', [QuoteController::class, 'anyData'])->name('quote.anyData');
    Route::get('/quote/show/{id}', 'QuoteController@show')->name('quote.show');
    Route::get('/quote/edit/{id}', 'QuoteController@edit')->name('quote.edit');
    Route::post('quote/update/{id}', 'QuoteController@update')->name('quote.update');

    //Blog Management
    Route::get('/blog', [BlogController::class, 'index'])->name('blog');
    Route::post('/blog/anyData', [BlogController::class, 'anyData'])->name('blog.anyData');
    Route::get('/blog/create', 'BlogController@create')->name('blog.create');
    Route::post('/blog/store', 'BlogController@store')->name('blog.store');
    Route::get('/blog/show/{id}', 'BlogController@show')->name('blog.show');
    Route::get('/blog/edit/{id}', 'BlogController@edit')->name('blog.edit');
    Route::post('blog/update/{id}', 'BlogController@update')->name('blog.update');
    Route::post('/blog/blogUpdateAll', 'BlogController@blogUpdateAll')->name('blogUpdateAll');
    Route::get('/blog/delete/{id}', 'BlogController@destroy')->name('blog.delete');
    Route::post('/blog/status_active', [BlogController::class, 'status_active'])->name('blog.status_active');
    Route::post('/blog/status_inactive', [BlogController::class, 'status_inactive'])->name('blog.status_inactive');
    //Hamburger Management
    Route::get('/hamburger', [HamburgerController::class, 'index'])->name('hamburger');
    Route::post('/hamburger/anyData', [HamburgerController::class, 'anyData'])->name('hamburger.anyData');
    Route::get('/hamburger/create', 'HamburgerController@create')->name('hamburger.create');
    Route::post('/hamburger/store', 'HamburgerController@store')->name('hamburger.store');
    Route::get('/hamburger/show/{id}', 'HamburgerController@show')->name('hamburger.show');
    Route::get('/hamburger/edit/{id}', 'HamburgerController@edit')->name('hamburger.edit');
    Route::post('hamburger/update/{id}', 'HamburgerController@update')->name('hamburger.update');
    Route::post('/hamburger/hamburgerUpdateAll', 'HamburgerController@hamburgerUpdateAll')->name('hamburgerUpdateAll');
    Route::get('/hamburger/delete/{id}', 'HamburgerController@destroy')->name('hamburger.delete');

    //Time Frame Management
    Route::get('/time_frame', [TimeFrameController::class, 'index'])->name('time_frame');
    Route::post('/time_frame/anyData', [TimeFrameController::class, 'anyData'])->name('time_frame.anyData');
    Route::post('/time_frame/store', [TimeFrameController::class, 'store'])->name('time_frame.store');
    Route::post('time_frame/edit', 'TimeFrameController@edit')->name('time_frame.edit');
    Route::post('time_frame/show', 'TimeFrameController@show')->name('time_frame.show');
    Route::post('/time_frame/UpdateAll', 'TimeFrameController@timeframeUpdateAll')->name('timeframeUpdateAll');
    Route::post('/time_frame/status_active', [TimeFrameController::class, 'status_active'])->name('time_frame.status_active');
    Route::post('/time_frame/status_inactive', [TimeFrameController::class, 'status_inactive'])->name('time_frame.status_inactive');
    Route::get('/time_frame/delete/{id}', 'TimeFrameController@destroy')->name('time_frame.delete');

    //Material Category Management
    Route::get('/material_category', [MaterialCategoryController::class, 'index'])->name('material_category');
    Route::post('/material_category/anyData', [MaterialCategoryController::class, 'anyData'])->name('material_category.anyData');
    Route::post('/material_category/store', [MaterialCategoryController::class, 'store'])->name('material_category.store');
    Route::post('material_category/edit', 'MaterialCategoryController@edit')->name('material_category.edit');
    Route::post('material_category/show', 'MaterialCategoryController@show')->name('material_category.show');
    Route::post('/material_category/UpdateAll', 'MaterialCategoryController@materialcategoryUpdateAll')->name('materialcategoryUpdateAll');
    Route::post('/material_category/status_active', [MaterialCategoryController::class, 'status_active'])->name('material_category.status_active');
    Route::post('/material_category/status_inactive', [MaterialCategoryController::class, 'status_inactive'])->name('material_category.status_inactive');
    Route::get('/material_category/delete/{id}', 'MaterialCategoryController@destroy')->name('material_category.delete');

    //Loyalty Management
    Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty');
    Route::post('/loyalty/anyData', [LoyaltyController::class, 'anyData'])->name('loyalty.anyData');
    Route::post('/loyalty/store', [LoyaltyController::class, 'store'])->name('loyalty.store');
    Route::post('loyalty/edit', 'LoyaltyController@edit')->name('loyalty.edit');
    Route::post('loyalty/show', 'LoyaltyController@show')->name('loyalty.show');
    Route::post('/loyalty/status_active', [LoyaltyController::class, 'status_active'])->name('loyalty.status_active');
    Route::post('/loyalty/status_inactive', [LoyaltyController::class, 'status_inactive'])->name('loyalty.status_inactive');
    Route::post('/loyalty/UpdateAll', 'LoyaltyController@loyaltyUpdateAll')->name('loyaltyUpdateAll');

    //Driver Management
    Route::get('/driver', 'DriverController@index')->name('driver');
    Route::get('/driver/create', 'DriverController@create')->name('driver.create');
    Route::post('/driver/store', 'DriverController@store')->name('driver.store');
    Route::get('/driver/delete/{id}', 'DriverController@destroy')->name('driver.delete');
    Route::get('/driver/show/{id}', 'DriverController@show')->name('driver.show');
    Route::get('/driver/rate-review/{id}', 'DriverController@rateAndReview')->name('driver.rate-review');
    Route::get('driver/edit/{id}', 'DriverController@edit')->name('driver.edit');
    Route::post('driver/update/{id}', 'DriverController@update')->name('driver.update');
    Route::post('driver/anyData', 'DriverController@anyData')->name('driver.data');
    Route::post('/driver/UpdateAll', 'DriverController@driverUpdateAll')->name('driverUpdateAll');


    //Admin Order
    Route::get('/adminorder', [OrderController::class, 'index'])->name('adminorder');
    Route::post('/adminorder/anyData', [OrderController::class, 'anyData'])->name('adminorder.anyData');
    Route::post('/adminorder/update-order-status', [OrderController::class, 'updateOrderStatus'])->name('adminorder.updateOrderStatus');
    Route::get('/adminorder/{id}/show', [OrderController::class, 'show'])->name('adminorder.show');
    Route::get('/adminorder/{id}/print', [OrderController::class, 'print'])->name('adminorder.print');
    Route::post('/adminorder/UpdateAll', 'OrderController@orderstatusUpdateAll')->name('orderstatusUpdateAll');
    Route::post('/send-sms', [OrderController::class,'sendsms'])->name('send-sms');

    //Admin Purchase Order
    Route::get('/purchase-order', [PurchaseOrderController::class, 'index'])->name('adminpurchaseorder');
    Route::post('/purchase-order/anyData', [PurchaseOrderController::class, 'anyData'])->name('adminpurchaseorder.anyData');
    Route::post('/purchase-order/store', 'PurchaseOrderController@store')->name('adminpurchaseorder.store');

    //User Report
    Route::get('/user-report', [UserReportController::class, 'index'])->name('userreport');
    Route::post('/userreport/anyData', [UserReportController::class, 'anyData'])->name('userreport.anyData');
    Route::post('/export_userreport', [UserReportController::class,'export_userreport'])->name('export_userreport');
    Route::post('/export_userpdf', [UserReportController::class,'export_userpdf'])->name('export_userpdf');

    //Earning Report
    // Route::get('/earning-report', [EarningReportController::class, 'index'])->name('earningreport');
    // Route::post('/earningreport/anyData', [EarningReportController::class, 'anyData'])->name('earningreport.anyData');
    // Route::post('/export_earningreport', [EarningReportController::class,'export_earningreport'])->name('export_earningreport');
    // Route::post('/export_earningpdf', [EarningReportController::class,'export_earningpdf'])->name('export_earningpdf');
    // Route::get('/earningreport/{id}/show', [EarningReportController::class, 'show'])->name('earningreport.show');
    // Route::post('/productreport/anyData', [EarningReportController::class, 'productreportanyData'])->name('productreport.anyData');

    //Stock Report
    Route::get('/stock-report', [StockReportController::class, 'index'])->name('stockreport');
    Route::post('/stockreport/anyData', [StockReportController::class, 'anyData'])->name('stockreport.anyData');
    Route::post('/export_stockreport', [StockReportController::class,'export_stockreport'])->name('export_stockreport');
    Route::post('/export_stockpdf', [StockReportController::class,'export_stockpdf'])->name('export_stockpdf');
    Route::get('/stockreport/{id}/show', [StockReportController::class, 'show'])->name('stockreport.show');
    Route::post('/productreport/anyData', [StockReportController::class, 'productreportanyData'])->name('productreport.anyData');

    //Order Report
    Route::get('/order-report', [OrderReportController::class, 'index'])->name('orderreport');
    Route::post('/orderreport/anyData', [OrderReportController::class, 'anyData'])->name('orderreport.anyData');
    Route::post('/export_orderreport', [OrderReportController::class,'export_orderreport'])->name('export_orderreport');
    Route::post('/export_orderpdf', [OrderReportController::class,'export_stockpdf'])->name('export_orderpdf');
    Route::get('/orderreport/show', [OrderReportController::class, 'show'])->name('orderreport.show');
    Route::post('/orderreport/anyData', [OrderReportController::class, 'anyData'])->name('orderreport.anyData');

    // Loyalty Points Report
    Route::get('/loyalty-report', [LoyaltyReportController::class, 'index'])->name('loyaltyreport');
    Route::post('/export_loyaltyreport', [LoyaltyReportController::class,'export_loyaltyreport'])->name('export_loyaltyreport');
     Route::post('/loyaltyreport/anyData', [LoyaltyReportController::class, 'loyaltyreportanyData'])->name('loyaltyreport.anyData');


    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('adminHome');
    Route::post('/filter', [DashboardController::class, 'index'])->name('dashboardfilter');

    //Search
    Route::get('/search', [DashboardController::class, 'search'])->name('adminSearch');
    Route::post('/find', [DashboardController::class, 'find'])->name('adminFind');

    // Webmaster Route
    Route::get('/webmaster', [WebmasterSettingsController::class, 'edit'])->name('webmasterSettings');
    Route::post('/webmaster', [WebmasterSettingsController::class, 'update'])->name('webmasterSettingsUpdate');
    Route::post('/webmaster/languages/store', [WebmasterSettingsController::class, 'language_store'])->name('webmasterLanguageStore');
    Route::post('/webmaster/languages/store', [WebmasterSettingsController::class, 'language_store'])->name('webmasterLanguageStore');
    Route::post('/webmaster/languages/update', [WebmasterSettingsController::class, 'language_update'])->name('webmasterLanguageUpdate');
    Route::get('/webmaster/languages/destroy/{id}', [WebmasterSettingsController::class, 'language_destroy'])->name('webmasterLanguageDestroy');
    Route::get('/webmaster/seo/repair', [WebmasterSettingsController::class, 'seo_repair'])->name('webmasterSEORepair');

    Route::post('/webmaster/mail/smtp', [WebmasterSettingsController::class, 'mail_smtp_check'])->name('mailSMTPCheck');
    Route::post('/webmaster/mail/test', [WebmasterSettingsController::class, 'mail_test'])->name('mailTest');


    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'updateSiteInfo'])->name('settingsUpdateSiteInfo');



    //Users & Permissions
    Route::get('/change-password', [UsersController::class, 'changePassword'])->name('admin-change-password');
    Route::post('/update-password', [UsersController::class, 'updatePassword'])->name('admin-update-password');


    //Route::get('/users', [UsersController::class, 'index'])->name('users');
    //Route::get('/users/create/', [UsersController::class, 'create'])->name('usersCreate');
    //Route::post('/users/store', [UsersController::class, 'store'])->name('usersStore');
    Route::get('/user-profile/{id}/edit', [UsersController::class, 'edit'])->name('usersEdit');
    Route::post('/users/{id}/update', [UsersController::class, 'update'])->name('usersUpdate');
    //Route::get('/users/destroy/{id}', [UsersController::class, 'destroy'])->name('usersDestroy');
    //Route::post('/users/updateAll', [UsersController::class, 'updateAll'])->name('usersUpdateAll');

    // Route::get('/users/permissions/create/', [UsersController::class, 'permissions_create'])->name('permissionsCreate');
    // Route::post('/users/permissions/store', [UsersController::class, 'permissions_store'])->name('permissionsStore');
    // Route::get('/users/permissions/{id}/edit', [UsersController::class, 'permissions_edit'])->name('permissionsEdit');
    // Route::post('/users/permissions/{id}/update', [UsersController::class, 'permissions_update'])->name('permissionsUpdate');
    // Route::post('/users/permissions/{id}/save', [UsersController::class, 'update_custom_home'])->name('permissionsHomePageUpdate');
    // Route::get('/users/permissions/destroy/{id}', [UsersController::class, 'permissions_destroy'])->name('permissionsDestroy');

    // Route::post('/permissions-links/store', [UsersController::class, 'links_store'])->name('customLinksStore');
    // Route::post('/permissions-links/update', [UsersController::class, 'links_update'])->name('customLinksUpdate');
    // Route::get('/permissions-links/edit/{id?}/{p_id?}', [UsersController::class, 'links_edit'])->name('customLinksEdit');
    // Route::get('/permissions-links/destroy/{id?}/{p_id?}', [UsersController::class, 'links_destroy'])->name('customLinksDestroy');
    // Route::get('/permissions-links/list/{p_id?}', [UsersController::class, 'links_list'])->name('customLinksList');

    Route::get('/store', [StoreController::class,'index'])->name('store');
    Route::post('/store/anyData', [StoreController::class,'anyData'])->name('store.anyData');
    Route::get('/store/create', 'StoreController@create')->name('store.create');
    Route::post('/store/store', [StoreController::class,'store'])->name('store.store');
    Route::get('store/edit/{id}','StoreController@edit')->name('store.edit');
    Route::post('store/update/{id}','StoreController@update')->name('store.update');
    Route::get('/store/show/{id}','StoreController@show')->name('store.show');
    Route::post('/store/UpdateAll', 'StoreController@wholesalerstoreUpdateAll')->name('storeUpdateAll');
    Route::post('/store/status_active', [StoreController::class,'status_active'])->name('store.status_active');
    Route::post('/store/status_inactive', [StoreController::class,'status_inactive'])->name('store.status_inactive');
    Route::post('get-formatted-address', [StoreController::class, 'get_formatted_address'])->name('getAddress');


    /* Transaction management*/
    Route::get('/transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
    Route::post('/transaction/store', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/transaction/delete/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    Route::get('/transaction/{id}/edit', [TransactionController::class, 'edit'])->name('transaction.edit');
    Route::post('/transaction/update/{id}', [TransactionController::class, 'update'])->name('transaction.update');
    Route::get('/transaction/show/{id}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::post('/transaction', [TransactionController::class, 'index'])->name('transaction.filter');
    Route::post('/transaction/updateAll', [TransactionController::class, 'updateAll'])->name('transaction.updateAll');
    Route::post('/transaction/anyData', [TransactionController::class, 'anyData'])->name('transaction.anyData');
    Route::get('/transaction/activate/{id}', [TransactionController::class, 'activate'])->name('transaction.activate');
    Route::get('/transaction/de-activate/{id}', [TransactionController::class, 'deActivate'])->name('transaction.deactivate');


    //label

    Route::get('/label', 'LabelController@index')->name('label');
    Route::get('/label/create', 'LabelController@create')->name('label.create');
    Route::post('/label/store', 'LabelController@store')->name('label.store');
    Route::get('/label/delete/{id}', 'LabelController@destroy')->name('label.delete');
    Route::get('/label/show/{id}', 'LabelController@show')->name('label.show');
    Route::get('label/edit/{id}', 'LabelController@edit')->name('label.edit');
    Route::post('label/update/{id}', 'LabelController@update')->name('label.update');
    Route::post('label/anyData', 'LabelController@anyData')->name('label.anyData');
    Route::post('/label/labelUpdateAll', 'LabelController@labelUpdateAll')->name('labelUpdateAll');


    // CMS Management
    Route::get('/cms', 'CmsController@index')->name('cms');
    Route::get('/cms/create', 'CmsController@create')->name('cms.create');
    Route::post('/cms/store', 'CmsController@store')->name('cms.store');
    Route::get('/cms/delete/{id}', 'CmsController@destroy')->name('cms.delete');
    Route::get('/cms/show/{id}', 'CmsController@show')->name('cms.show');
    Route::get('cms/edit/{id}', 'CmsController@edit')->name('cms.edit');
    Route::post('cms/update/{id}', 'CmsController@update')->name('cms.update');
    Route::post('cms/anyData', 'CmsController@anyData')->name('cms.anyData');
    Route::post('/cms/cmsUpdateAll', 'CmsController@updateAll')->name('cmsUpdateAll');
    Route::post('/cms/status_active', [CmsController::class, 'status_active'])->name('cms.status_active');
    Route::post('/cms/status_inactive', [CmsController::class, 'status_inactive'])->name('cms.status_inactive');




    // emailtemplate Management
    Route::get('/emailtemplate', 'EmailTemplateController@index')->name('emailtemplate');
    Route::get('/emailtemplate/create', 'EmailTemplateController@create')->name('emailtemplate.create');
    Route::post('/emailtemplate/store', 'EmailTemplateController@store')->name('emailtemplate.store');
    Route::get('emailtemplate/edit/{id}', 'EmailTemplateController@edit')->name('emailtemplate.edit');
    Route::post('emailtemplate/update/{id}', 'EmailTemplateController@update')->name('emailtemplate.update');
    Route::get('emailtemplate/show/{id}', 'EmailTemplateController@show')->name('emailtemplate.show');
    Route::post('emailtemplate/anyData', 'EmailTemplateController@anyData')->name('emailtemplate.anyData');
    Route::post('/emailtemplate/emailt_UpdateAll', 'EmailTemplateController@emailt_UpdateAll')->name('emailt_UpdateAll');
    
    
    //Banner Management
    Route::get('/banner', 'BannerController@index')->name('banner');
    Route::post('bannerdata', 'BannerController@anyData')->name('banner.data');
    Route::get('/banner/create', 'BannerController@create')->name('banner.create');
    Route::post('/banner/store', 'BannerController@store')->name('banner.store');
    Route::get('banner/edit/{id}', 'BannerController@edit')->name('banner.edit');
    Route::post('banner/update/{id}', 'BannerController@update')->name('banner.update');
    Route::get('/banner/show/{id}', 'BannerController@show')->name('banner.show');
    Route::post('/banner/updateAll', 'BannerController@bannerstatusupdate')->name('bannerUpdateAll');
    Route::get('/banner/search', 'BannerController@search')->name('bannerSearch');
    Route::delete('banner-destroy/{id}', 'BannerController@destroy')->name('banner.destroy');
    Route::post('/banner-image', 'BannerController@removeimage')->name('banner.image');
    Route::post('/banner/status_active', [BannerController::class, 'status_active'])->name('banner.status_active');
    Route::post('/banner/status_inactive', [BannerController::class, 'status_inactive'])->name('banner.status_inactive');
    Route::post('/banner/highlight', [BannerController::class, 'ischecked'])->name('banner.ischecked');
    Route::post('/banner/offer', [BannerController::class, 'offervalue'])->name('banner.offervalue');
    

    //  //Banner Management
    //  Route::get('/deliveryinformation', [DeliveryInformationController::class,'index'])->name('deliveryinformation');
    //  Route::post('deliveryinformationdata', [DeliveryInformationController::class,'anyData'])->name('deliveryinformation.data');
    //  Route::get('/deliveryinformationdata/create', [DeliveryInformationController::class,'create'])->name('deliveryinformation.create');
    //  Route::post('/deliveryinformationdata/store', [DeliveryInformationController::class,'store'])->name('deliveryinformation.store');
    //  Route::get('deliveryinformationdata/edit/{id}', [DeliveryInformationController::class,'edit'])->name('deliveryinformation.edit');
    //  Route::post('deliveryinformationdata/update/{id}', [DeliveryInformationController::class,'update'])->name('deliveryinformation.update');
    //  Route::get('/deliveryinformationdata/show/{id}', [DeliveryInformationController::class,'show'])->name('deliveryinformation.show');
    //  Route::post('/deliveryinformationdata/updateAll', [DeliveryInformationController::class,'bannerstatusupdate'])->name('deliveryinformationUpdateAll');
    //  Route::get('/deliveryinformationdata/search', [DeliveryInformationController::class,'search'])->name('deliveryinformationSearch');
    //  Route::delete('deliveryinformationdata-destroy/{id}', [DeliveryInformationController::class,'destroy'])->name('deliveryinformation.destroy');
    //  Route::post('/deliveryinformationdata-image', [DeliveryInformationController::class,'removeimage'])->name('deliveryinformation.image');
    //  Route::post('/deliveryinformationdata/status_active', [DeliveryInformationController::class, 'status_active'])->name('deliveryinformation.status_active');
    //  Route::post('/deliveryinformationdata/status_inactive', [DeliveryInformationController::class, 'status_inactive'])->name('deliveryinformation.status_inactive');
 
    // Promo Code List Management
    Route::get('/promocode', 'PromocodeController@index')->name('promocode');
    Route::get('/promocode/create', 'PromocodeController@create')->name('promocode.create');
    Route::post('/promocode/store', 'PromocodeController@store')->name('promocode.store');
    Route::get('promocode/edit/{id}', 'PromocodeController@edit')->name('promocode.edit');
    Route::post('promocode/update/{id}', 'PromocodeController@update')->name('promocode.update');
    Route::get('/promocode/show/{id}', 'PromocodeController@show')->name('promocode.show');
    Route::post('promocode/anyData', 'PromocodeController@anyData')->name('promocode.anyData');
    Route::post('/promocode/updateAll', 'PromocodeController@updateAll')->name('promocodeUpdateAll');
    Route::post('/promocode/status_active', [PromocodeController::class, 'status_active'])->name('promocode.status_active');
    Route::post('/promocode/status_inactive', [PromocodeController::class, 'status_inactive'])->name('promocode.status_inactive');
    // // Notification routes
    // Route::resource('notification','NotificationController');
    // Route::post('notification/{id}','NotificationController@update')->name('notification.update');
    // Route::post('notificationdata', 'NotificationController@anyData')->name('notification.data');
    // Route::get('notification/edit/{id}','NotificationController@edit')->name('notification.edit');
    // Route::get('/notification/show/{id}','NotificationController@show')->name('notification.show');
    // Route::delete('notification-delete/{id}', 'NotificationController@destroy')->name('notification.delete');
    // Route::post('/notification-updateAll', 'NotificationController@notificatonupdate')->name('notificationUpdateAll');

    //Users report
    Route::get('/report/users', 'GenerateReportController@users')->name('users-report');
    Route::post('/report/users-data', 'GenerateReportController@userAnyData')->name('report.users');
    Route::post('/report/users-filter', 'GenerateReportController@users')->name('report.users-filter');
    Route::post('/report/users-export', 'GenerateReportController@usersExport')->name('report.users-export');
    Route::post('/report/users-export-pdf', 'GenerateReportController@usersExportPdf')->name('report.users-export-pdf');


    //Instructor report
    Route::get('/report/instructor', 'GenerateReportController@instructor')->name('instructor-report');
    Route::post('/report/instructor-data', 'GenerateReportController@instructorAnyData')->name('report.instructor');
    Route::post('/report/instructor-filter', 'GenerateReportController@instructor')->name('report.instructor-filter');
    Route::post('/report/instructor-export', 'GenerateReportController@instructorExport')->name('report.instructor-export');
    Route::post('/report/instructor-export-pdf', 'GenerateReportController@instructorExportPdf')->name('report.instructor-export-pdf');


    //Earning report
    Route::get('/report/earning', 'GenerateReportController@earning')->name('earning-report');
    Route::post('/report/earning-data', 'GenerateReportController@earningAnyData')->name('report.earning');
    Route::post('/report/earning-filter', 'GenerateReportController@earning')->name('report.earning-filter');
    Route::post('/report/earning-export', 'GenerateReportController@earningExport')->name('report.earning-export');
    Route::post('/report/earning-export-pdf', 'GenerateReportController@earningExportPdf')->name('report.earning-export-pdf');


    //Withdraw History report
    Route::get('/report/withdraw-history', 'GenerateReportController@withdrawHistory')->name('withdraw-history-report');
    Route::post('/report/withdraw-history-data', 'GenerateReportController@withdrawHistoryAnyData')->name('report.withdraw-history');
    Route::post('/report/withdraw-history-filter', 'GenerateReportController@withdrawHistory')->name('report.withdraw-history-filter');
    Route::post('/report/withdraw-history-export', 'GenerateReportController@withdrawHistoryExport')->name('report.withdraw-history-export');
    Route::post('/report/withdraw-history-export-pdf', 'GenerateReportController@withdrawHistoryExportPdf')->name('report.withdraw-history-export-pdf');





    Route::get('/faq', 'FaqController@index')->name('faq');
    Route::get('/faq/create', 'FaqController@create')->name('faq.create');
    Route::post('/faq/store', 'FaqController@store')->name('faq.store');
    Route::get('/faq/delete/{id}', 'FaqController@destroy')->name('faq.delete');
    Route::get('/faq/show/{id}', 'FaqController@show')->name('faq.show');
    Route::get('faq/edit/{id}', 'FaqController@edit')->name('faq.edit');
    Route::post('faq/update/{id}', 'FaqController@update')->name('faq.update');
    Route::post('faq/anyData', 'FaqController@anyData')->name('faq.anyData');
    Route::post('/faq/faqUpdateAll', 'FaqController@faqUpdateAll')->name('faqUpdateAll');

    Route::get('/news_latter', 'NewsLatterController@index')->name('news');
    Route::post('news_latter/anyData', 'NewsLatterController@anyData')->name('news.anyData');
    Route::get('news_latter/show/{id}', 'NewsLatterController@show')->name('news.show');
    Route::post('/news_latter/emailUpdateAll', 'FaqController@emailUpdateAll')->name('emailUpdateAll');


    Route::get('/inquiry', 'InquiryController@index')->name('inquiry');
    Route::get('/inquiry/create', 'InquiryController@create')->name('inquiry.create');
    Route::post('/inquiry/store', 'InquiryController@store')->name('inquiry.store');
    Route::get('/inquiry/delete/{id}', 'InquiryController@destroy')->name('inquiry.delete');
    Route::get('/inquiry/show/{id}', 'InquiryController@show')->name('inquiry.show');
    Route::get('inquiry/edit/{id}', 'InquiryController@edit')->name('inquiry.edit');
    Route::post('inquiry/update/{id}', 'InquiryController@update')->name('inquiry.update');
    Route::post('inquiry/anyData', 'InquiryController@anyData')->name('inquiry.anyData');
    Route::post('/inquiry/inquiryUpdateAll', 'InquiryController@inquiryUpdateAll')->name('inquiryUpdateAll');

    Route::get('/subscribe', 'InquiryController@subscribe')->name('subscribe');
    Route::post('subscribe/anyData', 'InquiryController@subscribeAnyData')->name('subscribe.anyData');
    Route::get('/subscribe/delete/{id}', 'InquiryController@subscribeDestroy')->name('subscribe.delete');
    Route::post('/subscribe/subscribeUpdateAll', 'InquiryController@subscribeUpdateAll')->name('subscribeUpdateAll');

    ///////////////////////////************** roles ********************////////////////////////////////

    Route::get('/roles', [RolesController::class, 'index'])->name('roles'); 
    Route::post('/roles/anyData',[RolesController::class,'anyData'])->name('roles.anyData');  
    Route::get('/roles/create',[RolesController::class,'create'])->name('roles.create'); 
    Route::post('/roles/store-permission',[RolesController::class,'StorePermission'])->name('role.store.permission');  
    Route::get('/roles/edit/{id}',[RolesController::class,'edit'])->name('roles.edit');
    Route::post('/roles/update/{id}',[RolesController::class,'update'])->name('roles.update');
    Route::get('/roles/show/{id}',[RolesController::class,'show'])->name('roles.show');
    Route::post('/roles/delete', [RolesController::class,'destroy'])->name('roles.delete'); 
    Route::post('/roles/edit-permission-filter',[RolesController::class,'PermissionFilter'])->name('roles.edit.permission.filter'); 
    Route::post('/roles/update-permission',[RolesController::class,'UpdatePermission'])->name('role.update.permission');   
    Route::get('/export/roles', [RolesController::class,'export'])->name('export.roles'); 
});

// Clear Cache
Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return redirect()->back()->with('doneMessage', __('backend.cashClearDone'));
})->name('cacheClear');
