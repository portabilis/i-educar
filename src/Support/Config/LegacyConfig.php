<?php

namespace iEducar\Support\Config;

class LegacyConfig implements ConfigInterface
{

    private $config;

    public function __construct($enviroment, $tenant = null)
    {
        $this->config['legacy'] = $this->loadConfig($enviroment, $tenant);
    }

    public function getArrayConfig()
    {
        return $this->config;
    }

    private function loadConfig($enviroment, $tenant)
    {
        $legacyPath = base_path() . '/' . config('legacy.path');

        require_once $legacyPath . '/lib/CoreExt/Config.class.php';
        require_once $legacyPath . '/lib/CoreExt/Config/Ini.class.php';
        require_once $legacyPath . '/lib/CoreExt/Locale.php';

        $configFile = $legacyPath . '/configuration/' . $enviroment . '.ini';

        if (!file_exists($configFile)) {
            $configFile = $legacyPath . '/configuration/ieducar.ini';
        }

        $locale = \CoreExt_Locale::getInstance();
        $locale->setCulture('pt_BR')->setLocale();

        $configObject = new \CoreExt_Config_Ini($configFile, $enviroment);

        date_default_timezone_set($configObject->app->locale->timezone);

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

            if ($config instanceof \CoreExt_Config) {
                $configResult[$key] = $this->handleConfigArray($config->toArray());
                continue;
            }

            $configResult[$key] = $config;
        }

        return $configResult;
    }
}