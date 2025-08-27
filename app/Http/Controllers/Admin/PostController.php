<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function __construct()
    {
        // Spatie permission ; protège toutes les actions
        $this->middleware('permission:cms.manage');
    }

    public function index()
    {
        return Inertia::render('Admin/Posts/Index', [
            'items' => Post::latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Form');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'        => 'required|string|max:180',
            'slug'         => 'nullable|string|max:200',
            'status'       => 'required|in:brouillon,publié',
            'published_at' => 'nullable|date',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'nullable|string',
            'cover'        => 'nullable|image|max:4096',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:40',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/posts/covers', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('ok', 'Article publié');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Posts/Form', ['item' => $post]);
    }

    public function update(Request $r, Post $post)
    {
        $data = $r->validate([
            'title'        => 'required|string|max:180',
            'slug'         => 'nullable|string|max:200',
            'status'       => 'required|in:brouillon,publié',
            'published_at' => 'nullable|date',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'nullable|string',
            'cover'        => 'nullable|image|max:4096',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:40',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/posts/covers', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('ok', 'Article mis à jour');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('ok', 'Article supprimé');
    }
}
