<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuoteNotification extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequest $quote) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($n): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de devis')
            ->line('Nom: '.$this->quote->name.' / Société: '.$this->quote->company)
            ->line('Email: '.$this->quote->email.' | Tél: '.$this->quote->phone)
            ->line('Ville: '.$this->quote->city.' | Type: '.$this->quote->project_type.' | Budget: '.$this->quote->budget)
            ->line('Message: '.$this->quote->message);
    }
}
