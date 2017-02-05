<?php

use Phinx\Migration\AbstractMigration;

class AlteraFuncaoCopiaNotasTransf extends AbstractMigration
{
    public function up()
    {
        $this->execute("CREATE OR REPLACE FUNCTION modules.copia_notas_transf(old_matricula_id integer, new_matricula_id integer)
                          RETURNS void AS
                        $$
                          DECLARE
                          cur_comp RECORD;
                          cur_comp_media RECORD;
                          cur_falta_geral RECORD;
                          cur_falta_comp RECORD;
                          cur_parecer_geral RECORD;
                          cur_parecer_comp RECORD;
                          v_tipo_nota integer;
                          v_tipo_parecer integer;
                          v_tipo_falta integer;
                          v_nota_id integer;
                          v_old_nota_id integer;
                          v_falta_id integer;
                          v_old_falta_id integer;
                          v_parecer_id integer;
                          v_old_parecer_id integer;
                          begin

                          /* VERIFICA SE AS MATRICULAS FAZEM PARTE DO MESMO ANO LETIVO*/
                          IF ((SELECT eal.ano FROM pmieducar.escola_ano_letivo eal
                                INNER JOIN pmieducar.matricula mat ON (mat.ref_ref_cod_escola = eal.ref_cod_escola)
                                 WHERE mat.cod_matricula = old_matricula_id and eal.andamento = 1 limit 1) = (SELECT eal.ano FROM pmieducar.escola_ano_letivo eal
                                                                INNER JOIN pmieducar.matricula mat ON (mat.ref_ref_cod_escola = eal.ref_cod_escola)
                                                                 WHERE mat.cod_matricula = new_matricula_id and eal.andamento = 1 limit 1) ) THEN

                            /* VERIFICA SE POSSUEM MESMA QUANTIDADE DE ETAPAS*/
                            IF ((select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                            inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                            where mat.cod_matricula = new_matricula_id) = (select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                            inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                            where mat.cod_matricula = old_matricula_id)) THEN
                              /* VERIFICA SE UTILIZAM A MESMA REGRA DE AVALIAÇÃO*/
                              IF ((SELECT id FROM modules.regra_avaliacao rg
                                  INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                  INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                  where m.cod_matricula = old_matricula_id ) =
                                    (SELECT id FROM modules.regra_avaliacao rg
                                      INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                      INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                      where m.cod_matricula = new_matricula_id ) ) THEN


                                v_tipo_nota := (SELECT tipo_nota FROM modules.regra_avaliacao rg
                                          INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                          INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                          where m.cod_matricula = old_matricula_id);

                                v_tipo_falta := (SELECT tipo_presenca FROM modules.regra_avaliacao rg
                                          INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                          INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                          where m.cod_matricula = old_matricula_id);

                                v_tipo_parecer := (SELECT parecer_descritivo FROM modules.regra_avaliacao rg
                                          INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                          INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                          where m.cod_matricula = old_matricula_id);
                                /* SE A REGRA UTILIZAR NOTA, COPIA AS NOTAS*/
                                IF (v_tipo_nota >0) THEN

                                  v_nota_id := (SELECT max(id)+1 as id FROM modules.nota_aluno);

                                  IF (v_nota_id is null) THEN
                                    v_nota_id := 1;
                                  END IF;

                                  INSERT INTO modules.nota_aluno VALUES (v_nota_id, new_matricula_id);
                                  v_old_nota_id := (SELECT id FROM modules.nota_aluno WHERE matricula_id = old_matricula_id);

                                  FOR cur_comp IN (SELECT * FROM modules.nota_componente_curricular where nota_aluno_id = v_old_nota_id) LOOP
                                    INSERT INTO modules.nota_componente_curricular (nota_aluno_id,componente_curricular_id,nota,nota_arredondada,etapa)
                                    VALUES(v_nota_id,cur_comp.componente_curricular_id,cur_comp.nota,cur_comp.nota_arredondada,cur_comp.etapa);
                                  END LOOP;

                                  FOR cur_comp_media IN (SELECT * FROM modules.nota_componente_curricular_media where nota_aluno_id = v_old_nota_id) LOOP
                                    INSERT INTO modules.nota_componente_curricular_media (nota_aluno_id,componente_curricular_id,media,media_arredondada,etapa)
                                    VALUES(v_nota_id,cur_comp_media.componente_curricular_id,cur_comp_media.media,cur_comp_media.media_arredondada,cur_comp_media.etapa);
                                  END LOOP;
                                END IF;

                                IF (v_tipo_falta = 1) THEN
                                    v_falta_id := (SELECT max(id)+1 AS id FROM modules.falta_aluno);

                                  IF (v_falta_id is null) THEN
                                    v_falta_id := 1;
                                  END IF;

                                    INSERT INTO modules.falta_aluno VALUES (v_falta_id, new_matricula_id,1);
                                  v_old_falta_id := (SELECT id FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                                  FOR cur_falta_geral IN (SELECT * FROM modules.falta_geral where falta_aluno_id = v_old_falta_id) LOOP
                                    INSERT INTO modules.falta_geral (falta_aluno_id,quantidade,etapa)
                                    VALUES(v_falta_id,cur_falta_geral.quantidade, cur_falta_geral.etapa);
                                  END LOOP;
                                END IF;

                                IF (v_tipo_falta = 2) THEN
                                    v_falta_id := (SELECT max(id)+1 AS id FROM modules.falta_aluno);
                                    IF (v_falta_id is null) THEN
                                      v_falta_id := 1;
                                    END IF;
                                    INSERT INTO modules.falta_aluno VALUES (v_falta_id, new_matricula_id,2);
                                  v_old_falta_id := (SELECT id FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                                  FOR cur_falta_comp IN (SELECT * FROM modules.falta_componente_curricular where falta_aluno_id = v_old_falta_id) LOOP
                                    INSERT INTO modules.falta_componente_curricular (falta_aluno_id,componente_curricular_id,quantidade,etapa)
                                    VALUES(v_falta_id,cur_falta_comp.componente_curricular_id,cur_falta_comp.quantidade, cur_falta_comp.etapa);
                                  END LOOP;
                                END IF;

                                IF (v_tipo_parecer = 2) THEN
                                    v_parecer_id := (SELECT max(id)+1 AS id FROM modules.parecer_aluno);
                                    IF (v_parecer_id is null) THEN
                                      v_parecer_id := 1;
                                    END IF;
                                    INSERT INTO modules.parecer_aluno VALUES (v_parecer_id, new_matricula_id,2);
                                  v_old_parecer_id := (SELECT id FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                                  FOR cur_parecer_comp IN (SELECT * FROM modules.parecer_componente_curricular where parecer_aluno_id = v_old_parecer_id) LOOP
                                    INSERT INTO modules.parecer_componente_curricular (parecer_aluno_id,componente_curricular_id,parecer,etapa)
                                    VALUES(v_parecer_id,cur_parecer_comp.componente_curricular_id,cur_parecer_comp.parecer, cur_parecer_comp.etapa);
                                  END LOOP;
                                END IF;

                                IF (v_tipo_parecer = 3) THEN
                                    v_parecer_id := (SELECT max(id)+1 AS id FROM modules.parecer_aluno);
                                    IF (v_parecer_id is null) THEN
                                      v_parecer_id := 1;
                                    END IF;
                                    INSERT INTO modules.parecer_aluno VALUES (v_parecer_id, new_matricula_id,3);
                                  v_old_parecer_id := (SELECT id FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                                  FOR cur_parecer_geral IN (SELECT * FROM modules.parecer_geral where parecer_aluno_id = v_old_parecer_id) LOOP
                                    INSERT INTO modules.parecer_geral (parecer_aluno_id,parecer,etapa)
                                    VALUES(v_parecer_id,cur_parecer_geral.parecer, cur_parecer_geral.etapa);
                                  END LOOP;
                                END IF;

                              END IF;
                            END IF;

                          END IF;

                          end;$$
                          LANGUAGE 'plpgsql' VOLATILE;");
    }
}
