<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportsInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:install {--no-compile} {--no-migrate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install reports package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $compile = $this->option('no-compile') === false;
        $migrate = $this->option('no-migrate') === false;

        passthru('chmod +x vendor/cossou/jasperphp/src/JasperStarter/bin/jasperstarter');
        passthru('chmod 777 ieducar/modules/Reports/ReportSources');

        if ($compile) {
            $this->call('reports:compile');
        }

        if ($migrate) {
            $this->call('migrate', [
                '--force' => true,
            ]);
        }
    }
}
