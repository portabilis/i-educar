CREATE FUNCTION alimentos.fcn_calcular_qtde_percapita(character varying, integer, integer, integer, integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_cli ALIAS for $1;
  v_id_uni ALIAS for $2;
  v_id_pro ALIAS for $3;
  v_data_inicial ALIAS for $4;
  v_data_final ALIAS for $5;
  v_sql_qtde_produto text;
  v_reg_qtde_produto RECORD;
  v_qtde_produto_periodo numeric;
  v_existe_unidade integer;
BEGIN
  v_sql_qtde_produto := 'SELECT car.dt_cardapio
    ,cap.quantidade as qtde
    ,car.idcar as idcar
  FROM alimentos.cardapio car
      ,alimentos.cardapio_produto cap
  WHERE car.idcar = cap.idcar
  AND cap.idpro = ' || v_id_pro || '
  AND car.finalizado = ''S''
  AND car.idcli = ''' ||  v_id_cli || '''
  AND TO_NUMBER(TO_CHAR(car.dt_cardapio,''YYYYMMDD''),''99999999'') BETWEEN ' || v_data_inicial || ' AND  ' || v_data_final;
  v_qtde_produto_periodo := 0;
  v_existe_unidade := 0;
  FOR v_reg_qtde_produto IN EXECUTE v_sql_qtde_produto LOOP
    --
    -- Verifica se o cardápio atende a unidade em questão
    --
    SELECT INTO v_existe_unidade distinct uni.iduni
    FROM alimentos.cardapio car
      ,alimentos.cardapio_faixa_unidade caf
      ,alimentos.unidade_atendida uni
      ,alimentos.unidade_faixa_etaria ufa
    WHERE car.idcar = caf.idcar
    AND caf.idfeu = ufa.idfeu
    AND ufa.iduni = uni.iduni
    AND uni.iduni = v_id_uni
    AND car.idcar = v_reg_qtde_produto.idcar;
    IF v_existe_unidade > 0 THEN
      v_qtde_produto_periodo := v_qtde_produto_periodo  + v_reg_qtde_produto.qtde;
    END IF;
  END LOOP;
  RETURN v_qtde_produto_periodo;
END;$_$;
