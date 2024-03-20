<?php

namespace App\Jobs;

use App\Event\Order\OrderCompleted;
use App\Models\Order;
use App\ValueObject\OrderStatus;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImportOrdersVerified implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $orderCode, protected ?string $trackingCode)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Order $order */
        if (null !== $order = Order::where('code', $this->orderCode)->first()) {
            if ($order->status->is(OrderStatus::VERIFIED)) {
                if (null !== $this->trackingCode) {
                    $order->status = OrderStatus::COMPLETED;
                    $order->completed_at = Carbon::now();
                    $order->shipping->tracking_code = $this->trackingCode;
                    $order->error_message = null;
                    $order->save();
                    $order->shipping->save();

                    event(new OrderCompleted($order));
                } else {
                    $order->error_message = 'Tracking code in file import cannot be empty.';
                    $order->save();
                }
            }
        }
    }

    public function failed(?\Throwable $exception): void
    {
        if (null !== $order = Order::where('code', $this->orderCode)->first()) {
            $order->error_message = $exception->getMessage();
            $order->save();
        }
    }
}
