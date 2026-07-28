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

// Patients & appointments
Route::middleware('staff:admin,doctor,nurse,receptionist')->group(function () {
    Route::post('/patients', [AdminController::class, 'storePatient'])->name('patients.store');
    Route::put('/patients/{patient}', [AdminController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patient}', [AdminController::class, 'destroyPatient'])->name('patients.destroy');

    Route::post('/appointments', [AdminController::class, 'storeAppointment'])->name('appointments.store');
    Route::put('/appointments/{appointment}', [AdminController::class, 'updateAppointment'])->name('appointments.update');
    Route::patch('/appointments/{appointment}/status', [AdminController::class, 'updateAppointmentStatus'])->name('appointments.status');
    Route::delete('/appointments/{appointment}', [AdminController::class, 'destroyAppointment'])->name('appointments.destroy');
});

// IVF cycles
Route::middleware('staff:admin,doctor,lab_technician')->group(function () {
    Route::post('/ivf', [AdminController::class, 'storeCycle'])->name('ivf.store');
    Route::delete('/ivf/{cycle}', [AdminController::class, 'destroyCycle'])->name('ivf.destroy');
});

// Surgeries
Route::middleware('staff:admin,doctor')->group(function () {
    Route::post('/surgeries', [AdminController::class, 'storeSurgery'])->name('surgeries.store');
    Route::put('/surgeries/{surgery}', [AdminController::class, 'updateSurgery'])->name('surgeries.update');
    Route::delete('/surgeries/{surgery}', [AdminController::class, 'destroySurgery'])->name('surgeries.destroy');
});

// Content
Route::middleware('staff:admin,doctor')->group(function () {
    Route::post('/content', [AdminController::class, 'storeContent'])->name('content.store');
    Route::put('/content/{content}', [AdminController::class, 'updateContent'])->name('content.update');
    Route::delete('/content/{content}', [AdminController::class, 'destroyContent'])->name('content.destroy');
});

// Payments / invoices
Route::middleware('staff:admin,receptionist')->group(function () {
    Route::post('/invoices', [AdminController::class, 'storeInvoice'])->name('invoices.store');
    Route::patch('/invoices/{invoice}/paid', [AdminController::class, 'markInvoicePaid'])->name('invoices.paid');
    Route::delete('/invoices/{invoice}', [AdminController::class, 'destroyInvoice'])->name('invoices.destroy');
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

// Staff
Route::middleware('staff:admin')->group(function () {
    Route::post('/staff', [AdminController::class, 'storeStaff'])->name('staff.store');
    Route::put('/staff/{staff}', [AdminController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{staff}', [AdminController::class, 'destroyStaff'])->name('staff.destroy');
});

// Settings
Route::middleware('staff:admin')->group(function () {
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
