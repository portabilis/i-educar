<?php

namespace Tests\SuiteTestCase;

use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableIterator;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use Traversable;

class IeducarDataSet
{
    private $dataSet;
    private $suiteName;

    public function __construct(IeducarTestCase $test)
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

    private function getYamlDataSet($filename)
    {
        if (!\is_array($filename)) {
            return new YamlDataSet($this->getDirDataSet() . $filename);
        }

        throw \Exception('Não implementado');
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