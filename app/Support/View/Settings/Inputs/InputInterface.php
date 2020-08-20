<?php

namespace App\Support\View\Settings\Inputs;

interface InputInterface
{
    public function getInputView($id, $description, $key, $value);
}
