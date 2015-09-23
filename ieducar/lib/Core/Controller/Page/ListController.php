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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'Core/View/Tabulable.php';
require_once 'include/clsListagem.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * Core_Controller_Page_ListController abstract class.
 *
 * Provê um controller padrão para listagem de registros.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_ListController extends clsListagem implements Core_View_Tabulable
{
  /**
   * Mapeia um nome descritivo a um atributo de CoreExt_Entity retornado pela
   * instância CoreExt_DataMapper retornada por getDataMapper().
   *
   * Para uma instância de CoreExt_Entity que tenha os seguintes atributos:
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
   * Se um atributo não for mapeado, ele não será exibido por padrão durante
   * a geração de HTML na execução do método Gerar().
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
   * Subclasses devem sobrescrever este método quando os parâmetros para
   * CoreExt_DataMapper::findAll forem mais específicos.
   *
   * @return array (int => CoreExt_Entity)
   */
  public function getEntries()
  {
    $mapper = $this->getDataMapper();
    return $mapper->findAll();
  }

  /**
   * Configura o botão de ação padrão para a criação de novo registro.
   */
  public function setAcao()
  {
    $this->acao = 'go("edit")';
    $this->nome_acao = 'Novo';
  }

  /**
   * Implementação padrão para as subclasses que estenderem essa classe. Cria
   * uma lista de apresentação de dados simples utilizando o mapeamento de
   * $_tableMap.
   *
   * @see Core_Controller_Page_ListController#$_tableMap
   * @see clsDetalhe#Gerar()
   */
  public function Gerar()
  {
    $headers = $this->getTableMap();

    // Configura o cabeçalho da listagem.
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

    // Configura o botão padrão de ação para a criação de novo registro.
    $this->setAcao();

    // Largura da tabela HTML onde se encontra a listagem.
    $this->largura = '100%';
  }
}