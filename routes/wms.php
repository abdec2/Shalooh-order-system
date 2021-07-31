<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\wms\products\ProductsController;
use App\Http\Controllers\wms\location\BinController;
use App\Http\Controllers\wms\location\LocationController;
use App\Http\Controllers\wms\ajax\AjaxController;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\wms\orders\OrderController;
use App\Http\Controllers\wms\pickController;
use App\Http\Controllers\wms\CronJobController; 

Route::get('/wms/products/list', [ProductsController::class, 'ListProducts'])->middleware(['auth'])->name('wms.list_products');

Route::get('/wms/products/add', function () {
    return view('wms/addProducts');
})->middleware(['auth'])->name('wms.add_products');

Route::get('/wms/orders/pending', [OrderController::class, 'listPendingOrders'])->middleware(['auth'])->name('wms.orders.pending');

Route::get('/wms/orders/processing', [OrderController::class, 'listProcessingOrders'])->middleware(['auth'])->name('wms.orders.processing');

Route::get('/wms/pick', [pickController::class, 'index'])->middleware(['auth'])->name('wms.pick');

Route::get('/wms/orders/shipped', [OrderController::class, 'listShippedOrders'])->middleware(['auth'])->name('wms.orders.shipped');

Route::post('/wms/products/add_products', [ProductsController::class, 'add_products'])->middleware(['auth'])->name('wms.products.add_products');

Route::post('/ab-ajax/wavaOrder', [AjaxController::class, 'waveOrder'])->middleware(['auth']);

Route::post('/ab-ajax/fulfillment', [AjaxController::class, 'fulfillment'])->middleware(['auth']);

Route::post('/ab-ajax/fetchUserAssignedOrders', [AjaxController::class, 'fetchUserAssignedOrders'])->middleware(['auth']);

Route::post('/ab-ajax/AssignTray', [AjaxController::class, 'AssignTray'])->middleware(['auth']);

Route::post('/ab-ajax/pickNpackInit', [AjaxController::class, 'pickNpackInit'])->middleware(['auth']);

Route::post('/ab-ajax/addPickList', [AjaxController::class, 'addPickList'])->middleware(['auth']);

Route::post('/ab-ajax/getShipOrderDetails', [AjaxController::class, 'getShipOrderDetails'])->middleware(['auth']);

Route::post('/ab-ajax/save_customer_detail', [AjaxController::class, 'save_customer_detail'])->middleware(['auth']);

Route::post('/ab-ajax/save_shipping_detail', [AjaxController::class, 'save_shipping_detail'])->middleware(['auth']);

Route::post('/ab-ajax/getCountries', [AjaxController::class, 'getCountries'])->middleware(['auth']);

Route::post('/ab-ajax/getCitiesByCountry', [AjaxController::class, 'getCitiesByCountry'])->middleware(['auth']);

Route::post('/ab-ajax/createLabelAndShipOrder', [AjaxController::class, 'createLabelAndShipOrder'])->middleware(['auth']);

Route::post('/ab-ajax/getProductInfo', [AjaxController::class, 'getProductInfo'])->middleware(['auth']);


Route::get('/wms/websitestock/trigger/{sk}', [CronJobController::class, 'StockUpdateTrigger'])->middleware(['verify.cron']);
Route::get('/wms/websitestock/process/{sk}', [CronJobController::class, 'StockUpdateProcess'])->middleware(['verify.cron']);



// Route::get('/wms/generate/bins', [BinController::class, 'generateBins'])->middleware(['auth'])->name('wms.generate.bins');

// Route::post('/wms/generate/locations', [LocationController::class, 'GenerateLocation'])->middleware(['auth'])->name('wms.generate.locations');