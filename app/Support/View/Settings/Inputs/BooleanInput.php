<?php

namespace App\Support\View\Settings\Inputs;

class BooleanInput implements InputInterface
{
    public function getInputView($id, $description, $key, $value, $enabled, $hint)
    {
        return view('settings.boolean-input', [
            'id' => $id,
            'description' => $description,
            'key' => $key,
            'value' => $value,
            'enabled' => $enabled,
            'hint' => $hint,
        ]);
    }
}
