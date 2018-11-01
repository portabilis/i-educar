<?php

namespace iEducar\Support\Config;

use iEducar\Modules\Config\CoreConfig;
use iEducar\Modules\Config\IniConfig;

class LegacyConfig
{
    private $config;

    public function __construct($legacyConfigPath, $enviroment = 'development', $tenant = null)
    {
        $this->config['legacy'] = $this->loadConfig($legacyConfigPath, $enviroment, $tenant);
    }

    public function getArrayConfig()
    {
        return $this->config;
    }

    private function loadConfig($legacyConfigPath, $enviroment, $tenant)
    {
        $configFile = $legacyConfigPath . $enviroment . '.ini';

        if (!file_exists($configFile)) {
            $configFile = $legacyConfigPath . '/ieducar.ini';
        }

        $configObject = new IniConfig($configFile, $enviroment);

        if ($configObject->hasEnviromentSection($tenant)) {
            $configObject->changeEnviroment($tenant);
        } else {
            if (!$configObject->hasEnviromentSection($tenant) && $enviroment !== "development") {
                $configObject->app->ambiente_inexistente = true;
            }
        }

        $configArray = $configObject->toArray();

        return $this->handleConfigArray($configArray);
    }

    private function handleConfigArray($configArray)
    {
        $configResult = [];
        foreach ($configArray as $key => $config) {
            if (is_array($config)) {
                $configResult[$key] = $config;
                continue;
            }

            if ($config instanceof CoreConfig) {
                $configResult[$key] = $this->handleConfigArray($config->toArray());
                continue;
            }

            $configResult[$key] = $config;
        }

        return $configResult;
    }
}