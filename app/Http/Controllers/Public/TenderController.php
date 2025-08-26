<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Notifications\NewTenderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TenderController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'reference'     => ['nullable','string','max:120'],
            'organization'  => ['nullable','string','max:200'],
            'contact_name'  => ['nullable','string','max:160'],
            'email'         => ['nullable','email','max:160'],
            'phone'         => ['nullable','string','max:40'],
            'deadline'      => ['nullable','date'],
            'scope'         => ['nullable','string','max:15000'],
            'documents.*'   => ['nullable','file','mimes:pdf,zip,rar,7z,dwg,dxf,doc,docx,xls,xlsx','max:25600'],
            'hp'            => ['nullable','size:0'],
        ]);

        $docs = [];
        if ($req->hasFile('documents')) {
            foreach ($req->file('documents') as $f) {
                $docs[] = $f->store('uploads/ao/'.date('Y/m'), 'public');
            }
        }
        $data['documents'] = $docs;

        $tender = Tender::create($data);

        if (config('mail.from.address')) {
            Notification::route('mail', config('mail.from.address'))
                ->notify(new NewTenderNotification($tender));
        }

        return back()->with('ok','Votre appel d’offres a été transmis.');
    }
}
