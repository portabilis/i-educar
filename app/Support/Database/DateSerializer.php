<?php

namespace App\Support\Database;

use DateTimeInterface;

trait DateSerializer
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
