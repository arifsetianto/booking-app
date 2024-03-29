<?php

declare(strict_types=1);

namespace App\Event\Order;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderInvitationConfirmed
{
    use SerializesModels;

    public function __construct(protected Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
