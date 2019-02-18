<?php

trait Avaliacao_Service_Boletim_Validators
{
    /**
     * Validadores para instâncias de Avaliacao_Model_FaltaAbstract e
     * Avaliacao_Model_NotaComponente.
     *
     * @see Avaliacao_Service_Boletim::_addValidators()
     *
     * @var array
     */
    protected $_validators;

    /**
     * @return array
     */
    public function getValidators()
    {
        return $this->_validators;
    }

    /**
     * @param array $validators
     *
     * @return $this
     */
    public function setValidators(array $validators)
    {
        $this->_validators = $validators;

        return $this;
    }
}
