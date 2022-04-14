<?php

/**
 * Class Avaliacao_Model_NotaComponente
 */
class Avaliacao_Model_NotaComponente extends Avaliacao_Model_Etapa
{
    protected $_data = [
        'notaAluno'               => null,
        'componenteCurricular'    => null,
        'nota'                    => null,
        'notaArredondada'         => null,
        'notaRecuperacaoParalela' => null,
        'notaRecuperacaoEspecifica' => null,
        'notaOriginal'            => null
    ];

    protected $_dataTypes = [
        'nota' => 'numeric'
    ];

    protected $_references = [
        'notaAluno' => [
            'value' => null,
            'class' => 'Avaliacao_Model_NotaAluno',
            'file'  => 'Avaliacao/Model/NotaAluno.php'
        ],
        'componenteCurricular' => [
            'value' => null,
            'class' => 'ComponenteCurricular_Model_Componente',
            'file'  => 'ComponenteCurricular/Model/Componente.php'
        ]
  ];

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
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
