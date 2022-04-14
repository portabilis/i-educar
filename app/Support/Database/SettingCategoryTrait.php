<?php

declare(strict_types=1);

namespace App\Support\Database;

use App\SettingCategory;

trait SettingCategoryTrait
{
    private function getSettingCategoryIdByName(string $name): int
    {
        $settingsCattegories = SettingCategory::query()->get();
        $id = $settingsCattegories->first()->id;

        foreach ($settingsCattegories as $settingCategory) {
            if ($settingCategory->name === $name) {
                $id = $settingCategory->id;
            }
        }

        return $id;
    }
}
