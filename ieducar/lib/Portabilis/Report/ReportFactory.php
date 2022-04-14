<?php

abstract class Portabilis_Report_ReportFactory
{
    /**
     * @var object
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
        $this->config = json_decode(json_encode(config('legacy')));
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
     */
    abstract public function setSettings($config);

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array                        $options
     *
     * @return mixed
     */
    abstract public function dumps($report, $options = []);
}
