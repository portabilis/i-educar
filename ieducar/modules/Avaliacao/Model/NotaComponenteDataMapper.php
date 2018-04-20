<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/NotaComponente.php';

/**
 * Avaliacao_Model_NotaComponenteDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_NotaComponenteDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Avaliacao_Model_NotaComponente';
  protected $_tableName   = 'nota_componente_curricular';
  protected $_tableSchema = 'modules';

  protected $_primaryKey = array(
      'notaAluno'                 => 'nota_aluno_id',
      'componenteCurricular'      => 'componente_curricular_id',
      'etapa'                     => 'etapa',
  );

  protected $_attributeMap = array(
      'notaAluno'                 => 'nota_aluno_id',
      'componenteCurricular'      => 'componente_curricular_id',
      'etapa'                     => 'etapa',
      'nota'                      => 'nota',
      'notaArredondada'           => 'nota_arredondada',
      'notaRecuperacaoParalela'   => 'nota_recuperacao',
      'notaRecuperacaoEspecifica' => 'nota_recuperacao_especifica',
      'notaOriginal'              => 'nota_original'
  );
}
