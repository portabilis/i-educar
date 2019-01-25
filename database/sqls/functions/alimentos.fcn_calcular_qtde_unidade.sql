CREATE FUNCTION alimentos.fcn_calcular_qtde_unidade(character varying, integer, integer, numeric, integer, integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_cli ALIAS for $1;
  v_id_uni ALIAS for $2;
  v_id_pro ALIAS for $3;
  v_qtde_per_capita ALIAS for $4;
  v_data_inicial ALIAS for $5;
  v_data_final ALIAS for $6;
  v_reg_faixa RECORD;
  v_inscritos char(1);
  v_correcao numeric:=0;
  v_coccao numeric:=0;
  v_qtde numeric:= 0;
  v_peso numeric:=0;
  v_qtde_produto_periodo numeric:=0;
  v_num_inscritos integer;
  v_num_matriculados integer;
BEGIN
  SELECT INTO v_inscritos inscritos
  FROM alimentos.cliente
  WHERE alimentos.cliente.idcli = v_id_cli;
  SELECT INTO v_correcao,v_coccao,v_peso pro.fator_correcao, pro.fator_coccao, unp.peso
  FROM alimentos.produto pro, alimentos.unidade_produto unp
  WHERE   pro.idunp = unp.idunp
  AND unp.idcli = v_id_cli
  AND     pro.idpro = v_id_pro;
  --
  -- Obtém inscritos e matriculados para a unidade
  --
  v_num_inscritos:=0;
  v_num_matriculados:=0;
  FOR v_reg_faixa IN SELECT distinct ufa.idfae as idfae
      ,ufa.num_inscritos as num_inscritos
      ,ufa.num_matriculados as num_matriculados
    FROM  alimentos.cardapio car
      ,alimentos.cardapio_faixa_unidade caf
      ,alimentos.unidade_atendida uni
      ,alimentos.unidade_faixa_etaria ufa
    WHERE car.idcar = caf.idcar
    AND   caf.idfeu = ufa.idfeu
    AND ufa.iduni = uni.iduni
    AND     uni.iduni = v_id_uni
    AND     car.finalizado = 'S'
    AND car.idcli = v_id_cli
    AND TO_NUMBER(TO_CHAR(car.dt_cardapio,'YYYYMMDD'),'99999999') BETWEEN v_data_inicial AND v_data_final LOOP
    v_num_inscritos:=v_num_inscritos + v_reg_faixa.num_inscritos;
    v_num_matriculados:=v_num_matriculados + v_reg_faixa.num_matriculados;
  END LOOP;
  --
  -- Calcula a qtde percapita necessária do produto para o período
  --
  select INTO v_qtde_produto_periodo
  alimentos.fcn_calcular_qtde_percapita (v_id_cli,v_id_uni,v_id_pro,v_data_inicial,v_data_final);
  IF v_inscritos = 'S' THEN
    v_qtde := v_qtde_produto_periodo * v_num_inscritos * v_correcao * v_coccao;
  ELSE
    v_qtde := v_qtde_produto_periodo * v_num_matriculados * v_correcao * v_coccao;
  END IF;
  RETURN round(v_qtde/v_peso);
END;$_$;
