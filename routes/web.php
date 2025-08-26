<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\QuoteController;
use App\Http\Controllers\Public\TenderController;

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PostController;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::middleware(['role:client|admin|chef_projet', 'permission:access client portal|access admin'])
        ->group(function () {
            Route::get('/client', fn () => Inertia::render('Client/Portal'))->name('client.portal');
        });

    Route::prefix('admin')
        ->middleware(['role:admin|chef_projet', 'permission:access admin'])
        ->group(function () {
            Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
        });

    Route::get('/dashboard', function () {
        $u = Auth::user();
        if ($u && ($u->hasRole('admin') || $u->hasRole('chef_projet'))) {
            return redirect()->route('admin.dashboard');
        }
        if ($u && $u->hasRole('client')) {
            return redirect()->route('client.portal');
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

/* ---------- Public: endpoints de formulaires ---------- */
Route::post('/contact', [ContactController::class, 'store'])->name('forms.contact.store');
Route::post('/devis',   [QuoteController::class,   'store'])->name('forms.quote.store');
Route::post('/rfp',     [TenderController::class,  'store'])->name('forms.tender.store');

/* ---------- Admin (rôles spatie) ---------- */
Route::middleware(['auth','verified','role:admin|editor'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');
        Route::resource('projects', ProjectController::class)->except(['show']);
        Route::resource('posts',    PostController::class)->except(['show']);
    });
