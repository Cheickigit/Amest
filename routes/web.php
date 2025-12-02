<?php

use App\Http\Controllers\Admin\AccountSecurityController;
use App\Http\Controllers\Admin\DashboardController;
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

/* ===================== PUBLIC ===================== */
Route::get('/', function () {
    // Projets (carrousel + grille)
    $carouselProjects = Project::query()
        ->where('status', 'publié')
        ->latest()
        ->take(6)
        ->get(['id','title','slug','city','category','status','cover_image']); // Retirer 'media'

    $gridProjects = Project::query()
        ->where('status', 'publié')
        ->latest()
        ->take(9)
        ->get(['id','title','slug','city','category','status','cover_image']); // Retirer 'media'

    // Articles
    $posts = Post::query()
        ->where('status', 'publié')
        ->where('type', 'article') // Ajouter cette ligne
        ->orderByDesc('published_at')
        ->take(6)
        ->get(['id','title','slug','excerpt','cover_image','published_at']);

    return Inertia::render('Welcome', [
        'heroProjects' => $carouselProjects,
        'projects'     => $gridProjects,
        'posts'        => $posts,
    ]);
})->name('home');

/* -------- Formulaires publics (POST) -------- */
Route::post('/contact', [ContactController::class, 'store'])->name('forms.contact.store');
Route::post('/devis',   [QuoteController::class,   'store'])->name('forms.quote.store');
Route::post('/rfp',     [TenderController::class,  'store'])->name('forms.tender.store');

/* -------- Réalisations (public) -------- */
Route::get('/realisations', function () {
    return Inertia::render('Public/Projects/Index', [
        'items' => Project::where('status','publié')
            ->latest()
            ->paginate(9, ['id','slug','title','category','city','cover_image','excerpt']), // Retirer 'media'
    ]);
})->name('public.projects');

Route::get('/realisations/{slugOrId}', function (string $slugOrId) {
    $query = Project::query()->where('slug', $slugOrId);
    if (ctype_digit($slugOrId)) $query->orWhere('id', (int)$slugOrId);
    $project = $query->firstOrFail();

    $related = Project::where('status','publié')
        ->where('id','<>',$project->id)
        ->latest()->take(6)
        ->get(['id','slug','title','cover_image','category','city']);

    return Inertia::render('Public/Projects/Show', [
        'item'    => $project->only([
            'id','slug','title','category','city','client','year',
            'cover_image','excerpt','body' // Retirer 'media'
        ]),
        'related' => $related,
    ]);
})->name('public.projects.show');

/* -------- Annonces & Archives -------- */
Route::get('/annonces-archives', function () {
    $announcements = Post::query()
        ->where('status', 'publié')
        ->where('type', 'annonce')
        ->orderByDesc('published_at')
        ->paginate(12, ['id','slug','title','excerpt','published_at','cover_image','tags','important']);

    $archives = Post::query()
        ->where('status', 'publié')
        ->where('type', 'annonce')
        ->where('published_at', '<', now()->subMonths(6))
        ->orderByDesc('published_at')
        ->take(20)
        ->get(['id','slug','title','published_at','important']);

    return Inertia::render('Public/Announcements/Index', [
        'announcements' => $announcements,
        'archives' => $archives,
        'stats' => [
            'total' => Post::where('type', 'annonce')->where('status', 'publié')->count(),
            'important_count' => Post::where('type', 'annonce')->where('important', true)->count(),
        ]
    ]);
})->name('public.announcements');

/* -------- Actualités (Articles) -------- */
Route::get('/actualites', function () {
    return Inertia::render('Public/Posts/Index', [
        'items' => Post::where('status','publié')
            ->where('type', 'article') // Ajouter cette ligne
            ->orderByDesc('published_at')
            ->paginate(9, ['id','slug','title','excerpt','published_at','cover_image','tags']),
    ]);
})->name('public.posts');

Route::get('/actualites/{slugOrId}', function (string $slugOrId) {
    $query = Post::query()->where('slug', $slugOrId);
    if (ctype_digit($slugOrId)) $query->orWhere('id', (int)$slugOrId);
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

/* -------- Pages institutionnelles -------- */
Route::get('/a-propos', fn () => Inertia::render('Public/About', [
    'stats' => ['roads_km'=>120, 'buildings'=>40, 'years'=>1],
]))->name('public.about');

Route::get('/equipe', fn () => redirect()->route('public.announcements'))->name('public.team'); // Redirection

Route::get('/services', fn () => Inertia::render('Public/Services', [
    'groups' => [
        ['title' => 'Voirie & Réseaux (VRD)', 'items' => ['Chaussées souples & rigides', 'Assainissement pluvial & EU', 'Ouvrages hydrauliques', 'Signalisation & sécurité'], 'badge' => 'VRD'],
        ['title' => 'Bâtiments', 'items' => ['Structure béton & charpente', 'Cloisons & finitions premium', 'Lots techniques (CVC, CFO/CFA)', 'BIM exé & DOE'], 'badge' => 'BAT'],
        ['title' => 'Génie civil', 'items' => ['Ouvrages d\'art', 'Soutènements & pieux', 'Bassins & réservoirs', 'Études d\'exécution & phasage'], 'badge' => 'GC'],
        ['title' => 'Immobilier', 'items' => ['Promotion & AMO', 'Logements / tertiaire', 'Retail & parcs d\'activités', 'Pilotage & OPC'], 'badge' => 'IMO'],
        ['title' => 'Rénovation & Réhabilitation', 'items' => ['Renforcement structurel', 'Mise aux normes HSE', 'Réhabilitation site occupé', 'Finitions haut de gamme'], 'badge' => 'REN'],
        ['title' => 'Ouvrages hydrauliques', 'items' => ['Canaux & dalots', 'Stations de pompage', 'Buses & microtunnels', 'Étanchéité & protections'], 'badge' => 'HYD'],
    ],
]))->name('public.services');

Route::get('/appels-d-offres', fn () => Inertia::render('public/Rfp'))->name('public.rfp');
Route::get('/contact', fn () => Inertia::render('Public/Contact', [
    'contact' => [
        'phone_display' => '+216 24 282 332',
        'phone_href'    => 'tel:+21624282332',
        'whatsapp_href' => 'https://wa.me/21624282332?text=Bonjour%20AMEST-Sahel%2C%20je%20souhaite%20avoir%20plus%20d%27informations',
        'email'         => 'amestsahel04@gmail.com',
        'city'          => 'Tunisie - Région du Sahel',
        'hours'         => 'Lun–Ven : 9h – 17h',
        'social_links'  => [
            'facebook'  => 'https://facebook.com/amestsahel',
            'instagram' => 'https://instagram.com/amest_sahel',
            'youtube'   => 'https://youtube.com/amest-talk'
        ]
    ],
]))->name('public.contact');

/* Alias CTA "Demander un devis" */
Route::get('/devis', fn () => redirect()->route('public.rfp'))->name('public.quote');

/* ===================== AUTH + PORTAILS ===================== */
Route::middleware(['auth', config('jetstream.auth_session'), 'verified'])->group(function () {

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

    /* ===================== ADMIN ===================== */
    Route::prefix('admin')->as('admin.')
        ->middleware(['role:admin|chef_projet', 'permission:access admin'])
        ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Réalisations
        Route::get('projects',                [ProjectController::class, 'index'])->name('projects.index')->middleware('permission:projects.view');
        Route::get('projects/create',         [ProjectController::class, 'create'])->name('projects.create')->middleware('permission:projects.create');
        Route::post('projects',               [ProjectController::class, 'store'])->name('projects.store')->middleware('permission:projects.create');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('permission:projects.edit');
        Route::put('projects/{project}',      [ProjectController::class, 'update'])->name('projects.update')->middleware('permission:projects.edit');
        Route::delete('projects/{project}',   [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('permission:projects.delete');

        // Actualités
        Route::resource('posts', PostController::class)->except(['show'])->middleware('permission:cms.manage');

        // Compte & sécurité
        Route::get('account/security',                [AccountSecurityController::class, 'index'])->name('account.security');
        Route::post('account/password',               [AccountSecurityController::class, 'updatePassword'])->name('account.password.update');
        Route::post('account/email',                  [AccountSecurityController::class, 'updateEmail'])->name('account.email.update');
        Route::delete('account/sessions/{id}',        [AccountSecurityController::class, 'revokeSession'])->name('account.sessions.revoke');
        Route::post('account/sessions/logout-others', [AccountSecurityController::class, 'logoutOthers'])->name('account.sessions.logout_others');

        // Devis & leads
        Route::get('quotes',                        [QuoteAdminController::class, 'index'])->name('quotes.index');
        Route::get('quotes/{quote}',                [QuoteAdminController::class, 'show'])->name('quotes.show');
        Route::get('quotes/{quote}/files/{index}',  [QuoteAdminController::class, 'download'])->name('quotes.file');
        Route::delete('quotes/{quote}',             [QuoteAdminController::class, 'destroy'])->name('quotes.destroy');
    });
});
