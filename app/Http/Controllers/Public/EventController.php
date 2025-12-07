<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(20);

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'filters' => request()->only(['search', 'status', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Events/Create', [
            'categories' => ['social', 'formation', 'professionnel', 'culturel', 'sportif'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'organizer' => 'required|string',
            'event_date' => 'required|date',
            'date' => 'required|date',
            'status' => 'required|in:brouillon,publié,archivé',
            'cover_image' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement créé avec succès.');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
            'categories' => ['social', 'formation', 'professionnel', 'culturel', 'sportif'],
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'organizer' => 'required|string',
            'event_date' => 'required|date',
            'date' => 'required|date',
            'status' => 'required|in:brouillon,publié,archivé',
            'cover_image' => 'nullable|string',
        ]);

        if ($event->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement mis à jour avec succès.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé avec succès.');
    }
}
