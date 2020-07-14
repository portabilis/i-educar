<?php

namespace App\Support\View\Settings\Inputs;

class IntegerInput implements InputInterface
{
    public function getInputView($id, $description, $key, $value)
    {
        return view('settings.integer-input', [
            'id' => $id,
            'description' => $description,
            'key' => $key,
            'value' => $value,
        ]);
    }
}
