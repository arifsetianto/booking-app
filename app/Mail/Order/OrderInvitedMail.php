<?php

declare(strict_types=1);

namespace App\Mail\Order;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderInvitedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $url,
        public readonly Order  $order,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New invitation to book ThaiQuran!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.order-invited',
            with: [
                'url'  => $this->url,
                'user' => $this->order->user
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
