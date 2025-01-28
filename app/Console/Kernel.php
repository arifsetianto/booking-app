<?php

namespace App\Console;

use App\Console\Commands\AuditPublishBatch;
use App\Console\Commands\PruneExpiredPayment;
use App\Console\Commands\RetryFailedJobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command(PruneExpiredPayment::class)
        //         ->runInBackground()
        //         ->everyMinute();

        //$schedule->command(RetryFailedJobs::class)
        //         ->runInBackground()
        //         ->everyMinute();

        $schedule->command(AuditPublishBatch::class)
                 ->runInBackground()
                 ->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
