<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin CRUD write endpoints
|--------------------------------------------------------------------------
| Included from web.php inside the `admin.` name group, `/admin` prefix, and
| the `staff` auth middleware. Role restrictions mirror the read pages.
*/

// Appointments
Route::middleware('staff:admin,doctor,nurse,receptionist')->group(function () {
    Route::patch('/appointments/{appointment}/status', [AdminController::class, 'updateAppointmentStatus'])->name('appointments.status');
    Route::delete('/appointments/{appointment}', [AdminController::class, 'destroyAppointment'])->name('appointments.destroy');
    Route::delete('/patients/{patient}', [AdminController::class, 'destroyPatient'])->name('patients.destroy');
});

// Messages
Route::middleware('staff:admin,receptionist')->group(function () {
    Route::patch('/messages/{message}/reply', [AdminController::class, 'replyMessage'])->name('messages.reply');
    Route::delete('/messages/{message}', [AdminController::class, 'destroyMessage'])->name('messages.destroy');
});

// Reviews
Route::middleware('staff:admin')->group(function () {
    Route::patch('/reviews/{review}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
});

// Branches
Route::middleware('staff:admin')->group(function () {
    Route::post('/branches', [AdminController::class, 'storeBranch'])->name('branches.store');
    Route::put('/branches/{branch}', [AdminController::class, 'updateBranch'])->name('branches.update');
    Route::delete('/branches/{branch}', [AdminController::class, 'destroyBranch'])->name('branches.destroy');
});
