<?php

namespace iEducar\Config;

use CoreExt_Config;
use Dotenv\Dotenv;

class Env extends CoreExt_Config
{
    /**
     * Env constructor.
     * @param Dotenv $enviroment
     * @param Dotenv[] $tenants
     */
    public function __construct(Dotenv $enviroment, $tenants = [])
    {
        $enviroment->load();

        $this->loadTenants($tenants);

        parent::__construct($this->getEnviromentVariables());
    }

    /**
     * @param Dotenv $file
     */
    public function addFile(Dotenv $file): void
    {
        $file->overload();
    }

    /**
     * @param Dotenv[] $tenants
     */
    private function loadTenants($tenants)
    {
        foreach ($tenants as $tenant) {
            $tenant->overload();
        }
    }

    /**
     * @return array
     */
    private function getEnviromentVariables(): array
    {
        $config = [];
        $entries = $_ENV;

        foreach ($entries as $key => $value) {
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);
            } else {
                $keys = (array)$key;
            }

            $config = $this->processDirectives($value, $keys, $config);
        }

        return $config;
    }

    /**
     * @param $value
     * @param $keys
     * @param array $config
     * @return array
     */
    private function processDirectives($value, $keys, $config = [])
    {
        $key = array_shift($keys);

        if (count($keys) == 0) {
            $config[$key] = $value;

            return $config;
        }

        if (!array_key_exists($key, $config)) {
            $config[$key] = array();
        }

        $config[$key] = $this->processDirectives($value, $keys, $config[$key]);
        return $config;
    }
}