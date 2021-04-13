<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\wms\products\ProductsController;
use App\Http\Controllers\wms\location\BinController;
use App\Http\Controllers\wms\location\LocationController;
use Illuminate\Support\Facades\Log;

Route::get('/wms/products/list', function () {
    return view('wms/wms');
})->middleware(['auth'])->name('wms.list_products');

Route::get('/wms/products/add', function () {
    return view('wms/addProducts');
})->middleware(['auth'])->name('wms.add_products');

Route::get('/wms/orders/pending', function () {
    return view('wms/pendingOrders');
})->middleware(['auth'])->name('wms.orders.pending');

Route::get('/wms/orders/processing', function () {
    return view('wms/processOrders');
})->middleware(['auth'])->name('wms.orders.processing');

Route::get('/wms/orders/shipped', function () {
    return view('wms/shippedOrders');
})->middleware(['auth'])->name('wms.orders.shipped');

Route::post('/wms/products/add_products', [ProductsController::class, 'add_products'])->middleware(['auth'])->name('wms.products.add_products');

// Route::get('/wms/generate/bins', [BinController::class, 'generateBins'])->middleware(['auth'])->name('wms.generate.bins');

// Route::post('/wms/generate/locations', [LocationController::class, 'GenerateLocation'])->middleware(['auth'])->name('wms.generate.locations');