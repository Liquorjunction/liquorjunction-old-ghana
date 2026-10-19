<?php

use App\Http\Controllers\Wholesaler\DashboardController;
use App\Http\Controllers\Wholesaler\UsersController;
use App\Http\Controllers\Wholesaler\ProductController;
use App\Http\Controllers\Wholesaler\StoreController;
use App\Http\Controllers\Dashboard\UsersAdminController;
use App\Http\Controllers\Dashboard\WithdrawRequestController;
use App\Http\Controllers\Dashboard\InstructorRequestController;
use App\Http\Controllers\Dashboard\CategoryAdminController;
use App\Http\Controllers\Dashboard\QuestionAdminController;
use App\Http\Controllers\Dashboard\LevelController;
use App\Http\Controllers\Dashboard\WebmasterSettingsController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\SubCategoriesController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\WholesalerController;
use App\Http\Controllers\Dashboard\SubadminController;
use App\Http\Controllers\Dashboard\RequestProductController;
use App\Http\Controllers\Dashboard\AdvertiseController;
use App\Http\Controllers\Wholesaler\OrderController;
use App\Http\Controllers\Wholesaler\PromotedProductController;
use App\Http\Controllers\Wholesaler\SalesReportController;
use App\Http\Controllers\Wholesaler\PurchaseOrderController;
use Illuminate\Support\Facades\Route;


// No Permission
Route::get('/403', function () {
    return view('errors.403');
})->name('NoPermission');

// Not Found
Route::get('/404', function () {
    return view('errors.404');
})->name('NotFound');

// Dashboard Route
// Route::middleware(['wholesalerAuth'])->group(function () {
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('adminwholesalerHome');
// // Route::post('/filter', [DashboardController::class, 'index'])->name('dashboardfilter');
// // Route::post('/filter-class', [DashboardController::class, 'filterClass'])->name('dashboardfilter.class');
// // Route::post('/filter-category', [DashboardController::class, 'filterCategory'])->name('dashboardfilter.category');
// // Route::post('/filter-instructor', [DashboardController::class, 'filterInstructor'])->name('dashboardfilter.instructor');

// Route::get('/wholesaler-profile/{id}/edit', [UsersController::class, 'edit'])->name('userswholesalerEdit');
// Route::post('/wholesaler/{id}/update', [UsersController::class, 'update'])->name('userswholesalerUpdate');


// Route::get('/change-password', [UsersController::class, 'changePassword'])->name('wholesaler-change-password');
// Route::post('/update-password', [UsersController::class, 'updatePassword'])->name('wholesaler-update-password');

// //Wholesaler Multi Store
// Route::get('/store', [StoreController::class,'index'])->name('wholesalerstore');
// Route::post('/store/anyData', [StoreController::class,'anyData'])->name('wholesalerstore.anyData');
// Route::get('/store/create', 'StoreController@create')->name('store.create');
// Route::post('/store/store', [StoreController::class,'store'])->name('wholesalerstore.store');
// Route::get('store/edit/{id}','StoreController@edit')->name('wholesalerstore.edit');
// Route::post('store/update/{id}','StoreController@update')->name('wholesalerstore.update');
// Route::get('/store/show/{id}','StoreController@show')->name('wholesalerstore.show');
// Route::post('/store/UpdateAll', 'StoreController@wholesalerstoreUpdateAll')->name('wholesalerstoreUpdateAll');
// Route::post('/store/status_active', [StoreController::class,'status_active'])->name('wholesalerstore.status_active');
// Route::post('/store/status_inactive', [StoreController::class,'status_inactive'])->name('wholesalerstore.status_inactive');
// Route::post('get-formatted-address', [StoreController::class, 'get_formatted_address'])->name('getAddress');

// //Wholesaler Product
// Route::get('/product', [ProductController::class,'index'])->name('wholesalerproduct');
// Route::post('/product/anyData', [ProductController::class,'anyData'])->name('wholesalerproduct.anyData');
// Route::post('/product/store', [ProductController::class,'store'])->name('wholesalerproduct.store');
// // Route::post('product/show','ProductController@show')->name('wholesalerproduct.show');
// // Route::get('product/edit','ProductController@edit')->name('wholesalerproduct.edit');
// Route::post('/product/UpdateAll', 'ProductController@wholesalerproductUpdateAll')->name('wholesalerproductUpdateAll');
// Route::post('/product/status_active', [ProductController::class,'status_active'])->name('wholesalerproduct.status_active');
// Route::post('/product/status_inactive', [ProductController::class,'status_inactive'])->name('wholesalerproduct.status_inactive');
// Route::get('/product/create', 'ProductController@create')->name('product.create');
// Route::get('product/edit/{id}','ProductController@edit')->name('wholesalerproduct.edit');
// Route::post('product/update/{id}','ProductController@update')->name('wholesalerproduct.update');
// Route::get('/product/show/{id}','ProductController@show')->name('wholesalerproduct.show');

// Route::post('/bulk-product/product', 'ProductController@storeProduct')->name('wholesalerbulkproduct.store');

// //Wholesaler Purchase order Product
// Route::get('/wholesalerpurchase-order', [PurchaseOrderController::class,'index'])->name('wholesalerpurchase');
// Route::post('/wholesalerpurchase-order/anyData', [PurchaseOrderController::class,'anyData'])->name('wholesalerpurchase.anyData');

// //Wholesaler Order
// Route::get('/order', [OrderController::class,'index'])->name('order');
// Route::post('/order/anyData', [OrderController::class,'anyData'])->name('order.anyData');
// // Route::post('order/show','OrderController@show')->name('order.show');
// Route::get('/order/{id}/show', [OrderController::class, 'show'])->name('order.show');
// Route::post('/order/UpdateAll', 'OrderController@wholesalerorderUpdateAll')->name('wholesalerorderUpdateAll');

// //Wholesaler SalesReport 
// Route::get('/salesreport', [SalesReportController::class,'index'])->name('salesreport');
// Route::post('/salesreport/anyData', [SalesReportController::class,'anyData'])->name('salesreport.anyData');
// Route::post('/export_salesreport', 'SalesReportController@export_salesreport')->name('export_salesreport');

// //Wholesaler Promoted Product
// Route::get('/promoted-product', [PromotedProductController::class,'index'])->name('promoted-product');
// Route::post('/promoted-product/anyData', [PromotedProductController::class,'anyData'])->name('promoted-product.anyData');
// Route::post('/promoted-product/store', [PromotedProductController::class,'store'])->name('promoted-product.store');
// Route::post('/promoted-product/UpdateAll', 'PromotedProductController@wholesalerpromotedproductUpdateAll')->name('wholesalerpromotedproductUpdateAll');
// // Route::get('/promoted-product/delete/{id}', 'CategoriesController@destroy')->name('category.delete');
// });

// Clear Cache
Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return redirect()->back()->with('doneMessage', __('backend.cashClearDone'));
})->name('cacheClear');
