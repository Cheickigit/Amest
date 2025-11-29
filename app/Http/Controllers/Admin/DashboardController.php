<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', '30d');

        // helper pour les "il y a X"
        $human = fn ($ts) => $ts ? Carbon::parse($ts)->locale(app()->getLocale())->diffForHumans() : '';

        // ----- PROJETS -----
        $hasProjects = Schema::hasTable('projects');

        $projects = [
            'total'    => $hasProjects ? (int) DB::table('projects')->count() : 0,
            'published'=> $hasProjects ? (int) DB::table('projects')->where('status','publié')->count() : 0,
            'drafts'   => $hasProjects ? (int) DB::table('projects')->where('status','brouillon')->count() : 0,
            'byStatus' => $hasProjects ? DB::table('projects')->select('status', DB::raw('count(*) as total'))->groupBy('status')->orderByDesc('total')->get()->toArray() : [],
            'byCat'    => $hasProjects ? DB::table('projects')->select('category', DB::raw('count(*) as total'))->whereNotNull('category')->groupBy('category')->orderByDesc('total')->limit(6)->get()->toArray() : [],
            'byCity'   => $hasProjects ? DB::table('projects')->select('city', DB::raw('count(*) as total'))->whereNotNull('city')->groupBy('city')->orderByDesc('total')->limit(6)->get()->toArray() : [],
            'latest'   => $hasProjects ? DB::table('projects')->orderByDesc('created_at')->limit(8)
                                ->get(['id','title','slug','city','category','status','cover_image','created_at'])
                                ->map(fn($x)=>[
                                    'id'=>$x->id,
                                    'title'=>$x->title,
                                    'when'=>$human($x->created_at),
                                    'url'=>route('admin.projects.edit', $x->id),
                                    'type'=>'projet',
                                ])->toArray() : [],
        ];

        // ----- LEADS (QUOTES) -----
        $hasQuotes = Schema::hasTable('quotes');

        $leads = ['total'=>0,'new'=>0,'read'=>0,'latest'=>[]];
        if ($hasQuotes) {
            $leads['total'] = (int) DB::table('quotes')->count();
            $leads['new']   = (int) DB::table('quotes')->where('status','new')->count();
            $leads['read']  = (int) DB::table('quotes')->where('status','read')->count();
            $leads['latest']= DB::table('quotes')->orderByDesc('created_at')->limit(6)
                ->get(['id','name','email','phone','city','status','created_at'])
                ->map(fn($x)=>[
                    'id'    => $x->id,
                    'title' => "Lead — {$x->name}",
                    'when'  => $human($x->created_at),
                    'url'   => route('admin.quotes.show', $x->id),
                    'type'  => 'lead',
                ])->toArray();
        }

        // ----- POSTS -----
        $hasPosts = Schema::hasTable('posts');

        $cms = [
            'drafts' => $hasPosts ? (int) DB::table('posts')->where('status','brouillon')->count() : 0,
            'latest' => $hasPosts ? DB::table('posts')->orderByDesc('updated_at')->limit(5)
                            ->get(['id','title','slug','status','updated_at'])
                            ->map(fn($x)=>[
                                'id'    => $x->id,
                                'title' => "Article — {$x->title}",
                                'when'  => $human($x->updated_at),
                                'url'   => route('admin.posts.edit', $x->id),
                                'type'  => 'article',
                            ])->toArray() : [],
        ];

        // ----- METRICS (exemple) -----
        $metrics = [
            'projects' => ['label'=>'Projets','value'=>$projects['total'],'delta'=>0,'hint'=>'En portefeuille'],
            'leads'    => ['label'=>'Leads','value'=>$leads['total'],'delta'=>0,'hint'=>'30 derniers jours'],
            'rfps'     => ['label'=>'AO ouverts','value'=>0,'delta'=>0,'hint'=>'Bientôt relié'],
            'posts'    => ['label'=>'Brouillons','value'=>$cms['drafts'],'delta'=>0,'hint'=>'En cours'],
        ];

        // Petite série pour le graphe (démo)
        $days = collect(range(0, 29))->map(fn($i) => now()->subDays(29 - $i));
        $trends = [
            'leads' => [
                'label' => 'Leads',
                'data'  => $days->map(fn($d) => ['x' => $d->toDateString(), 'y' => rand(2, 18)]),
            ],
        ];

        // Segments / SLA / Activité
        $bySegment = [
            ['label' => 'Voiries & VRD', 'value' => 62],
            ['label' => 'Bâtiments',      'value' => 24],
            ['label' => 'Génie civil',    'value' => 14],
        ];
        $slas = ['ontime' => 96, 'quality' => 100, 'satisfaction' => 98];

        $activity = array_merge(
            $projects['latest'],
            $leads['latest'],
            $cms['latest'],
        );

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
