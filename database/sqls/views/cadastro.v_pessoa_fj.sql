CREATE OR REPLACE VIEW cadastro.v_pessoa_fj AS
 SELECT p.idpes,
    p.nome,
    ( SELECT fisica.ref_cod_sistema
           FROM cadastro.fisica
          WHERE (fisica.idpes = p.idpes)) AS ref_cod_sistema,
    ( SELECT juridica.fantasia
           FROM cadastro.juridica
          WHERE (juridica.idpes = p.idpes)) AS fantasia,
    p.tipo,
    COALESCE(( SELECT fisica.cpf
           FROM cadastro.fisica
          WHERE (fisica.idpes = p.idpes)), ( SELECT juridica.cnpj
           FROM cadastro.juridica
          WHERE (juridica.idpes = p.idpes))) AS id_federal
   FROM cadastro.pessoa p;
