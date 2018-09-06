<?php

require_once 'lib/Portabilis/Report/ReportFactory.php';
require_once 'relatorios/phpjasperxml/class/fpdf/fpdf.php';
require_once 'relatorios/phpjasperxml/class/PHPJasperXML.inc';

class Portabilis_Report_ReportFactoryPHPJasperXML extends Portabilis_Report_ReportFactory
{
    /**
     * Define as configurações dos relatórios.
     *
     * @param object $config
     *
     * @return void
     */
    public function setSettings($config)
    {
        $this->settings['db'] = $config->app->database;
        $this->settings['logo_file_name'] = $config->report->logo_file_name;
    }

    /**
     * Carrega o arquivo .jrxml do template.
     *
     * @param string $templateName
     *
     * @return SimpleXMLElement
     *
     * @throws CoreExt_Exception
     */
    public function loadReportSource($templateName)
    {
        $rootPath = dirname(dirname(dirname(dirname(__FILE__))));
        $fileName = "$templateName.jrxml";
        $filePath = $rootPath . "/modules/Reports/ReportSources/$fileName";

        if (!file_exists($filePath)) {
            throw new CoreExt_Exception("Report source '$fileName' not found in path '$filePath'");
        }

        return simplexml_load_file($filePath);
    }

    /**
     * Retorna o arquivo da logo utilizada nos relatórios.
     *
     * @return string
     *
     * @throws CoreExt_Exception
     * @throws Exception
     */
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

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function dumps($report, $options = [])
    {
        $options = self::mergeOptions($options, [
            'add_logo_arg' => true
        ]);

        if ($options['add_logo_arg']) {
            $report->addArg('logo', $this->logoPath());
        }

        $xml = $this->loadReportSource($report->templateName());

        $builder = new PHPJasperXML();

        $builder->debugsql = false;
        $builder->arrayParameter = $report->args;

        $builder->xml_dismantle($xml);

        $builder->transferDBtoArray(
            $this->settings['db']->hostname,
            $this->settings['db']->username,
            $this->settings['db']->password,
            $this->settings['db']->dbname,
            $this->settings['db']->port
        );

        // I: standard output
        // D: Download file
        // F: file
        $builder->outpage('I');
    }
}
