<?php

require_once 'lib/Portabilis/Report/ReportFactory.php';
require_once 'vendor/autoload.php';

use JasperPHP\JasperPHP;

class Portabilis_Report_ReportFactoryPHPJasper extends Portabilis_Report_ReportFactory
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
     * Retorna o diretório dos relatórios.
     *
     * @return string
     */
    public function getReportsPath()
    {
        return $GLOBALS['coreExt']['Config']->report->source_path;
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

        $dataFile = $this->getReportsPath() . time() . '-' . mt_rand();
        $outputFile = $this->getReportsPath() . time() . '-' . mt_rand();
        $filename = $this->getReportsPath() . $report->templateName();
        $jasperFile = $filename . '.jasper';
        $jrxmlFile = $filename . '.jrxml';

        foreach ($report->args as $key => $value) {
            if (is_bool($value)) {
                $report->args[$key] = ($value ? 'true' : 'false');
            }
        }

        $builder = new JasperPHP();

        // Compila o arquivo .jrxml caso o arquivo .jasper não exista.

        if (file_exists($jasperFile) === false) {
            $builder->compile($jrxmlFile, $filename, false, false)->execute();
        }

        // Com o intuito de manter a compatibilidade até finalizar a migração
        // de todos os relatórios será utilizado o método useJson() para
        // informar qual tipo de data source será utilizado.

        if ($report->useJson()) {

            $data = $report->getJsonData();
            $json = json_encode($data);

            file_put_contents($dataFile, $json);

            $report->addArg('source', $dataFile);

            $builder->process(
                $jasperFile,
                $outputFile,
                ['pdf'],
                $report->args,
                [
                    'driver'=>'json',
                    'json_query' => $report->getJsonQuery(),
                    'data_file' =>  $dataFile
                ],
                false // Não executar em background garante que o erro será retornado
            )->execute();

            unlink($dataFile);

        } else {
            $builder->process(
                $jasperFile,
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
                false // Não executar em background garante que o erro será retornado
            )->execute();
        }

        $outputFile .= '.pdf';

        $this->showPDF($outputFile);
        $this->destroyPDF($outputFile);
    }

    /**
     * Lê o PDF gerado para a saída de conteúdo.
     *
     * @param string $file
     *
     * @return void
     */
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

    /**
     * Deleta o PDF gerado.
     *
     * @param string $file
     *
     * @return void
     */
    public function destroyPDF($file)
    {
        unlink($file);
    }
}
