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
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'RegraAvaliacao/Model/Regra.php';
require_once 'FormulaMedia/Model/TipoFormula.php';

/**
 * RegraAvaliacao_Model_RegraDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class RegraAvaliacao_Model_RegraDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'RegraAvaliacao_Model_Regra';
  protected $_tableName   = 'regra_avaliacao';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'instituicao'                 => 'instituicao_id',
    'tipoNota'                    => 'tipo_nota',
    'tipoProgressao'              => 'tipo_progressao',
    'tabelaArredondamento'        => 'tabela_arredondamento_id',
    'formulaMedia'                => 'formula_media_id',
    'formulaRecuperacao'          => 'formula_recuperacao_id',
    'porcentagemPresenca'         => 'porcentagem_presenca',
    'parecerDescritivo'           => 'parecer_descritivo',
    'tipoPresenca'                => 'tipo_presenca',
    'mediaRecuperacao'            => 'media_recuperacao',
    'tipoRecuperacaoParalela'     => 'tipo_recuperacao_paralela',
    'mediaRecuperacaoParalela'    => 'media_recuperacao_paralela',
    'notaMaximaGeral'             => 'nota_maxima_geral',
    'notaMaximaExameFinal'        => 'nota_maxima_exame_final',
    'qtdCasasDecimais'            => 'qtd_casas_decimais',
    'notaGeralPorEtapa'           => 'nota_geral_por_etapa',
    'qtdDisciplinasDependencia'   => 'qtd_disciplinas_dependencia'
  );

  /**
   * @var FormulaMedia_Model_FormulaDataMapper
   */
  protected $_formulaDataMapper = NULL;

  /**
   * @var TabelaArredondamento_Model_TabelaDataMapper
   */
  protected $_tabelaDataMapper = NULL;

  /**
   * Setter.
   * @param FormulaMedia_Model_FormulaDataMapper $mapper
   * @return RegraAvaliacao_Model_RegraDataMapper
   */
  public function setFormulaDataMapper(FormulaMedia_Model_FormulaDataMapper $mapper)
  {
    $this->_formulaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return FormulaMedia_Model_FormulaDataMapper
   */
  public function getFormulaDataMapper()
  {
    if (is_null($this->_formulaDataMapper)) {
      require_once 'FormulaMedia/Model/FormulaDataMapper.php';
      $this->setFormulaDataMapper(new FormulaMedia_Model_FormulaDataMapper());
    }
    return $this->_formulaDataMapper;
  }

  /**
   * Setter.
   * @param TabelaArredondamento_Model_TabelaDataMapper $mapper
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setTabelaDataMapper(TabelaArredondamento_Model_TabelaDataMapper $mapper)
  {
    $this->_tabelaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return TabelaArredondamento_Model_TabelaDataMapper
   */
  public function getTabelaDataMapper()
  {
    if (is_null($this->_tabelaDataMapper)) {
      require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
      $this->setTabelaDataMapper(new TabelaArredondamento_Model_TabelaDataMapper());
    }
    return $this->_tabelaDataMapper;
  }

  /**
   * Finder.
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  public function findFormulaMediaFinal($where = array())
  {
    return $this->_findFormulaMedia(array(
      $this->_getTableColumn('tipoFormula') => FormulaMedia_Model_TipoFormula::MEDIA_FINAL)
    );
  }

  /**
   * Finder.
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  public function findFormulaMediaRecuperacao($where = array())
  {
    return $this->_findFormulaMedia(array(
      $this->_getTableColumn('tipoFormula') => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO)
    );
  }

  /**
   * Finder genérico para FormulaMedia_Model_Formula.
   * @param array $where
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  protected function _findFormulaMedia(array $where = array())
  {
    return $this->getFormulaDataMapper()->findAll(array('nome'), $where);
  }

  /**
   * Finder para instâncias de TabelaArredondamento_Model_Tabela. Utiliza
   * o valor de instituição por instâncias que referenciem a mesma instituição.
   *
   * @param RegraAvaliacao_Model_Regra $instance
   * @return array
   */
  public function findTabelaArredondamento(RegraAvaliacao_Model_Regra $instance)
  {
    $where = array();

    if (isset($instance->instituicao)) {
      $where['instituicao'] = $instance->instituicao;
    }

    return $this->getTabelaDataMapper()->findAll(array(), $where);
  }

  /**
   * @var RegraAvaliacao_Model_RegraRecuperacaoDataMapper
   */
  protected $_regraRecuperacaoDataMapper = NULL;

  /**
   * Setter.
   * @param RegraAvaliacao_Model_RegraRecuperacaoDataMapper $mapper
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setRegraRecuperacaoDataMapper(RegraAvaliacao_Model_RegraRecuperacaoDataMapper $mapper)
  {
    $this->_regraRecuperacaoDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return RegraAvaliacao_Model_RegraRecuperacaoDataMappers
   */
  public function getRegraRecuperacaoDataMapper()
  {
    if (is_null($this->_regraRecuperacaoDataMapper)) {
      require_once 'RegraAvaliacao/Model/RegraRecuperacaoDataMapper.php';
      $this->setRegraRecuperacaoDataMapper(new RegraAvaliacao_Model_RegraRecuperacaoDataMapper());
    }
    return $this->_regraRecuperacaoDataMapper;
  }

  /**
   * Finder para instâncias de RegraAvaliacao_Model_RegraRecuperacao que tenham
   * referências a instância RegraAvaliacao_Model_Regra passada como
   * parâmetro.
   *
   * @param RegraAvaliacao_Model_Regra $instance
   * @return array Um array de instâncias RegraAvaliacao_Model_RegraRecuperacao
   */
  public function findRegraRecuperacao(RegraAvaliacao_Model_Regra $instance)
  {
    $where = array(
      'regraAvaliacao' => $instance->id
    );

    $orderby = array(
      'etapasRecuperadas' => 'ASC'
    );
    return $this->getRegraRecuperacaoDataMapper()->findAll(array(), $where, $orderby);
  }
}
