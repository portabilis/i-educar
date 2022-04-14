<?php

namespace App\Support\View\Settings\Inputs;

class StringInput implements InputInterface
{
    public function getInputView($id, $description, $key, $value, $enabled, $hint)
    {
        return view('settings.string-input', [
            'id' => $id,
            'description' => $description,
            'key' => $key,
            'value' => $value,
            'enabled' => $enabled,
            'hint' => $hint,
        ]);
    }
}
