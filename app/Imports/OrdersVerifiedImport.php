<?php

namespace App\Imports;

use App\Jobs\ProcessImportOrdersVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrdersVerifiedImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return void
    */
    public function model(array $row): void
    {
        ProcessImportOrdersVerified::dispatch($row['order_number'], $row['tracking_code'] ?? null)->onQueue('import');
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
