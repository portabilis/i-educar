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
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Configurable.php';
require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'App/Model/Matricula.php';
require_once 'App/Model/MatriculaSituacao.php';

/**
 * Avaliacao_Service_Boletim class.
 *
 * Implementa uma API orientada a serviços (Service Layer Pattern
 * {@link http://martinfowler.com/eaaCatalog/serviceLayer.html}).
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @todo        Substituir todos os usos literais de 'Rc' e 'An' por constantes
 *              ou por um novo CoreExt_Enum
 * @todo        Criar método que retorna o conjunto de faltas de acordo com o
 *              tipo de presença da regra, refatorando a série de condicionais
 *              existentes em métodos como getSituacaoFaltas()
 * @version     @@package_version@@
 */
class Avaliacao_Service_Boletim implements CoreExt_Configurable
{
  /**
   * Valores escalares.
   * @var array
   */
  protected $_options = array(
    'matricula' => NULL,
    'etapas'    => NULL,
    'usuario'   => NULL
  );

  /**
   * Instância da regra de avaliação, com o qual o serviço irá utilizar para
   * decidir o fluxo da lógica.
   * @var RegraAvaliacao_Model_Regra
   */
  protected $_regra = NULL;

  /**
   * @var ComponenteCurricular_Model_ComponenteDataMapper
   */
  protected $_componenteDataMapper = NULL;

  /**
   * @var ComponenteCurricular_Model_TurmaDataMapper
   */
  protected $_componenteTurmaDataMapper = NULL;

  /**
   * @var RegraAvaliacao_Model_RegraDataMapper
   */
  protected $_regraDataMapper = NULL;

  /**
   * @var Avaliacao_Model_NotaAlunoDataMapper
   */
  protected $_notaAlunoDataMapper = NULL;

  /**
   * @var Avaliacao_Model_FaltaAlunoDataMapper
   */
  protected $_faltaAlunoDataMapper = NULL;

  /**
   * @var Avaliacao_Model_ParecerDescritivoAlunoDataMapper
   */
  protected $_parecerDescritivoAlunoDataMapper = NULL;

  /**
   * @var Avaliacao_Model_NotaComponenteDataMapper
   */
  protected $_notaComponenteDataMapper = NULL;

  /**
   * @var Avaliacao_Model_FaltaAbstractDataMapper
   */
  protected $_faltaAbstractDataMapper = NULL;

  /**
   * @var Avaliacao_Model_NotaComponenteMediaDataMapper
   */
  protected $_notaComponenteMediaDataMapper = NULL;

  /**
   * @var Avaliacao_Model_ParecerDescritivoAbstractDataMapper
   */
  protected $_parecerDescritivoAbstractDataMapper = NULL;

  /**
   * Uma instância de Avaliacao_Model_NotaAluno, que é a entrada que contém
   * o cruzamento de matrícula com as notas do aluno nos diversos componentes
   * cursados.
   *
   * @var Avaliacao_Model_NotaAluno
   */
  protected $_notaAluno = NULL;

  /**
   * Uma instância de Avaliacao_Model_FaltaAluno, que é a entrada que contém
   * o cruzamento de matrícula com as faltas do aluno nos diversos componentes
   * cursados ou no geral.
   *
   * @var Avaliacao_Model_NotaAluno
   */
  protected $_faltaAluno = NULL;

  /**
   * Uma instância de Avaliacao_Model_ParecerDescritivoAluno, que é a entrada
   * que contém o cruzamento de matrícula com os pareceres do aluno nos diversos
   * componentes cursados ou no geral.
   *
   * @var Avaliacao_Model_ParecerDescritivoAluno
   */
  protected $_parecerDescritivoAluno = NULL;

  /**
   * Componentes que o aluno cursa, indexado pelo id de
   * ComponenteCurricular_Model_Componente.
   * @var array
   */
  protected $_componentes = NULL;

  /**
   * Notas do aluno nos componentes cursados.
   * @var array
   */
  protected $_notasComponentes = array();

  /**
   * Médias do aluno nos componentes cursados.
   * @var array
   */
  protected $_mediasComponentes = array();

  /**
   * Notas adicionadas no boletim para inclusão ou edição.
   * @var array
   */
  protected $_notas = array();

  /**
   * Faltas do aluno nos componentes cursados.
   * @var array
   */
  protected $_faltasComponentes = array();

  /**
   * Faltas do aluno no geral.
   * @var array
   */
  protected $_faltasGerais = array();

  /**
   * Faltas adicionadas no boletim para inclusão ou edição.
   * @var array
   */
  protected $_faltas = array();

  /**
   * Pareceres descritivos adicionados no boletim para inclusão ou edição.
   * @var array
   */
  protected $_pareceres = array();

  /**
   * Pareceres descritivos do aluno nos componentes cursados.
   * @var array
   */
  protected $_pareceresComponentes = array();

  /**
   * Pareceres descritivos do aluno no geral.
   * @var array
   */
  protected $_pareceresGerais = array();

  /**
   * Validadores para instâncias de Avaliacao_Model_FaltaAbstract e
   * Avaliacao_Model_NotaComponente.
   *
   * @see Avaliacao_Service_Boletim#_addValidators()
   * @var array
   */
  protected $_validators = NULL;

  /**
   * Validadores para uma instância de Avaliacao_Model_ParecerDescritivoAbstract
   * adicionada no boletim.
   *
   * @see Avaliacao_Service_Boletim#_addParecerValidators()
   * @var array
   */
  protected $_parecerValidators = NULL;

  /**
   * Prioridade da situação da matrícula, usado para definir a situação
   * das notas e faltas.
   * @var array
   */
  protected $_situacaoPrioridade = array(
    App_Model_MatriculaSituacao::EM_ANDAMENTO        => 1,
    App_Model_MatriculaSituacao::EM_EXAME            => 2,
    App_Model_MatriculaSituacao::REPROVADO           => 3,
    App_Model_MatriculaSituacao::APROVADO_APOS_EXAME => 4,
    App_Model_MatriculaSituacao::APROVADO            => 5
  );

  /**
   * Construtor.
   *
   * Opções de configuração:
   * - matricula (int), obrigatória
   * - ComponenteDataMapper (Componente_Model_ComponenteDataMapper), opcional
   * - RegraDataMapper (Regra_Model_RegraDataMapper), opcional
   * - NotaAlunoDataMapper (Avaliacao_Model_NotaAlunoDataMapper), opcional
   *
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->setOptions($options)
         ->_setMatriculaInfo()
         ->_loadNotaComponenteCurricular()
         ->_loadFalta()
         ->_loadParecerDescritivo();
  }

  /**
   * @see CoreExt_Configurable#setOptions()
   */
  public function setOptions(array $options = array())
  {
    if (!isset($options['matricula'])) {
      require_once 'CoreExt/Service/Exception.php';
      throw new CoreExt_Service_Exception('É necessário informar o número de '
                . 'matrícula do aluno.');
    }

    if (isset($options['ComponenteDataMapper'])) {
      $this->setComponenteDataMapper($options['ComponenteDataMapper']);
      unset($options['ComponenteDataMapper']);
    }

    if (isset($options['ComponenteTurmaDataMapper'])) {
      $this->setComponenteTurmaDataMapper($options['ComponenteTurmaDataMapper']);
      unset($options['ComponenteTurmaDataMapper']);
    }

    if (isset($options['RegraDataMapper'])) {
      $this->setRegraDataMapper($options['RegraDataMapper']);
      unset($options['RegraDataMapper']);
    }

    if (isset($options['NotaAlunoDataMapper'])) {
      $this->setNotaAlunoDataMapper($options['NotaAlunoDataMapper']);
      unset($options['NotaAlunoDataMapper']);
    }

    if (isset($options['NotaComponenteDataMapper'])) {
      $this->setNotaComponenteDataMapper($options['NotaComponenteDataMapper']);
      unset($options['NotaComponenteDataMapper']);
    }

    if (isset($options['NotaComponenteMediaDataMapper'])) {
      $this->setNotaComponenteMediaDataMapper($options['NotaComponenteMediaDataMapper']);
      unset($options['NotaComponenteMediaDataMapper']);
    }

    if (isset($options['FaltaAlunoDataMapper'])) {
      $this->setFaltaAlunoDataMapper($options['FaltaAlunoDataMapper']);
      unset($options['FaltaAlunoDataMapper']);
    }

    if (isset($options['FaltaAbstractDataMapper'])) {
      $this->setFaltaAbstractDataMapper($options['FaltaAbstractDataMapper']);
      unset($options['FaltaAbstractDataMapper']);
    }

    if (isset($options['ParecerDescritivoAlunoDataMapper'])) {
      $this->setParecerDescritivoAlunoDataMapper($options['ParecerDescritivoAlunoDataMapper']);
      unset($options['ParecerDescritivoAlunoDataMapper']);
    }

    if (isset($options['ParecerDescritivoAbstractDataMapper'])) {
      $this->setParecerDescritivoAbstractDataMapper($options['ParecerDescritivoAbstractDataMapper']);
      unset($options['ParecerDescritivoAbstractDataMapper']);
    }

    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      throw new InvalidArgumentException(
        sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    $this->_options = array_merge($this->getOptions(), $options);

    return $this;
  }

  /**
   * @see CoreExt_Configurable#getOptions()
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Setter.
   * @param string $key
   * @param mixed $value
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function setOption($key, $value)
  {
    $this->_options[$key] = $value;
    return $this;
  }

  /**
   * Getter.
   * @param string $key
   * @return mixed
   */
  public function getOption($key)
  {
    return $this->_options[$key];
  }

  /**
   * Setter.
   * @param ComponenteCurricular_Model_ComponenteDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setComponenteDataMapper(ComponenteCurricular_Model_ComponenteDataMapper $mapper)
  {
    $this->_componenteDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return ComponenteCurricular_Model_ComponenteDataMapper
   */
  public function getComponenteDataMapper()
  {
    if (is_null($this->_componenteDataMapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $this->setComponenteDataMapper(new ComponenteCurricular_Model_ComponenteDataMapper());
    }
    return $this->_componenteDataMapper;
  }

  /**
   * Setter.
   * @param ComponenteCurricular_Model_TurmaDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setComponenteTurmaDataMapper(ComponenteCurricular_Model_TurmaDataMapper $mapper)
  {
    $this->_componenteTurmaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return ComponenteCurricular_Model_TurmaDataMapper
   */
  public function getComponenteTurmaDataMapper()
  {
    if (is_null($this->_componenteTurmaDataMapper)) {
      require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
      $this->setComponenteTurmaDataMapper(new ComponenteCurricular_Model_TurmaDataMapper());
    }
    return $this->_componenteTurmaDataMapper;
  }

  /**
   * Setter.
   * @param RegraAvaliacao_Model_RegraDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setRegraDataMapper(RegraAvaliacao_Model_RegraDataMapper $mapper)
  {
    $this->_regraDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return RegraAvaliacao_Model_RegraDataMapper
   */
  public function getRegraDataMapper()
  {
    if (is_null($this->_regraDataMapper)) {
      require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
      $this->setRegraDataMapper(new RegraAvaliacao_Model_RegraDataMapper());
    }
    return $this->_regraDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_NotaAlunoDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setNotaAlunoDataMapper(Avaliacao_Model_NotaAlunoDataMapper $mapper)
  {
    $this->_notaAlunoDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_NotaAlunoDataMapper
   */
  public function getNotaAlunoDataMapper()
  {
    if (is_null($this->_notaAlunoDataMapper)) {
      require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
      $this->setNotaAlunoDataMapper(new Avaliacao_Model_NotaAlunoDataMapper());
    }
    return $this->_notaAlunoDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_NotaComponenteDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setNotaComponenteDataMapper(Avaliacao_Model_NotaComponenteDataMapper $mapper)
  {
    $this->_notaComponenteDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_NotaComponenteDataMapper
   */
  public function getNotaComponenteDataMapper()
  {
    if (is_null($this->_notaComponenteDataMapper)) {
      require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
      $this->setNotaComponenteDataMapper(new Avaliacao_Model_NotaComponenteDataMapper());
    }
    return $this->_notaComponenteDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_NotaMediaComponenteDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setNotaComponenteMediaDataMapper(Avaliacao_Model_NotaComponenteMediaDataMapper $mapper)
  {
    $this->_notaComponenteMediaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_NotaComponenteMediaDataMapper
   */
  public function getNotaComponenteMediaDataMapper()
  {
    if (is_null($this->_notaComponenteMediaDataMapper)) {
      require_once 'Avaliacao/Model/NotaComponenteMediaDataMapper.php';
      $this->setNotaComponenteMediaDataMapper(new Avaliacao_Model_NotaComponenteMediaDataMapper());
    }
    return $this->_notaComponenteMediaDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_FaltaAlunoDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setFaltaAlunoDataMapper(Avaliacao_Model_FaltaAlunoDataMapper $mapper)
  {
    $this->_faltaAlunoDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_NotaAlunoDataMapper
   */
  public function getFaltaAlunoDataMapper()
  {
    if (is_null($this->_faltaAlunoDataMapper)) {
      require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
      $this->setFaltaAlunoDataMapper(new Avaliacao_Model_FaltaAlunoDataMapper());
    }
    return $this->_faltaAlunoDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_FaltaAbstractDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setFaltaAbstractDataMapper(Avaliacao_Model_FaltaAbstractDataMapper $mapper)
  {
    $this->_faltaAbstractDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_FaltaAbstractDataMapper
   */
  public function getFaltaAbstractDataMapper()
  {
    if (is_null($this->_faltaAbstractDataMapper)) {
      switch ($this->getRegra()->get('tipoPresenca')) {
        case RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE:
          require_once 'Avaliacao/Model/FaltaComponenteDataMapper.php';
          $class = 'Avaliacao_Model_FaltaComponenteDataMapper';
          break;
        case RegraAvaliacao_Model_TipoPresenca::GERAL:
          require_once 'Avaliacao/Model/FaltaGeralDataMapper.php';
          $class = 'Avaliacao_Model_FaltaGeralDataMapper';
          break;
      }
      $this->setFaltaAbstractDataMapper(new $class());
    }

    return $this->_faltaAbstractDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_ParecerDescritivoAlunoDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setParecerDescritivoAlunoDataMapper(Avaliacao_Model_ParecerDescritivoAlunoDataMapper $mapper)
  {
    $this->_parecerDescritivoAlunoDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_ParecerDescritivoAlunoDataMapper
   */
  public function getParecerDescritivoAlunoDataMapper()
  {
    if (is_null($this->_parecerDescritivoAlunoDataMapper)) {
      require_once 'Avaliacao/Model/ParecerDescritivoAlunoDataMapper.php';
      $this->setParecerDescritivoAlunoDataMapper(new Avaliacao_Model_ParecerDescritivoAlunoDataMapper());
    }
    return $this->_parecerDescritivoAlunoDataMapper;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_ParecerDescritivoAbstractDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setParecerDescritivoAbstractDataMapper(Avaliacao_Model_ParecerDescritivoAbstractDataMapper $mapper)
  {
    $this->_parecerDescritivoAbstractDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_ParecerDescritivoAbstractDataMapper
   */
  public function getParecerDescritivoAbstractDataMapper()
  {
    if (is_null($this->_parecerDescritivoAbstractDataMapper)) {
      $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');

      switch($parecerDescritivo) {
        case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL:
        case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL:
          $filename = 'Avaliacao/Model/ParecerDescritivoGeralDataMapper.php';
          $class    = 'Avaliacao_Model_ParecerDescritivoGeralDataMapper';
          break;
        case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE:
        case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE:
          $filename = 'Avaliacao/Model/ParecerDescritivoComponenteDataMapper.php';
          $class    = 'Avaliacao_Model_ParecerDescritivoComponenteDataMapper';
          break;
      }

      // Se não usar parecer descritivo, retorna NULL
      if (!isset($filename)) {
        return NULL;
      }

      require_once $filename;
      $this->setParecerDescritivoAbstractDataMapper(new $class());
    }

    return $this->_parecerDescritivoAbstractDataMapper;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_NotaComponente do aluno.
   * @return array
   */
  public function getNotasComponentes()
  {
    return $this->_notasComponentes;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_NotaComponenteMedia do aluno.
   * @return array
   */
  public function getMediasComponentes()
  {
    return $this->_mediasComponentes;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_FaltaComponente do aluno.
   * @return array
   */
  public function getFaltasComponentes()
  {
    return $this->_faltasComponentes;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_FaltaGeral do aluno.
   * @return array
   */
  public function getFaltasGerais()
  {
    return $this->_faltasGerais;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_ParecerDescritivoComponente do
   * aluno.
   * @return array
   */
  public function getPareceresComponentes()
  {
    return $this->_pareceresComponentes;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_ParecerDescritivoGeral do aluno.
   * @return array
   */
  public function getPareceresGerais()
  {
    return $this->_pareceresGerais;
  }

  /**
   * Retorna uma instância de Avaliacao_Model_NotaComponente.
   *
   * @param int $id O identificador de ComponenteCurricular_Model_Componente
   * @param int $etapa A etapa para o qual a nota foi lançada
   * @return Avaliacao_Model_NotaComponente|NULL
   */
  public function getNotaComponente($id, $etapa = 1)
  {
    $componentes = $this->getNotasComponentes();

    if (!isset($componentes[$id])) {
      return NULL;
    }

    $notasComponente = $componentes[$id];

    foreach ($notasComponente as $nota) {
      if ($nota->etapa == $etapa) {
        return $nota;
      }
    }

    return NULL;
  }

  /**
   * Retorna uma instância de Avaliacao_Model_FaltaAbstract.
   *
   * @param int $etapa A etapa para o qual a falta foi lançada
   * @param int $id O identificador de ComponenteCurricular_Model_Componente
   * @return Avaliacao_Model_FaltaAbstract|NULL
   */
  public function getFalta($etapa = 1, $id = NULL)
  {
    if ($this->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      $faltas = $this->getFaltasComponentes();

      if (!isset($faltas[$id])) {
        return NULL;
      }

      $faltas = $faltas[$id];
    }
    else {
      $faltas = $this->getFaltasGerais();
    }

    foreach ($faltas as $falta) {
      if ($falta->etapa == $etapa) {
        return $falta;
      }
    }

    return NULL;
  }

  /**
   * Retorna uma instância de Avaliacao_Model_ParecerDescritivoAbstract.
   *
   * @param int $etapa A etapa para o qual o parecer foi lançado
   * @param int $id O identificador de ComponenteCurricular_Model_Componente
   * @return Avaliacao_Model_ParecerAbstract|NULL
   */
  public function getParecerDescritivo($etapa = 1, $id = NULL)
  {
    $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');

    $gerais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
    );

    $componentes = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
    );

    if (in_array($parecerDescritivo, $gerais)) {
      $pareceres = $this->getPareceresGerais();
    }
    elseif (in_array($parecerDescritivo, $componentes)) {
      $pareceres = $this->getPareceresComponentes();

      if (!isset($pareceres[$id])) {
        return NULL;
      }

      $pareceres = $pareceres[$id];
    }

    foreach ($pareceres as $parecer) {
      if ($parecer->etapa == $etapa) {
        return $parecer;
      }
    }

    return NULL;
  }


  /**
   * Setter.
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _setMatriculaInfo()
  {
    $codMatricula = $this->getOption('matricula');

    $this->_setRegra(App_Model_IedFinder::getRegraAvaliacaoPorMatricula(
            $codMatricula, $this->getRegraDataMapper()
           ))

         ->_setComponentes(App_Model_IedFinder::getComponentesPorMatricula(
            $codMatricula, $this->getComponenteDataMapper(),
            $this->getComponenteTurmaDataMapper()
           ));

    // Valores scalar de referência
    $matricula = App_Model_IedFinder::getMatricula($codMatricula);

    $etapas = App_Model_IedFinder::getQuantidadeDeModulosMatricula($codMatricula);
    $this->setOption('matriculaData',     $matricula);
    $this->setOption('aprovado',          $matricula['aprovado']);
    $this->setOption('cursoHoraFalta',    $matricula['curso_hora_falta']);
    $this->setOption('cursoCargaHoraria', $matricula['curso_carga_horaria']);
    $this->setOption('serieCargaHoraria', $matricula['serie_carga_horaria']);
    $this->setOption('serieDiasLetivos',  $matricula['serie_dias_letivos']);
    $this->setOption('etapas',            $etapas);

    return $this;
  }

  /**
   * Carrega todas as notas e médias já lançadas para a matrícula atual.
   *
   * @param bool $loadMedias FALSE caso não seja necessário carregar as médias
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadNotaComponenteCurricular($loadMedias = TRUE)
  {
    // Cria uma entrada no boletim caso o aluno/matrícula não a tenha
    if (!$this->hasNotaAluno()) {
      $this->_createNotaAluno();
    }

    // Senão tiver, vai criar
    $notaAluno = $this->_getNotaAluno();

    $notas = $this->getNotaComponenteDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC')
    );

    // Separa cada nota em um array indexado pelo identity do componente
    $notasComponentes = array();
    foreach ($notas as $nota) {
      $notasComponentes[$nota->get('componenteCurricular')][] = $nota;
    }

    $this->_notasComponentes = $notasComponentes;

    if (FALSE == $loadMedias) {
      return $this;
    }

    return $this->_loadNotaComponenteCurricularMedia();
  }

  /**
   * Carrega as médias dos componentes curriculares já lançadas.
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadNotaComponenteCurricularMedia()
  {
    $notaAluno = $this->_getNotaAluno();

    $medias = $this->getNotaComponenteMediaDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id)
    );

    $mediasComponentes = array();
    foreach ($medias as $media) {
      $mediasComponentes[$media->get('componenteCurricular')][] = $media;
    }

    $this->_mediasComponentes = $mediasComponentes;

    return $this;
  }

  /**
   * Carrega as faltas do aluno, sejam gerais ou por componente.
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadFalta()
  {
    // Cria uma entrada no boletim caso o aluno/matrícula não a tenha
    if (!$this->hasFaltaAluno()) {
      $this->_createFaltaAluno();
    }

    // Senão tiver, vai criar
    $faltaAluno = $this->_getFaltaAluno();

    // Carrega as faltas já lançadas
    $faltas = $this->getFaltaAbstractDataMapper()->findAll(
      array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC')
    );

    // Se a falta for do tipo geral, popula um array indexado pela etapa
    if ($faltaAluno->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
      $faltasGerais = array();

      foreach ($faltas as $falta) {
        $faltasGerais[$falta->etapa] = $falta;
      }

      $this->_faltasGerais = $faltasGerais;
    }
    // Separa cada nota em um array indexado pelo identity field do componente
    elseif ($faltaAluno->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      $faltasComponentes = array();

      foreach ($faltas as $falta) {
        $faltasComponentes[$falta->get('componenteCurricular')][] = $falta;
      }

      $this->_faltasComponentes = $faltasComponentes;
    }

    return $this;
  }

  /**
   * Carrega os pareceres do aluno, sejam gerais ou por componentes.
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadParecerDescritivo()
  {
    if ($this->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
      return $this;
    }

    if (!$this->hasParecerDescritivoAluno()) {
      $this->_createParecerDescritivoAluno();
    }

    $parecerDescritivoAluno = $this->_getParecerDescritivoAluno();

    $pareceres = $this->getParecerDescritivoAbstractDataMapper()->findAll(
      array(), array('parecerDescritivoAluno' => $parecerDescritivoAluno->id), array('etapa' => 'ASC')
    );

    $gerais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL
    );

    $componentes = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
    );

    $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');
    if (in_array($parecerDescritivo, $gerais)) {
      $pareceresGerais = array();

      foreach ($pareceres as $parecer) {
        $pareceresGerais[$parecer->etapa] = $parecer;
      }

      $this->_pareceresGerais = $pareceresGerais;
    }
    elseif (in_array($parecerDescritivo, $componentes)) {
      $pareceresComponentes = array();

      foreach ($pareceres as $parecer) {
        $pareceresComponentes[$parecer->get('componenteCurricular')][] = $parecer;
      }

      $this->_pareceresComponentes = $pareceresComponentes;
    }

    return $this;
  }

  /**
   * Setter.
   * @param RegraAvaliacao_Model_Regra $regra
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _setRegra(RegraAvaliacao_Model_Regra $regra)
  {
    $this->_regra = $regra;
    return $this;
  }

  /**
   * Getter.
   * @return RegraAvaliacao_Model_Regra
   */
  public function getRegra()
  {
    return $this->_regra;
  }

  /**
   * Setter.
   * @param array $componentes
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  protected function _setComponentes(array $componentes)
  {
    $this->_componentes = $componentes;
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  public function getComponentes()
  {
    return $this->_componentes;
  }

  /**
   * Getter.
   * @return TabelaArredondamento_Model_Tabela
   */
  public function getTabelaArredondamento()
  {
    return $this->getRegra()->tabelaArredondamento;
  }

  /**
   * Verifica se a regra de avaliacação possui recuperação final.
   * @return bool
   */
  public function hasRecuperacao()
  {
    if (is_null($this->getRegra()->get('formulaRecuperacao'))) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Verifica se o aluno tem notas lançadas.
   * @return bool
   */
  public function hasNotaAluno()
  {
    if (!is_null($this->_getNotaAluno())) {
      return TRUE;
    }

    return FALSE;
  }


  function getSituacaoNotaFalta($flagSituacaoNota, $flagSituacaoFalta)
  {
    $situacao              = new stdClass();
    $situacao->situacao    = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    switch ($flagSituacaoNota) {
      case App_Model_MatriculaSituacao::EM_ANDAMENTO:
        $situacao->aprovado  = FALSE;
        $situacao->andamento = TRUE;
        break;
      case App_Model_MatriculaSituacao::APROVADO_APOS_EXAME:
        $situacao->recuperacao = TRUE;
        break;
      case App_Model_MatriculaSituacao::EM_EXAME:
        $situacao->aprovado    = FALSE;
        $situacao->andamento   = TRUE;
        $situacao->recuperacao = TRUE;
        break;
      case App_Model_MatriculaSituacao::REPROVADO:
        $situacao->aprovado    = FALSE;
        break;
    }

    switch ($flagSituacaoFalta) {
      case App_Model_MatriculaSituacao::EM_ANDAMENTO:
        $situacao->aprovado  = FALSE;
        $situacao->andamento = TRUE;
        break;
      case App_Model_MatriculaSituacao::REPROVADO:
        $situacao->retidoFalta = TRUE;
        $situacao->aprovado    = FALSE;

        // Se reprovado por falta, mesmo que falte lançar a nota de exame, considera como reprovado.
        $situacao->andamento   = FALSE;
        break;
      case App_Model_MatriculaSituacao::APROVADO:
        $situacao->retidoFalta = FALSE;
        break;
    }

    // seta situacao geral
    if ($situacao->andamento and $situacao->recuperacao)
      $situacao->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    elseif (! $situacao->andamento and $situacao->aprovado and $situacao->recuperacao)
      $situacao->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    elseif (! $situacao->andamento and $situacao->aprovado)
      $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;

    elseif (! $situacao->andamento and (! $situacao->aprovado || $situacao->retidoFalta))
      $situacao->situacao = App_Model_MatriculaSituacao::REPROVADO;

    return $situacao;
  }


  /**
   * Retorna a situação geral do aluno, levando em consideração as situações
   * das notas (médias) e faltas. O retorno é baseado em booleanos, indicando
   * se o aluno está aprovado, em andamento, em recuperação ou retido por falta.
   *
   * Retorna também a situação das notas e faltas tais quais retornadas pelos
   * métodos getSituacaoComponentesCurriculares() e getSituacaoFaltas().
   *
   * <code>
   * <?php
   * $situacao = new stdClass();
   * $situacao->aprovado    = TRUE;
   * $situacao->andamento   = FALSE;
   * $situacao->recuperacao = FALSE;
   * $situacao->retidoFalta = FALSE;
   * $situacao->nota        = $this->getSituacaoComponentesCurriculares();
   * $situacao->falta       = $this->getSituacaoFaltas();
   * </code>
   *
   * @return stdClass
   * @see Avaliacao_Service_Boletim#getSituacaoComponentesCurriculares()
   * @see Avaliacao_Service_Boletim#getSituacaoFaltas()
   */
  public function getSituacaoAluno()
  {
    $situacaoNotas  = $this->getSituacaoNotas();
    $situacaoFaltas = $this->getSituacaoFaltas();

    $situacao        = $this->getSituacaoNotaFalta($situacaoNotas->situacao, $situacaoFaltas->situacao);
    $situacao->nota  = $situacaoNotas;
    $situacao->falta = $situacaoFaltas;

    return $situacao;
  }

  /**
   * Retorna a situação das notas lançadas para os componentes curriculares cursados pelo aluno. Possui
   * uma flag "situacao" global, que indica a situação global do aluno, podendo
   * ser:
   *
   * - Em andamento
   * - Em exame
   * - Aprovado
   * - Reprovado
   *
   * Esses valores são definidos no enum App_Model_MatriculaSituacao.
   *
   * Para cada componente curricular, será indicado a situação do aluno no
   * componente.
   *
   * Esses resultados são retornados como um objeto stdClass que possui dois
   * atributos: "situacao" e "componentesCurriculares". O primeiro é um tipo
   * inteiro e o segundo um array indexado pelo id do componente e com um
   * atributo inteiro "situacao":
   *
   * <code>
   * <?php
   * $situacao = new stdClass();
   * $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
   * $situacao->componentesCurriculares = array();
   * $situacao->componentesCurriculares[1] = new stdClass();
   * $situacao->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
   * </code>
   *
   * Esses valores são definidos SOMENTE através da verificação das médias dos
   * componentes curriculares já avaliados.
   *
   * Obs: Anteriormente este metódo se chamava getSituacaoComponentesCurriculares, porem na verdade não retornava a
   *      situação dos componentes curriculares (que seria a situação baseada nas notas e das faltas lançadas) e sim
   *      então foi renomeado este metodo para getSituacaoNotas, para que no metódo getSituacaoComponentesCurriculares
   *      fosse retornado a situação do baseada nas notas e faltas lançadas.
   *
   *
   * @return stdClass|NULL Retorna NULL caso não
   * @see App_Model_MatriculaSituacao
   */
  public function getSituacaoNotas()
  {
    $situacao = new stdClass();
    $situacao->situacao = 0;
    $situacao->componentesCurriculares = array();

    // A situação é "aprovado" por padrão
    $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;

    if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
      return $situacao;
    }

    // Carrega as médias pois este método pode ser chamado após a chamada a saveNotas()
    $mediasComponentes = $this->_loadNotaComponenteCurricularMedia()
                              ->getMediasComponentes();

    // Se não tiver nenhuma média ou a quantidade for diferente dos componentes
    // curriculares da matrícula, ainda está em andamento
    if (0 == count($mediasComponentes) ||
      count($mediasComponentes) != count($this->getComponentes())) {
      $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    }

    foreach ($mediasComponentes as $id => $mediaComponente) {
      $etapa = $mediaComponente[0]->etapa;

      if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
        $media = $mediaComponente[0]->mediaArredondada;
      }
      else {
        $media = $mediaComponente[0]->media;
      }

      $situacao->componentesCurriculares[$id] = new stdClass();
      $situacao->componentesCurriculares[$id]->situacao = 0;

      if ($etapa == $this->getOption('etapas') && $media < $this->getRegra()->media &&
          $this->hasRecuperacao()) {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_EXAME;
      }
      elseif ($etapa == $this->getOption('etapas') && $media < $this->getRegra()->media) {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ($etapa == 'Rc' && $media < $this->getRegra()->mediaRecuperacao) {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ($etapa == 'Rc' && $media >= $this->getRegra()->mediaRecuperacao && $this->hasRecuperacao()) {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
      }
      elseif ($etapa < $this->getOption('etapas') && $etapa != 'Rc') {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
      }
      else {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO;
      }

      if ($this->_situacaoPrioritaria($situacao->componentesCurriculares[$id]->situacao,
                                        $situacaoGeral)) {
        $situacaoGeral = $situacao->componentesCurriculares[$id]->situacao;
      }
    }

    // Situação geral
    $situacao->situacao = $situacaoGeral;

    return $situacao;
  }

  /**
   * Retorna a situação das faltas do aluno, sejam por componentes curriculares
   * ou gerais. A situação pode ser:
   *
   * - Em andamento
   * - Aprovado
   * - Reprovado
   *
   * Retorna outros dados interessantes, a maioria informacional para exibição
   * ao usuário, como a carga horária (geral e por componente), a porcentagem
   * de presença (geral e por componente), a porcentagem de falta (geral e
   * por componente), a hora/falta usada para o cálculo das porcentagens e o
   * total de faltas geral.
   *
   * Esses resultados são retornados como um objeto stdClass que possui os
   * seguintes atributos:
   *
   * <code>
   * <?php
   * $presenca = new stdClass();
   * $presenca->situacao                 = 0;
   * $presenca->tipoFalta                = 0;
   * $presenca->cargaHoraria             = 0;
   * $presenca->cursoHoraFalta           = 0;
   * $presenca->totalFaltas              = 0;
   * $presenca->horasFaltas              = 0;
   * $presenca->porcentagemFalta         = 0;
   * $presenca->porcentagemPresenca      = 0;
   * $presenca->porcentagemPresencaRegra = 0;
   *
   * $presenca->componentesCurriculares  = array();
   * $presenca->componentesCurriculares[1] = new stdClass();
   * $presenca->componentesCurriculares[1]->situacao            = 0;
   * $presenca->componentesCurriculares[1]->horasFaltas         = 0;
   * $presenca->componentesCurriculares[1]->porcentagemFalta    = 0;
   * $presenca->componentesCurriculares[1]->porcentagemPresenca = 0;
   * </code>
   *
   * Esses valores são calculados SOMENTE através das faltas já lançadas.
   *
   * @return stdClass
   * @todo Verificação de situação geral nos moldes dos componentes curriculares
   *   para falta por componente (se 0 ou diferente de componentes matrícula)
   */
  public function getSituacaoFaltas()
  {
    $presenca                           = new stdClass();
    $presenca->totalFaltas              = 0;
    $presenca->horasFaltas              = 0;
    $presenca->porcentagemFalta         = 0;
    $presenca->porcentagemPresenca      = 0;
    $presenca->porcentagemPresencaRegra = $this->getRegra()->porcentagemPresenca;

    $presenca->tipoFalta                = $this->getRegra()->get('tipoPresenca');
    $presenca->cargaHoraria             = $this->getOption('serieCargaHoraria');
    $presenca->diasLetivos              = $this->getOption('serieDiasLetivos');

    $presenca->cursoHoraFalta           = $this->getOption('cursoHoraFalta');
    $presenca->componentesCurriculares  = array();
    $presenca->situacao                 = App_Model_MatriculaSituacao::EM_ANDAMENTO;

    $etapa                              = 0;
    $faltasComponentes                  = array();

    // Carrega faltas lançadas (persistidas)
    $this->_loadFalta();

    $tipoFaltaGeral         = $presenca->tipoFalta == RegraAvaliacao_Model_TipoPresenca::GERAL;
    $tipoFaltaPorComponente = $presenca->tipoFalta == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;

    if ($tipoFaltaGeral) {
      $faltas = $this->_faltasGerais;

      if (0 == count($faltas)) {
        $total = 0;
        $etapa = 0;
      }
      else {
        $total = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
        $etapa = array_pop($faltas)->etapa;
      }
    }

    elseif ($tipoFaltaPorComponente) {
      $faltas = $this->_faltasComponentes;
      $total   = 0;
      $etapasComponentes = array();
      $faltasComponentes = array();

      foreach ($faltas as $key => $falta) {
        // Total de faltas do componente
        $componenteTotal = array_sum(CoreExt_Entity::entityFilterAttr($falta,
          'id', 'quantidade'));

        // Pega o id de ComponenteCurricular_Model_Componente da última etapa do array
        $componenteEtapa = array_pop($falta);
        $id              = $componenteEtapa->get('componenteCurricular');
        $etapa           = $componenteEtapa->etapa;

        // Etapas lançadas
        $etapasComponentes[$etapa] = $etapa;

        // Usa stdClass como interface de acesso
        $faltasComponentes[$id] = new stdClass();
        $faltasComponentes[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        $faltasComponentes[$id]->horasFaltas = NULL;
        $faltasComponentes[$id]->porcentagemFalta = NULL;
        $faltasComponentes[$id]->porcentagemPresenca = NULL;
        $faltasComponentes[$id]->total = $componenteTotal;
        //$faltasComponentes[$id]->componenteCurricular = $componenteEtapa;

        // Calcula a quantidade de horas/faltas no componente
        $faltasComponentes[$id]->horasFaltas =
          $this->_calculateHoraFalta($componenteTotal, $presenca->cursoHoraFalta);

        // Calcula a porcentagem de falta no componente
        $faltasComponentes[$id]->porcentagemFalta =
          $this->_calculatePorcentagem($this->_componentes[$id]->cargaHoraria,
            $faltasComponentes[$id]->horasFaltas, FALSE);

        // Calcula a porcentagem de presença no componente
        $faltasComponentes[$id]->porcentagemPresenca =
          100 - $faltasComponentes[$id]->porcentagemFalta;

        // Na última etapa seta situação presença como aprovado ou reprovado.
        if ($etapa == $this->getOption('etapas') || $etapa == 'Rc') {
          $aprovado = ($faltasComponentes[$id]->porcentagemPresenca >= $this->getRegra()->porcentagemPresenca);
          $faltasComponentes[$id]->situacao = $aprovado ? App_Model_MatriculaSituacao::APROVADO :
                                                          App_Model_MatriculaSituacao::REPROVADO;
        }

        // Adiciona a quantidade de falta do componente ao total geral de faltas
        $total += $componenteTotal;
      }

      if (0 == count($faltasComponentes) ||
          count($faltasComponentes) != count($this->getComponentes())) {
        $etapa = 1;
      }
      else {
        $etapa = min($etapasComponentes);
      }
    } // fim if por_componente

    $presenca->totalFaltas = $total;
    $presenca->horasFaltas = $this->_calculateHoraFalta($total, $presenca->cursoHoraFalta);

    if ($tipoFaltaGeral) {
      $presenca->porcentagemFalta = $this->_calculatePorcentagem($presenca->diasLetivos,
                                                                 $presenca->totalFaltas, FALSE);
    }
    elseif ($tipoFaltaPorComponente) {
      $presenca->porcentagemFalta = $this->_calculatePorcentagem($presenca->cargaHoraria,
                                                                 $presenca->horasFaltas, FALSE);
    }

    $presenca->porcentagemPresenca     = 100 - $presenca->porcentagemFalta;
    $presenca->componentesCurriculares = $faltasComponentes;

    // Na última etapa seta situação presença como aprovado ou reprovado.
    if ($etapa == $this->getOption('etapas') || $etapa === 'Rc') {
      $aprovado           = ($presenca->porcentagemPresenca >= $this->getRegra()->porcentagemPresenca);
      $presenca->situacao = $aprovado ? App_Model_MatriculaSituacao::APROVADO :
                                        App_Model_MatriculaSituacao::REPROVADO;
    }

    return $presenca;
  }


  /**
   * Retorna a situação dos componentes curriculares cursados pelo aluno. Possui
   * uma flag "situacao" global, que indica a situação global do aluno, podendo
   * ser:
   *
   * - Em andamento
   * - Em exame
   * - Aprovado
   * - Reprovado
   *
   * Esses valores são definidos no enum App_Model_MatriculaSituacao.
   *
   * Para cada componente curricular, será indicado a situação do aluno no
   * componente.
   *
   * Esses resultados são retornados como um objeto stdClass que possui dois
   * atributos: "situacao" e "componentesCurriculares". O primeiro é um tipo
   * inteiro e o segundo um array indexado pelo id do componente e com um
   * atributo inteiro "situacao":
   *
   * <code>
   * <?php
   * $situacao = new stdClass();
   * $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
   * $situacao->componentesCurriculares = array();
   * $situacao->componentesCurriculares[1] = new stdClass();
   * $situacao->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
   * </code>
   *
   * Esses valores são definidos através da verificação das médias dos
   * componentes curriculares já avaliados e das faltas lançadas.
   *
   * Obs: Anteriormente este metódo SOMENTE verificava a situação baseando-se nas médias lançadas,
   *      porem o mesmo foi alterado para verificar a situação baseada nas notas e faltas lançadas.
   *
   *      A implementa antiga deste metodo esta contida no metodo getSituacaoNotas
   *
   * @return stdClass|NULL Retorna NULL caso não
   * @see App_Model_MatriculaSituacao
   */
  public function getSituacaoComponentesCurriculares()
  {
    $situacao                          = new stdClass();
    $situacao->situacao                = App_Model_MatriculaSituacao::APROVADO;
    $situacao->componentesCurriculares = array();

    $situacaoNotas  = $this->getSituacaoNotas();
    $situacaoFaltas = $this->getSituacaofaltas();

    foreach($situacaoNotas->componentesCurriculares as $ccId => $situacaoNotaCc) {
      // seta tipos nota, falta
      $tipoNotaNenhum         = $this->getRegra()->get('tipoNota')  ==
                                RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

      $tipoFaltaPorComponente = $this->getRegra()->get('tipoPresenca') ==
                                RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;

      // inicializa situacaoFaltaCc a ser usado caso tipoFaltaPorComponente
      $situacaoFaltaCc           = new stdClass();
      $situacaoFaltaCc->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

      // caso possua situacaoFalta para o componente substitui situacao inicializada
      if ($tipoFaltaPorComponente and isset($situacaoFaltas->componentesCurriculares[$ccId]))
        $situacaoFaltaCc = $situacaoFaltas->componentesCurriculares[$ccId];

      // pega situação nota geral ou do componente
      if ($tipoNotaNenhum)
        $situacaoNota = $situacaoNotas->situacao;
      else
        $situacaoNota = $situacaoNotaCc->situacao;

      // pega situacao da falta componente ou geral.
      if($tipoFaltaPorComponente)
        $situacaoFalta = $situacaoFaltas->componentesCurriculares[$ccId]->situacao;
      else
        $situacaoFalta = $situacaoFaltas->situacao;

      $situacao->componentesCurriculares[$ccId] = $this->getSituacaoNotaFalta($situacaoNota, $situacaoFalta);
    }

    // #FIXME verificar porque para regras sem nota, não é retornado a situacao.

    return $situacao;
  }

  /**
   * Verifica se uma determinada situação tem prioridade sobre a outra.
   *
   * @param int $item1
   * @param int $item2
   * @return bool
   */
  protected function _situacaoPrioritaria($item1, $item2)
  {
    return ($this->_situacaoPrioridade[$item1] <= $this->_situacaoPrioridade[$item2]);
  }

  /**
   * Setter.
   * @param Avaliacao_Model_NotaAluno $nota
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  protected function _setNotaAluno(Avaliacao_Model_NotaAluno $nota)
  {
    $this->_notaAluno = $nota;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_NotaAluno|NULL
   */
  protected function _getNotaAluno()
  {
    if (!is_null($this->_notaAluno)) {
      return $this->_notaAluno;
    }

    $notaAluno = $this->getNotaAlunoDataMapper()->findAll(
      array(),
      array('matricula' => $this->getOption('matricula'))
    );

    if (0 == count($notaAluno)) {
      return NULL;
    }

    $this->_setNotaAluno($notaAluno[0]);
    return $this->_notaAluno;
  }

  /**
   * Cria e persiste uma instância de Avaliacao_Model_NotaAluno.
   * @return bool
   */
  protected function _createNotaAluno()
  {
    $notaAluno = new Avaliacao_Model_NotaAluno();
    $notaAluno->matricula = $this->getOption('matricula');
    return $this->getNotaAlunoDataMapper()->save($notaAluno);
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_NotaComponente para
   * um determinado componente curricular já persistida.
   *
   * @param int $id
   * @return bool
   */
  protected function _hasNotaComponente($id)
  {
    $notasComponentes = $this->getNotasComponentes();
    if (!isset($notasComponentes[$id])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Retorna o field identity de um componente curricular de uma instância de
   * Avaliacao_Model_NotaComponente já esteja persistida.
   *
   * @param Avaliacao_Model_NotaComponente $instance
   * @return int|NULL Retorna NULL caso a instância não tenha sido lançada
   */
  protected function _getNotaIdEtapa(Avaliacao_Model_NotaComponente $instance)
  {
    $componenteCurricular = $instance->get('componenteCurricular');

    if (!$this->_hasNotaComponente($componenteCurricular)) {
      return NULL;
    }

    $notasComponentes = $this->getNotasComponentes();
    foreach ($notasComponentes[$componenteCurricular] as $notaComponente) {
      if ($instance->etapa == $notaComponente->etapa) {
        return $notaComponente->id;
      }
    }

    return NULL;
  }

  /**
   * Verifica se o aluno tem faltas lançadas.
   * @return bool
   */
  public function hasFaltaAluno()
  {
    if (!is_null($this->_getFaltaAluno())) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_FaltaAluno $falta
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  protected function _setFaltaAluno(Avaliacao_Model_FaltaAluno $falta)
  {
    $this->_faltaAluno = $falta;
    $tipoFaltaAtual = $this->_faltaAluno->get('tipoFalta');
    $tipoFaltaRegraAvaliacao = $this->getRegra()->get('tipoPresenca');

    if ($tipoFaltaAtual != $tipoFaltaRegraAvaliacao){
      $this->_faltaAluno->tipoFalta = $tipoFaltaRegraAvaliacao;
      $this->getFaltaAlunoDataMapper()->save($this->_faltaAluno);
    }

    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_FaltaAluno|NULL
   */
  protected function _getFaltaAluno()
  {
    if (!is_null($this->_faltaAluno)) {
      return $this->_faltaAluno;
    }

    $faltaAluno = $this->getFaltaAlunoDataMapper()->findAll(
      array(),
      array('matricula' => $this->getOption('matricula'))
    );

    if (0 == count($faltaAluno)) {
      return NULL;
    }

    $this->_setFaltaAluno($faltaAluno[0]);
    return $this->_faltaAluno;
  }

  /**
   * Cria e persiste uma instância de Avaliacao_Model_NotaAluno.
   * @return bool
   */
  protected function _createFaltaAluno()
  {
    $faltaAluno = new Avaliacao_Model_FaltaAluno();
    $faltaAluno->matricula = $this->getOption('matricula');
    $faltaAluno->tipoFalta = $this->getRegra()->get('tipoPresenca');
    return $this->getFaltaAlunoDataMapper()->save($faltaAluno);
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_FaltaGeral já
   * persistida.
   *
   * @return bool
   */
  protected function _hasFaltaGeral()
  {
    $faltasGerais = $this->getFaltasGerais();
    if (0 == count($faltasGerais)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_FaltaComponente para
   * um determinado componente curricular já persistida.
   *
   * @param int $id
   * @return bool
   */
  protected function _hasFaltaComponente($id)
  {
    $faltasComponentes = $this->getFaltasComponentes();
    if (!isset($faltasComponentes[$id])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_FaltaAbstract já
   * persistida em uma determinada etapa e retorna o field identity.
   *
   * @param Avaliacao_Model_FaltaAbstract $instance
   * @return int|NULL
   */
  protected function _getFaltaIdEtapa(Avaliacao_Model_FaltaAbstract $instance)
  {
    $etapa = $instance->etapa;

    if (!is_null($instance) &&
      $this->_getFaltaAluno()->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {

      $componenteCurricular = $instance->get('componenteCurricular');

      if (!$this->_hasFaltaComponente($componenteCurricular)) {
        return NULL;
      }

      $faltasComponentes = $this->getFaltasComponentes();
      foreach ($faltasComponentes[$componenteCurricular] as $faltaComponente) {
        if ($etapa == $faltaComponente->etapa) {
          return $faltaComponente->id;
        }
      }
    }
    elseif ($this->_getFaltaAluno()->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
      if (!$this->_hasFaltaGeral()) {
        return NULL;
      }

      $faltasGerais = $this->getFaltasGerais();
      if (isset($faltasGerais[$etapa])) {
        return $faltasGerais[$etapa]->id;
      }
    }

    return NULL;
  }

  /**
   * Verifica se o aluno tem pareceres lançados.
   * @return bool
   */
  public function hasParecerDescritivoAluno()
  {
    if (!is_null($this->_getParecerDescritivoAluno())) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Setter.
   * @param Avaliacao_Model_ParecerDescritivoAluno $parecerDescritivoAluno
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  protected function _setParecerDescritivoAluno(Avaliacao_Model_ParecerDescritivoAluno $parecerDescritivoAluno)
  {
    $this->_parecerDescritivoAluno = $parecerDescritivoAluno;
    return $this;
  }

  /**
   * Getter.
   * @return Avaliacao_Model_ParecerDescritivoAluno|NULL
   */
  protected function _getParecerDescritivoAluno()
  {
    if (!is_null($this->_parecerDescritivoAluno)) {
      return $this->_parecerDescritivoAluno;
    }

    $parecerDescritivoAluno = $this->getParecerDescritivoAlunoDataMapper()->findAll(
      array(), array('matricula' => $this->getOption('matricula'))
    );

    if (0 == count($parecerDescritivoAluno)) {
      return NULL;
    }

    $this->_setParecerDescritivoAluno($parecerDescritivoAluno[0]);
    return $this->_parecerDescritivoAluno;
  }

  /**
   * Cria e persiste uma instância de Avaliacao_Model_ParecerDescritivoAluno.
   * @return bool
   */
  protected function _createParecerDescritivoAluno()
  {
    $parecerDescritivoAluno = new Avaliacao_Model_ParecerDescritivoAluno();
    $parecerDescritivoAluno->matricula         = $this->getOption('matricula');
    $parecerDescritivoAluno->parecerDescritivo = $this->getRegra()->get('parecerDescritivo');
    return $this->getParecerDescritivoAlunoDataMapper()->save($parecerDescritivoAluno);
  }

  /**
   * Adiciona um array de instâncias Avaliacao_Model_NotaComponente.
   *
   * @param array $notas
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addNotas(array $notas)
  {
    foreach ($notas as $nota) {
      $this->addNota($nota);
    }
    return $this;
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoComponente
   * persistida para o aluno.
   *
   * @param int $id Field identity de ComponenteCurricular_Model_Componente
   * @return bool
   */
  protected function _hasParecerComponente($id)
  {
    $pareceresComponentes = $this->getPareceresComponentes();
    if (!isset($pareceresComponentes[$id])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoGeral
   * persistida para o aluno.
   * @return bool
   */
  protected function _hasParecerGeral()
  {
    if (0 == count($this->getPareceresGerais())) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoAbstract
   * persistida em uma determinada etapa e retorna o field identity.
   *
   * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
   * @return int|NULL
   */
  protected function _getParecerIdEtapa(Avaliacao_Model_ParecerDescritivoAbstract $instance)
  {
    $gerais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
    );

    $componentes = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
    );

    $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');

    if (in_array($parecerDescritivo, $gerais)) {
      if (!$this->_hasParecerGeral()) {
        return NULL;
      }

      $pareceres = $this->getPareceresGerais();
    }
    elseif (in_array($parecerDescritivo, $componentes)) {
      if (!$this->_hasParecerComponente($instance->get('componenteCurricular'))) {
        return NULL;
      }

      $pareceres = $this->getPareceresComponentes();
      $pareceres = $pareceres[$instance->get('componenteCurricular')];
    }

    foreach ($pareceres as $parecer) {
      if ($instance->etapa == $parecer->etapa) {
        return $parecer->id;
      }
    }
  }

  /**
   * Adiciona notas no boletim.
   * @param Avaliacao_Model_NotaComponente $nota
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addNota(Avaliacao_Model_NotaComponente $nota)
  {
    $key = 'n_' . spl_object_hash($nota);

    $nota = $this->_addValidators($nota);
    $nota = $this->_updateEtapa($nota);

    $nota->notaArredondada = $this->arredondaNota($nota);
    $this->_notas[$key]    = $nota;

    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  public function getNotas()
  {
    return $this->_notas;
  }

  /**
   * Adiciona um array de instâncias Avaliacao_Model_FaltaAbstract no boletim.
   *
   * @param array $faltas
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addFaltas(array $faltas)
  {
    foreach ($faltas as $falta) {
      $this->addFalta($falta);
    }
    return $this;
  }

  /**
   * Adiciona faltas no boletim.
   * @param Avaliacao_Model_FaltaAbstract $falta
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addFalta(Avaliacao_Model_FaltaAbstract $falta)
  {
    $key = 'f_' . spl_object_hash($falta);

    $falta = $this->_addValidators($falta);
    $falta = $this->_updateEtapa($falta);

    $this->_faltas[$key] = $falta;
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  public function getFaltas()
  {
    return $this->_faltas;
  }

  /**
   * Adiciona uma array de instâncias de Avaliacao_Model_ParecerDescritivoAbstract
   * no boletim.
   *
   * @param array $pareceres
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addPareceres(array $pareceres)
  {
    foreach ($pareceres as $parecer) {
      $this->addParecer($parecer);
    }
    return $this;
  }

  /**
   * Adiciona uma instância de Avaliacao_Model_ParecerDescritivoAbstract no
   * boletim.
   *
   * @param Avaliacao_Model_ParecerDescritivoAbstract $parecer
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function addParecer(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
  {
    $key = 'p_' . spl_object_hash($parecer);

    $this->_pareceres[$key] = $parecer;
    $this->_updateParecerEtapa($parecer);
    $this->_addParecerValidators($parecer);

    return $this;
  }

  /**
   * Getter para as instâncias de Avaliacao_Model_ParecerDescritivoAbstract
   * adicionadas no boletim (não persistidas).
   *
   * @return array
   */
  public function getPareceres()
  {
    return $this->_pareceres;
  }

  /**
   * Atualiza as opções de validação de uma instância de
   * CoreExt_Validate_Validatable, com os valores permitidos para os atributos
   * 'componenteCurricular' e 'etapa'.
   *
   * @param CoreExt_Validate_Validatable $nota
   * @return CoreExt_Validate_Validatable
   * @todo Substituir variável estática por uma de instância {@see _updateParecerEtapa()}
   */
  protected function _addValidators(CoreExt_Validate_Validatable $validatable)
  {
    $validators = array();

    // Como os componentes serão os mesmos, fazemos cache do validador
    if (is_null($this->_validators)) {

      $componentes = $this->getComponentes();
      $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');

      // Só pode adicionar uma nota/falta para os componentes cursados
      $validators['componenteCurricular'] = new CoreExt_Validate_Choice(
        array('choices' => $componentes
      ));

      // Pode informar uma nota para as etapas
      $etapas = $this->getOption('etapas');
      $etapas = array_merge(range(1, $etapas, 1), array('Rc'));

      $validators['etapa'] = new CoreExt_Validate_Choice(
        array('choices' => $etapas
      ));

      $this->_validators = $validators;
    }

    $validators = $this->_validators;

    if ($this->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      $validatable->setValidator('componenteCurricular', $validators['componenteCurricular']);
    }
    $validatable->setValidator('etapa', $validators['etapa']);

    return $validatable;
  }

  /**
   * Atualiza as opções de validação de uma instância de
   * Avaliacao_Model_ParecerDescritivoAbstract, com os valores permitidos
   * para os atributos 'componenteCurricular' e 'etapa'.
   *
   * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
   * @return Avaliacao_Model_ParecerDescritivoAbstract
   */
  protected function _addParecerValidators(Avaliacao_Model_ParecerDescritivoAbstract $instance)
  {
    if (is_null($this->_parecerValidators)) {
      $validators = array();

      $anuais = array(
        RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
        RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
      );

      $etapas = array(
        RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
        RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
      );

      $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');

      if (in_array($parecerDescritivo, $anuais)) {
        $validators['etapa'] = new CoreExt_Validate_Choice(array(
          'choices' => array('An')
        ));
      }
      elseif (in_array($parecerDescritivo, $etapas)) {
        $etapas = $this->getOption('etapas');
        $etapas = array_merge(range(1, $etapas, 1), array('Rc'));

        $validators['etapa'] = new CoreExt_Validate_Choice(array(
          'choices' => $etapas
        ));
      }

      if ($instance instanceof Avaliacao_Model_ParecerDescritivoComponente) {
        $componentes = $this->getComponentes();
        $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');

        $validators['componenteCurricular'] = new CoreExt_Validate_Choice(array(
          'choices' => $componentes
        ));
      }

      // Armazena os validadores na instância
      $this->_parecerValidators = $validators;
    }

    $validators = $this->_parecerValidators;

    // Etapas
    $instance->setValidator('etapa', $validators['etapa']);

    // Componentes curriculares
    if ($instance instanceof Avaliacao_Model_ParecerDescritivoComponente) {
      $instance->setValidator('componenteCurricular', $validators['componenteCurricular']);
    }

    return $instance;
  }

  /**
   * Atualiza a etapa de uma instância de Avaliacao_Model_Etapa.
   *
   * @param Avaliacao_Model_Etapa $nota
   * @return Avaliacao_Model_Etapa
   */
  protected function _updateEtapa(Avaliacao_Model_Etapa $instance)
  {
    if (!is_null($instance->etapa) && $instance->isValid('etapa')) {
      return $instance;
    }

    $proximaEtapa = 1;

    // Se for falta e do tipo geral, verifica qual foi a última etapa
    if ($instance instanceof Avaliacao_Model_FaltaGeral) {
      if (0 < count($this->_faltasGerais)) {
        $etapas = CoreExt_Entity::entityFilterAttr($this->_faltasGerais, 'id', 'etapa');
        $proximaEtapa = max($etapas) + 1;
      }
    }
    // Se for nota ou falta por componente, verifica no conjunto qual a última etapa
    else {
      if ($instance instanceof Avaliacao_Model_NotaComponente) {
        $search = '_notasComponentes';
      }
      elseif ($instance instanceof Avaliacao_Model_FaltaComponente) {
        $search = '_faltasComponentes';
      }

      if (isset($this->{$search}[$instance->get('componenteCurricular')])) {
        $etapas = CoreExt_Entity::entityFilterAttr(
          $this->{$search}[$instance->get('componenteCurricular')], 'id', 'etapa'
        );

        $proximaEtapa = max($etapas) + 1;
      }
    }

    // Se ainda estiver dentro dos limites, ok
    if ($proximaEtapa <= $this->getOption('etapas')) {
      $instance->etapa = $proximaEtapa;
    }
    // Se for maior, verifica se tem recuperação e atribui etapa como 'Rc'
    elseif ($proximaEtapa > $this->getOption('etapas') &&
      $this->hasRecuperacao()) {
      $instance->etapa = 'Rc';
    }

    return $instance;
  }

  /**
   * Atualiza a etapa de uma instância de Avaliacao_Model_ParecerDescritivoAbstract
   * para a última etapa possível.
   *
   * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
   * @return Avaliacao_Model_ParecerDescritivoAbstract
   */
  protected function _updateParecerEtapa(Avaliacao_Model_ParecerDescritivoAbstract $instance)
  {
    if (!is_null($instance->etapa)) {
      return $instance;
    }

    $proximaEtapa = 1;

    $anuais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
    );

    $etapas = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
    );

    $componentes = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
    );

    $gerais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
    );

    $parecerDescritivo = $this->getRegra()->get('parecerDescritivo');
    if (in_array($parecerDescritivo, $anuais)) {
      $instance->etapa = 'An';
      return $instance;
    }
    elseif (in_array($parecerDescritivo, $etapas)) {
      $attrValues = array();

      if (in_array($parecerDescritivo, $gerais)) {
        $attrValues = $this->getPareceresGerais();
      }
      elseif (in_array($parecerDescritivo, $componentes)) {
        $pareceresComponentes = $this->getPareceresComponentes();
        if (isset($pareceresComponentes[$instance->get('componenteCurricular')])) {
          $attrValues = $pareceresComponentes[$instance->get('componenteCurricular')];
        }
      }

      if (0 < count($attrValues)) {
        $etapas = CoreExt_Entity::entityFilterAttr($attrValues, 'id', 'etapa');
        $proximaEtapa = max($etapas) + 1;
      }
    }

    if ($proximaEtapa <= $this->getOption('etapas')) {
      $instance->etapa = $proximaEtapa;
    }
    elseif ($this->hasRecuperacao()) {
      $instance->etapa = 'Rc';
    }

    return $instance;
  }

  /**
   * Arredonda uma nota através da tabela de arredondamento da regra de avaliação.
   * @param Avaliacao_Model_NotaComponente|int $nota
   * @return mixed
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public function arredondaNota($nota)
  {
    if ($nota instanceof Avaliacao_Model_NotaComponente) {
      $nota = $nota->nota;
    }

    if (!is_numeric($nota)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException(sprintf(
        'O parâmetro $nota ("%s") não é um valor numérico.', $nota
      ));
    }

    return $this->getRegra()->tabelaArredondamento->round($nota);
  }

  /**
   * Prevê a nota necessária para que o aluno seja aprovado após a recuperação
   * escolar.
   *
   * @param  int $id
   * @return TabelaArredondamento_Model_TabelaValor|NULL
   * @see    TabelaArredondamento_Model_Tabela#predictValue()
   */
  public function preverNotaRecuperacao($id)
  {
    if (is_null($this->getRegra()->formulaRecuperacao) || !isset($this->_notasComponentes[$id])) {
      return NULL;
    }

    $notas      = $this->_notasComponentes[$id];
    $somaEtapas = array_sum(CoreExt_Entity::entityFilterAttr($notas, 'etapa', 'nota'));
    $formula    = $this->getRegra()->formulaRecuperacao;

    $data = array(
      'formulaValues' => array(
        'Se' => $somaEtapas,
        'Et' => $this->getOption('etapas'),
        'Rc' => NULL
      ),
      'expected' => array(
        'var'   => 'Rc',
        'value' => $this->getRegra()->media
      )
    );

    foreach ($notas as $nota) {
      $data['formulaValues']['E' . $nota->etapa] = $nota->nota;
    }

    return $this->getRegra()->tabelaArredondamento->predictValue($formula, $data);
  }

  /**
   * @param  numeric $falta
   * @param  numeric $horaFalta
   * @return numeric
   */
  protected function _calculateHoraFalta($falta, $horaFalta)
  {
    return ($falta * $horaFalta);
  }

  /**
   * Calcula a proporção de $num2 para $num1.
   *
   * @param  numeric $num1
   * @param  numeric $num2
   * @param  bool    $decimal Opcional. Se o resultado é retornado como decimal
   *   ou percentual. O padrão é TRUE.
   * @return float
   */
  protected function _calculatePorcentagem($num1, $num2, $decimal = TRUE)
  {
    $num1 = floatval($num1);
    $num2 = floatval($num2);

    if ($num1 == 0) {
      return 0;
    }

    $perc = $num2 / $num1;
    return ($decimal == TRUE ? $perc : ($perc * 100));
  }

  /**
   * Calcula uma média de acordo com uma fórmula de FormulaMedia_Model_Media
   * da regra de avaliação da série/matrícula do aluno.
   *
   * @param array $values
   * @return float
   */
  protected function _calculaMedia(array $values)
  {
    if (isset($values['Rc']) && $this->hasRecuperacao()) {
      $media = $this->getRegra()->formulaRecuperacao->execFormulaMedia($values);
    }
    else {
      $media = $this->getRegra()->formulaMedia->execFormulaMedia($values);
    }

    return $media;
  }

  /**
   * Insere ou atualiza as notas e/ou faltas que foram adicionadas ao service
   * e atualiza a matricula do aluno de acordo com a sua performance,
   * promovendo-o ou retendo-o caso o tipo de progressão da regra de avaliação
   * seja automática (e que a situação do aluno não esteja em "andamento").
   *
   * @see Avaliacao_Service_Boletim#getSituacaoAluno()
   * @throws CoreExt_Service_Exception|Exception
   */
  public function save()
  {
    try {
      $this->saveNotas()
           ->saveFaltas()
           ->savePareceres()
           ->promover();
    }
    catch (CoreExt_Service_Exception $e) {
      throw $e;
    }
    catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * Insere ou atualiza as notas no boletim do aluno.
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function saveNotas()
  {
    if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
      return $this;
    }

    $notaAluno = $this->_getNotaAluno();
    $notas = $this->getNotas();

    foreach ($notas as $nota) {
      $nota->notaAluno = $notaAluno;
      $nota->id = $this->_getNotaIdEtapa($nota);
      $this->getNotaComponenteDataMapper()->save($nota);
    }

    // Atualiza as médias
    $this->_updateNotaComponenteMedia();

    return $this;
  }

  /**
   * Insere ou atualiza as faltas no boletim.
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function saveFaltas()
  {
    $faltaAluno = $this->_getFaltaAluno();
    $faltas = $this->getFaltas();

    foreach ($faltas as $falta) {
      $falta->faltaAluno = $faltaAluno;
      $falta->id = $this->_getFaltaIdEtapa($falta);
      $this->getFaltaAbstractDataMapper()->save($falta);
    }

    return $this;
  }

  /**
   * Insere ou atualiza os pareceres no boletim.
   * @return Avaliacao_Service_Boletim Provê interface fluída
   */
  public function savePareceres()
  {
    $parecerAluno = $this->_getParecerDescritivoAluno();
    $pareceres    = $this->getPareceres();

    foreach ($pareceres as $parecer) {
      $parecer->parecerDescritivoAluno = $parecerAluno->id;
      $parecer->id = $this->_getParecerIdEtapa($parecer);
      $this->getParecerDescritivoAbstractDataMapper()->save($parecer);
    }

    return $this;
  }

  protected function updateSituacaoMatricula($situacaoNotasFaltas)
  {
    $matriculaSituacao = null;

    //se a situacao da matricula não é aprovado e a situacao da nota e falta estão aprovadas, aprova a matricula
    if (($this->getOption('aprovado') != App_Model_MatriculaSituacao::APROVADO) &&
        ($situacaoNotasFaltas->aprovado && ! $situacaoNotasFaltas->andamento))
    {
      $matriculaSituacao = App_Model_MatriculaSituacao::APROVADO;
    }
    //se a situacao da matricula não é reprovado e a situacao da nota e falta não estão aprovadas e nem em andamento, reprova a matricula
    elseif (($this->getOption('aprovado') != App_Model_MatriculaSituacao::REPROVADO) &&
            (! $situacaoNotasFaltas->aprovado && ! $situacaoNotasFaltas->andamento))
    {
      $matriculaSituacao = App_Model_MatriculaSituacao::REPROVADO;
    }
    elseif ($this->getOption('aprovado') != App_Model_MatriculaSituacao::EM_ANDAMENTO)
    {
      $matriculaSituacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    }

    if ($matriculaSituacao)
      $this->_updateMatricula($this->getOption('matricula'), $this->getOption('usuario'), $matriculaSituacao);
  }

  /**
   * Promove o aluno de etapa escolar caso esteja aprovado de acordo com o
   * necessário estabelecido por tipoProgressao de
   * RegraAvaliacao_Model_Regra.
   *
   * @param bool $ok Caso a progressão não seja automática, é necessário uma
   *   confirmação externa para a promoção do aluno.
   * @return bool
   */

  public function promover($novaSituacaoMatricula = NULL)
  {
    $tipoProgressao = $this->getRegra()->get('tipoProgressao');
    $situacaoMatricula = $this->getOption('aprovado');
    $situacaoBoletim = $this->getSituacaoAluno();
    $exceptionMsg = '';

    if ($situacaoBoletim->andamento)
        $novaSituacaoMatricula = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    else
    {

      switch ($tipoProgressao) {
        case RegraAvaliacao_Model_TipoProgressao::CONTINUADA:

          $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
          break;

        case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA:

          if ($situacaoBoletim->aprovado && !$situacaoBoletim->retidoFalta)
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
          else
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
          break;

        case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_SOMENTE_MEDIA:

          if ($situacaoBoletim->aprovado)
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
          else
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;

          break;

        case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL && is_null($novaSituacaoMatricula):

          $tipoProgressaoInstance = RegraAvaliacao_Model_TipoProgressao::getInstance();
          $exceptionMsg = sprintf('Para atualizar a matrícula em uma regra %s é '
                                  . 'necessário passar o valor do argumento "$novaSituacaoMatricula".',
                                  $tipoProgressaoInstance->getValue($tipoProgressao));
          break;
      }
    }

    if($novaSituacaoMatricula == $situacaoMatricula)
      $exceptionMsg = "Matrícula ({$this->getOption('matricula')}) não precisou ser promovida, " .
                      "pois a nova situação continua a mesma da anterior ($novaSituacaoMatricula)";

    if ($exceptionMsg) {
      require_once 'CoreExt/Service/Exception.php';
      throw new CoreExt_Service_Exception($exceptionMsg);
    }

    return $this->_updateMatricula($this->getOption('matricula'), $this->getOption('usuario'), $novaSituacaoMatricula);
  }


  /**
   * Atualiza a média dos componentes curriculares.
   */
  protected function _updateNotaComponenteMedia()
  {
    require_once 'Avaliacao/Model/NotaComponenteMedia.php';
    $this->_loadNotaComponenteCurricular(FALSE);

    foreach ($this->_notasComponentes as $id => $notasComponentes) {
      // Cria um array onde o índice é a etapa
      $etapasNotas = CoreExt_Entity::entityFilterAttr($notasComponentes, 'etapa', 'nota');
      $notas = array('Se' => 0, 'Et' => $this->getOption('etapas'));

      // Cria o array formatado para o cálculo da fórmula da média
      foreach ($etapasNotas as $etapa => $nota) {
        if (is_numeric($etapa)) {
          $notas['E' . $etapa] = $nota;
          $notas['Se'] += $nota;
          continue;
        }
        $notas[$etapa] = $nota;
      }

      // Calcula a média
      $media = $this->_calculaMedia($notas);

      // Cria uma nova instância de média, já com a nota arredondada e a etapa
      $notaComponenteCurricularMedia = new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno' => $this->_getNotaAluno()->id,
        'componenteCurricular' => $id,
        'media' => $media,
        'mediaArredondada' => $this->arredondaNota($media),
        'etapa' => $etapa
      ));

      try {
        // Se existir, marca como "old" para possibilitar a atualização
        $this->getNotaComponenteMediaDataMapper()->find(array(
          $notaComponenteCurricularMedia->get('notaAluno'),
          $notaComponenteCurricularMedia->get('componenteCurricular'),
        ));

        $notaComponenteCurricularMedia->markOld();
      }
      catch (Exception $e) {
        // Prossegue, sem problemas.
      }

      // Salva a média
      $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
    }
  }

  /**
   * Atualiza os dados da matrícula do aluno.
   *
   * @param int $matricula
   * @param int $usuario
   * @param bool $promover
   * @return bool
   * @see App_Model_Matricula#atualizaMatricula($matricula, $usuario, $promover)
   */
  protected function _updateMatricula($matricula, $usuario, $promover)
  {
    return App_Model_Matricula::atualizaMatricula($matricula, $usuario, $promover);
  }


  public function deleteNota($etapa, $ComponenteCurricularId)
  {
    // zera nota antes de deletar, para que a media seja recalculada
    try {
      $nota = new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => $ComponenteCurricularId,
        'nota' => 0,
        'etapa' => $etapa
      ));
      $this->addNota($nota);
      $this->save();
    }
    catch (Exception $e) {
      error_log("Excessao ignorada ao zerar nota a ser removida: " . $e->getMessage());
    }

    $nota = $this->getNotaComponente($ComponenteCurricularId, $etapa);
    $this->getNotaComponenteDataMapper()->delete($nota);

    return $this;
  }


  public function deleteFalta($etapa, $ComponenteCurricularId)
  {
    $nota = $this->getFalta($etapa, $ComponenteCurricularId);
    $this->getFaltaAbstractDataMapper()->delete($nota);

    return $this;
  }


  public function deleteParecer($etapa, $ComponenteCurricularId)
  {
    $parecer = $this->getParecerDescritivo($etapa, $ComponenteCurricularId);
    $this->getParecerDescritivoAbstractDataMapper()->delete($parecer);

    return $this;
  }

}
