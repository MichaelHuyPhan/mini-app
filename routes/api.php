<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [UserController::class, 'login'])->name('user.login');

Route::middleware('auth:api')->group(function () {
    Route::post('/loans', [LoanController::class, 'create'])->name('loans.create');
    Route::put('/loans/repay', [LoanController::class, 'repay'])->name('loans.repay');
    Route::get('/loans', [LoanController::class, 'get'])->name('loan.view');
});

Route::fallback(function (){
    abort(404, 'API resource not found');
});
