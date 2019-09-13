CREATE OR REPLACE FUNCTION pmieducar.unifica_alunos(alunoprincipal numeric, alunos numeric[], usuario integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
        DECLARE
            alunoPrincipal ALIAS FOR $1;
            alunos ALIAS FOR $2;
            usuario ALIAS FOR $3;
        BEGIN

            UPDATE pmieducar.historico_escolar
                SET ref_cod_aluno = alunoPrincipal, sequencial = he.seq+he.max_seq
            FROM
                (
                    SELECT ref_cod_aluno AS aluno, sequencial AS seq, COALESCE((
                        SELECT max(sequencial) FROM pmieducar.historico_escolar WHERE ref_cod_aluno = alunoPrincipal
                    ),0) AS max_seq
                    FROM pmieducar.historico_escolar
                    WHERE ref_cod_aluno = ANY(alunos)
                ) AS he
              WHERE sequencial = he.seq
              AND ref_cod_aluno = he.aluno;

            UPDATE pmieducar.matricula
                SET ref_cod_aluno = alunoPrincipal
            WHERE ref_cod_aluno = ANY(alunos);

            UPDATE pmieducar.aluno
                SET ativo = 0, data_exclusao = now(), ref_usuario_exc = usuario
            WHERE cod_aluno = ANY(alunos);

        END;$_$;
