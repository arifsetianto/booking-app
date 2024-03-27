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
class OrderForceCanceledMail extends Mailable
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
            subject: sprintf('Order has been canceled #%s', $this->order->code),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.order-force-canceled',
            with: [
                'url'   => $this->url,
                'order' => $this->order,
                'user'  => $this->order->user
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
