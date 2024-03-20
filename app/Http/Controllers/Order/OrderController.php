<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Exports\OrdersVerifiedExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderController extends Controller
{
    public function verifiedExport(): BinaryFileResponse
    {
        return Excel::download(new OrdersVerifiedExport(), sprintf('thaiquran-orders-%s.xlsx', date('ymd')));
    }
}
