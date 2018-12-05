<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;

class ReportsInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install reports package';

    /**
     * Return initial reports database file
     *
     * @return string
     */
    protected function getInitialReportsDatabaseFile()
    {
        return base_path('ieducar/modules/Reports/database/sqls/initial-reports-database.sql');
    }

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     * @param Connection $connection
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Filesystem $filesystem, Connection $connection)
    {
        $file = $this->getInitialReportsDatabaseFile();

        if (!$filesystem->exists($file)) {
            $this->error('Initial reports database file not found.');

            return;
        }

        $this->info('Seeding database with reports data..');

        $connection->unprepared(
            $filesystem->get($file)
        );

        $this->call('migrate', [
            '--force' => true
        ]);
    }
}
