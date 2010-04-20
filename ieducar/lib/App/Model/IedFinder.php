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
 * @package   App_Model
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/Exception.php';

/**
 * App_Model_IedFinder class.
 *
 * Disponibiliza finders estáticos para registros mantidos pelas classes
 * cls* do namespace Ied_*.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class App_Model_IedFinder extends CoreExt_Entity
{
  /**
   * Retorna todas as instituições cadastradas em pmieducar.instituicao.
   * @return array
   */
  public static function getInstituicoes()
  {
    $instituicao = self::addClassToStorage('clsPmieducarInstituicao', NULL,
      'include/pmieducar/clsPmieducarInstituicao.inc.php');

    $instituicoes = array();
    foreach ($instituicao->lista() as $instituicao) {
      $instituicoes[$instituicao['cod_instituicao']] = $instituicao['nm_instituicao'];
    }
    return $instituicoes;
  }

  /**
   * Retorna uma instância de RegraAvaliacao_Model_Regra a partir dos dados
   * da matrícula.
   *
   * @param int $codMatricula
   * @param RegraAvaliacao_Model_RegraDataMapper $mapper
   * @return RegraAvaliacao_Model_Regra
   * @throws App_Model_Exception
   */
  public static function getRegraAvaliacaoPorMatricula($codMatricula,
    RegraAvaliacao_Model_RegraDataMapper $mapper = NULL)
  {
    $matricula = self::getMatricula($codMatricula);
    $serie     = self::getSerie($matricula['ref_ref_cod_serie']);

    if (is_null($mapper)) {
      require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
      $mapper = new RegraAvaliacao_Model_RegraDataMapper();
    }

    return $mapper->find($serie['regra_avaliacao_id']);
  }

  /**
   * Retorna um array de instâncias ComponenteCurricular_Model_Componente ao
   * qual um aluno cursa através de sua matrícula.
   *
   * Exclui todas os componentes curriculares ao qual o aluno está dispensado
   * de cursar.
   *
   * @param int $codMatricula
   * @param RegraAvaliacao_Model_RegraDataMapper $mapper
   * @return array
   * @throws App_Model_Exception
   */
  public static function getComponentesPorMatricula($codMatricula,
    ComponenteCurricular_Model_ComponenteDataMapper $mapper = NULL)
  {
    $matricula = self::getMatricula($codMatricula);

    $codEscola = $matricula['ref_ref_cod_escola'];
    $codSerie  = $matricula['ref_ref_cod_serie'];

    $serie = self::getSerie($codSerie);

    // Disciplinas da escola na série em que o aluno está matriculado
    $disciplinas = self::getEscolaSerieDisciplina($codSerie, $codEscola);

    // Dispensas do aluno
    $disciplinasDispensa = self::getDisciplinasDispensadasPorMatricula(
      $codMatricula, $codSerie, $codEscola
    );

    // Instancia um data mapper caso nenhum seja provido
    if (is_null($mapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
    }

    // Seleciona os componentes curriculares em que o aluno está cursando
    $componentes = array();

    foreach ($disciplinas as $disciplina) {
      if (in_array($disciplina['ref_cod_disciplina'], $disciplinasDispensa)) {
        continue;
      }

      $componenteCurricular = $mapper->findComponenteCurricularAnoEscolar(
        $disciplina['ref_cod_disciplina'],
        $codSerie
      );

      if (!is_null($disciplina['carga_horaria'])) {
        $componenteCurricular->cargaHoraria = $disciplina['carga_horaria'];
      }

      $componentes[$componenteCurricular->id] = $componenteCurricular;
    }

    return $componentes;
  }

  /**
   * Retorna um array populado com os dados de uma matricula.
   *
   * @param int $codMatricula
   * @return array
   * @throws App_Model_Exception
   */
  public static function getMatricula($codMatricula)
  {
    // Recupera clsPmieducarMatricula do storage de classe estático
    $matricula = self::addClassToStorage('clsPmieducarMatricula', NULL,
      'include/pmieducar/clsPmieducarMatricula.inc.php');

    $curso = self::addClassToStorage('clsPmieducarCurso', NULL,
      'include/pmieducar/clsPmieducarCurso.inc.php');

    $serie = self::addClassToStorage('clsPmieducarSerie', NULL,
      'include/pmieducar/clsPmieducarSerie.inc.php');

    // Usa o atributo público para depois chamar o método detalhe()
    $matricula->cod_matricula = $codMatricula;
    $matricula = $matricula->detalhe();

    if (FALSE === $matricula) {
      throw new App_Model_Exception(
        sprintf('Matrícula de código "%d" não existe.', $codMatricula)
      );
    }

    // Atribui dados extra a matrícula
    $curso->cod_curso = $matricula['ref_cod_curso'];
    $curso = $curso->detalhe();

    $serie->cod_serie = $matricula['ref_ref_cod_serie'];
    $serie = $serie->detalhe();

    $matricula['curso_carga_horaria'] = $curso['carga_horaria'];
    $matricula['curso_hora_falta']    = $curso['hora_falta'];
    $matricula['serie_carga_horaria'] = $serie['carga_horaria'];

    $matricula['curso_nome']          = isset($curso['nm_curso']) ? $curso['nm_curso'] : NULL;
    $matricula['serie_nome']          = isset($serie['nm_serie']) ? $serie['nm_serie'] : NULL;
    $matricula['serie_concluinte']    = isset($serie['concluinte']) ? $serie['concluinte'] : NULL;

    return $matricula;
  }

  /**
   * Retorna um array com as informações da série a partir de seu código.
   *
   * @param int $codSerie
   * @return array
   * @throws App_Model_Exception
   */
  public static function getSerie($codSerie)
  {
    // Recupera clsPmieducarSerie do storage de classe estático
    $serie = self::addClassToStorage('clsPmieducarSerie', NULL,
      'include/pmieducar/clsPmieducarSerie.inc.php');

    // Usa o atributo público para depois chamar o método detalhe()
    $serie->cod_serie = $codSerie;
    $serie = $serie->detalhe();

    if (FALSE === $serie) {
      throw new App_Model_Exception(
        sprintf('Série com o código "%d" não existe.', $codSerie)
      );
    }

    return $serie;
  }

  /**
   * Retorna array com as referências de pmieducar.escola_serie_disciplina
   * a modules.componente_curricular ('ref_ref_cod_disciplina').
   *
   * @param int $codSerie
   * @param int $codEscola
   * @return array
   * @throws App_Model_Exception
   */
  public static function getEscolaSerieDisciplina($codSerie, $codEscola)
  {
    // Disciplinas na série na escola
    $escolaSerieDisciplina = self::addClassToStorage('clsPmieducarEscolaSerieDisciplina',
      NULL, 'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php');

    $disciplinasEscolaSerie = $escolaSerieDisciplina->lista($codSerie, $codEscola, NULL, 1);

    if (FALSE === $disciplinasEscolaSerie) {
      throw new App_Model_Exception(
        sprintf('Nenhuma disciplina para a série (%d) e a escola (%d) informados',
          $codSerie, $codEscola)
      );
    }

    $disciplinas = array();
    foreach ($disciplinasEscolaSerie as $disciplinaEscolaSerie) {
      $disciplinas[] = array(
        'ref_cod_disciplina' => $disciplinaEscolaSerie['ref_cod_disciplina'],
        'carga_horaria' => $disciplinaEscolaSerie['carga_horaria']
      );
    }

    return $disciplinas;
  }

  /**
   * Retorna array com as referências de pmieducar.dispensa_disciplina
   * a modules.componente_curricular ('ref_ref_cod_disciplina').
   *
   * @param int $codMatricula
   * @param int $codSerie
   * @param int $codEscola
   * @return array
   */
  public static function getDisciplinasDispensadasPorMatricula($codMatricula,
    $codSerie, $codEscola)
  {
    $dispensas = self::addClassToStorage('clsPmieducarDispensaDisciplina',
      NULL, 'include/pmieducar/clsPmieducarDispensaDisciplina.inc.php');

    $dispensas = $dispensas->lista($codMatricula, $codSerie, $codEscola);

    if (FALSE === $dispensas) {
      return array();
    }

    $disciplinasDispensa = array();
    foreach ($dispensas as $dispensa) {
      $disciplinasDispensa[] = $dispensa['ref_cod_disciplina'];
    }

    return $disciplinasDispensa;
  }

  /**
   * Retorna a quantidade de etapas (módulos) a serem ou cursados por um
   * aluno em uma dada matrícula.
   *
   * @param int $codMatricula
   * @return int
   * @throws App_Model_Exception
   */
  public static function getQuantidadeDeEtapasMatricula($codMatricula)
  {
    $modulos = array();

    // matricula
    $matricula = self::getMatricula($codMatricula);
    $codEscola = $matricula['ref_ref_cod_escola'];
    $codCurso  = $matricula['ref_cod_curso'];

    $curso = self::addClassToStorage('clsPmieducarCurso', NULL,
      'include/pmieducar/clsPmieducar.inc.php');

    $curso->cod_curso = $codCurso;
    $curso = $curso->detalhe();

    $padraoAnoEscolar = $curso['padrao_ano_escolar'] == 1 ? TRUE : FALSE;

    // Segue o padrão
    if (TRUE == $padraoAnoEscolar) {
      $escolaAnoLetivo = self::addClassToStorage('clsPmieducarEscolaAnoLetivo',
        NULL, 'include/pmieducar/clsPmieducarEscolaAnoLetivo.inc.php');

      $anosEmAndamento = $escolaAnoLetivo->lista($codEscola, NULL, NULL, NULL,
        1, NULL, NULL, NULL, NULL, 1);

      // Pela restrição na criação de anos letivos, eu posso confiar no primeiro
      // e único resultado que deve ter retornado
      if (FALSE !== $anosEmAndamento && 1 == count($anosEmAndamento)) {
        $ano = array_shift($anosEmAndamento);
        $ano = $ano['ano'];
      }
      else {
        throw new App_Model_Exception('Existem vários anos escolares em andamento.');
      }

      $anoLetivoModulo = self::addClassToStorage('clsPmieducarAnoLetivoModulo',
        NULL, 'include/pmieducar/clsPmieducarAnoLetivoModulo.inc.php');

      $modulos = $anoLetivoModulo->lista($ano, $codEscola);
    }
    else {
      $matriculaTurma = self::addClassToStorage('clsPmieducarMatriculaTurma',
        NULL, 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php');

      $matriculas = $matriculaTurma->lista($codMatricula);

      if (is_array($matriculas)) {
        $matricula = array_shift($matriculas);
        $codTurma  = $matricula['ref_cod_turma'];
      }
      else {
        throw new App_Model_Exception('Aluno não enturmado.');
      }

      $turmaModulo = self::addClassToStorage('clsPmieducarTurmaModulo',
        NULL, 'include/pmieducar/clsPmieducarTurmaModulo.inc.php');

      $modulos = $turmaModulo->lista($codTurma);
    }

    if (FALSE === $modulos) {
      return 0;
    }

    return count($modulos);
  }

  /**
   * Retorna todas as séries cadastradas na tabela pmieducar.serie, selecionando
   * opcionalmente pelo código da instituição.
   * @param int $instituicaoId
   * @return array
   */
  public static function getSeries($instituicaoId = NULL)
  {
    $serie = self::addClassToStorage('clsPmieducarSerie', NULL,
      'include/pmieducar/clsPmieducarSerie.inc.php');

    // Carrega as séries
    $serie->setOrderby('ref_cod_curso ASC, cod_serie ASC, etapa_curso ASC');
    $serie = $serie->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, $instituicaoId);

    $series = array();
    foreach ($serie as $key => $val) {
      $series[$val['cod_serie']] = $val;
    }

    return $series;
  }

  /**
   * Retorna um nome de curso, procurando pelo seu código.
   * @param  int $id
   * @return string|FALSE
   */
  public static function getCurso($id)
  {
    $curso = self::addClassToStorage('clsPmieducarCurso', NULL,
      'include/pmieducar/clsPmieducarCurso.inc.php');
    $curso->cod_curso = $id;
    $curso = $curso->detalhe();
    return $curso['nm_curso'];
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    return array();
  }
}