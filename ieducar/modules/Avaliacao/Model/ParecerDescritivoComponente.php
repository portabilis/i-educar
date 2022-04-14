<?php

class Avaliacao_Model_ParecerDescritivoComponente extends Avaliacao_Model_ParecerDescritivoAbstract
{
    /**
     * Construtor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->_data['componenteCurricular'] = null;

        $this->_references['componenteCurricular'] = [
      'value' => null,
      'class' => 'ComponenteCurricular_Model_Componente',
      'file'  => 'ComponenteCurricular/Model/Componente.php'
    ];

        parent::__construct($options);
    }
}
