<?php

namespace App\Services\Settings\Inputs;

class FloatInput implements InputInterface
{
    public function getInputView($id, $description, $key, $value)
    {
        return view('settings.float-input', [
            'id' => $id,
            'description' => $description,
            'key' => $key,
            'value' => $value,
        ]);
    }
}
