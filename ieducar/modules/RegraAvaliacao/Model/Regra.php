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

require_once 'CoreExt/Entity.php';
require_once 'RegraAvaliacao/Model/Nota/TipoValor.php';
require_once 'RegraAvaliacao/Model/TipoProgressao.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoRecuperacaoParalela.php';

/**
 * RegraAvaliacao_Model_Regra class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class RegraAvaliacao_Model_Regra extends CoreExt_Entity
{
  protected $_data = array(
    'instituicao'                    => NULL,
    'nome'                           => NULL,
    'tipoNota'                       => NULL,
    'tipoProgressao'                 => NULL,
    'tabelaArredondamento'           => NULL,
    'tabelaArredondamentoConceitual' => NULL,
    'media'                          => NULL,
    'formulaMedia'                   => NULL,
    'formulaRecuperacao'             => NULL,
    'porcentagemPresenca'            => NULL,
    'parecerDescritivo'              => NULL,
    'tipoPresenca'                   => NULL,
    'mediaRecuperacao'               => NULL,
    'tipoRecuperacaoParalela'        => NULL,
    'mediaRecuperacaoParalela'       => NULL,
    'notaMaximaGeral'                => NULL,
    'notaMinimaGeral'                => NULL,
    'notaMaximaExameFinal'           => NULL,
    'qtdCasasDecimais'               => NULL,
    'notaGeralPorEtapa'              => NULL,
    'definirComponentePorEtapa'      => NULL,
    'qtdDisciplinasDependencia'      => NULL,
    'qtdMatriculasDependencia'       => NULL,
    'aprovaMediaDisciplina'          => NULL,
    'reprovacaoAutomatica'           => NULL,
    'regraDiferenciada'              => NULL,
  );

  protected $_dataTypes = array(
    'media' => 'numeric',
    'porcentagemPresenca' => 'numeric',
    'mediaRecuperacao' => 'numeric',
    'tipoRecuperacaoParalela' => 'numeric',
    'notaMaximaGeral' => 'numeric',
    'notaMinimaGeral' => 'numeric',
    'notaMaximaExameFinal' => 'numeric',
    'qtdCasasDecimais' => 'numeric',
    'qtdDisciplinasDependencia' => 'numeric',
    'qtdMatriculasDependencia'  => 'numeric',
  );

  protected $_references = array(
    'tipoNota' => array(
      'value' => 1,
      'class' => 'RegraAvaliacao_Model_Nota_TipoValor',
      'file'  => 'RegraAvaliacao/Model/Nota/TipoValor.php'
    ),
    'tabelaArredondamento' => array(
      'value' => 1,
      'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
      'file'  => 'TabelaArredondamento/Model/TabelaDataMapper.php',
      'null'  => TRUE
    ),
    'tabelaArredondamentoConceitual' => array(
      'value' => 1,
      'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
      'file'  => 'TabelaArredondamento/Model/TabelaDataMapper.php',
      'null'  => TRUE
    ),
    'tipoProgressao' => array(
      'value' => 1,
      'class' => 'RegraAvaliacao_Model_TipoProgressao',
      'file'  => 'RegraAvaliacao/Model/TipoProgressao.php'
    ),
    'parecerDescritivo' => array(
      'value' => 0,
      'class' => 'RegraAvaliacao_Model_TipoParecerDescritivo',
      'file'  => 'RegraAvaliacao/Model/TipoParecerDescritivo.php',
      'null'  => TRUE
    ),
    'tipoPresenca' => array(
      'value' => 1,
      'class' => 'RegraAvaliacao_Model_TipoPresenca',
      'file'  => 'RegraAvaliacao/Model/TipoPresenca.php'
    ),
    'formulaMedia' => array(
      'value' => NULL,
      'class' => 'FormulaMedia_Model_FormulaDataMapper',
      'file'  => 'FormulaMedia/Model/FormulaDataMapper.php',
      'null'  => TRUE
    ),
    'formulaRecuperacao' => array(
      'value' => NULL,
      'class' => 'FormulaMedia_Model_FormulaDataMapper',
      'file'  => 'FormulaMedia/Model/FormulaDataMapper.php',
      'null'  => TRUE
    ),
    'tipoRecuperacaoParalela' => array(
      'value' => 0,
      'class' => 'RegraAvaliacao_Model_TipoRecuperacaoParalela',
      'file'  => 'RegraAvaliacao/Model/TipoRecuperacaoParalela.php',
      'null'  => TRUE
    ),
    'regraDiferenciada' => array(
      'value' => NULL,
      'class' => 'RegraAvaliacao_Model_RegraDataMapper',
      'file'  => 'RegraAvaliacao/Model/RegraDataMapper.php',
      'null'  => TRUE
    )
  );

  /**
   * @var array
   */
  protected $_regraRecuperacoes = array();

  /**
   * @see CoreExt_Entity#getDataMapper()
   */
  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
      $this->setDataMapper(new RegraAvaliacao_Model_RegraDataMapper());
    }
    return parent::getDataMapper();
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    // Enums
    $tipoNotaValor           = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
    $tipoProgressao          = RegraAvaliacao_Model_TipoProgressao::getInstance();
    $tipoParecerDescritivo   = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();
    $tipoPresenca            = RegraAvaliacao_Model_TipoPresenca::getInstance();
    $tipoRecuperacaoParalela = RegraAvaliacao_Model_TipoRecuperacaoParalela::getInstance();

    // ids de fórmulas de média
    $formulaMedia = $this->getDataMapper()->findFormulaMediaFinal();
    $formulaMedia = CoreExt_Entity::entityFilterAttr($formulaMedia, 'id');

    // ids de fórmulas de recuperação
    $formulaRecuperacao = $this->getDataMapper()->findFormulaMediaRecuperacao();
    $formulaRecuperacao = CoreExt_Entity::entityFilterAttr($formulaRecuperacao, 'id');
    $formulaRecuperacao[0] = NULL;

    // ids de regras diferenciadas
    $regraDiferenciada = $this->getDataMapper()->findAll();
    $regraDiferenciada = CoreExt_Entity::entityFilterAttr($regraDiferenciada, 'id');
    $regraDiferenciada[0] = NULL;

    // ids de tabelas de arredondamento
    $tabelas = $this->getDataMapper()->findTabelaArredondamento($this);
    $tabelas = CoreExt_Entity::entityFilterAttr($tabelas, 'id');

    // Instituições
    $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

    // Fórmula de média é obrigatória?
    $isFormulaMediaRequired = TRUE;

    // Média é obrigatória?
    $isMediaRequired = TRUE;

    if ($this->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
      $isFormulaMediaRequired = FALSE;
      $isMediaRequired = FALSE;

      // Aceita somente o valor NULL quando o tipo de nota é Nenhum.
      $formulaMedia = $formulaMedia + array(NULL);
    }

    return array(
      'instituicao' => new CoreExt_Validate_Choice(array(
        'choices' => $instituicoes
      )),
      'nome' => new CoreExt_Validate_String(array(
        'min' => 5, 'max' => 50
      )),
      'formulaMedia' => new CoreExt_Validate_Choice(array(
        'choices' => $formulaMedia,
        'required' => $isFormulaMediaRequired
      )),
      'formulaRecuperacao' => new CoreExt_Validate_Choice(array(
        'choices' => $formulaRecuperacao,
        'required' => FALSE
      )),
      'regraDiferenciada' => new CoreExt_Validate_Choice(array(
        'choices' => $regraDiferenciada,
        'required' => FALSE
      )),
      'tipoNota' => new CoreExt_Validate_Choice(array(
        'choices' => $tipoNotaValor->getKeys()
      )),
      'tipoProgressao' => new CoreExt_Validate_Choice(array(
        'choices' => $tipoProgressao->getKeys()
      )),
      'tabelaArredondamento' => new CoreExt_Validate_Choice(array(
        'choices' => $tabelas,
        'choice_error' => 'A tabela de arredondamento selecionada não '
                        . 'corresponde ao sistema de nota escolhido.'
      )),
      'tabelaArredondamentoConceitual' => new CoreExt_Validate_Choice(array(
        'choices' => $tabelas,
        'choice_error' => 'A tabela de arredondamento selecionada não '
                        . 'corresponde ao sistema de nota escolhido.'
      )),
      'parecerDescritivo' => new CoreExt_Validate_Choice(array(
        'choices' => $tipoParecerDescritivo->getKeys()
      )),
      'tipoPresenca' => new CoreExt_Validate_Choice(array(
        'choices' => $tipoPresenca->getKeys()
      )),
      'media' => $this->validateIfEquals(
        'tipoProgressao', RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
        'CoreExt_Validate_Numeric',
        array('required' => $isMediaRequired, 'min' => 1, 'max' => 10),
        array('required' => $isMediaRequired, 'min' => 0, 'max' => 10)
      ),
      'porcentagemPresenca' => new CoreExt_Validate_Numeric(array(
        'min' => 1, 'max' => 100
      )),
      'mediaRecuperacao' => $this->validateIfEquals(
        'tipoProgressao', RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
        'CoreExt_Validate_Numeric',
        array('required' => $isMediaRequired, 'min' => 1, 'max' => 14),
        array('required' => $isMediaRequired, 'min' => 0, 'max' => 14)
      ),
      'tipoRecuperacaoParalela' => new CoreExt_Validate_Choice(array(
        'choices' => $tipoRecuperacaoParalela->getKeys()
      )),
      'mediaRecuperacaoParalela' => new CoreExt_Validate_String(array(
        'min' => 1, 'max' => 10
      ))
      );
  }

  /**
   * Método finder para RegraAvaliacao_Model_RegraRecuperacao. Wrapper simples
   * para o mesmo método de RegraAvaliacao_Model_TabelaDataMapper.
   *
   * @return array
   */
  public function findRegraRecuperacao()
  {
    if (0 == count($this->_regraRecuperacoes)) {
      $this->_regraRecuperacoes = $this->getDataMapper()->findRegraRecuperacao($this);
    }
    return $this->_regraRecuperacoes;
  }

  public function getRegraRecuperacaoByEtapa($etapa){

    foreach ($this->findRegraRecuperacao() as $key => $_regraRecuperacao) {
      if (in_array($etapa, $_regraRecuperacao->getEtapas())){
        return $_regraRecuperacao;
      }
    }

    return null;
  }

  /**
   * @see CoreExt_Entity#__toString()
   */
  public function __toString()
  {
    return $this->nome;
  }
}
