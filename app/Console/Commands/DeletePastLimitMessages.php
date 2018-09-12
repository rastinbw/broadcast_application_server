<?php

namespace App\Console\Commands;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;

class DeletePastLimitMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:delete_past_limit_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would remove those messages which has been passed user message log limitation time.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user){
            Message::where([
                ['created_at', '<=', Carbon::now()->subMinutes($user->message_log_time_limit)->toDateTimeString()]
            ])->delete();
        }
    }
}
