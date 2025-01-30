<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class NewMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ThaiQuran! Your Account is Successfully Registered',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.new-member',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
