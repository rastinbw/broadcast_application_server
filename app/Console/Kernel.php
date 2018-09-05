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
        '\App\Console\Commands\CheckUsersActivationDate',
        '\App\Console\Commands\DeleteNotVerifiedUstudents',
        '\App\Console\Commands\DeletePastLimitMessages',
        '\App\Console\Commands\DeletePastLimitPosts',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('broadcast:delete_not_verified_ustudents')
            ->everyThirtyMinutes();

        $schedule->command('broadcast:delete_past_limit_posts')
            ->everyThirtyMinutes();

        $schedule->command('broadcast:delete_past_limit_messages')
            ->everyThirtyMinutes();

        $schedule->command('broadcast:check_users_activation_date')
            ->everyThirtyMinutes();
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
