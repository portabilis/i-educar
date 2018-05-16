<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteMedia.php';

/**
 * Avaliacao_Model_NotaComponenteMediaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_NotaComponenteMediaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Avaliacao_Model_NotaComponenteMedia';
  protected $_tableName   = 'nota_componente_curricular_media';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'notaAluno'            => 'nota_aluno_id',
    'componenteCurricular' => 'componente_curricular_id',
    'mediaArredondada'     => 'media_arredondada'
  );

  protected $_primaryKey = array(
    'notaAluno'             => 'nota_aluno_id',
    'componenteCurricular'  => 'componente_curricular_id'
  );
}
