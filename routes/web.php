<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\OrderList;
use App\Livewire\OrderDetails;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');
Route::get('/orders/{order}', OrderDetails::class)->name('orders.show');
