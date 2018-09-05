<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeletePastLimitPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:delete_past_limit_posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would remove those posts which has been passed user post limitation time.";
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
            Post::where([
                ['created_at', '<=', Carbon::now()->subMinutes($user->post_limitation_time)->toDateTimeString()]
            ])->delete();
        }
    }
}
