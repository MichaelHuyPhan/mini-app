<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;


Route::post('/login', [AdminController::class, 'login'])->name('login');

Route::middleware('auth:admin')->group(function() {
    Route::put('loans/{id}/actions/approve', [AdminController::class, 'approveLoan'])->name('loans.approve');
});

Route::fallback(function (){
    abort(404, 'API resource not found');
});
