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

require_once 'Core/Controller/Page/ViewController.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

/**
 * ViewController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class ViewController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper = 'Docente_Model_LicenciaturaDataMapper';
  protected $_titulo     = 'Detalhes da licenciatura';
  protected $_processoAp = 635;
  protected $_tableMap   = array(
    'Licenciatura'     => 'licenciatura',
    'Curso'            => 'curso',
    'Ano de conclusão' => 'anoConclusao',
    'IES'              => 'ies'
  );

  public function setUrlEditar(CoreExt_Entity $entry)
  {
    $this->url_editar = CoreExt_View_Helper_UrlHelper::url(
      'edit', array('query' => array(
        'id'          => $entry->id,
        'servidor'    => $entry->servidor,
        'instituicao' => $this->getRequest()->instituicao
      ))
    );
  }

  public function setUrlCancelar(CoreExt_Entity $entry)
  {
    $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url(
      'index', array('query' => array(
        'id'          => $entry->id,
        'servidor'    => $entry->servidor,
        'instituicao' => $this->getRequest()->instituicao
      ))
    );
  }
}