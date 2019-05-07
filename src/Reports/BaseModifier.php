<?php

namespace iEducar\Reports;

abstract class BaseModifier
{
    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var array
     */
    protected $args;

    /**
     * @param string $templateName
     * @param array $args
     *
     * @return void
     */
    public function __construct($templateName, $args)
    {
        $this->templateName = $templateName;
        $this->args = $args;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    abstract public function modify($data);
}
