<?php

use Phinx\Migration\AbstractMigration;

class AlteraFunctionMediaAnualRondon extends AbstractMigration
{
    public function up()
    {
        $this->query("CREATE OR REPLACE FUNCTION relatorio.media_anual_rondon(matricula_id integer, componente_id integer)
                        RETURNS NUMERIC AS $$
                        DECLARE
                          nota1 numeric;
                          nota2 numeric;
                          nota3 numeric;
                          media_componente numeric;
                          existe_dispensa boolean;
                        BEGIN

                          nota1 :=
                          (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0) THEN nota ELSE nota_recuperacao::numeric END)::numeric, 0)
                           FROM modules.nota_componente_curricular ncc
                           INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                                AND na.matricula_id = matricula_id)
                           WHERE ncc.etapa = 1
                             AND ncc.componente_curricular_id = componente_id);

                          nota2 :=
                          (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0) THEN nota ELSE nota_recuperacao::numeric END)::numeric, 0)
                           FROM modules.nota_componente_curricular ncc
                           INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                                AND na.matricula_id = matricula_id)
                           WHERE ncc.etapa = 2
                             AND ncc.componente_curricular_id = componente_id);

                          nota3 :=
                          (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0) THEN nota ELSE nota_recuperacao::numeric END)::numeric, 0)
                           FROM modules.nota_componente_curricular ncc
                           INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                                AND na.matricula_id = matricula_id)
                           WHERE ncc.etapa = 3
                             AND ncc.componente_curricular_id = componente_id);

                          media_componente :=
                          (SELECT media
                             FROM modules.nota_componente_curricular_media nccm
                            INNER JOIN modules.nota_aluno na ON (na.id = nccm.nota_aluno_id
                                                                AND na.matricula_id = matricula_id)
                            WHERE nccm.etapa = 3
                              AND nccm.componente_curricular_id = componente_id);

                          existe_dispensa := EXISTS
                          (SELECT 1
                             FROM pmieducar.dispensa_disciplina dd
                            WHERE dd.ref_cod_matricula = matricula_id
                              AND dd.ref_cod_disciplina = componente_id);

                          IF existe_dispensa = TRUE THEN
                            RETURN TRUNC(media_componente,1);
                          END IF;

                          RETURN TRUNC((((nota1 * 3) + (nota2 * 3) + (nota3 * 4)) / 10),1);
                        END; $$ LANGUAGE plpgsql;");
    }
}
