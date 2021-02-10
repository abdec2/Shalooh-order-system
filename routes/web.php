<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/orders', function () {
    return view('orders');
})->middleware(['auth'])->name('orders');

Route::post('/orders', [Order::class, 'formSubmit'])->middleware(['auth'])->name('orders');

Route::post('/save_order', [Order::class, 'save_order'])->middleware(['auth'])->name('save_order');

Route::post('/create_label', [Order::class, 'create_label'])->middleware(['auth'])->name('create_label');

require __DIR__.'/auth.php';
