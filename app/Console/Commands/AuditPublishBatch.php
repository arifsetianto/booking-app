<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class AuditPublishBatch extends Command
{
    protected $signature = 'batch:publish';

    protected $description = 'Publish batch automatically and close other published batches if necessary';

    public function handle(): void
    {
        $now = Carbon::now();

        // Fetch batch that should be published now
        $batchToPublish = Batch::where('publish_at', '<=', $now)
                               ->where('total_stock', '>', 'purchased_stock')
                               ->where('status', BatchStatus::PENDING)
                               ->first();

        if ($batchToPublish) {
            // Close other published batches
            Batch::where('status', BatchStatus::PUBLISHED)->update(['status' => BatchStatus::CLOSED]);

            // Publish the current batch
            $batchToPublish->update(['status' => BatchStatus::PUBLISHED]);
            $this->info("Batch {$batchToPublish->number} has been published.");
        } else {
            $this->info('No batch found to publish.');
        }
    }
}
