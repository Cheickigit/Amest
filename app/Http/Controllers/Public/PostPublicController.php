<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()
            ->where('status', 'publié')
            ->when($request->get('q'), function ($q, $term) {
                // ILIKE pour PostgreSQL (search insensible à la casse)
                $q->where('title', 'ilike', '%'.$term.'%');
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $items = $query->paginate(9)->withQueryString();

        return Inertia::render('Public/Posts/Index', [
            'items' => $items->through(fn (Post $p) => [
                'id'           => $p->id,
                'slug'         => $p->slug,
                'title'        => $p->title,
                'excerpt'      => $p->excerpt,
                'cover_image'  => $p->cover_image,
                'published_at' => optional($p->published_at)->toDateString(),
            ]),
        ]);
    }

    public function show(string $key)
    {
        // ctype_digit: vrai seulement si la chaîne ne contient que des chiffres
        $post = Post::query()
            ->where('slug', $key)
            ->when(ctype_digit($key), fn ($q) => $q->orWhere('id', (int) $key))
            ->firstOrFail();

        return Inertia::render('Public/Posts/Show', [
            'item' => [
                'id'           => $post->id,
                'slug'         => $post->slug,
                'title'        => $post->title,
                'body'         => $post->body,
                'excerpt'      => $post->excerpt,
                'cover_image'  => $post->cover_image,
                'published_at' => optional($post->published_at)->toDateString(),
            ],
        ]);
    }
}
