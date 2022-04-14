<?php

class Avaliacao_Model_ParecerDescritivoAluno extends CoreExt_Entity
{
    protected $_data = [
    'matricula'         => null,
    'parecerDescritivo' => null
  ];

    protected $_references = [
    'parecerDescritivo' => [
      'value' => null,
      'class' => 'RegraAvaliacao_Model_TipoParecerDescritivo',
      'file'  => 'RegraAvaliacao/Model/TipoParecerDescritivo.php'
    ]
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        $parecer = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();

        return [
      'matricula'         => new CoreExt_Validate_Numeric(['min' => 0]),
      'parecerDescritivo' => new CoreExt_Validate_Choice(['choices' => $parecer->getKeys()]),
    ];
    }
}
