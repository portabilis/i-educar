<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/Validate/Email.php';

/**
 * ComponenteCurricular_Model_Componente class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_Usuario extends CoreExt_Entity
{
  protected $_data = array(
    'id'               => NULL,
    'escolaId'         => NULL,
    'instituicaoId'    => NULL,
    'funcionarioCadId' => NULL,
    'funcionarioExcId' => NULL,
    'tipoUsuarioId'    => NULL,
    'dataCadastro'     => NULL,
    'dataExclusao'     => NULL,
    'ativo'            => NULL
  );

  /*protected $_dataTypes = array(
  );

  protected $_references = array(
  );*/

  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'Usuario/Model/UsuarioDataMapper.php';
      $this->setDataMapper(new Usuario_Model_UsuarioDataMapper());
    }
    return parent::getDataMapper();
  }

  public function getDefaultValidatorCollection()
  {
    return array();
  }

  // TODO remover metodo? já que foi usado $_attributeMap id
  protected function _createIdentityField()
  {
    $id = array('id' => NULL);
    $this->_data = array_merge($id, $this->_data);
    return $this;
  }
}
