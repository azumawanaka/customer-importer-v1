<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customerId}', [CustomerController::class, 'show'])->name('customers.show');
