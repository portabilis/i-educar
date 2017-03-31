<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionsCalculoDeMediaRondon extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP FUNCTION relatorio.media_final_rondon(integer, integer);
                      CREATE OR REPLACE FUNCTION relatorio.media_final_rondon( matricula_id_ integer, componente_id integer) RETURNS numeric AS $$
                      DECLARE
                        nota1 numeric;
                        nota2 numeric;
                        nota3 numeric;
                        existe_nota_exame boolean;
                        nota_exame numeric;
                      BEGIN

                      nota1 :=
                      (SELECT COALESCE( (CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric::numeric,0)
                                              THEN nota
                                              ELSE nota_recuperacao::numeric
                                          END)::numeric, 0)
                       FROM modules.nota_componente_curricular ncc
                       INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                            AND na.matricula_id = matricula_id_)
                       WHERE ncc.etapa = '1'
                         AND ncc.componente_curricular_id = componente_id ); nota2 :=
                      (SELECT COALESCE( (CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric::numeric,0)
                                              THEN nota
                                              ELSE nota_recuperacao::numeric
                                          END)::numeric, 0)
                       FROM modules.nota_componente_curricular ncc
                       INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                            AND na.matricula_id = matricula_id_)
                       WHERE ncc.etapa = '2'
                         AND ncc.componente_curricular_id = componente_id); nota3 :=
                      (SELECT COALESCE( (CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric::numeric,0)
                                              THEN nota
                                              ELSE nota_recuperacao::numeric
                                          END)::numeric, 0)
                       FROM modules.nota_componente_curricular ncc
                       INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                            AND na.matricula_id = matricula_id_)
                       WHERE ncc.etapa = '3'
                         AND ncc.componente_curricular_id = componente_id); nota_exame :=
                      (SELECT COALESCE( (CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric::numeric,0)
                                              THEN nota
                                              ELSE nota_recuperacao::numeric
                                          END)::numeric, 0)
                       FROM modules.nota_componente_curricular ncc
                       INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                            AND na.matricula_id = matricula_id_)
                       WHERE ncc.etapa = 'Rc'
                         AND ncc.componente_curricular_id = componente_id); existe_nota_exame := EXISTS
                      (SELECT 1
                       FROM modules.nota_componente_curricular ncc
                       INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                            AND na.matricula_id = matricula_id_)
                       WHERE ncc.etapa = 'Rc'
                         AND componente_curricular_id = componente_id); IF existe_nota_exame = TRUE THEN IF (nota3 <= nota2)
                      AND (nota3 <= nota1)
                      AND (nota3 <= nota_exame) THEN RETURN TRUNC((((nota1 * 3) + (nota2 * 3) + (nota_exame * 4))/10), 1); ELSIF (nota2 <= nota1)
                      AND (nota2 < nota3)
                      AND (nota2 <= nota_exame) THEN RETURN TRUNC((((nota1 * 3) + (nota_exame * 3) + (nota3 * 4))/10),1); ELSIF (nota1 < nota2)
                      AND (nota1 < nota3)
                      AND (nota1 <= nota_exame) THEN RETURN TRUNC((((nota_exame * 3) + (nota2 * 3) + (nota3 * 4))/10),1); END IF; END IF; RETURN relatorio.media_anual_rondon(matricula_id_, componente_id); END; $$ LANGUAGE plpgsql VOLATILE COST 100;

                      ALTER FUNCTION relatorio.media_final_rondon(integer, integer) OWNER TO ieducar;");

      $this->execute("DROP FUNCTION relatorio.media_anual_rondon(integer, integer);
                      CREATE OR REPLACE FUNCTION relatorio.media_anual_rondon( matricula_id_ integer, componente_id integer) RETURNS numeric AS $$
                      DECLARE
                        nota1 numeric;
                        nota2 numeric;
                        nota3 numeric;
                        media_componente numeric;
                        existe_dispensa boolean;
                      BEGIN

                        nota1 :=
                        (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                               THEN nota
                                               ELSE nota_recuperacao::numeric
                                           END)::numeric, 0)
                         FROM modules.nota_componente_curricular ncc
                         INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                              AND na.matricula_id = matricula_id_)
                         WHERE ncc.etapa = '1'
                           AND ncc.componente_curricular_id = componente_id);

                        nota2 :=
                        (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                               THEN nota
                                               ELSE nota_recuperacao::numeric
                                           END)::numeric, 0)
                         FROM modules.nota_componente_curricular ncc
                         INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                              AND na.matricula_id = matricula_id_)
                         WHERE ncc.etapa = '2'
                           AND ncc.componente_curricular_id = componente_id);

                        nota3 :=
                        (SELECT COALESCE((CASE WHEN COALESCE(nota, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                               THEN nota
                                               ELSE nota_recuperacao::numeric
                                           END)::numeric, 0)
                         FROM modules.nota_componente_curricular ncc
                         INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                              AND na.matricula_id = matricula_id_)
                         WHERE ncc.etapa = '3'
                           AND ncc.componente_curricular_id = componente_id);

                        media_componente :=
                        (SELECT media
                           FROM modules.nota_componente_curricular_media nccm
                          INNER JOIN modules.nota_aluno na ON (na.id = nccm.nota_aluno_id
                                                              AND na.matricula_id = matricula_id_)
                          WHERE nccm.etapa = '3'
                            AND nccm.componente_curricular_id = componente_id);

                        existe_dispensa := EXISTS
                        (SELECT 1
                           FROM pmieducar.dispensa_disciplina dd
                          WHERE dd.ref_cod_matricula = matricula_id_
                            AND dd.ref_cod_disciplina = componente_id);

                        IF existe_dispensa = TRUE THEN
                          RETURN TRUNC(media_componente,1);
                        END IF;

                        RETURN TRUNC((((nota1 * 3) + (nota2 * 3) + (nota3 * 4)) / 10),1);
                      END; $$ LANGUAGE plpgsql VOLATILE COST 100;

                      ALTER FUNCTION relatorio.media_anual_rondon(integer, integer) OWNER TO ieducar;");
    }
}
