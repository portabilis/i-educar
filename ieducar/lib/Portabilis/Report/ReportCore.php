<?php

require_once 'lib/Portabilis/Array/Utils.php';

class Portabilis_Report_ReportCore
{
    public function __construct()
    {
        $this->requiredArgs = [];
        $this->args = [];

        $this->requiredArgs();
    }

    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    public function addArg($name, $value)
    {
        if (is_string($value)) {
            $value = $value;
        }

        $this->args[$name] = $value;
    }

    public function addRequiredArg($name)
    {
        $this->requiredArgs[] = $name;
    }

    public function validatesPresenseOfRequiredArgs()
    {
        foreach ($this->requiredArgs as $requiredArg) {
            if (!isset($this->args[$requiredArg]) || empty($this->args[$requiredArg])) {
                throw new Exception("The required arg '{$requiredArg}' wasn't set or is empty!");
            }
        }
    }

    public function dumps($options = [])
    {
        $defaultOptions = ['report_factory' => null, 'options' => []];
        $options = self::mergeOptions($options, $defaultOptions);

        $this->validatesPresenseOfRequiredArgs();

        $reportFactory = !is_null($options['report_factory']) ? $options['report_factory'] : $this->reportFactory();

        return $reportFactory->dumps($this, $options['options']);
    }

    public function reportFactory()
    {
        $factoryClassName = $GLOBALS['coreExt']['Config']->report->default_factory;
        $factoryClassPath = str_replace('_', '/', $factoryClassName) . '.php';

        if (!$factoryClassName) {
            throw new CoreExt_Exception('No report.default_factory defined in configurations!');
        }

        include_once $factoryClassPath;

        if (!class_exists($factoryClassName)) {
            throw new CoreExt_Exception("Class '$factoryClassName' not found in path '$factoryClassPath'");
        }

        return new $factoryClassName();
    }

    public function templateName()
    {
        throw new Exception('The method \'templateName\' must be overridden!');
    }

    public function requiredArgs()
    {
        throw new Exception('The method \'requiredArgs\' must be overridden!');
    }
}
