<?php

use App\Http\Controllers\Api\AuthAdminController;
use App\Http\Controllers\Api\AdminBookingController;
use App\Http\Controllers\Api\InvoiceDetailController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\RoomTypeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\AuthUserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoomClientController;
use App\Http\Controllers\Api\UserHomeController;
use App\Http\Controllers\Api\UserBookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admins')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login']);

    Route::middleware('admin')->group(function () {
        Route::get('/profile', [AuthAdminController::class, 'getProfile']);
        Route::post('/logout', [AuthAdminController::class, 'logout']);
        Route::put('/profile', [AuthAdminController::class, 'updateProfile']);
        Route::post('/change-password', [AuthAdminController::class, 'changePassword']);

        Route::apiResource('properties', PropertyController::class);
        Route::apiResource('room-types', RoomTypeController::class);

        Route::apiResource('bookings', AdminBookingController::class);
        Route::post('/bookings/check-room-availability', [AdminBookingController::class, 'checkRoomAvailability']);
        Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm']);
        Route::post('/bookings/{booking}/check-in', [AdminBookingController::class, 'checkInGuest']);
        Route::post('/bookings/{booking}/cancelled', [AdminBookingController::class, 'cancelled']);
        Route::post('/bookings/{booking}/no-show', [AdminBookingController::class, 'noShow']);
        Route::post('/bookings/{booking}/check-out', [AdminBookingController::class, 'checkOutGuest']);
        Route::post('/bookings/{booking}/check-out-room', [AdminBookingController::class, 'checkOutRoom']);

        Route::get('bookings/{booking}/invoice-details', [InvoiceDetailController::class, 'index']);
        Route::post('bookings/{booking}/invoice-details', [InvoiceDetailController::class, 'editSave']);
        Route::delete('bookings/{booking}/invoice-details/{id}', [InvoiceDetailController::class, 'destroy']);

        Route::apiResource('rooms', RoomController::class)->except(['update']);
        Route::post('/rooms/{room}/restore', [RoomController::class, 'restore']);
        Route::post('/rooms/{room}', [RoomController::class, 'update']);

        Route::apiResource('services', ServiceController::class);
        Route::post('services/{id}/restore', [ServiceController::class, 'restore']);

        Route::apiResource('contacts', ContactController::class);
        Route::post('contacts/send-contact', [ContactController::class, 'sendContact']);
        Route::post('contacts/{id}/send-mail', [ContactController::class, 'sendMail']);

        Route::apiResource('users', UserController::class);
        Route::post('users/{id}/restore', [UserController::class, 'restore']);

        Route::get('/revenue', [DashboardController::class, 'revenue']);
        Route::get('/get-today-checkout-rooms', [DashboardController::class, 'getTodayCheckoutRooms']);
        Route::get('/count-contacts', [DashboardController::class, 'countContacts']);
        Route::get('/count-users', [DashboardController::class, 'countUsers']);
        Route::get('/count-bookings', [DashboardController::class, 'countBookings']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthUserController::class, 'logout']);
    Route::get('/profile', [AuthUserController::class, 'getProfile']);
    Route::put('/profile', [AuthUserController::class, 'updateProfile']);
    Route::post('/change-password', [AuthUserController::class, 'changePassword']);
    Route::get('/bookings/{id}', [UserBookingController::class, 'show']);
    Route::post('/bookings', [UserBookingController::class, 'store'])->middleware(['throttle:limitBooking']);
    Route::put('/bookings/{id}', [UserBookingController::class, 'update'])->middleware(['throttle:limitBooking']);
    Route::get('/bookings-user', [UserBookingController::class, 'indexCustomer']);
});

Route::post('/login', [AuthUserController::class, 'login']);
Route::post('/register', [AuthUserController::class, 'register']);
Route::post('contacts/send-contact', [ContactController::class, 'sendContact']);
Route::get('/user-home', [UserHomeController::class, 'index']);
Route::get('/rooms/{id}', [RoomClientController::class, 'show']);
Route::get('/rooms', [RoomClientController::class, 'index']);

Route::post('/bookings/check-room-availability', [UserBookingController::class, 'checkRoomAvailability']);
