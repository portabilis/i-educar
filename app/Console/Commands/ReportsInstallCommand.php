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
     * Return the reports source path.
     *
     * @return string
     */
    protected function getJasperFiles()
    {
        return base_path('ieducar/modules/Reports/ReportSources');
    }

    /**
     * Return the JasperStarter binary file.
     *
     * @return string
     */
    protected function getJasperStarter()
    {
        return base_path('vendor/cossou/jasperphp/src/JasperStarter/bin/jasperstarter');
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

        $this->info('Compiling reports files..');

        $jasperFiles = $this->getJasperFiles();
        $jasperStarter = $this->getJasperStarter();

        passthru('cd ' . $jasperFiles . '; for line in $(ls -a | sort | grep .jrxml | sed -e "s/\.jrxml//"); do $(' . $jasperStarter . ' cp $line.jrxml -o $line); done');

        $this->call('migrate', [
            '--force' => true
        ]);
    }
}
