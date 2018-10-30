<?php

namespace iEducar\Support\Config;

interface ConfigInterface
{
    public function __construct($enviroment, $tenant = null);
    public function getArrayConfig();
}