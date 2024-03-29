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
        public readonly bool   $useReference
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->useReference ? sprintf('Special Re-Invitation Order #%s', $this->order->code) :
                'Limited Invitation: Book Your Thai Quran Today!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: $this->useReference ? 'emails.orders.order-existing-invited' : 'emails.orders.order-new-invited',
            with: [
                'url'   => $this->url,
                'user'  => $this->order->user,
                'order' => $this->order,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
