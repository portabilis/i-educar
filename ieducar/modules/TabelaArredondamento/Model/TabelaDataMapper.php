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

require_once 'CoreExt/DataMapper.php';
require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'RegraAvaliacao/Model/Nota/TipoValor.php';
require_once 'App/Model/IedFinder.php';

/**
 * TabelaArredondamento_Model_TabelaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaArredondamento_Model_TabelaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'TabelaArredondamento_Model_Tabela';
  protected $_tableName   = 'tabela_arredondamento';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'instituicao' => 'instituicao_id',
    'tipoNota'    => 'tipo_nota'
  );

  /**
   * @var TabelaArredondamento_Model_TabelaValorDataMapper
   */
  protected $_tabelaValorDataMapper = NULL;

  /**
   * Setter.
   * @param TabelaArredondamento_Model_TabelaValorDataMapper $mapper
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setTabelaValorDataMapper(TabelaArredondamento_Model_TabelaValorDataMapper $mapper)
  {
    $this->_tabelaValorDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return TabelaArredondamento_Model_TabelaValorDataMappers
   */
  public function getTabelaValorDataMapper()
  {
    if (is_null($this->_tabelaValorDataMapper)) {
      require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
      $this->setTabelaValorDataMapper(new TabelaArredondamento_Model_TabelaValorDataMapper());
    }
    return $this->_tabelaValorDataMapper;
  }

  /**
   * Finder para instâncias de TabelaArredondamento_Model_TabelaValor que tenham
   * referências a instância TabelaArredondamento_Model_Tabela passada como
   * parâmetro.
   *
   * @param TabelaArredondamento_Model_Tabela $instance
   * @return array Um array de instâncias TabelaArredondamento_Model_TabelaValor
   */
  public function findTabelaValor(TabelaArredondamento_Model_Tabela $instance)
  {
    $where = array(
      'tabelaArredondamento' => $instance->id
    );
    return $this->getTabelaValorDataMapper()->findAll(array(), $where);
  }
}