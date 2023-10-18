<?php

use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Dashboard\DashboardController;
use App\Http\Controllers\Api\V1\GetServiceController;
use App\Http\Controllers\Api\V1\GetStateController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use App\Http\Controllers\Api\V1\User\Contractor\RegisterController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\Review\Contractor\ReviewController;
use App\Http\Controllers\Api\V1\User\Contractor\WalletController;
use App\Http\Controllers\Api\V1\Notification;
use App\Http\Controllers\Api\V1\Ticket\TicketController;
use App\Http\Controllers\Api\V1\VersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->group(function() {
        Route::post(uri: 'login', action: LoginController::class);
        Route::post(uri: 'forgot-password', action: ForgotPasswordController::class);

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::post(uri: 'logout', action: LogoutController::class);
            });
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::controller(ProfileController::class)
            ->prefix('profile')
            ->group(function () {
                Route::get(uri: '', action: 'show');
                Route::post(uri: 'update', action: 'update');
                Route::post(uri: 'update-password', action: 'updatePassword');
                Route::post(uri: 'profile-picture', action: 'uploadProfilePicture');
                Route::post(uri: 'deactivate', action: 'deactivate');
            });
        
        Route::prefix('notification')
            ->group(function () {
                Route::controller(Notification\NotificationController::class)
                    ->group(function () {
                        Route::get(uri: '/', action: 'index');
                    });

                Route::controller(Notification\NotificationReadController::class)
                    ->group(function () {
                        Route::post(uri: '/{notification}/mark-as-read', action: 'store')
                            ->missing(fn () => throw new \App\Exceptions\Notification\NotFoundException());
                    });
            });
    });

Route::prefix('contractor')
    ->group(function () {
        Route::post(uri: 'register', action: RegisterController::class);

        Route::get(uri: 'dashboard', action: [DashboardController::class, 'index'])
            ->middleware('auth:sanctum');

        Route::controller(TaskController::class)
            ->prefix('tasks')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get(uri: '', action: 'index');
                Route::get(uri: '/{task:task_number}/details', action: 'show')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());

                Route::post(uri: '{task:task_number}/accept', action: 'accept')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
                Route::post(uri: '{task:task_number}/reject', action: 'reject')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
                Route::post(uri: '{task:task_number}/start', action: 'start')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
                Route::post(uri: '{task:task_number}/complete', action: 'complete')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
                Route::post(uri: '{task:task_number}/report-issue', action: 'reportIssue')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
            });

        Route::controller(TicketController::class)
            ->prefix('tickets')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get(uri: 'logs', action: 'getLogActivities');
                Route::get(uri: 'logs/{ticket:id}', action: 'getLogDetail')
                    ->missing(fn () => throw new \App\Exceptions\Ticket\TicketIdNotFoundException());
                Route::post(uri: 'logs/read', action: 'readLog');
                Route::post(uri: 'task/{task:task_number}/read', action: 'readLogs')
                    ->missing(fn () => throw new \App\Exceptions\Task\TaskNumberNotFoundException());
            });

        Route::controller(ReviewController::class)
            ->prefix('reviews')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get(uri: '', action: 'index');
            });
        
        Route::controller(WalletController::class)
            ->prefix('wallet')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get(uri: '', action: 'index');
            });
        
        Route::controller(Notification\NotificationController::class)
            ->prefix('notifications')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get(uri: 'unread', action: 'getUnreadNotifications');
            });
    });

Route::get(
    uri: 'services',
    action: GetServiceController::class
);

Route::get(
    uri: 'states',
    action: GetStateController::class
);

Route::prefix('version')
    ->middleware('throttle:public')
    ->group(function () {
        Route::get(uri: 'check', action: [VersionController::class, 'checkMobileVersion']);
    });