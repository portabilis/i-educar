CREATE OR REPLACE FUNCTION relatorio.get_situacao_historico(situacao integer) RETURNS character varying
    LANGUAGE sql
AS $$
SELECT CASE
           WHEN situacao = 1 THEN 'Aprovado'::character varying
           WHEN situacao = 2 THEN 'Reprovado'::character varying
           WHEN situacao = 3 THEN 'Cursando'::character varying
           WHEN situacao = 4 THEN 'Transferido'::character varying
           WHEN situacao = 5 THEN 'Reclassificado'::character varying
           WHEN situacao = 6 THEN 'Abandono'::character varying
           WHEN situacao = 12 THEN 'Ap. Depen.'::character varying
           WHEN situacao = 13 THEN 'Aprovado conselho'::character varying
           WHEN situacao = 14 THEN 'Reprovado por faltas'::character varying
           ELSE ''::character varying
           END AS situacao; $$;
