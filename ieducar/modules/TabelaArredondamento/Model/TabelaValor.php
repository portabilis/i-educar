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
 * TabelaArredondamento_Model_TabelaValor class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaArredondamento_Model_TabelaValor extends CoreExt_Entity
{
  protected $_data = array(
    'tabelaArredondamento' => NULL,
    'nome'                 => NULL,
    'descricao'            => NULL,
    'valorMinimo'          => NULL,
    'valorMaximo'          => NULL
  );

  protected $_dataTypes = array(
    'valorMinimo' => 'numeric',
    'valorMaximo' => 'numeric'
  );

  protected $_references = array(
    'tabelaArredondamento' => array(
      'value' => NULL,
      'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
      'file'  => 'TabelaArredondamento/Model/TabelaDataMapper.php'
    )
  );

  /**
   * @see CoreExt_Entity#getDataMapper()
   */
  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
      $this->setDataMapper(new TabelaArredondamento_Model_TabelaValorDataMapper());
    }
    return parent::getDataMapper();
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   * @todo Implementar validador que retorne um String ou Numeric, dependendo
   *   do valor do atributo (assim como validateIfEquals().
   * @todo Implementar validador que aceite um valor de comparação como
   *   alternativa a uma chave de atributo. (COMENTADO ABAIXO)
   */
  public function getDefaultValidatorCollection()
  {
    $validators = array();

    // Validação condicional
    switch ($this->tabelaArredondamento->get('tipoNota')) {
      case RegraAvaliacao_Model_Nota_TipoValor::NUMERICA:
        $validators['nome'] = new CoreExt_Validate_Numeric(
          array('min' => 0.00, 'max' => 10.0)
        );
        break;
      case RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL:
        $validators['nome'] = new CoreExt_Validate_String(
          array('min' => 1, 'max' => 5)
        );
        $validators['descricao'] = new CoreExt_Validate_String(
          array('min' => 2, 'max' => 25)
        );
        break;
    }

    $ret =
    $validators  + array(
      'valorMinimo' => new CoreExt_Validate_Numeric(array('min' => 0.00, 'max' => 9.999)),
      'valorMaximo' => new CoreExt_Validate_Numeric(array('min' => 0.001, 'max' => 10.0)),
    );
    return $ret;
  }

  public function __toString()
  {
    return $this->nome;
  }
}