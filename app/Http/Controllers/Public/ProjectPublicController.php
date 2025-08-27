<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()
            ->where('status', 'publié')
            ->when($request->get('q'), function ($q, $term) {
                $q->where('title', 'ilike', '%'.$term.'%');
            })
            ->latest();

        $items = $query->paginate(9)->withQueryString();

        return Inertia::render('Public/Projects/Index', [
            'items' => $items->through(fn (Project $p) => [
                'id'          => $p->id,
                'slug'        => $p->slug,
                'title'       => $p->title,
                'city'        => $p->city,
                'category'    => $p->category,
                'cover_image' => $p->cover_image,
                'media'       => $p->media,
            ]),
        ]);
    }

    public function show(string $key)
    {
        $project = Project::query()
            ->where('slug', $key)
            ->when(ctype_digit($key), fn ($q) => $q->orWhere('id', (int) $key))
            ->firstOrFail();

        return Inertia::render('Public/Projects/Show', [
            'item' => [
                'id'          => $project->id,
                'slug'        => $project->slug,
                'title'       => $project->title,
                'description' => $project->description,
                'city'        => $project->city,
                'category'    => $project->category,
                'cover_image' => $project->cover_image,
                'media'       => $project->media,
            ],
        ]);
    }
}
