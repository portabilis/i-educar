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
require_once 'include/clsDetalhe.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * Core_Controller_Page_ViewController abstract class.
 *
 * Provê um controller padrão para a visualização de um registro.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_ViewController extends clsDetalhe implements Core_View_Tabulable
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
   * Construtor.
   * @todo Criar interface de hooks semelhante ao controller Edit.
   */
  public function __construct()
  {
    $this->titulo  = $this->getBaseTitulo();
    $this->largura = "100%";
  }

  /**
   * Getter.
   * @see Core_View_Tabulable#getTableMap()
   */
  public function getTableMap()
  {
    return $this->_tableMap;
  }

  /**
   * Configura a URL padrão para a ação de Edição de um registro.
   *
   * Por padrão, cria uma URL "edit/id", onde id é o valor do atributo "id"
   * de uma instância CoreExt_Entity.
   *
   * @param CoreExt_Entity $entry A instância atual recuperada
   *   ViewController::Gerar().
   */
  public function setUrlEditar(CoreExt_Entity $entry)
  {
    $this->url_editar = CoreExt_View_Helper_UrlHelper::url(
      'edit', array('query' => array('id' => $entry->id))
    );
  }

  /**
   * Configura a URL padrão para a ação Cancelar da tela de Edição de um
   * registro.
   *
   * Por padrão, cria uma URL "index".
   *
   * @param CoreExt_Entity $entry A instância atual recuperada
   *   ViewController::Gerar().
   */
  public function setUrlCancelar(CoreExt_Entity $entry)
  {
    $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url('index');
  }

  /**
   * Implementação padrão para as subclasses que estenderem essa classe. Cria
   * uma tela de apresentação de dados simples utilizando o mapeamento de
   * $_tableMap.
   *
   * @see Core_Controller_Page_ViewController#$_tableMap
   * @see clsDetalhe#Gerar()
   */
  public function Gerar()
  {
    $headers = $this->getTableMap();
    $mapper  = $this->getDataMapper();

    $this->titulo  = $this->getBaseTitulo();
    $this->largura = "100%";

    try {
      $entry = $mapper->find($this->getRequest()->id);
    } catch (Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }

    foreach ($headers as $label => $attr) {
      $value = $entry->$attr;
      if (!is_null($value)) {
        $this->addDetalhe(array($label, $value));
      }
    }

    $this->setUrlEditar($entry);
    $this->setUrlCancelar($entry);
  }
}