<?php

namespace App\Console;

use App\Models\Message;
use App\Models\Post;
use App\Models\Ustudent;
use App\User;
use Carbon\Carbon;

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
        // broadcast:delete_not_verified_ustudents
        $schedule->call(function (){
            $ustudents = Ustudent::where([
                ['verified', '=', false],
                ['created_at', '<=', Carbon::now()->subMinutes(1)->toDateTimeString()]
            ]);

            foreach ($ustudents->get() as $ustudent){
                $ustudent->plans()->sync([]);
            }

            $ustudents->delete();
        });

        // broadcast:delete_past_limit_posts
        $schedule->call(function (){
            $users = User::all();
            foreach ($users as $user){
                if ($user->post_time_limit != null) {
                    Post::where([
                        ['user_id', '=', $user->id],
                        ['created_at', '<=', Carbon::now()->subMinutes($user->post_time_limit)->toDateTimeString()]
                    ])->delete();
                }
            }
        });

        // broadcast:delete_past_limit_messages
        $schedule->call(function (){
            $users = User::all();
            foreach ($users as $user){
                if ($user->message_log_time_limit != null) {
                    $limit = $user->message_log_time_limit;
                    Message::where([
                        ['user_id', '=', $user->id],
                        ['created_at', '<=', Carbon::now()->subMinutes($limit)->toDateTimeString()]
                    ])->delete();
                }
            }
        });

        // broadcast:check_users_activation_date
        $schedule->call(function (){
            $users = User::all();
            foreach ($users as $user){
                if ($user->activation_date != null)
                    if ($user->activation_date <=  Carbon::now()->subHours(8760)->toDateTimeString()){
                        //todo send user info via email
                    }
            }
        });

        //$schedule->command('broadcast:delete_not_verified_ustudents')
        //    ->everyMinute();

        //$schedule->command('broadcast:delete_past_limit_posts')
        //    ->everyMinute();

        //$schedule->command('broadcast:delete_past_limit_messages')
        //    ->everyMinute();

        //$schedule->command('broadcast:check_users_activation_date')
        //    ->everyThirtyMinutes();
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
