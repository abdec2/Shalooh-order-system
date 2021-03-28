<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Reports;
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

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', [Dashboard::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::get('/orders', function () {
    return view('orders');
})->middleware(['auth'])->name('orders');

Route::get('/reports', function () {
    return view('reports');
})->middleware(['auth'])->name('reports');

Route::get('/orders/{id}', [Order::class, 'getOrder'])->middleware(['auth']);

Route::post('/orders', [Order::class, 'formSubmit'])->middleware(['auth'])->name('orders');
Route::post('/orders/get_cities', [Order::class, 'getAjaxCities'])->middleware(['auth'])->name('getAjaxCities');
Route::post('/reports', [Reports::class, 'index'])->middleware(['auth'])->name('reports');


Route::post('/save_order', [Order::class, 'save_order'])->middleware(['auth'])->name('save_order');

Route::post('/create_label', [Order::class, 'create_label'])->middleware(['auth'])->name('create_label');

require __DIR__.'/auth.php';
