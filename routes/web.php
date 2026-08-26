<?php

use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicFormController;
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
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/patient-portal/login', [PatientAuthController::class, 'login'])->name('patient.login');
    Route::post('/patient-portal/register', [PatientAuthController::class, 'register'])->name('patient.register');
});
Route::get('/patient-portal/logout', [PatientAuthController::class, 'logout'])->name('patient.logout');

// Public form submissions (persisted; light rate-limiting to deter abuse).
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/booking', [PublicFormController::class, 'submitBooking'])->name('booking.submit');
    Route::post('/contact', [PublicFormController::class, 'submitContact'])->name('contact.submit');
    Route::post('/newsletter', [PublicFormController::class, 'subscribe'])->name('newsletter.subscribe');
});

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
| Admin dashboard — real authentication + role-based route guards.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth (open to guests).
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

    // Everything below requires an authenticated staff user.
    Route::middleware('staff')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::middleware('staff:admin,doctor,nurse,receptionist')->group(function () {
            Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
            Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        });

        Route::get('/ivf', [AdminController::class, 'ivf'])->middleware('staff:admin,doctor,lab_technician')->name('ivf');
        Route::get('/surgeries', [AdminController::class, 'surgeries'])->middleware('staff:admin,doctor')->name('surgeries');
        Route::get('/reports', [AdminController::class, 'reports'])->middleware('staff:admin,doctor')->name('reports');
        // Site content hub — edit the public pages (blog, videos, services, about, contact).
        Route::middleware('staff:admin,doctor')->group(function () {
            Route::get('/content', [SiteContentController::class, 'hub'])->name('content');
            Route::get('/content/blog', [SiteContentController::class, 'blog'])->name('content.blog');
            Route::get('/content/videos', [SiteContentController::class, 'videos'])->name('content.videos');
            Route::get('/content/services', [SiteContentController::class, 'services'])->name('content.services');
            Route::get('/content/about', [SiteContentController::class, 'about'])->name('content.about');
            Route::get('/content/contact', [SiteContentController::class, 'contact'])->name('content.contact');
        });
        Route::get('/messages', [AdminController::class, 'messages'])->middleware('staff:admin,receptionist')->name('messages');
        Route::get('/payments', [AdminController::class, 'payments'])->middleware('staff:admin,receptionist')->name('payments');
        Route::get('/reviews', [AdminController::class, 'reviews'])->middleware('staff:admin')->name('reviews');
        Route::get('/branches', [AdminController::class, 'branches'])->middleware('staff:admin')->name('branches');
        Route::get('/staff', [AdminController::class, 'staff'])->middleware('staff:admin')->name('staff');
        Route::get('/settings', [AdminController::class, 'settings'])->middleware('staff:admin')->name('settings');
        Route::get('/settings/permissions', [AdminController::class, 'permissions'])->middleware('staff:admin')->name('permissions');

        // Admin CRUD write endpoints.
        require __DIR__.'/admin.php';
    });
});
