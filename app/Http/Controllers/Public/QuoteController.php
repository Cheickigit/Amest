<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        // 1) Validation
        $data = $request->validate([
            'name'         => 'required|string|max:180',
            'company'      => 'nullable|string|max:180',
            'email'        => 'nullable|email|max:180',
            'phone'        => 'nullable|string|max:60',
            'city'         => 'nullable|string|max:120',
            'project_type' => 'nullable|string|max:120',
            'budget'       => 'nullable|string|max:120',
            'desired_date' => 'nullable|string|max:120',
            'message'      => 'nullable|string|max:5000',
            'files.*'      => 'file|max:20480|mimes:pdf,zip,rar,7z,doc,docx,xls,xlsx,dwg,dxf',
            'hp'           => 'nullable|string|max:0', // honeypot
        ], [
            'name.required' => 'Votre nom est requis.',
        ]);

        // 2) Honeypot → on répond OK sans rien faire
        if ($request->filled('hp')) {
            return back()->with('ok', 'Merci, votre demande a été envoyée.');
        }

        // 3) Uploads
        $storedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Stockage sur le disque "public"
                $dir  = 'quotes/' . date('Y/m/d');
                $path = $file->store($dir, 'public'); // storage/app/public/quotes/...

                // URL publique sans utiliser Storage::url() (évite l’alerte Intelephense)
                $publicUrl = asset('storage/' . $path);

                $storedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,           // chemin interne (pour download/suppression)
                    'url'  => $publicUrl,      // URL publique (pour l’admin si besoin)
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        // 4) Sauvegarde en base si le modèle existe
        try {
            if (class_exists(\App\Models\Quote::class)) {
                $q = new \App\Models\Quote();
                $q->name         = $data['name'];
                $q->company      = $data['company']      ?? null;
                $q->email        = $data['email']        ?? null;
                $q->phone        = $data['phone']        ?? null;
                $q->city         = $data['city']         ?? null;
                $q->project_type = $data['project_type'] ?? null;
                $q->budget       = $data['budget']       ?? null;
                $q->desired_date = $data['desired_date'] ?? null;
                $q->message      = $data['message']      ?? null;
                $q->files        = $storedFiles; // JSON -> dans le modèle: protected $casts=['files'=>'array'];
                $q->status       = 'new';
                $q->source_ip    = $request->ip();
                $q->user_agent   = (string) $request->header('User-Agent');
                $q->save();
            } else {
                Log::info('Quote model not found, skipping DB save.', [
                    'payload' => $data,
                    'files'   => $storedFiles,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Quote save failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return back()->with('ok', 'Merci, nous revenons vers vous rapidement.');
    }
}
