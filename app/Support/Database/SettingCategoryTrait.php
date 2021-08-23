<?php

declare(strict_types=1);

namespace App\Support\Database;

use App\SettingCategory;

trait SettingCategoryTrait
{
    public $settingsCattegories = [];

    public function __construct()
    {
        $this->getAllSettingsCategories();
    }

    private function getAllSettingsCategories()
    {
        $this->settingsCattegories = SettingCategory::get();
    }

    private function getSettingCategoryIdByName($name): int
    {
        $id = 0;
        foreach ($this->settingsCattegories as $settingCategory) {
            if ($settingCategory->name === $name) {
                $id = $settingCategory->id;
            }
        }

        return $id;
    }
}
