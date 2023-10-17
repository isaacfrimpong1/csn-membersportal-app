<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         //$schedule->command('app:fetch-sales-data')->hourly();
         $schedule->command('app:fetch-sales-data')->everyminute()->appendOutputTo(storage_path('logs/fetch-sales-data.log'));
         //$schedule->command('app:fetch-stock-data')->hourly(24);
         $schedule->command('app:fetch-stock-data')->everyminute();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Register the FetchStockData command
        $this->load(__DIR__.'/Commands/FetchStockData');

        // Register the FetchSalesData command
        $this->load(__DIR__.'/Commands/FetchSalesData');

        require base_path('routes/console.php');
    }
}
