<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuoteAdminController extends Controller
{
    public function index(Request $request)
    {
        // Si la table n'existe pas encore, renvoyer une page vide (évite le 500)
        if (!Schema::hasTable('quotes')) {
            return Inertia::render('Admin/Quotes/Index', [
                'items' => [
                    'data' => [],
                    'links' => [],
                    'meta' => ['current_page'=>1,'last_page'=>1,'total'=>0],
                ],
                'stats' => ['total'=>0,'new'=>0],
            ]);
        }

        $term   = trim((string) $request->string('q'));
        $driver = DB::getDriverName();
        $op     = $driver === 'pgsql' ? 'ilike' : 'like';

        $items = Quote::query()
            ->when($term !== '', function ($q) use ($term, $op) {
                $q->where(function($s) use ($term, $op) {
                    $s->where('name',  $op, "%$term%")
                      ->orWhere('email',$op, "%$term%")
                      ->orWhere('phone',$op, "%$term%")
                      ->orWhere('city', $op, "%$term%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(function(Quote $x){
                return [
                    'id'         => $x->id,
                    'name'       => $x->name,
                    'email'      => $x->email,
                    'phone'      => $x->phone,
                    'city'       => $x->city,
                    'status'     => $x->status,
                    'files'      => is_array($x->files) ? count($x->files) : 0,
                    'created_at' => $x->created_at,
                    'read_at'    => $x->read_at,
                ];
            });

        $stats = [
            'total' => Quote::count(),
            'new'   => Quote::where('status','new')->count(),
        ];

        return Inertia::render('Admin/Quotes/Index', compact('items','stats'));
    }

    public function show(Quote $quote)
    {
        if ($quote->status === 'new') {
            $quote->update(['status' => 'read', 'read_at' => now()]);
        }

        return Inertia::render('Admin/Quotes/Show', [
            'item' => [
                'id'           => $quote->id,
                'name'         => $quote->name,
                'company'      => $quote->company,
                'email'        => $quote->email,
                'phone'        => $quote->phone,
                'city'         => $quote->city,
                'project_type' => $quote->project_type,
                'budget'       => $quote->budget,
                'desired_date' => $quote->desired_date,
                'message'      => $quote->message,
                'files'        => $quote->files ?? [],
                'status'       => $quote->status,
                'created_at'   => $quote->created_at,
                'read_at'      => $quote->read_at,
            ]
        ]);
    }

    public function download(Quote $quote, int $index)
    {
        $files = $quote->files ?? [];
        abort_unless(isset($files[$index]['path']), 404);

        $relPath = $files[$index]['path'];               // ex: quotes/2025/08/27/file.pdf
        $name    = $files[$index]['name'] ?? basename($relPath);

        $absPath = storage_path('app/public/' . ltrim($relPath, '/'));
        abort_unless(is_file($absPath), 404);

        return response()->download($absPath, $name);
    }

    public function destroy(Quote $quote)
    {
        foreach (($quote->files ?? []) as $f) {
            if (!empty($f['path'])) {
                Storage::disk('public')->delete($f['path']);
            }
        }
        $quote->delete();

        return redirect()
            ->route('admin.quotes.index')
            ->with('flash', ['quote_deleted' => true]);
    }
}
