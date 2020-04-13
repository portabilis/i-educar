<?php

namespace App\Contracts;

interface Output
{
    public function progressAdvance();

    public function info($message);
}
