CREATE OR REPLACE FUNCTION modules.frequencia_etapa_padrao_ano_escolar_um(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer)
  RETURNS numeric AS
$BODY$
		DECLARE
			dias_letivos_escola decimal;
			tipo_falta_aluno integer;
			faltas_aluno_geral decimal;
			faltas_aluno_componente decimal;
			cod_serie_ano_escolar integer;
			valor_hora_falta decimal;
			carga_horaria_componente decimal;
		begin 
			cod_serie_ano_escolar := (select ref_ref_cod_serie from pmieducar.matricula where matricula.cod_matricula = cod_matricula_aluno);
			tipo_falta_aluno := (select tipo_falta from modules.falta_aluno where matricula_id = cod_matricula_aluno);
			
			faltas_aluno_geral := (select quantidade
						 from modules.falta_aluno
					   inner join modules.falta_geral on (falta_aluno.id = falta_geral.falta_aluno_id)
					        where falta_aluno.matricula_id = cod_matricula_aluno
						  and etapa = cod_etapa);

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
						       and etapa = cod_etapa
						       and componente_curricular_id = id_componente_curricular);

			valor_hora_falta := (select hora_falta
					       from pmieducar.matricula
					 inner join pmieducar.curso on (matricula.ref_cod_curso = curso.cod_curso)
					      where matricula.cod_matricula = cod_matricula_aluno);

			carga_horaria_componente := (select carga_horaria
						       from modules.componente_curricular_ano_escolar
						      where componente_curricular_id = id_componente_curricular
							and ano_escolar_id = cod_serie_ano_escolar);
			
			if(dias_letivos_escola is not null or dias_letivos_escola <> 0) then

				if(tipo_falta_aluno = 1) then
					return round((((dias_letivos_escola - faltas_aluno_geral) * 100) / dias_letivos_escola), 2);
				else
					return round((100 - ((faltas_aluno_componente * (100 * valor_hora_falta)) / carga_horaria_componente)), 2);
				end if;
			else
				return null;
			end if;
			
		end;
	$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION modules.frequencia_etapa_padrao_ano_escolar_um(integer, integer, integer)
  OWNER TO ieducar;