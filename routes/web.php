<?php

use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(config('filament.path') . '/login');
});

Route::controller(ForgotPasswordController::class)
    ->group(function () {
        Route::get(uri: 'reset-password/{token}', action: 'showResetPasswordForm')->name('password.reset');
        Route::post(uri: 'reset-password', action: 'submitResetPasswordForm')->name('password.submit-reset');
    });
