<?php

namespace App\Services;

use App\Services\Settings\Inputs\BooleanInput;
use App\Services\Settings\Inputs\FloatInput;
use App\Services\Settings\Inputs\IntegerInput;
use App\Services\Settings\Inputs\StringInput;
use App\Setting;

class SettingService
{
    public function makeInput($id, $description, $type, $key, $value)
    {
        return $this->getInput($type)->getInputView($id, $description, $key, $value);
    }

    private function getInput($type)
    {
        $types = [
            Setting::TYPE_STRING => (new StringInput),
            Setting::TYPE_INTEGER => (new IntegerInput),
            Setting::TYPE_FLOAT => (new FloatInput),
            Setting::TYPE_BOOLEAN => (new BooleanInput),
        ];

        return $types[$type] ?? (new StringInput);
    }
}
