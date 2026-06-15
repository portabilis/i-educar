CREATE OR REPLACE VIEW relatorio.view_situacao AS
SELECT DISTINCT ON (vsr.cod_matricula, vsr.cod_turma, vsr.cod_situacao)
    vsr.cod_matricula,
    vsr.cod_situacao,
    vsr.cod_turma,
    vsr.sequencial,
    vsr.texto_situacao,
    vsr.texto_situacao_simplificado
FROM relatorio.view_situacao_relatorios vsr
ORDER BY
    vsr.cod_matricula,
    vsr.cod_turma,
    vsr.cod_situacao,
    vsr.sequencial DESC;
