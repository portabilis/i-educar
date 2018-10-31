<?php

use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use iEducar\Modules\Stages\Exceptions\StagesNotInformedByCoordinatorException;
use iEducar\Modules\Stages\Exceptions\StagesNotInformedByTeacherException;

require_once 'CoreExt/Configurable.php';
require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'App/Model/Matricula.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once  'ComponenteCurricular/Model/TipoNotaComponenteSerie.php';

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
   * @var Avaliacao_Model_NotaGeralAbstractDataMapper
   */
  protected $_notaGeralAbstractDataMapper = NULL;

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
   * Notas do aluno por etapa.
   * @var array
   */
  protected $_notasGerais = array();

  /**
   * Médias do aluno nos componentes cursados.
   * @var array
   */
  protected $_mediasComponentes = array();

 /**
   * Média geral do aluno.
   * @var array
   */
  protected $_mediasGerais = array();

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

  private $notaLancada = NULL;

  private $_currentComponenteCurricular = NULL;

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
         ->_loadNotas()
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

    if (isset($options['NotaGeralAbstractDataMapper'])) {
      $this->setNotaGeralAbstractDataMapper($options['NotaGeralAbstractDataMapper']);
      unset($options['NotaGeralAbstractDataMapper']);
    }

    if (isset($options['NotaGeralDataMapper'])) {
      $this->setNotaGeralDataMapper($options['NotaGeralDataMapper']);
      unset($options['NotaGeralDataMapper']);
    }

    if (isset($options['MediaGeralDataMapper'])) {
      $this->setMediaGeralDataMapper($options['MediaGeralDataMapper']);
      unset($options['MediaGeralDataMapper']);
    }

    if (isset($options['componenteCurricularId'])) {
      $this->setComponenteCurricularId($options['componenteCurricularId']);
      unset($options['componenteCurricularId']);
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
   * @param $id
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setComponenteCurricularId($componenteCurricularId)
  {
    $this->_componenteCurricularId = $componenteCurricularId;
    return $this;
  }

  /**
   * Getter.
   * @return int
   */
  public function getComponenteCurricularId()
  {
    return $this->_componenteCurricularId;
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
   * Getter.
   * @return Avaliacao_Model_NotaGeralDataMapper
   */
  public function getNotaGeralDataMapper()
  {
    if (is_null($this->_notaGeralDataMapper)) {
      require_once 'Avaliacao/Model/NotaGeralDataMapper.php';
      $this->setNotaGeralDataMapper(new Avaliacao_Model_NotaGeralDataMapper());
    }
    return $this->_notaGeralDataMapper;
  }

  /**
   * Setter.
   * @param setNotaGeralDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setNotaGeralDataMapper(Avaliacao_Model_NotaGeralDataMapper $mapper)
  {
    $this->_notaGeralDataMapper = $mapper;
    return $this;
  }

     /**
   * Getter.
   * @return Avaliacao_Model_NotaGeralDataMapper
   */
  public function getMediaGeralDataMapper()
  {
    if (is_null($this->_mediaGeralDataMapper)) {
      require_once 'Avaliacao/Model/MediaGeralDataMapper.php';
      $this->setMediaGeralDataMapper(new Avaliacao_Model_MediaGeralDataMapper());
    }
    return $this->_mediaGeralDataMapper;
  }

  /**
   * Setter.
   * @param setMediaGeralDataMapper $mapper
   * @return App_Service_Boletim Provê interface fluída
   */
  public function setMediaGeralDataMapper(Avaliacao_Model_MediaGeralDataMapper $mapper)
  {
    $this->_mediaGeralDataMapper = $mapper;
    return $this;
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

  public function setNotaGeralAbstractDataMapper(Avaliacao_Model_NotaGeralDataMapper $mapper)
  {
    $this->_notaGeralAbstractDataMapper = $mapper;
    return $this;
  }

  public function getNotaGeralAbstractDataMapper()
  {

    if (is_null($this->_notaGeralAbstractDataMapper)) {

      $filename = 'Avaliacao/Model/NotaGeralDataMapper.php';
      $class    = 'Avaliacao_Model_NotaGeralDataMapper';

      require_once $filename;

      $this->setNotaGeralAbstractDataMapper(new $class());

    }

    return $this->_notaGeralAbstractDataMapper;
  }

  /**
   * Retorna as instâncias de Avaliacao_Model_NotaComponente do aluno.
   * @return array
   */
  public function getNotasComponentes()
  {
    return $this->_notasComponentes;
  }

  public function getNotasGerais()
  {
    return $this->_notasGerais;
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
   * Retorna as instâncias de Avaliacao_Model_MediaGeral do aluno.
   * @return array
   */
  public function getMediasGerais()
  {
    return $this->_mediasGerais;
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

  public function getMediaComponente($id)
  {
    $componentes = $this->getMediasComponentes();

    if (!isset($componentes[$id])) {
      return NULL;
    }

    $mediaComponente = $componentes[$id];

    return $mediaComponente[0];
  }

  /**
   * Retorna uma instância de Avaliacao_Model_NotaGeral.
   *
   * @param int $id O identificador de ComponenteCurricular_Model_Componente
   * @param int $etapa A etapa para o qual a nota foi lançada
   * @return Avaliacao_Model_NotaComponente|NULL
   */
  public function getNotaGeral($etapa = 1)
  {
    $notasGerais = $this->getNotasGerais();

    foreach ($notasGerais as $nota) {
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

    $pareceres = [];

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

    $matricula = App_Model_IedFinder::getMatricula($codMatricula);

    $etapas = App_Model_IedFinder::getQuantidadeDeModulosMatricula($codMatricula, $matricula);
    $etapaAtual = $_GET['etapa'] == 'Rc' ? $etapas : $_GET['etapa'];

    $this->_setRegra(App_Model_IedFinder::getRegraAvaliacaoPorMatricula(
            $codMatricula, $this->getRegraDataMapper(), $matricula
           ));

    $this->_setComponentes(App_Model_IedFinder::getComponentesPorMatricula($codMatricula, $this->getComponenteDataMapper(), $this->getComponenteTurmaDataMapper(), $this->getComponenteCurricularId(), $etapaAtual, null, $matricula));

    $this->setOption('matriculaData',     $matricula);
    $this->setOption('aprovado',          $matricula['aprovado']);
    $this->setOption('cursoHoraFalta',    $matricula['curso_hora_falta']);
    $this->setOption('cursoCargaHoraria', $matricula['curso_carga_horaria']);
    $this->setOption('serieCargaHoraria', $matricula['serie_carga_horaria']);
    $this->setOption('serieDiasLetivos',  $matricula['serie_dias_letivos']);
    $this->setOption('ref_cod_turma',     $matricula['ref_cod_turma']);
    $this->setOption('etapas',            $etapas);
    $this->setOption('etapaAtual',            $etapaAtual);

    return $this;
  }

  /**
   * Carrega todas as notas e médias já lançadas para a matrícula atual.
   *
   * @param bool $loadMedias FALSE caso não seja necessário carregar as médias
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadNotas($loadMedias = TRUE)
  {
    // Cria uma entrada no boletim caso o aluno/matrícula não a tenha
    if (!$this->hasNotaAluno()) {
      $this->_createNotaAluno();
    }

    // Se não tiver, vai criar
    $notaAluno = $this->_getNotaAluno();

    $notas = $this->getNotaComponenteDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC')
    );

    // Separa cada nota em um array indexado pelo identity do componente
    $notasComponentes = array();
    foreach ($notas as $nota) {
      $notasComponentes[$nota->get('componenteCurricular')][] = $nota;
    }

    //Carrega as notas indexadas pela etapa
    $notasGerais = array();
    $notas = $this->getNotaGeralDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC')
    );

    foreach($notas as $nota){
      $notasGerais[$nota->get('etapa')] = $nota;
    }

    $this->_notasComponentes = $notasComponentes;
    $this->_notasGerais = $notasGerais;

    if (FALSE == $loadMedias) {
      return $this;
    }

    return $this->_loadMedias();
  }

  /**
   * Carrega as médias dos componentes curriculares já lançadas.
   * @return App_Service_Boletim Provê interface fluída
   */
  protected function _loadMedias()
  {
    $notaAluno = $this->_getNotaAluno();

    $medias = $this->getNotaComponenteMediaDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id)
    );

    $mediasComponentes = array();
    foreach ($medias as $media) {
      $mediasComponentes[$media->get('componenteCurricular')][] = $media;
    }

    $mediasGerais = array();

    $mediasGerais = $this->getMediaGeralDataMapper()->findAll(
      array(), array('notaAluno' => $notaAluno->id)
    );

    foreach ($mediasGerais as $mediaGeral) {
      $mediasGerais = $mediaGeral;
    }

    $this->_mediasComponentes = $mediasComponentes;
    $this->_mediasGerais = $mediasGerais;

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

  public function getQtdComponentes(){
    $codMatricula = $this->getOption('matricula');
    $matriculaData = $this->getOption('matriculaData');

    return count(App_Model_IedFinder::getComponentesPorMatricula($codMatricula, $this->getComponenteDataMapper(), $this->getComponenteTurmaDataMapper(),null,null,null, $matriculaData, false ));
  }

  function getSituacaoNotaFalta($flagSituacaoNota, $flagSituacaoFalta)
  {
    $situacao                          = new stdClass();
    $situacao->situacao                = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $situacao->aprovado                = TRUE;
    $situacao->andamento               = FALSE;
    $situacao->recuperacao             = FALSE;
    $situacao->aprovadoComDependencia  = FALSE;
    $situacao->retidoFalta             = FALSE;

    switch ($flagSituacaoNota) {
      case App_Model_MatriculaSituacao::EM_ANDAMENTO:
        $situacao->aprovado  = FALSE;
        $situacao->andamento = TRUE;
        break;
      case App_Model_MatriculaSituacao::APROVADO_APOS_EXAME:
        $situacao->recuperacao = TRUE;
        break;
      case App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA:
        $situacao->aprovadoComDependencia = TRUE;
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
        $andamento = FALSE;
        if ($this->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            // Mesmo se reprovado por falta, só da a situação final após o lançamento de todas as notas
            $situacoesFinais = array(App_Model_MatriculaSituacao::REPROVADO, App_Model_MatriculaSituacao::APROVADO, App_Model_MatriculaSituacao::APROVADO_APOS_EXAME);
            $andamento = (in_array($flagSituacaoNota, $situacoesFinais)) ? FALSE : TRUE;
        }
        $situacao->andamento = FALSE;
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

    elseif (! $situacao->andamento and $situacao->aprovado and $situacao->aprovadoComDependencia)
      $situacao->situacao = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;

    elseif (! $situacao->andamento and (!$situacao->aprovado || $situacao->retidoFalta))
        $situacao->situacao = App_Model_MatriculaSituacao::REPROVADO;

    elseif (! $situacao->andamento and $situacao->aprovado)
      $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;

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

    $infosMatricula = $this->getOption('matriculaData');
    $matriculaId = $infosMatricula['cod_matricula'];

    // Carrega as médias pois este método pode ser chamado após a chamada a saveNotas()
    $mediasComponentes = $this->_loadMedias()
                              ->getMediasComponentes();
    $componentes = $this->getComponentes();
    $mediasComponentes = array_intersect_key($mediasComponentes, $componentes);

    $disciplinaDispensadaTurma = clsPmieducarTurma::getDisciplinaDispensada($this->getOption('ref_cod_turma'));

    // A situação é "aprovado" por padrão
    $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;

    if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
      return $situacao;
    }

    if($this->getRegra()->get('notaGeralPorEtapa') == "1"){

       $mediaGeral = $this->getMediasGerais();

      if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
        $media = $mediaGeral->mediaArredondada;
      }
      else {
        $media = $mediaGeral->media;
      }

      $etapa = $mediaGeral->etapa;

      if ($etapa == $this->getOption('etapas') && $media < $this->getRegra()->media && $this->hasRecuperacao()) {
        $situacaoGeral = App_Model_MatriculaSituacao::EM_EXAME;
      }
      elseif ($etapa == $this->getOption('etapas') && $media < $this->getRegra()->media) {
        $situacaoGeral = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ($etapa == 'Rc' && $media < $this->getRegra()->mediaRecuperacao) {
        $situacaoGeral = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ($etapa == 'Rc' && $media >= $this->getRegra()->mediaRecuperacao && $this->hasRecuperacao()) {
        $situacaoGeral = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
      }
      elseif ($etapa < $this->getOption('etapas') && $etapa != 'Rc') {
        $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
      }
      else {
        $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;
      }

      foreach($mediasComponentes as $id => $mediaComponente){
        $situacao->componentesCurriculares[$id] = new stdClass();
        $situacao->componentesCurriculares[$id]->situacao = $situacaoGeral;
      }

      $situacao->situacao = $situacaoGeral;

      return $situacao;
    }

    if(is_numeric($disciplinaDispensadaTurma)){
      unset($componentes[$disciplinaDispensadaTurma]);
      unset($mediasComponentes[$disciplinaDispensadaTurma]);
    }

    // Se não tiver nenhuma média ou a quantidade for diferente dos componentes
    // curriculares da matrícula, ainda está em andamento
    if ((0 == count($mediasComponentes) || count($mediasComponentes) != count($componentes))
         && $this->getRegra()->get('definirComponentePorEtapa') != "1") {
      $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    }

    $totalComponentes = $this->getQtdComponentes();

    if ((0 == count($mediasComponentes) || count($mediasComponentes) < $totalComponentes)
         && $this->getRegra()->get('definirComponentePorEtapa') == "1"){
      $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    }

    $qtdComponenteReprovado = 0;
    $qtdComponentes = 0;
    $somaMedias = 0;
    $media = 0;
    $turmaId = $this->getOption('ref_cod_turma');
    foreach ($mediasComponentes as $id => $mediaComponente) {

      $etapa = $mediaComponente[0]->etapa;
      $qtdComponentes++;
      $somaMedias += $media;
      $totalEtapas = $this->getOption('etapas');

      if ($this->getRegra()->get('definirComponentePorEtapa') == "1") {
        $etapaEspecifica = App_Model_IedFinder::getUltimaEtapaComponente($turmaId, $id);

        if ($etapaEspecifica) {
          $totalEtapasComponente = $etapaEspecifica;
        }
      }
      else{
        $totalEtapasComponente = $totalEtapas;
      }

      if($this->getRegra()->get('tipoProgressao') == RegraAvaliacao_Model_TipoProgressao::CONTINUADA){

        $getCountNotaCC = App_Model_IedFinder::verificaSeExisteNotasComponenteCurricular($matriculaId, $id);

        if($getCountNotaCC[0]['cc'] == 0) $etapa = 0;

        if ($etapa < $totalEtapasComponente && (string)$etapa != 'Rc'){
          $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        }else{
          $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO;
        }
        if ($etapa < $totalEtapas && (string)$etapa != 'Rc'){
          $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        }else{
          $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;
        }
        continue;
      }

      if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
        $media = $mediaComponente[0]->mediaArredondada;
      }
      else {
        $media = $mediaComponente[0]->media;
      }

      $situacaoAtualComponente = $mediaComponente[0]->situacao;

      $permiteSituacaoEmExame = TRUE;

      if ($situacaoAtualComponente == App_Model_MatriculaSituacao::REPROVADO ||
          $situacaoAtualComponente == App_Model_MatriculaSituacao::APROVADO) {
        $permiteSituacaoEmExame = FALSE;
      }

      if ($etapa == $totalEtapasComponente && $media < $this->getRegra()->media && $this->hasRecuperacao() && $permiteSituacaoEmExame) {

        // lets make some changes here >:)
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

        if($this->getRegra()->reprovacaoAutomatica){
          if(!is_numeric($this->preverNotaRecuperacao($id))){
            $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
            $qtdComponenteReprovado++;
          }
        }
      }
      elseif ($etapa == $totalEtapasComponente && $media < $this->getRegra()->media) {
        $qtdComponenteReprovado++;
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ((string)$etapa == 'Rc' && $media < $this->getRegra()->mediaRecuperacao) {
        $qtdComponenteReprovado++;
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
      }
      elseif ((string)$etapa == 'Rc' && $media >= $this->getRegra()->mediaRecuperacao && $this->hasRecuperacao()) {
        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
      }
      elseif ($etapa < $totalEtapasComponente && (string)$etapa != 'Rc') {
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

    $matricula = $this->getOption('matriculaData');
    $serie = App_Model_IedFinder::getSerie($matricula['ref_ref_cod_serie']);
    $instituicao = App_Model_IedFinder::getInstituicao($matricula['ref_cod_instituicao']);

    $anoConcluinte = ($serie['concluinte'] == 2);
    $reprovaAnoConcluinte = dbBool($instituicao['reprova_dependencia_ano_concluinte']);

    $aprovaDependencia = !($reprovaAnoConcluinte && $anoConcluinte);
    $aprovaDependencia = ($aprovaDependencia && !dbBool($matricula['dependencia']));
    $aprovaDependencia = ($aprovaDependencia && $situacaoGeral == App_Model_MatriculaSituacao::REPROVADO);
    $aprovaDependencia = ($aprovaDependencia && $qtdComponenteReprovado <= $this->getRegra()->get('qtdDisciplinasDependencia'));

    if ($aprovaDependencia) {
      $situacaoGeral = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
    }

    if($situacaoGeral == App_Model_MatriculaSituacao::REPROVADO
        && $this->getRegra()->get('aprovaMediaDisciplina')
        && ($somaMedias / $qtdComponentes) >= $this->getRegra()->mediaRecuperacao){

      $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;

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

    $componentes = $this->getComponentes();

    $disciplinaDispensadaTurma = clsPmieducarTurma::getDisciplinaDispensada($this->getOption('ref_cod_turma'));

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
      $faltas = array_intersect_key($faltas, $componentes);
      $total  = 0;
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


      if(is_numeric($disciplinaDispensadaTurma)){
        unset($componentes[$disciplinaDispensadaTurma]);
        unset($faltasComponentes[$disciplinaDispensadaTurma]);
      }
      if (0 == count($faltasComponentes) ||
        count($faltasComponentes) != count($componentes)) {
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

      // inicializa 0FaltaCc a ser usado caso tipoFaltaPorComponente
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
   * Verifica se existe uma nota geral lançada com aquele id
   *
   * @param int $id
   * @return bool
   */
  protected function _hasNotaGeral($id)
  {
    $notasGerais = $this->getNotasGerais();
    if (!isset($notasGerais[$id])) {
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
   * Retorna o id de uma nota já lançada, retorna null caso não seja encontrada
   */
  protected function _getNotaGeralIdEtapa(Avaliacao_Model_NotaGeral $instance)
  {
    $notasGerais = $this->getNotasGerais();
    foreach ($notasGerais as $notaGeral) {
      if ($instance->etapa == $notaGeral->etapa) {
        return $notaGeral->id;
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
    $this->setCurrentComponenteCurricular($nota->get('componenteCurricular'));

    $key = 'n_' . spl_object_hash($nota);

    $nota = $this->_addValidators($nota);
    $nota = $this->_updateEtapa($nota);
    $nota->notaArredondada = $this->arredondaNota($nota);
    $this->_notas[$key]    = $nota;

    $this->notaLancada = $nota;

    return $this;
  }

  public function addNotaGeral(Avaliacao_Model_NotaGeral $notaGeral)
  {
    $key = 'ng_' . spl_object_hash($notaGeral);

    $notaGeral = $this->_addValidators($notaGeral);
    $notaGeral = $this->_updateEtapa($notaGeral);

    $notaGeral->notaArredondada = $this->arredondaNota($notaGeral);
    $this->_notas[$key]    = $notaGeral;

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

    if ($validatable instanceof Avaliacao_Model_NotaComponente || $this->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
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
    if (!is_null($instance->etapa)) {
      if($instance->isValid('etapa')){
        return $instance;
      }else{
        throw new CoreExt_Exception_InvalidArgumentException('A etapa informada é inválida.');
      }
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
      if($instance->isValid('etapa')){
        return $instance;
      }else{
        throw new CoreExt_Exception_InvalidArgumentException('A etapa informada é inválida.');
      }
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
   $componenteId = $nota->get('componenteCurricular');

   if (($nota instanceof Avaliacao_Model_NotaComponente) || ($nota instanceof Avaliacao_Model_NotaGeral)) {
       $nota = $nota->nota;
   }

    if (!is_numeric($nota)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException(sprintf(
        'O parâmetro $nota ("%s") não é um valor numérico.', $nota
      ));
    }

    if ($this->usaTabelaArredondamentoConceitual($componenteId)) {
        return $this->getRegra()->tabelaArredondamentoConceitual->round($nota, 1);
    }

    return $this->getRegra()->tabelaArredondamento->round($nota, 1);
  }

  public function regraUsaTipoNotaNumericaConceitual()
  {
      if ($this->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
          return true;
      }

      return false;
  }

  public function componenteUsaNotaConceitual($componenteId)
  {
      $serieId = $this->_options['matriculaData'][ref_ref_cod_serie];
      $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($componenteId, $serieId);

      if ($tipoNota == ComponenteSerie_Model_TipoNota::CONCEITUAL) {
          return true;
      }

      return false;
    }

  public function usaTabelaArredondamentoConceitual ($componenteId)
  {
      return $this->regraUsaTipoNotaNumericaConceitual() && $this->componenteUsaNotaConceitual($componenteId);
  }

  /**
   * Arredonda uma nota através da tabela de arredondamento da regra de avaliação.
   * @param Avaliacao_Model_NotaComponente|int $nota
   * @return mixed
   * @throws CoreExt_Exception_InvalidArgumentException
   */
  public function arredondaMedia($media)
  {
    $componenteId = $this->getCurrentComponenteCurricular();

    if ($media instanceof Avaliacao_Model_NotaComponenteMedia) {
      $media = $media->nota;
    }

    if (!is_numeric($media)) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException(sprintf(
        'O parâmetro $media ("%s") não é um valor numérico.', $media
      ));
    }

    if ($this->usaTabelaArredondamentoConceitual($componenteId)) {
        return $this->getRegra()->tabelaArredondamentoConceitual->round($media, 2);
    }

    return $this->getRegra()->tabelaArredondamento->round($media, 2);
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

    unset($notas[$this->getOption('etapas')]);

    $somaEtapas = array_sum(CoreExt_Entity::entityFilterAttr($notas, 'etapa', 'nota'));

    $formula    = $this->getRegra()->formulaRecuperacao;

    $data = array(
        'Se' => $somaEtapas,
        'Et' => $this->getOption('etapas'),
        'Rc' => NULL );

    foreach ($notas as $nota) {
      $data['E' . $nota->etapa] = $nota->nota;
    }

    $data = $this->_calculateNotasRecuperacoesEspecificas($id, $data);

    $increment = 0.1;
    $notaMax = $this->getRegra()->notaMaximaExameFinal;

    if($this->getRegra()->qtdCasasDecimais == 0)
      $increment = 1;

    // Definida varíavel de incremento e nota máxima, vai testando notas de Recuperação até que o resultado
    // da média seja superior a média de aprovação de recuperação
    for($i = $increment ; $i <= $notaMax; $i = round($i+$increment, 1)){
      $data['Rc']=$i;
      if ($this->getRegra()->formulaRecuperacao->execFormulaMedia($data) >= $this->getRegra()->mediaRecuperacao)
        return $i;
    }

    return null;
  }

  /**
   * Recupera notas e calcula variáveis relacionadas com as recuperações específicas
   *
   * @param  int $id
   * @return array $data
   */

  protected function _calculateNotasRecuperacoesEspecificas($id, $data = array()){
    // Verifica regras de recuperações (Recuperações específicas por etapa)
    $regrasRecuperacoes = $this->getRegra()->findRegraRecuperacao();

    $cont = 0;
    foreach ($regrasRecuperacoes as $key => $_regraRecuperacao) {
      $cont++;
      $notaRecuperacao = $this->getNotaComponente($id, $_regraRecuperacao->getLastEtapa());
      if($notaRecuperacao && is_numeric($notaRecuperacao->notaRecuperacaoEspecifica)){
        // Caso tenha nota de recuperação para regra atual, atribuí variável RE+N
        $data['RSP'.$cont] = $notaRecuperacao->notaRecuperacaoEspecifica;
        $notaRecuperacao->notaRecuperacaoEspecifica;

        $somaEtapasRecuperacao = 0;
        $countEtapasRecuperacao = 0;

        foreach ($_regraRecuperacao->getEtapas() as $__etapa){
          $somaEtapasRecuperacao += $data['E' . $__etapa];
          $countEtapasRecuperacao++;
        }

        $mediaEtapasRecuperacao = $somaEtapasRecuperacao / $countEtapasRecuperacao;
        $mediaEtapasRecuperacaoComRecuperacao = ($mediaEtapasRecuperacao + $notaRecuperacao->notaRecuperacaoEspecifica) / 2;
        $substituiMenorNota = (bool)$_regraRecuperacao->substituiMenorNota;

        // Caso média com recuperação seja maior que média das somas das etapas sem recuperação, atribuí variável MRE+N
        if(!$substituiMenorNota || $mediaEtapasRecuperacaoComRecuperacao > $mediaEtapasRecuperacao)
          $data['RSPM'.$cont] = $mediaEtapasRecuperacaoComRecuperacao;
        else
          $data['RSPM'.$cont] = $mediaEtapasRecuperacao;

        // Caso nota de recuperação seja maior que soma das etapas, atribuí variável SRE+N
        if(!$substituiMenorNota || $notaRecuperacao->notaRecuperacaoEspecifica > $somaEtapasRecuperacao)
          $data['RSPS'.$cont] = $notaRecuperacao->notaRecuperacaoEspecifica;
        else
          $data['RSPS'.$cont] = $somaEtapasRecuperacao;

      }else{
        // Caso tenha nota de recuperação para regra atual, atribuí variaveis RSPM+N E RSPS+N
        // considerando apenas soma das etapas
        $somaEtapasRecuperacao = 0;
        $countEtapasRecuperacao = 0;

        foreach ($_regraRecuperacao->getEtapas() as $__etapa){
          $somaEtapasRecuperacao += $data['E' . $__etapa];
          $countEtapasRecuperacao++;
        }

        $data['RSPM'.$cont] = $somaEtapasRecuperacao / $countEtapasRecuperacao;
        $data['RSPS'.$cont] = $somaEtapasRecuperacao;
      }
    }

    return $data;
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

  public function setCurrentComponenteCurricular($componenteId){
    $this->_currentComponenteCurricular = $componenteId;
  }

  public function getCurrentComponenteCurricular(){
    return $this->_currentComponenteCurricular;
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
      if($nota instanceof Avaliacao_Model_NotaComponente){
        $nota->id = $this->_getNotaIdEtapa($nota);
        $this->getNotaComponenteDataMapper()->save($nota);

      }elseif($nota instanceof Avaliacao_Model_NotaGeral){
         $nota->id = $this->_getNotaGeralIdEtapa($nota);
         $this->getNotaGeralDataMapper()->save($nota);
      }
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

  protected function reloadComponentes(){
    $this->_setComponentes(
        App_Model_IedFinder::getComponentesPorMatricula(
            $this->getOption('matricula'),
            $this->getComponenteDataMapper(),
            $this->getComponenteTurmaDataMapper(),
            null,
            $this->getOption('etapaAtual'),
            null,
            $this->getOption('matriculaData')
        )
    );
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
    // Essa função é necessária para promoção pois precisamos considerar a
    // situação de todas as disciplinas e não só da que está sendo lançada
    $this->reloadComponentes();
    $tipoProgressao = $this->getRegra()->get('tipoProgressao');
    $situacaoMatricula = $this->getOption('aprovado');
    $situacaoBoletim = $this->getSituacaoAluno();
    $exceptionMsg = '';

    if ($situacaoMatricula == App_Model_MatriculaSituacao::TRANSFERIDO) {
        $novaSituacaoMatricula = App_Model_MatriculaSituacao::TRANSFERIDO;
    } elseif ($situacaoBoletim->andamento) {
        $novaSituacaoMatricula = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    } else {

      switch ($tipoProgressao) {
        case RegraAvaliacao_Model_TipoProgressao::CONTINUADA:

          $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
          break;

        case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA:

          if ($situacaoBoletim->aprovado && !$situacaoBoletim->retidoFalta && $situacaoBoletim->aprovadoComDependencia)
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
          elseif ($situacaoBoletim->aprovado && !$situacaoBoletim->retidoFalta)
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
          elseif ($situacaoBoletim->retidoFalta)
            if (!$situacaoBoletim->aprovado){
              $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
            } else {
              $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS;
            }
          else
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
          break;

        case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_SOMENTE_MEDIA || RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL:

        if ($situacaoBoletim->aprovado && $situacaoBoletim->aprovadoComDependencia)
          $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
        elseif ($situacaoBoletim->retidoFalta)
          if (!$situacaoBoletim->aprovado){
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
          } else {
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS;
          }
        elseif (!$situacaoBoletim->aprovado)
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
        else
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;

          break;

        case is_null($novaSituacaoMatricula):

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


  public function updateMediaComponente($media, $componente, $etapa){
    $notaComponenteCurricularMedia = new Avaliacao_Model_NotaComponenteMedia(array(
      'notaAluno' => $this->_getNotaAluno()->id,
      'componenteCurricular' => $componente,
      'media' => $media,
      'mediaArredondada' => $this->arredondaMedia($media),
      'etapa' => $etapa,
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
    $notaComponenteCurricularMedia->situacao = $this->getSituacaoComponentesCurriculares()->componentesCurriculares[$componente]->situacao;

    // Salva a média
    $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
    //Atualiza a situação de acordo com o que foi inserido na média anteriormente
    $notaComponenteCurricularMedia->markOld();
    $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
  }

 public function updateMediaGeral($media, $etapa){
    $mediaGeral = new Avaliacao_Model_MediaGeral(array(
      'notaAluno' => $this->_getNotaAluno()->id,
      'media' => $media,
      'mediaArredondada' => $this->arredondaMedia($media),
      'etapa' => $etapa,
    ));

    try {
      // Se existir, marca como "old" para possibilitar a atualização
      $this->getMediaGeralDataMapper()->find(array(
        $mediaGeral->get('notaAluno')
      ));
      $mediaGeral->markOld();
    }
    catch (Exception $e) {
      // Prossegue, sem problemas.
    }

    // Salva a média
    $this->getMediaGeralDataMapper()->save($mediaGeral);
  }

public function alterarSituacao($novaSituacao, $matriculaId){
  return App_Model_Matricula::setNovaSituacao($matriculaId, $novaSituacao);
}

  /**
   * Atualiza a média dos componentes curriculares.
   */
  protected function _updateNotaComponenteMedia()
  {
    require_once 'Avaliacao/Model/NotaComponenteMedia.php';
    $this->_loadNotas(FALSE);
    $regra = $this->getRegra();

    $etapa = 1;

    if($regra->get('notaGeralPorEtapa') == "1"){
      $notasGerais = array('Se' => 0, 'Et' => $this->getOption('etapas'));

      foreach($this->_notasGerais as $id => $notaGeral){

        $etapasNotas = CoreExt_Entity::entityFilterAttr($notaGeral, 'etapa', 'nota');

        // Cria o array formatado para o cálculo da fórmula da média
        foreach ($etapasNotas as $etapa => $nota) {
          if (is_numeric($etapa)) {
            $notasGerais['E' . $etapa] = $nota;
            $notasGerais['Se'] += $nota;
            continue;
          }
          $notasGerais[$etapa] = $nota;
        }
      }

      //Calcula a média geral
      $mediaGeral = $this->_calculaMedia($notasGerais);

      // Cria uma nova instância de média, já com a nota arredondada e a etapa
      $mediaGeralEtapa = new Avaliacao_Model_MediaGeral(array(
        'notaAluno' => $this->_getNotaAluno()->id,
        'media' => $mediaGeral,
        'mediaArredondada' => $this->arredondaMedia($mediaGeral),
        'etapa' => $etapa
      ));

      try {
        // Se existir, marca como "old" para possibilitar a atualização
        $this->getMediaGeralDataMapper()->find(array(
          $mediaGeralEtapa->get('notaAluno')
        ));

        $mediaGeralEtapa->markOld();
      }
      catch (Exception $e) {
        // Prossegue, sem problemas.
      }

      // Salva a média
      $this->getMediaGeralDataMapper()->save($mediaGeralEtapa);
    }else{
      $turmaId = $this->getOption('ref_cod_turma');
      $infosMatricula = $this->getOption('matriculaData');
      $matriculaId = $infosMatricula['cod_matricula'];
      $serieId = $infosMatricula['ref_ref_cod_serie'];
      $escolaId = $infosMatricula['ref_ref_cod_escola'];

      foreach ($this->_notasComponentes as $id => $notasComponentes) {
        //busca última nota lançada e somente atualiza a média e situação da nota do mesmo componente curricular
        //pois atualizar todas as médias de todos os componentes pode deixar o sistema com perda de performance e excesso de processamento
        if(!isset($this->_currentComponenteCurricular) || $this->_currentComponenteCurricular == $id){
          // Cria um array onde o índice é a etapa
          $etapasNotas = CoreExt_Entity::entityFilterAttr($notasComponentes, 'etapa', 'nota');
          $qtdeEtapas = $this->getOption('etapas');

          if($regra->get('definirComponentePorEtapa') == "1"){
            $qtdeEtapaEspecifica = App_Model_IedFinder::getQtdeEtapasComponente($turmaId, $id);

            $qtdeEtapas = ($qtdeEtapaEspecifica ? $qtdeEtapaEspecifica : $qtdeEtapas);
          }

          $verificaDispensa = App_Model_IedFinder::validaDispensaPorMatricula($matriculaId, $serieId, $escolaId, $id);
          $consideraEtapas = [];

          for ($i = 1; $i <= $qtdeEtapas; $i++) {
              $consideraEtapas['C' . $i] = in_array($i, $verificaDispensa) ? 0 : 1;
          }

          if ($verificaDispensa) {
            $qtdeEtapas = $qtdeEtapas - count($verificaDispensa);
          }

          $notas = array_merge(['Se' => 0, 'Et' => $qtdeEtapas], $consideraEtapas);

          // Cria o array formatado para o cálculo da fórmula da média
          foreach ($etapasNotas as $etapa => $nota) {
            if (is_numeric($etapa)) {
              $notas['E' . $etapa] = $nota;
              $notas['Se'] += $nota;
              continue;
            }
            $notas[$etapa] = $nota;
          }

          $notas = $this->_calculateNotasRecuperacoesEspecificas($id, $notas);

          // Calcula a média por componente curricular
          $media = $this->_calculaMedia($notas);

          // Cria uma nova instância de média, já com a nota arredondada e a etapa
          $notaComponenteCurricularMedia = new Avaliacao_Model_NotaComponenteMedia(array(
            'notaAluno' => $this->_getNotaAluno()->id,
            'componenteCurricular' => $id,
            'media' => $media,
            'mediaArredondada' => $this->arredondaMedia($media),
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
          //Atualiza a situação de acordo com o que foi inserido na média anteriormente
          $notaComponenteCurricularMedia->markOld();
          $notaComponenteCurricularMedia->situacao = $this->getSituacaoComponentesCurriculares()->componentesCurriculares[$id]->situacao;

          $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
        }
      }
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

  public function deleteNotaGeral($etapa)
  {
    $notaGeral = $this->getNotaGeral($etapa);
    if(!is_null($notaGeral)){
      $this->getNotaGeralAbstractDataMapper()->delete($notaGeral);
    }

    return $this;
  }

    /**
     * Verifica se as notas das etapas anteriores foram lançadas para o
     * componente curricular. Lança uma exceção caso contrário.
     *
     * @param int|string $etapaId
     * @param int $componenteCurricularId
     *
     * @return bool
     *
     * @throws MissingStagesException
     * @throws Exception
     */
    public function verificaNotasLancadasNasEtapasAnteriores($etapaId, $componenteCurricularId)
    {
        $temEtapasAnterioresLancadas = true;
        $etapasSemNotas = [];
        $regra = $this->getRegra();
        $matriculaId = $this->getOption('matricula');
        $serieId = $this->getOption('ref_cod_serie');
        $escolaId = $this->getOption('ref_cod_escola');
        $instituicao = App_Model_IedFinder::getInstituicao($regra->instituicao);

        // Pelo que eu entendi, caso a opção `definirComponentePorEtapa` é
        // possível lançar notas para etapas futuras.

        if ($regra->get('definirComponentePorEtapa') == "1") {
            return true;
        }

        $etapasDispensadas = (array) App_Model_IedFinder::validaDispensaPorMatricula(
            $matriculaId, $serieId, $escolaId, $componenteCurricularId
        );

        $informacoesMatricula = (array) App_Model_IedFinder::getMatricula(
            $matriculaId
        );

        $informacoesEtapas = (array) App_Model_IedFinder::getEtapasDaTurma(
            $informacoesMatricula['ref_cod_turma']
        );

        $etapasAntesDaEnturmacao = array_filter($informacoesEtapas, function ($etapa) use ($informacoesMatricula) {
            return $informacoesMatricula['data_enturmacao'] > $etapa['data_fim'];
        });

        $etapasAntesDaEnturmacao = array_map(function ($etapa) {
            return $etapa['sequencial'];
        }, $etapasAntesDaEnturmacao);

        $exigirLancamentosAnteriores = dbBool($instituicao['exigir_lancamentos_anteriores']);

        if ($etapaId == 'Rc') {
            $etapaId = $this->getOption('etapas');
        }

        $secretarioDeveLancarNota = false;

        for ($etapa = 1; $etapa <= $etapaId; $etapa++) {

            // Etapas com dispensa não terão notas, então não devem ser
            // consideradas como bloqueantes.

            if (in_array($etapa, $etapasDispensadas)) {
                continue;
            }

            // Se o o parâmetro da instituição "exigir_lancamentos_anteriores"
            // não estiver ativo e o aluno foi enturmado em uma data posterior
            // ao fim de uma etapa, o lançamento da nota da etapa anterior não
            // será considerado bloqueante.

            if (!$exigirLancamentosAnteriores && in_array($etapa, $etapasAntesDaEnturmacao)) {
                continue;
            }

            if (in_array($etapa, $etapasAntesDaEnturmacao)) {
                $secretarioDeveLancarNota = true;
            }

            $nota = $this->getNotaAtual($etapa, $componenteCurricularId);

            $etapaDiferenteOuRecuperacao = $etapa != $etapaId || $etapaId == 'Rc';

            if (
                $etapaDiferenteOuRecuperacao
                && empty($nota)
                && !is_numeric($nota)
            ) {
                $temEtapasAnterioresLancadas = false;
                $etapasSemNotas[] = $etapa;
            }
        }

        if ($temEtapasAnterioresLancadas) {
            return true;
        }

        $nomeDaEtapa = 'Etapa';

        if (count($informacoesEtapas)) {
            $etapa = App_Model_IedFinder::getEtapa($informacoesEtapas[0]['cod_modulo']);

            if ($etapa) {
                $nomeDaEtapa = $etapa['nm_tipo'];
            }
        }

        if ($secretarioDeveLancarNota) {
            throw new StagesNotInformedByCoordinatorException($etapasSemNotas, $nomeDaEtapa);
        }

        throw new StagesNotInformedByTeacherException($etapasSemNotas, $nomeDaEtapa);
    }

    /**
     * Verifica se as faltas das etapas anteriores foram lançadas para o
     * componente curricular. Lança uma exceção caso contrário.
     *
     * @param int|string $etapaId
     * @param int $componenteCurricularId
     *
     * @return bool
     *
     * @throws Exception
     */
    public function verificaFaltasLancadasNasEtapasAnteriores($etapaId, $componenteCurricularId)
    {
        $temEtapasAnterioresLancadas = true;
        $etapasSemFaltas = [];
        $matriculaId = $this->getOption('matricula');
        $serieId = $this->getOption('ref_cod_serie');
        $escolaId = $this->getOption('ref_cod_escola');

        $existeEtapaDispensada = (array) App_Model_IedFinder::validaDispensaPorMatricula($matriculaId, $serieId, $escolaId, $componenteCurricularId);

        for ($etapa = 1; $etapa <= $etapaId; $etapa++) {

            $faltas = $this->getFaltaAtual($etapa, $componenteCurricularId);

            if (in_array($etapa, $existeEtapaDispensada)) {
                continue;
            }

            if ($etapa != $etapaId && empty($faltas) && !is_numeric($faltas)) {
                $temEtapasAnterioresLancadas = false;
                $etapasSemFaltas[] = $etapa;
            }
        }

        if ($temEtapasAnterioresLancadas) {
            return true;
        }

        $mensagem = 'Falta somente pode ser lançada após lançar faltas nas '
            . 'etapas anteriores: ' . join(', ', $etapasSemFaltas);

        if ($this->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $mensagem .= ' deste componente curricular.';
        }

        throw new Exception($mensagem);
    }

    /**
     * Retorna a nota lançada na etapa para o componente curricular.
     *
     * @param int|string $etapa
     * @param int $componenteCurricularId
     *
     * @return int|string
     */
    public function getNotaAtual($etapa, $componenteCurricularId)
    {
        // FIXME não entendi o motivo deste urldecode
        $nota = urldecode($this->getNotaComponente($componenteCurricularId, $etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    /**
     * Retorna o número de faltas lançadas na etapa para o componente
     * curricular. Caso não exista, retorna null.
     *
     * @param int|string $etapa
     * @param int $componenteCurricularId
     *
     * @return int|null
     */
    public function getFaltaAtual($etapa, $componenteCurricularId)
    {
        $faltas = null;
        $tipoPresenca = $this->getRegra()->get('tipoPresenca');

        if ($tipoPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $faltas = $this->getFalta($etapa, $componenteCurricularId)->quantidade;
        }

        if ($tipoPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $faltas = $this->getFalta($etapa)->quantidade;
        }

        return $faltas;
    }
}
