CREATE VIEW public.educacenso_record50 AS
SELECT DISTINCT
    '50' AS registro,
    educacenso_cod_escola.cod_escola_inep AS "inepEscola",
    servidor.cod_servidor AS "codigoPessoa",
    educacenso_cod_docente.cod_docente_inep AS "inepDocente",
    turma.cod_turma AS "codigoTurma",
    null AS "inepTurma",
    professor_turma.funcao_exercida AS "funcaoDocente",
    professor_turma.tipo_vinculo AS "tipoVinculo",
    tbl_componentes.componentes AS componentes,
    relatorio.get_nome_escola(escola.cod_escola) AS "nomeEscola",
    pessoa.nome AS "nomeDocente",
    servidor.cod_servidor AS "idServidor",
    instituicao.cod_instituicao AS "idInstituicao",
    professor_turma.id AS "idAlocacao",
    turma.tipo_mediacao_didatico_pedagogico AS "tipoMediacaoTurma",
    turma.tipo_atendimento AS "tipoAtendimentoTurma",
    turma.nm_turma AS "nomeTurma",
    escola.dependencia_administrativa AS "dependenciaAdministrativaEscola",
    turma.etapa_educacenso AS "etapaEducacensoTurma",
    turma.ano AS "anoTurma",
    escola.cod_escola AS "codEscola"
FROM pmieducar.servidor
     JOIN modules.professor_turma ON professor_turma.servidor_id = servidor.cod_servidor
     JOIN pmieducar.turma ON turma.cod_turma = professor_turma.turma_id
        AND turma.ano = professor_turma.ano
     JOIN pmieducar.escola ON escola.cod_escola = turma.ref_ref_cod_escola
     JOIN pmieducar.instituicao ON escola.ref_cod_instituicao = instituicao.cod_instituicao
     JOIN cadastro.pessoa ON pessoa.idpes = servidor.cod_servidor
     LEFT JOIN pmieducar.servidor_alocacao ON servidor_alocacao.ref_cod_escola = escola.cod_escola
        AND servidor_alocacao.ref_cod_servidor = servidor.cod_servidor
        AND servidor_alocacao.ano = turma.ano
     LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
     LEFT JOIN modules.educacenso_cod_docente ON educacenso_cod_docente.cod_servidor = servidor.cod_servidor
     LEFT JOIN modules.educacenso_cod_turma ON educacenso_cod_turma.cod_turma = turma.cod_turma
     LEFT JOIN modules.professor_turma_disciplina ON professor_turma_disciplina.professor_turma_id = professor_turma.id,
     LATERAL (
         SELECT DISTINCT
             array_agg(DISTINCT cc.codigo_educacenso) AS componentes
         FROM modules.componente_curricular cc
         INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)
         WHERE ptd.professor_turma_id = professor_turma.id
         ) AS tbl_componentes
WHERE true
  AND turma.ativo = 1
  AND turma.visivel = true
  AND escola.ativo = 1
  AND COALESCE(turma.nao_informar_educacenso, 0) = 0
  AND servidor.ativo = 1
  AND coalesce(servidor_alocacao.data_admissao, '1900-01-01'::date) <= instituicao.data_educacenso
  AND coalesce(servidor_alocacao.data_saida, '2999-01-01'::date) >= instituicao.data_educacenso
  AND (
        exists(
            SELECT
                1
            FROM pmieducar.matricula_turma
            JOIN pmieducar.matricula
                 ON matricula.cod_matricula = matricula_turma.ref_cod_matricula
            WHERE matricula_turma.ref_cod_turma = turma.cod_turma
              AND matricula.ativo = 1
              AND matricula_turma.data_enturmacao < instituicao.data_educacenso
              AND coalesce(matricula_turma.data_exclusao, '2999-01-01'::date) >= instituicao.data_educacenso
            )
        OR
        exists(
            SELECT
                1
            FROM pmieducar.matricula_turma
            JOIN pmieducar.matricula
                 ON matricula.cod_matricula = matricula_turma.ref_cod_matricula
            WHERE matricula_turma.ref_cod_turma = turma.cod_turma
              AND matricula.ativo = 1
              AND matricula_turma.data_enturmacao = instituicao.data_educacenso
              AND coalesce(matricula_turma.data_exclusao, '2999-01-01'::date) >= instituicao.data_educacenso
              AND NOT EXISTS(
                SELECT
                    1
                FROM pmieducar.matricula_turma smt
                JOIN pmieducar.matricula sm
                     ON sm.cod_matricula = smt.ref_cod_matricula
                WHERE sm.ref_cod_aluno = matricula.ref_cod_aluno
                  AND sm.ativo = 1
                  AND sm.ano = matricula.ano
                  AND smt.data_enturmacao < matricula_turma.data_enturmacao
                  AND coalesce(smt.data_exclusao, '2999-01-01'::date) >= instituicao.data_educacenso
                )
            )
    )
