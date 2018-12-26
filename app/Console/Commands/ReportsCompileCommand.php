<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportsCompileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile reports files';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Compiling reports files..');

        $jasperFiles = $this->getJasperFiles();
        $jasperStarter = $this->getJasperStarter();

        passthru('cd ' . $jasperFiles . '; for line in $(ls -a | sort | grep .jrxml | sed -e "s/\.jrxml//"); do $(' . $jasperStarter . ' cp $line.jrxml -o $line); done');
    }
}
