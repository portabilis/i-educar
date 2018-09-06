<?php

require_once 'lib/Portabilis/Array/Utils.php';

class Portabilis_Report_ReportFactory
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $settings;

    /**
     * Portabilis_Report_ReportFactory constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = $GLOBALS['coreExt']['Config'];
        $this->settings = [];

        $this->setSettings($this->config);
    }

    /**
     * Wrapper para Portabilis_Array_Utils::merge.
     *
     * @see Portabilis_Array_Utils::merge()
     *
     * @param array $options
     * @param array $defaultOptions
     *
     * @return array
     */
    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    /**
     * Define as configurações dos relatórios.
     *
     * @param object $config
     *
     * @return void
     *
     * @throws Exception
     */
    public function setSettings($config)
    {
        throw new Exception('The method \'setSettings\' from class Portabilis_Report_ReportFactory must be overridden!');
    }

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function dumps($report, $options = [])
    {
        throw new Exception('The method \'dumps\' from class Portabilis_Report_ReportFactory must be overridden!');
    }
}
