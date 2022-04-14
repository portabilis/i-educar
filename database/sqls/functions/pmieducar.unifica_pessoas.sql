CREATE OR REPLACE FUNCTION pmieducar.unifica_pessoas(pessoaprincipal numeric, pessoas numeric[], usuario integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
        DECLARE
            pessoaPrincipal ALIAS FOR $1;
            pessoas ALIAS FOR $2;
            usuario ALIAS FOR $3;
        BEGIN

            SET session_replication_role = REPLICA;

            IF
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas)) > 0 AND
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal) = 0
                THEN
                    UPDATE pmieducar.aluno
                        SET ref_idpes = pessoaPrincipal
                    WHERE ref_idpes = ANY(pessoas)
                    AND ref_idpes <> pessoaPrincipal
                    AND cod_aluno = (SELECT MAX(cod_aluno) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas));

                    PERFORM pmieducar.unifica_alunos(
                        (SELECT cod_aluno FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal),
                        (SELECT ARRAY(SELECT cod_aluno::numeric FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas))),
                        1
                    );
            ELSEIF
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal) > 0 AND
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas)) > 0
                THEN
                    PERFORM pmieducar.unifica_alunos(
                        (SELECT cod_aluno FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal),
                        (SELECT ARRAY(SELECT cod_aluno::numeric FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas))),
                        1
                    );
	        END IF;

	        UPDATE cadastro.fisica
                SET idpes_pai = pessoaPrincipal
            WHERE idpes_pai = ANY(pessoas);

            UPDATE cadastro.fisica
                SET idpes_mae = pessoaPrincipal
            WHERE idpes_mae = ANY(pessoas);

	        UPDATE pmieducar.servidor_alocacao
                SET ref_cod_servidor = pessoaPrincipal
            WHERE ref_cod_servidor = ANY(pessoas);

	        UPDATE pmieducar.servidor_funcao
                SET ref_cod_servidor = pessoaPrincipal
            WHERE ref_cod_servidor = ANY(pessoas);

	        IF
	            (SELECT COUNT(1) FROM pmieducar.servidor WHERE cod_servidor = ANY(pessoas)) > 0 AND
	            (SELECT COUNT(1) FROM pmieducar.servidor WHERE cod_servidor = pessoaPrincipal) = 0
	            THEN
	                INSERT INTO pmieducar.servidor SELECT
	                pessoaPrincipal as cod_servidor,
        	            ref_cod_instituicao, ref_idesco, carga_horaria,
        	            data_cadastro, data_exclusao, ativo, ref_cod_subnivel,
        	            situacao_curso_superior_1, formacao_complementacao_pedagogica_1,
        	            codigo_curso_superior_1, ano_inicio_curso_superior_1,
        	            ano_conclusao_curso_superior_1, tipo_instituicao_curso_superior_1,
        	            instituicao_curso_superior_1, situacao_curso_superior_2,
        	            formacao_complementacao_pedagogica_2, codigo_curso_superior_2,
        	            ano_inicio_curso_superior_2, ano_conclusao_curso_superior_2,
        	            tipo_instituicao_curso_superior_2, instituicao_curso_superior_2,
        	            situacao_curso_superior_3, formacao_complementacao_pedagogica_3,
        	            codigo_curso_superior_3, ano_inicio_curso_superior_3,
        	            ano_conclusao_curso_superior_3, tipo_instituicao_curso_superior_3,
        	            instituicao_curso_superior_3, pos_especializacao,
        	            pos_mestrado, pos_doutorado, pos_nenhuma,
        	            curso_creche, curso_pre_escola, curso_anos_iniciais,
        	            curso_anos_finais, curso_ensino_medio, curso_eja, curso_educacao_especial,
        	            curso_educacao_indigena, curso_educacao_campo, curso_educacao_ambiental,
        	            curso_educacao_direitos_humanos, curso_genero_diversidade_sexual,
        	            curso_direito_crianca_adolescente, curso_relacoes_etnicorraciais,
        	            curso_outros, curso_nenhum, multi_seriado
    	        FROM pmieducar.servidor
    	        WHERE cod_servidor = ANY(pessoas)
    	        ORDER BY cod_servidor ASC
                LIMIT 1;
	        END IF;

	        DELETE FROM pmieducar.servidor WHERE cod_servidor = ANY(pessoas) AND cod_servidor <> pessoaPrincipal;
            DELETE FROM cadastro.documento WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.fisica WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.fone_pessoa WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.pessoa WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;

            SET session_replication_role = DEFAULT;

        END;$_$;
