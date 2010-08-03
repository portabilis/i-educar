<?php

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'App/Model/MatriculaSituacao.php';

print '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
print '<query xmlns="sugestoes">' . "\n";

@session_start();
$pessoa_logada = $_SESSION['id_pessoa'];
@session_write_close();

/**
 * Retorna o ano escolar de sequência do ano escolar no curso.
 *
 * @param  clsBanco $db
 * @param  int      $codSerie
 * @param  int      $codCurso
 * @return array    (codSerie => nome)
 * @todo   Selecionar pelo curso é desnecessário pois a relação pmieducar.serie
 *         com pmieducar.curso é 1:1.
 */
function _anosEscolaresSequenciaCurso(clsBanco $db, $codSerie, $codCurso)
{
  $sql = sprintf('SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.sequencia_serie ss
    WHERE
      ss.ref_serie_origem = %d
      AND ss.ref_serie_destino = s.cod_serie
      AND s.ref_cod_curso = %d
      AND ss.ativo = 1
    ORDER BY
      s.nm_serie ASC',
    $codSerie, $codCurso
  );

  $db->Consulta($sql);

  $lst_serie = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      list($cod, $nome) = $db->Tupla();
      $lst_serie[$cod] = $nome;
    }
  }

  return $lst_serie;
}

/**
 * Seleciona um ano escolar/série.
 *
 * @param  clsBanco $db
 * @param  int      $codSerie  Código do ano escolar/série.
 * @return array    (codSerie => nome)
 */
function _mesmoAnoEscolar(clsBanco $db, $codSerie)
{
  $sql = sprintf('SELECT
      cod_serie,
      nm_serie
    FROM
      pmieducar.serie
    WHERE
      cod_serie = %d
      AND ativo = 1
    ORDER BY
      nm_serie ASC',
    $codSerie
  );

  $db->Consulta($sql);

  $lst_serie = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      list($cod, $nome) = $db->Tupla();
      $lst_serie[$cod] = $nome;
    }
  }

  return $lst_serie;
}

/**
 * Retorna os anos escolares da sequência do ano escolar/série para a escola
 * e o curso.
 *
 * @param  clsBanco $db
 * @param  int      $codSerie   Código do ano escolar.
 * @param  int      $codEscola  Código da escola.
 * @param  int      $codCurso   Código do curso.
 * @return array    (codSerie => nome)
 * @see    _anosEscolaresEscolaCurso
 */
function _sequencias(clsBanco $db, $codSerie, $codEscola, $codCurso)
{
  $db->Consulta('SELECT
      so.ref_cod_curso AS curso_origem,
      ss.ref_serie_origem AS serie_origem,
      sd.ref_cod_curso AS curso_destino,
      ss.ref_serie_destino AS serie_destino
    FROM
      pmieducar.sequencia_serie ss,
      pmieducar.serie so,
      pmieducar.serie sd
    WHERE
      ss.ativo = 1
      AND ref_serie_origem = so.cod_serie
      AND ref_serie_destino = sd.cod_serie
    ORDER BY
      ss.ref_serie_origem ASC');

  $sequencias = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      $sequencias[] = $db->Tupla();
    }
  }

  // Seleciona todas as séries de origem de sequência de série que não sejam
  // séries destino de alguma sequência.
  $db->Consulta('SELECT
      DISTINCT(o.ref_serie_origem)
    FROM
      pmieducar.sequencia_serie o,
      pmieducar.escola_serie es
    WHERE NOT EXISTS(
      SELECT
        1
      FROM
        pmieducar.sequencia_serie d
      WHERE
        o.ref_serie_origem = d.ref_serie_destino
    )');

  $lst_serie = array();
  $serie_sequencia    = array();

  if ($db->numLinhas()) {
    $pertence_sequencia = FALSE;
    $achou_serie        = FALSE;
    $reset              = FALSE;

    while ($db->ProximoRegistro()) {
      list($ini_sequencia) = $db->Tupla();

      $ini_serie = $ini_sequencia;
      reset($sequencias);

      do {
        if ($reset) {
          reset($sequencias);
          $reset = FALSE;
        }

        $sequencia = current($sequencias);
        $aux_serie = $sequencia['serie_origem'];

        if ($ini_serie == $aux_serie) {
          // Achou a série da matrícula.
          if ($codSerie == $aux_serie) {
            $achou_serie = TRUE;
          }

          // Curso escolhido é da sequência de série
          if ($sequencia['curso_destino'] == $codCurso) {
            $pertence_sequencia = TRUE;
            $ini_serie          = $sequencia['serie_destino'];
            $reset              = TRUE;

            // Armazena a série de destino no array de série sequencial
            $serie_sequencia[]  = $sequencia['serie_destino'];
          }
          else {
            $ini_serie = $sequencia['serie_destino'];
            $reset     = TRUE;
          }
        }
      } while (each($sequencias));

      if ($achou_serie && $pertence_sequencia) {
        // Curso escolhido pertence a sequência da série da matrícula.
        break;
      }
    }

    // @pertenceSequencia{
    if (! $pertence_sequencia) {
      $lst_serie = _anosEscolaresEscolaCurso($db, $serie_sequencia, $codEscola, $codCurso);
    }
    // }@pertenceSequencia
  }

  return $lst_serie;
}

/**
 * Seleciona os anos escolares de um curso em uma escola, eliminando da seleção
 * as séries identificadas no array $serie_sequencia.
 *
 * @param  clsBanco $db
 * @param  array    $serie_sequencia Códigos dos anos escolares/séries da
 *                                   sequência.
 * @param  int      $codEscola       Código da escola.
 * @param  int      $codCurso        Código do curso.
 * @return array    (codSerie => nome)
 */
function _anosEscolaresEscolaCurso(clsBanco $db, array $serie_sequencia, $codEscola, $codCurso)
{
  $sql = sprintf('SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.escola_serie es
    WHERE
      es.ref_cod_escola = %d
      AND s.cod_serie = es.ref_cod_serie
      AND s.ref_cod_curso = %d
      AND s.ativo = 1',
    $codEscola, $codCurso
  );

  if (is_array($serie_sequencia)) {
    foreach ($serie_sequencia as $series)
      $sql .=  sprintf(' AND s.cod_serie != %d ', $series);
  }

  $sql .= '
    ORDER BY
      s.nm_serie ASC';

  $db->Consulta($sql);

  $lst_serie = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      list($cod, $nome) = $db->Tupla();
      $lst_serie[$cod] = $nome;
    }
  }

  return $lst_serie;
}

/**
 * Retorna os anos escolares da sequência.
 *
 * @param  clsBanco $db
 * @param  int      $codSerie  Código do ano escolar/série.
 * @return array    (codSerie => nome)
 */
function _anoEscolarSequencia(clsBanco $db, $codSerie)
{
  $sql = sprintf('SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.sequencia_serie ss
    WHERE
      ss.ref_serie_origem = %d
      AND ss.ref_serie_destino = s.cod_serie
      AND ss.ativo = 1
    ORDER BY
      s.nm_serie ASC',
    $codSerie
  );

  // Lista série sequência
  $db->Consulta($sql);

  $lst_serie = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      list($cod, $nome) = $db->Tupla();
      $lst_serie[$cod] = $nome;
    }
  }

  return $lst_serie;
}

/**
 * Retorna os anos escolares/série do curso de uma escola e instituição.
 *
 * @param  clsBanco $db
 * @param  int      $codEscola       Código da escola.
 * @param  int      $codCurso        Código do curso.
 * @param  int      $codInstituicao  Código da instituição.
 * @return array    (codSerie => nome)
 */
function _anoEscolarEscolaCurso(clsBanco $db, $codEscola, $codCurso, $codInstituicao)
{
  $sql = sprintf('SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.escola_serie es,
      pmieducar.curso c
    WHERE
      es.ref_cod_escola = %d
      AND es.ref_cod_serie = s.cod_serie
      AND s.ativo = 1
      AND c.cod_curso = %d
      AND s.ref_cod_curso = c.cod_curso
      AND c.ref_cod_instituicao = %d
    ORDER BY
      s.nm_serie ASC',
    $codEscola, $codCurso, $codInstituicao
  );

  $db->Consulta($sql);

  $resultado = array();
  if ($db->numLinhas()) {
    while ($db->ProximoRegistro()) {
      list($cod, $nome) = $db->Tupla();
      $resultado[$cod] = $nome;
    }
  }

  return $resultado;
}

if (is_numeric($_GET['alu']) && is_numeric($_GET['ins']) &&
    is_numeric($_GET['cur']) && is_numeric( $_GET['esc'])) {

  $sql = sprintf('SELECT
    m.cod_matricula,
    m.ref_ref_cod_escola,
    m.ref_cod_curso,
    m.ref_ref_cod_serie,
    m.ano,
    eal.ano AS ano_letivo,
    c.padrao_ano_escolar,
    m.aprovado,
    COALESCE((
      SELECT
        1
      FROM
        pmieducar.transferencia_solicitacao ts
      WHERE
        m.cod_matricula = ts.ref_cod_matricula_saida
        AND ts.ativo = 1
        AND ts.data_transferencia IS NULL
    ), 0) AS transferencia_int,
    COALESCE((
      SELECT
        1
      FROM
        pmieducar.transferencia_solicitacao ts
      WHERE
        m.cod_matricula = ts.ref_cod_matricula_saida
        AND ts.ativo = 1
        AND ts.data_transferencia IS NOT NULL
        AND ts.ref_cod_matricula_entrada IS NULL
    ), 0) AS transferencia_ext
    FROM
      pmieducar.matricula m,
      pmieducar.escola_ano_letivo eal,
      pmieducar.curso c
    WHERE
      m.ref_cod_aluno = %d
      AND m.ultima_matricula = 1
      AND m.ativo = 1
      AND m.ref_ref_cod_escola = eal.ref_cod_escola
      AND eal.andamento = 1
      AND eal.ativo = 1
      AND m.ref_cod_curso = c.cod_curso
      AND m.aprovado != 6
      AND c.ref_cod_instituicao = %d
    ORDER BY
      m.cod_matricula ASC',
    $_GET['alu'], $_GET['ins']
  );

  $db = new clsBanco();
  $db->Consulta($sql);

  $resultado = array();

  // caso o aluno nao tenha nenhuma matricula em determinada instituicao
  if (! $db->numLinhas()) {
    $resultado = _anoEscolarEscolaCurso($db, $_GET['esc'], $_GET['cur'], $_GET['ins']);
  }
  // Caso o aluno tenha matrícula(s) em determinada Instituição
  else {
    $db2 = new clsBanco();

    while ($db->ProximoRegistro()) {
      $lst_serie = array();

      list($matricula, $escola, $curso, $serie, $ano, $ano_letivo,
           $padrao_ano_escolar, $aprovado, $transferencia_int,
           $transferencia_ext) = $db->Tupla();

      // Caso o aluno tenha alguma solicitação de transferência externa em
      // aberto, libera todas as séries.
      // @transferencia{
      if ($transferencia_ext) {
        $resultado = _anoEscolarEscolaCurso($db2, $_GET['esc'], $_GET['cur'], $_GET['ins']);
        break;
      }
      // }@transferencia

      // @escola{
      if ($escola == $_GET['esc']) {

        // @curso{
        // Curso ao qual está matriculado é igual ao escolhido.
        if ($curso == $_GET['cur']) {

          // @reprovado{
          // Situação reprovado.
          // Ano letivo da escola maior que ano da matrícula OU não padrão.
          if (App_Model_MatriculaSituacao::REPROVADO == $aprovado &&
              ($ano_letivo > $ano || !$padrao_ano_escolar)) {
            $lst_serie = _mesmoAnoEscolar($db2, $serie);
          }
          // }@reprovado

          // @aprovado{
          // Situação aprovado.
          // Ano letivo da escola maior que ano da matrícula OU não padrão.
          elseif (App_Model_MatriculaSituacao::APROVADO == $aprovado &&
                  ($ano_letivo > $ano || !$padrao_ano_escolar)) {
            $lst_serie = _anoEscolarSequencia($db2, $serie);
          }
          // }@aprovado
        }
        // }@curso

        // @curso-diferente{
        // Curso matriculado diferente do curso escolhido.
        else {
          // O curso é diferente mas o ano escolar/série faz parte da sequência.
          // Isso se torna verdadeiro caso as séries sejam listadas no primeiro
          // IF @aprovado.

          // @aprovado{
          // Ano letivo da escola maior que ano da matrícula OU curso não padrão.
          if (App_Model_MatriculaSituacao::APROVADO == $aprovado &&
              ($ano_letivo > $ano || !$padrao_ano_escolar)) {
            // Lista anos escolares (séries) da sequência.
            $lst_serie = _anosEscolaresSequenciaCurso($db2, $serie, $_GET['cur']);
          }
          // }@aprovado

          $situacoes = array(
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::REPROVADO,
            App_Model_MatriculaSituacao::EM_ANDAMENTO
          );

          // O curso é diferente e não faz parte da sequência.
          // @emAndamento{
          if (in_array($aprovado, $situacoes)) {
            // Lista os anos escolares/séries da sequência.
            $lst_serie = _sequencias($db2, $serie, $_GET['esc'], $_GET['cur']);
          }
          // }@emAndamento
        }
        // }@curso-diferente
      }
      // }@escola

      // @escolaDiferente{
      elseif (($escola != $_GET['esc']) && ($transferencia_int == 1)) {

        // Curso matriculado igual ao curso escolhido.
        // @curso{
        if ($curso == $_GET['cur']) {

          // Reprovado ou em andamento.
          $situacoes = array(
            App_Model_MatriculaSituacao::REPROVADO,
            App_Model_MatriculaSituacao::EM_ANDAMENTO
          );

          // @emAndamento{
          if (in_array($aprovado, $situacoes)) {
            // Lista a mesma série.
            $lst_serie = _mesmoAnoEscolar($db2, $serie);
          }
          // }@emAndamento

          // @aprovado{
          elseif (App_Model_MatriculaSituacao::APROVADO == $aprovado) {
            // Lista série sequência
            $lst_serie = _anoEscolarSequencia($db2, $serie);
          }
          // }@aprovado
        }
        // }@curso

        // Curso matriculado diferente do curso escolhido.
        // @cursoDiferente{
        else {

          // Curso é diferente mas faz parte da sequência.
          // @aprovado{
          if ($aprovado == 1) {
            // Lista anos escolares (séries) da sequência.
            $lst_serie = _anosEscolaresSequenciaCurso($db2, $serie, $_GET['cur']);
          }
          // }@aprovado

          $situacoes = array(
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::REPROVADO,
            App_Model_MatriculaSituacao::EM_ANDAMENTO
          );

          // Curso é diferente e não faz parte da sequência.
          // @emAndamento{
          if (in_array($aprovado, $situacoes)) {
            // Lista os anos escolares/séries da sequência.
            $lst_serie = _sequencias($db2, $serie, $_GET['esc'], $_GET['cur']);
          }
          // }@emAndamento

        }
        // }@cursoDiferente
      }
      // }@escolaDiferente

      // @escolaDiferenteNaoTransferencia
      elseif ($escola != $_GET['esc'] && !$transferencia_int) {

        // @cursoDiferente
        // Curso matriculado diferente do curso escolhido.
        if ($curso != $_GET['cur']) {

          // Situações aprovado e reprovado.
          $situacoes = array(
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::REPROVADO
          );

          // @aprovado{
          if (in_array($aprovado, $situacoes)) {
            // Lista os anos escolares/séries da sequência.
            $lst_serie = _sequencias($db2, $serie, $_GET['esc'], $_GET['cur']);
          }
          // }@aprovado

        }
        // }@cursoDiferente

        // @cursoIgual{
        else {

          // Curso matriculado igual ao curso escolhido.
          if ($curso == $_GET['cur']) {

            // Situação reprovado ou tranferência.
            // @reprovado{
            if ($aprovado == 2 || $transferencia_int == 1) {
              // Lista a mesma série.
              $lst_serie = _mesmoAnoEscolar($db2, $serie);
            }
            // }@reprovado

            // Situação aprovado
            // @aprovado{
            elseif ($aprovado == 1) {
              // Lista ano escolar/série da sequência.
              $lst_serie = _anoEscolarSequencia($db2, $serie);
            }
            // }@aprovado

          }
        }
        // }@cursoIgual
      }

      if (empty($resultado)) {
        $resultado = $lst_serie;
      }
      else {
        $resultado = array_intersect_assoc($lst_serie,$resultado);
      }

    }
  }
}

if (! empty($resultado)) {
  foreach ($resultado as $cod => $nome) {
    print sprintf('<serie cod_serie="%d">%s</serie>' . "\n", $cod, $nome);
  }
}

print '</query>';