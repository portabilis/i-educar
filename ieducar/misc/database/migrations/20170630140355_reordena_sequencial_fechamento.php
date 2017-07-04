<?php

use Phinx\Migration\AbstractMigration;

class ReordenaSequencialFechamento extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmieducar.matricula_turma
                        SET sequencial_fechamento = tabela_reordenada.novo_sequencial
                        FROM (
                        SELECT nome,
                            ROW_NUMBER () OVER (PARTITION BY ref_cod_turma ORDER BY (CASE WHEN data_base_remanejamento IS NULL THEN 0
                                WHEN data_enturmacao > data_base_remanejamento THEN 1
	                            WHEN matricula.dependencia THEN 2
                                ELSE 0 END), nome) novo_sequencial,
                            matricula_turma.sequencial_fechamento,
                            data_base_remanejamento,
                            data_enturmacao,
                            sequencial,
                            ref_cod_matricula,
                            ref_cod_turma
                        FROM cadastro.pessoa
                        INNER JOIN pmieducar.aluno ON (aluno.ref_idpes = pessoa.idpes)
                        INNER JOIN pmieducar.matricula ON (matricula.ref_cod_aluno = aluno.cod_aluno)
                        INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
                        INNER JOIN pmieducar.escola ON (escola.cod_escola = matricula.ref_ref_cod_escola)
                        INNER JOIN pmieducar.instituicao ON (instituicao.cod_instituicao = escola.ref_cod_instituicao)
                        WHERE matricula.ativo = 1
			AND matricula.ano = 2017
			AND (CASE WHEN matricula_turma.ativo = 1 THEN TRUE
				  WHEN matricula_turma.transferido THEN TRUE
				  WHEN matricula_turma.remanejado THEN TRUE
				  WHEN matricula.dependencia THEN TRUE
				  ELSE FALSE END)
                        AND matricula_turma.sequencial = (SELECT MAX(sequencial)
                                            FROM pmieducar.matricula_turma mt
                                            WHERE mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                                            AND mt.ref_cod_turma = matricula_turma.ref_cod_turma)
                        ORDER BY novo_sequencial, nome ) AS tabela_reordenada

                        WHERE matricula_turma.sequencial = tabela_reordenada.sequencial
                                                AND matricula_turma.ref_cod_matricula = tabela_reordenada.ref_cod_matricula
                                                AND matricula_turma.ref_cod_turma = tabela_reordenada.ref_cod_turma
                                                AND matricula_turma.ref_cod_matricula IN (SELECT cod_matricula
                                                        FROM pmieducar.matricula
                                                        WHERE matricula.ativo = 1
                                                        AND matricula.ano = 2017
                                                        AND (CASE WHEN matricula_turma.ativo = 1 THEN TRUE
								  WHEN matricula_turma.transferido THEN TRUE
								  WHEN matricula_turma.remanejado THEN TRUE
								  WHEN matricula.dependencia THEN TRUE
								  ELSE FALSE END)
                                                        AND matricula_turma.sequencial = (SELECT MAX(sequencial)
                                                                            FROM pmieducar.matricula_turma mt
                                                                            WHERE mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                                                                            AND mt.ref_cod_turma = matricula_turma.ref_cod_turma));");
    }
}
