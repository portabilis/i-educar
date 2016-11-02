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
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * ComponenteCurricular_Model_Turma class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_Turma extends CoreExt_Entity
{
  protected $_data = array(
    'componenteCurricular' => NULL,
    'anoEscolar'           => NULL,
    'escola'               => NULL,
    'turma'                => NULL,
    'cargaHoraria'         => NULL
  );

  protected $_dataTypes = array(
    'cargaHoraria' => 'numeric'
  );

  protected $_references = array(
    'componenteCurricular' => array(
      'value' => NULL,
      'class' => 'ComponenteCurricular_Model_ComponenteDataMapper',
      'file'  => 'ComponenteCurricular/Model/ComponenteDataMapper.php'
    )
  );

  /**
   * Construtor. Remove o campo identidade já que usa uma chave composta.
   * @see CoreExt_Entity#__construct($options = array())
   */
  public function __construct($options = array()) {
    parent::__construct($options);
    unset($this->_data['id']);
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    return array(
      'cargaHoraria' => new CoreExt_Validate_Numeric(array('required' => FALSE))
    );
  }
}