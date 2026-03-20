<?php

namespace App\Mail;

use App\Models\Dispute;
use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChargebackAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Merchant $merchant,
        public Dispute  $dispute,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chargeback filed — {$this->dispute->reason_code} — response ready",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chargeback-alert',
        );
    }
}
