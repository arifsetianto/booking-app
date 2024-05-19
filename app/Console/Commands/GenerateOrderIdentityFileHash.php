<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class GenerateOrderIdentityFileHash extends Command
{
    protected $signature = 'order:generate-identity-file-hash';

    protected $description = 'Generate order identity file hash';

    public function handle(): void
    {
        /** @var OrderItem $orderItem */
        foreach ($this->getQuery() as $orderItem) {
            $orderItem = OrderItem::find($orderItem->id);
            $fileContent = Storage::get($orderItem->identity_file);
            $fileHash = md5($fileContent);

            // Check if the file with the same hash exists
            $existingFile = OrderItem::where('identity_file_hash', $fileHash)->first();

            if ($existingFile) {
                $this->error('The Receiver Thai ID file has been uploaded.');

                continue;
            }

            $orderItem->identity_file_hash = $fileHash;
            $orderItem->save();

            $this->info('Identity file hash has been set successfully.');
        }
    }

    public function getQuery(): LazyCollection
    {
        return DB::table('order_items')
                 ->whereNull('identity_file_hash')
                 ->cursor();
    }
}
