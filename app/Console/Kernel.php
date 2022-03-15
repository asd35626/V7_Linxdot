<?php

namespace App\Console;

use Artisan;
use Storage;
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
        //
        Commands\createModel::class,
        // Commands\userCheckout::class,
        // Commands\restartGameServer::class,
        // Commands\addBaccaratLiveRoom::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('userCheckout')->dailyAt('15:00')->sendOutputTo(storage_path('app/public')."/userCheckout.log")->withoutOverlapping()->after(function () {
        //         // 任務已完成...
        //         Artisan::call('exportCheckoutReportByUser');
        //         Storage::append(storage_path('app/public')."/userCheckout.log", Artisan::output());
        //         Artisan::call('databaseReport');
        //         Storage::append(storage_path('app/public')."/userCheckout.log", Artisan::output());
        //         Artisan::call('exportMachineReport');
        //         Storage::append(storage_path('app/public')."/userCheckout.log", Artisan::output());
        //     });
        // $schedule->command('inspire')
        //          ->hourly();
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
