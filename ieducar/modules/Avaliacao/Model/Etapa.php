<?php

abstract class Avaliacao_Model_Etapa extends CoreExt_Entity
{
    public function __construct($options = [])
    {
        $this->_data['etapa'] = null;
        $this->setValidator('etapa', $this->_getEtapaValidator());

        parent::__construct($options);
    }

    protected function _getEtapaValidator()
    {
        // Aceita etapas de 0 a 10 + a string Rc
        $etapas = range(0, 10, 1) + ['Rc'];

        return (new CoreExt_Validate_Choice(['choices' => $etapas]));
    }
}
