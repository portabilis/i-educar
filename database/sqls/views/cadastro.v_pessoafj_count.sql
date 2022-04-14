CREATE OR REPLACE VIEW cadastro.v_pessoafj_count AS
 SELECT fisica.ref_cod_sistema,
    fisica.cpf AS id_federal
   FROM cadastro.fisica
UNION ALL
 SELECT NULL::integer AS ref_cod_sistema,
    juridica.cnpj AS id_federal
   FROM cadastro.juridica;
