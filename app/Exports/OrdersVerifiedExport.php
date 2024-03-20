<?php

namespace App\Exports;

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersVerifiedExport implements FromCollection, WithMapping, WithHeadings
{
    protected int $index = 0;

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Order::where('status', OrderStatus::VERIFIED)->get();
    }

    /**
     * @param Order $row
     * @return array
     */
    public function map($row): array
    {
        return [
            ++$this->index,
            $row->code,
            sprintf('%s (%s)', $row->orderItem->receiver_th_name, $row->orderItem->receiver_en_name),
            sprintf(
                '%s, Tambon %s, Amphur %s, %s Province, %s',
                $row->shipping->address,
                $row->shipping->subDistrict->en_name,
                $row->shipping->subDistrict->district->en_name,
                $row->shipping->subDistrict->district->city->en_name,
                $row->shipping->subDistrict->zip_code,
            ),
            $row->shipping->phone,
            1,
            750,
            null,
        ];
    }

    public function headings(): array
    {
        return [
            'No.',
            'Order Number',
            'Receiver Name',
            'Address',
            'Receiver Mobile No.',
            'Qty',
            'Weight (gr)',
            'Tracking Code',
        ];
    }
}
