<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    private function rules(): array
    {
        $maxYear = date('Y') + 1;

        return [
            'title'           => 'required|string|max:180',
            'slug'            => 'nullable|string|max:200',
            'category'        => 'nullable|string|max:80',
            'city'            => 'nullable|string|max:120',
            'client'          => 'nullable|string|max:160',
            // année plus souple et explicite
            'year'            => "nullable|integer|digits:4|min:1900|max:$maxYear",
            'status'          => 'required|in:brouillon,publié',
            'excerpt'         => 'nullable|string|max:500',
            'body'            => 'nullable|string',
            'cover'           => 'nullable|image|max:4096',
            'media_uploads'   => 'nullable|array',
            'media_uploads.*' => 'file|max:8192|mimetypes:image/*,video/*',
            'video_urls'      => 'nullable|array',
            'video_urls.*'    => 'url|max:1024',
        ];
    }

    public function index()
    {
        return Inertia::render('Admin/Projects/Index', [
            'items' => Project::latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Projects/Form');
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());

        // slug auto si vide
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        // normaliser year
        $data['year'] = $data['year'] ?: null;

        $media = [];

        // cover
        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/projects/covers', 'public');
        }

        // uploads images/vidéos
        if ($r->hasFile('media_uploads')) {
            foreach ($r->file('media_uploads') as $f) {
                $stored = $f->store('uploads/projects/media', 'public');
                $mime   = $f->getMimeType();
                $media[] = [
                    'type' => str_starts_with($mime, 'video') ? 'video' : 'image',
                    'kind' => 'upload',
                    'path' => $stored,
                    'mime' => $mime,
                    'size' => $f->getSize(),
                ];
            }
        }

        // liens vidéo
        foreach (($data['video_urls'] ?? []) as $u) {
            $media[] = ['type' => 'video', 'kind' => 'url', 'url' => $u];
        }

        $data['media'] = $media;
        unset($data['media_uploads'], $data['video_urls'], $data['cover']);

        $project = Project::create($data);

        return redirect()->route('admin.projects.edit', $project)->with('ok', 'Projet créé');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Admin/Projects/Form', ['item' => $project]);
    }

    public function update(Request $r, Project $project)
    {
        $data = $r->validate($this->rules());

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['year'] = $data['year'] ?: null;

        // cover
        if ($r->hasFile('cover')) {
            $data['cover_image'] = $r->file('cover')->store('uploads/projects/covers', 'public');
        }

        $media = $project->media ?? [];

        // nouveaux uploads
        if ($r->hasFile('media_uploads')) {
            foreach ($r->file('media_uploads') as $f) {
                $stored = $f->store('uploads/projects/media', 'public');
                $mime   = $f->getMimeType();
                $media[] = [
                    'type' => str_starts_with($mime, 'video') ? 'video' : 'image',
                    'kind' => 'upload',
                    'path' => $stored,
                    'mime' => $mime,
                    'size' => $f->getSize(),
                ];
            }
        }

        // nouveaux liens vidéo
        foreach (($data['video_urls'] ?? []) as $u) {
            $media[] = ['type' => 'video', 'kind' => 'url', 'url' => $u];
        }

        $data['media'] = $media;
        unset($data['media_uploads'], $data['video_urls'], $data['cover']);

        $project->update($data);

        return back()->with('ok', 'Projet mis à jour');
    }

    public function destroy(Project $project)
    {
        foreach (($project->media ?? []) as $m) {
            if (($m['kind'] ?? null) === 'upload' && !empty($m['path'])) {
                Storage::disk('public')->delete($m['path']);
            }
        }
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }

        $project->delete();
        return redirect()->route('admin.projects.index')->with('ok', 'Projet supprimé');
    }
}
