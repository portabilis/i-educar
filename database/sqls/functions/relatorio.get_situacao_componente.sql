CREATE OR REPLACE FUNCTION relatorio.get_situacao_componente(cod_situacao numeric) RETURNS character varying
    LANGUAGE plpgsql
AS $$
DECLARE
    texto_situacao varchar := '';
BEGIN
    texto_situacao := (CASE
                           WHEN cod_situacao = 1 THEN 'Aprovado'
                           WHEN cod_situacao = 2 THEN 'Retido'
                           WHEN cod_situacao = 3 THEN 'Cursando'
                           WHEN cod_situacao = 4 THEN 'Transferido'
                           WHEN cod_situacao = 5 THEN 'Reclassificado'
                           WHEN cod_situacao = 6 THEN 'Abandono'
                           WHEN cod_situacao = 7 THEN 'Em exame'
                           WHEN cod_situacao = 8 THEN 'Aprovado após exame'
                           WHEN cod_situacao = 9 THEN 'Retido por falta'
                           WHEN cod_situacao = 10 THEN 'Aprovado sem exame'
                           WHEN cod_situacao = 11 THEN 'Pré-matrícula'
                           WHEN cod_situacao = 12 THEN 'Aprovado com dependência'
                           WHEN cod_situacao = 13 THEN 'Aprovado pelo conselho'
                           ELSE '' END);
    RETURN texto_situacao;
END;
$$;
