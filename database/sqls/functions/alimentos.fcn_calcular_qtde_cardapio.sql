CREATE FUNCTION alimentos.fcn_calcular_qtde_cardapio(character varying, integer, integer, numeric) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_cli ALIAS for $1;
  v_id_car ALIAS for $2;
  v_id_pro ALIAS for $3;
  v_qtde_per_capita ALIAS for $4;
  reg_unidade RECORD;
  v_inscritos char(1);
  v_correcao numeric:=0;
  v_coccao numeric:=0;
  v_qtde numeric:= 0;
  v_qtde_retorno varchar(20);
BEGIN
  SELECT INTO v_inscritos inscritos
  FROM alimentos.cliente
  WHERE alimentos.cliente.idcli = v_id_cli;
  SELECT INTO v_correcao,v_coccao fator_correcao,fator_coccao
  FROM alimentos.produto
  WHERE alimentos.produto.idpro = v_id_pro;
  FOR reg_unidade IN SELECT unf.num_inscritos as insc, unf.num_matriculados as matr
        FROM alimentos.cardapio_faixa_unidade car,
                     alimentos.unidade_faixa_etaria unf
        WHERE car.idfeu = unf.idfeu
        AND   car.idcar = v_id_car LOOP
    IF v_inscritos = 'S' THEN
      v_qtde := v_qtde + (v_qtde_per_capita * reg_unidade.insc * v_correcao * v_coccao);
    ELSE
      v_qtde := v_qtde + (v_qtde_per_capita * reg_unidade.matr * v_correcao * v_coccao);
    END IF;
  END LOOP;

  v_qtde_retorno := trim(to_char(v_qtde, '9999999999.99'));
  RETURN v_qtde_retorno;
END;$_$;
