CREATE OR REPLACE VIEW relatorio.view_situacao AS
SELECT
    matricula.cod_matricula,
    situacao_matricula.cod_situacao,
    matricula_turma.ref_cod_turma AS cod_turma,
    matricula_turma.sequencial,
    (
        CASE
            WHEN matricula_turma.remanejado THEN
                'Remanejado'::character varying
            WHEN matricula_turma.transferido THEN
                'Transferido'::character varying
            WHEN matricula_turma.reclassificado THEN
                'Reclassificado'::character varying
            WHEN matricula_turma.abandono THEN
                'Abandono'::character varying
            WHEN matricula.aprovado = 1 THEN
                'Aprovado'::character varying
            WHEN matricula.aprovado = 12 THEN
                'Ap. Depen.'::character varying
            WHEN matricula.aprovado = 13 THEN
                'Ap. Cons.'::character varying
            WHEN matricula.aprovado = 2 THEN
                'Reprovado'::character varying
            WHEN matricula.aprovado = 3 THEN
                'Cursando'::character varying
            WHEN matricula.aprovado = 4 THEN
                'Transferido'::character varying
            WHEN matricula.aprovado = 5 THEN
                'Reclassificado'::character varying
            WHEN matricula.aprovado = 6 THEN
                'Abandono'::character varying
            WHEN matricula.aprovado = 14 THEN
                'Rp. Faltas'::character varying
            WHEN matricula.aprovado = 15 THEN
                'Falecido'::character varying
            ELSE 'Recl'::character varying
        END
    ) AS texto_situacao,
    (
        CASE
            WHEN matricula_turma.remanejado THEN
                'Rem'::character varying
            WHEN matricula_turma.transferido THEN
                'Trs'::character varying
            WHEN matricula_turma.reclassificado THEN
                'Recl'::character varying
            WHEN matricula_turma.abandono THEN
                'Aba'::character varying
            WHEN matricula.aprovado = 1 THEN
                'Apr'::character varying
            WHEN matricula.aprovado = 12 THEN
                'ApDp'::character varying
            WHEN matricula.aprovado = 13 THEN
                'ApCo'::character varying
            WHEN matricula.aprovado = 2 THEN
                'Rep'::character varying
            WHEN matricula.aprovado = 3 THEN
                'Cur'::character varying
            WHEN matricula.aprovado = 4 THEN
                'Trs'::character varying
            WHEN matricula.aprovado = 5 THEN
                'Recl'::character varying
            WHEN matricula.aprovado = 6 THEN
                'Aba'::character varying
            WHEN matricula.aprovado = 14 THEN
                'RpFt'::character varying
            WHEN matricula.aprovado = 15 THEN
                'Fal'::character varying
            ELSE 'Recl'::character varying
        END
    ) AS texto_situacao_simplificado
FROM
    relatorio.situacao_matricula,
    pmieducar.matricula
JOIN pmieducar.escola
    ON escola.cod_escola = matricula.ref_ref_cod_escola
JOIN pmieducar.instituicao
    ON instituicao.cod_instituicao = escola.ref_cod_instituicao
LEFT JOIN pmieducar.matricula_turma
    ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
WHERE TRUE
    AND matricula.ativo = 1
    AND (
        CASE WHEN instituicao.data_base_remanejamento IS NULL THEN
            COALESCE(matricula_turma.remanejado, false) = false
        ELSE
            COALESCE(matricula_turma.remanejado, false) = false OR
            matricula_turma.data_exclusao::date > instituicao.data_base_remanejamento
        END
    )
    AND (
      matricula_turma.ativo = 1
      OR
      (
          (
            instituicao.data_base_remanejamento IS NOT NULL
            AND matricula_turma.data_exclusao::date > instituicao.data_base_remanejamento
            OR (
              matricula_turma.sequencial = (
                select max(sequencial)
                from pmieducar.matricula_turma mt
                where mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                and mt.ref_cod_turma = matricula_turma.ref_cod_turma
              )
            )
          )
          AND (
              matricula_turma.transferido
              OR matricula_turma.remanejado
              OR matricula_turma.reclassificado
              OR matricula_turma.abandono
              OR matricula_turma.falecido
          )
      )
    )
    AND
    (
        CASE
            WHEN situacao_matricula.cod_situacao = 10 THEN
                matricula.aprovado = ANY (ARRAY[1, 2, 3, 4, 5, 6, 12, 13, 14, 15])
            WHEN situacao_matricula.cod_situacao = 9 THEN
                (matricula.aprovado = ANY (ARRAY[1, 2, 3, 5, 12, 13, 14]))
                AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL)
                AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL)
                AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL)
                AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
                AND (NOT matricula_turma.falecido OR matricula_turma.falecido IS NULL)
            WHEN
                situacao_matricula.cod_situacao = 2 THEN
                (matricula.aprovado = ANY (ARRAY[2, 14]))
                AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL)
                AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL)
                AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL)
                AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
                AND (NOT matricula_turma.falecido OR matricula_turma.falecido IS NULL)
            WHEN
                situacao_matricula.cod_situacao = 1 THEN
                (matricula.aprovado = ANY (ARRAY[1, 12, 13]))
                AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL)
                AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL)
                AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL)
                AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
                AND (NOT matricula_turma.falecido OR matricula_turma.falecido IS NULL)
            WHEN situacao_matricula.cod_situacao = ANY (ARRAY[3, 12, 13]) THEN
                matricula.aprovado = situacao_matricula.cod_situacao
                AND (NOT matricula_turma.reclassificado OR matricula_turma.reclassificado IS NULL)
                AND (NOT matricula_turma.abandono OR matricula_turma.abandono IS NULL)
                AND (NOT matricula_turma.remanejado OR matricula_turma.remanejado IS NULL)
                AND (NOT matricula_turma.transferido OR matricula_turma.transferido IS NULL)
                AND (NOT matricula_turma.falecido OR matricula_turma.falecido IS NULL)
            ELSE
                matricula.aprovado = situacao_matricula.cod_situacao
        END
    );
