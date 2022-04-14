<?php

class Portabilis_DataMapper_Utils
{
    public function getDataMapperFor($packageName, $modelName)
    {
        $dataMapperClassName = ucfirst($packageName) . '_Model_' . ucfirst($modelName) . 'DataMapper';
        $classPath = str_replace('_', '/', $dataMapperClassName) . '.php';

        include_once $classPath;

        if (!class_exists($dataMapperClassName)) {
            throw new CoreExt_Exception("Class '$dataMapperClassName' not found in path $classPath.");
        }

        return new $dataMapperClassName();
    }
}
