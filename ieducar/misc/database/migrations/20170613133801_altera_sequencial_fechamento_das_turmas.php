<?php

use Phinx\Migration\AbstractMigration;

class AlteraSequencialFechamentoDasTurmas extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.matricula_turma
                           SET sequencial_fechamento = new.valor_linha
                          FROM
                               (SELECT row_number() over (partition BY ref_cod_turma
                                 ORDER BY (CASE
                                               WHEN data_base_remanejamento IS NULL
                                                  THEN 0
                                               WHEN data_enturmacao <= data_base_remanejamento
                                                  THEN 1
                                               ELSE 2
                                           END),
                                           split_part(nome,' ',1),
                                           split_part(nome,' ',2),
                                           split_part(nome,' ',3)) AS valor_linha,
        mt.sequencial,
        mt.ref_cod_matricula,
        mt.ref_cod_turma,
        mt.sequencial_fechamento
   FROM pmieducar.matricula_turma mt,
        pmieducar.turma t,
        pmieducar.instituicao i,
        pmieducar.matricula m,
        pmieducar.aluno a,
        cadastro.pessoa p
WHERE mt.ref_cod_turma IN
     (SELECT cod_turma
        FROM pmieducar.turma
       WHERE
     (SELECT count(1)
        FROM pmieducar.matricula_turma
       WHERE ref_cod_turma = cod_turma) =
     (SELECT count(1)
        FROM pmieducar.matricula_turma
       WHERE ref_cod_turma = cod_turma
         AND sequencial_fechamento = 0)
         AND ativo =1)
  AND t.cod_turma = mt.ref_cod_turma
  AND i.cod_instituicao = t.ref_cod_instituicao
  AND m.cod_matricula = mt.ref_cod_matricula
  AND a.cod_aluno = m.ref_cod_aluno
  AND p.idpes = a.ref_idpes) AS NEW
WHERE matricula_turma.sequencial = NEW.sequencial
  AND matricula_turma.ref_cod_matricula = NEW.ref_cod_matricula
  AND matricula_turma.ref_cod_turma = NEW.ref_cod_turma
  AND matricula_turma.ref_cod_turma IN
 (SELECT cod_turma
    FROM pmieducar.turma
   WHERE
 (SELECT count(1)
    FROM pmieducar.matricula_turma
   WHERE ref_cod_turma = cod_turma) =
 (SELECT count(1)
    FROM pmieducar.matricula_turma
   WHERE ref_cod_turma = cod_turma
     AND sequencial_fechamento = 0)
     AND ativo =1);
                    ");
    }
}
