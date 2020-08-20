<?php

namespace App\Support\View;

use App\Support\View\Settings\Inputs\BooleanInput;
use App\Support\View\Settings\Inputs\FloatInput;
use App\Support\View\Settings\Inputs\IntegerInput;
use App\Support\View\Settings\Inputs\StringInput;
use App\Setting;

class SettingView
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
