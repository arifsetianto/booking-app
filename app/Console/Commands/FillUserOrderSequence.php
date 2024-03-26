<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\ValueObject\UserStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class FillUserOrderSequence extends Command
{
    protected $signature = 'order:fill-user-order-sequence';

    protected $description = 'Fill user order sequence';

    public function handle(): void
    {
        $this->withProgressBar($this->getQueryUser(), function (User $user) {
            /**
             * @var integer $i
             * @var Order $order
             */
            foreach ($this->getQueryUserOrder($user->id) as $i => $order) {
                $order->user_order_sequence = $i + 1;
                $order->save();
            }
        });
    }

    public function getQueryUser(): LazyCollection
    {
        return User::where('status', operator: UserStatus::COMPLETED)
                     ->whereNotNull('profile_id')
                     ->cursor();
    }

    public function getQueryUserOrder(string $userId): LazyCollection
    {
        return Order::where('user_id', $userId)
                     ->orderBy('code')
                     ->cursor();
    }
}
