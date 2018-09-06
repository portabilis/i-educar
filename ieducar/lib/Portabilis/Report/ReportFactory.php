<?php

require_once 'lib/Portabilis/Array/Utils.php';

class Portabilis_Report_ReportFactory
{
    public function __construct()
    {
        $this->config = $GLOBALS['coreExt']['Config'];
        $this->settings = [];

        $this->setSettings($this->config);
    }

    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    public function setSettings($config)
    {
        throw new Exception('The method \'setSettings\' from class Portabilis_Report_ReportFactory must be overridden!');
    }

    public function dumps($report, $options = [])
    {
        throw new Exception('The method \'dumps\' from class Portabilis_Report_ReportFactory must be overridden!');
    }
}
