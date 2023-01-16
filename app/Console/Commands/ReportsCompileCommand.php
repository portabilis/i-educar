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

    protected function getJasperFiles(): ?string
    {
        $reportDefaultPath = 'ieducar/modules/Reports/ReportSources';
        if(false === is_dir(base_path($reportDefaultPath))) {
            return null;
        }

        return base_path($reportDefaultPath);
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

        if ($jasperFiles === null) {
            $this->info('Report Packet not install or linked');
            return;
        }

        $jasperStarter = $this->getJasperStarter();

        passthru('cd ' . $jasperFiles . '; for line in $(ls -a | sort | grep .jrxml | sed -e "s/\.jrxml//"); do $(' . $jasperStarter . ' cp $line.jrxml -o $line); done');
    }
}
