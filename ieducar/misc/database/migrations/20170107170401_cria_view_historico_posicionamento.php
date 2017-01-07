<?php

use Phinx\Migration\AbstractMigration;

class CriaViewHistoricoPosicionamento extends AbstractMigration
{
    public function up()
    {
        $this->execute("
CREATE OR REPLACE VIEW relatorio.view_dados_historico_posicionamento AS
SELECT he.ref_cod_aluno,
       hd.nm_disciplina,

  (SELECT COALESCE(MIN(ordenamento), 9999)
   FROM pmieducar.historico_escolar hee2
   INNER JOIN pmieducar.historico_disciplinas hdd2 ON (hdd2.ref_ref_cod_aluno = hee2.ref_cod_aluno
                                                       AND hdd2.ref_sequencial = hee2.sequencial)
   WHERE hee2.ref_cod_aluno = he.ref_cod_aluno
     AND hdd2.nm_disciplina = hd.nm_disciplina) AS order_componente,
       he1.ano AS ano1,
       he2.ano AS ano2,
       he3.ano AS ano3,
       he4.ano AS ano4,
       he5.ano AS ano5,
       he6.ano AS ano6,
       he7.ano AS ano7,
       he8.ano AS ano8,
       he9.ano AS ano9,
       he1.escola AS escola1,
       he2.escola AS escola2,
       he3.escola AS escola3,
       he4.escola AS escola4,
       he5.escola AS escola5,
       he6.escola AS escola6,
       he7.escola AS escola7,
       he8.escola AS escola8,
       he9.escola AS escola9,
       he1.escola_cidade AS escola_cidade1,
       he2.escola_cidade AS escola_cidade2,
       he3.escola_cidade AS escola_cidade3,
       he4.escola_cidade AS escola_cidade4,
       he5.escola_cidade AS escola_cidade5,
       he6.escola_cidade AS escola_cidade6,
       he7.escola_cidade AS escola_cidade7,
       he8.escola_cidade AS escola_cidade8,
       he9.escola_cidade AS escola_cidade9,
       he1.escola_uf AS escola_uf1,
       he2.escola_uf AS escola_uf2,
       he3.escola_uf AS escola_uf3,
       he4.escola_uf AS escola_uf4,
       he5.escola_uf AS escola_uf5,
       he6.escola_uf AS escola_uf6,
       he7.escola_uf AS escola_uf7,
       he8.escola_uf AS escola_uf8,
       he9.escola_uf AS escola_uf9,
       he1.nm_serie AS nm_serie1,
       he2.nm_serie AS nm_serie2,
       he3.nm_serie AS nm_serie3,
       he4.nm_serie AS nm_serie4,
       he5.nm_serie AS nm_serie5,
       he6.nm_serie AS nm_serie6,
       he7.nm_serie AS nm_serie7,
       he8.nm_serie AS nm_serie8,
       he9.nm_serie AS nm_serie9,
       he1.carga_horaria AS ch1,
       he2.carga_horaria AS ch2,
       he3.carga_horaria AS ch3,
       he4.carga_horaria AS ch4,
       he5.carga_horaria AS ch5,
       he6.carga_horaria AS ch6,
       he7.carga_horaria AS ch7,
       he8.carga_horaria AS ch8,
       he9.carga_horaria AS ch9,
       he1.frequencia AS freq1,
       he2.frequencia AS freq2,
       he3.frequencia AS freq3,
       he4.frequencia AS freq4,
       he5.frequencia AS freq5,
       he6.frequencia AS freq6,
       he7.frequencia AS freq7,
       he8.frequencia AS freq8,
       he9.frequencia AS freq9,
       he1.observacao AS obs1,
       he2.observacao AS obs2,
       he3.observacao AS obs3,
       he4.observacao AS obs4,
       he5.observacao AS obs5,
       he6.observacao AS obs6,
       he7.observacao AS obs7,
       he8.observacao AS obs8,
       he9.observacao AS obs9,
       hd1.nota AS nota1,
       hd2.nota AS nota2,
       hd3.nota AS nota3,
       hd4.nota AS nota4,
       hd5.nota AS nota5,
       hd6.nota AS nota6,
       hd7.nota AS nota7,
       hd8.nota AS nota8,
       hd9.nota AS nota9,
       hd1.faltas AS faltas1,
       hd2.faltas AS faltas2,
       hd3.faltas AS faltas3,
       hd4.faltas AS faltas4,
       hd5.faltas AS faltas5,
       hd6.faltas AS faltas6,
       hd7.faltas AS faltas7,
       hd8.faltas AS faltas8,
       hd9.faltas AS faltas9,
       CASE
           WHEN he1.aceleracao = 1 THEN (CASE
                                             WHEN he1.aprovado = 1 THEN 'Apro'
                                             WHEN he1.aprovado = 12 THEN 'AprDep'
                                             WHEN he1.aprovado = 13 THEN 'AprCo'
                                             WHEN he1.aprovado = 2 THEN 'Repr'
                                             WHEN he1.aprovado = 3 THEN 'Curs'
                                             WHEN he1.aprovado = 4 THEN 'Tran'
                                             WHEN he1.aprovado = 5 THEN 'Recl'
                                             WHEN he1.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he1.aprovado = 1 THEN 'Apro'
                     WHEN he1.aprovado = 12 THEN 'AprDep'
                     WHEN he1.aprovado = 13 THEN 'AprCo'
                     WHEN he1.aprovado = 2 THEN 'Repr'
                     WHEN he1.aprovado = 3 THEN 'Curs'
                     WHEN he1.aprovado = 4 THEN 'Tran'
                     WHEN he1.aprovado = 5 THEN 'Recl'
                     WHEN he1.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status1,
       CASE
           WHEN he2.aceleracao = 1 THEN (CASE
                                             WHEN he2.aprovado = 1 THEN 'Apro'
                                             WHEN he2.aprovado = 12 THEN 'AprDep'
                                             WHEN he2.aprovado = 13 THEN 'AprCo'
                                             WHEN he2.aprovado = 2 THEN 'Repr'
                                             WHEN he2.aprovado = 3 THEN 'Curs'
                                             WHEN he2.aprovado = 4 THEN 'Tran'
                                             WHEN he2.aprovado = 5 THEN 'Recl'
                                             WHEN he2.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he2.aprovado = 1 THEN 'Apro'
                     WHEN he2.aprovado = 12 THEN 'AprDep'
                     WHEN he2.aprovado = 13 THEN 'AprCo'
                     WHEN he2.aprovado = 2 THEN 'Repr'
                     WHEN he2.aprovado = 3 THEN 'Curs'
                     WHEN he2.aprovado = 4 THEN 'Tran'
                     WHEN he2.aprovado = 5 THEN 'Recl'
                     WHEN he2.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status2,
       CASE
           WHEN he3.aceleracao = 1 THEN (CASE
                                             WHEN he3.aprovado = 1 THEN 'Apro'
                                             WHEN he3.aprovado = 12 THEN 'AprDep'
                                             WHEN he3.aprovado = 13 THEN 'AprCo'
                                             WHEN he3.aprovado = 2 THEN 'Repr'
                                             WHEN he3.aprovado = 3 THEN 'Curs'
                                             WHEN he3.aprovado = 4 THEN 'Tran'
                                             WHEN he3.aprovado = 5 THEN 'Recl'
                                             WHEN he3.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he3.aprovado = 1 THEN 'Apro'
                     WHEN he3.aprovado = 12 THEN 'AprDep'
                     WHEN he3.aprovado = 13 THEN 'AprCo'
                     WHEN he3.aprovado = 2 THEN 'Repr'
                     WHEN he3.aprovado = 3 THEN 'Curs'
                     WHEN he3.aprovado = 4 THEN 'Tran'
                     WHEN he3.aprovado = 5 THEN 'Recl'
                     WHEN he3.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status3,
       CASE
           WHEN he4.aceleracao = 1 THEN (CASE
                                             WHEN he4.aprovado = 1 THEN 'Apro'
                                             WHEN he4.aprovado = 12 THEN 'AprDep'
                                             WHEN he4.aprovado = 13 THEN 'AprCo'
                                             WHEN he4.aprovado = 2 THEN 'Repr'
                                             WHEN he4.aprovado = 3 THEN 'Curs'
                                             WHEN he4.aprovado = 4 THEN 'Tran'
                                             WHEN he4.aprovado = 5 THEN 'Recl'
                                             WHEN he4.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he4.aprovado = 1 THEN 'Apro'
                     WHEN he4.aprovado = 12 THEN 'AprDep'
                     WHEN he4.aprovado = 13 THEN 'AprCo'
                     WHEN he4.aprovado = 2 THEN 'Repr'
                     WHEN he4.aprovado = 3 THEN 'Curs'
                     WHEN he4.aprovado = 4 THEN 'Tran'
                     WHEN he4.aprovado = 5 THEN 'Recl'
                     WHEN he4.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status4,
       CASE
           WHEN he5.aceleracao = 1 THEN (CASE
                                             WHEN he5.aprovado = 1 THEN 'Apro'
                                             WHEN he5.aprovado = 12 THEN 'AprDep'
                                             WHEN he5.aprovado = 13 THEN 'AprCo'
                                             WHEN he5.aprovado = 2 THEN 'Repr'
                                             WHEN he5.aprovado = 3 THEN 'Curs'
                                             WHEN he5.aprovado = 4 THEN 'Tran'
                                             WHEN he5.aprovado = 5 THEN 'Recl'
                                             WHEN he5.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he5.aprovado = 1 THEN 'Apro'
                     WHEN he5.aprovado = 12 THEN 'AprDep'
                     WHEN he5.aprovado = 13 THEN 'AprCo'
                     WHEN he5.aprovado = 2 THEN 'Repr'
                     WHEN he5.aprovado = 3 THEN 'Curs'
                     WHEN he5.aprovado = 4 THEN 'Tran'
                     WHEN he5.aprovado = 5 THEN 'Recl'
                     WHEN he5.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status5,
       CASE
           WHEN he6.aceleracao = 1 THEN (CASE
                                             WHEN he6.aprovado = 1 THEN 'Apro'
                                             WHEN he6.aprovado = 12 THEN 'AprDep'
                                             WHEN he6.aprovado = 13 THEN 'AprCo'
                                             WHEN he6.aprovado = 2 THEN 'Repr'
                                             WHEN he6.aprovado = 3 THEN 'Curs'
                                             WHEN he6.aprovado = 4 THEN 'Tran'
                                             WHEN he6.aprovado = 5 THEN 'Recl'
                                             WHEN he6.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he6.aprovado = 1 THEN 'Apro'
                     WHEN he6.aprovado = 12 THEN 'AprDep'
                     WHEN he6.aprovado = 13 THEN 'AprCo'
                     WHEN he6.aprovado = 2 THEN 'Repr'
                     WHEN he6.aprovado = 3 THEN 'Curs'
                     WHEN he6.aprovado = 4 THEN 'Tran'
                     WHEN he6.aprovado = 5 THEN 'Recl'
                     WHEN he6.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status6,
       CASE
           WHEN he7.aceleracao = 1 THEN (CASE
                                             WHEN he7.aprovado = 1 THEN 'Apro'
                                             WHEN he7.aprovado = 12 THEN 'AprDep'
                                             WHEN he7.aprovado = 13 THEN 'AprCo'
                                             WHEN he7.aprovado = 2 THEN 'Repr'
                                             WHEN he7.aprovado = 3 THEN 'Curs'
                                             WHEN he7.aprovado = 4 THEN 'Tran'
                                             WHEN he7.aprovado = 5 THEN 'Recl'
                                             WHEN he7.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he7.aprovado = 1 THEN 'Apro'
                     WHEN he7.aprovado = 12 THEN 'AprDep'
                     WHEN he7.aprovado = 13 THEN 'AprCo'
                     WHEN he7.aprovado = 2 THEN 'Repr'
                     WHEN he7.aprovado = 3 THEN 'Curs'
                     WHEN he7.aprovado = 4 THEN 'Tran'
                     WHEN he7.aprovado = 5 THEN 'Recl'
                     WHEN he7.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status7,
       CASE
           WHEN he8.aceleracao = 1 THEN (CASE
                                             WHEN he8.aprovado = 1 THEN 'Apro'
                                             WHEN he8.aprovado = 12 THEN 'AprDep'
                                             WHEN he8.aprovado = 13 THEN 'AprCo'
                                             WHEN he8.aprovado = 2 THEN 'Repr'
                                             WHEN he8.aprovado = 3 THEN 'Curs'
                                             WHEN he8.aprovado = 4 THEN 'Tran'
                                             WHEN he8.aprovado = 5 THEN 'Recl'
                                             WHEN he8.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he8.aprovado = 1 THEN 'Apro'
                     WHEN he8.aprovado = 12 THEN 'AprDep'
                     WHEN he8.aprovado = 13 THEN 'AprCo'
                     WHEN he8.aprovado = 2 THEN 'Repr'
                     WHEN he8.aprovado = 3 THEN 'Curs'
                     WHEN he8.aprovado = 4 THEN 'Tran'
                     WHEN he8.aprovado = 5 THEN 'Recl'
                     WHEN he8.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status8,
       CASE
           WHEN he9.aceleracao = 1 THEN (CASE
                                             WHEN he9.aprovado = 1 THEN 'Apro'
                                             WHEN he9.aprovado = 12 THEN 'AprDep'
                                             WHEN he9.aprovado = 13 THEN 'AprCo'
                                             WHEN he9.aprovado = 2 THEN 'Repr'
                                             WHEN he9.aprovado = 3 THEN 'Curs'
                                             WHEN he9.aprovado = 4 THEN 'Tran'
                                             WHEN he9.aprovado = 5 THEN 'Recl'
                                             WHEN he9.aprovado = 6 THEN 'Aban'
                                             ELSE ''
                                         END) || ' AC'
           ELSE (CASE
                     WHEN he9.aprovado = 1 THEN 'Apro'
                     WHEN he9.aprovado = 12 THEN 'AprDep'
                     WHEN he9.aprovado = 13 THEN 'AprCo'
                     WHEN he9.aprovado = 2 THEN 'Repr'
                     WHEN he9.aprovado = 3 THEN 'Curs'
                     WHEN he9.aprovado = 4 THEN 'Tran'
                     WHEN he9.aprovado = 5 THEN 'Recl'
                     WHEN he9.aprovado = 6 THEN 'Aban'
                     ELSE ''
                 END)
       END AS status9
FROM pmieducar.historico_escolar he
INNER JOIN pmieducar.historico_disciplinas hd ON (hd.ref_ref_cod_aluno = he.ref_cod_aluno
                                                  AND hd.ref_sequencial = he.sequencial)
LEFT JOIN pmieducar.historico_escolar he1 ON (he1.ref_cod_aluno = he.ref_cod_aluno
                                              AND he1.posicao = 1
                                              AND he1.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 1)
                                              AND CASE
                                                      WHEN he1.historico_grade_curso_id = 3 THEN he1.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 1)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he2 ON (he2.ref_cod_aluno = he.ref_cod_aluno
                                              AND he2.posicao = 2
                                              AND he2.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 2)
                                              AND CASE
                                                      WHEN he2.historico_grade_curso_id = 3 THEN he2.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 2)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he3 ON (he3.ref_cod_aluno = he.ref_cod_aluno
                                              AND he3.posicao = 3
                                              AND he3.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 3)
                                              AND CASE
                                                      WHEN he3.historico_grade_curso_id = 3 THEN he3.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 3)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he4 ON (he4.ref_cod_aluno = he.ref_cod_aluno
                                              AND he4.posicao = 4
                                              AND he4.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 4)
                                              AND CASE
                                                      WHEN he4.historico_grade_curso_id = 3 THEN he4.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 4)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he5 ON (he5.ref_cod_aluno = he.ref_cod_aluno
                                              AND he5.posicao = 5
                                              AND he5.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 5)
                                              AND CASE
                                                      WHEN he5.historico_grade_curso_id = 3 THEN he5.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 5)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he6 ON (he6.ref_cod_aluno = he.ref_cod_aluno
                                              AND he6.posicao = 6
                                              AND he6.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 6)
                                              AND CASE
                                                      WHEN he6.historico_grade_curso_id = 3 THEN he6.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 6)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he7 ON (he7.ref_cod_aluno = he.ref_cod_aluno
                                              AND he7.posicao = 7
                                              AND he7.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 7)
                                              AND CASE
                                                      WHEN he7.historico_grade_curso_id = 3 THEN he7.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 7)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he8 ON (he8.ref_cod_aluno = he.ref_cod_aluno
                                              AND he8.posicao = 8
                                              AND he8.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 8)
                                              AND CASE
                                                      WHEN he8.historico_grade_curso_id = 3 THEN he8.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 8)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_escolar he9 ON (he9.ref_cod_aluno = he.ref_cod_aluno
                                              AND he9.posicao = 9
                                              AND he9.ano =
                                                (SELECT MAX(ano)
                                                 FROM pmieducar.historico_escolar hee
                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                   AND COALESCE(hee.dependencia, FALSE) = FALSE
                                                   AND hee.posicao = 9)
                                              AND CASE
                                                      WHEN he9.historico_grade_curso_id = 3 THEN he9.sequencial =
                                                             (SELECT MAX(sequencial)
                                                              FROM pmieducar.historico_escolar hee
                                                              WHERE hee.ref_cod_aluno = he.ref_cod_aluno
                                                                AND hee.posicao = 9)
                                                      ELSE TRUE
                                                  END)
LEFT JOIN pmieducar.historico_disciplinas hd1 ON (hd1.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd1.nm_disciplina = hd.nm_disciplina
                                                  AND hd1.ref_sequencial = he1.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd2 ON (hd2.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd2.nm_disciplina = hd.nm_disciplina
                                                  AND hd2.ref_sequencial = he2.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd3 ON (hd3.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd3.nm_disciplina = hd.nm_disciplina
                                                  AND hd3.ref_sequencial = he3.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd4 ON (hd4.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd4.nm_disciplina = hd.nm_disciplina
                                                  AND hd4.ref_sequencial = he4.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd5 ON (hd5.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd5.nm_disciplina = hd.nm_disciplina
                                                  AND hd5.ref_sequencial = he5.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd6 ON (hd6.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd6.nm_disciplina = hd.nm_disciplina
                                                  AND hd6.ref_sequencial = he6.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd7 ON (hd7.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd7.nm_disciplina = hd.nm_disciplina
                                                  AND hd7.ref_sequencial = he7.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd8 ON (hd8.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd8.nm_disciplina = hd.nm_disciplina
                                                  AND hd8.ref_sequencial = he8.sequencial)
LEFT JOIN pmieducar.historico_disciplinas hd9 ON (hd9.ref_ref_cod_aluno = hd.ref_ref_cod_aluno
                                                  AND hd9.nm_disciplina = hd.nm_disciplina
                                                  AND hd9.ref_sequencial = he9.sequencial)
WHERE he.ativo = 1
GROUP BY he.ref_cod_aluno,
         hd.nm_disciplina,
         he1.ano,
         he2.ano,
         he3.ano,
         he4.ano,
         he5.ano,
         he6.ano,
         he7.ano,
         he8.ano,
         he9.ano,
         he1.escola,
         he2.escola,
         he3.escola,
         he4.escola,
         he5.escola,
         he6.escola,
         he7.escola,
         he8.escola,
         he9.escola,
         he1.escola_cidade,
         he2.escola_cidade,
         he3.escola_cidade,
         he4.escola_cidade,
         he5.escola_cidade,
         he6.escola_cidade,
         he7.escola_cidade,
         he8.escola_cidade,
         he9.escola_cidade,
         he1.escola_uf,
         he2.escola_uf,
         he3.escola_uf,
         he4.escola_uf,
         he5.escola_uf,
         he6.escola_uf,
         he7.escola_uf,
         he8.escola_uf,
         he9.escola_uf,
         he1.nm_serie,
         he2.nm_serie,
         he3.nm_serie,
         he4.nm_serie,
         he5.nm_serie,
         he6.nm_serie,
         he7.nm_serie,
         he8.nm_serie,
         he9.nm_serie,
         he1.carga_horaria,
         he2.carga_horaria,
         he3.carga_horaria,
         he4.carga_horaria,
         he5.carga_horaria,
         he6.carga_horaria,
         he7.carga_horaria,
         he8.carga_horaria,
         he9.carga_horaria,
         he1.frequencia,
         he2.frequencia,
         he3.frequencia,
         he4.frequencia,
         he5.frequencia,
         he6.frequencia,
         he7.frequencia,
         he8.frequencia,
         he9.frequencia,
         he1.observacao,
         he2.observacao,
         he3.observacao,
         he4.observacao,
         he5.observacao,
         he6.observacao,
         he7.observacao,
         he8.observacao,
         he9.observacao,
         hd1.nota,
         hd2.nota,
         hd3.nota,
         hd4.nota,
         hd5.nota,
         hd6.nota,
         hd7.nota,
         hd8.nota,
         hd9.nota,
         hd1.faltas,
         hd2.faltas,
         hd3.faltas,
         hd4.faltas,
         hd5.faltas,
         hd6.faltas,
         hd7.faltas,
         hd8.faltas,
         hd9.faltas,
         he1.aceleracao,
         he2.aceleracao,
         he3.aceleracao,
         he4.aceleracao,
         he5.aceleracao,
         he6.aceleracao,
         he7.aceleracao,
         he8.aceleracao,
         he9.aceleracao,
         he1.aprovado,
         he2.aprovado,
         he3.aprovado,
         he4.aprovado,
         he5.aprovado,
         he6.aprovado,
         he7.aprovado,
         he8.aprovado,
         he9.aprovado
ORDER BY order_componente,
         nm_disciplina;


ALTER TABLE relatorio.view_dados_historico_posicionamento OWNER TO ieducar;
            ");
    }

    public function down()
    {
        $this->execute("DROP VIEW relatorio.view_dados_historico_posicionamento");
    }
}
