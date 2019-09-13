CREATE OR REPLACE FUNCTION modules.copia_notas_transf(old_matricula_id integer, new_matricula_id integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
              DECLARE
              cur_comp RECORD;
              cur_comp_media RECORD;
              cur_geral RECORD;
              cur_geral_media RECORD;
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

              old_nota_aluno_id integer;
              new_nota_aluno_id integer;
              old_ano_matricula integer;
              new_ano_matricula integer;
              begin

              old_nota_aluno_id := (select id from modules.nota_aluno where matricula_id = old_matricula_id);
              new_nota_aluno_id := (select id from modules.nota_aluno where matricula_id = new_matricula_id);

              old_nota_aluno_id := (select (case when count(1) >= 1 then 1 else 0 end) from modules.nota_componente_curricular where nota_aluno_id = old_nota_aluno_id);
              new_nota_aluno_id := (select (case when count(1) >= 1 then 1 else 0 end) from modules.nota_componente_curricular where nota_aluno_id = new_nota_aluno_id);

              old_ano_matricula := (SELECT ano FROM pmieducar.matricula WHERE cod_matricula = old_matricula_id);
              new_ano_matricula := (SELECT ano FROM pmieducar.matricula WHERE cod_matricula = new_matricula_id);

              IF (old_nota_aluno_id = 1 and new_nota_aluno_id = 0) THEN
                /* VERIFICA SE AS MATRICULAS FAZEM PARTE DO MESMO ANO LETIVO*/
                IF (old_ano_matricula = new_ano_matricula) THEN

                  IF (
                   (  CASE WHEN (select padrao_ano_escolar from pmieducar.curso
                      where cod_curso = (select ref_cod_curso from pmieducar.matricula
                      where cod_matricula = new_matricula_id)) = 1
                     THEN  (select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                      inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                                  where mat.cod_matricula = new_matricula_id)
                           ELSE (select count(ref_cod_modulo) from pmieducar.turma_modulo
                      where ref_cod_turma = (select ref_cod_turma from pmieducar.matricula_turma
                      where ref_cod_matricula = new_matricula_id))
                           END
                 ) = (CASE WHEN (select padrao_ano_escolar from pmieducar.curso
                      where cod_curso = (select ref_cod_curso from pmieducar.matricula
                      where cod_matricula = old_matricula_id)) = 1
                     THEN  (select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                            inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                                  where mat.cod_matricula = old_matricula_id)
                           ELSE  (select count(ref_cod_modulo) from pmieducar.turma_modulo
                      where ref_cod_turma = (select max(ref_cod_turma) from pmieducar.matricula_turma
                      where ref_cod_matricula = old_matricula_id))
                           END
                      )
                ) THEN

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

                        INSERT INTO modules.nota_aluno (matricula_id) VALUES (new_matricula_id);
                        v_nota_id := (SELECT max(id) FROM modules.nota_aluno WHERE matricula_id = new_matricula_id);

                        v_old_nota_id := (SELECT max(id) FROM modules.nota_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_comp IN (SELECT * FROM modules.nota_componente_curricular where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_componente_curricular (nota_aluno_id,componente_curricular_id,nota,nota_arredondada,etapa, nota_recuperacao, nota_original, nota_recuperacao_especifica)
                          VALUES(v_nota_id,cur_comp.componente_curricular_id,cur_comp.nota,cur_comp.nota_arredondada,cur_comp.etapa,cur_comp.nota_recuperacao,cur_comp.nota_original,cur_comp.nota_recuperacao_especifica);
                        END LOOP;

                        FOR cur_comp_media IN (SELECT * FROM modules.nota_componente_curricular_media where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_componente_curricular_media (nota_aluno_id,componente_curricular_id,media,media_arredondada,etapa, situacao)
                          VALUES(v_nota_id,cur_comp_media.componente_curricular_id,cur_comp_media.media,cur_comp_media.media_arredondada,cur_comp_media.etapa, cur_comp_media.situacao);
                        END LOOP;

                        FOR cur_geral IN (SELECT * FROM modules.nota_geral where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_geral (nota_aluno_id,nota,nota_arredondada,etapa)
                          VALUES(v_nota_id,cur_geral.nota,cur_geral.nota_arredondada,cur_geral.etapa);
                        END LOOP;

                        FOR cur_geral_media IN (SELECT * FROM modules.media_geral where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.media_geral (nota_aluno_id,media,media_arredondada,etapa)
                          VALUES(v_nota_id,cur_geral_media.media,cur_geral_media.media_arredondada,cur_geral_media.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_falta = 1) THEN

                          INSERT INTO modules.falta_aluno (matricula_id, tipo_falta) VALUES (new_matricula_id,1);
                          v_falta_id = (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = new_matricula_id);
                        v_old_falta_id := (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_falta_geral IN (SELECT * FROM modules.falta_geral where falta_aluno_id = v_old_falta_id) LOOP
                          INSERT INTO modules.falta_geral (falta_aluno_id,quantidade,etapa)
                          VALUES(v_falta_id,cur_falta_geral.quantidade, cur_falta_geral.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_falta = 2) THEN

                        INSERT INTO modules.falta_aluno (matricula_id, tipo_falta) VALUES (new_matricula_id,2);
                        v_falta_id = (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = new_matricula_id);
                        v_old_falta_id := (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_falta_comp IN (SELECT * FROM modules.falta_componente_curricular where falta_aluno_id = v_old_falta_id) LOOP
                          INSERT INTO modules.falta_componente_curricular (falta_aluno_id,componente_curricular_id,quantidade,etapa)
                          VALUES(v_falta_id,cur_falta_comp.componente_curricular_id,cur_falta_comp.quantidade, cur_falta_comp.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_parecer = 2) THEN

                        INSERT INTO modules.parecer_aluno (matricula_id, parecer_descritivo)VALUES (new_matricula_id,2);
                        v_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = new_matricula_id);
                        v_old_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_parecer_comp IN (SELECT * FROM modules.parecer_componente_curricular where parecer_aluno_id = v_old_parecer_id) LOOP
                          INSERT INTO modules.parecer_componente_curricular (parecer_aluno_id,componente_curricular_id,parecer,etapa)
                          VALUES(v_parecer_id,cur_parecer_comp.componente_curricular_id,cur_parecer_comp.parecer, cur_parecer_comp.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_parecer = 3) THEN

                        INSERT INTO modules.parecer_aluno (matricula_id, parecer_descritivo)VALUES (new_matricula_id,3);
                        v_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = new_matricula_id);
                        v_old_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_parecer_geral IN (SELECT * FROM modules.parecer_geral where parecer_aluno_id = v_old_parecer_id) LOOP
                          INSERT INTO modules.parecer_geral (parecer_aluno_id,parecer,etapa)
                          VALUES(v_parecer_id,cur_parecer_geral.parecer, cur_parecer_geral.etapa);
                        END LOOP;
                      END IF;

                      RETURN 'OK';

                    ELSE RETURN 'REGRA AVALIACAO DIFERENTE'; END IF;
                  ELSE RETURN 'ETAPA DIFERENTE'; END IF;
                ELSE RETURN 'MATRICULAS DE ANOS DIFERENTES';
                END IF;
              ELSE RETURN 'NAO EXISTE NOTAS';END IF;

              end;$$;
