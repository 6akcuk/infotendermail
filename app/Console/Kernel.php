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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\DownloadContracts::class,
        \App\Console\Commands\BuildIndex::class,
        \App\Console\Commands\SendMail::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('download:contracts')
                 ->hourly();

        $schedule->command('build:index')
                 ->dailyAt('07:00');

        $schedule->command('send:mail')
                 ->dailyAt('08:00');
    }
}
