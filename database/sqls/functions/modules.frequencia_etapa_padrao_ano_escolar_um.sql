CREATE OR REPLACE FUNCTION modules.frequencia_etapa_padrao_ano_escolar_um(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
                      		DECLARE
                      			dias_letivos_escola decimal;
                      			tipo_falta_aluno integer;
                      			faltas_aluno_geral decimal;
                      			faltas_aluno_componente decimal;
                      			cod_serie_ano_escolar integer;
                      		begin
                      			cod_serie_ano_escolar := (select ref_ref_cod_serie from pmieducar.matricula where matricula.cod_matricula = cod_matricula_aluno);
                      			tipo_falta_aluno := (select tipo_falta from modules.falta_aluno where matricula_id = cod_matricula_aluno);

                      			faltas_aluno_geral := (select quantidade
                      						 from modules.falta_aluno
                      					   inner join modules.falta_geral on (falta_aluno.id = falta_geral.falta_aluno_id)
                      					        where falta_aluno.matricula_id = cod_matricula_aluno
                      						  and etapa = cod_etapa::varchar);

                      			dias_letivos_escola := (select dias_letivos
                      						  from pmieducar.matricula
                      					    inner join pmieducar.ano_letivo_modulo on (matricula.ref_ref_cod_escola = ano_letivo_modulo.ref_ref_cod_escola
                      										       and matricula.ano = ano_letivo_modulo.ref_ano)
                      						 where cod_matricula = cod_matricula_aluno
                      						   and sequencial = cod_etapa);

                      			faltas_aluno_componente := (select quantidade
                      						      from modules.falta_aluno
                      					        inner join modules.falta_componente_curricular on (falta_aluno.id = falta_componente_curricular.falta_aluno_id)
                      						     where falta_aluno.matricula_id = cod_matricula_aluno
                      						       and etapa = cod_etapa::varchar
                      						       and componente_curricular_id = id_componente_curricular);

                      			if(dias_letivos_escola is not null and dias_letivos_escola <> 0) then

                      				if(tipo_falta_aluno = 1) then
                      					return round((((dias_letivos_escola - faltas_aluno_geral) * 100) / dias_letivos_escola), 2);
                      				else
                      					return round((((dias_letivos_escola - faltas_aluno_componente) * 100) / dias_letivos_escola), 2);
                      				end if;
                      			else
                      				return null;
                      			end if;
                      		end;
                      	$$;
