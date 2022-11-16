<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['tur'])) {
        $db = new clsBanco();

        $sql = "
            SELECT t.ref_cod_matricula, p.nome
            FROM pmieducar.aluno a
                JOIN pmieducar.matricula m
                    ON (a.cod_aluno = m.ref_cod_aluno)
                JOIN pmieducar.matricula_turma t
                    ON (m.cod_matricula = t.ref_cod_matricula)
                JOIN cadastro.pessoa p
                    ON (p.idpes = a.ref_idpes)
        ";

        if(is_numeric($_GET['ccur'])) {
            $sql .= "
                FULL JOIN pmieducar.dispensa_disciplina c
                    ON (m.cod_matricula = c.ref_cod_matricula AND {$_GET['ccur']} = c.ref_cod_disciplina)
                WHERE t.ref_cod_turma = {$_GET['tur']} AND c.ref_cod_disciplina IS NULL AND c.ref_cod_matricula IS NULL
            ";
        } else {
            $sql .= "WHERE t.ref_cod_turma = {$_GET['tur']}";
        }

        if (isset($_GET['data']) && !empty($_GET['data'])) {
            $dateSql = implode("-",array_reverse(explode("/",$_GET['data'])));
            $sql .= " AND T.data_enturmacao <= '{$dateSql}' AND (T.data_exclusao IS NULL OR T.data_exclusao >= '{$dateSql}') ";
        }

        $sql .= " AND m.ativo = '1' AND m.ultima_matricula = '1'";

        $sql .= " ORDER BY p.nome ASC";

        $db->Consulta("{$sql}");


        $objFrequencia = new clsModulesFrequencia();
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();

            $ccur = is_numeric($_GET['ccur']) ? $_GET['ccur'] : null;

            $qtdFaltas = $objFrequencia->getTotalFaltas($cod, $ccur);

            echo "  <aluno cod_aluno=\"{$cod}\" qtd_faltas=\"{$qtdFaltas}\">{$nome}</aluno>\n";
        }
    }

    echo '</query>';
