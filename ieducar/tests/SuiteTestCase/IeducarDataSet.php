<?php

namespace Tests\SuiteTestCase;

use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableIterator;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use Traversable;

class IeducarDataSet implements IDataSet
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
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->dataSet->getIterator();
    }

    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames()
    {
        return $this->dataSet->getTableNames();
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     *
     * @return ITableMetadata
     */
    public function getTableMetaData($tableName)
    {
        $this->dataSet->getTableMetaData($tableName);
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return ITable
     */
    public function getTable($tableName)
    {
        return $this->dataSet->getTable($tableName);
    }

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getReverseIterator()
    {
        return $this->dataSet->getReverseIterator();
    }

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param IDataSet $other
     */
    public function matches(IDataSet $other)
    {
        return $this->dataSet->matches($other);
    }
}