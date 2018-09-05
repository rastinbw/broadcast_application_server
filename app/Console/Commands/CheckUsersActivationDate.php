<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckUsersActivationDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:check_users_activation_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would check users activation date and sends users' information whose 
                              activation date passed one year to admin email address.";

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
            if ($user->activation_date <=  Carbon::now()->subHours(8760)->toDateTimeString()){
                //todo send user info via email
            }
        }
    }
}
