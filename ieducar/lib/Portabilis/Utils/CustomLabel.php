<?php

use App\Models\LegacyGeneralConfiguration;

class CustomLabel
{
    protected static $instance;

    protected $defaults;

    public function __construct()
    {
        $this->defaults = $this->getFromDatabase();
    }

    public function customize($key)
    {
        if (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }

        return $key;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    protected function getFromDatabase()
    {
        $customLabels = LegacyGeneralConfiguration::query()
            ->forActiveInstitution()
            ->value('custom_labels');

        return !empty($customLabels) ? json_decode($customLabels, true) : $customLabels;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CustomLabel;
        }

        return self::$instance;
    }
}
