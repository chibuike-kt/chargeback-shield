<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Merchant $merchant,
        public array    $stats,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Chargeback Shield weekly summary',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-summary',
        );
    }
}
