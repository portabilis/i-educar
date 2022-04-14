<?php

namespace App\Support\View;

use App\Setting;
use App\Support\View\Settings\Inputs\BooleanInput;
use App\Support\View\Settings\Inputs\FloatInput;
use App\Support\View\Settings\Inputs\IntegerInput;
use App\Support\View\Settings\Inputs\StringInput;

class SettingView
{
    public function makeInput($id, $description, $type, $key, $value, $enabled, $hint)
    {
        return $this->getInput($type)->getInputView($id, $description, $key, $value, $enabled, $hint);
    }

    private function getInput($type)
    {
        $types = [
            Setting::TYPE_STRING => (new StringInput()),
            Setting::TYPE_INTEGER => (new IntegerInput()),
            Setting::TYPE_FLOAT => (new FloatInput()),
            Setting::TYPE_BOOLEAN => (new BooleanInput()),
        ];

        return $types[$type] ?? (new StringInput());
    }
}
