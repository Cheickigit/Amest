<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\AccountSecurityController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\QuoteController;
use App\Http\Controllers\Public\TenderController;

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PostController;

/* -------- Public -------- */
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::post('/contact', [ContactController::class, 'store'])->name('forms.contact.store');
Route::post('/devis',   [QuoteController::class,   'store'])->name('forms.quote.store');
Route::post('/rfp',     [TenderController::class,  'store'])->name('forms.tender.store');

/* -------- Auth + Portails -------- */
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

    // Redirection dashboard générique
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

    // Espace client
    Route::middleware(['role:client|admin|chef_projet', 'permission:access client portal|access admin'])
        ->group(function () {
            Route::get('/client', fn () => Inertia::render('Client/Portal'))->name('client.portal');
        });

    /* -------- ADMIN (un seul groupe, clair) -------- */
    Route::prefix('admin')->as('admin.')
        ->middleware(['role:admin|chef_projet', 'permission:access admin'])
        ->group(function () {

        // Dashboard
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

        // Réalisations (permissions fines par action)
        Route::get('projects',                 [ProjectController::class, 'index'  ])->name('projects.index'  )->middleware('permission:projects.view');
        Route::get('projects/create',          [ProjectController::class, 'create' ])->name('projects.create' )->middleware('permission:projects.create');
        Route::post('projects',                [ProjectController::class, 'store'  ])->name('projects.store'  )->middleware('permission:projects.create');
        Route::get('projects/{project}/edit',  [ProjectController::class, 'edit'   ])->name('projects.edit'   )->middleware('permission:projects.edit');
        Route::put('projects/{project}',       [ProjectController::class, 'update' ])->name('projects.update' )->middleware('permission:projects.edit');
        Route::delete('projects/{project}',    [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('permission:projects.delete');

        // Actualités (selon ton besoin, ici simple)
        Route::resource('posts', PostController::class)->except(['show'])->middleware('permission:cms.manage');

        // Compte & sécurité
        Route::get('account/security',                [AccountSecurityController::class, 'index'        ])->name('account.security');
        Route::post('account/password',               [AccountSecurityController::class, 'updatePassword'])->name('account.password.update');
        Route::delete('account/sessions/{id}',        [AccountSecurityController::class, 'revokeSession' ])->name('account.sessions.revoke');
        Route::post('account/sessions/logout-others', [AccountSecurityController::class, 'logoutOthers'  ])->name('account.sessions.logout_others');
    });
});
