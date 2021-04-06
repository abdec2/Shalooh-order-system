<?php 

use Illuminate\Support\Facades\Route;

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