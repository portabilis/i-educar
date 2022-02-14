<?php

abstract class Avaliacao_Model_ParecerDescritivoAbstract extends Avaliacao_Model_Etapa implements \Stringable
{
    protected $_data = [
    'parecerDescritivoAluno' => null,
    'parecer'                => null
  ];

    protected $_references = [
    'parecerDescritivoAluno' => [
      'value' => null,
      'class' => 'Avaliacao_Model_ParecerDescritivoAluno',
      'file'  => 'Avaliacao/Model/ParecerDescritivoAluno.php'
    ]
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        $etapa  = $this->getValidator('etapa');
        $etapas = $etapa->getOption('choices');
        $etapas[] = 'An';

        $etapa->setOptions(['choices' => $etapas]);

        return [
      'etapa'   => $etapa,
      'parecer' => new CoreExt_Validate_String()
    ];
    }

    /**
     * Implementa método mágico __toString().
     *
     * @link http://br.php.net/__toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->parecer;
    }
}
