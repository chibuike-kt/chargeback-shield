<?php

namespace App\Mail;

use App\Models\Merchant;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebhookFailureMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Merchant         $merchant,
        public WebhookDelivery  $delivery,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Webhook permanently failed — {$this->delivery->event_type->value}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.webhook-failure',
        );
    }
}
