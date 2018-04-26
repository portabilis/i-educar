<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'Usuario/Model/Usuario.php';

/**
 * Usuario_Model_UsuarioDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_UsuarioDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Usuario_Model_Usuario';
  protected $_tableName   = 'usuario';
  protected $_tableSchema = 'pmieducar';

  protected $_attributeMap = array(
    'id'               => 'cod_usuario',
    'escolaId'         => 'ref_cod_escola',
    'instituicaoId'    => 'ref_cod_instituicao',
    'funcionarioCadId' => 'ref_funcionario_cad',
    'funcionarioExcId' => 'ref_funcionario_exc',
    'tipoUsuarioId'    => 'ref_cod_tipo_usuario',
    'dataCadastro'     => 'data_cadastro',
    'dataExclusao'     => 'data_exclusao',
    'ativo'            => 'ativo'
  );

  protected $_notPersistable = array(
  );

  // TODO remover? já que foi usado $_attributeMap id
  protected $_primaryKey = array('id');

  // TODO remover metodo? já que foi usado $_attributeMap id
  protected function _getFindStatment($pkey)
  {
    if (!is_array($pkey))
      $pkey = array("id" => $pkey);

    return parent::_getFindStatment($pkey);
  }
}
