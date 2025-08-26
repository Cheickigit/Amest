<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTenderNotification extends Notification
{
    use Queueable;

    public function __construct(public Tender $tender) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($n): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvel appel d’offres')
            ->line('Réf: '.$this->tender->reference.' | Organisme: '.$this->tender->organization)
            ->line('Contact: '.$this->tender->contact_name.' | '.$this->tender->email.' | '.$this->tender->phone)
            ->line('Date limite: '.optional($this->tender->deadline)->format('d/m/Y'))
            ->line('Objet: '.$this->tender->scope);
    }
}
