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
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';

/**
 * TabelaArredondamento_Model_Tabela class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaArredondamento_Model_Tabela extends CoreExt_Entity
{
  protected $_data = array(
    'instituicao'    => NULL,
    'nome'           => NULL,
    'tipoNota'       => NULL
  );

  protected $_references = array(
    'tipoNota' => array(
      'value' => 1,
      'class' => 'RegraAvaliacao_Model_Nota_TipoValor',
      'file'  => 'RegraAvaliacao/Model/Nota/TipoValor.php'
    )
  );

  /**
   * Precisão decimal do valor da nota.
   * @var int
   */
  protected $_precision = 3;

  /**
   * @var array
   */
  protected $_tabelaValores = array();

  /**
   * @see CoreExt_Entity#getDataMapper()
   */
  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
      $this->setDataMapper(new TabelaArredondamento_Model_TabelaDataMapper());
    }
    return parent::getDataMapper();
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

    // Tipo nota
    $tipoNota = RegraAvaliacao_Model_Nota_TipoValor::getInstance();

    return array(
      'instituicao' => new CoreExt_Validate_Choice(array('choices' => $instituicoes)),
      'nome'        => new CoreExt_Validate_String(array('min' => 5, 'max' => 50)),
      'tipoNota'    => new CoreExt_Validate_Choice(array('choices' => $tipoNota->getKeys()))
    );
  }

  /**
   * Arredonda a nota de acordo com a tabela de valores da instância atual.
   *
   * @param $value
   * @return mixed
   */
  public function round($value)
  {
    if (0 > $value || 10 < $value) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException('O valor para '
                . 'arredondamento deve estar entre 0 e 10.');
    }

    if (0 == count($this->_tabelaValores)) {
      $this->_tabelaValores = $this->getDataMapper()->findTabelaValor($this);
    }

    // Multiplicador para transformar os números em uma escala inteira.
    $scale = pow(10, $this->_precision);

    // Escala o valor para se tornar comparável
    $value = $this->getFloat($value) * $scale;

    $return = 0;
    foreach ($this->_tabelaValores as $tabelaValor) {
      if ($value >= ($tabelaValor->valorMinimo * $scale) &&
          $value <= ($tabelaValor->valorMaximo * $scale)) {
        $return = $tabelaValor->nome;
        break;
      }
      $return = $tabelaValor->nome;
    }

    return $return;
  }

  /**
   * Método finder para TabelaArredondamento_Model_TabelaValor. Wrapper simples
   * para o mesmo método de TabelaArredondamento_Model_TabelaDataMapper.
   *
   * @return array
   */
  public function findTabelaValor()
  {
    if (0 == count($this->_tabelaValores)) {
      $this->_tabelaValores = $this->getDataMapper()->findTabelaValor($this);
    }
    return $this->_tabelaValores;
  }

  /**
   * @see CoreExt_Entity#__toString()
   */
  public function __toString()
  {
    return $this->nome;
  }
}