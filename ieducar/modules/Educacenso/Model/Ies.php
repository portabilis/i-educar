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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Educacenso_Model_Ies class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_Ies extends CoreExt_Entity
{
  protected $_data = array(
    'ies'                       => NULL,
    'nome'                      => NULL,
    'dependenciaAdministrativa' => NULL,
    'tipoInstituicao'           => NULL,
    'uf'                        => NULL,
    'user'                      => NULL,
    'created_at'                => NULL,
    'updated_at'                => NULL
  );

  public function getDefaultValidatorCollection()
  {
    return array(
      'ies'                       => new CoreExt_Validate_Numeric(array('min' => 0)),
      'nome'                      => new CoreExt_Validate_String(array('min' => 1)),
      'dependenciaAdministrativa' => new CoreExt_Validate_Numeric(array('min' => 0)),
      'tipoInstituicao'           => new CoreExt_Validate_Numeric(array('min' => 0)),
      'uf'                        => new CoreExt_Validate_String(array('required' => FALSE)),
      'user'                      => new CoreExt_Validate_Numeric(array('min' => 0))
    );
  }

  public function __toString()
  {
    return $this->nome;
  }
}