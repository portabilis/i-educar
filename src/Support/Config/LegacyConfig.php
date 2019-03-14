<?php

namespace iEducar\Support\Config;

use Exception;
use iEducar\Modules\Config\CoreConfig;
use iEducar\Modules\Config\IniConfig;

class LegacyConfig
{
    private $config;

    /**
     * @param $legacyConfigPath
     * @param string $enviroment
     * @param null $tenant
     */
    public function __construct($legacyConfigPath, $enviroment = 'local', $tenant = null)
    {
        $this->config = $this->loadConfig($legacyConfigPath, $enviroment, $tenant);
    }

    /**
     * @return array
     */
    public function getArrayConfig()
    {
        return $this->config;
    }

    /**
     * @param $legacyConfigPath
     * @param $enviroment
     * @param $tenant
     * @return array
     * @throws Exception
     */

    private function loadConfig($legacyConfigPath, $enviroment, $tenant)
    {
        $configFile = $legacyConfigPath . $enviroment . '.ini';

        if (!file_exists($configFile)) {
            $configFile = $legacyConfigPath . '/ieducar.ini';

            if (!file_exists($configFile)) {
                throw new Exception("Config file [{$configFile}] not found");
            }
        }

        $configObject = new IniConfig($configFile, $enviroment);

        if ($configObject->hasEnviromentSection($tenant)) {
            $configObject->changeEnviroment($tenant);
        } elseif (!$configObject->hasEnviromentSection($tenant) && $enviroment !== "local") {
                $configObject->app->ambiente_inexistente = true;
        }

        $configArray = $configObject->toArray();

        return $this->handleConfigArray($configArray);
    }

    /**
     * @param $configArray
     * @return array
     */
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
