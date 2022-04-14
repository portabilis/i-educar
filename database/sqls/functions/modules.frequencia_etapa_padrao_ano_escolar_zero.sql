CREATE OR REPLACE FUNCTION modules.frequencia_etapa_padrao_ano_escolar_zero(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
		DECLARE
			dias_letivos_turma decimal;
			tipo_falta_aluno integer;
			faltas_aluno_geral decimal;
			faltas_aluno_componente decimal;
			cod_serie_ano_escolar integer;
		begin
			cod_serie_ano_escolar := (select ref_ref_cod_serie from pmieducar.matricula where matricula.cod_matricula = cod_matricula_aluno);
			tipo_falta_aluno := (select tipo_falta from modules.falta_aluno where matricula_id = cod_matricula_aluno);

			dias_letivos_turma := (select turma_modulo.dias_letivos
						 from pmieducar.matricula
					   inner join pmieducar.matricula_turma on (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
					   inner join pmieducar.turma on (matricula_turma.ref_cod_turma = turma.cod_turma)
					    left join pmieducar.turma_modulo on (turma.cod_turma = turma_modulo.ref_cod_turma)
						where matricula.cod_matricula = cod_matricula_aluno
						  and turma_modulo.sequencial = cod_etapa
						  and matricula_turma.ativo = 1);

			faltas_aluno_geral := (select quantidade
						 from modules.falta_aluno
					   inner join modules.falta_geral on (falta_aluno.id = falta_geral.falta_aluno_id)
						where falta_aluno.matricula_id = cod_matricula_aluno
						  and etapa = cod_etapa);

			faltas_aluno_componente := (select quantidade
						      from modules.falta_aluno
						inner join modules.falta_componente_curricular on (falta_aluno.id = falta_componente_curricular.falta_aluno_id)
						     where falta_aluno.matricula_id = cod_matricula_aluno
						       and etapa = cod_etapa
						       and componente_curricular_id = id_componente_curricular);

			if(dias_letivos_turma is not null and dias_letivos_turma <> 0) then

				if(tipo_falta_aluno = 1) then
					return round((((dias_letivos_turma - faltas_aluno_geral) * 100) / dias_letivos_turma), 2);
				else
					return round((((dias_letivos_turma - faltas_aluno_componente) * 100) / dias_letivos_turma), 2);
				end if;
			else
				return null;
			end if;
		end;
	$$;
