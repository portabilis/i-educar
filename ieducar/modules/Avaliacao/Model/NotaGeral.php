<?php

class Avaliacao_Model_NotaGeral extends Avaliacao_Model_Etapa
{
    protected $_data = [
    'notaAluno'               => null,
    'nota'                    => null,
    'notaArredondada'         => null
  ];

    protected $_dataTypes = [
    'nota' => 'numeric'
  ];

    protected $_references = [
    'notaAluno' => [
      'value' => null,
      'class' => 'Avaliacao_Model_NotaAluno',
      'file'  => 'Avaliacao/Model/NotaAluno.php'
    ]
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        // Aceita etapas de 0 a 10 + a string Rc
        $etapas = range(0, 10, 1) + ['Rc'];

        return [
      'nota' => new CoreExt_Validate_Numeric(['min' => 0, 'max' => 10]),
      'notaArredondada'  => new CoreExt_Validate_String(['max' => 5])
    ];
    }
}
