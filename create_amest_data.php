<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🎓 CRÉATION DES DONNÉES AMEST...\n\n";

// Vérifier si des données existent déjà
$hasEvents = DB::table('projects')->count() > 0;
$hasNews = DB::table('posts')->count() > 0;
$hasMemberships = DB::table('quotes')->count() > 0;

if ($hasEvents || $hasNews || $hasMemberships) {
    echo "⚠️  Des données existent déjà. Les supprimer ? (o/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim($line) == 'o') {
        if ($hasEvents) {
            DB::table('projects')->delete();
            echo "✓ Anciens événements supprimés\n";
        }
        if ($hasNews) {
            DB::table('posts')->delete();
            echo "✓ Anciennes actualités supprimées\n";
        }
        if ($hasMemberships) {
            DB::table('quotes')->delete();
            echo "✓ Anciennes adhésions supprimées\n";
        }
    } else {
        echo "❌ Annulé\n";
        exit;
    }
}

// ========== ÉVÉNEMENTS AMEST ==========
echo "\n📅 CRÉATION DES ÉVÉNEMENTS AMEST:\n";

$events = [
    [
        'title' => 'Soirée Culturelle Malienne',
        'slug' => 'soiree-culturelle-malienne',
        'excerpt' => 'Découverte de la culture malienne à travers musique et danse',
        'body' => '<p>Soirée organisée pour célébrer la richesse culturelle du Mali.</p>',
        'category' => 'Culturel',
        'status' => 'publié',
        'city' => 'Tunis',
        'client' => 'AMEST-Sahel',
        'cover_image' => '/storage/events/culture.jpg',
        'year' => 2025,
        'created_at' => now()->subDays(5),
        'updated_at' => now(),
    ],
    [
        'title' => 'Soutien Scolaire Mathématiques',
        'slug' => 'soutien-scolaire-maths',
        'excerpt' => 'Session de soutien pour étudiants en difficulté',
        'body' => '<p>Séance de tutorat en mathématiques pour les membres.</p>',
        'category' => 'Scolaire',
        'status' => 'publié',
        'city' => 'Tunis',
        'client' => 'AMEST-Sahel',
        'cover_image' => '/storage/events/education.jpg',
        'year' => 2025,
        'created_at' => now()->subDays(20),
        'updated_at' => now(),
    ],
    [
        'title' => 'Collecte de fonds pour étudiants',
        'slug' => 'collecte-fonds-etudiants',
        'excerpt' => 'Solidarité pour aider les étudiants en difficulté financière',
        'body' => '<p>Campagne de solidarité pour soutenir les étudiants dans le besoin.</p>',
        'category' => 'Social',
        'status' => 'publié',
        'city' => 'Tunis',
        'client' => 'AMEST-Sahel',
        'cover_image' => '/storage/events/solidarity.jpg',
        'year' => 2025,
        'created_at' => now()->subDays(35),
        'updated_at' => now(),
    ],
    [
        'title' => 'Tournoi de Football AMEST',
        'slug' => 'tournoi-football-amest',
        'excerpt' => 'Tournoi sportif entre étudiants maliens',
        'body' => '<p>Compétition sportive pour renforcer la cohésion entre membres.</p>',
        'category' => 'Sportif',
        'status' => 'publié',
        'city' => 'Tunis',
        'client' => 'AMEST-Sahel',
        'cover_image' => '/storage/events/sport.jpg',
        'year' => 2025,
        'created_at' => now()->subDays(50),
        'updated_at' => now(),
    ],
    [
        'title' => 'Conférence sur le Mali Moderne',
        'slug' => 'conference-mali-moderne',
        'excerpt' => 'Débats sur le développement du Mali',
        'body' => '<p>Conférence sur les enjeux du développement au Mali.</p>',
        'category' => 'Culturel',
        'status' => 'publié',
        'city' => 'Tunis',
        'client' => 'AMEST-Sahel',
        'cover_image' => '/storage/events/conference.jpg',
        'year' => 2025,
        'created_at' => now()->subDays(65),
        'updated_at' => now(),
    ]
];

foreach ($events as $event) {
    DB::table('projects')->insert($event);
    echo "  ✓ {$event['title']}\n";
}

// ========== ACTUALITÉS AMEST ==========
echo "\n📰 CRÉATION DES ACTUALITÉS AMEST:\n";

$news = [
    [
        'title' => 'Nouveau Bureau Élu 2025',
        'slug' => 'nouveau-bureau-elu-2025',
        'excerpt' => 'Élection du nouveau bureau exécutif de l\'AMEST-Sahel',
        'body' => '<p>Le nouveau bureau a été élu pour l\'année 2025-2026.</p>',
        'type' => 'article',
        'status' => 'publié',
        'cover_image' => '/storage/news/article1.jpg',
        'published_at' => now()->subDays(10),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Partenariat avec l\'Ambassade du Mali',
        'slug' => 'partenariat-ambassade-mali',
        'excerpt' => 'Signature d\'un partenariat pour soutenir les étudiants',
        'body' => '<p>Signature d\'un accord de partenariat avec l\'ambassade.</p>',
        'type' => 'article',
        'status' => 'publié',
        'cover_image' => '/storage/news/article2.jpg',
        'published_at' => now()->subDays(30),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Résultats Excellents des Membres',
        'slug' => 'resultats-excellents-membres',
        'excerpt' => 'Félicitations aux membres ayant obtenu leur diplôme',
        'body' => '<p>Célébration des succès académiques des membres.</p>',
        'type' => 'article',
        'status' => 'publié',
        'cover_image' => '/storage/news/article3.jpg',
        'published_at' => now()->subDays(50),
        'created_at' => now(),
        'updated_at' => now(),
    ]
];

foreach ($news as $article) {
    DB::table('posts')->insert($article);
    echo "  ✓ {$article['title']}\n";
}

// ========== ADHÉSIONS AMEST ==========
echo "\n👥 CRÉATION DES DEMANDES D\'ADHÉSION:\n";

$memberships = [
    [
        'name' => 'Abdoulaye Traoré',
        'email' => 'abdoulaye.traore@etudiant.tn',
        'phone' => '+216 98 765 432',
        'city' => 'Tunis',
        'message' => 'Je souhaite rejoindre l\'AMEST pour participer aux activités culturelles',
        'status' => 'new',
        'created_at' => now()->subDays(2),
        'updated_at' => now(),
    ],
    [
        'name' => 'Fatoumata Diallo',
        'email' => 'fatou.diallo@university.tn',
        'phone' => '+216 23 456 789',
        'city' => 'Sousse',
        'message' => 'Demande d\'adhésion pour bénéficier du soutien scolaire',
        'status' => 'read',
        'created_at' => now()->subDays(7),
        'updated_at' => now(),
    ],
    [
        'name' => 'Moussa Keita',
        'email' => 'm.keita@polytechnique.tn',
        'phone' => '+216 54 321 987',
        'city' => 'Tunis',
        'message' => 'Intéressé par les activités sportives de l\'association',
        'status' => 'new',
        'created_at' => now()->subDays(12),
        'updated_at' => now(),
    ]
];

foreach ($memberships as $membership) {
    DB::table('quotes')->insert($membership);
    echo "  ✓ {$membership['name']} ({$membership['status']})\n";
}

// ========== RÉSUMÉ ==========
echo "\n✅ DONNÉES AMEST CRÉÉES AVEC SUCCÈS !\n";
echo "=======================================\n";
echo "RÉSUMÉ :\n";
echo "  • 5 événements AMEST\n";
echo "  • 3 actualités AMEST\n";
echo "  • 3 demandes d'adhésion (2 nouvelles)\n";
echo "=======================================\n";

// Afficher les statistiques
$eventsCount = DB::table('projects')->count();
$newsCount = DB::table('posts')->count();
$membersCount = DB::table('quotes')->count();
$newMembersCount = DB::table('quotes')->where('status', 'new')->count();

echo "\n📊 STATISTIQUES FINALES :\n";
echo "  Événements: {$eventsCount}\n";
echo "  Actualités: {$newsCount}\n";
echo "  Adhésions: {$membersCount} (dont {$newMembersCount} nouvelles)\n";

echo "\n🎉 PRÊT POUR LE DASHBOARD AMEST ! 🇲🇱\n";
