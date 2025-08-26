<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Notifications\NewContactNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'    => ['nullable','string','max:160'],
            'email'   => ['nullable','email','max:160'],
            'phone'   => ['nullable','string','max:40'],
            'subject' => ['nullable','string','max:180'],
            'message' => ['nullable','string','max:10000'],
            'source'  => ['required','in:contact,devis,rfp'],
            'hp'      => ['nullable','size:0'], // honeypot
        ]);

        $data['ip'] = $req->ip();
        $msg = ContactMessage::create($data);

        if (config('mail.from.address')) {
            Notification::route('mail', config('mail.from.address'))
                ->notify(new NewContactNotification($msg));
        }

        return back()->with('ok','Votre demande a bien été envoyée.');
    }
}
