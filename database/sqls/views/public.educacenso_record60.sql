CREATE VIEW public.educacenso_record60 AS
SELECT
    '60' AS registro,
    educacenso_cod_escola.cod_escola_inep "inepEscola",
    aluno.ref_idpes "codigoPessoa",
    educacenso_cod_aluno.cod_aluno_inep "inepAluno",
    turma.cod_turma "codigoTurma",
    null "inepTurma",
    null "matriculaAluno",
    matricula_turma.etapa_educacenso "etapaAluno",
    COALESCE((ARRAY [1] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoDesenvolvimentoFuncoesGognitivas",
    COALESCE((ARRAY [2] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoDesenvolvimentoVidaAutonoma",
    COALESCE((ARRAY [3] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnriquecimentoCurricular",
    COALESCE((ARRAY [4] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoInformaticaAcessivel",
    COALESCE((ARRAY [5] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoLibras",
    COALESCE((ARRAY [6] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoLinguaPortuguesa",
    COALESCE((ARRAY [7] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoSoroban",
    COALESCE((ARRAY [8] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoBraile",
    COALESCE((ARRAY [9] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoOrientacaoMobilidade",
    COALESCE((ARRAY [10] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoCaa",
    COALESCE((ARRAY [11] <@ matricula_turma.tipo_atendimento)::INT, 0) "tipoAtendimentoEnsinoRecursosOpticosNaoOpticos",
    aluno.recebe_escolarizacao_em_outro_espaco AS "recebeEscolarizacaoOutroEspacao",
    (CASE
         WHEN transporte_aluno.responsavel > 0 THEN 1
         ELSE transporte_aluno.responsavel END) AS "transportePublico",
    transporte_aluno.responsavel AS "poderPublicoResponsavelTransporte",
    (ARRAY [4] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteBicicleta",
    (ARRAY [2] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteMicroonibus",
    (ARRAY [3] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteOnibus",
    (ARRAY [5] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteTracaoAnimal",
    (ARRAY [1] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteVanKonbi",
    (ARRAY [6] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteOutro",
    (ARRAY [7] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteAquaviarioCapacidade5",
    (ARRAY [8] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteAquaviarioCapacidade5a15",
    (ARRAY [9] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteAquaviarioCapacidade15a35",
    (ARRAY [10] <@ aluno.veiculo_transporte_escolar)::INT "veiculoTransporteAquaviarioCapacidadeAcima35",
    relatorio.get_nome_escola(escola.cod_escola) "nomeEscola",
    cadastro.pessoa.nome "nomeAluno",
    aluno.cod_aluno "codigoAluno",
    turma.tipo_atendimento "tipoAtendimentoTurma",
    turma.etapa_educacenso "etapaTurma",
    matricula.cod_matricula "codigoMatricula",
    turma.nm_turma "nomeTurma",
    matricula_turma.tipo_atendimento "tipoAtendimentoMatricula",
    turma.tipo_mediacao_didatico_pedagogico "tipoMediacaoTurma",
    aluno.veiculo_transporte_escolar "veiculoTransporteEscolar",
    curso.modalidade_curso as "modalidadeCurso",
    turma.local_funcionamento_diferenciado AS "localFuncionamentoDiferenciadoTurma",
    fisica.pais_residencia AS "paisResidenciaAluno",
    matricula.ano AS "anoTurma",
    escola.cod_escola AS "codEscola"
FROM pmieducar.aluno
JOIN pmieducar.matricula ON matricula.ref_cod_aluno = aluno.cod_aluno
JOIN pmieducar.escola ON escola.cod_escola = matricula.ref_ref_cod_escola
JOIN pmieducar.matricula_turma ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
JOIN pmieducar.instituicao ON instituicao.cod_instituicao = escola.ref_cod_instituicao
JOIN pmieducar.turma ON turma.cod_turma = matricula_turma.ref_cod_turma
JOIN pmieducar.curso ON curso.cod_curso = turma.ref_cod_curso
JOIN cadastro.pessoa ON pessoa.idpes = aluno.ref_idpes
JOIN cadastro.fisica ON fisica.idpes = pessoa.idpes
LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
LEFT JOIN modules.educacenso_cod_turma ON educacenso_cod_turma.cod_turma = turma.cod_turma
LEFT JOIN modules.educacenso_cod_aluno ON educacenso_cod_aluno.cod_aluno = aluno.cod_aluno
LEFT JOIN modules.transporte_aluno ON transporte_aluno.aluno_id = aluno.cod_aluno
WHERE true
  AND matricula.ativo = 1
  AND turma.ativo = 1
  AND COALESCE(turma.nao_informar_educacenso, 0) = 0
  AND (
        (
                matricula_turma.data_enturmacao < instituicao.data_educacenso
                AND coalesce(matricula_turma.data_exclusao, '2999-01-01'::date) >= instituicao.data_educacenso
            )
        OR (
                matricula_turma.data_enturmacao = instituicao.data_educacenso AND
                (
                    NOT EXISTS(
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
    )
