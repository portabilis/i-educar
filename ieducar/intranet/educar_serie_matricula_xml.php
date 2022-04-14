<?php

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding=""?>' . "\n";
print '<query xmlns="sugestoes">' . "\n";

$pessoa_logada = \Illuminate\Support\Facades\Auth::id();

/**
 * @param array  $data
 * @param string $index
 *
 * @return array $data[$index] => key($data)
 */
function _createArrayFromIndex(array $data, $index)
{
    $ret = [];
    foreach ($data as $key => $entry) {
        if (isset($entry[$index])) {
            $ret[$entry[$index]] = $key;
        }
    }

    return $ret;
}

/**
 * @param clsBanco $db
 * @param string   $sql
 *
 * @return array (codSerie => nome)
 */
function _getAnoEscolar(clsBanco $db, $sql)
{
    $db->Consulta($sql);

    $resultado = [];
    if ($db->numLinhas()) {
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            $resultado[$cod] = $nome;
        }
    }

    return $resultado;
}

/**
 * Retorna o ano escolar/série de uma escola.
 *
 * @param clsBanco $db
 * @param int      $codSerie Código do ano escolar/série.
 *
 * @return array (codSerie => nome)
 */
function _mesmoAnoEscolar(clsBanco $db, $codEscola, $codSerie)
{
    $sql = sprintf(
        'SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.escola_serie es
    WHERE
      s.cod_serie = es.ref_cod_serie
      AND es.ref_cod_escola = %d
      AND es.ativo = 1
      AND s.cod_serie = %d
      AND s.ativo = 1
    ORDER BY
      s.nm_serie ASC',
        $codEscola,
        $codSerie
    );

    return _getAnoEscolar($db, $sql);
}

/**
 * Retorna os anos escolares/séries da sequência de série de uma escola.
 *
 * @param clsBanco $db
 * @param int      $codEscola Código da escola.
 * @param int      $codSerie  Código do ano escolar/série.
 *
 * @return array (codSerie => nome)
 */
function _anoEscolarSequencia(clsBanco $db, $codEscola, $codSerie)
{
    $sql = sprintf(
        'SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.sequencia_serie ss,
      pmieducar.escola_serie es
    WHERE
      ss.ref_serie_destino = s.cod_serie
      AND s.cod_serie = es.ref_cod_serie
      AND es.ref_cod_escola = %d
      AND es.ativo = 1
      AND ss.ref_serie_origem = %d
      AND ss.ativo = 1
    ORDER BY
      s.nm_serie ASC',
        $codEscola,
        $codSerie
    );

    return _getAnoEscolar($db, $sql);
}

/**
 * Retorna os anos escolares/série do curso de uma escola.
 *
 * @param clsBanco $db
 * @param int      $codEscola Código da escola.
 * @param int      $codCurso  Código do curso.
 *
 * @return array (codSerie => nome)
 */
function _anoEscolarEscolaCurso(clsBanco $db, $codEscola, $codCurso)
{
    $sql = sprintf(
        'SELECT
      s.cod_serie,
      s.nm_serie
    FROM
      pmieducar.serie s,
      pmieducar.escola_serie es,
      pmieducar.curso c
    WHERE
      es.ref_cod_escola = %d
      AND es.ref_cod_serie = s.cod_serie
      AND es.ativo = 1
      AND s.ref_cod_curso = c.cod_curso
      AND s.ativo = 1
      AND c.cod_curso = %d
    ORDER BY
      s.nm_serie ASC',
        $codEscola,
        $codCurso
    );

    return _getAnoEscolar($db, $sql);
}

$resultado = [];

if (is_numeric($_GET['alu']) && is_numeric($_GET['ins']) &&
    is_numeric($_GET['cur']) && is_numeric($_GET['esc'])) {
    $sql = sprintf(
        'SELECT
    m.cod_matricula AS cod_matricula,
    m.ref_ref_cod_escola AS cod_escola,
    m.ref_cod_curso AS cod_curso,
    m.ref_ref_cod_serie AS cod_serie,
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
        $_GET['alu'],
        $_GET['ins']
    );

    $db = new clsBanco();
    $db->Consulta($sql);

    $matriculas = [];
    while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $matriculas[$tupla['cod_matricula']] = $tupla;
    }

    $codEscola = $_GET['esc'];
    $codCurso  = $_GET['cur'];

    if (count($matriculas)) {
        $cursos = _createArrayFromIndex($matriculas, 'cod_curso');

        // Mesmo curso?
        if (in_array($codCurso, array_keys($cursos))) {
            // Matrícula do curso.
            $matricula = $matriculas[$cursos[$codCurso]];

            // Matrícula reprovada, retorna o mesmo ano escolar da matrícula para a escola selecionada.
            if (App_Model_MatriculaSituacao::REPROVADO == $matricula['aprovado']) {
                $resultado = _mesmoAnoEscolar($db, $codEscola, $matricula['cod_serie']);
            }

            // Matrícula aprovada, retorna os anos escolares da sequência de série para a escola selecionada.
            elseif (App_Model_MatriculaSituacao::APROVADO == $matricula['aprovado']) {
                $resultado = _anoEscolarSequencia($db, $codEscola, $matricula['cod_serie']);
            }

            // Matrícula em andamento
            elseif (App_Model_MatriculaSituacao::TRANSFERIDO == $matricula['aprovado']) {
                // Transferência interna, retorna o mesmo ano escolar da matrícula para a escola selecionada.
                if (1 == $matricula['transferencia_int']) {
                    $resultado = _mesmoAnoEscolar($db, $codEscola, $matricula['cod_serie']);
                }

                // Transferência externa, retorna os anos escolares da sequência de série para a escola selecionada.
                elseif (1 == $matricula['transferencia_ext']) {
                    $resultado = _anoEscolarSequencia($db, $codEscola, $matricula['cod_serie']);
                }
            }
        } else {
            // Retorna todos os anos escolares para o curso em uma escola.
            $resultado = _anoEscolarEscolaCurso($db, $codEscola, $codCurso);
        }
    } else {
        $resultado = _anoEscolarEscolaCurso($db, $codEscola, $codCurso);
    }
}

foreach ($resultado as $cod => $nome) {
    print sprintf('<serie cod_serie="%d">%s</serie>' . "\n", $cod, $nome);
}

print '</query>';
