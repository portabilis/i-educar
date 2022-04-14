<?php

class Avaliacao_Model_FaltaAluno extends CoreExt_Entity
{
    protected $_data = [
    'matricula' => null,
    'tipoFalta' => null
  ];

    protected $_references = [
    'tipoFalta' => [
      'value' => null,
      'class' => 'RegraAvaliacao_Model_TipoPresenca',
      'file'  => 'RegraAvaliacao/Model/TipoPresenca.php'
    ]
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        $presenca = RegraAvaliacao_Model_TipoPresenca::getInstance();

        return [
      'matricula' => new CoreExt_Validate_Numeric(['min' => 0]),
      'tipoFalta' => new CoreExt_Validate_Choice(['choices' => $presenca->getKeys()]),
    ];
    }
}
