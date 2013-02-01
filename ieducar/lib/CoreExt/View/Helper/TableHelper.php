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
 * @package   CoreExt_View
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/View/Helper/Abstract.php';

/**
 * CoreExt_View_Helper_TableHelper class.
 *
 * Helper para a criação de tabelas HTML através de arrays associativos.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_View
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_View_Helper_TableHelper extends CoreExt_View_Helper_Abstract
{
  protected $_header = array();
  protected $_body   = array();
  protected $_footer = array();

  /**
   * Construtor singleton.
   */
  protected function __construct()
  {
  }

  /**
   * Retorna uma instância singleton.
   * @return CoreExt_View_Helper_Abstract
   */
  public static function getInstance()
  {
    $instance = self::_getInstance(__CLASS__);
    $instance->resetTable();
    return $instance;
  }

  /**
   * Reseta os dados da tabela.
   * @return CoreExt_View_Helper_TableHelper Provê interface fluída
   */
  public function resetTable()
  {
    $this->_header = array();
    $this->_body   = array();
    $this->_footer = array();
    return $this;
  }

  /**
   * Adiciona um array para um row de tabela para a tag cabeçalho (thead). Cada
   * valor do array precisa ser um array associativo, podendo conter os
   * seguintes atributos:
   *
   * - data: o valor que será impresso na célula (td) da tabela
   * - colspan: valor inteiro para colspan {@link }
   * - attributes: array associativo onde o nome do atributo é o índice
   *
   * <code>
   * $table = CoreExt_View_Helper_TableHelper::getInstance();
   *
   * $data = array(
   *   array('data' => 'Example 1', 'colspan' => 1),
   *   array('data' => 'Example 2', 'attributes' => array(
   *     'class' => 'tdd1', 'style' => 'border: 1px dashed green'
   *   ))
   * );
   *
   * $table->addHeaderRow($data);
   * print $table;
   *
   * // <table>
   * //   <thead>
   * //     <tr>
   * //       <td colspan="1">Example 1</td>
   * //       <td class="tdd1" style="border: 1px dashed green">Example 2</td>
   * //     </tr>
   * //   </thead>
   * </code>
   *
   * @param array $cols
   * @return CoreExt_View_Helper_TableHelper Provê interface fluída
   */
  public function addHeaderRow(array $cols, array $rowAttributes = array())
  {
    $this->_header[] = array('row' => $cols, 'attributes' => $rowAttributes);
    return $this;
  }

  /**
   * Adiciona um array para um row de tabela para a tag corpo (tbody).
   *
   * @param $cols
   * @return CoreExt_View_Helper_TableHelper Provê interface fluída
   * @see CoreExt_View_Helper_TableHelper::addHeaderRow(array $cols)
   */
  public function addBodyRow(array $cols, array $rowAttributes = array())
  {
    $this->_body[] = array('row' => $cols, 'attributes' => $rowAttributes);
    return $this;
  }

  /**
   * Adiciona um array para um row de tabela para a tag rodapé (tfooter).
   *
   * @param $cols
   * @return CoreExt_View_Helper_TableHelper Provê interface fluída
   * @see CoreExt_View_Helper_TableHelper::addHeaderRow(array $cols)
   */
  public function addFooterRow(array $cols, array $rowAttributes = array())
  {
    $this->_footer[] = array('row' => $cols, 'attributes' => $rowAttributes);
    return $this;
  }

  /**
   * Cria uma tabela HTML usando os valores passados para os métodos add*().
   *
   * @param array $tableAttributes
   * @return string
   */
  public function createTable(array $tableAttributes = array())
  {
    return sprintf(
      '<table%s>%s%s%s%s%s</table>',
      $this->_attributes($tableAttributes),
      PHP_EOL,
      $this->createHeader(TRUE),
      $this->createBody(TRUE),
      $this->createFooter(TRUE),
      PHP_EOL
    );
  }

  /**
   * Cria a seção thead de uma tabela.
   * @param bool $indent
   * @return string
   */
  public function createHeader($indent = FALSE)
  {
    return $this->_createHtml($this->_getTag('thead', $indent), $this->_header, $indent);
  }

  /**
   * Cria a seção tbody de uma tabela.
   * @param bool $indent
   * @return string
   */
  public function createBody($indent = FALSE)
  {
    return $this->_createHtml($this->_getTag('tbody', $indent), $this->_body, $indent);
  }

  /**
   * Cria a seção tfooter de uma tabela.
   * @param bool $indent
   * @return string
   */
  public function createFooter($indent = FALSE)
  {
    return $this->_createHtml($this->_getTag('tfooter', $indent), $this->_footer, $indent);
  }

  /**
   * Formata uma string para o uso de _createHtml().
   *
   * @param string $name
   * @param bool $indent
   * @return string
   */
  protected function _getTag($name, $indent = TRUE)
  {
    $indent = $indent ? '  ' : '';
    return sprintf('%s<%s>%s%s%s</%s>', $indent, $name, '%s', '%s', $indent, $name);
  }

  /**
   * Cria código Html de um row de tabela.
   *
   * @param string $closure
   * @param array $rows
   * @param bool $indent
   * @return string
   */
  protected function _createHtml($closure, $rows = array(), $indent = FALSE)
  {
    $html = '';
    $indent = $indent ? '  ' : '';

    foreach ($rows as $cols) {
      $row = '';

      $cells = $cols['row'];
      $rowAttributes = $cols['attributes'];

      foreach ($cells as $cell) {
        $attributes = isset($cell['attributes']) ? $cell['attributes'] : array();
        $data       = isset($cell['data']) ? $cell['data'] : '&nbsp;';

        if (isset($cell['colspan'])) {
          $attributes['colspan'] = $cell['colspan'];
        }

        $row .= sprintf('%s    %s<td%s>%s</td>', PHP_EOL, $indent,
          $this->_attributes($attributes), $data);
      }

      $html .= sprintf('  %s<tr%s>%s%s%s  </tr>%s', $indent,
        $this->_attributes($rowAttributes), $row, PHP_EOL, $indent, PHP_EOL);
    }

    if (0 == strlen(trim($html))) {
      return '';
    }

    return sprintf($closure, PHP_EOL, $html);
  }

  /**
   * Cria uma string de atributos HTML.
   *
   * @param array $attributes
   * @return string
   */
  protected function _attributes(array $attributes = array())
  {
    if (0 == count($attributes)) {
      return '';
    }

    $html = array();
    ksort($attributes);
    foreach ($attributes as $key => $value) {
      $html[] = sprintf('%s="%s"', $key, $value);
    }

    return ' ' . implode(' ', $html);
  }

  /**
   * Implementa método mágico __toString().
   * @link
   */
  public function __toString()
  {
    return $this->createTable();
  }
}