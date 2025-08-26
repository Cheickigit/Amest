<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', '30d');

        // ——— Données démo (remplace par tes requêtes) ———
        $metrics = [
            'projects' => ['label' => 'Projets', 'value' => 120, 'delta' => 6],
            'leads'    => ['label' => 'Leads', 'value' => 34, 'delta' => 12],
            'rfps'     => ['label' => 'AO ouverts', 'value' => 7,  'delta' => -3],
            'posts'    => ['label' => 'Brouillons', 'value' => 12, 'delta' => 0],
        ];

        $days = collect(range(0, 29))->map(fn($i) => now()->subDays(29 - $i));
        $trends = [
            'leads' => [
                'label' => 'Leads',
                'data'  => $days->map(fn($d) => ['x' => $d->toDateString(), 'y' => rand(2, 18)]),
            ],
            'projects' => [
                'label' => 'Projets',
                'data'  => $days->map(fn($d) => ['x' => $d->toDateString(), 'y' => rand(1, 8)]),
            ],
        ];

        $bySegment = [
            ['label' => 'Voiries & VRD', 'value' => 62],
            ['label' => 'Bâtiments',      'value' => 24],
            ['label' => 'Génie civil',    'value' => 14],
        ];

        $slas = ['ontime' => 96, 'quality' => 100, 'satisfaction' => 98];

        $activity = [
            ['id'=>1,'type'=>'📝','title'=>'Devis envoyé — Projet ZI Tanger','when'=>'Il y a 2 h','url'=>route('admin.quotes.index')],
            ['id'=>2,'type'=>'🏗️','title'=>'Nouveau projet — VRD Lot 3','when'=>'Il y a 5 h','url'=>route('admin.projects.index')],
            ['id'=>3,'type'=>'📰','title'=>'Article brouillon mis à jour','when'=>'Hier','url'=>route('admin.posts.index')],
        ];

        return Inertia::render('Admin/Dashboard', [
            'filters'   => ['range' => $range],
            'metrics'   => $metrics,
            'trends'    => $trends,
            'bySegment' => $bySegment,
            'slas'      => $slas,
            'activity'  => $activity,
        ]);
    }
}
