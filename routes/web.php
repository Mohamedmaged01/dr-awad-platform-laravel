<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site — mirrors the Next.js app router routes 1:1.
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PublicController::class, 'serviceDetail'])->name('services.show');
Route::get('/videos', [PublicController::class, 'videos'])->name('videos');
Route::get('/blog', [PublicController::class, 'blog'])->name('blog');
Route::get('/blog/{id}', [PublicController::class, 'blogPost'])->name('blog.show');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/booking', [PublicController::class, 'booking'])->name('booking');
Route::get('/patient-portal', [PublicController::class, 'patientPortal'])->name('patient-portal');

// NOTE: the booking / contact / newsletter forms are simulated client-side with
// Alpine (2s spinner, then an inline success card) exactly as the prototype did —
// no submissions are persisted, so there are no POST endpoints.

// Footer legal links.
Route::view('/privacy', 'static-page', ['pageTitle' => 'سياسة الخصوصية'])->name('privacy');
Route::view('/terms', 'static-page', ['pageTitle' => 'الشروط والأحكام'])->name('terms');

/*
|--------------------------------------------------------------------------
| Admin dashboard — no auth middleware (demo parity with the prototype).
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
    Route::get('/ivf', [AdminController::class, 'ivf'])->name('ivf');
    Route::get('/settings/permissions', [AdminController::class, 'permissions'])->name('permissions');

    // Quick-action links from the dashboard (registered before the generic stubs).
    foreach (['patients/new', 'appointments/new', 'ivf/new', 'reports/new'] as $stub) {
        Route::get("/{$stub}", [AdminController::class, 'comingSoon']);
    }

    // Sidebar entries the prototype listed but never built pages for.
    foreach (['surgeries', 'content', 'messages', 'reviews', 'payments', 'reports', 'branches', 'staff', 'settings'] as $stub) {
        Route::get("/{$stub}", [AdminController::class, 'comingSoon'])->name($stub);
    }
});
