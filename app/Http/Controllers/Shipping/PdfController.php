<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class PdfController extends Controller
{
    public function generateLabel(Request $request): Response
    {
        /** @var Order $order */
        $order = Order::findOrFail($request->route('order'));
        $path = public_path('images/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $order->printed = true;
        $order->save();

        $data = ['order' => $order, 'image' => $base64];
        $pdf = PDF::loadView('pdf.shipping.print-label', $data)->setPaper('a4');

        return $pdf->download(sprintf('%s.pdf', $order->code));
    }
}
