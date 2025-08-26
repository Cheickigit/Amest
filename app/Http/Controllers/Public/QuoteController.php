<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Notifications\NewQuoteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class QuoteController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'         => ['required','string','max:160'],
            'company'      => ['nullable','string','max:160'],
            'email'        => ['nullable','email','max:160'],
            'phone'        => ['nullable','string','max:40'],
            'city'         => ['nullable','string','max:120'],
            'project_type' => ['nullable','string','max:120'],
            'budget'       => ['nullable','string','max:120'],
            'desired_date' => ['nullable','date'],
            'message'      => ['nullable','string','max:10000'],
            'files.*'      => ['nullable','file','mimes:pdf,zip,rar,7z,doc,docx,xls,xlsx,dwg,dxf','max:15360'],
            'hp'           => ['nullable','size:0'],
        ]);

        $paths = [];
        if ($req->hasFile('files')) {
            foreach ($req->file('files') as $file) {
                $paths[] = $file->store('uploads/devis/'.date('Y/m'), 'public');
            }
        }
        $data['files'] = $paths;

        $quote = QuoteRequest::create($data);

        if (config('mail.from.address')) {
            Notification::route('mail', config('mail.from.address'))
                ->notify(new NewQuoteNotification($quote));
        }

        return back()->with('ok','Demande de devis reçue. Nous revenons vers vous rapidement.');
    }
}
