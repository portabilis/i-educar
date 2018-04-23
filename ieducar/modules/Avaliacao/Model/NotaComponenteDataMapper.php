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
      'id'                        => 'id',
      'notaAluno'                 => 'nota_aluno_id',
      'componenteCurricular'      => 'componente_curricular_id',
      'nota'                      => 'nota',
      'notaArredondada'           => 'nota_arredondada',
      'etapa'                     => 'etapa',
      'notaRecuperacaoParalela'   => 'nota_recuperacao',
      'notaOriginal'              => 'nota_original',
      'notaRecuperacaoEspecifica' => 'nota_recuperacao_especifica'

  );
}
