<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CheckStock
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Batch $batch */
        $batch = Batch::where('status', BatchStatus::PUBLISHED)->first();

        if ($batch && $batch->getAvailableStock() >= 1) {
            return $next($request);
        }

        return redirect()->route('stock.unavailable');
    }
}
