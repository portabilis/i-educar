<?php

class Avaliacao_Model_ParecerDescritivoGeralDataMapper extends Avaliacao_Model_ParecerDescritivoAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_ParecerDescritivoGeral';
    protected $_tableName   = 'parecer_geral';

    protected $_primaryKey = [
      'parecerDescritivoAluno' => 'parecer_aluno_id',
      'etapa' => 'etapa'
  ];
}
