CREATE VIEW cadastro.v_pessoa_fisica_simples AS
 SELECT p.idpes,
    ( SELECT fisica_cpf.cpf
           FROM cadastro.fisica_cpf
          WHERE (fisica_cpf.idpes = p.idpes)) AS cpf,
    f.ref_cod_sistema,
    f.idesco
   FROM cadastro.pessoa p,
    cadastro.fisica f
  WHERE (p.idpes = f.idpes);
