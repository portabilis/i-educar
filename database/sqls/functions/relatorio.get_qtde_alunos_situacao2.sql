CREATE OR REPLACE FUNCTION relatorio.get_qtde_alunos_situacao(ano integer, instituicao integer, escola integer, curso integer, serie integer, turma integer, situacao integer, bairro integer, sexo character, idadeini integer, idadefim integer) RETURNS integer
    LANGUAGE plpgsql
AS $$

DECLARE

BEGIN

    RETURN (SELECT COUNT(*) AS qtde_situacao
            FROM pmieducar.instituicao
                     INNER JOIN pmieducar.escola            ON (escola.ref_cod_instituicao = instituicao.cod_instituicao)
                     INNER JOIN pmieducar.escola_ano_letivo ON (escola_ano_letivo.ref_cod_escola = escola.cod_escola)
                     INNER JOIN pmieducar.matricula         ON (matricula.ref_ref_cod_escola = escola.cod_escola)
                     INNER JOIN pmieducar.aluno             ON (matricula.ref_cod_aluno = aluno.cod_aluno)
                     INNER JOIN pmieducar.matricula_turma   ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
                     INNER JOIN pmieducar.turma             ON (turma.cod_turma = matricula_turma.ref_cod_turma)
                     INNER JOIN pmieducar.serie             ON (turma.ref_ref_cod_serie = serie.cod_serie)
                     INNER JOIN pmieducar.curso             ON (serie.ref_cod_curso = curso.cod_curso)
                     INNER JOIN cadastro.pessoa             ON (pessoa.idpes = aluno.ref_idpes)
                     INNER JOIN cadastro.fisica             ON (fisica.idpes = pessoa.idpes)
                     LEFT  JOIN cadastro.endereco_pessoa    ON (endereco_pessoa.idpes = pessoa.idpes)
                     LEFT  JOIN public.bairro               ON (endereco_pessoa.idbai = bairro.idbai)
                     LEFT  JOIN public.logradouro           ON (logradouro.idlog = endereco_pessoa.idlog)
                     LEFT  JOIN cadastro.fone_pessoa        ON (fone_pessoa.idpes = pessoa.idpes
                AND fone_pessoa.tipo =
                    (SELECT COALESCE(MIN(fone_pessoa_aux.tipo),1)
                     FROM cadastro.fone_pessoa AS fone_pessoa_aux
                     WHERE fone_pessoa_aux.fone <> 0
                       AND fone_pessoa_aux.idpes = pessoa.idpes))
                     LEFT  JOIN cadastro.documento        ON (documento.idpes = pessoa.idpes)
                     LEFT  JOIN cadastro.orgao_emissor_rg ON (orgao_emissor_rg.idorg_rg = documento.idorg_exp_rg)
                     INNER JOIN relatorio.view_situacao   ON (view_situacao.cod_matricula = matricula.cod_matricula
                AND view_situacao.cod_turma = turma.cod_turma
                AND view_situacao.cod_situacao = situacao
                AND matricula_turma.sequencial = view_situacao.sequencial)
                     LEFT JOIN cadastro.pessoa pessoa_mae             ON (pessoa_mae.idpes = fisica.idpes_mae)
                     LEFT JOIN cadastro.juridica                      ON (juridica.idpes = escola.ref_idpes)
                     LEFT JOIN endereco_pessoa endereco_pessoa_escola ON (endereco_pessoa_escola.idpes = escola.ref_idpes)
                     LEFT JOIN public.bairro bairro_escola            ON (endereco_pessoa.idbai = bairro_escola.idbai)
                     LEFT JOIN public.logradouro logradouro_escola    ON (logradouro_escola.idlog = endereco_pessoa.idlog)
                     LEFT JOIN public.municipio                       ON (municipio.idmun = bairro_escola.idmun)
                     LEFT JOIN cadastro.pessoa pessoa_escola          ON (pessoa_escola.idpes = escola.ref_idpes)
            WHERE aluno.ativo = 1       			 AND
                    matricula.ativo = 1   			 AND
                    turma.ativo = 1       			 AND
                    serie.ativo = 1       			 AND
                    curso.ativo = 1       			 AND
                    instituicao.ativo = 1                     AND
                    escola.ativo = 1                          AND
                    matricula.ano = escola_ano_letivo.ano     AND
                    instituicao.cod_instituicao = instituicao AND
                    escola.cod_escola = escola 		 AND
                    turma.ano = ano 				 AND
                (SELECT CASE WHEN curso = 0 THEN TRUE ELSE curso.cod_curso = curso END)
              AND (SELECT CASE WHEN serie = 0 THEN TRUE ELSE serie.cod_serie = serie END)
              AND (SELECT CASE WHEN turma = 0 THEN TRUE ELSE turma.cod_turma = turma END)
              AND (SELECT CASE WHEN bairro = 0 THEN TRUE ELSE bairro.idbai = bairro END)
              AND (SELECT CASE WHEN sexo = 'A' THEN TRUE ELSE (CASE WHEN sexo = 'M' THEN fisica.sexo = 'M' ELSE fisica.sexo = 'F' END) END)
              AND (((idadeIni > 0
                AND
                     (SELECT substring(age(CURRENT_DATE, fisica.data_nasc),1,2)
                      FROM cadastro.pessoa,
                           cadastro.fisica
                      WHERE aluno.ref_idpes = fisica.idpes
                        AND fisica.idpes = pessoa.idpes)::integer >= idadeIni)
                AND (idadeFim > 0
                    AND
                     (SELECT substring(age(CURRENT_DATE, fisica.data_nasc),1,2)
                      FROM cadastro.pessoa,
                           cadastro.fisica
                      WHERE aluno.ref_idpes = fisica.idpes
                        AND fisica.idpes = pessoa.idpes)::integer <= idadeFim))
                OR (idadeIni = 0)
                       AND (idadeFim = 0)));

END;

$$;
