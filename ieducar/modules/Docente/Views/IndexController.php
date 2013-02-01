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
 * @package     Docente
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/ListController.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

/**
 * IndexController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class IndexController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Docente_Model_LicenciaturaDataMapper';
  protected $_titulo     = 'Listagem de licenciaturas do servidor';
  protected $_processoAp = 635;

  protected $_tableMap = array(
    'Licenciatura'     => 'licenciatura',
    'Curso'            => 'curso',
    'Ano de conclusão' => 'anoConclusao',
    'IES'              => 'ies'
  );

  public function getEntries()
  {
    return $this->getDataMapper()->findAll(
      array(), array('servidor' => $this->getRequest()->servidor), array('anoConclusao' => 'ASC')
    );
  }

  public function setAcao()
  {
    $this->acao = sprintf(
      'go("edit?servidor=%d&instituicao=%d")',
      $this->getRequest()->servidor, $this->getRequest()->instituicao
    );

    $this->nome_acao = 'Novo';
  }

  public function Gerar()
  {
    $headers = $this->getTableMap();

    $this->addCabecalhos(array_keys($headers));

    $entries = $this->getEntries();

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite
      : 0;

    foreach ($entries as $entry) {
      $item = array();
      $data = $entry->toArray();
      $options = array('query' => array(
        'id'          => $entry->id,
        'servidor'    => $entry->servidor,
        'instituicao' => $this->getRequest()->instituicao
      ));

      foreach ($headers as $label => $attr) {
        $item[] = CoreExt_View_Helper_UrlHelper::l(
          $entry->$attr, 'view', $options
        );
      }

      $this->addLinhas($item);
    }

    $this->addPaginador2("", count($entries), $_GET, $this->nome, $this->limite);

    $this->setAcao();

    $this->acao_voltar = sprintf(
      'go("/intranet/educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d")',
      $this->getRequest()->servidor, $this->getRequest()->instituicao
    );

    $this->largura = "100%";
  }
}