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
Route::get('/privacy', fn () => view('static-page', ['pageTitle' => __('privacyPolicy')]))->name('privacy');
Route::get('/terms', fn () => view('static-page', ['pageTitle' => __('termsConditions')]))->name('terms');

// Language switch — persists the chosen locale to the session, then returns.
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Admin dashboard — demo login only (no real auth guard), matching the prototype.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Demo auth (fake — role is inferred from the email, no password check).
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
    Route::get('/ivf', [AdminController::class, 'ivf'])->name('ivf');
    Route::get('/surgeries', [AdminController::class, 'surgeries'])->name('surgeries');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
    Route::get('/branches', [AdminController::class, 'branches'])->name('branches');
    Route::get('/content', [AdminController::class, 'content'])->name('content');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/settings/permissions', [AdminController::class, 'permissions'])->name('permissions');

    // Quick-action "/new" links from the dashboard still resolve to a stub page.
    foreach (['patients/new', 'appointments/new', 'ivf/new', 'reports/new'] as $stub) {
        Route::get("/{$stub}", [AdminController::class, 'comingSoon']);
    }
});
