<?php

require_once 'lib/Portabilis/Report/ReportFactory.php';
require_once 'vendor/autoload.php';

use JasperPHP\JasperPHP;

class Portabilis_Report_ReportFactoryPHPJasper extends Portabilis_Report_ReportFactory
{
    public function setSettings($config)
    {
        $this->settings['db'] = $config->app->database;
        $this->settings['logo_file_name'] = $config->report->logo_file_name;
    }

    public function getReportsPath()
    {
        $rootPath = dirname(dirname(dirname(dirname(__FILE__))));
        $reportsPath = $rootPath . '/modules/Reports/ReportSources/Portabilis/';

        return $reportsPath;
    }

    public function logoPath()
    {
        if (!$this->settings['logo_file_name']) {
            throw new Exception('No report.logo_file_name defined in configurations!');
        }

        $rootPath = dirname(dirname(dirname(dirname(__FILE__))));
        $filePath = $rootPath . "/modules/Reports/ReportLogos/{$this->settings['logo_file_name']}";

        if (!file_exists($filePath)) {
            throw new CoreExt_Exception("Report logo '{$this->settings['logo_file_name']}' not found in path '$filePath'");
        }

        return $filePath;
    }

    public function dumps($report, $options = [])
    {
        $defaultOptions = ['add_logo_arg' => true];
        $options = self::mergeOptions($options, $defaultOptions);

        if ($options['add_logo_arg']) {
            $report->addArg('logo', $this->logoPath());
        }

        // Generate a random file name
        $outputFile = $this->getReportsPath() . time() . '-' . mt_rand();;

        // Corrige parametros boleanos
        foreach ($report->args as $key => $value) {
            if (is_bool($value)) {
                $report->args[$key] = ($value ? 'true' : 'false');
            }
        }

        $builder = new JasperPHP();
        $return = $builder->process(
            $this->getReportsPath() . $report->templateName() . '.jasper',
            $outputFile,
            ['pdf'],
            $report->args,
            [
                'driver' => 'postgres',
                'username' => $this->settings['db']->username,
                'host' => $this->settings['db']->hostname,
                'database' => $this->settings['db']->dbname,
                'port' => $this->settings['db']->port,
                'password' => $this->settings['db']->password,
            ],
            false
        )->execute();

        $outputFile .= '.pdf';

        $this->showPDF($outputFile);
        $this->destroyPDF($outputFile);
    }

    public function showPDF($file)
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/pdf;');
        header('Content-Disposition: inline;');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));

        ob_clean();
        flush();

        readfile($file);
    }

    public function destroyPDF($file)
    {
        unlink($file);
    }
}
