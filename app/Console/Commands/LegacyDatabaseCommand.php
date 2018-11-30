<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LegacyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs initial database seeder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Seeding database..');

        $this->call('db:seed', [
            '--class' => 'InitialDatabaseSeeder',
            '--force' => true
        ]);
    }
}
