<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index() {
        $this->authorize('admin');
        return Inertia::render('Admin/Projects/Index', [
            'items' => Project::latest()->paginate(12),
        ]);
    }

    public function create() {
        $this->authorize('admin');
        return Inertia::render('Admin/Projects/Form');
    }

    public function store(Request $r) {
        $this->authorize('admin');

        $data = $r->validate([
            'title'=>'required|string|max:180',
            'slug'=>'nullable|string|max:200',
            'category'=>'nullable|string|max:80',
            'city'=>'nullable|string|max:120',
            'client'=>'nullable|string|max:160',
            'year'=>'nullable|integer|min:1980|max:2100',
            'status'=>'required|in:brouillon,publié',
            'excerpt'=>'nullable|string|max:500',
            'body'=>'nullable|string',
            'cover'=>'nullable|image|max:4096',
            'gallery.*'=>'nullable|image|max:6144',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/projects/covers','public');
        }
        if ($r->hasFile('gallery')) {
            $g = [];
            foreach ($r->file('gallery') as $img) {
                $g[] = $img->store('uploads/projects/gallery','public');
            }
            $data['gallery'] = $g;
        }

        Project::create($data);
        return redirect()->route('admin.projects.index')->with('ok','Projet créé');
    }

    public function edit(Project $project) {
        $this->authorize('admin');
        return Inertia::render('Admin/Projects/Form', ['item'=>$project]);
    }

    public function update(Request $r, Project $project) {
        $this->authorize('admin');

        $data = $r->validate([
            'title'=>'required|string|max:180',
            'slug'=>'nullable|string|max:200',
            'category'=>'nullable|string|max:80',
            'city'=>'nullable|string|max:120',
            'client'=>'nullable|string|max:160',
            'year'=>'nullable|integer|min:1980|max:2100',
            'status'=>'required|in:brouillon,publié',
            'excerpt'=>'nullable|string|max:500',
            'body'=>'nullable|string',
            'cover'=>'nullable|image|max:4096',
            'gallery.*'=>'nullable|image|max:6144',
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/projects/covers','public');
        }
        if ($r->hasFile('gallery')) {
            $g = $project->gallery ?? [];
            foreach ($r->file('gallery') as $img) {
                $g[] = $img->store('uploads/projects/gallery','public');
            }
            $data['gallery'] = $g;
        }

        $project->update($data);
        return back()->with('ok','Projet mis à jour');
    }

    public function destroy(Project $project) {
        $this->authorize('admin');
        $project->delete();
        return back()->with('ok','Projet supprimé');
    }
}
