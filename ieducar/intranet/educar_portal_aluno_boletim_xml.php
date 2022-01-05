<?php

    header('Content-type: text/xml');

    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
    if (is_numeric($_GET['nota'])) {
        $db = new clsBanco();
        
        $sql = "
        SELECT 
        cc.nome, 
        STRING_AGG (ncc.nota_arredondada::character varying, ',' ORDER BY ncc.etapa ASC ) AS Notas
     
         FROM pmieducar.matricula AS m
     
         JOIN modules.nota_aluno AS na
             ON na.matricula_id = m.cod_matricula
     
         JOIN modules.nota_componente_curricular AS ncc
             ON ncc.nota_aluno_id = na.id
     
         JOIN modules.componente_curricular AS cc
             ON ncc.componente_curricular_id = cc.id
        WHERE cod_matricula = {$_GET['matricula']}
        "; 
        while ($db->ProximoRegistro()) {
            list($cod, $nome, $notas) = $db->Tupla();
            echo "  <aluno cod_matricula=\"{$cod}\">{$nome}</aluno>\n";
            echo "  <aluno notas=\"{$notas}\">{$notas}</aluno>\n";
        }
    }

        if(is_numeric($_GET['falta'])) {
            $db = new clsBanco();
            $sql .= "
            SELECT  
            cc.nome, 
            STRING_AGG (fcc.quantidade::character varying, ',' ORDER BY fcc.etapa ASC) AS Faltas
          
          
          FROM pmieducar.matricula AS m
          JOIN modules.falta_aluno AS fa
              ON fa.matricula_id = m.cod_matricula
          JOIN modules.falta_componente_curricular AS fcc
              ON fa.id = fcc.falta_aluno_id
          JOIN modules.componente_curricular AS cc
              ON fcc.componente_curricular_id = cc.id
            WHERE cod_matricula = {$_GET['mat']}
            ";
            while ($db->ProximoRegistro()) {
                list($cod, $nome, $faltas) = $db->Tupla();
                echo "  <aluno cod_matricula=\"{$cod}\">{$nome}</aluno>\n";
                echo "  <aluno notas=\"{$faltas}\">{$faltas}</aluno>\n";
            }
        } 
        else {
            $db = new clsBanco();
            $sql .= " 
            SELECT
            STRING_AGG (fg.quantidade::character varying, ',' ORDER BY fg.etapa ASC) AS Faltas
            
           FROM pmieducar.matricula AS m
            JOIN modules.falta_aluno AS fa
                ON fa.matricula_id = m.cod_matricula
            JOIN modules.falta_geral AS fg
                ON fa.id = fg.falta_aluno_id
            WHERE cod_matricula = {$_GET['mat']}   
                ";
             while ($db->ProximoRegistro()) {
                    list($cod, $nome, $faltas) = $db->Tupla();
                    echo "  <aluno cod_matricula=\"{$cod}\">{$nome}</aluno>\n";
                    echo "  <aluno notas=\"{$faltas}\">{$faltas}</aluno>\n";
                }
            
        }

        $db->Consulta("{$sql}");

       
   
    echo '</query>';
