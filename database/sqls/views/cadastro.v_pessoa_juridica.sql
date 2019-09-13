CREATE OR REPLACE VIEW cadastro.v_pessoa_juridica AS
 SELECT j.idpes,
    j.fantasia,
    j.cnpj,
    j.insc_estadual,
    j.capital_social,
    ( SELECT pessoa.nome
           FROM cadastro.pessoa
          WHERE (pessoa.idpes = j.idpes)) AS nome
   FROM cadastro.juridica j;
