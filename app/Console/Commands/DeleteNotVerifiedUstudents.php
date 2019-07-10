<?php

namespace App\Console\Commands;

use App\Models\Ustudent;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteNotVerifiedUstudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:delete_not_verified_ustudents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command would remove those ustudents who registered but did not succeed to verify
                              themselves in a certain limited period of time.";

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
        $ustudents = Ustudent::where([
            ['verified', '=', false],
            ['created_at', '<=', Carbon::now()->subMinutes(1)->toDateTimeString()]
        ]);

        foreach ($ustudents->get() as $ustudent){
            $ustudent->plans()->sync([]);
        }

        $ustudents->delete();
    }
}
