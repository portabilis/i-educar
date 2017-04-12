<?php

/**
 * i-Educar - Sistema de gestדo escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaם
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa י software livre; vocך pode redistribuם-lo e/ou modificב-lo
 * sob os termos da Licenחa Pתblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versדo 2 da Licenחa, como (a seu critיrio)
 * qualquer versדo posterior.
 *
 * Este programa י distribuם­do na expectativa de que seja תtil, porיm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implם­cita de COMERCIABILIDADE OU
 * ADEQUAֳַO A UMA FINALIDADE ESPECֽFICA. Consulte a Licenחa Pתblica Geral
 * do GNU para mais detalhes.
 *
 * Vocך deve ter recebido uma cףpia da Licenחa Pתblica Geral do GNU junto
 * com este programa; se nדo, escreva para a Free Software Foundation, Inc., no
 * endereחo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Arquivo disponםvel desde a versדo 1.1.0
 * @version   $Id$
 */

require_once 'Core/View/Tabulable.php';
require_once 'include/clsListagem.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * Core_Controller_Page_ListController abstract class.
 *
 * Provך um controller padrדo para listagem de registros.
 *
 * @author    Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe disponםvel desde a versדo 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_ListController extends clsListagem implements Core_View_Tabulable
{
  /**
   * Mapeia um nome descritivo a um atributo de CoreExt_Entity retornado pela
   * instגncia CoreExt_DataMapper retornada por getDataMapper().
   *
   * Para uma instגncia de CoreExt_Entity que tenha os seguintes atributos:
   * <code>
   * <?php
   * $_data = array(
   *   'nome' => NULL
   *   'idade' => NULL,
   *   'data_validacao' => NULL
   * );
   * </code>
   *
   * O mapeamento poderia ser feito da seguinte forma:
   * <code>
   * <?php
   * $_tableMap = array(
   *   'Nome' => 'nome',
   *   'Idade (anos)' => 'idade'
   * );
   * </code>
   *
   * Se um atributo nדo for mapeado, ele nדo serב exibido por padrדo durante
   * a geraחדo de HTML na execuחדo do mיtodo Gerar().
   *
   * @var array
   */
  protected $_tableMap = array();

  /**
   * Getter.
   * @see Core_View_Tabulable#getTableMap()
   */
  public function getTableMap()
  {
    return $this->_tableMap;
  }

  /**
   * Retorna os registros a serem exibidos na listagem.
   *
   * Subclasses devem sobrescrever este mיtodo quando os parגmetros para
   * CoreExt_DataMapper::findAll forem mais especםficos.
   *
   * @return array (int => CoreExt_Entity)
   */
  public function getEntries()
  {
    $mapper = $this->getDataMapper();
    return $mapper->findAll();
  }

  /**
   * Configura o botדo de aחדo padrדo para a criaחדo de novo registro.
   */
  public function setAcao()
  {
    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra($this->_processoAp, $this->getPessoaLogada(),7,null,true))
    {
      $this->acao = 'go("edit")';
      $this->nome_acao = 'Novo';
    }
  }

  protected function getPessoaLogada(){
    return $_SESSION['id_pessoa'];
  }

  /**
   * Implementaחדo padrדo para as subclasses que estenderem essa classe. Cria
   * uma lista de apresentaחדo de dados simples utilizando o mapeamento de
   * $_tableMap.
   *
   * @see Core_Controller_Page_ListController#$_tableMap
   * @see clsDetalhe#Gerar()
   */
  public function Gerar()
  {
    $headers = $this->getTableMap();

    // Configura o cabeחalho da listagem.
    $this->addCabecalhos(array_keys($headers));

    // Recupera os registros para a listagem.
    $entries = $this->getEntries();

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite
      : 0;

    foreach ($entries as $entry) {
      $item = array();
      $data = $entry->toArray();
      $options = array('query' => array('id' => $entry->id));

      foreach ($headers as $label => $attr) {
        $item[] = CoreExt_View_Helper_UrlHelper::l(
          $entry->$attr, 'view', $options
        );
      }

      $this->addLinhas($item);
    }

    $this->addPaginador2('', count($entries), $_GET, $this->nome, $this->limite);

    // Configura o botדo padrדo de aחדo para a criaחדo de novo registro.
    $this->setAcao();

    // Largura da tabela HTML onde se encontra a listagem.
    $this->largura = '100%';
  }
}
