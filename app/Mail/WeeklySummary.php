<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySummary extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $stats,
        public $trendingProjects,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu resumen semanal de Fluxa',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-summary',
        );
    }
}
