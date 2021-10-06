CREATE OR REPLACE VIEW relatorio.view_situacao AS
SELECT *
FROM relatorio.view_situacao_relatorios
WHERE view_situacao_relatorios.sequencial = (
    SELECT max(sequencial)
    FROM pmieducar.matricula_turma mt
    WHERE mt.ref_cod_turma = view_situacao_relatorios.cod_turma
      AND mt.ref_cod_matricula = view_situacao_relatorios.cod_matricula
);
