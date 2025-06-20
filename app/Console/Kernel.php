<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\AutoPurchase::class,
        // Commands\DsoAlert::class,
        // Commands\ResetDB::class,
        Commands\StockWhatsappNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('transaction:check-activity')->hourly();
        // * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
        // php artisan transaction:check-activity
        // * * * * * cd /path/to/sbpos && php artisan schedule:run >> /dev/null 2>&1

        $schedule->command('stock:whatsapp-notification')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
