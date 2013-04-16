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
   * Retorna um array com as informações de escola a partir de seu código.
   * @param  int $id
   * @return array
   */
  public static function getEscola($id)
  {
    $escola = self::addClassToStorage('clsPmieducarEscola', NULL,
      'include/pmieducar/clsPmieducarEscola.inc.php');
    $escola->cod_escola = $id;
    $escola = $escola->detalhe();

    if (FALSE === $escola) {
      throw new App_Model_Exception(
        sprintf('Escola com o código "%d" não existe.', $id)
      );
    }

    return $escola;
  }

  /**
   * Retorna todas as escolas cadastradas na tabela pmieducar.escola, selecionando
   * opcionalmente pelo código da instituição.
   * @param int $instituicaoId
   * @return array
   */
  public static function getEscolas($instituicaoId = NULL)
  {
    $_escolas = self::addClassToStorage('clsPmieducarEscola', NULL,
      'include/pmieducar/clsPmieducarEscola.inc.php');

    $escolas = array();
    foreach ($_escolas->lista(NULL, NULL, NULL,  $instituicaoId) as $escola) {
      $escolas[$escola['cod_escola']] = $escola['nome'];
    }

    return $escolas;
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
   * Retorna todos os cursos cadastradas na tabela pmieducar.escola_curso, selecionando
   * opcionalmente pelo código da escola.
   * @param int $escolaId
   * @return array
   */
  public static function getCursos($escolaId = NULL)
  {
    $escola_curso = self::addClassToStorage('clsPmieducarEscolaCurso', NULL,
      'include/pmieducar/clsPmieducarEscolaCurso.inc.php');

    // Carrega os cursos
    $escola_curso->setOrderby('ref_cod_escola ASC, cod_curso ASC');
    $escola_curso = $escola_curso->lista($escolaId);

    $cursos = array();
    foreach ($escola_curso as $key => $val) {
      $nomeCurso = self::getCurso($val['ref_cod_curso']);
      $cursos[$val['ref_cod_curso']] = $nomeCurso;
    }

    return $cursos;
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
   * Retorna todas as séries cadastradas na tabela pmieducar.serie, selecionando
   * opcionalmente pelo código da instituição, da escola ou do curso.
   * @param int $instituicaoId
   * @param int $escolaId
   * @param int $cursoId
   * @return array
   */
  public static function getSeries($instituicaoId = NULL, $escolaId = NULL, $cursoId = NULL)
  {
    $series = self::addClassToStorage('clsPmieducarSerie', NULL,
                                       'include/pmieducar/clsPmieducarSerie.inc.php');

    $series->setOrderby('ref_cod_curso ASC, cod_serie ASC, etapa_curso ASC');
    $series = $series->lista(NULL, NULL, NULL, $cursoId, NULL, NULL, NULL, NULL, NULL,
                             NULL, NULL, NULL, NULL, $instituicaoId, NULL, NULL, NULL, $escolaId);

    $_series = array();

    foreach ($series as $serie) {
      //$series[$val['cod_serie']] = $val;
      $_series[$serie['cod_serie']] = $serie['nm_serie'];
    }

    return $_series;
  }

  /**
   * Retorna um array com as informações da turma a partir de seu código.
   *
   * @param int $codTurma
   * @return array
   * @throws App_Model_Exception
   */
  public static function getTurma($codTurma)
  {
    // Recupera clsPmieducarTurma do storage de classe estático
    $turma = self::addClassToStorage('clsPmieducarTurma', NULL,
      'include/pmieducar/clsPmieducarTurma.inc.php');

    // Usa o atributo público para depois chamar o método detalhe()
    $turma->cod_turma = $codTurma;
    $turma = $turma->detalhe();

    if (FALSE === $turma) {
      throw new App_Model_Exception(
        sprintf('Turma com o código "%d" não existe.', $codTurma)
      );
    }

    return $turma;
  }

  /**
   * Retorna as turmas de uma escola, selecionando opcionalmente pelo código da série.
   * @param  int   $escolaId
   * @param  int   $serieId
   * @return array (cod_turma => nm_turma)
   */
  public static function getTurmas($escolaId, $serieId = NULL)
  {
    $turma = self::addClassToStorage('clsPmieducarTurma', NULL,
      'include/pmieducar/clsPmieducarTurma.inc.php');

    // Carrega as turmas da escola
    $turma->setOrderBy('nm_turma ASC');
    $turmas = $turma->lista(NULL, NULL, NULL, $serieId, $escolaId);

    $ret = array();
    foreach ($turmas as $turma) {
      $ret[$turma['cod_turma']] = $turma['nm_turma'];
    }

    return $ret;
  }

  /**
   * Retorna o total de módulos do ano letivo da escola ou turma (caso o ano
   * escolar do curso não seja "padrão"). Retorna um array com o total de
   * módulos atribuídos ao ano letivo e o nome do módulo. Ex:
   *
   * <code>
   * <?php
   * array(
   *   'total' => 4,
   *   'nome'  => 'Bimestre'
   * );
   * </code>
   *
   * @param int      $codEscola
   * @param int      $codCurso
   * @param int      $codTurma
   * @param int|NULL $ano        Ano letivo da escola ou turma. Opcional.
   * @return array
   */
  public static function getModulo($codEscola, $codCurso, $codTurma,
    $ano = NULL)
  {
    $modulos = array();

    $curso = self::addClassToStorage('clsPmieducarCurso', NULL,
      'include/pmieducar/clsPmieducarCurso.inc.php');

    $curso->cod_curso = $codCurso;
    $curso = $curso->detalhe();

    $padraoAnoEscolar = $curso['padrao_ano_escolar'] == 1 ? TRUE : FALSE;

    // Segue o padrão
    if (TRUE == $padraoAnoEscolar) {
      $escolaAnoLetivo = self::addClassToStorage('clsPmieducarEscolaAnoLetivo',
        NULL, 'include/pmieducar/clsPmieducarEscolaAnoLetivo.inc.php');

      $anosEmAndamento = $escolaAnoLetivo->lista($codEscola, $ano, NULL, NULL,
        1, NULL, NULL, NULL, NULL, 1);

      // Pela restrição na criação de anos letivos, eu posso confiar no primeiro
      // e único resultado que deve ter retornado
      if (FALSE == $anosEmAndamento || count($anosEmAndamento) < 1)
        throw new App_Model_Exception('Não foi encontrado um ano escolar em andamento.');

      elseif (count($anosEmAndamento) > 1)
        throw new App_Model_Exception('Existe mais de um ano escolar em andamento.');

      $ano = array_shift($anosEmAndamento);
      $ano = $ano['ano'];

      $anoLetivoModulo = self::addClassToStorage('clsPmieducarAnoLetivoModulo',
        NULL, 'include/pmieducar/clsPmieducarAnoLetivoModulo.inc.php');

      $modulos = $anoLetivoModulo->lista($ano, $codEscola);
    }
    else {
      $turmaModulo = self::addClassToStorage('clsPmieducarTurmaModulo',
        NULL, 'include/pmieducar/clsPmieducarTurmaModulo.inc.php');

      $modulos = $turmaModulo->lista($codTurma);
    }

    if (FALSE === $modulos) {
      return 0;
    }

    // Total de módulos
    $total = count($modulos);

    // Código do tipo de módulo
    $modulo    = array_shift($modulos);
    $codModulo = $modulo['ref_cod_modulo'];

    // Recupera do regstry o objeto legado
    $modulo = self::addClassToStorage('clsPmieducarModulo', NULL,
      'include/pmieducar/clsPmieducarModulo.inc.php');

    $modulo->cod_modulo = $codModulo;
    $modulo = $modulo->detalhe();
    $modulo = $modulo['nm_tipo'];

    return array(
      'total' => $total,
      'nome'  => $modulo
    );
  }

  /**
   * Retorna array com as referências de pmieducar.escola_serie_disciplina
   * a modules.componente_curricular ('ref_ref_cod_disciplina').
   *
   * @param  int $serieId O código do ano escolar/série.
   * @param  int $escola  O código da escola.
   * @param  ComponenteCurricular_Model_ComponenteDataMapper $mapper (Opcional)
   *   Instância do mapper para recuperar todas as instâncias persistidas de
   *   ComponenteCurricular_Model_Componente atribuídas no ano escolar/série da
   *   escola.
   * @return array
   * @throws App_Model_Exception caso não existam componentes curriculares
   *   atribuídos ao ano escolar/série da escola.
   */
  public static function getEscolaSerieDisciplina($serieId, $escolaId,
    ComponenteCurricular_Model_ComponenteDataMapper $mapper = NULL,
    $disciplinaId = null)
  {
    if (is_null($serieId))
      throw new App_Model_Exception('O parametro serieId não pode ser nulo');

    if (is_null($escolaId))
      throw new App_Model_Exception('O parametro escolaId não pode ser nulo');

    // Disciplinas na série na escola
    $escolaSerieDisciplina = self::addClassToStorage('clsPmieducarEscolaSerieDisciplina',
      NULL, 'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php');

    $disciplinas = $escolaSerieDisciplina->lista($serieId, $escolaId, $disciplinaId, 1);

    if (FALSE === $disciplinas) {
      throw new App_Model_Exception(sprintf(
          'Nenhuma disciplina para a série (%d) e a escola (%d) informados',
          $serieId, $escolaId
      ));
    }

    $componentes = array();
    foreach ($disciplinas as $disciplina) {
      $componente = new stdClass();

      $componente->id           = $disciplina['ref_cod_disciplina'];
      $componente->cargaHoraria = $disciplina['carga_horaria'];

      $componentes[] = $componente;
    }

    return self::_hydrateComponentes($componentes, $serieId, $mapper);
  }

  /**
   * Retorna as instâncias de ComponenteCurricular_Model_Componente de uma turma.
   *
   * @param int $serieId O código do ano escolar/série da turma.
   * @param int $escola  O código da escola da turma.
   * @param int $turma   O código da turma.
   * @param ComponenteCurricular_Model_TurmaDataMapper $mapper (Opcional) Instância
   *   do mapper para selecionar todas as referências de
   *   ComponenteCurricular_Model_Componente persistidas para a turma.
   * @param ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper (Opcional)
   *   Instância do mapper para recuperar as instâncias de
   *   ComponenteCurricular_Model_Componente recuperadas por $mapper.
   * @return array
   */
  public static function getComponentesTurma($serieId, $escola, $turma,
    ComponenteCurricular_Model_TurmaDataMapper $mapper = NULL,
    ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper = NULL,
    $componenteCurricularId = null)
  {
    if (is_null($mapper)) {
      require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
      $mapper = new ComponenteCurricular_Model_TurmaDataMapper();
    }

    $where = array('turma' => $turma);

    if (is_numeric($componenteCurricularId))
      $where['componente_curricular_id'] = $componenteCurricularId;

    $componentesTurma = $mapper->findAll(array(), $where);

    // Não existem componentes específicos para a turma
    if (0 == count($componentesTurma)) {
      return self::getEscolaSerieDisciplina($serieId, $escola, $componenteMapper, $componenteCurricularId);
    }

    $componentes = array();
    foreach ($componentesTurma as $componenteTurma) {
      $componente = new stdClass();

      $componente->id           = $componenteTurma->get('componenteCurricular');
      $componente->cargaHoraria = $componenteTurma->cargaHoraria;

      $componentes[] = $componente;
    }

    return self::_hydrateComponentes($componentes, $serieId, $componenteMapper);
  }

  /**
   * Recupera instâncias persistidas de ComponenteCurricular_Model_Componente,
   * retornando-as com a carga horária padrão caso o componente identificado
   * em $componentes possua uma carga horária (atributo cargaHoraria) nula.
   *
   * @param  array  $componentes  (array(stdClass->id, stdClass->cargaHoraria))
   * @param  int    $anoEscolar   O ano escolar/série para recuperar a carga
   *   horária padrão do componente curricular.
   * @param  ComponenteCurricular_Model_ComponenteDataMapper $mapper (Opcional)
   *   O mapper para recuperar a instância persistida com a carga horária padrão.
   * @return array
   */
  protected static function _hydrateComponentes(array $componentes, $anoEscolar,
    ComponenteCurricular_Model_ComponenteDataMapper $mapper = NULL)
  {
    if (is_null($mapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
    }

    $ret = array();

    foreach ($componentes as $componentePlaceholder) {
      $id    = $componentePlaceholder->id;
      $carga = $componentePlaceholder->cargaHoraria;

      $componente = $mapper->findComponenteCurricularAnoEscolar($id, $anoEscolar);

      if (! is_null($carga)) {
        $componente->cargaHoraria = $carga;
      }

      $ret[$id] = $componente;
    }

    return $ret;
  }

  /**
   * Retorna um array populado com os dados de uma matricula.
   *
   * @param  int $codMatricula
   * @return array
   * @throws App_Model_Exception
   */
  public static function getMatricula($codMatricula)
  {
    // Recupera clsPmieducarMatricula do storage de classe estático
    $matricula = self::addClassToStorage('clsPmieducarMatricula', NULL,
      'include/pmieducar/clsPmieducarMatricula.inc.php');

    $turma = self::addClassToStorage('clsPmieducarMatriculaTurma', NULL,
      'include/pmieducar/clsPmieducarMatriculaTurma.inc.php');

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
    $turmas = $turma->lista($codMatricula, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    if (0 < count($turmas)) {
      $turma = array_shift($turmas);

      $matricula['ref_cod_turma'] = $turma['ref_cod_turma'];
      $matricula['turma_nome']    = isset($turma['nm_turma']) ? $turma['nm_turma'] : NULL;
    }
    else {
      throw new App_Model_Exception('Aluno não enturmado.');
    }

    $curso->cod_curso = $matricula['ref_cod_curso'];
    $curso = $curso->detalhe();

    $serie->cod_serie = $matricula['ref_ref_cod_serie'];
    $serie = $serie->detalhe();

    $matricula['curso_carga_horaria'] = $curso['carga_horaria'];
    $matricula['curso_hora_falta']    = $curso['hora_falta'];
    $matricula['serie_carga_horaria'] = $serie['carga_horaria'];
    $matricula['serie_dias_letivos']  = $serie['dias_letivos'];

    $matricula['curso_nome']          = isset($curso['nm_curso']) ? $curso['nm_curso'] : NULL;
    $matricula['serie_nome']          = isset($serie['nm_serie']) ? $serie['nm_serie'] : NULL;
    $matricula['serie_concluinte']    = isset($serie['concluinte']) ? $serie['concluinte'] : NULL;

    return $matricula;
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
   * @param  int $codMatricula
   * @param  ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper
   * @param  ComponenteCurricular_Model_TurmaDataMapper $turmaMapper
   * @return array
   * @throws App_Model_Exception
   */
  public static function getComponentesPorMatricula($codMatricula,
    ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper = NULL,
    ComponenteCurricular_Model_TurmaDataMapper $turmaMapper = NULL,
    $componenteCurricularId = null)
  {
    $matricula = self::getMatricula($codMatricula);

    $codEscola = $matricula['ref_ref_cod_escola'];
    $codSerie  = $matricula['ref_ref_cod_serie'];
    $turma     = $matricula['ref_cod_turma'];

    $serie = self::getSerie($codSerie);

    // Disciplinas da escola na série em que o aluno está matriculado
    $componentes = self::getComponentesTurma(
      $codSerie, $codEscola, $turma, $turmaMapper, $componenteMapper, $componenteCurricularId
    );

    // Dispensas do aluno
    $disciplinasDispensa = self::getDisciplinasDispensadasPorMatricula(
      $codMatricula, $codSerie, $codEscola
    );

    $ret = array();
    foreach ($componentes as $id => $componente) {
      if (in_array($id, $disciplinasDispensa)) {
        continue;
      }

      $ret[$id] = $componente;
    }

    return $ret;
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
   * Retorna a quantidade de módulos do ano letivo por uma dada matrícula.
   *
   * @param  int $codMatricula
   * @return int
   */
  public static function getQuantidadeDeModulosMatricula($codMatricula)
  {
    $modulos = array();

    // matricula
    $matricula = self::getMatricula($codMatricula);
    $codEscola = $matricula['ref_ref_cod_escola'];
    $codCurso  = $matricula['ref_cod_curso'];
    $codTurma  = $matricula['ref_cod_turma'];

    $modulos = self::getModulo($codEscola, $codCurso, $codTurma);

    return $modulos['total'];
  }

  /**
   * Retorna um array com as informações de biblioteca a partir de seu código.
   * @param  int $id
   * @return array
   */
  public static function getBiblioteca($id)
  {
    $biblioteca = self::addClassToStorage('clsPmieducarBiblioteca', NULL,
      'include/pmieducar/clsPmieducarBiblioteca.inc.php');
    $biblioteca->cod_biblioteca = $id;
    $biblioteca = $biblioteca->detalhe();

    if (FALSE === $biblioteca) {
      throw new App_Model_Exception(
        sprintf('Biblioteca com o código "%d" não existe.', $id)
      );
    }

    return $biblioteca;
  }

  /**
   * Retorna todas as bibliotecas cadastradas na tabela pmieducar.biblioteca, selecionando
   * opcionalmente pelo código da instituição e/ ou escola.
   * @param int $instituicaoId
   * @return array
   */
  public static function getBibliotecas($instituicaoId = NULL, $escolaId = NULL)
  {
    $_bibliotecas = self::addClassToStorage('clsPmieducarBiblioteca', NULL,
      'include/pmieducar/clsPmieducarBiblioteca.inc.php');

    $bibliotecas = array();
    foreach ($_bibliotecas->lista(NULL, $instituicaoId, $escolaId) as $biblioteca) {
      $bibliotecas[$biblioteca['cod_biblioteca']] = $biblioteca['nm_biblioteca'];
    }

    return $bibliotecas;
  }

  /**
   * Retorna todas as situações cadastradas para as bibliotecas na tabela pmieducar.situacao, selecionando
   * opcionalmente pelo código da biblioteca.
   * @param int $bibliotecaId
   * @return array
   */
  public static function getBibliotecaSituacoes($bibliotecaId = NULL)
  {
    $_situacoes = self::addClassToStorage('clsPmieducarSituacao', NULL,
      'include/pmieducar/clsPmieducarSituacao.inc.php');

    $situacoes = array();
    foreach ($_situacoes->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, $bibliotecaId) as $situacao) {
      $situacoes[$situacao['cod_situacao']] = $situacao['nm_situacao'];
    }

    return $situacoes;
  }

  /**
   * Retorna todas as fontes cadastradas para as bibliotecas na tabela pmieducar.fonte, selecionando
   * opcionalmente pelo código da biblioteca.
   * @param int $bibliotecaId
   * @return array
   */
  public static function getBibliotecaFontes($bibliotecaId = NULL)
  {
    $_fontes = self::addClassToStorage('clsPmieducarFonte', NULL,
      'include/pmieducar/clsPmieducarFonte.inc.php');

    $fontes = array();
    foreach ($_fontes->lista(null,null,null,null,null,null,null,null,null,1, $bibliotecaId) as $fonte) {
      $fontes[$fonte['cod_fonte']] = $fonte['nm_fonte'];
    }

    return $fontes;
  }

  /**
   * Retorna uma obra cadastrada para uma biblioteca na tabela pmieducar.acervo, selecionando
   * obrigatóriamente pelo código da biblioteca e opcionalmente pelo código da obra.
   * @param int $bibliotecaId
   * @return array
   */
  public static function getBibliotecaObra($bibliotecaId, $id = NULL)
  {
    $obra = self::addClassToStorage('clsPmieducarAcervo', NULL,
                                    'include/pmieducar/clsPmieducarAcervo.inc.php');

    $obra->ref_cod_biblioteca = $$bibliotecaId;
    $obra->cod_acervo         = $id;
    $obra                     = $obra->detalhe();

    if (FALSE === $obra) {
      throw new App_Model_Exception(
        sprintf('Obra com o código "%d" não existe.', $id)
      );
    }

    return $obra;
  }

  /**
   * Retorna um aluno cadastrado para uma escola na tabela pmieducar.aluno, selecionando
   * obrigatóriamente pelo código da escola e opcionalmente pelo código do aluno.
   * @param int $id
   * @return array
   */
  public static function getAluno($escolaId, $id)
  {
    $aluno = self::addClassToStorage('clsPmieducarAluno', NULL,
                                    'include/pmieducar/clsPmieducarAluno.inc.php');
    #$aluno->cod_aluno      = $id;
    #$aluno                 = $aluno->detalhe();

    $aluno = $aluno->lista($id, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $escolaId);

    if (FALSE === $aluno) {
      throw new App_Model_Exception(
        sprintf('Aluno com o código "%d" não existe.', $id)
      );
    }

    return $aluno[0];
  }

  /**
   * Retorna todos os tipos de cliente cadastrados para determinada biblioteca na tabela
   * pmieducar.cliente_tipo, selecionando obrigatoriamente pelo código da biblioteca.
   * @param int $bibliotecaId
   * @return array
   */
  public static function getBibliotecaTiposCliente($bibliotecaId)
  {
    $resources = self::addClassToStorage('clsPmieducarClienteTipo', NULL,
      'include/pmieducar/clsPmieducarClienteTipo.inc.php');

    $filtered_resources = array();
    foreach ($resources->lista(null, $bibliotecaId) as $resource) {
      $filtered_resources[$resource['cod_cliente_tipo']] = $resource['nm_tipo'];
    }

    return $filtered_resources;
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    return array();
  }
}
