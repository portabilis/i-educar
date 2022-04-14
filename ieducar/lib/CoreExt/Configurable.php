<?php

interface CoreExt_Configurable
{
    /**
     * Setter.
     *
     * @param array $options
     *
     * @return CoreExt_Configurable Provê interface fluída
     */
    public function setOptions(array $options = []);

    /**
     * Getter.
     *
     * @return array
     */
    public function getOptions();
}
