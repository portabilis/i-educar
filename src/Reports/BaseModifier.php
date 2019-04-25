<?php

namespace iEducar\Reports;

abstract class BaseModifier
{
    protected $templateName;

    protected $args;

    public function __construct($templateName, $args)
    {
        $this->templateName = $templateName;
        $this->args = $args;
    }

    abstract public function modify($data);
}
