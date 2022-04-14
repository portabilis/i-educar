<?php

interface Core_Controller_Page_Validatable
{
    /**
     * Retorna um array com objetos CoreExt_Validate.
     *
     * @return array
     */
    public function getValidators();
}
