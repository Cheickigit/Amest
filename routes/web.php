<?php

use App\Http\Controllers\Admin\AccountSecurityController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;

use App\Http\Controllers\Admin\QuoteAdminController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\QuoteController;
use App\Http\Controllers\Public\TenderController;

use App\Models\Post;
use App\Models\Project;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


/* -------------------- Public -------------------- */
Route::get('/', function () {
    // Projets publiés (carrousel + grille)
    $carouselProjects = Project::query()
        ->where('status', 'publié')
        ->latest()
        ->take(6)
        ->get(['id','title','slug','city','category','status','cover_image','media']);

    $gridProjects = Project::query()
        ->where('status', 'publié')
        ->latest()
        ->take(9)
        ->get(['id','title','slug','city','category','status','cover_image','media']);

    // Articles publiés (les plus récents d’abord)
    $posts = Post::query()
        ->where('status', 'publié')
        ->orderByDesc('published_at')
        ->take(6)
        ->get(['id','title','slug','excerpt','cover_image','published_at']);

    return Inertia::render('Welcome', [
        'heroProjects' => $carouselProjects,
        'projects'     => $gridProjects,
        'posts'        => $posts,
    ]);
})->name('home');

// Formulaires publics
Route::post('/contact', [ContactController::class, 'store'])->name('forms.contact.store');
Route::post('/devis',   [QuoteController::class,   'store'])->name('forms.quote.store');
Route::post('/rfp',     [TenderController::class,  'store'])->name('forms.tender.store');

/* -------- Réalisations (public) -------- */
Route::get('/realisations', function () {
    return Inertia::render('Public/Projects/Index', [
        'items' => Project::where('status','publié')
            ->latest()
            ->paginate(9, ['id','slug','title','category','city','cover_image','media','excerpt']),
    ]);
})->name('public.projects');

Route::get('/realisations/{slugOrId}', function (string $slugOrId) {
    // 1) on tente par slug
    $query = Project::query()->where('slug', $slugOrId);

    // 2) si le paramètre est 100% numérique, on ajoute la recherche par id
    if (ctype_digit($slugOrId)) {
        $query->orWhere('id', (int)$slugOrId);
    }

    $project = $query->firstOrFail();

    $related = Project::where('status','publié')
        ->where('id','<>',$project->id)
        ->latest()->take(6)
        ->get(['id','slug','title','cover_image','category','city']);

    return Inertia::render('Public/Projects/Show', [
        'item'    => $project->only([
            'id','slug','title','category','city','client','year',
            'cover_image','excerpt','body','media'
        ]),
        'related' => $related,
    ]);
})->name('public.projects.show');

/* -------- Actualités (public) -------- */
Route::get('/actualites', function () {
    return Inertia::render('Public/Posts/Index', [
        'items' => Post::where('status','publié')
            ->orderByDesc('published_at')
            ->paginate(9, ['id','slug','title','excerpt','published_at','cover_image','tags']),
    ]);
})->name('public.posts');

Route::get('/actualites/{slugOrId}', function (string $slugOrId) {
    $query = Post::query()->where('slug', $slugOrId);
    if (ctype_digit($slugOrId)) {
        $query->orWhere('id', (int)$slugOrId);
    }
    $post = $query->firstOrFail();

    $more = Post::where('status','publié')
        ->where('id','<>',$post->id)
        ->orderByDesc('published_at')->take(6)
        ->get(['id','slug','title','cover_image','published_at']);

    return Inertia::render('Public/Posts/Show', [
        'item' => $post->only(['id','slug','title','excerpt','body','cover_image','published_at','tags']),
        'more' => $more,
    ]);
})->name('public.posts.show');

/* -------------------- Auth + Portails -------------------- */
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {

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

    Route::middleware(['role:client|admin|chef_projet', 'permission:access client portal|access admin'])
        ->group(function () {
            Route::get('/client', fn () => Inertia::render('Client/Portal'))->name('client.portal');
        });

    /* -------------------- Admin -------------------- */
    Route::prefix('admin')->as('admin.')
        ->middleware(['role:admin|chef_projet', 'permission:access admin'])
        ->group(function () {

        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

        // Réalisations
        Route::get('projects',                 [ProjectController::class, 'index'  ])->name('projects.index'  )->middleware('permission:projects.view');
        Route::get('projects/create',          [ProjectController::class, 'create' ])->name('projects.create' )->middleware('permission:projects.create');
        Route::post('projects',                [ProjectController::class, 'store'  ])->name('projects.store'  )->middleware('permission:projects.create');
        Route::get('projects/{project}/edit',  [ProjectController::class, 'edit'   ])->name('projects.edit'   )->middleware('permission:projects.edit');
        Route::put('projects/{project}',       [ProjectController::class, 'update' ])->name('projects.update' )->middleware('permission:projects.edit');
        Route::delete('projects/{project}',    [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('permission:projects.delete');

        // Actualités
        Route::resource('posts', PostController::class)->except(['show'])->middleware('permission:cms.manage');

        // Compte & sécurité
        Route::get('account/security',                [AccountSecurityController::class, 'index'        ])->name('account.security');
        Route::post('account/password',               [AccountSecurityController::class, 'updatePassword'])->name('account.password.update');
        Route::delete('account/sessions/{id}',        [AccountSecurityController::class, 'revokeSession' ])->name('account.sessions.revoke');
        Route::post('account/sessions/logout-others', [AccountSecurityController::class, 'logoutOthers'  ])->name('account.sessions.logout_others');
         Route::get('quotes', [QuoteAdminController::class, 'index'])->name('quotes.index');
        Route::get('quotes/{quote}', [QuoteAdminController::class, 'show'])->name('quotes.show');
        Route::get('quotes/{quote}/files/{index}', [QuoteAdminController::class, 'download'])->name('quotes.file');
        Route::delete('quotes/{quote}', [QuoteAdminController::class, 'destroy'])->name('quotes.destroy');
    });
});
