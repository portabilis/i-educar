<?php

namespace iEducar\Modules\Config;

interface ConfigInterface
{
    public function __construct($enviroment, $tenant = null);
    public function getArrayConfig();
}