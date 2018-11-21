<?php

namespace Tests\SuiteTestCase;

use Exception;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

class DataSet
{
    private $dataSet;
    private $suiteName;

    public function __construct(TestCase $test)
    {
        $this->suiteName = $this->extractSuiteName($test->toString());
        $this->dataSet = $this->createYamlDataSet($test->getYamlDataSet());
    }

    private function createYamlDataSet($yamlFile)
    {
        if ($this->objectValid($yamlFile)) {
            return $yamlFile;
        }

        $filename = $yamlFile;

        return $this->getYamlDataSet($filename);
    }

    private function extractSuiteName($string)
    {
        $parts = explode('\\', $string);
        return $parts[1];
    }

    private function objectValid($object)
    {
        if ($object instanceof DefaultDataset) {
            return true;
        }

        return false;
    }

    /**
     * @param $filename
     *
     * @return YamlDataSet
     *
     * @throws \Exception
     */
    private function getYamlDataSet($filename)
    {
        if (! is_array($filename)) {
            return new YamlDataSet($this->getDirDataSet() . $filename);
        }

        throw new Exception('Não implementado');
    }

    private function getDirDataSet()
    {
        return __DIR__ . '/../' . $this->suiteName . '/datasets/';
    }

    /**
     * @return YamlDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }
}
