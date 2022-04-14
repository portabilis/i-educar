<?php

namespace App\Console\Commands;

use App\Jobs\BatchDisableUsersWithDaysGoneSinceLastAccess;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\DB;

class DisableUsersWithAccessExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disable:users-with-access-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable users with access expired';

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
     * @return int
     */
    public function handle()
    {
        $job = new BatchDisableUsersWithDaysGoneSinceLastAccess(DB::getDefaultConnection());
        app(Dispatcher::class)->dispatch($job);

        return 0;
    }
}
