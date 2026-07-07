<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DailyScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $dateString;
    public int $count;
    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(string $dateString, int $count, string $pdfPath)
    {
        $this->dateString = $dateString;
        $this->count = $count;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Schedule Backup: ' . $this->dateString,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_schedule',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('schedule_backup_' . str_replace([' ', ','], ['_', ''], $this->dateString) . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
