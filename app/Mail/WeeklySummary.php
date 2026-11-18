<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklySummary extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public array $stats,
        public Collection $trendingProjects,
        public string $unsubscribeUrl,
        public string $unsubscribeOneClickUrl,
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

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => '<'.$this->unsubscribeOneClickUrl.'>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }
}
