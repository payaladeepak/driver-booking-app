<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public auth routes (register/login) are outside auth:sanctum so clients
| can obtain tokens. Protected routes are inside auth:sanctum.
|
*/

//
// Public Auth routes
//
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

//
// Protected routes (require Sanctum token)
//
Route::middleware('auth:sanctum')->group(function () {
    // Auth protected helpers
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

    // Bookings
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::patch('bookings/{id}', [BookingController::class, 'update']);
    Route::delete('bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Trashed / Restore
    Route::get('bookings/trashed-list', [BookingController::class, 'trashed'])->name('bookings.trashed');
    Route::post('bookings/{id}/restore', [BookingController::class, 'restore'])->name('bookings.restore');

    // Assign driver
    Route::post('bookings/{id}/assign-driver', [BookingController::class, 'assignDriver'])->name('bookings.assignDriver');
});

/*
| Notes:
| - Use Authorization: Bearer <token> header for protected endpoints.
| - If you ever need a public booking creation endpoint (e.g., website widget),
|   add it outside the auth:sanctum group (e.g., Route::post('public/bookings', ...)).
*/
