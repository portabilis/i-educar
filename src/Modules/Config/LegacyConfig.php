<?php

namespace iEducar\Modules\Config;

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

    private function loadConfig()
    {
        $legacyPath = base_path() . '/' . config('legacy.path');

        require_once $legacyPath . '/lib/CoreExt/Config.class.php';
        require_once $legacyPath . '/lib/CoreExt/Config/Ini.class.php';
        require_once $legacyPath . '/lib/CoreExt/Locale.php';

        if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
            define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
        } else {
            define('CORE_EXT_CONFIGURATION_ENV', 'production');
        }

        $configFile = $legacyPath . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

        if (!file_exists($configFile)) {
            $configFile = $legacyPath . '/configuration/ieducar.ini';
        }

        $locale = \CoreExt_Locale::getInstance();
        $locale->setCulture('pt_BR')->setLocale();

        $configObject = new \CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);

        date_default_timezone_set($configObject->app->locale->timezone);

        $tenantEnv = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

        if ($configObject->hasEnviromentSection($tenantEnv)) {
            $configObject->changeEnviroment($tenantEnv);
        } else {
            if (!$configObject->hasEnviromentSection($tenantEnv) && CORE_EXT_CONFIGURATION_ENV !== "development") {
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