<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $message) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($n): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau message ('.$this->message->source.')')
            ->line('Nom: '.$this->message->name)
            ->line('Email: '.$this->message->email.' | Tél: '.$this->message->phone)
            ->line('Sujet: '.$this->message->subject)
            ->line('Message: '.$this->message->message)
            ->line('IP: '.$this->message->ip);
    }
}
