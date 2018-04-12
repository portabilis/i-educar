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
require_once 'Usuario/Model/Funcionario.php';

/**
 * Usuario_Model_FuncionarioDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_FuncionarioDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Usuario_Model_Funcionario';
  protected $_tableName   = 'funcionario';
  protected $_tableSchema = 'portal';

  protected $_attributeMap = array(
    #'ref_cod_pessoa_fj' => 'ref_cod_pessoa_fj',
    #'matricula'         => 'matricula',
    #'email'             => 'email',
    #'data_troca_senha'  => 'data_troca_senha'
  );

  protected $_notPersistable = array(
    #'ippes'
  );

  protected $_primaryKey = array('ref_cod_pessoa_fj');

  protected function _getFindStatment($pkey)
  {
    if (!is_array($pkey))
      $pkey = array("ref_cod_pessoa_fj" => $pkey);

    return parent::_getFindStatment($pkey);
  }
}
