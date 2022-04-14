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

        $db->Consulta("{$sql}");

        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <aluno cod_aluno=\"{$cod}\">{$nome}</aluno>\n";
        }
    }

    echo '</query>';
