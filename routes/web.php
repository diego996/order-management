<?php

use Illuminate\Support\Facades\Route;

use App\Models\Order;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('orders.index');
})->name('orders.index');

Route::get('/orders/{order}', function (Order $order) {
    return view('orders.show', ['order' => $order]);
})->name('orders.show');