<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class RetryFailedJobs extends Command
{
    protected $signature = 'jobs:retry';
    protected $description = 'Retry failed jobs';

    public function handle(): void
    {
        $maxRetries = 120;
        $retries = 0;

        $failedJobs = DB::table('failed_jobs')->select()->get();

        foreach ($failedJobs as $job) {
            if ($retries >= $maxRetries) {
                break;
            }

            Artisan::call('queue:retry', ['id' => [$job->uuid]]);
            $retries++;
        }

        $this->info("Retried $retries failed jobs out of $maxRetries limit.");
    }
}
