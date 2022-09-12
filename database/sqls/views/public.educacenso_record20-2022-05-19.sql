CREATE VIEW public.educacenso_record20 AS
SELECT
    turma.cod_turma AS "codTurma",
    educacenso_cod_escola.cod_escola_inep AS "codigoEscolaInep",
    turma.ref_ref_cod_escola AS "codEscola",
    turma.ref_cod_curso AS "codCurso",
    turma.ref_ref_cod_serie AS "codSerie",
    turma.nm_turma AS "nomeTurma",
    turma.ano AS "anoTurma",
    turma.hora_inicial AS "horaInicial",
    turma.hora_final AS "horaFinal",
    turma.dias_semana AS "diasSemana",
    turma.tipo_atendimento AS "tipoAtendimento",
    turma.atividades_complementares AS "atividadesComplementares",
    turma.etapa_educacenso AS "etapaEducacenso",
    juridica.fantasia AS "nomeEscola",
    turma.tipo_mediacao_didatico_pedagogico AS "tipoMediacaoDidaticoPedagogico",
    turma.estrutura_curricular AS "estruturaCurricular",
    turma.formas_organizacao_turma AS "formasOrganizacaoTurma",
    turma.unidade_curricular AS "unidadesCurriculares",
    (
      SELECT
        array_agg(unidade_curricular) AS unidades_curriculares
      FROM (
        SELECT
          cod_turma,
          unnest(unidade_curricular) AS unidade_curricular
        FROM pmieducar.turma
      ) AS t
      WHERE true
      AND NOT EXISTS (
        SELECT 1
        FROM modules.professor_turma pt
        WHERE true
        AND pt.turma_id = t.cod_turma
        AND ARRAY[t.unidade_curricular] <@ pt.unidades_curriculares
      )
      AND t.cod_turma = turma.cod_turma
    ) "unidadesCurricularesSemDocenteVinculado",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
                 WHERE professor_turma.turma_id = turma.cod_turma
                 LIMIT 1), 0) as "possuiServidor",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
                 WHERE professor_turma.turma_id = turma.cod_turma
                   AND professor_turma.funcao_exercida IN (1, 5)
                 LIMIT 1), 0) as "possuiServidorDocente",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
                 WHERE professor_turma.turma_id = turma.cod_turma
                   AND professor_turma.funcao_exercida = 4
                 LIMIT 1), 0) as "possuiServidorLibras",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
                 WHERE professor_turma.turma_id = turma.cod_turma
                   AND professor_turma.funcao_exercida IN (4, 6)
                 LIMIT 1), 0) as "possuiServidorLibrasOuAuxiliarEad",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
                 WHERE professor_turma.turma_id = turma.cod_turma
                   AND professor_turma.funcao_exercida NOT IN (4, 6)
                 LIMIT 1), 0) as "possuiServidorDiferenteLibrasOuAuxiliarEad",

    COALESCE((
                 SELECT
                     1
                 FROM pmieducar.matricula_turma
                 JOIN pmieducar.matricula
                      ON matricula.cod_matricula = matricula_turma.ref_cod_matricula
                 JOIN pmieducar.aluno
                      ON aluno.cod_aluno = matricula.ref_cod_aluno
                 JOIN cadastro.fisica_deficiencia
                      ON fisica_deficiencia.ref_idpes = aluno.ref_idpes
                 JOIN cadastro.deficiencia
                      ON fisica_deficiencia.ref_cod_deficiencia = deficiencia.cod_deficiencia
                          AND deficiencia.deficiencia_educacenso IN (3, 4, 5)
                 WHERE matricula_turma.ref_cod_turma = turma.cod_turma
                   AND matricula_turma.data_enturmacao <= instituicao.data_educacenso
                   AND coalesce(matricula_turma.data_exclusao, '2999-01-01'::date) > instituicao.data_educacenso

                 LIMIT 1), 0) as "possuiAlunoNecessitandoTradutor",

    COALESCE((
                 SELECT
                     1
                 FROM modules.professor_turma
                 INNER JOIN pmieducar.servidor
                            ON servidor.cod_servidor = professor_turma.servidor_id
                 JOIN cadastro.fisica_deficiencia
                      ON fisica_deficiencia.ref_idpes = servidor.cod_servidor
                 JOIN cadastro.deficiencia
                      ON fisica_deficiencia.ref_cod_deficiencia = deficiencia.cod_deficiencia
                          AND deficiencia.deficiencia_educacenso IN (3, 4, 5)
                 WHERE professor_turma.turma_id = turma.cod_turma
                 LIMIT 1), 0) as "possuiServidorNecessitandoTradutor",
    (
      SELECT array_agg(DISTINCT codigo_educacenso)
        FROM pmieducar.turma t
        JOIN modules.professor_turma pt ON pt.turma_id = t.cod_turma
        JOIN modules.professor_turma_disciplina ptd ON ptd.professor_turma_id = pt.id
        JOIN modules.componente_curricular cc ON cc.id = ptd.componente_curricular_id
      WHERE t.cod_turma = turma.cod_turma
    ) AS "disciplinasEducacensoComDocentes",

    turma.local_funcionamento_diferenciado as "localFuncionamentoDiferenciado",
    escola.local_funcionamento as "localFuncionamento",
    curso.modalidade_curso as "modalidadeCurso",
    turma.cod_curso_profissional as "codCursoProfissional"

FROM pmieducar.escola
LEFT JOIN modules.educacenso_cod_escola ON (escola.cod_escola = educacenso_cod_escola.cod_escola)
JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola)
JOIN pmieducar.curso ON (turma.ref_cod_curso = curso.cod_curso)
JOIN pmieducar.instituicao ON (escola.ref_cod_instituicao = instituicao.cod_instituicao)
WHERE true
  AND COALESCE(turma.nao_informar_educacenso, 0) = 0
  AND turma.ativo = 1
  AND turma.visivel = TRUE
  AND escola.ativo = 1
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
