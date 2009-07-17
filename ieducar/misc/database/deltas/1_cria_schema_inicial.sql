-- //

--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: acesso; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA acesso;


--
-- Name: alimentos; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA alimentos;


--
-- Name: cadastro; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA cadastro;


--
-- Name: consistenciacao; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA consistenciacao;


--
-- Name: historico; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA historico;


--
-- Name: pmiacoes; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA pmiacoes;


--
-- Name: pmicontrolesis; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA pmicontrolesis;


--
-- Name: pmidrh; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA pmidrh;


--
-- Name: pmieducar; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA pmieducar;


--
-- Name: pmiotopic; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA pmiotopic;


--
-- Name: portal; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA portal;


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- Name: urbano; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA urbano;


--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- Name: typ_idlog; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE typ_idlog AS (
	idlog integer
);


--
-- Name: typ_idpes; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE typ_idpes AS (
	idpes integer
);


SET search_path = alimentos, pg_catalog;

--
-- Name: fcn_calcular_qtde_cardapio(character varying, integer, integer, numeric); Type: FUNCTION; Schema: alimentos; Owner: -
--

CREATE FUNCTION fcn_calcular_qtde_cardapio(character varying, integer, integer, numeric) RETURNS character varying
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
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_calcular_qtde_percapita(character varying, integer, integer, integer, integer); Type: FUNCTION; Schema: alimentos; Owner: -
--

CREATE FUNCTION fcn_calcular_qtde_percapita(character varying, integer, integer, integer, integer) RETURNS numeric
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
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_calcular_qtde_unidade(character varying, integer, integer, numeric, integer, integer); Type: FUNCTION; Schema: alimentos; Owner: -
--

CREATE FUNCTION fcn_calcular_qtde_unidade(character varying, integer, integer, numeric, integer, integer) RETURNS numeric
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
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_gerar_guia_remessa(text, text, integer, integer, character varying, character varying, character varying, integer); Type: FUNCTION; Schema: alimentos; Owner: -
--

CREATE FUNCTION fcn_gerar_guia_remessa(text, text, integer, integer, character varying, character varying, character varying, integer) RETURNS text
    AS $_$DECLARE
   -- Parâmetro recebidos
   v_array_fornecedor ALIAS for $1;
   v_array_unidade ALIAS for $2;
   v_data_inicial ALIAS for $3;
   v_data_final ALIAS for $4;
   v_classe ALIAS for $5;
   v_id_cliente ALIAS for $6;
   v_login ALIAS for $7;
   v_id_log ALIAS for $8;   
   v_sql_unidade text;
   v_reg_unidade RECORD;
   v_reg_faixa RECORD;
   v_sql_produto text;
   v_reg_produto RECORD;
   v_sql_fornecedor text;
   v_reg_fornecedor RECORD;
   v_num_inscritos integer;
   v_num_matriculados integer;
   v_inscritos char(1);
   v_qtde_necessaria numeric;         -- quantidade total necessaria para o produto
   v_qtde_necessaria_saldo numeric;   -- saldo do produto a fornecer
   v_qtde_nec_saldo_ant numeric;      -- saldo anterior do produto
   v_qtde_guia_produto numeric;       -- qtde do produto com guia de remessa gerada
   v_qtde_disponivel numeric;         -- qtde total disponível no(s) fornecedor(res)
   v_qtde_total_contrato numeric;     -- qtde total contratada com o fornecedor
   v_percentual_forn numeric;         -- percentual que pode ser fornecido pelo forn
   -- quantidade fornecida para o produto pelo fornecedor na guia
   v_qtde_forn numeric;
   v_qtde_guia integer:=0;
   v_qtde_produto_periodo numeric:=0; -- qtde de produto necessária para o período
              --(somatória da qtde percapita da receita)
                                      
   v_id_guia integer:=0;
   v_id_guia_produto integer:=0;
   v_sequencial integer:=0;         -- sequencial da guia de remessa gerada
   v_num_inscr_matr integer:=0;
   v_num_refeicao integer:=0;
   v_dt_cardapio_ini_x date;
   v_dt_cardapio_ini_y integer:=0;
   v_dt_cardapio_ini date;
   v_dt_cardapio_fim date;
   v_classe_aux VARCHAR(2);
   v_peso numeric;
   v_existe_forn integer:=0;
   v_existe_produto integer:=0;
   v_existe_estoque integer:=0;
   --v_id_log integer:=0;
   
BEGIN
   --
   -- Converte data numérica invertida para o formato DD/MM/YYYY
   --
   v_dt_cardapio_ini := TO_DATE(substr(TRIM(TO_CHAR(v_data_inicial,'99999999')),7,2)|| '/' || substr(TRIM(TO_CHAR(v_data_inicial,'99999999')),5,2)|| '/' || substr(TRIM(TO_CHAR(v_data_inicial,'99999999')),1,4),'DD/MM/YYYY');
   v_dt_cardapio_ini_x := v_dt_cardapio_ini;
   v_dt_cardapio_fim := TO_DATE(substr(TRIM(TO_CHAR(v_data_final,'99999999')),7,2)|| '/' || substr(TRIM(TO_CHAR(v_data_final,'99999999')),5,2)|| '/' || substr(TRIM(TO_CHAR(v_data_final,'99999999')),1,4),'DD/MM/YYYY');
   --
   -- INSERE LOG
   --
   --v_id_log:=nextval('alimentos.log_guia_remessa_idlogguia_seq'::text);
   INSERT INTO alimentos.log_guia_remessa
    (idlogguia
    ,login
    ,idcli
    ,dt_inicial
    ,dt_final
    ,unidade
    ,fornecedor
    ,classe
    ,dt_geracao
    ,mensagem)
  VALUES
    (v_id_log
    ,v_login
    ,v_id_cliente
    ,v_dt_cardapio_ini
    ,v_dt_cardapio_fim
    ,v_array_unidade
    ,v_array_fornecedor
    ,v_classe
    ,CURRENT_TIMESTAMP
    ,'*** INÍCIO ***');
   --
   -- Obtém indicação de inscritos ou matriculados para o cliente
   --
   SELECT INTO v_inscritos inscritos
   FROM alimentos.cliente
   WHERE alimentos.cliente.idcli = v_id_cliente;
   RAISE NOTICE 'Inscritos(%)',v_inscritos;
   --
   -- GRAVA LOG
   --
   UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n Inscritos: ' || v_inscritos WHERE idlogguia = v_id_log;
   --
   -- Obtém o número de tipos de refeições que serão atendidas
   -- (total café da manhã, total almoço, etc)
   --
   SELECT INTO v_num_refeicao COUNT(distinct idtre)
   FROM alimentos.cardapio car
   WHERE car.idcli = v_id_cliente
   AND car.finalizado = 'S'
   AND TO_NUMBER(TO_CHAR(car.dt_cardapio,'YYYYMMDD'),'99999999') BETWEEN v_data_inicial AND v_data_final;
   WHILE v_dt_cardapio_ini_x <= v_dt_cardapio_fim LOOP
   
      RAISE NOTICE '---------------->>1º Loop Data(%)',v_dt_cardapio_ini_x;
      
      --
      -- GRAVA LOG
      --
      UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      ----- 1º Loop Data ' || TO_CHAR(v_dt_cardapio_ini_x,'DD/MM/YYYY') || ' -----' WHERE idlogguia = v_id_log;
      --
      -- Prepara SELECT para obter as unidades que atendem aos filtros informados
      --
      v_sql_unidade := 'SELECT distinct uni.iduni as iduni
      ,TRIM(uni.nome) as nome
      FROM alimentos.cardapio car
       ,alimentos.cardapio_faixa_unidade caf
       ,alimentos.unidade_atendida uni
       ,alimentos.unidade_faixa_etaria ufa
      WHERE car.idcar = caf.idcar
      AND caf.idfeu = ufa.idfeu
      AND ufa.iduni = uni.iduni';
      
      IF v_array_unidade <> '0' THEN
         v_sql_unidade := v_sql_unidade || ' AND uni.iduni IN (' || v_array_unidade || ')';
      END IF;
      
      v_sql_unidade := v_sql_unidade || ' AND car.finalizado = ''S''
      AND car.idcli = ''' || v_id_cliente || '''
      AND car.dt_cardapio = ''' || v_dt_cardapio_ini_x || '''';
      
      --
      -- Executa SELECT para obter as unidades
      --
      FOR v_reg_unidade IN EXECUTE v_sql_unidade LOOP
      
         RAISE NOTICE '                                          ';
         RAISE NOTICE '2º Loop Unidade(%)',v_reg_unidade.nome;
         
         --
         -- GRAVA LOG
         --
         UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      ----- 2º Loop Unidade ' || TRIM(TO_CHAR(v_reg_unidade.iduni, '9999999999')) || '-' || v_reg_unidade.nome || ' -----' WHERE idlogguia = v_id_log;
         v_num_inscritos:=0;
   v_num_matriculados:=0;
         
         --
         -- Obtém inscritos e matriculados para a unidade
         --
         
         FOR v_reg_faixa IN SELECT distinct ufa.idfae as idfae
                   ,ufa.num_inscritos as num_inscritos
             ,ufa.num_matriculados as num_matriculados
      FROM alimentos.cardapio car
    ,alimentos.cardapio_faixa_unidade caf
    ,alimentos.unidade_atendida uni
    ,alimentos.unidade_faixa_etaria ufa
      WHERE car.idcar = caf.idcar
      AND caf.idfeu = ufa.idfeu
      AND ufa.iduni = uni.iduni
      AND uni.iduni = v_reg_unidade.iduni
      AND car.finalizado = 'S'
      AND car.idcli = v_id_cliente
      AND car.dt_cardapio = v_dt_cardapio_ini_x LOOP
            
            v_num_inscritos:=v_num_inscritos + v_reg_faixa.num_inscritos;
      v_num_matriculados:=v_num_matriculados + v_reg_faixa.num_matriculados;
            
   END LOOP;
   RAISE NOTICE 'Inscritos(%)',v_num_inscritos;
   RAISE NOTICE 'Matriculados(%)',v_num_matriculados;
         
         --
         -- GRAVA LOG
         --
         UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Nº Inscritos: ' || TRIM(TO_CHAR(v_num_inscritos, '9999999999')) WHERE idlogguia = v_id_log;
   UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Nº Matriculados: ' || TRIM(TO_CHAR(v_num_matriculados, '9999999999')) WHERE idlogguia = v_id_log;
   --
   -- Prepara SELECT para carregar produtos existentes no cardápio
   --
         v_sql_produto := 'SELECT distinct cap.idpro as idpro
        ,cap.quantidade as qtde
        ,pro.fator_correcao as fator_correcao
        ,pro.fator_coccao as fator_coccao
        ,TRIM(pro.nome_compra) as nome_compra
        ,unp.peso as peso
      FROM  alimentos.cardapio car
        ,alimentos.cardapio_produto cap
        ,alimentos.produto pro
        ,alimentos.unidade_produto unp
        ,alimentos.cardapio_faixa_unidade caf
        ,alimentos.unidade_atendida uni
        ,alimentos.unidade_faixa_etaria ufa
      WHERE car.idcar = cap.idcar
      AND cap.idpro = pro.idpro
      AND     pro.idunp = unp.idunp
      AND     unp.idcli = ''' || v_id_cliente || '''
      AND car.idcar = caf.idcar
      AND   caf.idfeu = ufa.idfeu
      AND ufa.iduni = uni.iduni';
   IF v_classe is not null THEN
      v_sql_produto := v_sql_produto || ' AND pro.classe = ''' || v_classe || '''';
         END IF;
         
         --
   -- Seleciona somente os produtos do cardápio que o fornecedor
         -- pode fornecedor
   --
         IF v_array_fornecedor <> '0' THEN
      v_sql_produto := v_sql_produto || ' AND pro.idpro IN (
         SELECT cop.idpro
            FROM alimentos.contrato con
          ,alimentos.contrato_produto cop
                  WHERE cop.idcon = con.idcon
                AND con.cancelado = ''N''
                AND con.finalizado = ''S''
                AND con.ultimo_contrato = ''S''
                AND con.idfor IN (' || v_array_fornecedor || ')
                  AND cop.qtde_remessa < cop.qtde_contratada
                  AND cop.idpro = pro.idpro)';
         END IF;
         
         v_sql_produto := v_sql_produto || '
      AND uni.iduni = ' || v_reg_unidade.iduni || '
            AND car.finalizado = ''S''
            AND car.idcli = ''' || v_id_cliente || '''
            AND car.dt_cardapio = ''' || v_dt_cardapio_ini_x || '''
            ORDER BY TRIM(pro.nome_compra)';
            
         --
         -- Obtém os produtos do cardápio utilizados pela unidade
         --
         
         FOR v_reg_produto IN EXECUTE v_sql_produto LOOP
         
            RAISE NOTICE '                                          ';
            RAISE NOTICE '3º Loop Produto(%)',v_reg_produto.nome_compra;
            
            --
            -- GRAVA LOG
            --
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n      3º Loop Produto ' || TRIM(TO_CHAR(v_reg_produto.idpro, '9999999999')) || '-' || v_reg_produto.nome_compra WHERE idlogguia = v_id_log;
            
            v_existe_produto := v_existe_produto + 1;
            
            --
            -- Calcula a qtde percapita necessária do produto para o período
            --
            
            v_dt_cardapio_ini_y := TO_NUMBER(TO_CHAR(v_dt_cardapio_ini_x, 'YYYYMMDD'),'99999999');
            
            select INTO v_qtde_produto_periodo alimentos.fcn_calcular_qtde_percapita (v_id_cliente, v_reg_unidade.iduni, v_reg_produto.idpro, v_dt_cardapio_ini_y, v_dt_cardapio_ini_y);
            
            --
            --
            -- Calcula a qtde necessária para o produto e para a unidade considerando
            -- os inscritos/matriculados
            --
            if v_inscritos = 'S' THEN
               v_qtde_necessaria := v_qtde_produto_periodo * v_num_inscritos * v_reg_produto.fator_correcao * v_reg_produto.fator_coccao;
            ELSE
               v_qtde_necessaria := v_qtde_produto_periodo * v_num_matriculados * v_reg_produto.fator_correcao * v_reg_produto.fator_coccao;
            END IF;
            
            --
            -- Divide qtde total em gramas pelo peso da unidade do produto
            --
            v_qtde_necessaria := ROUND(v_qtde_necessaria / v_reg_produto.peso);
            
            --
            -- Obtém qtde do produto já gerado em outras guias
            --
            v_qtde_guia_produto := 0;
            
            SELECT INTO v_qtde_guia_produto COALESCE(SUM(qtde),0)
               FROM alimentos.guia_produto_diario
               WHERE idpro = v_reg_produto.idpro
               AND iduni = v_reg_unidade.iduni
               AND dt_guia = v_dt_cardapio_ini_x;
            
            v_qtde_necessaria_saldo:=v_qtde_necessaria - v_qtde_guia_produto;
            
            
            --
            -- GRAVA LOG
            --
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Qtde per capita: ' || TRIM(TO_CHAR(v_qtde_produto_periodo, '9999999999.99')) WHERE idlogguia = v_id_log;
            
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Fator correção: ' || TRIM(TO_CHAR(v_reg_produto.fator_correcao, '9999999999.99')) WHERE idlogguia = v_id_log;
            
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Fator cocção: ' || TRIM(TO_CHAR(v_reg_produto.fator_coccao, '9999999999.99')) WHERE idlogguia = v_id_log;
            
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Peso: ' || TRIM(TO_CHAR(v_reg_produto.peso, '9999999999.99')) WHERE idlogguia = v_id_log;
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Qtde necessária: ' || TRIM(TO_CHAR(v_qtde_necessaria, '9999999999.99')) WHERE idlogguia = v_id_log;
            
            RAISE NOTICE 'Qtde per capita(%)',v_qtde_produto_periodo;
            RAISE NOTICE 'Fator correção(%)',v_reg_produto.fator_correcao;
            RAISE NOTICE 'Fator cocção(%)',v_reg_produto.fator_coccao;
            RAISE NOTICE 'Peso(%)',v_reg_produto.peso;
            RAISE NOTICE 'Qtde Necessária(%)',v_qtde_necessaria;
            
            --
            -- Obtém qtde do produto em contrato, incluindo
            -- todos os fornecedores autorizados a fornecer para a unidade
            -- e que tenham produtos a fornecer (qtde remessa < qtde contrato)
            -- ou qtde remessa = qtde_contrato (estoque zerado) e contrato vigente porque
            -- possuem outros produtos a fornecer
            --
            v_sql_fornecedor := 'SELECT con.idfor as idfor
               ,COALESCE(SUM(cop.qtde_contratada-cop.qtde_remessa),0) as qtde
               ,SUM(cop.qtde_contratada) as qtde_contratada
               FROM alimentos.contrato con
                   ,alimentos.contrato_produto cop
               WHERE con.idcon = cop.idcon
         AND con.cancelado = ''N''
         AND con.finalizado = ''S''
         AND con.ultimo_contrato = ''S''
         AND con.idfor IN (SELECT idfor
                  FROM alimentos.fornecedor_unidade_atendida
                  WHERE iduni = ' || v_reg_unidade.iduni || ')
               AND (cop.qtde_remessa < cop.qtde_contratada
               OR  (cop.qtde_remessa  = cop.qtde_contratada
               AND (SELECT COALESCE(SUM(cop2.qtde_contratada-cop2.qtde_remessa),0)
                       FROM alimentos.contrato_produto cop2
                       WHERE cop2.idcon = con.idcon
                       AND cop2.idpro <> ' ||  v_reg_produto.idpro || ' ) > 0 ))
               AND cop.idpro = ' || v_reg_produto.idpro || '
               GROUP BY con.idfor ';
               
            v_qtde_total_contrato := 0;
            v_existe_forn := 0;
            
            FOR v_reg_fornecedor IN EXECUTE v_sql_fornecedor LOOP
               v_existe_forn := 1;
               v_qtde_total_contrato:= v_qtde_total_contrato + v_reg_fornecedor.qtde_contratada;
            END LOOP;
            
            RAISE NOTICE 'Qtde total contrato(%)',v_qtde_total_contrato;
            
            --
            -- GRAVA LOG
            --
            UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      Qtde total contrato: ' || TRIM(TO_CHAR(v_qtde_total_contrato, '9999999999.99')) WHERE idlogguia = v_id_log;
            
            --
            -- Testa se fornecedor tem contrato e autorização para fornecer para a
            -- unidade
            --
            IF v_existe_forn = 0 THEN
            
               --
               -- GRAVA LOG
               --
               UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n      *** existe forn: N ***' WHERE idlogguia = v_id_log;
               
               RETURN '-1 Produto: ' || v_reg_produto.nome_compra || ' não tem contrato ou o fornecedor contratado não está autorizado a fornecer para a unidade ' || v_reg_unidade.nome || '.';
               
            END IF;
            
            --
            -- Qtde necessária deve ser > 0
            --
            IF v_qtde_necessaria > 0 THEN
            
               --
               -- Calcula a quantidade a ser fornecida por cada fornecedor
               --
               
               -- PREPARA SELECT
               v_sql_fornecedor := 'SELECT con.idcon as idcon
                  ,con.idfor as idfor
                  ,TRIM(con.codigo) as codigo
                  ,cop.qtde_contratada as qtde_contratada
                  ,cop.qtde_remessa as qtde_remessa
                  ,cop.idcop as idcop
                  ,TRIM(forn.nome_fantasia) as nome
                  FROM  alimentos.contrato con
                       ,alimentos.contrato_produto cop
                       ,alimentos.fornecedor forn
                  WHERE con.idcon = cop.idcon
                  AND con.idfor = forn.idfor
                  AND     con.cancelado = ''N''
                  AND     con.finalizado = ''S''
                  AND con.ultimo_contrato = ''S''';
                  
               IF v_array_fornecedor <> '0' THEN
                  v_sql_fornecedor := v_sql_fornecedor || ' AND con.idfor IN (' || v_array_fornecedor || ')';
               END IF;
               
               v_sql_fornecedor := v_sql_fornecedor || ' AND con.idfor IN
                  (SELECT idfor 
                      FROM alimentos.fornecedor_unidade_atendida
                      WHERE iduni = ' || v_reg_unidade.iduni || ')
                      AND cop.qtde_remessa < cop.qtde_contratada
                      AND cop.idpro = ' || v_reg_produto.idpro;
               
               v_qtde_disponivel := 0;
               v_existe_estoque := 0;
               
               -- EXECUTA SELECT PREPARADO
               FOR v_reg_fornecedor IN EXECUTE v_sql_fornecedor LOOP
               
                  RAISE NOTICE '                                          ';
                  RAISE NOTICE '4º Loop Fornecedor(%)',v_reg_fornecedor.nome;
                  
                  --
                  -- GRAVA LOG
                  --
                  UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n           4º Loop Fornecedor ' || TRIM(TO_CHAR(v_reg_fornecedor.idfor, '9999999999')) || '-' || v_reg_fornecedor.nome || ' Contrato: ' || v_reg_fornecedor.codigo  WHERE idlogguia = v_id_log;
                  
                  v_existe_estoque := 1;
                  
                  v_qtde_disponivel:=COALESCE(v_reg_fornecedor.qtde_contratada- v_reg_fornecedor.qtde_remessa,0);
                  
                  v_percentual_forn:=(v_reg_fornecedor.qtde_contratada * 100) / v_qtde_total_contrato;
                  
                  v_qtde_forn:=ROUND((v_qtde_necessaria * v_percentual_forn) / 100 );
                  
                  --
                  -- Verifica quantidade de saldo a ser fornecida é < 0
                  -- Deve ser verificado por questões de arredondamento,
                  -- evitando enviar uma qtde maior que a necessária
                  
                  v_qtde_nec_saldo_ant := v_qtde_necessaria_saldo;
                  v_qtde_necessaria_saldo:=v_qtde_necessaria_saldo - v_qtde_forn;
                  
                  RAISE NOTICE 'Qtde disponível(%)',v_qtde_disponivel;
                  RAISE NOTICE 'Percentual(%)',v_percentual_forn;
                  RAISE NOTICE 'Qtde a fornecer(%)',v_qtde_forn;
                  
                  --
                  -- GRAVA LOG
                  --
                  UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || '  \n           Qtde disponível: ' || TRIM(TO_CHAR(v_qtde_disponivel, '9999999999.99')) WHERE idlogguia = v_id_log;
                  
                  UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || '  \n           Percentual: ' || TRIM(TO_CHAR(v_percentual_forn, '9999999999.9999999999')) WHERE idlogguia = v_id_log;
                  
                  UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || '  \n           Qtde a fornecer: ' || TRIM(TO_CHAR(v_qtde_forn, '9999999999')) WHERE idlogguia = v_id_log;
                  
                  IF v_qtde_necessaria_saldo < 0 THEN
                  
                     v_qtde_forn := v_qtde_nec_saldo_ant;
                     RAISE NOTICE 'Qtde a fornecer ajustada(%)',v_qtde_forn;
                     
                     --
                     -- GRAVA LOG
                     --
                     UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || '  \n           Qtde a fornecer ajustada: ' || TRIM(TO_CHAR(v_qtde_forn, '9999999999')) WHERE idlogguia = v_id_log;
                     
                  END IF;
                  
                  --
                  -- Qtde a ser fornecida do produto deve ser maior que zero
                  -- Qtde disponível > qtde a ser fornecida
                  
                  IF v_qtde_forn > 0 AND v_qtde_disponivel >= v_qtde_forn THEN
                  
                     --
                     -- Grava a Guia de Remessa para a Unidade, Forn, Produto
                     -- e Contrato
                     --
                     v_id_guia:=0;
                     -- Verifica se existe guia gravada
                     SELECT INTO v_id_guia gui.idgui
                        FROM alimentos.guia_remessa gui
                        WHERE gui.iduni = v_reg_unidade.iduni
                        AND gui.idfor = v_reg_fornecedor.idfor
                        AND gui.idcon = v_reg_fornecedor.idcon
                        AND gui.classe_produto = COALESCE (v_classe, gui.classe_produto)
                        AND gui.situacao = 'E'
                        AND gui.dt_cardapio_inicial  = v_dt_cardapio_ini
                        AND gui.dt_cardapio_final = v_dt_cardapio_fim FOR UPDATE;
                     
                     IF v_id_guia IS NULL THEN
                     
                        -- Obtém ID próxima guia
                        v_id_guia := nextval( 'alimentos.guia_remessa_idgui_seq'::text);
                        
                        IF v_inscritos = 'S' THEN
                           v_num_inscr_matr := v_num_inscritos;
                        ELSE
                           v_num_inscr_matr := v_num_matriculados;
                        END IF;
                        
                        v_classe_aux := v_classe;
                        IF v_classe IS NULL THEN
                           v_classe_aux := 'PN';
                        END IF;
                        
                        -- Obtém o próximo sequencial anual para guia
                        SELECT INTO v_sequencial
                           COALESCE(MAX(sequencial),0) + 1
                           FROM alimentos.guia_remessa gui
                           WHERE gui.ano = TO_NUMBER(TO_CHAR(CURRENT_TIMESTAMP, 'YYYY'),'9999')
                           AND gui.idcli = v_id_cliente;
                           
                        --
                        -- GRAVA LOG
                        --
                        UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n           ID Nova guia: ' || TRIM(TO_CHAR(v_id_guia, '9999999999')) || ' Sequencial: ' || TRIM(TO_CHAR(v_sequencial, '9999999999')) WHERE idlogguia = v_id_log;
                        
                        INSERT INTO alimentos.guia_remessa
         (idgui
                           ,idcon
                           ,login_emissao
                           ,idfor
                           ,iduni
                           ,idcli
                           ,dt_emissao
                           ,ano
                           ,sequencial
                           ,dt_cardapio_inicial
                           ,dt_cardapio_final
                           ,num_inscr_matr
                           ,num_refeicao
                           ,situacao
                           ,classe_produto)
                           VALUES (v_id_guia
                           ,v_reg_fornecedor.idcon
                           ,v_login
                           ,v_reg_fornecedor.idfor
                           ,v_reg_unidade.iduni
                           ,v_id_cliente
                           ,CURRENT_TIMESTAMP
                           ,TO_NUMBER(TO_CHAR(CURRENT_TIMESTAMP,'YYYY'),'9999')
                           ,v_sequencial
                           ,v_dt_cardapio_ini
                           ,v_dt_cardapio_fim
                           ,v_num_inscr_matr
                           ,v_num_refeicao
                           ,'E'
                           ,v_classe_aux);
                           
                        v_qtde_guia := v_qtde_guia + 1;
                        
                     END IF;
                     
                     v_id_guia_produto:=0;
                     -- Verifica se produto já existe na guia
                     SELECT INTO v_id_guia_produto gup.idgup
                        FROM alimentos.guia_remessa_produto gup
                            ,alimentos.guia_remessa gui
                        WHERE gui.idgui = gup.idgui
                        AND gup.idpro = v_reg_produto.idpro
                        AND TRIM(gui.login_emissao) = TRIM(v_login)
                        AND gui.idgui = v_id_guia FOR UPDATE;
                        
                     v_peso := v_reg_produto.peso;
                     
                     IF v_peso = 0 THEN
                        v_peso := 1;
                     END IF;
                     
                     IF v_id_guia_produto IS NULL THEN
                     
                        --
                        -- GRAVA LOG
                        --
                        UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n           *** Novo produto na guia ***   ID Guia:  ' || TRIM(TO_CHAR(v_id_guia, '9999999999')) || ' *** ' WHERE idlogguia = v_id_log;
                        
                        -- Insere novo produto na guia
                        INSERT INTO alimentos.guia_remessa_produto (
                            idgui
                           ,idpro
                           ,qtde_per_capita
                           ,qtde_guia
                           ,peso
                           ,qtde_recebida
                           ,peso_total)
                           VALUES(v_id_guia
                           ,v_reg_produto.idpro
                           ,v_reg_produto.qtde
                           ,v_qtde_forn
                           ,v_peso
                           ,0
                           ,ROUND((v_qtde_forn*v_peso),3));
                           
                     ELSE
                     
                        --
                        -- GRAVA LOG
                        --
                        UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n           Update ID guia_produto: ' || TRIM(TO_CHAR(v_id_guia_produto, '9999999999')) || '   ID Guia: ' || TRIM(TO_CHAR(v_id_guia, '9999999999')) WHERE idlogguia = v_id_log;
                        
                        UPDATE alimentos.guia_remessa_produto
                           SET qtde_guia = (qtde_guia + v_qtde_forn)
                           ,peso_total = ROUND(((qtde_guia + v_qtde_forn)*v_peso),3)
                           WHERE idgup = v_id_guia_produto;
                           
                     END IF;
                     
                     --
                     -- Grava guia_produto_diario.
                     -- Armazena a qtde a ser fornecida do produto em uma data  
                     -- e unidade, ou seja, diariamente.
                     --
                     INSERT INTO alimentos.guia_produto_diario
                        (idgui
                        ,idpro
                        ,iduni
                        ,dt_guia
                        ,qtde)
                        VALUES
                        (v_id_guia
                        ,v_reg_produto.idpro
                        ,v_reg_unidade.iduni
                        ,v_dt_cardapio_ini_x
                        ,v_qtde_forn);
                        
                     --
                     -- Diminui estoque do produto
                     --
                     UPDATE alimentos.produto
                        SET qtde_estoque = ROUND((qtde_estoque - v_qtde_forn),2)
                        WHERE idpro = v_reg_produto.idpro;
                        
                     --
                     -- Aumenta quantidade de remessa emitida para o contrato
                     --
                     UPDATE alimentos.contrato_produto
                        SET qtde_remessa = ROUND((qtde_remessa + v_qtde_forn),2)
                        WHERE idcop = v_reg_fornecedor.idcop;
                        
                  ELSE
                  
                     IF v_qtde_disponivel < v_qtde_forn THEN
                     
                        --
                        -- GRAVA LOG
                        --
                        UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n           *** Estoque insuficiente ***' WHERE idlogguia = v_id_log;
                        
                        RETURN '-1 Fornecedor: ' ||  v_reg_fornecedor.nome || ' está com estoque insuficiente para o produto: ' || v_reg_produto.nome_compra || '.';
                        
                     END IF;
                     
                  END IF;
                  
               END LOOP;
               
               --
               -- Identifica que existe contrato com fornecedor, porém
               -- contrato está com estoque zerado.
               IF v_existe_estoque = 0 THEN
               
                  --
                  -- GRAVA LOG
                  --
                  UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n      *** Estoque zerado ***' WHERE idlogguia = v_id_log;
                  
                  RETURN '-1 Produto: ' ||  v_reg_produto.nome_compra || ' está com estoque zerado.';
                                    
               END IF;
                        
            END IF;
            
         END LOOP;
         
      END LOOP;
      
      v_dt_cardapio_ini_x := v_dt_cardapio_ini_x + interval '1 day';
      
   END LOOP;
   
   IF v_existe_produto = 0 AND v_array_fornecedor <> '0' THEN
   
      --
      -- GRAVA LOG
      --
      UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n     *** Fornecedor não possui produtos para fornecer no período. ***' WHERE idlogguia = v_id_log;
      
      RETURN '-1 Fornecedor não possui produtos para fornecer no período. Verifique os contratos elaborados com o fornecedor.';
      
   ELSIF v_qtde_guia > 0 THEN
   
      --
      -- GRAVA LOG
      --
      UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n *** GUIAS GERADAS: ' ||  TRIM(TO_CHAR(v_qtde_guia, '9999999999')) || ' ***' WHERE idlogguia = v_id_log;
      
      RETURN 'Foram geradas ' || REPLACE(TRIM(TO_CHAR(v_qtde_guia,'9,999,999,999')),',','.') || ' guias de remessas.';
      
   ELSE
   
      --
      -- GRAVA LOG
      --
      UPDATE alimentos.log_guia_remessa SET mensagem = mensagem || ' \n \n     *** Não existem dados para gerar guias de acordo com os filtros informados. ***' WHERE idlogguia = v_id_log;
      
      RETURN 'Não existem dados para gerar guias de acordo com os filtros informados.';
      
   END IF;
   
END;$_$
    LANGUAGE plpgsql;


SET search_path = cadastro, pg_catalog;

--
-- Name: fcn_aft_documento(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_documento() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes   numeric;
  BEGIN
    v_idpes := NEW.idpes;
    EXECUTE 'DELETE FROM cadastro.documento WHERE ( (rg = 0 OR rg IS NULL) AND (idorg_exp_rg IS NULL) AND data_exp_rg IS NULL AND (sigla_uf_exp_rg IS NULL OR length(trim(sigla_uf_exp_rg))=0) AND (tipo_cert_civil = 0 OR tipo_cert_civil IS NULL) AND (num_termo = 0 OR num_termo IS NULL) AND (num_livro = 0 OR num_livro IS NULL) AND (num_livro = 0 OR num_livro IS NULL) AND (num_folha = 0 OR num_folha IS NULL) AND data_emissao_cert_civil IS NULL AND (sigla_uf_cert_civil IS NULL OR length(trim(sigla_uf_cert_civil))=0) AND (sigla_uf_cart_trabalho IS NULL OR length(trim(sigla_uf_cart_trabalho))=0) AND (cartorio_cert_civil IS NULL OR length(trim(cartorio_cert_civil))=0) AND (num_cart_trabalho = 0 OR num_cart_trabalho IS NULL) AND (serie_cart_trabalho = 0 OR serie_cart_trabalho IS NULL) AND data_emissao_cart_trabalho IS NULL AND (num_tit_eleitor = 0 OR num_tit_eleitor IS NULL) AND (zona_tit_eleitor = 0 OR zona_tit_eleitor IS NULL) AND (secao_tit_eleitor = 0 OR secao_tit_eleitor IS NULL) ) AND idpes='||quote_literal(v_idpes)||'';
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_documento_provisorio(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_documento_provisorio() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_rg        numeric;
  v_uf_expedicao      text;
  v_verificacao_provisorio  numeric;
  
  v_comando     text;
  v_registro      record;
  
  BEGIN
    v_idpes     := NEW.idpes;
    v_rg      := COALESCE(NEW.rg, -1);
    v_uf_expedicao    := TRIM(COALESCE(NEW.sigla_uf_exp_rg, ''));
    
    v_verificacao_provisorio:= 0;
    
    -- verificar se a situação do cadastro da pessoa é provisório
    FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
      IF v_registro.situacao = 'P' THEN
        v_verificacao_provisorio := 1;
      END IF;
    END LOOP;
    
    -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
    IF LENGTH(v_uf_expedicao) > 0 AND v_rg > 0 AND v_verificacao_provisorio = 1 THEN
      EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_fisica(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_fisica() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_idpes_mae     numeric;
  v_idpes_pai     numeric;
  v_idpes_responsavel   numeric;
  v_idpes_conjuge     numeric;
  
  v_nome_mae      text;
  v_nome_pai      text;
  v_nome_responsavel    text;
  v_nome_conjuge      text;
  
  v_verificacao_mae   numeric;
  v_verificacao_pai   numeric;
  v_verificacao_conjuge   numeric;
  v_verificacao_responsavel numeric;
  
  v_num_aviso_mae     numeric;
  v_num_aviso_pai     numeric;
  v_num_aviso_conjuge   numeric;
  v_num_aviso_responsavel   numeric;
  
  v_existe_aviso_mae    numeric;
  v_existe_aviso_pai    numeric;
  v_existe_aviso_conjuge    numeric;
  v_existe_aviso_responsavel  numeric;
  
  v_comando     text;
  v_registro      record;
  
  BEGIN
    v_idpes     := NEW.idpes;
    v_idpes_mae   := NEW.idpes_mae;
    v_idpes_pai   := NEW.idpes_pai;
    v_idpes_responsavel := NEW.idpes_responsavel;
    v_idpes_conjuge   := NEW.idpes_con;
    v_nome_mae    := TRIM(NEW.nome_mae);
    v_nome_pai    := TRIM(NEW.nome_pai);
    v_nome_responsavel  := TRIM(NEW.nome_responsavel);
    v_nome_conjuge    := TRIM(NEW.nome_conjuge);
    
    v_num_aviso_mae   := 1;
    v_num_aviso_pai   := 2;
    v_num_aviso_conjuge := 3;
    v_num_aviso_responsavel := 4;
    
    v_verificacao_mae   := 0;
    v_verificacao_pai   := 0;
    v_verificacao_conjuge   := 0;
    v_verificacao_responsavel := 0;
    
    v_existe_aviso_mae    := 0;
    v_existe_aviso_pai    := 0;
    v_existe_aviso_conjuge    := 0;
    v_existe_aviso_responsavel  := 0;
    
    -- obter os avisos já existentes para a pessoa
    FOR v_registro IN SELECT aviso FROM cadastro.aviso_nome WHERE idpes=v_idpes LOOP
      IF v_registro.aviso = 1 THEN
        v_existe_aviso_mae := 1;
      ELSIF v_registro.aviso = 2 THEN
        v_existe_aviso_pai := 1;
      ELSIF v_registro.aviso = 3 THEN
        v_existe_aviso_conjuge := 1;
      ELSIF v_registro.aviso = 4 THEN
        v_existe_aviso_responsavel := 1;
      END IF;
    END LOOP;
    
    -- MAE
    IF v_idpes_mae > 0 AND v_idpes_mae IS NOT NULL AND LENGTH(v_nome_mae) > 0 AND v_nome_mae IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_mae, v_idpes_mae) LOOP
        v_verificacao_mae := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_mae := 1;
    END IF;
    
    -- PAI
    IF v_idpes_pai > 0 AND v_idpes_pai IS NOT NULL AND LENGTH(v_nome_pai) > 0 AND v_nome_pai IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_pai, v_idpes_pai) LOOP
        v_verificacao_pai := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_pai := 1;
    END IF;
    
    -- CONJUGE
    IF v_idpes_conjuge > 0 AND v_idpes_conjuge IS NOT NULL AND LENGTH(v_nome_conjuge) > 0 AND v_nome_conjuge IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_conjuge, v_idpes_conjuge) LOOP
        v_verificacao_conjuge := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_conjuge := 1;
    END IF;
    
    -- RESPONSAVEL
    IF v_idpes_responsavel > 0 AND v_idpes_responsavel IS NOT NULL AND LENGTH(v_nome_responsavel) > 0 AND v_nome_responsavel IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_responsavel, v_idpes_responsavel) LOOP
        v_verificacao_responsavel := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_responsavel := 1;
    END IF;
    -- Inserir ou Deletar aviso da MAE
    IF v_verificacao_mae = 0 AND v_existe_aviso_mae = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_mae||');';
    ELSIF v_verificacao_mae = 1 AND v_existe_aviso_mae = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_mae||';';
    END IF;
    
    -- Inserir ou Deletar aviso do PAI
    IF v_verificacao_pai = 0 AND v_existe_aviso_pai = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_pai||');';
    ELSIF v_verificacao_pai = 1 AND v_existe_aviso_pai = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_pai||';';
    END IF;
    
    -- Inserir ou Deletar aviso do CONJUGE
    IF v_verificacao_conjuge = 0 AND v_existe_aviso_conjuge = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_conjuge||');';
    ELSIF v_verificacao_conjuge = 1 AND v_existe_aviso_conjuge = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_conjuge||';';
    END IF;
    
    -- Inserir ou Deletar aviso do RESPONSAVEL
    IF v_verificacao_responsavel = 0 AND v_existe_aviso_responsavel = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_responsavel||');';
    ELSIF v_verificacao_responsavel = 1 AND v_existe_aviso_responsavel = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_responsavel||';';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_fisica_cpf_provisorio(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_fisica_cpf_provisorio() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_cpf       numeric;
  v_verificacao_provisorio  numeric;
  
  v_comando     text;
  v_registro      record;
  
  BEGIN
    v_idpes     := NEW.idpes;
    v_cpf     := COALESCE(NEW.cpf, -1);
    
    v_verificacao_provisorio:= 0;
    
    -- verificar se a situação do cadastro da pessoa é provisório
    FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
      IF v_registro.situacao = 'P' THEN
        v_verificacao_provisorio := 1;
      END IF;
    END LOOP;
    
    -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
    IF v_cpf > 0 AND v_verificacao_provisorio = 1 THEN
      EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_fisica_provisorio(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_fisica_provisorio() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_idpes_mae     numeric;
  v_nome_mae      text;
  v_data_nascimento   text;
  v_verificacao_provisorio  numeric;
  
  v_comando     text;
  v_registro      record;
  
  BEGIN
    v_idpes     := NEW.idpes;
    v_idpes_mae   := COALESCE(NEW.idpes_mae, -1);
    v_nome_mae    := TRIM(COALESCE(NEW.nome_mae, ''));
    v_data_nascimento := COALESCE(TO_CHAR(NEW.data_nasc, 'DD/MM/YYYY'), '');
    
    v_verificacao_provisorio:= 0;
    
    -- verificar se a situação do cadastro da pessoa é provisório
    FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
      IF v_registro.situacao = 'P' THEN
        v_verificacao_provisorio := 1;
      END IF;
    END LOOP;
    
    -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
    IF v_data_nascimento <> '' AND (LENGTH(v_nome_mae) > 0 OR v_idpes_mae > 0) AND v_verificacao_provisorio = 1 THEN
      EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_ins_endereco_externo(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_ins_endereco_externo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes   numeric;
  v_tipo_endereco text;
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_endereco := NEW.tipo;
    EXECUTE 'DELETE FROM cadastro.endereco_pessoa WHERE idpes='||quote_literal(v_idpes)||' AND tipo='||v_tipo_endereco||';';
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_ins_endereco_pessoa(); Type: FUNCTION; Schema: cadastro; Owner: -
--

CREATE FUNCTION fcn_aft_ins_endereco_pessoa() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes   numeric;
  v_tipo_endereco text;
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_endereco := NEW.tipo;
    EXECUTE 'DELETE FROM cadastro.endereco_externo WHERE idpes='||quote_literal(v_idpes)||' AND tipo='||v_tipo_endereco||';';
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


SET search_path = consistenciacao, pg_catalog;

--
-- Name: fcn_delete_temp_cadastro_unificacao_cmf(integer); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_delete_temp_cadastro_unificacao_cmf(integer) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpes ALIAS for $1;
BEGIN
  -- Deleta dados da tabela temp_cadastro_unificacao_cmf
  DELETE FROM consistenciacao.temp_cadastro_unificacao_cmf WHERE idpes = v_idpes;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_temp_cadastro_unificacao_siam(integer); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_delete_temp_cadastro_unificacao_siam(integer) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpes ALIAS for $1;
BEGIN
  -- Deleta dados da tabela temp_cadastro_unificacao_siam
  DELETE FROM consistenciacao.temp_cadastro_unificacao_siam WHERE idpes = v_idpes;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_documento_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_documento_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes         numeric;
  
  v_data_expedicao_rg_nova    date;
  v_data_expedicao_rg_antiga    date;
  v_data_emissao_cert_civil_nova    date;
  v_data_emissao_cert_civil_antiga  date;
  v_data_emissao_cart_trabalho_nova date;
  v_data_emissao_cart_trabalho_antiga date; 
  v_orgao_expedicao_rg_novo   numeric;
  v_orgao_expedicao_rg_antigo   numeric;
  v_numero_rg_novo      numeric;
  v_numero_rg_antigo      numeric;
  v_numero_titulo_eleitor_novo    numeric;
  v_numero_titulo_eleitor_antigo    numeric;
  v_numero_zona_titulo_novo   numeric;
  v_numero_zona_titulo_antigo   numeric;
  v_numero_secao_titulo_novo    numeric;
  v_numero_secao_titulo_antigo    numeric;  
  v_numero_cart_trabalho_novo   numeric;
  v_numero_cart_trabalho_antigo   numeric;
  v_numero_serie_cart_trabalho_novo numeric;
  v_numero_serie_cart_trabalho_antigo numeric;
  v_tipo_certidao_civil_novo    numeric;
  v_tipo_certidao_civil_antigo    numeric;
  v_numero_termo_certidao_civil_novo  numeric;
  v_numero_termo_certidao_civil_antigo  numeric;
  v_numero_livro_certidao_civil_novo  numeric;
  v_numero_livro_certidao_civil_antigo  numeric;
  v_numero_folha_certidao_civil_novo  numeric;
  v_numero_folha_certidao_civil_antigo  numeric;
  v_cartorio_certidao_civil_novo    text;
  v_cartorio_certidao_civil_antigo  text;
  v_uf_expedicao_rg_novo      text;
  v_uf_expedicao_rg_antigo    text;
  v_uf_emissao_certidao_civil_novo  text;
  v_uf_emissao_certidao_civil_antigo  text;
  v_uf_emissao_carteira_trabalho_novo text;
  v_uf_emissao_carteira_trabalho_antigo text;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  v_aux_data_nova     text;
  v_aux_data_antiga   text;
  
  -- ID dos campos
  v_idcam_numero_rg     numeric;
  v_idcam_orgao_expedidor_rg    numeric;
  v_idcam_data_expedicao_rg   numeric;
  v_idcam_uf_expedicao_rg     numeric;
  v_idcam_tipo_certidao_civil   numeric;
  v_idcam_numero_termo_certidao_civil numeric;
  v_idcam_numero_livro_certidao_civil numeric;
  v_idcam_numero_folha_certidao_civil numeric;
  v_idcam_data_emissao_certidao_civil numeric;
  v_idcam_cartorio_certidao_civil   numeric;
  v_idcam_uf_emissao_certidao_civil numeric;
  v_idcam_numero_carteira_trabalho  numeric;
  v_idcam_numero_serie_carteira_trabalho  numeric;
  v_idcam_data_emissao_carteira_trabalho  numeric;
  v_idcam_uf_emissao_carteira_trabalho  numeric;
  v_idcam_numero_titulo_eleitor   numeric;
  v_idcam_numero_zona_titulo_eleitor  numeric;
  v_idcam_numero_secao_titulo_eleitor numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes         := NEW.idpes;
    
    v_data_expedicao_rg_nova    := NEW.data_exp_rg;
    v_data_emissao_cert_civil_nova    := NEW.data_emissao_cert_civil;
    v_data_emissao_cart_trabalho_nova := NEW.data_emissao_cart_trabalho;
    v_orgao_expedicao_rg_novo   := NEW.idorg_exp_rg;
    v_numero_rg_novo      := NEW.rg;
    v_numero_titulo_eleitor_novo    := NEW.num_tit_eleitor;
    v_numero_zona_titulo_novo   := NEW.zona_tit_eleitor;
    v_numero_secao_titulo_novo    := NEW.secao_tit_eleitor;
    v_numero_cart_trabalho_novo   := NEW.num_cart_trabalho;
    v_numero_serie_cart_trabalho_novo := NEW.serie_cart_trabalho;
    v_tipo_certidao_civil_novo    := NEW.tipo_cert_civil;
    v_numero_termo_certidao_civil_novo  := NEW.num_termo;
    v_numero_livro_certidao_civil_novo  := NEW.num_livro;
    v_numero_folha_certidao_civil_novo  := NEW.num_folha;
    v_cartorio_certidao_civil_novo    := NEW.cartorio_cert_civil;
    v_uf_expedicao_rg_novo      := NEW.sigla_uf_exp_rg;
    v_uf_emissao_certidao_civil_novo  := NEW.sigla_uf_cert_civil;
    v_uf_emissao_carteira_trabalho_novo := NEW.sigla_uf_cart_trabalho;
    
    IF TG_OP <> 'UPDATE' THEN
      v_data_expedicao_rg_antiga    := NULL;
      v_data_emissao_cert_civil_antiga  := NULL;
      v_data_emissao_cart_trabalho_antiga := NULL;
      v_orgao_expedicao_rg_antigo   := 0;
      v_numero_rg_antigo      := 0;
      v_numero_titulo_eleitor_antigo    := 0;
      v_numero_zona_titulo_antigo   := 0;
      v_numero_secao_titulo_antigo    := 0;
      v_numero_cart_trabalho_antigo   := 0;
      v_numero_serie_cart_trabalho_antigo := 0;
      v_tipo_certidao_civil_antigo    := 0;
      v_numero_termo_certidao_civil_antigo  := 0;
      v_numero_livro_certidao_civil_antigo  := 0;
      v_numero_folha_certidao_civil_antigo  := 0;
      v_cartorio_certidao_civil_antigo  := '';
      v_uf_expedicao_rg_antigo    := '';
      v_uf_emissao_certidao_civil_antigo  := '';
      v_uf_emissao_carteira_trabalho_antigo := '';
    ELSE
      v_data_expedicao_rg_antiga    := OLD.data_exp_rg;
      v_data_emissao_cert_civil_antiga  := OLD.data_emissao_cert_civil;
      v_data_emissao_cart_trabalho_antiga := OLD.data_emissao_cart_trabalho;
      v_orgao_expedicao_rg_antigo   := COALESCE(OLD.idorg_exp_rg, 0);
      v_numero_rg_antigo      := COALESCE(OLD.rg, 0);
      v_numero_titulo_eleitor_antigo    := COALESCE(OLD.num_tit_eleitor, 0);
      v_numero_zona_titulo_antigo   := COALESCE(OLD.zona_tit_eleitor, 0);
      v_numero_secao_titulo_antigo    := COALESCE(OLD.secao_tit_eleitor, 0);
      v_numero_cart_trabalho_antigo   := COALESCE(OLD.num_cart_trabalho, 0);
      v_numero_serie_cart_trabalho_antigo := COALESCE(OLD.serie_cart_trabalho, 0);
      v_tipo_certidao_civil_antigo    := COALESCE(OLD.tipo_cert_civil, 0);
      v_numero_termo_certidao_civil_antigo  := COALESCE(OLD.num_termo, 0);
      v_numero_livro_certidao_civil_antigo  := COALESCE(OLD.num_livro, 0);
      v_numero_folha_certidao_civil_antigo  := COALESCE(OLD.num_folha, 0);
      v_cartorio_certidao_civil_antigo  := COALESCE(OLD.cartorio_cert_civil, '');
      v_uf_expedicao_rg_antigo    := COALESCE(OLD.sigla_uf_exp_rg, '');
      v_uf_emissao_certidao_civil_antigo  := COALESCE(OLD.sigla_uf_cert_civil, '');
      v_uf_emissao_carteira_trabalho_antigo := COALESCE(OLD.sigla_uf_cart_trabalho, '');
    END IF;
    
    v_idcam_numero_rg     := 6;
    v_idcam_orgao_expedidor_rg    := 7;
    v_idcam_data_expedicao_rg   := 8;
    v_idcam_uf_expedicao_rg     := 9;
    v_idcam_tipo_certidao_civil   := 10;
    v_idcam_numero_termo_certidao_civil := 11;
    v_idcam_numero_livro_certidao_civil := 12;
    v_idcam_numero_folha_certidao_civil := 13;
    v_idcam_data_emissao_certidao_civil := 14;
    v_idcam_cartorio_certidao_civil   := 15;
    v_idcam_uf_emissao_certidao_civil := 16;
    v_idcam_numero_carteira_trabalho  := 17;
    v_idcam_numero_serie_carteira_trabalho  := 18;
    v_idcam_data_emissao_carteira_trabalho  := 19;
    v_idcam_uf_emissao_carteira_trabalho  := 20;
    v_idcam_numero_titulo_eleitor   := 21;
    v_idcam_numero_zona_titulo_eleitor  := 22;
    v_idcam_numero_secao_titulo_eleitor := 23;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      
      -- DATA DE EXPEDICAO DO RG
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_expedicao_rg_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_expedicao_rg_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_nova_credibilidade||');';
      END IF;
    
      -- DATA DE EMISSÃO CERTIDÃO CIVIL
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cert_civil_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cert_civil_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
    
      -- DATA DE EMISSÃO CARTEIRA DE TRABALHO
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
      END IF;
      
      -- ORGÃO EXPEDIDOR DO RG
      IF v_orgao_expedicao_rg_novo <> v_orgao_expedicao_rg_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_orgao_expedidor_rg||','||v_nova_credibilidade||');';
      END IF;
      
      -- RG
      IF v_numero_rg_novo <> v_numero_rg_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_rg||','||v_nova_credibilidade||');';
      END IF;
      
      -- TITULO ELEITOR
      IF v_numero_titulo_eleitor_novo <> v_numero_titulo_eleitor_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_titulo_eleitor||','||v_nova_credibilidade||');';
      END IF;
      
      -- ZONA TITULO ELEITOR
      IF v_numero_zona_titulo_novo <> v_numero_zona_titulo_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_zona_titulo_eleitor||','||v_nova_credibilidade||');';
      END IF;
      
      -- SECAO TITULO ELEITOR
      IF v_numero_secao_titulo_novo <> v_numero_secao_titulo_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_secao_titulo_eleitor||','||v_nova_credibilidade||');';
      END IF;
      
      -- CARTEIRA DE TRABALHO
      IF v_numero_cart_trabalho_novo <> v_numero_cart_trabalho_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_carteira_trabalho||','||v_nova_credibilidade||');';
      END IF;
      
      -- SERIE CARTEIRA DE TRABALHO
      IF v_numero_serie_cart_trabalho_novo <> v_numero_serie_cart_trabalho_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_serie_carteira_trabalho||','||v_nova_credibilidade||');';
      END IF;
      
      -- TIPO CERTIDAO CIVIL
      IF v_tipo_certidao_civil_novo <> v_tipo_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_tipo_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
      
      -- NÚMERO TERMO CERTIDAO CIVIL
      IF v_numero_termo_certidao_civil_novo <> v_numero_termo_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
      
      -- NÚMERO LIVRO CERTIDAO CIVIL
      IF v_numero_livro_certidao_civil_novo <> v_numero_livro_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
      
      -- NÚMERO FOLHA CERTIDAO CIVIL
      IF v_numero_folha_certidao_civil_novo <> v_numero_folha_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
    
      -- CARTÓRIO CERTIDÃO CIVIL
      IF v_cartorio_certidao_civil_novo <> v_cartorio_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
    
      -- UF EXPEDIÇÃO RG
      IF v_uf_expedicao_rg_novo <> v_uf_expedicao_rg_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_nova_credibilidade||');';
      END IF;
    
      -- UF EMISSÃO CERTIDAO CIVIL
      IF v_uf_emissao_certidao_civil_novo <> v_uf_emissao_certidao_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_nova_credibilidade||');';
      END IF;
    
      -- UF EMISSÃO CARTEIRA DE TRABALHO
      IF v_uf_emissao_carteira_trabalho_novo <> v_uf_emissao_carteira_trabalho_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
      END IF;
      
    END IF;
    -- Verificar os campos Vazios ou Nulos
    -- DATA DE EXPEDICAO DO RG
    IF TRIM(v_data_expedicao_rg_nova)='' OR v_data_expedicao_rg_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_sem_credibilidade||');';
    END IF;
    -- DATA DE EMISSÃO CERTIDÃO CIVIL
    IF TRIM(v_data_emissao_cert_civil_nova)='' OR v_data_emissao_cert_civil_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- DATA DE EMISSÃO CARTEIRA DE TRABALHO
    IF TRIM(v_data_emissao_cart_trabalho_nova)='' OR v_data_emissao_cart_trabalho_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
    END IF;
    -- ORGÃO EXPEDIDOR DO RG
    IF v_orgao_expedicao_rg_novo <= 0 OR v_orgao_expedicao_rg_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_orgao_expedidor_rg||','||v_sem_credibilidade||');';
    END IF;
    -- RG
    IF v_numero_rg_novo <= 0 OR v_numero_rg_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_rg||','||v_sem_credibilidade||');';
    END IF;
    -- TITULO ELEITOR
    IF v_numero_titulo_eleitor_novo <= 0 OR v_numero_titulo_eleitor_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_titulo_eleitor||','||v_sem_credibilidade||');';
    END IF;
    -- ZONA TITULO ELEITOR
    IF v_numero_zona_titulo_novo <= 0 OR v_numero_zona_titulo_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_zona_titulo_eleitor||','||v_sem_credibilidade||');';
    END IF;
    -- SECAO TITULO ELEITOR
    IF v_numero_secao_titulo_novo <= 0 OR v_numero_secao_titulo_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_secao_titulo_eleitor||','||v_sem_credibilidade||');';
    END IF;
    -- CARTEIRA DE TRABALHO
    IF v_numero_cart_trabalho_novo <= 0 OR v_numero_cart_trabalho_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_carteira_trabalho||','||v_sem_credibilidade||');';
    END IF;
    -- SERIE CARTEIRA DE TRABALHO
    IF v_numero_serie_cart_trabalho_novo <= 0 OR v_numero_serie_cart_trabalho_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_serie_carteira_trabalho||','||v_sem_credibilidade||');';
    END IF;
    -- TIPO CERTIDAO CIVIL
    IF v_tipo_certidao_civil_novo <= 0 OR v_tipo_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_tipo_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- NÚMERO TERMO CERTIDAO CIVIL
    IF v_numero_termo_certidao_civil_novo <= 0 OR v_numero_termo_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- NÚMERO LIVRO CERTIDAO CIVIL
    IF v_numero_livro_certidao_civil_novo <= 0 OR v_numero_livro_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- NÚMERO FOLHA CERTIDAO CIVIL
    IF v_numero_folha_certidao_civil_novo <= 0 OR v_numero_folha_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- CARTÓRIO CERTIDÃO CIVIL
    IF TRIM(v_cartorio_certidao_civil_novo)='' OR v_cartorio_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- UF EXPEDIÇÃO RG
    IF TRIM(v_uf_expedicao_rg_novo)='' OR v_uf_expedicao_rg_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_sem_credibilidade||');';
    END IF;
    -- UF EMISSÃO CERTIDAO CIVIL
    IF TRIM(v_uf_emissao_certidao_civil_novo)='' OR v_uf_emissao_certidao_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_sem_credibilidade||');';
    END IF;
    -- UF EMISSÃO CARTEIRA DE TRABALHO
    IF TRIM(v_uf_emissao_carteira_trabalho_novo)='' OR v_uf_emissao_carteira_trabalho_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
    END IF;
    
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_endereco_externo_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_endereco_externo_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  
  v_sigla_uf_antiga   text;
  v_sigla_uf_nova     text;
  v_id_tipo_logradouro_antigo text;
  v_id_tipo_logradouro_novo text;
  v_logradouro_antigo   text;
  v_logradouro_novo   text;
  v_numero_antigo     numeric;
  v_numero_novo     numeric;
  v_letra_antiga      text;
  v_letra_nova      text;
  v_complemento_antigo    text;
  v_complemento_novo    text;
  v_bairro_antigo     text;
  v_bairro_novo     text;
  v_cep_antigo      numeric;
  v_cep_novo      numeric;
  v_cidade_antiga     text;
  v_cidade_nova     text;
  
  v_tipo_endereco     numeric;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  
  -- ID dos campos
  v_idcam_sigla_uf_correspondencia    numeric;
  v_idcam_id_tipo_logradouro_correspondencia  numeric;
  v_idcam_logradouro_correspondencia    numeric;
  v_idcam_numero_correspondencia      numeric;
  v_idcam_letra_correspondencia     numeric;
  v_idcam_complemento_correspondencia   numeric;
  v_idcam_bairro_correspondencia      numeric;
  v_idcam_cep_correspondencia     numeric;
  v_idcam_cidade_correspondencia      numeric;
  
  v_idcam_sigla_uf_residencial      numeric;
  v_idcam_id_tipo_logradouro_residencial    numeric;
  v_idcam_logradouro_residencial      numeric;
  v_idcam_numero_residencial      numeric;
  v_idcam_letra_residencial     numeric;
  v_idcam_complemento_residencial     numeric;
  v_idcam_bairro_residencial      numeric;
  v_idcam_cep_residencial       numeric;
  v_idcam_cidade_residencial      numeric;
  
  v_idcam_sigla_uf_comercial      numeric;
  v_idcam_id_tipo_logradouro_comercial    numeric;
  v_idcam_logradouro_comercial      numeric;
  v_idcam_numero_comercial      numeric;
  v_idcam_letra_comercial       numeric;
  v_idcam_complemento_comercial     numeric;
  v_idcam_bairro_comercial      numeric;
  v_idcam_cep_comercial       numeric;
  v_idcam_cidade_comercial      numeric;
  
  v_idcam_sigla_uf    numeric;
  v_idcam_id_tipo_logradouro  numeric;
  v_idcam_logradouro    numeric;
  v_idcam_numero      numeric;
  v_idcam_letra     numeric;
  v_idcam_complemento   numeric;
  v_idcam_bairro      numeric;
  v_idcam_cep     numeric;
  v_idcam_cidade      numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_tipo_endereco     := NEW.tipo;
    
    v_sigla_uf_nova     := NEW.sigla_uf;
    v_id_tipo_logradouro_novo := NEW.idtlog;
    v_logradouro_novo   := NEW.logradouro;
    v_numero_novo     := NEW.numero;
    v_letra_nova      := NEW.letra;
    v_complemento_novo    := NEW.complemento;
    v_bairro_novo     := NEW.bairro;
    v_cep_novo      := NEW.cep;
    v_cidade_nova     := NEW.cidade;
    
    IF TG_OP <> 'UPDATE' THEN
      v_sigla_uf_antiga   := '';
      v_id_tipo_logradouro_antigo := '';
      v_logradouro_antigo   := '';
      v_numero_antigo     := 0;
      v_letra_antiga      := '';
      v_complemento_antigo    := '';
      v_bairro_antigo     := '';
      v_cep_antigo      := 0;
      v_cidade_antiga     := '';
    ELSE
      v_sigla_uf_antiga   := COALESCE(OLD.sigla_uf, '');
      v_id_tipo_logradouro_antigo := COALESCE(OLD.idtlog, '');
      v_logradouro_antigo   := COALESCE(OLD.logradouro, '');
      v_numero_antigo     := COALESCE(OLD.numero, 0);
      v_letra_antiga      := COALESCE(OLD.letra, '');
      v_complemento_antigo    := COALESCE(OLD.complemento, '');
      v_bairro_antigo     := COALESCE(OLD.bairro, '');
      v_cep_antigo      := COALESCE(OLD.cep, 0);
      v_cidade_antiga     := COALESCE(OLD.cidade, '');
    END IF;
    
    v_idcam_sigla_uf_correspondencia    := 55;
    v_idcam_id_tipo_logradouro_correspondencia  := 48;
    v_idcam_logradouro_correspondencia    := 47;
    v_idcam_numero_correspondencia      := 49;
    v_idcam_letra_correspondencia     := 50;
    v_idcam_complemento_correspondencia   := 51;
    v_idcam_bairro_correspondencia      := 52;
    v_idcam_cep_correspondencia     := 53;
    v_idcam_cidade_correspondencia      := 54;
    v_idcam_sigla_uf_residencial      := 64;
    v_idcam_id_tipo_logradouro_residencial    := 57;
    v_idcam_logradouro_residencial      := 56;
    v_idcam_numero_residencial      := 58;
    v_idcam_letra_residencial     := 59;
    v_idcam_complemento_residencial     := 60;
    v_idcam_bairro_residencial      := 61;
    v_idcam_cep_residencial       := 62;
    v_idcam_cidade_residencial      := 63;
    v_idcam_sigla_uf_comercial      := 73;
    v_idcam_id_tipo_logradouro_comercial    := 66;
    v_idcam_logradouro_comercial      := 65;
    v_idcam_numero_comercial      := 67;
    v_idcam_letra_comercial       := 68;
    v_idcam_complemento_comercial     := 69;
    v_idcam_bairro_comercial      := 70;
    v_idcam_cep_comercial       := 71;
    v_idcam_cidade_comercial      := 72;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_tipo_endereco = 1 THEN
      v_idcam_sigla_uf    := v_idcam_sigla_uf_correspondencia;
      v_idcam_id_tipo_logradouro  := v_idcam_id_tipo_logradouro_correspondencia;
      v_idcam_logradouro    := v_idcam_logradouro_correspondencia;
      v_idcam_numero      := v_idcam_numero_correspondencia;
      v_idcam_letra     := v_idcam_letra_correspondencia;
      v_idcam_complemento   := v_idcam_complemento_correspondencia;
      v_idcam_bairro      := v_idcam_bairro_correspondencia;
      v_idcam_cep     := v_idcam_cep_correspondencia;
      v_idcam_cidade      := v_idcam_cidade_correspondencia;
    ELSIF v_tipo_endereco = 2 THEN
      v_idcam_sigla_uf    := v_idcam_sigla_uf_residencial;
      v_idcam_id_tipo_logradouro  := v_idcam_id_tipo_logradouro_residencial;
      v_idcam_logradouro    := v_idcam_logradouro_residencial;
      v_idcam_numero      := v_idcam_numero_residencial;
      v_idcam_letra     := v_idcam_letra_residencial;
      v_idcam_complemento   := v_idcam_complemento_residencial;
      v_idcam_bairro      := v_idcam_bairro_residencial;
      v_idcam_cep     := v_idcam_cep_residencial;
      v_idcam_cidade      := v_idcam_cidade_residencial;
    ELSIF v_tipo_endereco = 3 THEN
      v_idcam_sigla_uf    := v_idcam_sigla_uf_comercial;
      v_idcam_id_tipo_logradouro  := v_idcam_id_tipo_logradouro_comercial;
      v_idcam_logradouro    := v_idcam_logradouro_comercial;
      v_idcam_numero      := v_idcam_numero_comercial;
      v_idcam_letra     := v_idcam_letra_comercial;
      v_idcam_complemento   := v_idcam_complemento_comercial;
      v_idcam_bairro      := v_idcam_bairro_comercial;
      v_idcam_cep     := v_idcam_cep_comercial;
      v_idcam_cidade      := v_idcam_cidade_comercial;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      -- UF
      IF v_sigla_uf_nova <> v_sigla_uf_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sigla_uf||','||v_nova_credibilidade||');';
      END IF;
      
      -- ID TIPO LOGRADOURO
      IF v_id_tipo_logradouro_novo <> v_id_tipo_logradouro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_tipo_logradouro||','||v_nova_credibilidade||');';
      END IF;
      
      -- LOGRADOURO
      IF v_logradouro_novo <> v_logradouro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_logradouro||','||v_nova_credibilidade||');';
      END IF;
      
      -- NUMERO
      IF v_numero_novo <> v_numero_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_nova_credibilidade||');';
      END IF;
      
      -- LETRA
      IF v_letra_nova <> v_letra_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_nova_credibilidade||');';
      END IF;
      
      -- COMPLEMENTO
      IF v_complemento_novo <> v_complemento_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_nova_credibilidade||');';
      END IF;
      
      -- BAIRRO
      IF v_bairro_novo <> v_bairro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_bairro||','||v_nova_credibilidade||');';
      END IF;
      
      -- CEP
      IF v_cep_novo <> v_cep_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_nova_credibilidade||');';
      END IF;
      
      -- CIDADE
      IF v_cidade_nova <> v_cidade_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cidade||','||v_nova_credibilidade||');';
      END IF;
      
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    
    -- UF
    IF TRIM(v_sigla_uf_nova)='' OR v_sigla_uf_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sigla_uf||','||v_sem_credibilidade||');';
    END IF;
    
    -- ID TIPO LOGRADOURO
    IF TRIM(v_id_tipo_logradouro_novo)='' OR v_id_tipo_logradouro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_tipo_logradouro||','||v_sem_credibilidade||');';
    END IF;
    
    -- LOGRADOURO
    IF TRIM(v_logradouro_novo)='' OR v_logradouro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_logradouro||','||v_sem_credibilidade||');';
    END IF;
    -- NUMERO
    IF v_numero_novo <= 0 OR v_numero_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_sem_credibilidade||');';
    END IF;
    
    -- LETRA
    IF TRIM(v_letra_nova)='' OR v_letra_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_sem_credibilidade||');';
    END IF;
    
    -- COMPLEMENTO
    IF TRIM(v_complemento_novo)='' OR v_complemento_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_sem_credibilidade||');';
    END IF;
    
    -- BAIRRO
    IF TRIM(v_bairro_novo)='' OR v_bairro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_bairro||','||v_sem_credibilidade||');';
    END IF;
    -- CEP
    IF v_cep_novo <= 0 OR v_cep_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_sem_credibilidade||');';
    END IF;
    
    -- CIDADE
    IF TRIM(v_cidade_nova)='' OR v_cidade_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cidade||','||v_sem_credibilidade||');';
    END IF;
    
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_endereco_pessoa_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_endereco_pessoa_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_cep_antigo      numeric;
  v_cep_novo      numeric;
  v_id_logradouro_antigo    numeric;
  v_id_logradouro_novo    numeric;
  v_id_bairro_antigo    numeric;
  v_id_bairro_novo    numeric;
  v_numero_antigo     numeric;
  v_numero_novo     numeric;
  v_letra_antiga      text;
  v_letra_nova      text;
  v_complemento_antigo    text;
  v_complemento_novo    text;
  v_tipo_endereco     numeric;
  v_comando     text;
  v_origem_gravacao   text;
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  
  -- ID dos campos
  v_idcam_cep_correspondencia     numeric;
  v_idcam_id_logradouro_correspondencia   numeric;
  v_idcam_id_bairro_correspondencia   numeric;
  v_idcam_numero_correspondencia      numeric;
  v_idcam_letra_correspondencia     numeric;
  v_idcam_complemento_correspondencia   numeric;
  
  v_idcam_cep_residencial       numeric;
  v_idcam_id_logradouro_residencial   numeric;
  v_idcam_id_bairro_residencial     numeric;
  v_idcam_numero_residencial      numeric;
  v_idcam_letra_residencial     numeric;
  v_idcam_complemento_residencial     numeric;
  
  v_idcam_cep_comercial       numeric;
  v_idcam_id_logradouro_comercial     numeric;
  v_idcam_id_bairro_comercial     numeric;
  v_idcam_numero_comercial      numeric;
  v_idcam_letra_comercial       numeric;
  v_idcam_complemento_comercial     numeric;
  
  v_idcam_cep     numeric;
  v_idcam_id_logradouro   numeric;
  v_idcam_id_bairro   numeric;
  v_idcam_numero      numeric;
  v_idcam_letra     numeric;
  v_idcam_complemento   numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_tipo_endereco     := NEW.tipo;
    
    v_cep_novo      := NEW.cep;
    v_id_logradouro_novo    := NEW.idlog;
    v_id_bairro_novo    := NEW.idbai;
    v_numero_novo     := NEW.numero;
    v_letra_nova      := NEW.letra;
    v_complemento_novo    := NEW.complemento;
    
    IF TG_OP <> 'UPDATE' THEN
      v_cep_antigo    := 0;
      v_id_logradouro_antigo  := 0;
      v_id_bairro_antigo  := 0;
      v_numero_antigo   := 0;
      v_letra_antiga    := '';
      v_complemento_antigo  := '';
    ELSE
      v_cep_antigo    := COALESCE(OLD.cep, 0);
      v_id_logradouro_antigo  := COALESCE(OLD.idlog, 0);
      v_id_bairro_antigo  := COALESCE(OLD.idbai, 0);
      v_numero_antigo   := COALESCE(OLD.numero, 0);
      v_letra_antiga    := COALESCE(OLD.letra, '');
      v_complemento_antigo  := COALESCE(OLD.complemento, '');
    END IF;
    
    v_idcam_cep_correspondencia     := 53;
    v_idcam_id_logradouro_correspondencia   := 47;
    v_idcam_id_bairro_correspondencia   := 52;
    v_idcam_numero_correspondencia      := 49;
    v_idcam_letra_correspondencia     := 50;
    v_idcam_complemento_correspondencia   := 51;
    v_idcam_cep_residencial       := 62;
    v_idcam_id_logradouro_residencial   := 56;
    v_idcam_id_bairro_residencial     := 61;
    v_idcam_numero_residencial      := 58;
    v_idcam_letra_residencial     := 59;
    v_idcam_complemento_residencial     := 60;
    v_idcam_cep_comercial       := 71;
    v_idcam_id_logradouro_comercial     := 65;
    v_idcam_id_bairro_comercial     := 70;
    v_idcam_numero_comercial      := 67;
    v_idcam_letra_comercial       := 68;
    v_idcam_complemento_comercial     := 69;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    IF v_tipo_endereco = 1 THEN
      v_idcam_cep     := v_idcam_cep_correspondencia;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_correspondencia;
      v_idcam_id_bairro   := v_idcam_id_bairro_correspondencia;
      v_idcam_numero      := v_idcam_numero_correspondencia;
      v_idcam_letra     := v_idcam_letra_correspondencia;
      v_idcam_complemento   := v_idcam_complemento_correspondencia;
    ELSIF v_tipo_endereco = 2 THEN
      v_idcam_cep     := v_idcam_cep_residencial;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_residencial;
      v_idcam_id_bairro   := v_idcam_id_bairro_residencial;
      v_idcam_numero      := v_idcam_numero_residencial;
      v_idcam_letra     := v_idcam_letra_residencial;
      v_idcam_complemento   := v_idcam_complemento_residencial;
    ELSIF v_tipo_endereco = 3 THEN
      v_idcam_cep     := v_idcam_cep_comercial;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_comercial;
      v_idcam_id_bairro   := v_idcam_id_bairro_comercial;
      v_idcam_numero      := v_idcam_numero_comercial;
      v_idcam_letra     := v_idcam_letra_comercial;
      v_idcam_complemento   := v_idcam_complemento_comercial;
    END IF;
    IF v_nova_credibilidade > 0 THEN
      -- CEP
      IF v_cep_novo <> v_cep_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_nova_credibilidade||');';
      END IF;
      -- ID LOGRADOURO
      IF v_id_logradouro_novo <> v_id_logradouro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_logradouro||','||v_nova_credibilidade||');';
      END IF;
      -- BAIRRO
      IF v_id_bairro_novo <> v_id_bairro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_bairro||','||v_nova_credibilidade||');';
      END IF;
      
      -- NUMERO
      IF v_numero_novo <> v_numero_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_nova_credibilidade||');';
      END IF;
      
      -- LETRA
      IF v_letra_nova <> v_letra_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_nova_credibilidade||');';
      END IF;
      
      -- COMPLEMENTO
      IF v_complemento_novo <> v_complemento_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_nova_credibilidade||');';
      END IF;
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    
    -- CEP
    IF v_cep_novo <= 0 OR v_cep_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_sem_credibilidade||');';
    END IF;
    
    -- ID LOGRADOURO
    IF v_id_logradouro_novo <= 0 OR v_id_logradouro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_logradouro||','||v_sem_credibilidade||');';
    END IF;
    
    -- BAIRRO
    IF v_id_bairro_novo <= 0 OR v_id_bairro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_bairro||','||v_sem_credibilidade||');';
    END IF;
    
    -- NUMERO
    IF v_numero_novo <= 0 OR v_numero_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_sem_credibilidade||');';
    END IF;
    
    -- LETRA
    IF TRIM(v_letra_nova)='' OR v_letra_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_sem_credibilidade||');';
    END IF;
    
    -- COMPLEMENTO
    IF TRIM(v_complemento_novo)='' OR v_complemento_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_fisica_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_fisica_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_data_nasc_nova    date;
  v_data_nasc_antiga    date;
  v_sexo_novo     text;
  v_sexo_antigo     text;
  v_nome_mae_novo     text;
  v_nome_mae_antigo   text;
  v_nome_pai_novo     text;
  v_nome_pai_antigo   text;
  v_nome_conjuge_novo   text;
  v_nome_conjuge_antigo   text;
  v_nome_responsavel_novo   text;
  v_nome_responsavel_antigo text;
  v_nome_ultima_empresa_novo  text;
  v_nome_ultima_empresa_antigo  text;
  v_id_ocupacao_novo    numeric;
  v_id_ocupacao_antigo    numeric;
  v_id_escolaridade_novo    numeric;
  v_id_escolaridade_antigo  numeric;
  v_id_estado_civil_novo    numeric;
  v_id_estado_civil_antigo  numeric;
  v_id_pais_origem_novo   numeric;
  v_id_pais_origem_antigo   numeric;
  v_data_chegada_brasil_nova  date;
  v_data_chegada_brasil_antiga  date;
  v_data_obito_nova   date;
  v_data_obito_antiga   date;
  v_data_uniao_nova   date;
  v_data_uniao_antiga   date;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  v_aux_data_nova     text;
  v_aux_data_antiga   text;
  
  -- ID dos campos
  v_idcam_data_nasc   numeric;
  v_idcam_sexo      numeric;
  v_idcam_nome_mae    numeric;
  v_idcam_nome_pai    numeric;
  v_idcam_nome_conjuge    numeric;
  v_idcam_nome_responsavel  numeric;
  v_idcam_nome_ultima_empresa numeric;
  v_idcam_ocupacao    numeric;
  v_idcam_escolaridade    numeric;
  v_idcam_estado_civil    numeric;
  v_idcam_pais_origem   numeric;
  v_idcam_data_chegada_brasil numeric;
  v_idcam_data_obito    numeric;
  v_idcam_data_uniao    numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_data_nasc_nova    := NEW.data_nasc;
    v_sexo_novo     := NEW.sexo;
    v_nome_mae_novo     := NEW.nome_mae;
    v_nome_pai_novo     := NEW.nome_pai;
    v_nome_conjuge_novo   := NEW.nome_conjuge;
    v_nome_responsavel_novo   := NEW.nome_responsavel;
    v_nome_ultima_empresa_novo  := NEW.ultima_empresa;
    v_id_ocupacao_novo    := NEW.idocup;
    v_id_escolaridade_novo    := NEW.idesco;
    v_id_estado_civil_novo    := NEW.ideciv;
    v_id_pais_origem_novo   := NEW.idpais_estrangeiro;
    v_data_chegada_brasil_nova  := NEW.data_chegada_brasil;
    v_data_obito_nova   := NEW.data_obito;
    v_data_uniao_nova   := NEW.data_uniao;
    
    IF TG_OP <> 'UPDATE' THEN
      v_data_nasc_antiga    := NULL;
      v_sexo_antigo     := '';
      v_nome_mae_antigo   := '';
      v_nome_pai_antigo   := '';
      v_nome_conjuge_antigo   := '';
      v_nome_responsavel_antigo := '';
      v_nome_ultima_empresa_antigo  := '';
      v_id_ocupacao_antigo    := 0;
      v_id_escolaridade_antigo  := 0;
      v_id_estado_civil_antigo  := 0;
      v_id_pais_origem_antigo   := 0;
      v_data_chegada_brasil_antiga  := NULL;
      v_data_obito_antiga   := NULL;
      v_data_uniao_antiga   := NULL;
    ELSE
      v_data_nasc_antiga    := OLD.data_nasc;
      v_sexo_antigo     := COALESCE(OLD.sexo, '');
      v_nome_mae_antigo   := COALESCE(OLD.nome_mae, '');
      v_nome_pai_antigo   := COALESCE(OLD.nome_pai, '');
      v_nome_conjuge_antigo   := COALESCE(OLD.nome_conjuge, '');
      v_nome_responsavel_antigo := COALESCE(OLD.nome_responsavel, '');
      v_nome_ultima_empresa_antigo  := COALESCE(OLD.ultima_empresa, '');
      v_id_ocupacao_antigo    := COALESCE(OLD.idocup, 0);
      v_id_escolaridade_antigo  := COALESCE(OLD.idesco, 0);
      v_id_estado_civil_antigo  := COALESCE(OLD.ideciv, 0);
      v_id_pais_origem_antigo   := COALESCE(OLD.idpais_estrangeiro, 0);
      v_data_chegada_brasil_antiga  := OLD.data_chegada_brasil;
      v_data_obito_antiga   := OLD.data_obito;
      v_data_uniao_antiga   := OLD.data_uniao;
    END IF;
    
    v_idcam_data_nasc   := 5;
    v_idcam_sexo      := 26;
    v_idcam_nome_mae    := 27;
    v_idcam_nome_pai    := 28;
    v_idcam_nome_conjuge    := 29;
    v_idcam_nome_responsavel  := 30;
    v_idcam_nome_ultima_empresa := 31;
    v_idcam_ocupacao    := 32;
    v_idcam_escolaridade    := 33;
    v_idcam_estado_civil    := 34;
    v_idcam_pais_origem   := 35;
    v_idcam_data_chegada_brasil := 36;
    v_idcam_data_obito    := 37;
    v_idcam_data_uniao    := 38;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      
      -- DATA DE NASCIMENTO
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_nasc_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_nasc_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_nasc||','||v_nova_credibilidade||');';
      END IF;
    
      -- DATA DE UNIÃO
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_uniao_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_uniao_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_uniao||','||v_nova_credibilidade||');';
      END IF;
    
      -- DATA DE ÓBITO
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_obito_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_obito_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_obito||','||v_nova_credibilidade||');';
      END IF;
    
      -- DATA DE CHEGADA AO BRASIL
      v_aux_data_nova := COALESCE(TO_CHAR (v_data_chegada_brasil_nova, 'DD/MM/YYYY'), '');
      v_aux_data_antiga := COALESCE(TO_CHAR (v_data_chegada_brasil_antiga, 'DD/MM/YYYY'), '');
      
      IF v_aux_data_nova <> v_aux_data_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_chegada_brasil||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME DA MÃE
      IF v_nome_mae_novo <> v_nome_mae_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_mae||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME DO PAI
      IF v_nome_pai_novo <> v_nome_pai_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_pai||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME DO CONJUGE
      IF v_nome_conjuge_novo <> v_nome_conjuge_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_conjuge||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME DO RESPONSAVEL
      IF v_nome_responsavel_novo <> v_nome_responsavel_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_responsavel||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME ÚLTIMA EMPRESA
      IF v_nome_ultima_empresa_novo <> v_nome_ultima_empresa_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_ultima_empresa||','||v_nova_credibilidade||');';
      END IF;
      
      -- SEXO
      IF v_sexo_novo <> v_sexo_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sexo||','||v_nova_credibilidade||');';
      END IF;
      
      -- ID OCUPAÇÃO PROFISSIONAL
      IF v_id_ocupacao_novo <> v_id_ocupacao_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ocupacao||','||v_nova_credibilidade||');';
      END IF;
      
      -- ID ESCOLARIDADE
      IF v_id_escolaridade_novo <> v_id_escolaridade_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_escolaridade||','||v_nova_credibilidade||');';
      END IF;
      
      -- ID ESTADO CIVIL
      IF v_id_estado_civil_novo <> v_id_estado_civil_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_estado_civil||','||v_nova_credibilidade||');';
      END IF;
      
      -- ID PAIS ORIGEM
      IF v_id_pais_origem_novo <> v_id_pais_origem_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_pais_origem||','||v_nova_credibilidade||');';
      END IF;
      
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    -- DATA DE NASCIMENTO
    IF TRIM(v_data_nasc_nova)='' OR v_data_nasc_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_nasc||','||v_sem_credibilidade||');';
    END IF;
    -- DATA DE UNIÃO
    IF TRIM(v_data_uniao_nova)='' OR v_data_uniao_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_uniao||','||v_sem_credibilidade||');';
    END IF;
    -- DATA DE ÓBITO
    IF TRIM(v_data_obito_nova)='' OR v_data_obito_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_obito||','||v_sem_credibilidade||');';
    END IF;
    -- DATA DE CHEGADA AO BRASIL
    IF TRIM(v_data_chegada_brasil_nova)='' OR v_data_chegada_brasil_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_chegada_brasil||','||v_sem_credibilidade||');';
    END IF;
    -- NOME DA MÃE
    IF TRIM(v_nome_mae_novo)='' OR v_nome_mae_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_mae||','||v_sem_credibilidade||');';
    END IF;
    -- NOME DO PAI
    IF TRIM(v_nome_pai_novo)='' OR v_nome_pai_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_pai||','||v_sem_credibilidade||');';
    END IF;
    -- NOME DO CONJUGE
    IF TRIM(v_nome_conjuge_novo)='' OR v_nome_conjuge_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_conjuge||','||v_sem_credibilidade||');';
    END IF;
    -- NOME DO RESPONSAVEL
    IF TRIM(v_nome_responsavel_novo)='' OR v_nome_responsavel_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_responsavel||','||v_sem_credibilidade||');';
    END IF;
    -- NOME ÚLTIMA EMPRESA
    IF TRIM(v_nome_ultima_empresa_novo)='' OR v_nome_ultima_empresa_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_ultima_empresa||','||v_sem_credibilidade||');';
    END IF;
    -- SEXO
    IF TRIM(v_sexo_novo)='' OR v_sexo_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sexo||','||v_sem_credibilidade||');';
    END IF;
    -- ID OCUPAÇÃO PROFISSIONAL
    IF v_id_ocupacao_novo <= 0 OR v_id_ocupacao_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ocupacao||','||v_sem_credibilidade||');';
    END IF;
    -- ID ESCOLARIDADE
    IF v_id_escolaridade_novo <= 0 OR v_id_escolaridade_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_escolaridade||','||v_sem_credibilidade||');';
    END IF;
    -- ID ESTADO CIVIL
    IF v_id_estado_civil_novo <= 0 OR v_id_estado_civil_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_estado_civil||','||v_sem_credibilidade||');';
    END IF;
    -- ID PAIS ORIGEM
    IF v_id_pais_origem_novo <= 0 OR v_id_pais_origem_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_pais_origem||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_fone_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_fone_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  
  v_ddd_antigo      numeric;
  v_ddd_novo      numeric;
  v_fone_antigo     numeric;
  v_fone_novo     numeric;
  v_tipo_fone     numeric;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  
  -- ID dos campos
  v_idcam_ddd_fone_residencial  numeric;
  v_idcam_fone_residencial  numeric;
  v_idcam_ddd_fone_comercial  numeric;
  v_idcam_fone_comercial    numeric;
  v_idcam_ddd_fone_celular  numeric;
  v_idcam_fone_celular    numeric;
  v_idcam_ddd_fax     numeric;
  v_idcam_fax     numeric;
  
  v_idcam_ddd     numeric;
  v_idcam_fone      numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_fone := NEW.tipo;
    v_ddd_novo  := NEW.ddd;
    v_fone_novo := NEW.fone;
    
    IF TG_OP <> 'UPDATE' THEN
      v_ddd_antigo  := 0;
      v_fone_antigo := 0;
    ELSE
      v_ddd_antigo  := COALESCE(OLD.ddd, 0);
      v_fone_antigo := COALESCE(OLD.fone, 0);
    END IF;
    
    v_idcam_ddd_fone_residencial  := 39;
    v_idcam_fone_residencial  := 40;
    v_idcam_ddd_fone_comercial  := 41;
    v_idcam_fone_comercial    := 42;
    v_idcam_ddd_fone_celular  := 43;
    v_idcam_fone_celular    := 44;
    v_idcam_ddd_fax     := 45;
    v_idcam_fax     := 46;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_tipo_fone = 1 THEN
      v_idcam_ddd := v_idcam_ddd_fone_residencial;
      v_idcam_fone := v_idcam_fone_residencial;
    ELSIF v_tipo_fone = 2 THEN
      v_idcam_ddd := v_idcam_ddd_fone_comercial;
      v_idcam_fone := v_idcam_fone_comercial;
    ELSIF v_tipo_fone = 3 THEN
      v_idcam_ddd := v_idcam_ddd_fone_celular;
      v_idcam_fone := v_idcam_fone_celular;
    ELSIF v_tipo_fone = 4 THEN
      v_idcam_ddd := v_idcam_ddd_fax;
      v_idcam_fone := v_idcam_fax;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      IF v_ddd_novo <> v_ddd_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_nova_credibilidade||');';
      END IF;
      
      IF v_fone_novo <> v_fone_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_nova_credibilidade||');';
      END IF;
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    IF v_ddd_novo <= 0 OR v_ddd_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_sem_credibilidade||');';
    END IF;
    IF v_fone_novo <= 0 OR v_fone_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_sem_credibilidade||');';
    END IF;
    
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_gravar_historico_campo(numeric, numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_gravar_historico_campo(numeric, numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  v_idpes ALIAS for $1;
  v_idcam ALIAS for $2;
  v_credibilidade ALIAS for $3;
  
  v_comando   text;
  v_existe_historico  numeric;  
  v_registro    record;
  
  BEGIN
    -- verificar se já existe histórico para o campo
    v_comando := 'SELECT idcam FROM consistenciacao.historico_campo WHERE idpes='||quote_literal(v_idpes)||' AND idcam='||quote_literal(v_idcam)||';';
    FOR v_registro IN EXECUTE v_comando LOOP
      v_existe_historico := v_registro.idcam;
    END LOOP;
    IF v_existe_historico > 0 THEN
      EXECUTE 'UPDATE consistenciacao.historico_campo SET credibilidade='||v_credibilidade||', data_hora=CURRENT_TIMESTAMP WHERE idpes='||quote_literal(v_idpes)||' AND idcam='||quote_literal(v_idcam)||';';
    ELSE
      EXECUTE 'INSERT INTO consistenciacao.historico_campo(idpes, idcam, credibilidade, data_hora) VALUES ('||quote_literal(v_idpes)||','|| quote_literal(v_idcam)||', '||quote_literal(v_credibilidade)||', CURRENT_TIMESTAMP);';
    END IF;
  RETURN 1;
END; $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_juridica_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_juridica_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_cnpj_novo     numeric;
  v_cnpj_antigo     numeric;
  v_fantasia_novo     text;
  v_fantasia_antigo   text;
  v_inscricao_estadual_novo numeric;
  v_inscricao_estadual_antigo numeric;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  
  -- ID dos campos
  v_idcam_cnpj      numeric;
  v_idcam_fantasia    numeric;
  v_idcam_inscricao_estadual  numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_cnpj_novo     := NEW.cnpj;
    v_fantasia_novo     := NEW.fantasia;
    v_inscricao_estadual_novo := NEW.insc_estadual;
    
    IF TG_OP <> 'UPDATE' THEN
      v_cnpj_antigo     := 0;
      v_fantasia_antigo   := '';
      v_inscricao_estadual_antigo := 0;
    ELSE
      v_cnpj_antigo     := COALESCE(OLD.cnpj, 0);
      v_fantasia_antigo   := COALESCE(OLD.fantasia, '');
      v_inscricao_estadual_antigo := COALESCE(OLD.insc_estadual, 0);
    END IF;
    
    v_idcam_cnpj      := 1;
    v_idcam_fantasia    := 24;
    v_idcam_inscricao_estadual  := 25;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      
      -- CNPJ
      IF v_cnpj_novo <> v_cnpj_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cnpj||','||v_nova_credibilidade||');';
      END IF;
      
      -- NOME FANTASIA
      IF v_fantasia_novo <> v_fantasia_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fantasia||','||v_nova_credibilidade||');';
      END IF;
      
      -- INSCRIÇÃO ESTADUAL
      IF v_inscricao_estadual_novo <> v_inscricao_estadual_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_inscricao_estadual||','||v_nova_credibilidade||');';
      END IF;
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    -- CNPJ
    IF v_cnpj_novo <= 0 OR v_cnpj_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cnpj||','||v_sem_credibilidade||');';
    END IF;
    -- NOME FANTASIA
    IF TRIM(v_fantasia_novo)='' OR v_fantasia_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fantasia||','||v_sem_credibilidade||');';
    END IF;
    
    -- INSCRIÇÃO ESTADUAL
    IF v_inscricao_estadual_novo <= 0 OR v_inscricao_estadual_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_inscricao_estadual||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_pessoa_historico_campo(); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_pessoa_historico_campo() RETURNS "trigger"
    AS $$
DECLARE
  v_idpes       numeric;
  v_nome_novo     text;
  v_nome_antigo     text;
  v_email_novo      text;
  v_email_antigo      text;
  v_url_novo      text;
  v_url_antigo      text;
  
  v_comando     text;
  v_origem_gravacao   text;
  
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  
  v_nova_credibilidade    numeric;
  
  v_registro      record;
  
  -- ID dos campos
  v_idcam_nome      numeric;
  v_idcam_email     numeric;
  v_idcam_url     numeric;
  
  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes   := NEW.idpes;
    v_nome_novo := NEW.nome;
    v_email_novo  := NEW.email;
    v_url_novo  := NEW.url;
    
    IF TG_OP <> 'UPDATE' THEN
      v_nome_antigo := '';
      v_email_antigo  := '';
      v_url_antigo  := '';
    ELSE
      v_nome_antigo := COALESCE(OLD.nome, '');
      v_email_antigo  := COALESCE(OLD.email, '');
      v_url_antigo  := COALESCE(OLD.url, '');
    END IF;
    
    v_idcam_nome  := 2;
    v_idcam_email := 3;
    v_idcam_url := 4;
    
    v_nova_credibilidade := 0;  
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';
    
    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;
    
    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;
    
    IF v_nova_credibilidade > 0 THEN
      
      -- NOME
      IF v_nome_novo <> v_nome_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome||','||v_nova_credibilidade||');';
      END IF;
      
      -- E-MAIL
      IF v_email_novo <> v_email_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_email||','||v_nova_credibilidade||');';
      END IF;
      
      -- URL
      IF v_url_novo <> v_url_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_url||','||v_nova_credibilidade||');';
      END IF;
    END IF;
    
    -- Verificar os campos Vazios ou Nulos
    -- NOME
    IF TRIM(v_nome_novo)='' OR v_nome_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome||','||v_sem_credibilidade||');';
    END IF;
    -- E-MAIL
    IF TRIM(v_email_novo)='' OR v_email_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_email||','||v_sem_credibilidade||');';
    END IF;
    -- URL
    IF TRIM(v_url_novo)='' OR v_url_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_url||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_cadastro(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_cadastro(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho     ALIAS for $1;
  v_idpesNovo      ALIAS for $2;
  v_cpfAux           NUMERIC;
  v_cpfIdpesNovo     NUMERIC;
  v_reg          record;
  v_regAux         record;
BEGIN
  --Unificando registros das tabelas do cadastros de pessoas--
  SET search_path = cadastro, pg_catalog;
  --FONE_PESSOA--
  FOR v_reg IN SELECT *
       FROM fone_pessoa
       WHERE
       idpes = v_idpesVelho AND
       tipo NOT IN (SELECT tipo FROM fone_pessoa WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO fone_pessoa (idpes, tipo, ddd, fone) VALUES (v_idpesNovo,v_reg.tipo, v_reg.ddd, v_reg.fone);
  END LOOP;
  --Atualizando os telefones do idpeNovo que tenham o DDD e o FONE igual a zero--
  FOR v_reg IN SELECT * FROM fone_pessoa WHERE idpes = v_idpesNovo AND ddd = 0 AND fone = 0 LOOP
    DELETE FROM fone_pessoa WHERE idpes = v_reg.idpes AND tipo = v_reg.tipo;
    FOR v_regAux IN SELECT * FROM fone_pessoa WHERE idpes = v_idpesVelho AND tipo = v_reg.tipo LOOP
      INSERT INTO fone_pessoa(idpes,tipo,ddd,fone) VALUES (v_reg.idpes,v_regAux.tipo,v_regAux.ddd,v_regAux.fone);
    END LOOP;
  END LOOP;
  DELETE FROM fone_pessoa WHERE idpes = v_idpesVelho;
  --JURIDICA--
  DELETE FROM juridica WHERE idpes = v_idpesVelho;
  --HISTORICO_CARTAO--
  UPDATE historico_cartao SET idpes_cidadao = v_idpesNovo WHERE idpes_cidadao = v_idpesVelho;
  --ENDERECO_PESSOA--
  FOR v_reg IN SELECT *
         FROM endereco_pessoa
         WHERE
         idpes = v_idpesVelho AND
         tipo NOT IN (SELECT tipo FROM endereco_pessoa WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO endereco_pessoa (idpes, tipo, cep, idlog, idbai, numero, letra, complemento, reside_desde)
    VALUES (v_idpesNovo,v_reg.tipo, v_reg.cep, v_reg.idlog, v_reg.idbai, v_reg.numero, v_reg.letra, v_reg.complemento, v_reg.reside_desde);
  END LOOP;
  DELETE FROM endereco_pessoa WHERE idpes = v_idpesVelho;
  --ENDERECO_EXTERNO--
  FOR v_reg IN SELECT *
     FROM endereco_externo
     WHERE
     idpes = v_idpesVelho AND
     tipo NOT IN (SELECT tipo FROM endereco_externo WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO endereco_externo (idpes, tipo, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, sigla_uf,reside_desde)
    VALUES (v_idpesNovo, v_reg.tipo, v_reg.idtlog, v_reg.logradouro, v_reg.numero, v_reg.letra, v_reg.complemento, v_reg.bairro, v_reg.cep, v_reg.cidade, v_reg.sigla_uf,v_reg.reside_desde);
  END LOOP;
  DELETE FROM endereco_externo WHERE idpes = v_idpesVelho;
  --FISICA_CPF--
  --Obtendo enventual CPF da pessoa antiga para ser inserido na pessoa nova--
  FOR v_reg IN SELECT cpf FROM fisica_cpf WHERE idpes = v_idpesVelho AND idpes <> v_idpesNovo LOOP
    v_cpfAux := v_reg.cpf;
  END LOOP;
  DELETE FROM fisica_cpf WHERE idpes = v_idpesVelho;
  IF v_cpfAux IS NOT NULL THEN
    FOR v_reg IN SELECT cpf FROM fisica_cpf WHERE idpes = v_idpesNovo LOOP
      v_cpfIdpesNovo := v_reg.cpf;
    END LOOP;
    --Verificando se o idpess já possuí um CPF--
    IF v_cpfIdpesNovo IS NULL THEN
      INSERT INTO fisica_cpf (idpes, cpf) VALUES (v_idpesNovo,v_cpfAux);
    END IF;
  END IF;
  --FUNCIONARIO--
  UPDATE funcionario SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --DOCUMENTO--
  DELETE FROM documento WHERE idpes = v_idpesVelho;
  --FISICA--
  DELETE FROM fisica WHERE idpes = v_idpesVelho;
  --PESSOA--
  DELETE FROM pessoa WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_cmf(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_cmf(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo ALIAS for $2;
  
BEGIN
  --Unificando registros do SECM (Sistema de Emissao de Certidoes Municipais)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_secm('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGDO (Sistema de Gestao de Despesas Orcamentarias)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgdo('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGP (Sistema de Gerenciamento de Protocolo)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgp('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGPA (Sistema de Gestao da Praca de Atendimento)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgpa('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGSP (Sistema de Gerenciamento de Servicos consistenciacaoos)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgsp('||v_idpesVelho||','||v_idpesNovo||');';
      --Unificando registros das tabelas do SCD (Sistema de Consintenciacao de Dados)--
   EXECUTE 'SELECT consistenciacao.fcn_unifica_scd('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando registros das tabelas do SCA (Controle de acesso)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sca('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando registros das tabelas de cadastro--
   EXECUTE 'SELECT consistenciacao.fcn_unifica_cadastro('||v_idpesVelho||','||v_idpesNovo||');';
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_sca(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_sca(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  --Parametro recebidos--
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
  v_loginAux   varchar;
  v_reg      record;
BEGIN
  --Unificando registros das tabelas do SCA (Sistema de Controle de Acesso)--
  SET search_path = acesso, pg_catalog;
  --PESSOA_INSTITUICAO--
  --Inserindo as instituicoes do idvelho para o id novo--
  FOR v_reg IN SELECT *
               FROM pessoa_instituicao
               WHERE
         idpes = v_idpesVelho AND
         idins NOT IN
        (SELECT idins FROM
         pessoa_instituicao
         WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO pessoa_instituicao (idpes,idins) VALUES (v_idpesNovo, v_reg.idins);
  END LOOP;
  --Apagando os registro do id velho--
  EXECUTE 'DELETE FROM pessoa_instituicao WHERE idpes = '||v_idpesVelho||';';
  --LOG_ACESSO--
  EXECUTE 'UPDATE log_acesso SET idpes ='||v_idpesNovo||' WHERE idpes = '||v_idpesVelho||';';
  --USUARIO_GRUPO--
  --Obtendo o login do novo idpes para ser usado na unificacao da tabela usuario_grupo--
  FOR v_reg IN SELECT login FROM usuario WHERE idpes = v_idpesNovo LOOP
    v_loginAux := v_reg.login;
  END LOOP;
  --Inserindo os grupos do idpes velho para o idpes novo--
  FOR v_reg IN SELECT *
         FROM usuario_grupo
         WHERE
         login IN (SELECT login FROM usuario WHERE idpes = v_idpesVelho) AND
         idgrp NOT IN (SELECT idgrp
                       FROM usuario_grupo
           WHERE
           login = v_loginAux) LOOP
    INSERT INTO usuario_grupo (idgrp,login) VALUES (v_reg.idgrp, v_loginAux);
  END LOOP;
  --Deletando os registros do idpes velho--
  EXECUTE 'DELETE FROM usuario_grupo
            WHERE login IN
         (SELECT login FROM usuario WHERE idpes ='|| v_idpesVelho ||');';
  --LOG_ERRO--
  EXECUTE 'UPDATE log_erro SET idpes = '||v_idpesNovo||' WHERE idpes = '||v_idpesVelho||';';
  
  --USUARIO--
  EXECUTE 'DELETE FROM usuario WHERE idpes ='|| v_idpesVelho||';';
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_scd(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_scd(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
BEGIN
  --Unificando registros das tabelas do SCD (Sistema de Consintenciacao de Dados)--
  SET search_path = consistenciacao, pg_catalog;
  --INCOERENCIA--
  UPDATE incoerencia_pessoa_possivel SET idpes =  v_idpesNovo WHERE idpes = v_idpesVelho;
  --CONFRONTACAO--
  UPDATE confrontacao SET idpes =  v_idpesNovo WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_sgp(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_sgp(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo ALIAS for $2;
BEGIN
  --Unificando as tabelas do sistema de SGP (Sistema de Gerenciamento de Protocolo)--
  SET search_path = protocolo, pg_catalog;
  --ATIVIDADE--
  UPDATE atividade SET idpes_ini = v_idpesNovo WHERE idpes_ini = v_idpesVelho;
  UPDATE atividade SET idpes_fim = v_idpesNovo WHERE idpes_fim = v_idpesVelho;
  --LOG_FUNC_PROCESSO--
  UPDATE log_func_processo SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --PROCESSO--
  UPDATE processo SET idpesreq = v_idpesNovo WHERE idpesreq = v_idpesVelho;
  UPDATE processo SET idpesfav = v_idpesNovo WHERE idpesfav = v_idpesVelho;
  --TEMP_ATIVIDADE--
  UPDATE temp_atividade SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_sgpa(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_sgpa(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho  ALIAS for $1;
  v_idpesNovo   ALIAS for $2;
  v_reg       record;
  v_idAtnVelho  numeric;
  v_idAtnNovo numeric;
BEGIN
  --Unificando as tabelas do sistema de SGPA (Sistema de getao da Praca de Atendimento)--
  SET search_path = praca, pg_catalog;
  --Obtendo o id do atendente velho para poder fazer UPDATE nas tabelas para o id do atendeentente novo--
  FOR v_reg IN SELECT idatn FROM atendente WHERE idpes = v_idpesVelho LOOP
    v_idAtnVelho := v_reg.idatn;
  END LOOP;
  FOR v_reg IN SELECT idatn FROM atendente WHERE idpes = v_idpesNovo LOOP
    v_idAtnNovo := v_reg.idatn;
  END LOOP;
  --ATENDIMENTO--
  UPDATE atendimento SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  UPDATE atendimento SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --TURNO_ATENDENTE--
  FOR v_reg IN SELECT * FROM turno_atendente
         WHERE
         idatn = v_idAtnVelho AND
         idtur NOT IN (SELECT idtur FROM turno_atendente WHERE idatn = v_idAtnNovo) LOOP
    INSERT INTO turno_atendente (idmes,idins,idatn,idtur) VALUES (v_reg.idmes, v_reg.idins, v_idAtnNovo,v_reg.idtur);
  END LOOP;
  DELETE FROM turno_atendente WHERE idatn = v_idAtnVelho;
  --AUSENCIA--
  UPDATE ausencia SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --SITUCAO_ATENDENTE--
  UPDATE situacao_atendente SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --PRODUTIVIDADE--
  UPDATE produtividade SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --ATENDENTE--
  DELETE FROM atendente WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_unifica_sgsp(numeric, numeric); Type: FUNCTION; Schema: consistenciacao; Owner: -
--

CREATE FUNCTION fcn_unifica_sgsp(numeric, numeric) RETURNS integer
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
  v_reg      record;
BEGIN
  --Unificando registros das tabelas do SGSP (Sistema de Gererenciamento de Serviços Públicos)--
  SET search_path = servicos, pg_catalog;
  --FUNCIONARIO_AUTORIZADO--
  FOR v_reg IN SELECT *
         FROM funcionario_autorizado
         WHERE
         idpes = v_idpesVelho AND
         idpes <> v_idpesNovo LOOP
    INSERT INTO funcionario_autorizado (idpes, idins) VALUES (v_idpesNovo, v_reg.idins);
  END LOOP;
  --SOLICITACAO_SERVICO--
  UPDATE solicitacao_servico SET idpes_atendente = v_idpesNovo WHERE idpes_atendente = v_idpesVelho;
  UPDATE solicitacao_servico SET idpes_planejamento = v_idpesNovo WHERE idpes_planejamento = v_idpesVelho;
  --SOLICITANTE--
  UPDATE solicitante SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --ORDEM_SERVICO--
  UPDATE ordem_servico SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --APROVACAO--
  UPDATE aprovacao SET idpes_resposta = v_idpesNovo WHERE idpes_resposta = v_idpesVelho;
  --AUTORIZACAO_DIARIA--
  UPDATE autorizacao_diaria SET idpes_usuario = v_idpesNovo WHERE idpes_usuario = v_idpesVelho;
  UPDATE autorizacao_diaria SET idpes_autorizacao = v_idpesNovo WHERE idpes_autorizacao = v_idpesVelho;
  --FUNCIONARIO_AUTORIZADO--
  DELETE FROM funcionario_autorizado WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


SET search_path = historico, pg_catalog;

--
-- Name: fcn_delete_grava_historico_bairro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_bairro() RETURNS "trigger"
    AS $$
DECLARE
   v_idbai    numeric;
   v_idmun    numeric;
   v_nome   varchar;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_idbai    := OLD.idbai;
   v_idmun    := OLD.idmun;
   v_nome   := OLD.nome;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA BAIRRO
      INSERT INTO historico.bairro
      (idbai, idmun, nome, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idbai, v_idmun, v_nome, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_cep_logradouro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_cep_logradouro() RETURNS "trigger"
    AS $$
DECLARE
   v_cep    numeric;
   v_idlog    numeric;
   v_nroini   numeric;
   v_nrofin   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_cep    := OLD.cep;
   v_idlog    := OLD.idlog;
   v_nroini   := OLD.nroini;
   v_nrofin   := OLD.nrofin;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA CEP_LOGRADOURO
      INSERT INTO historico.cep_logradouro
      (cep, idlog, nroini, nrofin, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_cep, v_idlog, v_nroini, v_nrofin, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_cep_logradouro_bairro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_cep_logradouro_bairro() RETURNS "trigger"
    AS $$
DECLARE
   v_idbai    numeric;
   v_idlog    numeric;
   v_cep    numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idbai    := OLD.idbai;
   v_idlog    := OLD.idlog;
   v_cep    := OLD.cep;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA CEP_LOGRADOURO_BAIRRO
      INSERT INTO historico.cep_logradouro_bairro
      (idbai, idlog, cep, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idbai, v_idlog, v_cep, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_documento(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_documento() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_sigla_uf_exp_rg  char(2);
   v_rg     numeric;
   v_data_exp_rg  date;
   v_tipo_cert_civil  numeric;
   v_num_termo    numeric;
   v_num_livro    varchar;
   v_num_folha    numeric;
   v_data_emissao_cert_civil  date;
   v_sigla_uf_cert_civil  char(2);
   v_sigla_uf_cart_trabalho char(2);
   v_cartorio_cert_civil  varchar;
   v_num_cart_trabalho    numeric;
   v_serie_cart_trabalho  numeric;
   v_data_emissao_cart_trabalho date;
   v_num_tit_eleitor  numeric;
   v_zona_tit_eleitor numeric;
   v_secao_tit_eleitor  numeric;
   v_idorg_exp_rg   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_sigla_uf_exp_rg    := OLD.sigla_uf_exp_rg;
   v_rg       := OLD.rg;
   v_data_exp_rg    := OLD.data_exp_rg;
   v_tipo_cert_civil    := OLD.tipo_cert_civil;
   v_num_termo      := OLD.num_termo;
   v_num_livro      := OLD.num_livro;
   v_num_folha      := OLD.num_folha;
   v_data_emissao_cert_civil  := OLD.data_emissao_cert_civil;
   v_sigla_uf_cert_civil  := OLD.sigla_uf_cert_civil;
   v_sigla_uf_cart_trabalho := OLD.sigla_uf_cart_trabalho;
   v_cartorio_cert_civil  := OLD.cartorio_cert_civil;
   v_num_cart_trabalho    := OLD.num_cart_trabalho;
   v_serie_cart_trabalho  := OLD.serie_cart_trabalho;
   v_data_emissao_cart_trabalho := OLD.data_emissao_cart_trabalho;
   v_num_tit_eleitor    := OLD.num_tit_eleitor;
   v_zona_tit_eleitor   := OLD.zona_tit_eleitor;
   v_secao_tit_eleitor    := OLD.secao_tit_eleitor;
   v_idorg_exp_rg   := OLD.idorg_exp_rg;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA DOCUMENTO
      INSERT INTO historico.documento
      (idpes, sigla_uf_exp_rg, rg, data_exp_rg, tipo_cert_civil, num_termo, num_livro, num_folha, data_emissao_cert_civil, sigla_uf_cert_civil, sigla_uf_cart_trabalho, cartorio_cert_civil, num_cart_trabalho, serie_cart_trabalho, data_emissao_cart_trabalho, num_tit_eleitor, zona_tit_eleitor, secao_tit_eleitor, idorg_exp_rg, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_sigla_uf_exp_rg, v_rg, v_data_exp_rg, v_tipo_cert_civil, v_num_termo, v_num_livro, v_num_folha, v_data_emissao_cert_civil, v_sigla_uf_cert_civil, v_sigla_uf_cart_trabalho, v_cartorio_cert_civil, v_num_cart_trabalho, v_serie_cart_trabalho, v_data_emissao_cart_trabalho, v_num_tit_eleitor, v_zona_tit_eleitor, v_secao_tit_eleitor, v_idorg_exp_rg, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_endereco_externo(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_endereco_externo() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_tipo   numeric;
   v_sigla_uf   char(2);
   v_idtlog   varchar;
   v_logradouro   varchar;
   v_numero   numeric;
   v_letra    char(1);
   v_complemento  varchar;
   v_bairro   varchar;
   v_cep    numeric;
   v_cidade   varchar;
   v_reside_desde date;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_tipo     := OLD.tipo;
   v_sigla_uf     := OLD.sigla_uf;
   v_idtlog     := OLD.idtlog;
   v_logradouro     := OLD.logradouro;
   v_numero     := OLD.numero;
   v_letra      := OLD.letra;
   v_complemento    := OLD.complemento;
   v_bairro     := OLD.bairro;
   v_cep      := OLD.cep;
   v_cidade     := OLD.cidade;
   v_reside_desde   := OLD.reside_desde;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA ENDERECO_EXTERNO
      INSERT INTO historico.endereco_externo
      (idpes, tipo, sigla_uf, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, reside_desde, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_sigla_uf, v_idtlog, v_logradouro, v_numero, v_letra, v_complemento, v_bairro, v_cep, v_cidade, v_reside_desde, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_endereco_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_endereco_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_tipo   numeric;
   v_cep    numeric;
   v_idlog    numeric;
   v_idbai    numeric;
   v_numero   numeric;
   v_letra    char(1);
   v_complemento  varchar;
   v_reside_desde date;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_idpes    := OLD.idpes;
   v_tipo   := OLD.tipo;
   v_cep    := OLD.cep;
   v_idlog    := OLD.idlog;
   v_idbai    := OLD.idbai;
   v_numero   := OLD.numero;
   v_letra    := OLD.letra;
   v_complemento  := OLD.complemento;
   v_reside_desde := OLD.reside_desde;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA ENDERECO_PESSOA
      INSERT INTO historico.endereco_pessoa
      (idpes, tipo, cep, idlog, idbai, numero, letra, complemento, reside_desde, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_cep, v_idlog, v_idbai, v_numero, v_letra, v_complemento, v_reside_desde, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_fisica(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_fisica() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_data_nasc      date;
   v_sexo     char(1);
   v_idpes_mae      numeric;
   v_idpes_pai      numeric;
   v_idpes_responsavel    numeric;
   v_idesco     numeric;
   v_ideciv     numeric;
   v_idpes_con      numeric;
   v_data_uniao     date;
   v_data_obito     date;
   v_nacionalidade    numeric;
   v_idpais_estrangeiro   numeric;
   v_data_chegada_brasil  date;
   v_idmun_nascimento   numeric;
   v_ultima_empresa   varchar;
   v_idocup     numeric;
   v_nome_mae     varchar;
   v_nome_pai     varchar;
   v_nome_conjuge   varchar;
   v_nome_responsavel   varchar;
   v_justificativa_provisorio varchar;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_data_nasc      := OLD.data_nasc;
   v_sexo     := OLD.sexo;
   v_idpes_mae      := OLD.idpes_mae;
   v_idpes_pai      := OLD.idpes_pai;
   v_idpes_responsavel    := OLD.idpes_responsavel;
   v_idesco     := OLD.idesco;
   v_ideciv     := OLD.ideciv;
   v_idpes_con      := OLD.idpes_con;
   v_data_uniao     := OLD.data_uniao;
   v_data_obito     := OLD.data_obito;
   v_nacionalidade    := OLD.nacionalidade;
   v_idpais_estrangeiro   := OLD.idpais_estrangeiro;
   v_data_chegada_brasil  := OLD.data_chegada_brasil;
   v_idmun_nascimento   := OLD.idmun_nascimento;
   v_ultima_empresa   := OLD.ultima_empresa;
   v_idocup     := OLD.idocup;
   v_nome_mae     := OLD.nome_mae;
   v_nome_pai     := OLD.nome_pai;
   v_nome_conjuge   := OLD.nome_conjuge;
   v_nome_responsavel   := OLD.nome_responsavel;
   v_justificativa_provisorio := OLD.justificativa_provisorio;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FISICA
      INSERT INTO historico.fisica
      (idpes, data_nasc, sexo, idpes_mae, idpes_pai, idpes_responsavel, idesco, ideciv, idpes_con, data_uniao, data_obito, nacionalidade, idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, ultima_empresa, idocup, nome_mae, nome_pai, nome_conjuge, nome_responsavel, justificativa_provisorio, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_data_nasc, v_sexo, v_idpes_mae, v_idpes_pai, v_idpes_responsavel, v_idesco, v_ideciv, v_idpes_con, v_data_uniao, v_data_obito, v_nacionalidade, v_idpais_estrangeiro, v_data_chegada_brasil, v_idmun_nascimento, v_ultima_empresa, v_idocup, v_nome_mae, v_nome_pai, v_nome_conjuge, v_nome_responsavel, v_justificativa_provisorio, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_fisica_cpf(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_fisica_cpf() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_cpf      numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_cpf      := OLD.cpf;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FISICA_CPF
      INSERT INTO historico.fisica_cpf
      (idpes, cpf, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_cpf, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_fone_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_fone_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_tipo     numeric;
   v_ddd      numeric;
   v_fone     numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_tipo     := OLD.tipo;
   v_ddd      := OLD.ddd;
   v_fone     := OLD.fone;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FONE_PESSOA
      INSERT INTO historico.fone_pessoa
      (idpes, tipo, ddd, fone, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_ddd, v_fone, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_funcionario(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_funcionario() RETURNS "trigger"
    AS $$
DECLARE
   v_matricula      numeric;
   v_idins      numeric;
   v_idset      numeric;
   v_idpes      numeric;
   v_situacao     char(1);
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_matricula      := OLD.matricula;
   v_idins      := OLD.idins;
   v_idset      := OLD.idset;
   v_idpes      := OLD.idpes;
   v_situacao     := OLD.situacao;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FUNCIONARIO
      INSERT INTO historico.funcionario
      (matricula, idins, idset, idpes, situacao, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_matricula, v_idins, v_idset, v_idpes, v_situacao, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_juridica(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_juridica() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_cnpj     numeric;
   v_fantasia     varchar;
   v_insc_estadual    numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_cnpj     := OLD.cnpj;
   v_fantasia     := OLD.fantasia;
   v_insc_estadual    := OLD.insc_estadual;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA JURIDICA
      INSERT INTO historico.juridica
      (idpes, cnpj, fantasia, insc_estadual, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_cnpj, v_fantasia, v_insc_estadual, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_logradouro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_logradouro() RETURNS "trigger"
    AS $$
DECLARE
   v_idlog      numeric;
   v_idtlog     varchar;
   v_nome     varchar;
   v_idmun      numeric;
   v_ident_oficial    char(1);
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idlog      := OLD.idlog;
   v_idtlog     := OLD.idtlog;
   v_nome     := OLD.nome;
   v_idmun      := OLD.idmun;
   v_ident_oficial    := OLD.ident_oficial;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA LOGRADOURO
      INSERT INTO historico.logradouro
      (idlog, idtlog, nome, idmun, ident_oficial, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idlog, v_idtlog, v_nome, v_idmun, v_ident_oficial, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_municipio(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_municipio() RETURNS "trigger"
    AS $$
DECLARE
   v_idmun      numeric;
   v_sigla_uf     char(2);
   v_nome     varchar;
   v_area_km2     numeric;
   v_idmreg     numeric;
   v_idasmun      numeric;
   v_cod_ibge     numeric;
   v_geom     TEXT;
   v_tipo     char(1);
   v_idmun_pai      numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idmun      := OLD.idmun;
   v_sigla_uf     := OLD.sigla_uf;
   v_nome     := OLD.nome;
   v_area_km2     := OLD.area_km2;
   v_idmreg     := OLD.idmreg;
   v_idasmun      := OLD.idasmun;
   v_cod_ibge     := OLD.cod_ibge;
   v_geom     := OLD.geom;
   v_tipo     := OLD.tipo;
   v_idmun_pai      := OLD.idmun_pai;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA MUNICIPIO
      INSERT INTO historico.municipio
      (idmun, sigla_uf, nome, area_km2, idmreg, idasmun, cod_ibge, geom, tipo, idmun_pai, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idmun, v_sigla_uf, v_nome, v_area_km2, v_idmreg, v_idasmun, v_cod_ibge, v_geom, v_tipo, v_idmun_pai, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_nome   TEXT;
   v_idpes_cad    numeric;
   v_data_cad   timestamp;
   v_url    TEXT;
   v_tipo   char;
   v_idpes_rev    numeric;
   v_data_rev   timestamp;
   v_email    TEXT;
   v_situacao   char;
   v_origem_gravacao  char;
   v_idsis_rev    numeric;
   v_idsis_cad    numeric;
BEGIN
   v_idpes    := OLD.idpes;
   v_nome   := OLD.nome;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_url    := OLD.url;
   v_tipo   := OLD.tipo;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_email    := OLD.email;
   v_situacao   := OLD.situacao;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idsis_cad    := OLD.idsis_cad;
      
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA PESSOA
      INSERT INTO historico.pessoa
      (idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, email, situacao, origem_gravacao, idsis_rev, idsis_cad, operacao) VALUES 
      (v_idpes, v_nome, v_idpes_cad, v_data_cad, v_url, v_tipo, v_idpes_rev, v_data_rev, v_email, v_situacao, v_origem_gravacao, v_idsis_rev, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_grava_historico_socio(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_delete_grava_historico_socio() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes_juridica   numeric;
   v_idpes_fisica   numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_idpes_juridica   := OLD.idpes_juridica;
   v_idpes_fisica   := OLD.idpes_fisica;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA SOCIO
      INSERT INTO historico.socio
      (idpes_juridica, idpes_fisica, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes_juridica, v_idpes_fisica, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_bairro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_bairro() RETURNS "trigger"
    AS $$
DECLARE
   v_idbai    numeric;
   v_idmun    numeric;
   v_nome   varchar;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idbai    := OLD.idbai;
   v_idmun    := OLD.idmun;
   v_nome   := OLD.nome;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA BAIRRO
      INSERT INTO historico.bairro
      (idbai, idmun, nome, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idbai, v_idmun, v_nome, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_cep_logradouro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_cep_logradouro() RETURNS "trigger"
    AS $$
DECLARE
   v_cep    numeric;
   v_idlog    numeric;
   v_nroini   numeric;
   v_nrofin   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_cep    := OLD.cep;
   v_idlog    := OLD.idlog;
   v_nroini   := OLD.nroini;
   v_nrofin   := OLD.nrofin;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA CEP_LOGRADOURO
      INSERT INTO historico.cep_logradouro
      (cep, idlog, nroini, nrofin, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_cep, v_idlog, v_nroini, v_nrofin, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_cep_logradouro_bairro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_cep_logradouro_bairro() RETURNS "trigger"
    AS $$
DECLARE
   v_idbai    numeric;
   v_idlog    numeric;
   v_cep    numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idbai    := OLD.idbai;
   v_idlog    := OLD.idlog;
   v_cep    := OLD.cep;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA CEP_LOGRADOURO_BAIRRO
      INSERT INTO historico.cep_logradouro_bairro
      (idbai, idlog, cep, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idbai, v_idlog, v_cep, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_documento(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_documento() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_sigla_uf_exp_rg  char(2);
   v_rg     numeric;
   v_data_exp_rg  date;
   v_tipo_cert_civil  numeric;
   v_num_termo    numeric;
   v_num_livro    varchar;
   v_num_folha    numeric;
   v_data_emissao_cert_civil  date;
   v_sigla_uf_cert_civil  char(2);
   v_sigla_uf_cart_trabalho char(2);
   v_cartorio_cert_civil  varchar;
   v_num_cart_trabalho    numeric;
   v_serie_cart_trabalho  numeric;
   v_data_emissao_cart_trabalho date;
   v_num_tit_eleitor  numeric;
   v_zona_tit_eleitor numeric;
   v_secao_tit_eleitor  numeric;
   v_idorg_exp_rg   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_sigla_uf_exp_rg    := OLD.sigla_uf_exp_rg;
   v_rg       := OLD.rg;
   v_data_exp_rg    := OLD.data_exp_rg;
   v_tipo_cert_civil    := OLD.tipo_cert_civil;
   v_num_termo      := OLD.num_termo;
   v_num_livro      := OLD.num_livro;
   v_num_folha      := OLD.num_folha;
   v_data_emissao_cert_civil  := OLD.data_emissao_cert_civil;
   v_sigla_uf_cert_civil  := OLD.sigla_uf_cert_civil;
   v_sigla_uf_cart_trabalho := OLD.sigla_uf_cart_trabalho;
   v_cartorio_cert_civil  := OLD.cartorio_cert_civil;
   v_num_cart_trabalho    := OLD.num_cart_trabalho;
   v_serie_cart_trabalho  := OLD.serie_cart_trabalho;
   v_data_emissao_cart_trabalho := OLD.data_emissao_cart_trabalho;
   v_num_tit_eleitor    := OLD.num_tit_eleitor;
   v_zona_tit_eleitor   := OLD.zona_tit_eleitor;
   v_secao_tit_eleitor    := OLD.secao_tit_eleitor;
   v_idorg_exp_rg   := OLD.idorg_exp_rg;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA DOCUMENTO
      INSERT INTO historico.documento
      (idpes, sigla_uf_exp_rg, rg, data_exp_rg, tipo_cert_civil, num_termo, num_livro, num_folha, data_emissao_cert_civil, sigla_uf_cert_civil, sigla_uf_cart_trabalho, cartorio_cert_civil, num_cart_trabalho, serie_cart_trabalho, data_emissao_cart_trabalho, num_tit_eleitor, zona_tit_eleitor, secao_tit_eleitor, idorg_exp_rg, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_sigla_uf_exp_rg, v_rg, v_data_exp_rg, v_tipo_cert_civil, v_num_termo, v_num_livro, v_num_folha, v_data_emissao_cert_civil, v_sigla_uf_cert_civil, v_sigla_uf_cart_trabalho, v_cartorio_cert_civil, v_num_cart_trabalho, v_serie_cart_trabalho, v_data_emissao_cart_trabalho, v_num_tit_eleitor, v_zona_tit_eleitor, v_secao_tit_eleitor, v_idorg_exp_rg, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_endereco_externo(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_endereco_externo() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_tipo   numeric;
   v_sigla_uf   char(2);
   v_idtlog   varchar;
   v_logradouro   varchar;
   v_numero   numeric;
   v_letra    char(1);
   v_complemento  varchar;
   v_bairro   varchar;
   v_cep    numeric;
   v_cidade   varchar;
   v_reside_desde date;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes    := OLD.idpes;
   v_tipo   := OLD.tipo;
   v_sigla_uf   := OLD.sigla_uf;
   v_idtlog   := OLD.idtlog;
   v_logradouro   := OLD.logradouro;
   v_numero   := OLD.numero;
   v_letra    := OLD.letra;
   v_complemento  := OLD.complemento;
   v_bairro   := OLD.bairro;
   v_cep    := OLD.cep;
   v_cidade   := OLD.cidade;
   v_reside_desde := OLD.reside_desde;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA ENDERECO_EXTERNO
      INSERT INTO historico.endereco_externo
      (idpes, tipo, sigla_uf, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, reside_desde, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_sigla_uf, v_idtlog, v_logradouro, v_numero, v_letra, v_complemento, v_bairro, v_cep, v_cidade, v_reside_desde, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_endereco_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_endereco_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_tipo   numeric;
   v_cep    numeric;
   v_idlog    numeric;
   v_idbai    numeric;
   v_numero   numeric;
   v_letra    char(1);
   v_complemento  varchar;
   v_reside_desde date;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes    := OLD.idpes;
   v_tipo   := OLD.tipo;
   v_cep    := OLD.cep;
   v_idlog    := OLD.idlog;
   v_idbai    := OLD.idbai;
   v_numero   := OLD.numero;
   v_letra    := OLD.letra;
   v_complemento  := OLD.complemento;
   v_reside_desde := OLD.reside_desde;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA ENDERECO_PESSOA
      INSERT INTO historico.endereco_pessoa
      (idpes, tipo, cep, idlog, idbai, numero, letra, complemento, reside_desde, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_cep, v_idlog, v_idbai, v_numero, v_letra, v_complemento, v_reside_desde, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_fisica(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_fisica() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_data_nasc      date;
   v_sexo     char(1);
   v_idpes_mae      numeric;
   v_idpes_pai      numeric;
   v_idpes_responsavel    numeric;
   v_idesco     numeric;
   v_ideciv     numeric;
   v_idpes_con      numeric;
   v_data_uniao     date;
   v_data_obito     date;
   v_nacionalidade    numeric;
   v_idpais_estrangeiro   numeric;
   v_data_chegada_brasil  date;
   v_idmun_nascimento   numeric;
   v_ultima_empresa   varchar;
   v_idocup     numeric;
   v_nome_mae     varchar;
   v_nome_pai     varchar;
   v_nome_conjuge   varchar;
   v_nome_responsavel   varchar;
   v_justificativa_provisorio varchar;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_data_nasc      := OLD.data_nasc;
   v_sexo     := OLD.sexo;
   v_idpes_mae      := OLD.idpes_mae;
   v_idpes_pai      := OLD.idpes_pai;
   v_idpes_responsavel    := OLD.idpes_responsavel;
   v_idesco     := OLD.idesco;
   v_ideciv     := OLD.ideciv;
   v_idpes_con      := OLD.idpes_con;
   v_data_uniao     := OLD.data_uniao;
   v_data_obito     := OLD.data_obito;
   v_nacionalidade    := OLD.nacionalidade;
   v_idpais_estrangeiro   := OLD.idpais_estrangeiro;
   v_data_chegada_brasil  := OLD.data_chegada_brasil;
   v_idmun_nascimento   := OLD.idmun_nascimento;
   v_ultima_empresa   := OLD.ultima_empresa;
   v_idocup     := OLD.idocup;
   v_nome_mae     := OLD.nome_mae;
   v_nome_pai     := OLD.nome_pai;
   v_nome_conjuge   := OLD.nome_conjuge;
   v_nome_responsavel   := OLD.nome_responsavel;
   v_justificativa_provisorio := OLD.justificativa_provisorio;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FISICA
      INSERT INTO historico.fisica
      (idpes, data_nasc, sexo, idpes_mae, idpes_pai, idpes_responsavel, idesco, ideciv, idpes_con, data_uniao, data_obito, nacionalidade, idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, ultima_empresa, idocup, nome_mae, nome_pai, nome_conjuge, nome_responsavel, justificativa_provisorio, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_data_nasc, v_sexo, v_idpes_mae, v_idpes_pai, v_idpes_responsavel, v_idesco, v_ideciv, v_idpes_con, v_data_uniao, v_data_obito, v_nacionalidade, v_idpais_estrangeiro, v_data_chegada_brasil, v_idmun_nascimento, v_ultima_empresa, v_idocup, v_nome_mae, v_nome_pai, v_nome_conjuge, v_nome_responsavel, v_justificativa_provisorio, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_fisica_cpf(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_fisica_cpf() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_cpf      numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_cpf      := OLD.cpf;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FISICA_CPF
      INSERT INTO historico.fisica_cpf
      (idpes, cpf, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_cpf, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_fone_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_fone_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_tipo     numeric;
   v_ddd      numeric;
   v_fone     numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_tipo     := OLD.tipo;
   v_ddd      := OLD.ddd;
   v_fone     := OLD.fone;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FONE_PESSOA
      INSERT INTO historico.fone_pessoa
      (idpes, tipo, ddd, fone, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_tipo, v_ddd, v_fone, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_funcionario(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_funcionario() RETURNS "trigger"
    AS $$
DECLARE
   v_matricula      numeric;
   v_idins      numeric;
   v_idset      numeric;
   v_idpes      numeric;
   v_situacao     char(1);
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_matricula      := OLD.matricula;
   v_idins      := OLD.idins;
   v_idset      := OLD.idset;
   v_idpes      := OLD.idpes;
   v_situacao     := OLD.situacao;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA FUNCIONARIO
      INSERT INTO historico.funcionario
      (matricula, idins, idset, idpes, situacao, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_matricula, v_idins, v_idset, v_idpes, v_situacao, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_juridica(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_juridica() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes      numeric;
   v_cnpj     numeric;
   v_fantasia     varchar;
   v_insc_estadual    numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_cnpj     := OLD.cnpj;
   v_fantasia     := OLD.fantasia;
   v_insc_estadual    := OLD.insc_estadual;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA JURIDICA
      INSERT INTO historico.juridica
      (idpes, cnpj, fantasia, insc_estadual, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_cnpj, v_fantasia, v_insc_estadual, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_logradouro(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_logradouro() RETURNS "trigger"
    AS $$
DECLARE
   v_idlog      numeric;
   v_idtlog     varchar;
   v_nome     varchar;
   v_idmun      numeric;
   v_ident_oficial    char(1);
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idlog      := OLD.idlog;
   v_idtlog     := OLD.idtlog;
   v_nome     := OLD.nome;
   v_idmun      := OLD.idmun;
   v_ident_oficial    := OLD.ident_oficial;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA LOGRADOURO
      INSERT INTO historico.logradouro
      (idlog, idtlog, nome, idmun, ident_oficial, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idlog, v_idtlog, v_nome, v_idmun, v_ident_oficial, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_municipio(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_municipio() RETURNS "trigger"
    AS $$
DECLARE
   v_idmun      numeric;
   v_sigla_uf     char(2);
   v_nome     varchar;
   v_area_km2     numeric;
   v_idmreg     numeric;
   v_idasmun      numeric;
   v_cod_ibge     numeric;
   v_geom     TEXT;
   v_tipo     char(1);
   v_idmun_pai      numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idmun      := OLD.idmun;
   v_sigla_uf     := OLD.sigla_uf;
   v_nome     := OLD.nome;
   v_area_km2     := OLD.area_km2;
   v_idmreg     := OLD.idmreg;
   v_idasmun      := OLD.idasmun;
   v_cod_ibge     := OLD.cod_ibge;
   v_geom     := OLD.geom;
   v_tipo     := OLD.tipo;
   v_idmun_pai      := OLD.idmun_pai;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA MUNICIPIO
      INSERT INTO historico.municipio
      (idmun, sigla_uf, nome, area_km2, idmreg, idasmun, cod_ibge, geom, tipo, idmun_pai, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idmun, v_sigla_uf, v_nome, v_area_km2, v_idmreg, v_idasmun, v_cod_ibge, v_geom, v_tipo, v_idmun_pai, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_pessoa(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_pessoa() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes    numeric;
   v_nome   TEXT;
   v_idpes_cad    numeric;
   v_data_cad   timestamp;
   v_url    TEXT;
   v_tipo   char;
   v_idpes_rev    numeric;
   v_data_rev   timestamp;
   v_email    TEXT;
   v_situacao   char;
   v_origem_gravacao  char;
   v_idsis_rev    numeric;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes    := OLD.idpes;
   v_nome   := OLD.nome;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_url    := OLD.url;
   v_tipo   := OLD.tipo;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_email    := OLD.email;
   v_situacao   := OLD.situacao;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;
      
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA PESSOA
      INSERT INTO historico.pessoa
      (idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, email, situacao, origem_gravacao, idsis_rev, idsis_cad, operacao) VALUES 
      (v_idpes, v_nome, v_idpes_cad, v_data_cad, v_url, v_tipo, v_idpes_rev, v_data_rev, v_email, v_situacao, v_origem_gravacao, v_idsis_rev, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


--
-- Name: fcn_grava_historico_socio(); Type: FUNCTION; Schema: historico; Owner: -
--

CREATE FUNCTION fcn_grava_historico_socio() RETURNS "trigger"
    AS $$
DECLARE
   v_idpes_juridica   numeric;
   v_idpes_fisica   numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes_juridica   := OLD.idpes_juridica;
   v_idpes_fisica   := OLD.idpes_fisica;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA SOCIO
      INSERT INTO historico.socio
      (idpes_juridica, idpes_fisica, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes_juridica, v_idpes_fisica, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $$
    LANGUAGE plpgsql;


SET search_path = pmieducar, pg_catalog;

--
-- Name: fcn_aft_update(); Type: FUNCTION; Schema: pmieducar; Owner: -
--

CREATE FUNCTION fcn_aft_update() RETURNS "trigger"
    AS $$
DECLARE
  nm_tabela   varchar(255);
  alteracoes    text;
  data_cadastro   TIMESTAMP;
  v_insercao    int2;
  
  
  BEGIN
    v_insercao    := 0;
    nm_tabela   := TG_RELNAME;
    alteracoes    := NEW;
    data_cadastro   := CURRENT_TIMESTAMP;
    IF TG_OP = 'INSERT' THEN
      v_insercao := 1;
    END IF;
    insert into pmieducar.historico_educar (tabela, alteracao, data, insercao) values (nm_tabela, alteracoes, data_cadastro, v_insercao);
  RETURN NEW;
END; $$
    LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- Name: fcn_aft_logradouro_fonetiza(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_aft_logradouro_fonetiza() RETURNS "trigger"
    AS $$
   DECLARE
    v_idlog    bigint;
    v_nome_n   text;
    v_nome_v   text;
    v_fonema   text;
    v_reg_fon  record;
   BEGIN
    IF TG_OP = 'INSERT' THEN
     v_idlog  := NEW.idlog;
     v_nome_n := NEW.nome;
     FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO public.logradouro_fonetico (fonema,idlog) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idlog)||');';
     END LOOP;
    ELSIF TG_OP = 'UPDATE' THEN
     v_idlog  := NEW.idlog;
     v_nome_n := NEW.nome;
     v_nome_v := OLD.nome;
     IF v_nome_n <> v_nome_v THEN
      EXECUTE 'DELETE FROM public.logradouro_fonetico WHERE idlog = '||quote_literal(v_idlog)||';';
      FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
       v_fonema := v_reg_fon.fcn_fonetiza;
       EXECUTE 'INSERT INTO public.logradouro_fonetico (fonema,idlog) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idlog)||');';
      END LOOP;
     END IF;
    END IF;
    RETURN NEW;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_aft_pessoa_fonetiza(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_aft_pessoa_fonetiza() RETURNS "trigger"
    AS $$
   DECLARE
    v_idpes    bigint;
    v_nome_n   text;
    v_nome_v   text;
    v_fonema   text;
    v_reg_fon  record;
   BEGIN
    IF TG_OP = 'INSERT' THEN
     v_idpes  := NEW.idpes;
     v_nome_n := NEW.nome;
     FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
     END LOOP;
    ELSIF TG_OP = 'UPDATE' THEN
     v_idpes  := NEW.idpes;
     v_nome_n := NEW.nome;
     v_nome_v := OLD.nome;
     IF v_nome_n <> v_nome_v THEN
      EXECUTE 'DELETE FROM cadastro.pessoa_fonetico WHERE idpes = '||quote_literal(v_idpes)||';';
      FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
       v_fonema := v_reg_fon.fcn_fonetiza;
       EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES
 ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
      END LOOP;
     END IF;
    END IF;
    RETURN NEW;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_bef_ins_fisica(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_bef_ins_fisica() RETURNS "trigger"
    AS $$
   DECLARE
    v_idpes    cadastro.fisica.idpes%TYPE;
    v_contador integer;
   BEGIN
    SELECT INTO v_contador count(idpes) from cadastro.juridica where idpes = NEW.idpes;
    IF v_contador = 1 THEN
     RAISE EXCEPTION 'O Identificador % já está cadastrado como Pessoa Jurídica', NEW.idpes;
    END IF;
    RETURN NEW;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_bef_ins_juridica(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_bef_ins_juridica() RETURNS "trigger"
    AS $$
   DECLARE
    v_idpes    cadastro.juridica.idpes%TYPE;
    v_contador integer;
   BEGIN
    SELECT INTO v_contador count(idpes) from cadastro.fisica where idpes = NEW.idpes;
    IF v_contador = 1 THEN
     RAISE EXCEPTION 'O Identificador % já está cadastrado como Pessoa Física', NEW.idpes;
    END IF;
    RETURN NEW;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_bef_logradouro_fonetiza(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_bef_logradouro_fonetiza() RETURNS "trigger"
    AS $$
   DECLARE
    v_idlog    bigint;
   BEGIN
    v_idlog := OLD.idlog;
    EXECUTE 'DELETE FROM public.logradouro_fonetico WHERE idlog = '||quote_literal(v_idlog)||';';
    RETURN OLD;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_bef_pessoa_fonetiza(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_bef_pessoa_fonetiza() RETURNS "trigger"
    AS $$
   DECLARE
    v_idpes    bigint;
   BEGIN
    v_idpes := OLD.idpes;
    EXECUTE 'DELETE FROM cadastro.pessoa_fonetico WHERE idpes = '||quote_literal(v_idpes)||';';
    RETURN OLD;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_compara_nome_pessoa_fonetica(text, numeric); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_compara_nome_pessoa_fonetica(text, numeric) RETURNS integer
    AS $_$
DECLARE
  v_nome_parametro      ALIAS FOR $1;
  v_idpes_parametro     ALIAS FOR $2;
  v_nome_pessoa_1       text;
  v_nome_pessoa_2       text;
  v_registro        record;
  v_nome_primeiro_ultimo_pessoa_1   text;
  v_nome_primeiro_ultimo_pessoa_2   text;
  v_cont     integer;
  v_fonema   text;
  v_comando  text;
  
  BEGIN
  
  -- obter o nome da pessoa referente ao IDPES passado como parametro
  v_comando := 'SELECT nome FROM cadastro.pessoa WHERE idpes = '||quote_literal(v_idpes_parametro)||';';
  FOR v_registro IN EXECUTE v_comando LOOP
    v_nome_pessoa_1 := v_registro.nome;
  END LOOP;
  
  v_nome_pessoa_2 := v_nome_parametro;
  
  v_nome_primeiro_ultimo_pessoa_1 := '';
  v_nome_primeiro_ultimo_pessoa_2 := '';
  v_cont := 0;
  
  -- primeiro e último nome da pessoa com fonética
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_pessoa_1)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;
    
    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa_1 := v_nome_primeiro_ultimo_pessoa_1 || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa_1 := v_nome_primeiro_ultimo_pessoa_1 || v_fonema;
  END LOOP;
  
  v_cont := 0;
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_pessoa_2)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;
    
    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa_2 := v_nome_primeiro_ultimo_pessoa_2 || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa_2 := v_nome_primeiro_ultimo_pessoa_2 || v_fonema;
  END LOOP;
  
  IF v_nome_primeiro_ultimo_pessoa_1 = v_nome_primeiro_ultimo_pessoa_2 THEN
    RETURN 1;
  ELSE
    RETURN 0;
  END IF;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_cons_log_fonetica(text, bigint); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_cons_log_fonetica(text, bigint) RETURNS SETOF typ_idlog
    AS $_$
   DECLARE
    v_texto    ALIAS FOR $1;
    v_idmun    ALIAS FOR $2;
    v_fonema   text;
    v_comando  text;
    v_idlog    bigint;
    v_reg_fon  record;
    v_cont     integer;
    retorno    typ_idlog%ROWTYPE;
   BEGIN
    v_cont := 0;
    v_comando := 'select public.logradouro_fonetico.idlog from public.logradouro_fonetico, public.logradouro where (fonema = ';
    FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_texto) LOOP v_cont := v_cont + 1;
     v_fonema := v_reg_fon.fcn_fonetiza;
     IF v_cont > 1 THEN
      v_comando := v_comando||' or fonema = ';
     END IF;
     v_comando := v_comando||quote_literal(v_fonema);
    END LOOP;
    v_comando := v_comando||') AND public.logradouro.idlog = public.logradouro_fonetico.idlog';
    v_comando := v_comando||' AND public.logradouro.idmun = '||v_idmun;
    v_comando := v_comando||' group by public.logradouro_fonetico.idlog having count(fonema) = '||quote_literal(v_cont)||';';
    FOR retorno IN EXECUTE v_comando LOOP
     RETURN NEXT retorno;
    END LOOP;
    RETURN;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_consulta_fonetica(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_consulta_fonetica(text) RETURNS SETOF typ_idpes
    AS $_$
   DECLARE
    v_texto    ALIAS FOR $1;
    v_fonema   text;
    v_comando  text;
    v_idpes    bigint;
    v_reg_fon  record;
    v_cont     integer;
    retorno typ_idpes%ROWTYPE;
   BEGIN
    v_cont := 0;
    v_comando := 'select idpes from cadastro.pessoa_fonetico where fonema = ';
    FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_texto) LOOP
     v_cont := v_cont + 1;
     v_fonema := v_reg_fon.fcn_fonetiza;
     IF v_cont > 1 THEN
      v_comando := v_comando||' or fonema = ';
     END IF;
     v_comando := v_comando||quote_literal(v_fonema);
    END LOOP;
    v_comando := v_comando||' group by idpes having count(fonema) = '||quote_literal(v_cont)||';';
    FOR retorno IN EXECUTE v_comando LOOP
     RETURN NEXT retorno;
    END LOOP;
    RETURN;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_endereco_externo(integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_delete_endereco_externo(integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_idpes ALIAS for $1;
  v_tipo ALIAS for $2;
  
BEGIN
  -- Deleta dados da tabela endereco_externo
  DELETE FROM cadastro.endereco_externo WHERE idpes = v_idpes AND tipo = v_tipo;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_endereco_pessoa(integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_delete_endereco_pessoa(integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_idpes ALIAS for $1;
  v_tipo ALIAS for $2;
  
BEGIN
  -- Deleta dados da tabela endereco_pessoa
  DELETE FROM cadastro.endereco_pessoa WHERE idpes = v_idpes AND tipo = v_tipo;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_fone_pessoa(integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_delete_fone_pessoa(integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  
BEGIN
  -- Deleta dados da tabela fone_pessoa
  DELETE FROM cadastro.fone_pessoa WHERE idpes = v_id_pes;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_delete_funcionario(integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_delete_funcionario(integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;
  
BEGIN
  -- Deleta dados da tabela funcionário
  DELETE FROM cadastro.funcionario WHERE matricula = v_matricula AND idins = v_id_ins;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_dia_util(date, date); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_dia_util(date, date) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_dt_ini ALIAS for $1;
  v_dt_fim ALIAS for $2;
  v_dt_ini_x date;
  v_qtde integer;
BEGIN
  v_qtde := 0;
  v_dt_ini_x := v_dt_ini;
  WHILE v_dt_ini_x <= v_dt_fim LOOP
    IF to_char(v_dt_ini_x,'D') NOT IN ('1','7') THEN
      IF NOT EXISTS(SELECT idfer
             from servicos.feriado
             WHERE to_char(data,'DD/MM/YYYY') =
             to_char(v_dt_ini_x,'DD/MM/YYYY')) THEN
        v_qtde := v_qtde + 1;
      END IF;
    END IF;
    v_dt_ini_x := v_dt_ini_x + interval '1 day';
  END LOOP;
  RETURN v_qtde;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_fonetiza(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_fonetiza(text) RETURNS SETOF text
    AS $_$
   DECLARE
    v_array         ALIAS FOR $1;
    orig            text := '';
    dest            text := '';
    ori             integer := 1;
    v_campo         integer;
   BEGIN
    orig := public.fcn_upper_nrm(v_array)||'  ';
    FOR ori IN 1..152 LOOP
     IF substr(orig, ori, 1) = ' ' THEN
      IF substr(orig, ori + 1, 1) = ' ' THEN
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN NEXT trim(dest);
        dest := '';
       END IF;
       ori := 152;
       EXIT;
      ELSE
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN NEXT trim(dest);
        dest := '';
       END IF;
       ori := ori + 1;
      END IF;
     ELSIF substr(orig,ori + 1,1) = '-' OR
           substr(orig,ori + 1,1) = '/' THEN
      IF substr(orig,ori,1) <> ' ' AND
         substr(orig,ori + 2,1) <> ' ' THEN
       orig = substr(orig,1,ori)||' '||substr(orig,ori + 1,42);
      END IF;
     END IF;
     -- Numero
     IF substr(orig,ori,1) >= '0' and
        substr(orig,ori,1) <= '9' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra Igual
     ELSIF substr(orig,ori,1) = substr(orig,ori + 1,1) THEN
      IF length(dest) = 1 THEN
       dest := '';
      END IF;
     -- Letras A, I ou O
     ELSIF substr(orig,ori,1) = 'A' OR
           substr(orig,ori,1) = 'I' OR
           substr(orig,ori,1) = 'O' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra E
     ELSIF substr(orig,ori,1) = 'E' THEN
      dest := dest||'I';
     -- Letra R
     ELSIF substr(orig,ori,1) = 'R' THEN
       dest := dest||'H';
     -- Letra S
     ELSIF substr(orig,ori,1) = 'S' THEN
      IF substr(orig,ori + 1,1) NOT IN ('A','E','I','O','U','Y') AND
         length(dest) = 0 THEN
       dest := dest||'IS';
      ELSIF substr(orig,ori + 1,1) = 'C' AND
            substr(orig,ori + 2,1) = 'H' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSIF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'KS';
       ori := ori + 1;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra N
     ELSIF substr(orig,ori,1) = 'N' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'NI';
      ELSE
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||substr(orig,ori,1);
       ELSE
        dest := dest||'M';
       END IF;
      END IF;
     -- Letra L
     ELSIF substr(orig,ori,1) = 'L' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'LI';
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'O';
      END IF;
     -- Letra D
     ELSIF substr(orig,ori,1) = 'D' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'DI';
      END IF;
     -- Letra C
     ELSIF substr(orig,ori,1) = 'C' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       IF substr(orig,ori + 2,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'KS';
        ori := ori + 1;
       END IF;
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) IN ('E','I','Y') THEN
        dest := dest||'S';
       ELSE
        dest := dest||'K';
       END IF;
      ELSE
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      END IF;
     -- Letra M
     ELSIF substr(orig,ori,1) = 'M' THEN
      IF substr(orig,ori + 1,1) = 'N' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra T
     ELSIF substr(orig,ori,1) = 'T' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'TI';
      END IF;
     -- Letra U
     ELSIF substr(orig,ori,1) = 'U' THEN
      dest := dest||'O';
     -- Letra V
     ELSIF substr(orig,ori,1) = 'V' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'VI';
      END IF;
     -- Letra G
     ELSIF substr(orig,ori,1) = 'G' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        dest := dest||'J';
        ori := ori + 1;
       ELSE
        dest := dest||'J';
       END IF;
      ELSE
       dest := dest||'JI';
      END IF;
     -- Letra B
     ELSIF substr(orig,ori,1) = 'B' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'BI';
      END IF;
     -- Letra P
     ELSIF substr(orig,ori,1) = 'P' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       IF substr(orig,ori + 1,1) = 'H' THEN
        dest := dest||'F';
       ELSE
        dest := dest||'PI';
       END IF;
      END IF;
     -- Letra Z
     ELSIF substr(orig,ori,1) = 'Z' THEN
       dest := dest||'S';
     -- Letra F
     ELSIF substr(orig,ori,1) = 'F' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'FI';
      END IF;
     -- Letra J
     ELSIF substr(orig,ori,1) = 'J' THEN
      dest := dest||'J';
     -- Letra K
     ELSIF substr(orig,ori,1) = 'K' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'KI';
      END IF;
     -- Letra Y
     ELSIF substr(orig,ori,1) = 'Y' THEN
      dest := dest||'I';
     -- Letra W
     ELSIF substr(orig,ori,1) = 'W' THEN
      IF ori = 1 THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'VI';
       END IF;
      ELSIF substr(orig,ori - 1,1) IN ('E','I') THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'O';
       END IF;
      ELSE
       dest := dest||'V';
      END IF;
     -- Letra Q
     ELSIF substr(orig,ori,1) = 'Q' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y',' ') THEN
       dest := dest||'K';
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        ori := ori + 1;
       END IF;
      ELSE
       dest := dest||'QI';
      END IF;
     -- Letra X
     ELSIF substr(orig,ori,1) = 'X' THEN
      dest := dest||'KS';
     END IF;
    END LOOP;
    dest := NULL;
    RETURN;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_fonetiza_logr_geral(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_fonetiza_logr_geral() RETURNS text
    AS $$
   DECLARE
    v_fonema   text;
    v_nomlog   text;
    v_idlog    bigint;
    v_reg_log  record;
    v_reg_fon  record;
    v_cont     integer;
   BEGIN
    FOR v_reg_log IN SELECT idlog, nome FROM public.logradouro LOOP
     v_nomlog := v_reg_log.nome;
     v_idlog  := v_reg_log.idlog;
     FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_nomlog) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO public.logradouro_fonetico (fonema,idlog) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idlog)||');';
     END LOOP;
    END LOOP;
    SELECT count(idlog) INTO v_cont FROM public.logradouro_fonetico;
    v_fonema := 'Foram gravados '||to_char(v_cont,'9999999')||' registros em logradouro_fonetico';
    RETURN v_fonema;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_fonetiza_palavra(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_fonetiza_palavra(text) RETURNS text
    AS $_$
DECLARE
    v_array         ALIAS FOR $1;
    orig            text := '';
    dest            text := '';
    ori             integer := 1;
    v_campo         integer;
   BEGIN
    orig := public.fcn_upper_nrm(v_array)||'  ';
    FOR ori IN 1..152 LOOP
     IF substr(orig, ori, 1) = ' ' THEN
      IF substr(orig, ori + 1, 1) = ' ' THEN
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN trim(dest);
        dest := '';
       END IF;
       ori := 152;
       EXIT;
      ELSE
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN trim(dest);
        dest := '';
       END IF;
       ori := ori + 1;
      END IF;
     ELSIF substr(orig,ori + 1,1) = '-' OR
           substr(orig,ori + 1,1) = '/' THEN
      IF substr(orig,ori,1) <> ' ' AND
         substr(orig,ori + 2,1) <> ' ' THEN
       orig = substr(orig,1,ori)||' '||substr(orig,ori + 1,42);
      END IF;
     END IF;
     -- Numero
     IF substr(orig,ori,1) >= '0' and
        substr(orig,ori,1) <= '9' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra Igual
     ELSIF substr(orig,ori,1) = substr(orig,ori + 1,1) THEN
      IF length(dest) = 1 THEN
       dest := '';
      END IF;
     -- Letras A, I ou O
     ELSIF substr(orig,ori,1) = 'A' OR
           substr(orig,ori,1) = 'I' OR
           substr(orig,ori,1) = 'O' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra E
     ELSIF substr(orig,ori,1) = 'E' THEN
      dest := dest||'I';
     -- Letra R
     ELSIF substr(orig,ori,1) = 'R' THEN
       dest := dest||'H';
     -- Letra S
     ELSIF substr(orig,ori,1) = 'S' THEN
      IF substr(orig,ori + 1,1) NOT IN ('A','E','I','O','U','Y') AND
         length(dest) = 0 THEN
       dest := dest||'IS';
      ELSIF substr(orig,ori + 1,1) = 'C' AND
            substr(orig,ori + 2,1) = 'H' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSIF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'KS';
       ori := ori + 1;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra N
     ELSIF substr(orig,ori,1) = 'N' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'NI';
      ELSE
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||substr(orig,ori,1);
       ELSE
        dest := dest||'M';
       END IF;
      END IF;
     -- Letra L
     ELSIF substr(orig,ori,1) = 'L' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'LI';
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'O';
      END IF;
     -- Letra D
     ELSIF substr(orig,ori,1) = 'D' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'DI';
      END IF;
     -- Letra C
     ELSIF substr(orig,ori,1) = 'C' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       IF substr(orig,ori + 2,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'KS';
        ori := ori + 1;
       END IF;
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) IN ('E','I','Y') THEN
        dest := dest||'S';
       ELSE
        dest := dest||'K';
       END IF;
      ELSE
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      END IF;
     -- Letra M
     ELSIF substr(orig,ori,1) = 'M' THEN
      IF substr(orig,ori + 1,1) = 'N' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra T
     ELSIF substr(orig,ori,1) = 'T' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'TI';
      END IF;
     -- Letra U
     ELSIF substr(orig,ori,1) = 'U' THEN
      dest := dest||'O';
     -- Letra V
     ELSIF substr(orig,ori,1) = 'V' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'VI';
      END IF;
     -- Letra G
     ELSIF substr(orig,ori,1) = 'G' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        dest := dest||'J';
        ori := ori + 1;
       ELSE
        dest := dest||'J';
       END IF;
      ELSE
       dest := dest||'JI';
      END IF;
     -- Letra B
     ELSIF substr(orig,ori,1) = 'B' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'BI';
      END IF;
     -- Letra P
     ELSIF substr(orig,ori,1) = 'P' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       IF substr(orig,ori + 1,1) = 'H' THEN
        dest := dest||'F';
       ELSE
        dest := dest||'PI';
       END IF;
      END IF;
     -- Letra Z
     ELSIF substr(orig,ori,1) = 'Z' THEN
       dest := dest||'S';
     -- Letra F
     ELSIF substr(orig,ori,1) = 'F' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'FI';
      END IF;
     -- Letra J
     ELSIF substr(orig,ori,1) = 'J' THEN
      dest := dest||'J';
     -- Letra K
     ELSIF substr(orig,ori,1) = 'K' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'KI';
      END IF;
     -- Letra Y
     ELSIF substr(orig,ori,1) = 'Y' THEN
      dest := dest||'I';
     -- Letra W
     ELSIF substr(orig,ori,1) = 'W' THEN
      IF ori = 1 THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'VI';
       END IF;
      ELSIF substr(orig,ori - 1,1) IN ('E','I') THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'O';
       END IF;
      ELSE
       dest := dest||'V';
      END IF;
     -- Letra Q
     ELSIF substr(orig,ori,1) = 'Q' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y',' ') THEN
       dest := dest||'K';
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        ori := ori + 1;
       END IF;
      ELSE
       dest := dest||'QI';
      END IF;
     -- Letra X
     ELSIF substr(orig,ori,1) = 'X' THEN
      dest := dest||'KS';
     END IF;
    END LOOP;
    dest := NULL;
    RETURN;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_fonetiza_pessoa_geral(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_fonetiza_pessoa_geral() RETURNS text
    AS $$
   DECLARE
    v_fonema   text;
    v_nome     text;
    v_idpes    bigint;
    v_reg_pes  record;
    v_reg_fon  record;
    v_cont     integer;
   BEGIN
    FOR v_reg_pes IN SELECT idpes, nome FROM cadastro.pessoa LOOP
     v_nome  := v_reg_pes.nome;
     v_idpes := v_reg_pes.idpes;
     FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_nome) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
     END LOOP;
    END LOOP;
    SELECT count(idpes) INTO v_cont FROM cadastro.pessoa_fonetico;
    v_fonema := 'Foram gravados '||to_char(v_cont,'999999')||' registros em pessoa_fonetico';
    RETURN v_fonema;
   END;
  $$
    LANGUAGE plpgsql;


--
-- Name: fcn_fonetiza_primeiro_ultimo_nome(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_fonetiza_primeiro_ultimo_nome(text) RETURNS text
    AS $_$
DECLARE
  v_nome_parametro    ALIAS FOR $1;
  v_registro      record;
  v_nome_primeiro_ultimo_pessoa text;
  v_cont          integer;
  v_fonema        text;
  BEGIN
  v_nome_primeiro_ultimo_pessoa := '';
  v_cont := 0;
  -- primeiro e último nome da pessoa com fonética
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_parametro)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;
    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa := v_nome_primeiro_ultimo_pessoa || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa := v_nome_primeiro_ultimo_pessoa || v_fonema;
  END LOOP;
  RETURN v_nome_primeiro_ultimo_pessoa;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_rg ALIAS for $2;  
  v_orgao_exp_rg ALIAS for $3;
  v_data_exp_rg ALIAS for $4;
  v_sigla_uf_exp_rg ALIAS for $5;
      v_tipo_cert_civil ALIAS for $6;
      v_num_termo ALIAS for $7;
  v_num_livro ALIAS for $8;
  v_num_folha ALIAS for $9;
      v_data_emissao_cert_civil ALIAS for $10;
      v_sigla_uf_cert_civil ALIAS for $11;
  v_sigla_uf_cart_trabalho ALIAS for $12;
  v_cartorio_cert_civil ALIAS for $13;
  v_num_cart_trabalho ALIAS for $14;
      v_serie_cart_trabalho ALIAS for $15;
      v_data_emissao_cart_trabalho ALIAS for $16;
  v_num_tit_eleitor ALIAS for $17;  
  v_zona_tit_eleitor ALIAS for $18;
  v_secao_tit_eleitor ALIAS for $19;
  v_origem_gravacao ALIAS for $20;
  v_idpes_cad ALIAS for $21;
  v_idsis_cad ALIAS for $22;
    
    -- Outras variáveis
      v_rg_aux varchar(10);
      v_orgao_exp_rg_aux varchar(10);
      v_sigla_uf_exp_rg_aux varchar(2);
  v_tipo_cert_civil_aux integer;
  v_num_termo_aux integer;
  v_num_livro_aux integer;
  v_num_folha_aux integer;
      v_sigla_uf_cert_civil_aux varchar(2);
  v_sigla_uf_cart_trabalho_aux varchar(2);
      v_cartorio_cert_civil_aux varchar(150);
      v_num_cart_trabalho_aux integer;
      v_serie_cart_trabalho_aux integer;
      v_num_tit_eleitor_aux varchar(13);
      v_zona_tit_eleitor_aux integer;
      v_secao_tit_eleitor_aux integer;
BEGIN
    IF v_rg = '' THEN
        v_rg_aux := NULL;
    ELSE
        v_rg_aux := v_rg;
    END IF;
    IF v_orgao_exp_rg = '' THEN
        v_orgao_exp_rg_aux := NULL;
    ELSE
        v_orgao_exp_rg_aux := v_orgao_exp_rg;
    END IF;
    IF v_sigla_uf_exp_rg = '' THEN
        v_sigla_uf_exp_rg_aux := NULL;
    ELSE
        v_sigla_uf_exp_rg_aux := v_sigla_uf_exp_rg;
    END IF;
    IF v_tipo_cert_civil = 0 THEN
        v_tipo_cert_civil_aux := NULL;
    ELSE
        v_tipo_cert_civil_aux := v_tipo_cert_civil;
    END IF;
    IF v_num_termo = 0 THEN
        v_num_termo_aux := NULL;
    ELSE
        v_num_termo_aux := v_num_termo;
    END IF;
    IF v_num_livro = 0 THEN
        v_num_livro_aux := NULL;
    ELSE
        v_num_livro_aux := v_num_livro;
    END IF;
    IF v_num_folha = 0 THEN
        v_num_folha_aux := NULL;
    ELSE
        v_num_folha_aux := v_num_folha;
    END IF;
    IF v_sigla_uf_cert_civil = '' THEN
        v_sigla_uf_cert_civil_aux := NULL;
    ELSE
        v_sigla_uf_cert_civil_aux := v_sigla_uf_cert_civil;
    END IF;
    IF v_sigla_uf_cart_trabalho = '' THEN
        v_sigla_uf_cart_trabalho_aux := NULL;
    ELSE
        v_sigla_uf_cart_trabalho_aux := v_sigla_uf_cart_trabalho;
    END IF;
    IF v_cartorio_cert_civil = '' THEN
        v_cartorio_cert_civil_aux := NULL;
    ELSE
        v_cartorio_cert_civil_aux := v_cartorio_cert_civil;
    END IF;
    IF v_num_cart_trabalho = 0 THEN
        v_num_cart_trabalho_aux := NULL;
    ELSE
        v_num_cart_trabalho_aux := v_num_cart_trabalho;
    END IF;
    IF v_serie_cart_trabalho = 0 THEN
        v_serie_cart_trabalho_aux := NULL;
    ELSE
        v_serie_cart_trabalho_aux := v_serie_cart_trabalho;
    END IF;
    IF v_num_tit_eleitor = '' THEN
        v_num_tit_eleitor_aux := NULL;
    ELSE
        v_num_tit_eleitor_aux := v_num_tit_eleitor;
    END IF;
    IF v_zona_tit_eleitor = 0 THEN
        v_zona_tit_eleitor_aux := NULL;
    ELSE
        v_zona_tit_eleitor_aux := v_zona_tit_eleitor;
    END IF;
    IF v_secao_tit_eleitor = 0 THEN
        v_secao_tit_eleitor_aux := NULL;
    ELSE
        v_secao_tit_eleitor_aux := v_secao_tit_eleitor;
    END IF;
 
  -- Insere dados na tabela funcionário
    INSERT INTO cadastro.documento (idpes, rg, idorg_exp_rg, 
                                data_exp_rg, sigla_uf_exp_rg,
                                tipo_cert_civil, num_termo,
                                num_livro, num_folha,
                                data_emissao_cert_civil, sigla_uf_cert_civil,
                                sigla_uf_cart_trabalho, cartorio_cert_civil,
                                num_cart_trabalho, serie_cart_trabalho,
                                data_emissao_cart_trabalho, num_tit_eleitor,                                              zona_tit_eleitor, secao_tit_eleitor, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES(v_id_pes, to_number(v_rg_aux,9999999999), to_number(v_orgao_exp_rg_aux,9999999999),
           to_date(v_data_exp_rg,'DD/MM/YYYY'), v_sigla_uf_exp_rg_aux,
           v_tipo_cert_civil_aux, v_num_termo_aux,
           v_num_livro_aux, v_num_folha_aux,
           to_date(v_data_emissao_cert_civil,'DD/MM/YYYY'), v_sigla_uf_cert_civil_aux,
           v_sigla_uf_cart_trabalho_aux, v_cartorio_cert_civil_aux,
           v_num_cart_trabalho_aux, v_serie_cart_trabalho_aux,
           to_date(v_data_emissao_cart_trabalho,'DD/MM/YYYY'), to_number(v_num_tit_eleitor_aux,9999999999999),
           v_zona_tit_eleitor_aux, v_secao_tit_eleitor_aux, v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2; 
  v_sigla_uf ALIAS for $3;
  v_idtlog ALIAS for $4;
    v_logradouro ALIAS for $5;
    v_numero ALIAS for $6;
    v_letra ALIAS for $7;
    v_complemento ALIAS for $8;
    v_bairro ALIAS for $9;
    v_cep ALIAS for $10;
    v_cidade ALIAS for $11;
    v_reside_desde ALIAS for $12;
    v_origem_gravacao ALIAS for $13;
    v_idpes_cad ALIAS for $14;
    v_idsis_cad ALIAS for $15;
  
BEGIN
  -- Atualiza dados na tabela endereco_externo
    INSERT INTO cadastro.endereco_externo(idpes, tipo, sigla_uf, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, reside_desde, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes, v_tipo, v_sigla_uf, v_idtlog, public.fcn_upper(v_logradouro), v_numero, public.fcn_upper(v_letra), public.fcn_upper(v_complemento), public.fcn_upper(v_bairro), v_cep, public.fcn_upper(v_cidade), to_date(v_reside_desde,'DD/MM/YYYY'), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
 
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_cep ALIAS for $3;
  v_idlog ALIAS for $4;
  v_idbai ALIAS for $5;
  v_numero ALIAS for $6;
  v_letra ALIAS for $7;
  v_complemento ALIAS for $8;
  v_reside_desde ALIAS for $9;
  v_origem_gravacao ALIAS for $10;
      v_idpes_cad ALIAS for $11;
      v_idsis_cad ALIAS for $12;
BEGIN
  -- Insere dados na tabela endereco_pessoa
    INSERT INTO cadastro.endereco_pessoa (idpes,tipo,cep,idlog,idbai,numero,letra,complemento,reside_desde, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
  VALUES(v_id_pes,v_tipo,v_cep,v_idlog,v_idbai,v_numero,public.fcn_upper(v_letra),public.fcn_upper(v_complemento),to_date(v_reside_desde,'DD/MM/YYYY'), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_data_nasc ALIAS for $2;
  v_sexo ALIAS for $3;
  v_id_pes_mae ALIAS for $4;
      v_id_pes_pai ALIAS for $5;
      v_id_pes_responsavel ALIAS for $6;
  v_id_esco ALIAS for $7;
  v_id_eciv ALIAS for $8;
  v_id_pes_con ALIAS for $9;
      v_data_uniao ALIAS for $10;
      v_data_obito ALIAS for $11;
  v_nacionalidade ALIAS for $12;
  v_id_pais_estrangeiro ALIAS for $13;
  v_data_chegada ALIAS for $14;
      v_id_mun_nascimento ALIAS for $15;
      v_ultima_empresa ALIAS for $16;
  v_id_ocup ALIAS for $17;
  v_nome_mae ALIAS for $18;
  v_nome_pai ALIAS for $19;
      v_nome_conjuge ALIAS for $20;
      v_nome_responsavel ALIAS for $21;
      v_justificativa_provisorio ALIAS for $22;
  v_origem_gravacao ALIAS for $23;
      v_idpes_cad ALIAS for $24;
      v_idsis_cad ALIAS for $25;
      -- Outras variáveis
      v_id_pes_mae_aux integer;
      v_id_pes_pai_aux integer;
      v_id_pes_responsavel_aux integer;
  v_id_esco_aux integer;
  v_id_eciv_aux integer;
  v_id_pes_con_aux integer;
      v_nacionalidade_aux integer;
  v_id_pais_estrangeiro_aux integer;
      v_id_mun_nascimento_aux integer;
      v_id_ocup_aux integer;
      v_sexo_aux text;
BEGIN
    IF v_id_pes_mae = 0 THEN
        v_id_pes_mae_aux := NULL;
    ELSE
        v_id_pes_mae_aux := v_id_pes_mae;
    END IF;
    IF v_id_pes_pai = 0 THEN
        v_id_pes_pai_aux := NULL;
    ELSE
        v_id_pes_pai_aux := v_id_pes_pai;
    END IF;
    IF v_id_pes_responsavel = 0 THEN
        v_id_pes_responsavel_aux := NULL;
    ELSE
        v_id_pes_responsavel_aux := v_id_pes_responsavel;
    END IF;
    IF v_id_esco = 0 THEN
        v_id_esco_aux := NULL;
    ELSE
        v_id_esco_aux := v_id_esco;
    END IF;
    IF v_id_eciv = 0 THEN
        v_id_eciv_aux := NULL;
    ELSE
        v_id_eciv_aux := v_id_eciv;
    END IF;
    IF v_id_pes_con = 0 THEN
        v_id_pes_con_aux := NULL;
    ELSE
        v_id_pes_con_aux := v_id_pes_con;
    END IF;
    IF v_nacionalidade = 0 THEN
        v_nacionalidade_aux := NULL;
    ELSE
        v_nacionalidade_aux := v_nacionalidade;
    END IF;
    IF v_id_pais_estrangeiro = 0 THEN
        v_id_pais_estrangeiro_aux := NULL;
    ELSE
        v_id_pais_estrangeiro_aux := v_id_pais_estrangeiro;
    END IF;
    IF v_id_mun_nascimento = 0 THEN
        v_id_mun_nascimento_aux := NULL;
    ELSE
        v_id_mun_nascimento_aux := v_id_mun_nascimento;
    END IF;
    IF v_id_ocup = 0 THEN
        v_id_ocup_aux := NULL;
    ELSE
        v_id_ocup_aux := v_id_ocup;
    END IF;
    IF TRIM(v_sexo) = '' THEN
        v_sexo_aux := NULL;
    ELSE
        v_sexo_aux := public.fcn_upper(v_sexo);
    END IF;
  -- Insere dados na tabela funcionário
    INSERT INTO cadastro.fisica (idpes, data_nasc, sexo, 
                                idpes_mae,idpes_pai, idpes_responsavel, 
                                idesco, ideciv, idpes_con, 
                                data_uniao, data_obito, nacionalidade,
                                idpais_estrangeiro, data_chegada_brasil, idmun_nascimento, 
                                ultima_empresa, idocup, nome_mae, 
                                nome_pai, nome_conjuge, 
                                nome_responsavel, justificativa_provisorio, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES(v_id_pes,to_date(v_data_nasc,'DD/MM/YYYY'),v_sexo_aux,
           v_id_pes_mae_aux, v_id_pes_pai_aux, v_id_pes_responsavel_aux, 
           v_id_esco_aux, v_id_eciv_aux, v_id_pes_con_aux,
           to_date(v_data_uniao,'DD/MM/YYYY'), to_date(v_data_obito,'DD/MM/YYYY'), v_nacionalidade_aux,
           v_id_pais_estrangeiro_aux, to_date(v_data_chegada,'DD/MM/YYYY'), v_id_mun_nascimento_aux,             public.fcn_upper(v_ultima_empresa), v_id_ocup_aux, public.fcn_upper(v_nome_mae),
           public.fcn_upper(v_nome_pai), public.fcn_upper(v_nome_conjuge),         
           public.fcn_upper(v_nome_responsavel), v_justificativa_provisorio, v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
 
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_fisica_cpf(integer, text, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cpf ALIAS for $2;
  v_origem_gravacao ALIAS for $3;
      v_idpes_cad ALIAS for $4;
      v_idsis_cad ALIAS for $5;
BEGIN
  INSERT INTO cadastro.fisica_cpf (idpes,cpf, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao) VALUES (v_id_pes,to_number(v_cpf,99999999999), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_fone_pessoa(integer, integer, integer, integer, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_ddd ALIAS for $3;
  v_fone ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_cad ALIAS for $6;
      v_idsis_cad ALIAS for $7;
BEGIN
  -- Insere dados na tabela fone_pessoa
  INSERT INTO cadastro.fone_pessoa (idpes,tipo,ddd,fone, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes,v_tipo,v_ddd,v_fone, v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_funcionario(integer, integer, integer, integer, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_funcionario(integer, integer, integer, integer, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;
  v_id_set ALIAS for $3;
  v_id_pes ALIAS for $4;
      v_situacao ALIAS for $5;
  v_origem_gravacao ALIAS for $6;
      v_idpes_cad ALIAS for $7;
      v_idsis_cad ALIAS for $8;
    -- Outras variáveis
    v_id_set_aux integer;
BEGIN
    IF v_id_set = 0 THEN
        v_id_set_aux := NULL;
    ELSE
        v_id_set_aux := v_id_set;
    END IF;
  -- Insere dados na tabela funcionário
    INSERT INTO cadastro.funcionario (matricula,idins,idset,idpes,situacao, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES(v_matricula,v_id_ins,v_id_set_aux,v_id_pes,public.fcn_upper(v_situacao), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_juridica(integer, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cnpj ALIAS for $2;
  v_fantasia ALIAS for $3;
  v_inscr_estadual ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_cad ALIAS for $6;
      v_idsis_cad ALIAS for $7;
  
BEGIN
  -- Insere dados na tabela juridica
    INSERT INTO cadastro.juridica (idpes,cnpj,fantasia,insc_estadual, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes,to_number(v_cnpj,99999999999999),public.fcn_upper(v_fantasia),to_number(v_inscr_estadual,9999999999), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_insert_pessoa(integer, character varying, character varying, character varying, character varying, integer, character varying, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_insert_pessoa(integer, character varying, character varying, character varying, character varying, integer, character varying, character varying, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_razao_social ALIAS for $2;
  v_url ALIAS for $3;
  v_email ALIAS for $4;
  v_situacao ALIAS for $5;
  v_id_pes_logado ALIAS for $6;
  v_tipo ALIAS for $7;
  v_origem_gravacao ALIAS for $8;
      v_idpes_cad ALIAS for $9;
      v_idsis_cad ALIAS for $10;
  
  idpes_logado integer;
BEGIN
  idpes_logado := v_id_pes_logado;
  IF (idpes_logado <= 0) THEN
    INSERT INTO cadastro.pessoa (idpes,nome,idpes_cad,data_cad,url,tipo,email,situacao,origem_gravacao, idsis_cad, operacao) VALUES (v_id_pes,public.fcn_upper(v_razao_social),NULL,CURRENT_TIMESTAMP,v_url,v_tipo,v_email,v_situacao,v_origem_gravacao, v_idsis_cad, 'I');
  ELSE
    INSERT INTO cadastro.pessoa (idpes,nome,idpes_cad,data_cad,url,tipo,email,situacao,origem_gravacao, idsis_cad, operacao) VALUES (v_id_pes,public.fcn_upper(v_razao_social),idpes_logado,CURRENT_TIMESTAMP,v_url,v_tipo,v_email,v_situacao,v_origem_gravacao, v_idsis_cad, 'I');
  END IF;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_obter_primeiro_ultimo_nome(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_obter_primeiro_ultimo_nome(text) RETURNS text
    AS $_$
DECLARE
  v_nome_parametro    ALIAS FOR $1;
  v_nome        text;
  v_primeiro_nome     text;
  v_ultimo_nome     text;
  v_posicao_espaco_primeiro_nome  integer;
  v_posicao_espaco_ultimo_nome  integer;
  v_cont        integer;
  v_fonema_junior     text;
  v_fonema_sobrinho   text;
  v_fonema_sobrinha   text;
  v_fonema_filho      text;
  v_fonema_filha      text;
  v_fonema_ultimo_nome    text;
  v_reg       record;
  v_total_caracteres    integer;
  BEGIN
  v_primeiro_nome := '';
  v_ultimo_nome := '';
  v_nome := TRIM(v_nome_parametro);
  v_total_caracteres := LENGTH(v_nome);
  -- obter somente o primeiro e o último nome das pessoas
  IF v_total_caracteres > 0 THEN
    -- retirar os espaços duplicados
    WHILE POSITION('  ' IN v_nome) > 0 LOOP
      v_nome := REPLACE(UPPER(v_nome),'  ', ' ');
    END LOOP;
    -- retirar ocorrências que devem ser ignoradas no nome
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTROS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRAS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRO', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OTS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E SM', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E S/M', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/M', ''));
    v_total_caracteres := LENGTH(v_nome);
    IF v_total_caracteres = (POSITION(' OUTRO' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRO', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRA' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTROS' IN v_nome) + 6) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTROS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRAS' IN v_nome) + 6) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRAS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OTS' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OTS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTA' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUT' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUT', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRS' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' SM' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' SM', ''));
    END IF;
    v_nome := TRIM(v_nome);
    -- posição do espaço em branco para obter o primeiro nome
    v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
    ELSE
      v_primeiro_nome := v_nome;
    END IF;
    -- obter o último nome
    v_posicao_espaco_ultimo_nome := 0;
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_cont := v_posicao_espaco_ultimo_nome + 1;
      -- obter posição do espaço em branco anterior ao último nome
      WHILE v_cont < LENGTH(v_nome) LOOP
        IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
          v_posicao_espaco_ultimo_nome = v_cont;
        END IF;
        v_cont := v_cont + 1;
      END LOOP;
      v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
      -- fonema do último nome
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_ultimo_nome) LOOP
        v_fonema_ultimo_nome := v_reg.fcn_fonetiza;
      END LOOP;
      -- verificar se o último nome termina com Junior, Sobrinho ou Filho e outros
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('junior') LOOP
        v_fonema_junior := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('sobrinho') LOOP
        v_fonema_sobrinho := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('filho') LOOP
        v_fonema_filho := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('filha') LOOP
        v_fonema_filha := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('sobrinha') LOOP
        v_fonema_sobrinha := v_reg.fcn_fonetiza;
      END LOOP;
      IF v_fonema_ultimo_nome = v_fonema_junior OR
                          v_fonema_ultimo_nome = v_fonema_sobrinho OR
                          v_fonema_ultimo_nome = v_fonema_filho OR
                          v_fonema_ultimo_nome = v_fonema_filha OR
                          v_fonema_ultimo_nome = v_fonema_sobrinha
                        THEN
        v_nome := TRIM(SUBSTR(v_nome, 1, (LENGTH(v_nome) - LENGTH(v_ultimo_nome) - 1)));
        v_primeiro_nome := '';
        v_ultimo_nome := '';
        -- obter novamente o primeiro nome
        v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
        IF v_posicao_espaco_primeiro_nome > 0 THEN
          v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
        END IF;
        
        -- obter o penultimo nome
        IF v_posicao_espaco_primeiro_nome > 0 THEN
          v_posicao_espaco_ultimo_nome := 0;
          v_cont := 1;
          
          -- obter posição do espaço em branco anterior ao último nome
          WHILE v_cont < LENGTH(v_nome) LOOP
          IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
            v_posicao_espaco_ultimo_nome = v_cont;
          END IF;
          v_cont := v_cont + 1;
          END LOOP;
          v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
        END IF;
      END IF;
    END IF;
  END IF;
  RETURN v_primeiro_nome || ' ' || v_ultimo_nome;
  END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_obter_primeiro_ultimo_nome_juridica(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_obter_primeiro_ultimo_nome_juridica(text) RETURNS text
    AS $_$
DECLARE
  v_nome_parametro    ALIAS FOR $1;
  v_nome        text;
  v_primeiro_nome     text;
  v_ultimo_nome     text;
  v_posicao_espaco_primeiro_nome  integer;
  v_posicao_espaco_ultimo_nome  integer;
  v_cont        integer;
  v_fonema_ultimo_nome    text;
  v_reg       record;
  v_total_caracteres    integer;
  BEGIN
  v_primeiro_nome := '';
  v_ultimo_nome := '';
  v_nome := TRIM(v_nome_parametro);
  v_total_caracteres := LENGTH(v_nome);
  -- obter somente o primeiro e o ultimo nome das pessoas
  IF v_total_caracteres > 0 THEN
    -- retirar os espaços duplicados
    WHILE POSITION('  ' IN v_nome) > 0 LOOP
      v_nome := REPLACE(UPPER(v_nome),'  ', ' ');
    END LOOP;
    -- retirar ocorrências que devem ser ignoradas no nome
    v_nome := TRIM(REPLACE(UPPER(v_nome),'&', ' '));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA E OUTRA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA. LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA.LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA.LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA. LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC LT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C LT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C L', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC L', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C LTD', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC LTD', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA. ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA.ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'S/C', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'S/A', ''));
    v_total_caracteres := LENGTH(v_nome);
    IF v_total_caracteres = (POSITION(' LT' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LT', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LT.' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LT.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LTD' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LTD', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LTD.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LTD.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' ME.' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' ME.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' ME' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' ME', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' -ME' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' -ME', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' -ME.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' -ME.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' SA' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' SA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S.A.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S.A.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S.A' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S.A', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S A' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S A', ''));
    END IF;
    v_nome := TRIM(v_nome);
    -- posição do espaco em branco para obter o primeiro nome
    v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
    ELSE
      v_primeiro_nome := v_nome;
    END IF;
    -- obter o ultimo nome
    v_posicao_espaco_ultimo_nome := 0;
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_cont := v_posicao_espaco_ultimo_nome + 1;
      -- obter posicao do espaco em branco anterior ao ultimo nome
      WHILE v_cont < LENGTH(v_nome) LOOP
        IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
          v_posicao_espaco_ultimo_nome = v_cont;
        END IF;
        v_cont := v_cont + 1;
      END LOOP;
      v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
    END IF;
  END IF;
  --Fonetizando o primeiro nome--
  FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_primeiro_nome) LOOP
    v_primeiro_nome := v_reg.fcn_fonetiza;
  END LOOP;
  --Fonetizando o ultimo nome nome--
  FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_ultimo_nome) LOOP
    v_ultimo_nome := v_reg.fcn_fonetiza;
  END LOOP;
  RETURN v_primeiro_nome || ' ' || v_ultimo_nome;
  END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_rg ALIAS for $2;  
  v_orgao_exp_rg ALIAS for $3;    
  v_data_exp_rg ALIAS for $4;
  v_sigla_uf_exp_rg ALIAS for $5;
    v_tipo_cert_civil ALIAS for $6;
    v_num_termo ALIAS for $7;
  v_num_livro ALIAS for $8;
  v_num_folha ALIAS for $9;
    v_data_emissao_cert_civil ALIAS for $10;
    v_sigla_uf_cert_civil ALIAS for $11;
  v_sigla_uf_cart_trabalho ALIAS for $12;
  v_cartorio_cert_civil ALIAS for $13;
  v_num_cart_trabalho ALIAS for $14;
    v_serie_cart_trabalho ALIAS for $15;
    v_data_emissao_cart_trabalho ALIAS for $16;
  v_num_tit_eleitor ALIAS for $17;  
  v_zona_tit_eleitor ALIAS for $18;
  v_secao_tit_eleitor ALIAS for $19;
  v_origem_gravacao ALIAS for $20;
  v_idpes_rev ALIAS for $21;
  v_idsis_rev ALIAS for $22;
    
    -- Outras variáveis
    v_rg_aux varchar(10);
    v_orgao_exp_rg_aux varchar(10);
    v_sigla_uf_exp_rg_aux varchar(2);
    v_tipo_cert_civil_aux integer;
  v_num_termo_aux integer;
  v_num_livro_aux integer;
  v_num_folha_aux integer;
    v_sigla_uf_cert_civil_aux varchar(2);
  v_sigla_uf_cart_trabalho_aux varchar(2);
    v_cartorio_cert_civil_aux varchar(150);
    v_num_cart_trabalho_aux integer;
    v_serie_cart_trabalho_aux integer;
    v_num_tit_eleitor_aux varchar(13);
    v_zona_tit_eleitor_aux integer;
    v_secao_tit_eleitor_aux integer;
BEGIN
    IF v_rg = '' THEN
        v_rg_aux := NULL;
    ELSE
        v_rg_aux := v_rg;
    END IF;
    IF v_orgao_exp_rg = '' THEN
        v_orgao_exp_rg_aux := NULL;
    ELSE
        v_orgao_exp_rg_aux := v_orgao_exp_rg;
    END IF;
    IF v_sigla_uf_exp_rg = '' THEN
        v_sigla_uf_exp_rg_aux := NULL;
    ELSE
        v_sigla_uf_exp_rg_aux := v_sigla_uf_exp_rg;
    END IF;
    IF v_tipo_cert_civil = 0 THEN
        v_tipo_cert_civil_aux := NULL;
    ELSE
        v_tipo_cert_civil_aux := v_tipo_cert_civil;
    END IF;
    IF v_num_termo = 0 THEN
        v_num_termo_aux := NULL;
    ELSE
        v_num_termo_aux := v_num_termo;
    END IF;
    IF v_num_livro = 0 THEN
        v_num_livro_aux := NULL;
    ELSE
        v_num_livro_aux := v_num_livro;
    END IF;
    IF v_num_folha = 0 THEN
        v_num_folha_aux := NULL;
    ELSE
        v_num_folha_aux := v_num_folha;
    END IF;
    IF v_sigla_uf_cert_civil = '' THEN
        v_sigla_uf_cert_civil_aux := NULL;
    ELSE
        v_sigla_uf_cert_civil_aux := v_sigla_uf_cert_civil;
    END IF;
    IF v_sigla_uf_cart_trabalho = '' THEN
        v_sigla_uf_cart_trabalho_aux := NULL;
    ELSE
        v_sigla_uf_cart_trabalho_aux := v_sigla_uf_cart_trabalho;
    END IF;
    IF v_cartorio_cert_civil = '' THEN
        v_cartorio_cert_civil_aux := NULL;
    ELSE
        v_cartorio_cert_civil_aux := v_cartorio_cert_civil;
    END IF;
    IF v_num_cart_trabalho = 0 THEN
        v_num_cart_trabalho_aux := NULL;
    ELSE
        v_num_cart_trabalho_aux := v_num_cart_trabalho;
    END IF;
    IF v_serie_cart_trabalho = 0 THEN
        v_serie_cart_trabalho_aux := NULL;
    ELSE
        v_serie_cart_trabalho_aux := v_serie_cart_trabalho;
    END IF;
    IF v_num_tit_eleitor = '' THEN
        v_num_tit_eleitor_aux := NULL;
    ELSE
        v_num_tit_eleitor_aux := v_num_tit_eleitor;
    END IF;
    IF v_zona_tit_eleitor = 0 THEN
        v_zona_tit_eleitor_aux := NULL;
    ELSE
        v_zona_tit_eleitor_aux := v_zona_tit_eleitor;
    END IF;
    IF v_secao_tit_eleitor = 0 THEN
        v_secao_tit_eleitor_aux := NULL;
    ELSE
        v_secao_tit_eleitor_aux := v_secao_tit_eleitor;
    END IF;
 
  -- Insere dados na tabela funcionário
    UPDATE cadastro.documento
    SET rg = to_number(v_rg_aux,9999999999),
        idorg_exp_rg = to_number(v_orgao_exp_rg_aux,9999999999),
        data_exp_rg = to_date(v_data_exp_rg,'DD/MM/YYYY'), 
        sigla_uf_exp_rg = v_sigla_uf_exp_rg_aux, 
        tipo_cert_civil = v_tipo_cert_civil_aux, 
        num_termo = v_num_termo_aux, 
        num_livro = v_num_livro_aux, 
        num_folha = v_num_folha_aux, 
        data_emissao_cert_civil = to_date(v_data_emissao_cert_civil,'DD/MM/YYYY'), 
        sigla_uf_cert_civil = v_sigla_uf_cert_civil_aux, 
        sigla_uf_cart_trabalho = v_sigla_uf_cart_trabalho_aux, 
        cartorio_cert_civil = v_cartorio_cert_civil_aux, 
        num_cart_trabalho = v_num_cart_trabalho_aux, 
        serie_cart_trabalho = v_serie_cart_trabalho_aux, 
        data_emissao_cart_trabalho = to_date(v_data_emissao_cart_trabalho,'DD/MM/YYYY'), 
        num_tit_eleitor = to_number(v_num_tit_eleitor_aux,9999999999999),                                              zona_tit_eleitor = v_zona_tit_eleitor_aux, 
        secao_tit_eleitor = v_secao_tit_eleitor_aux,
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes;
   
 
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2; 
  v_sigla_uf ALIAS for $3;
  v_idtlog ALIAS for $4;
    v_logradouro ALIAS for $5;
    v_numero ALIAS for $6;
    v_letra ALIAS for $7;
    v_complemento ALIAS for $8;
    v_bairro ALIAS for $9;
    v_cep ALIAS for $10;
    v_cidade ALIAS for $11;
    v_reside_desde ALIAS for $12;
    v_origem_gravacao ALIAS for $13;
    v_idpes_rev ALIAS for $14;
    v_idsis_rev ALIAS for $15;
  
BEGIN
  -- Atualiza dados na tabela endereco_externo
  UPDATE cadastro.endereco_externo
    SET sigla_uf = v_sigla_uf,
        idtlog = v_idtlog,
        logradouro = public.fcn_upper(v_logradouro),
        numero = v_numero,
        letra = public.fcn_upper(v_letra),
        complemento = public.fcn_upper(v_complemento),
        bairro = public.fcn_upper(v_bairro),
        cep = v_cep,
        cidade = public.fcn_upper(v_cidade),
        reside_desde = to_date(v_reside_desde,'DD/MM/YYYY'),
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes
    AND tipo = v_tipo;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
    -- Parâmetro recebidos
    v_id_pes ALIAS for $1;
    v_tipo ALIAS for $2;
    v_cep ALIAS for $3;
    v_idlog ALIAS for $4;
    v_idbai ALIAS for $5;
    v_numero ALIAS for $6;
    v_letra ALIAS for $7;
    v_complemento ALIAS for $8;
    v_reside_desde ALIAS for $9;
    v_origem_gravacao ALIAS for $10;
    v_idpes_rev ALIAS for $11;
    v_idsis_rev ALIAS for $12;
    v_sql_update text;
BEGIN
    -- Atualiza dados na tabela endereco_pessoa
    UPDATE cadastro.endereco_pessoa
    SET cep = v_cep,
        idlog = v_idlog,
        idbai = v_idbai,
        numero = v_numero,
        letra = public.fcn_upper(v_letra),
        complemento = public.fcn_upper(v_complemento),
        reside_desde = to_date(v_reside_desde,'DD/MM/YYYY'),
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes
    AND tipo = v_tipo;
    RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_data_nasc ALIAS for $2;
  v_sexo ALIAS for $3;
  v_id_pes_mae ALIAS for $4;
      v_id_pes_pai ALIAS for $5;
      v_id_pes_responsavel ALIAS for $6;
  v_id_esco ALIAS for $7;
  v_id_eciv ALIAS for $8;
  v_id_pes_con ALIAS for $9;
      v_data_uniao ALIAS for $10;
      v_data_obito ALIAS for $11;
  v_nacionalidade ALIAS for $12;
  v_id_pais_estrangeiro ALIAS for $13;
  v_data_chegada ALIAS for $14;
      v_id_mun_nascimento ALIAS for $15;
      v_ultima_empresa ALIAS for $16;
  v_id_ocup ALIAS for $17;
  v_nome_mae ALIAS for $18;
  v_nome_pai ALIAS for $19;
      v_nome_conjuge ALIAS for $20;
      v_nome_responsavel ALIAS for $21;
      v_justificativa_provisorio ALIAS for $22;
      v_origem_gravacao ALIAS for $23;
      v_idpes_rev ALIAS for $24;
      v_idsis_rev ALIAS for $25;
    
      -- Outras variáveis
      v_id_pes_mae_aux integer;
      v_id_pes_pai_aux integer;
      v_id_pes_responsavel_aux integer;
  v_id_esco_aux integer;
  v_id_eciv_aux integer;
  v_id_pes_con_aux integer;
      v_nacionalidade_aux integer;
  v_id_pais_estrangeiro_aux integer;
      v_id_mun_nascimento_aux integer;
      v_id_ocup_aux integer;
  v_sexo_aux text;
BEGIN
    IF v_id_pes_mae = 0 THEN
        v_id_pes_mae_aux := NULL;
    ELSE
        v_id_pes_mae_aux := v_id_pes_mae;
    END IF;
    IF v_id_pes_pai = 0 THEN
        v_id_pes_pai_aux := NULL;
    ELSE
        v_id_pes_pai_aux := v_id_pes_pai;
    END IF;
    IF v_id_pes_responsavel = 0 THEN
        v_id_pes_responsavel_aux := NULL;
    ELSE
        v_id_pes_responsavel_aux := v_id_pes_responsavel;
    END IF;
    IF v_id_esco = 0 THEN
        v_id_esco_aux := NULL;
    ELSE
        v_id_esco_aux := v_id_esco;
    END IF;
    IF v_id_eciv = 0 THEN
        v_id_eciv_aux := NULL;
    ELSE
        v_id_eciv_aux := v_id_eciv;
    END IF;
    IF v_id_pes_con = 0 THEN
        v_id_pes_con_aux := NULL;
    ELSE
        v_id_pes_con_aux := v_id_pes_con;
    END IF;
    IF v_nacionalidade = 0 THEN
        v_nacionalidade_aux := NULL;
    ELSE
        v_nacionalidade_aux := v_nacionalidade;
    END IF;
    IF v_id_pais_estrangeiro = 0 THEN
        v_id_pais_estrangeiro_aux := NULL;
    ELSE
        v_id_pais_estrangeiro_aux := v_id_pais_estrangeiro;
    END IF;
    IF v_id_mun_nascimento = 0 THEN
        v_id_mun_nascimento_aux := NULL;
    ELSE
        v_id_mun_nascimento_aux := v_id_mun_nascimento;
    END IF;
    IF v_id_ocup = 0 THEN
        v_id_ocup_aux := NULL;
    ELSE
        v_id_ocup_aux := v_id_ocup;
    END IF;
    IF TRIM(v_sexo) = '' THEN
        v_sexo_aux := NULL;
    ELSE
        v_sexo_aux := public.fcn_upper(v_sexo);
    END IF;
  -- Insere dados na tabela funcionário
    UPDATE cadastro.fisica 
    SET data_nasc = to_date(v_data_nasc,'DD/MM/YYYY'),
        sexo = v_sexo_aux, 
        idpes_mae = v_id_pes_mae_aux,
        idpes_pai = v_id_pes_pai_aux,
        idpes_responsavel = v_id_pes_responsavel_aux, 
        idesco = v_id_esco_aux,
        ideciv = v_id_eciv_aux, 
        idpes_con = v_id_pes_con_aux, 
        data_uniao = to_date(v_data_uniao,'DD/MM/YYYY'), 
        data_obito = to_date(v_data_obito,'DD/MM/YYYY'), 
        nacionalidade = v_nacionalidade_aux,
        idpais_estrangeiro = v_id_pais_estrangeiro_aux, 
        data_chegada_brasil = to_date(v_data_chegada,'DD/MM/YYYY'), 
        idmun_nascimento = v_id_mun_nascimento_aux, 
        ultima_empresa = public.fcn_upper(v_ultima_empresa), 
        idocup = v_id_ocup_aux, 
        nome_mae = public.fcn_upper(v_nome_mae), 
        nome_pai = public.fcn_upper(v_nome_pai), 
        nome_conjuge = public.fcn_upper(v_nome_conjuge),
        nome_responsavel = public.fcn_upper(v_nome_responsavel), 
        justificativa_provisorio = v_justificativa_provisorio,
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes;
 
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_fisica_cpf(integer, text, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cpf ALIAS for $2;
  v_origem_gravacao ALIAS for $3;
  v_idpes_rev ALIAS for $4;
  v_idsis_rev ALIAS for $5;
  
BEGIN
  -- Atualiza dados na tabela fisica_cpf
  UPDATE cadastro.fisica_cpf SET 
    origem_gravacao = v_origem_gravacao,
    idpes_rev = v_idpes_rev,
    idsis_rev = v_idsis_rev,
    data_rev = CURRENT_TIMESTAMP,
    operacao = 'A',
    cpf = to_number(v_cpf,99999999999)
    WHERE cadastro.fisica_cpf.idpes = v_id_pes;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_fone_pessoa(integer, integer, integer, integer, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_ddd ALIAS for $3;
  v_fone ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;
  
BEGIN
  -- Atualiza dados na tabela fone_pessoa
  UPDATE cadastro.fone_pessoa 
    SET ddd = v_ddd,
        fone = v_fone,
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes
    AND tipo = v_tipo;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_funcionario(numeric, integer, integer, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_funcionario(numeric, integer, integer, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;
  v_id_set ALIAS for $3;
  v_situacao ALIAS for $4;
      v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;
  -- Outras variáveis
  v_id_set_aux integer;
  v_matricula_aux numeric;
  v_id_ins_aux integer;
  v_situacao_aux varchar(1);
BEGIN
  v_matricula_aux := v_matricula;
  v_id_ins_aux := v_id_ins;
  v_situacao_aux := v_situacao;
  IF v_id_set <= 0 THEN
    v_id_set_aux := NULL;
  ELSE
    v_id_set_aux := v_id_set;
  END IF;
    
  IF v_id_set_aux IS NULL AND v_id_set = -1 THEN
    -- Sql utilizado para ativar e desativar o registro na tabela funcionário
     UPDATE cadastro.funcionario 
      SET situacao=v_situacao_aux,
      origem_gravacao = v_origem_gravacao,
      idpes_rev = v_idpes_rev,
      idsis_rev = v_idsis_rev,
      data_rev = CURRENT_DATE,
      operacao = 'A' 
      WHERE   
          matricula=v_matricula_aux AND 
          idins=v_id_ins_aux;  
  ELSE 
    UPDATE cadastro.funcionario 
      SET idset=v_id_set_aux,
          situacao=v_situacao_aux,
          origem_gravacao = v_origem_gravacao,
          idpes_rev = v_idpes_rev,
          idsis_rev = v_idsis_rev,
          data_rev = CURRENT_TIMESTAMP,
            operacao = 'A' 
      WHERE 
          matricula=v_matricula_aux AND 
          idins=v_id_ins_aux;
  END IF;
  
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_juridica(integer, character varying, character varying, character varying, character, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cnpj ALIAS for $2;
  v_fantasia ALIAS for $3;
  v_inscr_estadual ALIAS for $4;
      v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;
  
BEGIN
  -- Atualiza dados na tabela juridica
  UPDATE cadastro.juridica
    SET cnpj = to_number(v_cnpj,99999999999999),
        fantasia = public.fcn_upper(v_fantasia),
        insc_estadual = to_number(v_inscr_estadual,9999999999),
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_update_pessoa(integer, text, character varying, character varying, character varying, integer, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_update_pessoa(integer, text, character varying, character varying, character varying, integer, character varying, integer, integer) RETURNS integer
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_razao_social ALIAS for $2;
  v_url ALIAS for $3;
  v_email ALIAS for $4;
  v_situacao ALIAS for $5;
  v_id_pes_logado ALIAS for $6;
  v_origem_gravacao ALIAS for $7;
      v_idpes_rev ALIAS for $8;
      v_idsis_rev ALIAS for $9;
  
  idpes_logado integer;
BEGIN
  idpes_logado := v_id_pes_logado;
  IF (idpes_logado <= 0) THEN
    -- Atualiza dados na tabela pessoa
    UPDATE cadastro.pessoa SET
        nome = public.fcn_upper(v_razao_social),
        url = v_url,
        email = v_email,
        situacao = v_situacao,
        idpes_rev = NULL,
        data_rev = CURRENT_TIMESTAMP,
              origem_gravacao = v_origem_gravacao,
        idsis_rev = v_idsis_rev,
        operacao = 'A'
        WHERE idpes = v_id_pes;
  ELSE
    -- Atualiza dados na tabela pessoa
    UPDATE cadastro.pessoa SET
        nome = public.fcn_upper(v_razao_social),
        url = v_url,
        email = v_email,
        situacao = v_situacao,
        idpes_rev = idpes_logado,
        data_rev = CURRENT_TIMESTAMP,
              origem_gravacao = v_origem_gravacao,
        idsis_rev = v_idsis_rev,
        operacao = 'A'
        WHERE idpes = v_id_pes;
    END IF;
  RETURN 0;
END;$_$
    LANGUAGE plpgsql;


--
-- Name: fcn_upper(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_upper(text) RETURNS text
    AS $_$
   DECLARE
    v_texto     ALIAS FOR $1;
    v_retorno   text := '';
   BEGIN
    IF v_texto IS NOT NULL THEN
     SELECT translate(upper(v_texto),'áéíóúýàèìòùãõâêîôûäëïöüç','ÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ') INTO v_retorno;
    END IF;
    RETURN v_retorno;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: fcn_upper_nrm(text); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fcn_upper_nrm(text) RETURNS text
    AS $_$
   DECLARE
    v_texto     ALIAS FOR $1;
    v_retorno   text := '';
   BEGIN
    IF v_texto IS NOT NULL THEN
     SELECT translate(upper(v_texto),'áéíóúýàèìòùãõâêîôûäëïöüÿçÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','AEIOUYAEIOUAOAEIOUAEIOUYCAEIOUYAEIOUAOAEIOUAEIOUC') INTO v_retorno;
    END IF;
    RETURN v_retorno;
   END;
  $_$
    LANGUAGE plpgsql;


--
-- Name: plpgsql_call_handler(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION plpgsql_call_handler() RETURNS language_handler
    AS '$libdir/plpgsql', 'plpgsql_call_handler'
    LANGUAGE c;


SET search_path = acesso, pg_catalog;

--
-- Name: funcao_idfunc_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE funcao_idfunc_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: funcao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE funcao (
    idfunc integer DEFAULT nextval('funcao_idfunc_seq'::regclass) NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL,
    nome character varying(100) NOT NULL,
    situacao character(1) NOT NULL,
    url character varying(250) NOT NULL,
    ordem numeric(2,0) NOT NULL,
    descricao character varying(250) NOT NULL,
    CONSTRAINT ck_funcao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: grupo_idgrp_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE grupo_idgrp_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: grupo; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE grupo (
    idgrp integer DEFAULT nextval('grupo_idgrp_seq'::regclass) NOT NULL,
    nome character varying(40) NOT NULL,
    situacao character(1) NOT NULL,
    descricao character varying(250),
    CONSTRAINT ck_grupo_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: grupo_funcao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE grupo_funcao (
    idmen integer NOT NULL,
    idsis integer NOT NULL,
    idgrp integer NOT NULL,
    idfunc integer NOT NULL
);


--
-- Name: grupo_menu; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE grupo_menu (
    idgrp integer NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL
);


--
-- Name: grupo_operacao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE grupo_operacao (
    idfunc integer NOT NULL,
    idgrp integer NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL,
    idope integer NOT NULL
);


--
-- Name: grupo_sistema; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE grupo_sistema (
    idsis integer NOT NULL,
    idgrp integer NOT NULL
);


--
-- Name: historico_senha; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE historico_senha (
    "login" character varying(16) NOT NULL,
    senha character varying(60) NOT NULL,
    data_cad timestamp without time zone NOT NULL
);


--
-- Name: instituicao_idins_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE instituicao_idins_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: instituicao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE instituicao (
    idins integer DEFAULT nextval('instituicao_idins_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_instituicao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: log_acesso; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE log_acesso (
    data timestamp without time zone NOT NULL,
    idpes numeric(8,0) NOT NULL,
    idsis integer,
    idins integer,
    idcli character varying(10),
    operacao character(1) NOT NULL,
    CONSTRAINT ck_log_acesso_situacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'O'::bpchar)))
);


--
-- Name: log_erro; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE log_erro (
    data timestamp without time zone NOT NULL,
    idpes numeric(8,0),
    idsis integer,
    idmen integer,
    idfunc integer,
    idope integer,
    msg_erro text NOT NULL
);


--
-- Name: menu_idmen_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE menu_idmen_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: menu; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE menu (
    idmen integer DEFAULT nextval('menu_idmen_seq'::regclass) NOT NULL,
    idsis integer NOT NULL,
    menu_idsis integer,
    menu_idmen integer,
    nome character varying(40) NOT NULL,
    descricao character varying(250) NOT NULL,
    situacao character(1) NOT NULL,
    ordem numeric(2,0) NOT NULL,
    CONSTRAINT ck_menu_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: operacao_idope_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE operacao_idope_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: operacao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE operacao (
    idope integer DEFAULT nextval('operacao_idope_seq'::regclass) NOT NULL,
    idsis integer,
    nome character varying(40) NOT NULL,
    situacao character(1) NOT NULL,
    descricao character varying(250) NOT NULL,
    CONSTRAINT ck_operacao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: operacao_funcao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE operacao_funcao (
    idmen integer NOT NULL,
    idsis integer NOT NULL,
    idfunc integer NOT NULL,
    idope integer NOT NULL
);


--
-- Name: pessoa_instituicao; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_instituicao (
    idins integer NOT NULL,
    idpes numeric(8,0) NOT NULL
);


--
-- Name: sistema_idsis_seq; Type: SEQUENCE; Schema: acesso; Owner: -
--

CREATE SEQUENCE sistema_idsis_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: sistema; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE sistema (
    idsis integer DEFAULT nextval('sistema_idsis_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    descricao character varying(100) NOT NULL,
    contexto character varying(30) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_sistema_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: usuario; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE usuario (
    "login" character varying(16) NOT NULL,
    idpes numeric(8,0) NOT NULL,
    idpes_sga numeric(8,0),
    senha character varying(60) NOT NULL,
    datacad date NOT NULL,
    lastlogin timestamp without time zone NOT NULL,
    dica character varying(60),
    situacao character(1) NOT NULL,
    data_alt_senha date NOT NULL,
    exp_senha character(1),
    mudar_senha character(1),
    num_sessao_atual numeric(2,0) NOT NULL,
    prazo_exp numeric(3,0),
    estilo_menu character(1),
    CONSTRAINT ck_usuario_estilo_menu CHECK (((estilo_menu = 'C'::bpchar) OR (estilo_menu = 'D'::bpchar))),
    CONSTRAINT ck_usuario_exp_senha CHECK (((exp_senha = 'S'::bpchar) OR (exp_senha = 'N'::bpchar))),
    CONSTRAINT ck_usuario_mudar_senha CHECK (((mudar_senha = 'S'::bpchar) OR (mudar_senha = 'N'::bpchar))),
    CONSTRAINT ck_usuario_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: usuario_grupo; Type: TABLE; Schema: acesso; Owner: -; Tablespace: 
--

CREATE TABLE usuario_grupo (
    idgrp integer NOT NULL,
    "login" character varying(16) NOT NULL
);


SET search_path = alimentos, pg_catalog;

--
-- Name: baixa_guia_produto_idbap_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE baixa_guia_produto_idbap_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: baixa_guia_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE baixa_guia_produto (
    idbap integer DEFAULT nextval('baixa_guia_produto_idbap_seq'::regclass) NOT NULL,
    idgup integer NOT NULL,
    idbai integer NOT NULL,
    dt_validade date,
    qtde_recebida numeric NOT NULL,
    dt_operacao date NOT NULL,
    login_baixa character varying(80) NOT NULL
);


--
-- Name: baixa_guia_remessa_idbai_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE baixa_guia_remessa_idbai_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: baixa_guia_remessa; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE baixa_guia_remessa (
    idbai integer DEFAULT nextval('baixa_guia_remessa_idbai_seq'::regclass) NOT NULL,
    login_baixa character varying(80) NOT NULL,
    idgui integer NOT NULL,
    dt_recebimento date NOT NULL,
    nome_recebedor character varying(40) NOT NULL,
    cargo_recebedor character varying(40) NOT NULL,
    dt_operacao date NOT NULL
);


--
-- Name: calendario_idcad_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE calendario_idcad_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: calendario; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE calendario (
    idcad integer DEFAULT nextval('calendario_idcad_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    ano integer NOT NULL,
    descricao character varying(40) NOT NULL
);


--
-- Name: cardapio_idcar_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE cardapio_idcar_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: cardapio; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE cardapio (
    idcar integer DEFAULT nextval('cardapio_idcar_seq'::regclass) NOT NULL,
    login_inclusao character varying(80) NOT NULL,
    login_alteracao character varying(80),
    idcli character varying(10) NOT NULL,
    idtre integer NOT NULL,
    dt_cardapio date NOT NULL,
    dt_inclusao timestamp without time zone NOT NULL,
    dt_ultima_alteracao timestamp without time zone,
    valor numeric NOT NULL,
    finalizado character(1) NOT NULL,
    CONSTRAINT ck_cardapio_finalizado CHECK (((finalizado = 'S'::bpchar) OR (finalizado = 'N'::bpchar)))
);


--
-- Name: cardapio_faixa_unidade; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE cardapio_faixa_unidade (
    idfeu integer NOT NULL,
    idcar integer NOT NULL
);


--
-- Name: cardapio_produto_idcpr_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE cardapio_produto_idcpr_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: cardapio_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE cardapio_produto (
    idcpr integer DEFAULT nextval('cardapio_produto_idcpr_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idcar integer NOT NULL,
    quantidade numeric NOT NULL,
    valor numeric NOT NULL
);


--
-- Name: cardapio_receita; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE cardapio_receita (
    idcar integer NOT NULL,
    idrec integer NOT NULL
);


--
-- Name: cliente; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE cliente (
    idcli character varying(10) NOT NULL,
    nome character varying(40) NOT NULL,
    cnpj character varying(14) NOT NULL,
    endereco character varying(60) NOT NULL,
    bairro character varying(30) NOT NULL,
    cidade character varying(18) NOT NULL,
    cep character varying(8),
    uf character varying(2) NOT NULL,
    telefone character varying(11) NOT NULL,
    fax character varying(11),
    email character varying(40),
    prefeito character varying(40) NOT NULL,
    educacao character varying(40) NOT NULL,
    administracao character varying(40) NOT NULL,
    coordenacao character varying(40) NOT NULL,
    inscritos character(1) NOT NULL,
    idpes integer NOT NULL,
    identificacao character varying(20) NOT NULL,
    tab_produtos character(1),
    CONSTRAINT ck_tab_produtos CHECK (((tab_produtos = '1'::bpchar) OR (tab_produtos = '2'::bpchar)))
);


--
-- Name: composto_quimico_idcom_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE composto_quimico_idcom_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: composto_quimico; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE composto_quimico (
    idcom integer DEFAULT nextval('composto_quimico_idcom_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    idgrpq integer NOT NULL,
    descricao character varying(50) NOT NULL,
    unidade character varying(5) NOT NULL
);


--
-- Name: contrato_idcon_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE contrato_idcon_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: contrato; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE contrato (
    idcon integer DEFAULT nextval('contrato_idcon_seq'::regclass) NOT NULL,
    codigo character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    "login" character varying(80) NOT NULL,
    num_aditivo integer NOT NULL,
    idfor integer NOT NULL,
    dt_vigencia date NOT NULL,
    tipo character(1) NOT NULL,
    vlr_atual numeric NOT NULL,
    cancelado character(1) NOT NULL,
    dt_cancelamento timestamp without time zone,
    dt_inclusao timestamp without time zone NOT NULL,
    ultimo_contrato character(1) NOT NULL,
    vlr_original numeric NOT NULL,
    finalizado character(1) NOT NULL,
    CONSTRAINT ck_contrato_cancelado CHECK (((cancelado = 'S'::bpchar) OR (cancelado = 'N'::bpchar))),
    CONSTRAINT ck_contrato_finalizado CHECK (((finalizado = 'S'::bpchar) OR (finalizado = 'N'::bpchar))),
    CONSTRAINT ck_contrato_tipo CHECK (((tipo = 'C'::bpchar) OR (tipo = 'A'::bpchar))),
    CONSTRAINT ck_contrato_ultimo_contrato CHECK (((ultimo_contrato = 'S'::bpchar) OR (ultimo_contrato = 'N'::bpchar)))
);


--
-- Name: contrato_produto_idcop_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE contrato_produto_idcop_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: contrato_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE contrato_produto (
    idcop integer DEFAULT nextval('contrato_produto_idcop_seq'::regclass) NOT NULL,
    idcon integer NOT NULL,
    idpro integer NOT NULL,
    qtde_contratada numeric NOT NULL,
    vlr_unitario_atual numeric NOT NULL,
    qtde_remessa numeric NOT NULL,
    qtde_recebida numeric NOT NULL,
    qtde_aditivo numeric NOT NULL,
    vlr_unitario_original numeric NOT NULL,
    operacao character(1) NOT NULL,
    ajuste numeric,
    CONSTRAINT ck_contrato_operacao CHECK (((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar)) OR (operacao = 'N'::bpchar)))
);


--
-- Name: evento_ideve_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE evento_ideve_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: evento; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE evento (
    ideve integer DEFAULT nextval('evento_ideve_seq'::regclass) NOT NULL,
    idcad integer NOT NULL,
    mes integer NOT NULL,
    dia integer NOT NULL,
    dia_util character(1) NOT NULL,
    descricao character varying(50) NOT NULL,
    CONSTRAINT ck_evento_dia_util CHECK (((dia_util = 'S'::bpchar) OR (dia_util = 'N'::bpchar)))
);


--
-- Name: faixa_composto_quimico_idfcp_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE faixa_composto_quimico_idfcp_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: faixa_composto_quimico; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE faixa_composto_quimico (
    idfcp integer DEFAULT nextval('faixa_composto_quimico_idfcp_seq'::regclass) NOT NULL,
    idcom integer NOT NULL,
    idfae integer NOT NULL,
    quantidade numeric NOT NULL,
    qtde_max_min character(3) NOT NULL,
    CONSTRAINT ck_qtde_max_min CHECK (((qtde_max_min = 'MAX'::bpchar) OR (qtde_max_min = 'MIN'::bpchar)))
);


--
-- Name: faixa_etaria_idfae_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE faixa_etaria_idfae_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: faixa_etaria; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE faixa_etaria (
    idfae integer DEFAULT nextval('faixa_etaria_idfae_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL,
    vlr_base_refeicao numeric NOT NULL
);


--
-- Name: fornecedor_idfor_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE fornecedor_idfor_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: fornecedor; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE fornecedor (
    idfor integer DEFAULT nextval('fornecedor_idfor_seq'::regclass) NOT NULL,
    idpes integer NOT NULL,
    idcli character varying(10) NOT NULL,
    razao_social character varying(50) NOT NULL,
    nome_fantasia character varying(50) NOT NULL,
    endereco character varying(40) NOT NULL,
    complemento character varying(30),
    bairro character varying(30) NOT NULL,
    cep character varying(8),
    cidade character varying(18) NOT NULL,
    uf character varying(2) NOT NULL,
    telefone character varying(11) NOT NULL,
    fax character varying(11),
    email character varying(40),
    contato character varying(40) NOT NULL,
    cpf_cnpj character varying(14) NOT NULL,
    inscr_estadual character varying(20),
    inscr_municipal character varying(20),
    tipo character(1) NOT NULL,
    CONSTRAINT ck_fornecedor CHECK (((tipo = 'F'::bpchar) OR (tipo = 'J'::bpchar)))
);


--
-- Name: fornecedor_unidade_atendida; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE fornecedor_unidade_atendida (
    iduni integer NOT NULL,
    idfor integer NOT NULL
);


--
-- Name: grupo_quimico_idgrpq_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE grupo_quimico_idgrpq_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: grupo_quimico; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE grupo_quimico (
    idgrpq integer DEFAULT nextval('grupo_quimico_idgrpq_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);


--
-- Name: guia_produto_diario_idguiaprodiario_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE guia_produto_diario_idguiaprodiario_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: guia_produto_diario; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE guia_produto_diario (
    idguiaprodiario integer DEFAULT nextval('guia_produto_diario_idguiaprodiario_seq'::regclass) NOT NULL,
    idgui integer NOT NULL,
    idpro integer NOT NULL,
    iduni integer NOT NULL,
    dt_guia date NOT NULL,
    qtde numeric NOT NULL
);


--
-- Name: guia_remessa_idgui_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE guia_remessa_idgui_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: guia_remessa; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE guia_remessa (
    idgui integer DEFAULT nextval('guia_remessa_idgui_seq'::regclass) NOT NULL,
    idcon integer NOT NULL,
    login_cancelamento character varying(80),
    login_emissao character varying(80) NOT NULL,
    idfor integer NOT NULL,
    iduni integer NOT NULL,
    idcli character varying(10) NOT NULL,
    dt_emissao timestamp without time zone NOT NULL,
    ano integer NOT NULL,
    sequencial integer NOT NULL,
    dt_cardapio_inicial date NOT NULL,
    dt_cardapio_final date NOT NULL,
    num_inscr_matr integer NOT NULL,
    num_refeicao integer NOT NULL,
    situacao character(1) NOT NULL,
    dt_cancelamento timestamp without time zone,
    justificativa_cancelamento character varying(300),
    classe_produto character varying(2) NOT NULL,
    CONSTRAINT ck_guia_remessa_classe_produto CHECK (((((classe_produto)::text = 'P'::text) OR ((classe_produto)::text = 'N'::text)) OR ((classe_produto)::text = 'PN'::text))),
    CONSTRAINT ck_guia_remessa_situacao CHECK (((((situacao = 'E'::bpchar) OR (situacao = 'R'::bpchar)) OR (situacao = 'C'::bpchar)) OR (situacao = 'P'::bpchar)))
);


--
-- Name: guia_remessa_produto_idgup_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE guia_remessa_produto_idgup_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: guia_remessa_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE guia_remessa_produto (
    idgup integer DEFAULT nextval('guia_remessa_produto_idgup_seq'::regclass) NOT NULL,
    idgui integer NOT NULL,
    idpro integer NOT NULL,
    qtde_per_capita numeric NOT NULL,
    qtde_guia numeric NOT NULL,
    peso numeric NOT NULL,
    qtde_recebida numeric NOT NULL,
    peso_total numeric NOT NULL
);


--
-- Name: log_guia_remessa_idlogguia_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE log_guia_remessa_idlogguia_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: log_guia_remessa; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE log_guia_remessa (
    idlogguia integer DEFAULT nextval('log_guia_remessa_idlogguia_seq'::regclass) NOT NULL,
    "login" character varying(80) NOT NULL,
    idcli character varying(10) NOT NULL,
    dt_inicial date NOT NULL,
    dt_final date NOT NULL,
    unidade character varying(80) NOT NULL,
    fornecedor character varying(80) NOT NULL,
    classe character(2),
    dt_geracao timestamp without time zone NOT NULL,
    mensagem text NOT NULL
);


--
-- Name: medidas_caseiras; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE medidas_caseiras (
    idmedcas character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);


--
-- Name: pessoa_idpes_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE pessoa_idpes_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pessoa; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE pessoa (
    idpes integer DEFAULT nextval('pessoa_idpes_seq'::regclass) NOT NULL,
    tipo character varying(1) NOT NULL,
    CONSTRAINT ck_pessoa CHECK (((((tipo)::text = 'C'::text) OR ((tipo)::text = 'F'::text)) OR ((tipo)::text = 'U'::text)))
);


--
-- Name: produto_idpro_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE produto_idpro_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE produto (
    idpro integer DEFAULT nextval('produto_idpro_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    idunp character varying(20) NOT NULL,
    idfnde character varying(15) NOT NULL,
    idtip integer NOT NULL,
    descricao character varying(60) NOT NULL,
    nome_compra character varying(60) NOT NULL,
    referencia_ceasa character varying(1) NOT NULL,
    fator_coccao numeric NOT NULL,
    fator_correcao numeric NOT NULL,
    vlr_unitario numeric NOT NULL,
    penultimo_vlr_unitario numeric,
    dt_ultima_compra timestamp without time zone,
    dt_penultima_compra timestamp without time zone,
    qtde_estoque numeric NOT NULL,
    classe character varying(1) NOT NULL,
    desc_composto character varying(300),
    idfor integer,
    CONSTRAINT ck_produto_classe CHECK ((((classe)::text = 'P'::text) OR ((classe)::text = 'N'::text))),
    CONSTRAINT ck_produto_referencia_ceasa CHECK ((((referencia_ceasa)::text = '1'::text) OR ((referencia_ceasa)::text = '0'::text)))
);


--
-- Name: produto_composto_quimico_idpcq_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE produto_composto_quimico_idpcq_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: produto_composto_quimico; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE produto_composto_quimico (
    idpcq integer DEFAULT nextval('produto_composto_quimico_idpcq_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idcom integer NOT NULL,
    quantidade numeric NOT NULL
);


--
-- Name: produto_fornecedor_idprf_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE produto_fornecedor_idprf_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: produto_fornecedor; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE produto_fornecedor (
    idprf integer DEFAULT nextval('produto_fornecedor_idprf_seq'::regclass) NOT NULL,
    idfor integer NOT NULL,
    idpro integer NOT NULL,
    codigo_ean character varying(18) NOT NULL
);


--
-- Name: produto_medida_caseira_idpmc_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE produto_medida_caseira_idpmc_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: produto_medida_caseira; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE produto_medida_caseira (
    idpmc integer DEFAULT nextval('produto_medida_caseira_idpmc_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    idmedcas character varying(20) NOT NULL,
    idpro integer NOT NULL,
    peso numeric NOT NULL
);


--
-- Name: receita_idrec_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE receita_idrec_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: receita; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE receita (
    idrec integer DEFAULT nextval('receita_idrec_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    valor numeric NOT NULL,
    descricao character varying(60) NOT NULL,
    modo_preparo text NOT NULL,
    rendimento integer NOT NULL,
    valor_percapita numeric NOT NULL
);


--
-- Name: receita_composto_quimico_idrcq_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE receita_composto_quimico_idrcq_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: receita_composto_quimico; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE receita_composto_quimico (
    idrcq integer DEFAULT nextval('receita_composto_quimico_idrcq_seq'::regclass) NOT NULL,
    idcom integer NOT NULL,
    idrec integer NOT NULL,
    quantidade numeric NOT NULL
);


--
-- Name: receita_produto_idrpr_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE receita_produto_idrpr_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: receita_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE receita_produto (
    idrpr integer DEFAULT nextval('receita_produto_idrpr_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idrec integer NOT NULL,
    idmedcas character varying(20),
    quantidade numeric NOT NULL,
    valor numeric NOT NULL,
    qtdemedidacaseira integer NOT NULL,
    valor_percapita numeric NOT NULL
);


--
-- Name: tipo_produto_idtip_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE tipo_produto_idtip_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE tipo_produto (
    idtip integer DEFAULT nextval('tipo_produto_idtip_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);


--
-- Name: tipo_refeicao_idtre_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE tipo_refeicao_idtre_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_refeicao; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE tipo_refeicao (
    idtre integer DEFAULT nextval('tipo_refeicao_idtre_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(30) NOT NULL
);


--
-- Name: tipo_unidade_idtip_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE tipo_unidade_idtip_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_unidade; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE tipo_unidade (
    idtip integer DEFAULT nextval('tipo_unidade_idtip_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);


--
-- Name: unidade_atendida_iduni_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE unidade_atendida_iduni_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: unidade_atendida; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE unidade_atendida (
    iduni integer DEFAULT nextval('unidade_atendida_iduni_seq'::regclass) NOT NULL,
    idcad integer NOT NULL,
    idtip integer NOT NULL,
    codigo character varying(10) NOT NULL,
    idcli character varying(10) NOT NULL,
    nome character varying(40) NOT NULL,
    endereco character varying(60) NOT NULL,
    complemento character varying(30),
    bairro character varying(30) NOT NULL,
    cep character varying(8),
    telefone character varying(11) NOT NULL,
    fax character varying(11),
    email character varying(40),
    idpes integer NOT NULL,
    diretor character varying(40) NOT NULL
);


--
-- Name: unidade_faixa_etaria_idfeu_seq; Type: SEQUENCE; Schema: alimentos; Owner: -
--

CREATE SEQUENCE unidade_faixa_etaria_idfeu_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: unidade_faixa_etaria; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE unidade_faixa_etaria (
    idfeu integer DEFAULT nextval('unidade_faixa_etaria_idfeu_seq'::regclass) NOT NULL,
    iduni integer NOT NULL,
    idfae integer NOT NULL,
    num_inscritos integer NOT NULL,
    num_matriculados integer NOT NULL
);


--
-- Name: unidade_produto; Type: TABLE; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE TABLE unidade_produto (
    idunp character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL,
    peso numeric NOT NULL
);


SET search_path = cadastro, pg_catalog;

--
-- Name: aviso_nome; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE aviso_nome (
    idpes numeric(8,0) NOT NULL,
    aviso numeric(1,0) NOT NULL,
    CONSTRAINT ck_aviso_nome_aviso CHECK (((aviso >= (1)::numeric) AND (aviso <= (4)::numeric)))
);


--
-- Name: deficiencia_cod_deficiencia_seq; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE deficiencia_cod_deficiencia_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: deficiencia; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE deficiencia (
    cod_deficiencia integer DEFAULT nextval('deficiencia_cod_deficiencia_seq'::regclass) NOT NULL,
    nm_deficiencia character varying(70) NOT NULL
);


--
-- Name: documento; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE documento (
    idpes numeric(8,0) NOT NULL,
    rg numeric(10,0),
    data_exp_rg date,
    sigla_uf_exp_rg character(2),
    tipo_cert_civil numeric(2,0),
    num_termo numeric(8,0),
    num_livro numeric(8,0),
    num_folha numeric(4,0),
    data_emissao_cert_civil date,
    sigla_uf_cert_civil character(2),
    cartorio_cert_civil character varying(150),
    num_cart_trabalho numeric(9,0),
    serie_cart_trabalho numeric(5,0),
    data_emissao_cart_trabalho date,
    sigla_uf_cart_trabalho character(2),
    num_tit_eleitor numeric(13,0),
    zona_tit_eleitor numeric(4,0),
    secao_tit_eleitor numeric(4,0),
    idorg_exp_rg integer,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_documento_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_documento_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_documento_tipo_cert CHECK (((tipo_cert_civil >= (91)::numeric) AND (tipo_cert_civil <= (92)::numeric)))
);


--
-- Name: endereco_externo; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE endereco_externo (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    idtlog character varying(5) NOT NULL,
    logradouro character varying(150) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(20),
    bairro character varying(40),
    cep numeric(8,0),
    cidade character varying(60) NOT NULL,
    sigla_uf character(2) NOT NULL,
    reside_desde date,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    bloco character varying(20),
    andar numeric(2,0),
    apartamento numeric(6,0),
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_endereco_externo_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_externo_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_externo_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


--
-- Name: endereco_pessoa; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE endereco_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(20),
    reside_desde date,
    idbai numeric(6,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    bloco character varying(20),
    andar numeric(2,0),
    apartamento numeric(6,0),
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_endereco_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


--
-- Name: escolaridade; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE escolaridade (
    idesco numeric(2,0) NOT NULL,
    descricao character varying(60) NOT NULL
);


--
-- Name: estado_civil; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE estado_civil (
    ideciv numeric(1,0) NOT NULL,
    descricao character varying(15) NOT NULL
);


--
-- Name: fisica; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica (
    idpes numeric(8,0) NOT NULL,
    data_nasc date,
    sexo character(1),
    idpes_mae numeric(8,0),
    idpes_pai numeric(8,0),
    idpes_responsavel numeric(8,0),
    idesco numeric(2,0),
    ideciv numeric(1,0),
    idpes_con numeric(8,0),
    data_uniao date,
    data_obito date,
    nacionalidade numeric(1,0),
    idpais_estrangeiro numeric(3,0),
    data_chegada_brasil date,
    idmun_nascimento numeric(6,0),
    ultima_empresa character varying(150),
    idocup numeric(6,0),
    nome_mae character varying(150),
    nome_pai character varying(150),
    nome_conjuge character varying(150),
    nome_responsavel character varying(150),
    justificativa_provisorio character varying(150),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    ref_cod_sistema integer,
    cpf numeric(11,0),
    ref_cod_religiao integer,
    CONSTRAINT ck_fisica_nacionalidade CHECK (((nacionalidade >= (1)::numeric) AND (nacionalidade <= (3)::numeric))),
    CONSTRAINT ck_fisica_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fisica_sexo CHECK (((sexo = 'M'::bpchar) OR (sexo = 'F'::bpchar)))
);


--
-- Name: fisica_cpf; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica_cpf (
    idpes numeric(8,0) NOT NULL,
    cpf numeric(11,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_fisica_cpf_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_cpf_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: fisica_deficiencia; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica_deficiencia (
    ref_idpes integer NOT NULL,
    ref_cod_deficiencia integer NOT NULL
);


--
-- Name: fisica_foto; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica_foto (
    idpes integer NOT NULL,
    caminho character varying(255)
);


--
-- Name: fisica_raca; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica_raca (
    ref_idpes integer NOT NULL,
    ref_cod_raca integer NOT NULL
);


--
-- Name: fisica_sangue; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fisica_sangue (
    idpes numeric(8,0) NOT NULL,
    grupo character(2) NOT NULL,
    rh smallint NOT NULL
);


--
-- Name: fone_pessoa; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE fone_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    ddd numeric(3,0) NOT NULL,
    fone numeric(11,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_fone_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (4)::numeric)))
);


--
-- Name: funcionario; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE funcionario (
    matricula numeric(8,0) NOT NULL,
    idins integer NOT NULL,
    idset integer,
    idpes numeric(8,0) NOT NULL,
    situacao character(1) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_funcionario_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_funcionario_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_funcionario_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: historico_cartao; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE historico_cartao (
    idpes_cidadao numeric(8,0) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    idpes_emitiu numeric(8,0) NOT NULL,
    tipo character(1) NOT NULL,
    CONSTRAINT ck_historico_cartao_tipo CHECK (((tipo = 'P'::bpchar) OR (tipo = 'D'::bpchar)))
);


--
-- Name: juridica; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE juridica (
    idpes numeric(8,0) NOT NULL,
    cnpj numeric(14,0) NOT NULL,
    insc_estadual numeric(20,0),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    fantasia character varying(255),
    capital_social character varying(255),
    CONSTRAINT ck_juridica_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_juridica_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: ocupacao; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE ocupacao (
    idocup numeric(6,0) NOT NULL,
    descricao character varying(250) NOT NULL
);


--
-- Name: orgao_emissor_rg_idorg_rg_seq; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE orgao_emissor_rg_idorg_rg_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: orgao_emissor_rg; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE orgao_emissor_rg (
    idorg_rg integer DEFAULT nextval('orgao_emissor_rg_idorg_rg_seq'::regclass) NOT NULL,
    sigla character varying(20) NOT NULL,
    descricao character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_orgao_emissor_rg_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: pessoa; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE pessoa (
    idpes numeric(8,0) DEFAULT nextval(('cadastro.seq_pessoa'::text)::regclass) NOT NULL,
    nome character varying(150) NOT NULL,
    idpes_cad numeric(8,0),
    data_cad timestamp without time zone NOT NULL,
    url character varying(60),
    tipo character(1) NOT NULL,
    idpes_rev numeric(8,0),
    data_rev timestamp without time zone,
    email character varying(50),
    situacao character(1) NOT NULL,
    origem_gravacao character(1) NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_pessoa_situacao CHECK ((((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)) OR (situacao = 'P'::bpchar))),
    CONSTRAINT ck_pessoa_tipo CHECK (((tipo = 'F'::bpchar) OR (tipo = 'J'::bpchar)))
);


--
-- Name: pessoa_fonetico; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_fonetico (
    idpes numeric(8,0) NOT NULL,
    fonema character varying(30) NOT NULL
);


--
-- Name: raca_cod_raca_seq; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE raca_cod_raca_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: raca; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE raca (
    cod_raca integer DEFAULT nextval('raca_cod_raca_seq'::regclass) NOT NULL,
    idpes_exc integer,
    idpes_cad integer NOT NULL,
    nm_raca character varying(50) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT false
);


--
-- Name: religiao_cod_religiao_seq; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE religiao_cod_religiao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: religiao; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE religiao (
    cod_religiao integer DEFAULT nextval('religiao_cod_religiao_seq'::regclass) NOT NULL,
    idpes_exc integer,
    idpes_cad integer NOT NULL,
    nm_religiao character varying(50) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT false
);


--
-- Name: seq_pessoa; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE seq_pessoa
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: socio; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE socio (
    idpes_juridica numeric(8,0) NOT NULL,
    idpes_fisica numeric(8,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_socio_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_socio_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


SET search_path = public, pg_catalog;

--
-- Name: bairro; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE bairro (
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    idbai numeric(6,0) DEFAULT nextval(('public.seq_bairro'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_bairro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_bairro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: logradouro; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE logradouro (
    idlog numeric(6,0) DEFAULT nextval(('public.seq_logradouro'::text)::regclass) NOT NULL,
    idtlog character varying(5) NOT NULL,
    nome character varying(150) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    ident_oficial character(1),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_logradouro_ident_oficial CHECK (((ident_oficial = 'S'::bpchar) OR (ident_oficial = 'N'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_logradouro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: municipio; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE municipio (
    idmun numeric(6,0) DEFAULT nextval(('public.seq_municipio'::text)::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    sigla_uf character(2) NOT NULL,
    area_km2 numeric(6,0),
    idmreg numeric(2,0),
    idasmun numeric(2,0),
    cod_ibge numeric(6,0),
    geom character varying,
    tipo character(1) NOT NULL,
    idmun_pai numeric(6,0),
    idpes_rev numeric,
    idpes_cad numeric,
    data_rev timestamp without time zone,
    data_cad timestamp without time zone NOT NULL,
    origem_gravacao character(1) NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_municipio_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_municipio_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_municipio_tipo CHECK (((((tipo = 'D'::bpchar) OR (tipo = 'M'::bpchar)) OR (tipo = 'P'::bpchar)) OR (tipo = 'R'::bpchar)))
);


SET search_path = cadastro, pg_catalog;

--
-- Name: v_endereco; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_endereco AS
    SELECT e.idpes, e.cep, e.idlog, e.numero, e.letra, e.complemento, e.idbai, e.bloco, e.andar, e.apartamento, l.nome AS logradouro, l.idtlog, b.nome AS bairro, m.nome AS cidade, m.sigla_uf FROM endereco_pessoa e, public.logradouro l, public.bairro b, public.municipio m WHERE ((((e.idlog = l.idlog) AND (e.idbai = b.idbai)) AND (b.idmun = m.idmun)) AND (e.tipo = (1)::numeric)) UNION SELECT e.idpes, e.cep, NULL::"unknown" AS idlog, e.numero, e.letra, e.complemento, NULL::"unknown" AS idbai, e.bloco, e.andar, e.apartamento, e.logradouro, e.idtlog, e.bairro, e.cidade, e.sigla_uf FROM endereco_externo e WHERE (e.tipo = (1)::numeric);


--
-- Name: v_fone_pessoa; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_fone_pessoa AS
    SELECT DISTINCT t.idpes, (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS ddd_1, (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS fone_1, (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS ddd_2, (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS fone_2, (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS ddd_mov, (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS fone_mov, (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS ddd_fax, (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS fone_fax FROM fone_pessoa t ORDER BY t.idpes, (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.ddd FROM fone_pessoa t1 WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))), (SELECT t1.fone FROM fone_pessoa t1 WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes)));


--
-- Name: v_pessoa_fisica; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_pessoa_fisica AS
    SELECT p.idpes, p.nome, p.url, p.email, p.situacao, f.data_nasc, f.sexo, f.cpf, f.ref_cod_sistema, f.idesco FROM pessoa p, fisica f WHERE (p.idpes = f.idpes);


--
-- Name: v_pessoa_fisica_simples; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_pessoa_fisica_simples AS
    SELECT p.idpes, (SELECT fisica_cpf.cpf FROM fisica_cpf WHERE (fisica_cpf.idpes = p.idpes)) AS cpf, f.ref_cod_sistema, f.idesco FROM pessoa p, fisica f WHERE (p.idpes = f.idpes);


--
-- Name: v_pessoa_fj; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_pessoa_fj AS
    SELECT p.idpes, p.nome, (SELECT fisica.ref_cod_sistema FROM fisica WHERE (fisica.idpes = p.idpes)) AS ref_cod_sistema, (SELECT juridica.fantasia FROM juridica WHERE (juridica.idpes = p.idpes)) AS fantasia, p.tipo, COALESCE((SELECT fisica.cpf FROM fisica WHERE (fisica.idpes = p.idpes)), (SELECT juridica.cnpj FROM juridica WHERE (juridica.idpes = p.idpes))) AS id_federal FROM pessoa p;


--
-- Name: v_pessoa_juridica; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_pessoa_juridica AS
    SELECT j.idpes, j.fantasia, j.cnpj, j.insc_estadual, j.capital_social, (SELECT pessoa.nome FROM pessoa WHERE (pessoa.idpes = j.idpes)) AS nome FROM juridica j;


--
-- Name: v_pessoafj_count; Type: VIEW; Schema: cadastro; Owner: -
--

CREATE VIEW v_pessoafj_count AS
    SELECT fisica.ref_cod_sistema, fisica.cpf AS id_federal FROM fisica UNION ALL SELECT NULL::"unknown" AS ref_cod_sistema, juridica.cnpj AS id_federal FROM juridica;


SET search_path = consistenciacao, pg_catalog;

--
-- Name: campo_consistenciacao; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE campo_consistenciacao (
    idcam numeric(3,0) NOT NULL,
    campo character varying(50) NOT NULL,
    permite_regra_cadastrada character(1) NOT NULL,
    tamanho_maximo numeric(4,0),
    CONSTRAINT ck_campo_consistenciacao_permite_regra CHECK (((permite_regra_cadastrada = 'S'::bpchar) OR (permite_regra_cadastrada = 'N'::bpchar)))
);


--
-- Name: campo_metadado_id_campo_met_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE campo_metadado_id_campo_met_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: campo_metadado; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE campo_metadado (
    id_campo_met integer DEFAULT nextval('campo_metadado_id_campo_met_seq'::regclass) NOT NULL,
    idmet integer NOT NULL,
    idreg integer,
    idcam numeric(3,0),
    posicao_inicial numeric(5,0),
    posicao_final numeric(5,0),
    posicao_coluna numeric(5,0),
    credibilidade numeric(1,0) NOT NULL,
    data_atualizacao character(1) NOT NULL,
    CONSTRAINT ck_cam_met_campo_cred CHECK (((credibilidade >= (2)::numeric) AND (credibilidade <= (4)::numeric))),
    CONSTRAINT ck_cam_met_data_atualizacao CHECK (((data_atualizacao = 'S'::bpchar) OR (data_atualizacao = 'N'::bpchar)))
);


--
-- Name: confrontacao_idcon_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE confrontacao_idcon_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: confrontacao; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE confrontacao (
    idcon integer DEFAULT nextval('confrontacao_idcon_seq'::regclass) NOT NULL,
    idins integer NOT NULL,
    idpes integer NOT NULL,
    idmet integer NOT NULL,
    arquivo_fonte_dados character varying(250) NOT NULL,
    ignorar_reg_fonte date,
    desconsiderar_reg_cred_maxima date,
    data_hora timestamp without time zone NOT NULL
);


--
-- Name: fonte_idfon_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE fonte_idfon_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: fonte; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE fonte (
    idfon integer DEFAULT nextval('fonte_idfon_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_fonte_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: historico_campo; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE historico_campo (
    idpes numeric(8,0) NOT NULL,
    idcam numeric(3,0) NOT NULL,
    credibilidade numeric(1,0) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    CONSTRAINT ck_historico_campo_cred CHECK (((credibilidade >= (1)::numeric) AND (credibilidade <= (5)::numeric)))
);


--
-- Name: incoerencia_idinc_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE incoerencia_idinc_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: incoerencia; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia (
    idinc integer DEFAULT nextval('incoerencia_idinc_seq'::regclass) NOT NULL,
    idcon integer NOT NULL,
    data_gravacao date NOT NULL,
    ultima_etapa numeric(1,0) NOT NULL,
    cpf_cnpj numeric(14,0),
    nome character varying(150),
    email character varying(100),
    url character varying(60),
    data_nasc character varying(20),
    fantasia character varying(50),
    insc_estadual numeric(10,0),
    sexo character varying(10),
    nome_mae character varying(150),
    nome_pai character varying(150),
    nome_responsavel character varying(150),
    nome_conjuge character varying(150),
    ultima_empresa character varying(150),
    ocupacao character varying(250),
    escolaridade character varying(60),
    estado_civil character varying(15),
    pais_estrangeiro character varying(60),
    data_chegada_brasil character varying(20),
    data_obito character varying(20),
    data_uniao character varying(20)
);


--
-- Name: incoerencia_documento_id_inc_doc_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE incoerencia_documento_id_inc_doc_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: incoerencia_documento; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia_documento (
    id_inc_doc integer DEFAULT nextval('incoerencia_documento_id_inc_doc_seq'::regclass) NOT NULL,
    idinc integer NOT NULL,
    rg numeric(10,0),
    orgao_exp_rg character varying(20),
    data_exp_rg character varying(20),
    sigla_uf_rg_exp character varying(30),
    tipo_cert_civil numeric(2,0),
    num_termo numeric(8,0),
    num_livro numeric(8,0),
    num_folha numeric(4,0),
    data_emissao_cert_civil character varying(20),
    cartorio_cert_civil character varying(150),
    sigla_uf_cert_civil character varying(30),
    num_cart_trabalho numeric(7,0),
    serie_cart_trabalho numeric(5,0),
    data_emissao_cart_trabalho character varying(20),
    sigla_uf_cart_trabalho character varying(30),
    num_tit_eleitor numeric(13,0),
    zona_tit_eleitor numeric(4,0),
    secao_tit_eleitor numeric(4,0)
);


--
-- Name: incoerencia_endereco_id_inc_end_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE incoerencia_endereco_id_inc_end_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: incoerencia_endereco; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia_endereco (
    id_inc_end integer DEFAULT nextval('incoerencia_endereco_id_inc_end_seq'::regclass) NOT NULL,
    idinc integer NOT NULL,
    tipo character varying(60) NOT NULL,
    tipo_logradouro character varying(15),
    logradouro character varying(150),
    numero numeric(6,0),
    letra character(1),
    complemento character varying(20),
    bairro character varying(40),
    cep numeric(8,0),
    cidade character varying(60),
    uf character varying(30),
    CONSTRAINT ck_incoerencia_endereco_tipo CHECK ((((tipo)::text >= ((1)::numeric)::text) AND ((tipo)::text <= ((3)::numeric)::text)))
);


--
-- Name: incoerencia_fone_id_inc_fone_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE incoerencia_fone_id_inc_fone_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: incoerencia_fone; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia_fone (
    id_inc_fone integer DEFAULT nextval('incoerencia_fone_id_inc_fone_seq'::regclass) NOT NULL,
    idinc integer NOT NULL,
    tipo character varying(60) NOT NULL,
    ddd numeric(3,0),
    fone numeric(8,0),
    CONSTRAINT ck_incoerencia_fone_tipo CHECK ((((tipo)::text >= ((1)::numeric)::text) AND ((tipo)::text <= ((4)::numeric)::text)))
);


--
-- Name: incoerencia_pessoa_possivel; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia_pessoa_possivel (
    idinc integer NOT NULL,
    idpes numeric(8,0) NOT NULL
);


--
-- Name: incoerencia_tipo_incoerencia; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE incoerencia_tipo_incoerencia (
    id_tipo_inc numeric(3,0) NOT NULL,
    idinc integer NOT NULL
);


--
-- Name: metadado_idmet_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE metadado_idmet_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: metadado; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE metadado (
    idmet integer DEFAULT nextval('metadado_idmet_seq'::regclass) NOT NULL,
    idfon integer NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    separador character(1),
    CONSTRAINT ck_metadado_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: ocorrencia_regra_campo; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE ocorrencia_regra_campo (
    idreg integer NOT NULL,
    conteudo_padrao character varying(60) NOT NULL,
    ocorrencias text NOT NULL
);


--
-- Name: regra_campo_idreg_seq; Type: SEQUENCE; Schema: consistenciacao; Owner: -
--

CREATE SEQUENCE regra_campo_idreg_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: regra_campo; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE regra_campo (
    idreg integer DEFAULT nextval('regra_campo_idreg_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    tipo character(1) NOT NULL,
    CONSTRAINT ck_regra_campo_tipo CHECK (((tipo = 'S'::bpchar) OR (tipo = 'N'::bpchar)))
);


--
-- Name: temp_cadastro_unificacao_cmf; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE temp_cadastro_unificacao_cmf (
    idpes numeric(8,0) NOT NULL,
    nome character varying(150) NOT NULL,
    cpf_cnpj character varying(14),
    rg character varying(10),
    uf_rg character varying(50),
    data_nascimento character varying(10),
    logradouro character varying(150),
    cep character varying(10),
    bairro character varying(40),
    numero character varying(6),
    cidade_end character varying(60),
    uf_end character varying(2),
    complemento character varying(20),
    fone character varying(14),
    nome_mae character varying(150),
    nome_mae_idpes character varying(150),
    data_cadastro character varying(10),
    data_atualizacao character varying(10),
    situacao character varying(15),
    tipo_pess character varying(1),
    nome_fantasia character varying(50),
    inscr_estadual character varying(10)
);


--
-- Name: temp_cadastro_unificacao_siam; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE temp_cadastro_unificacao_siam (
    idpes numeric(8,0) NOT NULL,
    nome character varying(40) NOT NULL,
    cpf_cnpj character varying(14),
    rg character varying(15),
    logradouro character varying(40),
    cep character varying(10),
    bairro character varying(20),
    cidade_end character varying(20),
    uf_end character varying(2),
    fone character varying(14),
    data_cadastro character varying(10)
);


--
-- Name: tipo_incoerencia; Type: TABLE; Schema: consistenciacao; Owner: -; Tablespace: 
--

CREATE TABLE tipo_incoerencia (
    id_tipo_inc numeric(3,0) NOT NULL,
    idcam numeric(3,0) NOT NULL,
    descricao character varying(250) NOT NULL
);


SET search_path = historico, pg_catalog;

--
-- Name: bairro; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE bairro (
    idbai numeric(6,0) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    nome character varying(80) NOT NULL,
    geom character varying,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_bairro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_bairro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: cep_logradouro; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE cep_logradouro (
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    nroini numeric(6,0),
    nrofin numeric(6,0),
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_cep_logradouro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_cep_logradouro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: cep_logradouro_bairro; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE cep_logradouro_bairro (
    idbai numeric(6,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_cep_logradouro_bairro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_cep_logradouro_bairro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: documento; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE documento (
    idpes numeric(8,0) NOT NULL,
    rg numeric(20,0),
    data_exp_rg date,
    sigla_uf_exp_rg character(2),
    tipo_cert_civil numeric(2,0),
    num_termo numeric(8,0),
    num_livro character varying (8),
    num_folha numeric(4,0),
    data_emissao_cert_civil date,
    sigla_uf_cert_civil character(2),
    cartorio_cert_civil character varying(150),
    num_cart_trabalho numeric(7,0),
    serie_cart_trabalho numeric(5,0),
    data_emissao_cart_trabalho date,
    sigla_uf_cart_trabalho character(2),
    num_tit_eleitor numeric(13,0),
    zona_tit_eleitor numeric(4,0),
    secao_tit_eleitor numeric(4,0),
    idorg_exp_rg integer,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_documento_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_documento_tipo_cert CHECK (((tipo_cert_civil >= (91)::numeric) AND (tipo_cert_civil <= (92)::numeric))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: endereco_externo; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE endereco_externo (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    idtlog character varying(5) NOT NULL,
    logradouro character varying(150) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(20),
    bairro character varying(40),
    cep numeric(8,0),
    cidade character varying(60) NOT NULL,
    sigla_uf character(2) NOT NULL,
    reside_desde date,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_endereco_externo_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_externo_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_externo_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


--
-- Name: endereco_pessoa; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE endereco_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    idbai numeric(6,0) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(20),
    reside_desde date,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_endereco_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


--
-- Name: fisica; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE fisica (
    idpes numeric(8,0) NOT NULL,
    data_nasc date,
    sexo character(1),
    idpes_mae numeric(8,0),
    idpes_pai numeric(8,0),
    idpes_responsavel numeric(8,0),
    idesco numeric(2,0),
    ideciv numeric(1,0),
    idpes_con numeric(8,0),
    data_uniao date,
    data_obito date,
    nacionalidade numeric(1,0),
    idpais_estrangeiro numeric(3,0),
    data_chegada_brasil date,
    idmun_nascimento numeric(6,0),
    ultima_empresa character varying(150),
    idocup numeric(6,0),
    nome_mae character varying(150),
    nome_pai character varying(150),
    nome_conjuge character varying(150),
    nome_responsavel character varying(150),
    justificativa_provisorio character varying(150),
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_fisica_nacionalidade CHECK (((nacionalidade >= (1)::numeric) AND (nacionalidade <= (3)::numeric))),
    CONSTRAINT ck_fisica_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fisica_sexo CHECK (((sexo = 'M'::bpchar) OR (sexo = 'F'::bpchar)))
);


--
-- Name: fisica_cpf; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE fisica_cpf (
    idpes numeric(8,0) NOT NULL,
    cpf numeric(11,0) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_fisica_cpf_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar)))
);


--
-- Name: fone_pessoa; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE fone_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    ddd numeric(3,0) NOT NULL,
    fone numeric(11,0) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_fone_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (4)::numeric)))
);


--
-- Name: funcionario; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE funcionario (
    matricula numeric(8,0) NOT NULL,
    idins integer NOT NULL,
    idset integer,
    idpes numeric(8,0) NOT NULL,
    situacao character(1) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_funcionario_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_funcionario_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: juridica; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE juridica (
    idpes numeric(8,0) NOT NULL,
    cnpj numeric(14,0) NOT NULL,
    insc_estadual numeric(20,0),
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    fantasia character varying(255),
    CONSTRAINT ck_juridica_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_juridica_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: logradouro; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE logradouro (
    idlog numeric(6,0) NOT NULL,
    idtlog character varying(5) NOT NULL,
    nome character varying(150) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    ident_oficial character(1) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_logradouro_ident_oficial CHECK (((ident_oficial = 'S'::bpchar) OR (ident_oficial = 'N'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_logradouro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


--
-- Name: municipio; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE municipio (
    idmun numeric(6,0) NOT NULL,
    nome character varying(60) NOT NULL,
    sigla_uf character(2) NOT NULL,
    area_km2 numeric(6,0),
    idmreg numeric(2,0),
    idasmun numeric(2,0),
    cod_ibge numeric(6,0),
    geom character varying,
    tipo character(1) NOT NULL,
    idmun_pai numeric(6,0),
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_municipio_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_municipio_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_municipio_tipo CHECK (((((tipo = 'D'::bpchar) OR (tipo = 'M'::bpchar)) OR (tipo = 'P'::bpchar)) OR (tipo = 'R'::bpchar)))
);


--
-- Name: pessoa; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE pessoa (
    idpes numeric(8,0) NOT NULL,
    nome character varying(150) NOT NULL,
    idpes_cad numeric(8,0),
    data_cad timestamp without time zone NOT NULL,
    url character varying(60),
    tipo character(1) NOT NULL,
    idpes_rev numeric(8,0),
    data_rev timestamp without time zone,
    email character varying(50),
    situacao character(1) NOT NULL,
    origem_gravacao character(1) NOT NULL,
    idsis_rev numeric,
    idsis_cad numeric NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_pessoa_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_pessoa_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_pessoa_situacao CHECK ((((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)) OR (situacao = 'P'::bpchar))),
    CONSTRAINT ck_pessoa_tipo CHECK (((tipo = 'F'::bpchar) OR (tipo = 'J'::bpchar)))
);


--
-- Name: socio; Type: TABLE; Schema: historico; Owner: -; Tablespace: 
--

CREATE TABLE socio (
    idpes_juridica numeric(8,0) NOT NULL,
    idpes_fisica numeric(8,0) NOT NULL,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_socio_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_socio_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar)))
);


SET search_path = pmiacoes, pg_catalog;

--
-- Name: acao_governo_cod_acao_governo_seq; Type: SEQUENCE; Schema: pmiacoes; Owner: -
--

CREATE SEQUENCE acao_governo_cod_acao_governo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acao_governo; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo (
    cod_acao_governo integer DEFAULT nextval('acao_governo_cod_acao_governo_seq'::regclass) NOT NULL,
    ref_funcionario_exc integer,
    ref_funcionario_cad integer NOT NULL,
    nm_acao character varying(255) NOT NULL,
    descricao text,
    data_inauguracao timestamp without time zone,
    valor double precision,
    destaque smallint DEFAULT (0)::smallint NOT NULL,
    status_acao smallint DEFAULT (0)::smallint NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    numero_acao smallint DEFAULT 0,
    categoria smallint,
    idbai bigint
);


--
-- Name: acao_governo_arquivo_cod_acao_governo_arquivo_seq; Type: SEQUENCE; Schema: pmiacoes; Owner: -
--

CREATE SEQUENCE acao_governo_arquivo_cod_acao_governo_arquivo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acao_governo_arquivo; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_arquivo (
    cod_acao_governo_arquivo integer DEFAULT nextval('acao_governo_arquivo_cod_acao_governo_arquivo_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL,
    nm_arquivo character varying(255) NOT NULL,
    caminho_arquivo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: acao_governo_categoria; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_categoria (
    ref_cod_categoria integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL
);


--
-- Name: acao_governo_foto_cod_acao_governo_foto_seq; Type: SEQUENCE; Schema: pmiacoes; Owner: -
--

CREATE SEQUENCE acao_governo_foto_cod_acao_governo_foto_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acao_governo_foto; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_foto (
    cod_acao_governo_foto integer DEFAULT nextval('acao_governo_foto_cod_acao_governo_foto_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL,
    nm_foto character varying(255) NOT NULL,
    caminho character varying(255) NOT NULL,
    data_foto timestamp without time zone,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: acao_governo_foto_portal; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_foto_portal (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_foto_portal integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: acao_governo_noticia; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_noticia (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_not_portal integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: acao_governo_setor; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE acao_governo_setor (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_setor integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: categoria_cod_categoria_seq; Type: SEQUENCE; Schema: pmiacoes; Owner: -
--

CREATE SEQUENCE categoria_cod_categoria_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: categoria; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE categoria (
    cod_categoria integer DEFAULT nextval('categoria_cod_categoria_seq'::regclass) NOT NULL,
    ref_funcionario_exc integer,
    ref_funcionario_cad integer NOT NULL,
    nm_categoria character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: secretaria_responsavel; Type: TABLE; Schema: pmiacoes; Owner: -; Tablespace: 
--

CREATE TABLE secretaria_responsavel (
    ref_cod_setor integer NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


SET search_path = pmicontrolesis, pg_catalog;

--
-- Name: acontecimento_cod_acontecimento_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE acontecimento_cod_acontecimento_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acontecimento; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE acontecimento (
    cod_acontecimento integer DEFAULT nextval('acontecimento_cod_acontecimento_seq'::regclass) NOT NULL,
    ref_cod_tipo_acontecimento integer NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    titulo character varying(255),
    descricao text,
    dt_inicio timestamp without time zone,
    dt_fim timestamp without time zone,
    hr_inicio time without time zone,
    hr_fim time without time zone,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint,
    "local" character varying,
    contato character varying,
    link character varying
);


--
-- Name: artigo_cod_artigo_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE artigo_cod_artigo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: artigo; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE artigo (
    cod_artigo integer DEFAULT nextval('artigo_cod_artigo_seq'::regclass) NOT NULL,
    texto text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);


--
-- Name: foto_evento_cod_foto_evento_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE foto_evento_cod_foto_evento_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: foto_evento; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE foto_evento (
    cod_foto_evento integer DEFAULT nextval('foto_evento_cod_foto_evento_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_foto timestamp without time zone,
    titulo character varying(255),
    descricao text,
    caminho character varying(255),
    altura integer,
    largura integer,
    nm_credito character varying(255)
);


--
-- Name: foto_vinc_cod_foto_vinc_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE foto_vinc_cod_foto_vinc_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: foto_vinc; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE foto_vinc (
    cod_foto_vinc integer DEFAULT nextval('foto_vinc_cod_foto_vinc_seq'::regclass) NOT NULL,
    ref_cod_acontecimento integer NOT NULL,
    ref_cod_foto_evento integer NOT NULL
);


--
-- Name: itinerario_cod_itinerario_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE itinerario_cod_itinerario_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: itinerario; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE itinerario (
    cod_itinerario integer DEFAULT nextval('itinerario_cod_itinerario_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    numero integer,
    itinerario text,
    retorno text,
    horarios text,
    descricao_horario text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nome character varying(255) NOT NULL
);


--
-- Name: menu_cod_menu_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE menu_cod_menu_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: menu; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE menu (
    cod_menu integer DEFAULT nextval('menu_cod_menu_seq'::regclass) NOT NULL,
    ref_cod_menu_submenu integer,
    ref_cod_menu_pai integer,
    tt_menu character varying(40) NOT NULL,
    ord_menu integer NOT NULL,
    caminho character varying(255),
    alvo character varying(20),
    suprime_menu smallint DEFAULT 1,
    ref_cod_tutormenu integer,
    ref_cod_ico integer
);


--
-- Name: menu_portal_cod_menu_portal_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE menu_portal_cod_menu_portal_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: menu_portal; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE menu_portal (
    cod_menu_portal integer DEFAULT nextval('menu_portal_cod_menu_portal_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nm_menu character varying(15) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    caminho character varying(255),
    cor character varying(255),
    posicao character(1) DEFAULT 'E'::bpchar NOT NULL,
    ordem double precision DEFAULT (0)::double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    expansivel smallint
);


--
-- Name: portais_cod_portais_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE portais_cod_portais_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: portais; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE portais (
    cod_portais integer DEFAULT nextval('portais_cod_portais_seq'::regclass) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    url character varying(255),
    caminho character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint,
    title character varying(255),
    descricao text
);


--
-- Name: servicos_cod_servicos_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE servicos_cod_servicos_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: servicos; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE servicos (
    cod_servicos integer DEFAULT nextval('servicos_cod_servicos_seq'::regclass) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    url character varying(255),
    caminho character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint,
    title character varying(255),
    descricao text
);


--
-- Name: sistema_cod_sistema_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE sistema_cod_sistema_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: sistema; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE sistema (
    cod_sistema integer DEFAULT nextval('sistema_cod_sistema_seq'::regclass) NOT NULL,
    nm_sistema character varying(255) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);


--
-- Name: submenu_portal_cod_submenu_portal_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE submenu_portal_cod_submenu_portal_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: submenu_portal; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE submenu_portal (
    cod_submenu_portal integer DEFAULT nextval('submenu_portal_cod_submenu_portal_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    ref_cod_menu_portal integer DEFAULT 0 NOT NULL,
    nm_submenu character varying(255) DEFAULT ''::character varying NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    target character(1) DEFAULT 'S'::bpchar NOT NULL,
    title text,
    ordem double precision DEFAULT (0)::double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: telefones_cod_telefones_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE telefones_cod_telefones_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: telefones; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE telefones (
    cod_telefones integer DEFAULT nextval('telefones_cod_telefones_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nome character varying(255) NOT NULL,
    numero character varying,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: tipo_acontecimento_cod_tipo_acontecimento_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE tipo_acontecimento_cod_tipo_acontecimento_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_acontecimento; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE tipo_acontecimento (
    cod_tipo_acontecimento integer DEFAULT nextval('tipo_acontecimento_cod_tipo_acontecimento_seq'::regclass) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    nm_tipo character varying(255),
    caminho character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);


--
-- Name: topo_portal_cod_topo_portal_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE topo_portal_cod_topo_portal_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: topo_portal; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE topo_portal (
    cod_topo_portal integer DEFAULT nextval('topo_portal_cod_topo_portal_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    ref_cod_menu_portal integer DEFAULT 0,
    caminho1 character varying(255),
    caminho2 character varying(255),
    caminho3 character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: tutormenu_cod_tutormenu_seq; Type: SEQUENCE; Schema: pmicontrolesis; Owner: -
--

CREATE SEQUENCE tutormenu_cod_tutormenu_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tutormenu; Type: TABLE; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

CREATE TABLE tutormenu (
    cod_tutormenu integer DEFAULT nextval('tutormenu_cod_tutormenu_seq'::regclass) NOT NULL,
    nm_tutormenu character varying(200) NOT NULL
);


SET search_path = pmidrh, pg_catalog;

--
-- Name: diaria_cod_diaria_seq; Type: SEQUENCE; Schema: pmidrh; Owner: -
--

CREATE SEQUENCE diaria_cod_diaria_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: diaria; Type: TABLE; Schema: pmidrh; Owner: -; Tablespace: 
--

CREATE TABLE diaria (
    cod_diaria integer DEFAULT nextval('diaria_cod_diaria_seq'::regclass) NOT NULL,
    ref_funcionario_cadastro integer NOT NULL,
    ref_cod_diaria_grupo integer NOT NULL,
    ref_funcionario integer NOT NULL,
    conta_corrente integer,
    agencia integer,
    banco integer,
    dotacao_orcamentaria character varying(50),
    objetivo text,
    data_partida timestamp without time zone,
    data_chegada timestamp without time zone,
    estadual smallint,
    destino character varying(100),
    data_pedido timestamp without time zone,
    vl100 double precision,
    vl75 double precision,
    vl50 double precision,
    vl25 double precision,
    roteiro integer,
    ativo boolean DEFAULT true,
    ref_cod_setor integer,
    num_diaria numeric(6,0)
);


--
-- Name: diaria_grupo_cod_diaria_grupo_seq; Type: SEQUENCE; Schema: pmidrh; Owner: -
--

CREATE SEQUENCE diaria_grupo_cod_diaria_grupo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: diaria_grupo; Type: TABLE; Schema: pmidrh; Owner: -; Tablespace: 
--

CREATE TABLE diaria_grupo (
    cod_diaria_grupo integer DEFAULT nextval('diaria_grupo_cod_diaria_grupo_seq'::regclass) NOT NULL,
    desc_grupo character varying(255) NOT NULL
);


--
-- Name: diaria_valores_cod_diaria_valores_seq; Type: SEQUENCE; Schema: pmidrh; Owner: -
--

CREATE SEQUENCE diaria_valores_cod_diaria_valores_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: diaria_valores; Type: TABLE; Schema: pmidrh; Owner: -; Tablespace: 
--

CREATE TABLE diaria_valores (
    cod_diaria_valores integer DEFAULT nextval('diaria_valores_cod_diaria_valores_seq'::regclass) NOT NULL,
    ref_funcionario_cadastro integer NOT NULL,
    ref_cod_diaria_grupo integer NOT NULL,
    estadual smallint NOT NULL,
    p100 double precision,
    p75 double precision,
    p50 double precision,
    p25 double precision,
    data_vigencia timestamp without time zone NOT NULL
);


--
-- Name: setor_cod_setor_seq; Type: SEQUENCE; Schema: pmidrh; Owner: -
--

CREATE SEQUENCE setor_cod_setor_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: setor; Type: TABLE; Schema: pmidrh; Owner: -; Tablespace: 
--

CREATE TABLE setor (
    cod_setor integer DEFAULT nextval('setor_cod_setor_seq'::regclass) NOT NULL,
    ref_cod_pessoa_exc integer,
    ref_cod_pessoa_cad integer NOT NULL,
    ref_cod_setor integer,
    nm_setor character varying(255) NOT NULL,
    sgl_setor character varying(15) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nivel smallint DEFAULT (1)::smallint NOT NULL,
    no_paco smallint DEFAULT 1,
    endereco text,
    tipo character(1),
    ref_idpes_resp integer
);


SET search_path = pmieducar, pg_catalog;

--
-- Name: acervo_cod_acervo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_cod_acervo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo (
    cod_acervo integer DEFAULT nextval('acervo_cod_acervo_seq'::regclass) NOT NULL,
    ref_cod_exemplar_tipo integer NOT NULL,
    ref_cod_acervo integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_acervo_colecao integer,
    ref_cod_acervo_idioma integer NOT NULL,
    ref_cod_acervo_editora integer NOT NULL,
    titulo character varying(255) NOT NULL,
    sub_titulo character varying(255),
    cdu character varying(20),
    cutter character varying(20),
    volume integer NOT NULL,
    num_edicao integer NOT NULL,
    ano numeric(4,0) NOT NULL,
    num_paginas integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    isbn numeric(13,0)
);


--
-- Name: acervo_acervo_assunto; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_acervo_assunto (
    ref_cod_acervo integer NOT NULL,
    ref_cod_acervo_assunto integer NOT NULL
);


--
-- Name: acervo_acervo_autor; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_acervo_autor (
    ref_cod_acervo_autor integer NOT NULL,
    ref_cod_acervo integer NOT NULL,
    principal smallint DEFAULT (0)::smallint NOT NULL
);


--
-- Name: acervo_assunto_cod_acervo_assunto_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_assunto_cod_acervo_assunto_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo_assunto; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_assunto (
    cod_acervo_assunto integer DEFAULT nextval('acervo_assunto_cod_acervo_assunto_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_assunto character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: acervo_autor_cod_acervo_autor_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_autor_cod_acervo_autor_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo_autor; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_autor (
    cod_acervo_autor integer DEFAULT nextval('acervo_autor_cod_acervo_autor_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_autor character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);


--
-- Name: acervo_colecao_cod_acervo_colecao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_colecao_cod_acervo_colecao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo_colecao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_colecao (
    cod_acervo_colecao integer DEFAULT nextval('acervo_colecao_cod_acervo_colecao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_colecao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: acervo_editora_cod_acervo_editora_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_editora_cod_acervo_editora_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo_editora; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_editora (
    cod_acervo_editora integer DEFAULT nextval('acervo_editora_cod_acervo_editora_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_idtlog character varying(20) NOT NULL,
    ref_sigla_uf character(2) NOT NULL,
    nm_editora character varying(255) NOT NULL,
    cep numeric(8,0) NOT NULL,
    cidade character varying(60) NOT NULL,
    bairro character varying(60) NOT NULL,
    logradouro character varying(255) NOT NULL,
    numero numeric(6,0),
    telefone integer,
    ddd_telefone numeric(3,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: acervo_idioma_cod_acervo_idioma_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE acervo_idioma_cod_acervo_idioma_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acervo_idioma; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE acervo_idioma (
    cod_acervo_idioma integer DEFAULT nextval('acervo_idioma_cod_acervo_idioma_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_idioma character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: aluno_cod_aluno_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE aluno_cod_aluno_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: aluno; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE aluno (
    cod_aluno integer DEFAULT nextval('aluno_cod_aluno_seq'::regclass) NOT NULL,
    ref_cod_aluno_beneficio integer,
    ref_cod_religiao integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer,
    ref_idpes integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    caminho_foto character varying(255),
    analfabeto smallint DEFAULT (0)::smallint,
    nm_pai character varying(255),
    nm_mae character varying(255),
    tipo_responsavel character(1)
);


--
-- Name: aluno_beneficio_cod_aluno_beneficio_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE aluno_beneficio_cod_aluno_beneficio_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: aluno_beneficio; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE aluno_beneficio (
    cod_aluno_beneficio integer DEFAULT nextval('aluno_beneficio_cod_aluno_beneficio_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_beneficio character varying(255) NOT NULL,
    desc_beneficio text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: ano_letivo_modulo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE ano_letivo_modulo (
    ref_ano integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);


--
-- Name: avaliacao_desempenho; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE avaliacao_desempenho (
    sequencial integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    descricao text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    titulo_avaliacao character varying(255) NOT NULL
);


--
-- Name: biblioteca_cod_biblioteca_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE biblioteca_cod_biblioteca_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: biblioteca; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE biblioteca (
    cod_biblioteca integer DEFAULT nextval('biblioteca_cod_biblioteca_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer,
    ref_cod_escola integer,
    nm_biblioteca character varying(255) NOT NULL,
    valor_multa double precision,
    max_emprestimo integer,
    valor_maximo_multa double precision,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    requisita_senha smallint DEFAULT (0)::smallint NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    dias_espera numeric(2,0),
    tombo_automatico boolean DEFAULT true
);


--
-- Name: biblioteca_dia; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE biblioteca_dia (
    ref_cod_biblioteca integer NOT NULL,
    dia numeric(1,0) NOT NULL
);


--
-- Name: biblioteca_feriados_cod_feriado_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE biblioteca_feriados_cod_feriado_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: biblioteca_feriados; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE biblioteca_feriados (
    cod_feriado integer DEFAULT nextval('biblioteca_feriados_cod_feriado_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    nm_feriado character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_feriado date NOT NULL
);


--
-- Name: biblioteca_usuario; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE biblioteca_usuario (
    ref_cod_biblioteca integer NOT NULL,
    ref_cod_usuario integer NOT NULL
);


--
-- Name: calendario_ano_letivo_cod_calendario_ano_letivo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE calendario_ano_letivo_cod_calendario_ano_letivo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: calendario_ano_letivo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE calendario_ano_letivo (
    cod_calendario_ano_letivo integer DEFAULT nextval('calendario_ano_letivo_cod_calendario_ano_letivo_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    data_cadastra timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: calendario_anotacao_cod_calendario_anotacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE calendario_anotacao_cod_calendario_anotacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: calendario_anotacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE calendario_anotacao (
    cod_calendario_anotacao integer DEFAULT nextval('calendario_anotacao_cod_calendario_anotacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_anotacao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL
);


--
-- Name: calendario_dia; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE calendario_dia (
    ref_cod_calendario_ano_letivo integer NOT NULL,
    mes integer NOT NULL,
    dia integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_calendario_dia_motivo integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    descricao text
);


--
-- Name: calendario_dia_anotacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE calendario_dia_anotacao (
    ref_dia integer NOT NULL,
    ref_mes integer NOT NULL,
    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
    ref_cod_calendario_anotacao integer NOT NULL
);


--
-- Name: calendario_dia_motivo_cod_calendario_dia_motivo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE calendario_dia_motivo_cod_calendario_dia_motivo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: calendario_dia_motivo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE calendario_dia_motivo (
    cod_calendario_dia_motivo integer DEFAULT nextval('calendario_dia_motivo_cod_calendario_dia_motivo_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    sigla character varying(15) NOT NULL,
    descricao text,
    tipo character(1) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_motivo character varying(255) NOT NULL
);


--
-- Name: categoria_nivel_cod_categoria_nivel_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE categoria_nivel_cod_categoria_nivel_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: categoria_nivel; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE categoria_nivel (
    cod_categoria_nivel integer DEFAULT nextval('categoria_nivel_cod_categoria_nivel_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_categoria_nivel character varying(100) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL
);


--
-- Name: cliente_cod_cliente_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE cliente_cod_cliente_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: cliente; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE cliente (
    cod_cliente integer DEFAULT nextval('cliente_cod_cliente_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_idpes integer NOT NULL,
    "login" integer,
    senha character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: cliente_suspensao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE cliente_suspensao (
    sequencial integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_motivo_suspensao integer NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_suspende integer NOT NULL,
    dias integer NOT NULL,
    data_suspensao timestamp without time zone NOT NULL,
    data_liberacao timestamp without time zone
);


--
-- Name: cliente_tipo_cod_cliente_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE cliente_tipo_cod_cliente_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: cliente_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE cliente_tipo (
    cod_cliente_tipo integer DEFAULT nextval('cliente_tipo_cod_cliente_tipo_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

--
-- Name: cliente_tipo_cliente; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE cliente_tipo_cliente (
    ref_cod_cliente_tipo integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ativo smallint DEFAULT (1)::smallint,
    ref_cod_biblioteca integer
);


SET default_with_oids = true;

--
-- Name: cliente_tipo_exemplar_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE cliente_tipo_exemplar_tipo (
    ref_cod_cliente_tipo integer NOT NULL,
    ref_cod_exemplar_tipo integer NOT NULL,
    dias_emprestimo numeric(3,0)
);


--
-- Name: coffebreak_tipo_cod_coffebreak_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE coffebreak_tipo_cod_coffebreak_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: coffebreak_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE coffebreak_tipo (
    cod_coffebreak_tipo integer DEFAULT nextval('coffebreak_tipo_cod_coffebreak_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    custo_unitario double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: curso_cod_curso_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE curso_cod_curso_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: curso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE curso (
    cod_curso integer DEFAULT nextval('curso_cod_curso_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_tipo_regime integer,
    ref_cod_nivel_ensino integer NOT NULL,
    ref_cod_tipo_ensino integer NOT NULL,
    ref_cod_tipo_avaliacao integer,
    nm_curso character varying(255) NOT NULL,
    sgl_curso character varying(15) NOT NULL,
    qtd_etapas smallint NOT NULL,
    frequencia_minima double precision DEFAULT 0.00 NOT NULL,
    media double precision DEFAULT 0.00 NOT NULL,
    media_exame double precision,
    falta_ch_globalizada smallint DEFAULT (0)::smallint NOT NULL,
    carga_horaria double precision NOT NULL,
    ato_poder_publico character varying(255),
    edicao_final smallint DEFAULT (0)::smallint NOT NULL,
    objetivo_curso text,
    publico_alvo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    padrao_ano_escolar smallint DEFAULT (0)::smallint NOT NULL,
    hora_falta double precision DEFAULT 0.00 NOT NULL,
    avaliacao_globalizada boolean DEFAULT false NOT NULL
);


--
-- Name: disciplina_cod_disciplina_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE disciplina_cod_disciplina_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: disciplina; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE disciplina (
    cod_disciplina integer DEFAULT nextval('disciplina_cod_disciplina_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    desc_disciplina text,
    desc_resumida text,
    abreviatura character varying(15) NOT NULL,
    carga_horaria integer NOT NULL,
    apura_falta smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_disciplina character varying(255) NOT NULL,
    ref_cod_curso integer
);


SET default_with_oids = false;

--
-- Name: disciplina_serie; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE disciplina_serie (
    ref_cod_disciplina integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: disciplina_topico_cod_disciplina_topico_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE disciplina_topico_cod_disciplina_topico_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: disciplina_topico; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE disciplina_topico (
    cod_disciplina_topico integer DEFAULT nextval('disciplina_topico_cod_disciplina_topico_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_topico character varying(255) NOT NULL,
    desc_topico text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

--
-- Name: dispensa_disciplina; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE dispensa_disciplina (
    ref_cod_matricula integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_tipo_dispensa integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    observacao text,
    cod_dispensa integer NOT NULL
);


--
-- Name: escola_cod_escola_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE escola_cod_escola_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: escola; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola (
    cod_escola integer DEFAULT nextval('escola_cod_escola_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola_localizacao integer NOT NULL,
    ref_cod_escola_rede_ensino integer NOT NULL,
    ref_idpes integer,
    sigla character varying(20) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: escola_ano_letivo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_ano_letivo (
    ref_cod_escola integer NOT NULL,
    ano integer NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    andamento smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: escola_complemento; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_complemento (
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    cep numeric(8,0) NOT NULL,
    numero numeric(6,0),
    complemento character varying(50),
    email character varying(50),
    nm_escola character varying(255) NOT NULL,
    municipio character varying(60) NOT NULL,
    bairro character varying(40) NOT NULL,
    logradouro character varying(150) NOT NULL,
    ddd_telefone numeric(2,0),
    telefone numeric(11,0),
    ddd_fax numeric(2,0),
    fax numeric(11,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: escola_curso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_curso (
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: escola_localizacao_cod_escola_localizacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE escola_localizacao_cod_escola_localizacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: escola_localizacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_localizacao (
    cod_escola_localizacao integer DEFAULT nextval('escola_localizacao_cod_escola_localizacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_localizacao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: escola_rede_ensino_cod_escola_rede_ensino_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE escola_rede_ensino_cod_escola_rede_ensino_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: escola_rede_ensino; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_rede_ensino (
    cod_escola_rede_ensino integer DEFAULT nextval('escola_rede_ensino_cod_escola_rede_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_rede character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: escola_serie; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_serie (
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    hora_inicial time without time zone NOT NULL,
    hora_final time without time zone NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    hora_inicio_intervalo time without time zone NOT NULL,
    hora_fim_intervalo time without time zone NOT NULL
);


SET default_with_oids = false;

--
-- Name: escola_serie_disciplina; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE escola_serie_disciplina (
    ref_ref_cod_serie integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: exemplar_cod_exemplar_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE exemplar_cod_exemplar_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: exemplar; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE exemplar (
    cod_exemplar integer DEFAULT nextval('exemplar_cod_exemplar_seq'::regclass) NOT NULL,
    ref_cod_fonte integer NOT NULL,
    ref_cod_motivo_baixa integer,
    ref_cod_acervo integer NOT NULL,
    ref_cod_situacao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    permite_emprestimo smallint DEFAULT (1)::smallint NOT NULL,
    preco double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_aquisicao timestamp without time zone,
    tombo integer
);


--
-- Name: exemplar_emprestimo_cod_emprestimo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE exemplar_emprestimo_cod_emprestimo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: exemplar_emprestimo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE exemplar_emprestimo (
    cod_emprestimo integer DEFAULT nextval('exemplar_emprestimo_cod_emprestimo_seq'::regclass) NOT NULL,
    ref_usuario_devolucao integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_exemplar integer NOT NULL,
    data_retirada timestamp without time zone NOT NULL,
    data_devolucao timestamp without time zone,
    valor_multa double precision
);


--
-- Name: exemplar_tipo_cod_exemplar_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE exemplar_tipo_cod_exemplar_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: exemplar_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE exemplar_tipo (
    cod_exemplar_tipo integer DEFAULT nextval('exemplar_tipo_cod_exemplar_tipo_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: falta_aluno_cod_falta_aluno_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE falta_aluno_cod_falta_aluno_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: falta_aluno; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE falta_aluno (
    cod_falta_aluno integer DEFAULT nextval('falta_aluno_cod_falta_aluno_seq'::regclass) NOT NULL,
    ref_cod_disciplina integer,
    ref_cod_escola integer,
    ref_cod_serie integer,
    ref_cod_matricula integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    faltas integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    modulo smallint NOT NULL,
    ref_cod_curso_disciplina integer
);


--
-- Name: falta_atraso_cod_falta_atraso_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE falta_atraso_cod_falta_atraso_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: falta_atraso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE falta_atraso (
    cod_falta_atraso integer DEFAULT nextval('falta_atraso_cod_falta_atraso_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    tipo smallint NOT NULL,
    data_falta_atraso timestamp without time zone NOT NULL,
    qtd_horas integer,
    qtd_min integer,
    justificada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: falta_atraso_compensado_cod_compensado_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE falta_atraso_compensado_cod_compensado_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: falta_atraso_compensado; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE falta_atraso_compensado (
    cod_compensado integer DEFAULT nextval('falta_atraso_compensado_cod_compensado_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_inicio timestamp without time zone NOT NULL,
    data_fim timestamp without time zone NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: faltas_sequencial_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE faltas_sequencial_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: faltas; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE faltas (
    ref_cod_matricula integer NOT NULL,
    sequencial integer DEFAULT nextval('faltas_sequencial_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    falta integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: fonte_cod_fonte_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE fonte_cod_fonte_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: fonte; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE fonte (
    cod_fonte integer DEFAULT nextval('fonte_cod_fonte_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_fonte character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: funcao_cod_funcao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE funcao_cod_funcao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: funcao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE funcao (
    cod_funcao integer DEFAULT nextval('funcao_cod_funcao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_funcao character varying(255) NOT NULL,
    abreviatura character varying(30) NOT NULL,
    professor smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: habilitacao_cod_habilitacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE habilitacao_cod_habilitacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: habilitacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE habilitacao (
    cod_habilitacao integer DEFAULT nextval('habilitacao_cod_habilitacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: habilitacao_curso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE habilitacao_curso (
    ref_cod_habilitacao integer NOT NULL,
    ref_cod_curso integer NOT NULL
);


--
-- Name: historico_disciplinas; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE historico_disciplinas (
    sequencial integer NOT NULL,
    ref_ref_cod_aluno integer NOT NULL,
    ref_sequencial integer NOT NULL,
    nm_disciplina character varying(255) NOT NULL,
    nota character varying(255) NOT NULL,
    faltas integer,
    import numeric(1,0)
);


SET default_with_oids = false;

--
-- Name: historico_educar; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE historico_educar (
    tabela character varying(50),
    alteracao text,
    data timestamp without time zone,
    insercao smallint DEFAULT 0
);


SET default_with_oids = true;

--
-- Name: historico_escolar; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE historico_escolar (
    ref_cod_aluno integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    carga_horaria double precision NOT NULL,
    dias_letivos integer,
    escola character varying(255) NOT NULL,
    escola_cidade character varying(255) NOT NULL,
    escola_uf character(2),
    observacao text,
    aprovado smallint DEFAULT (1)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    faltas_globalizadas integer,
    nm_serie character varying(255),
    origem smallint DEFAULT (1)::smallint,
    extra_curricular smallint DEFAULT (0)::smallint,
    ref_cod_matricula integer,
    ref_cod_instituicao integer,
    import numeric(1,0)
);


--
-- Name: infra_comodo_funcao_cod_infra_comodo_funcao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE infra_comodo_funcao_cod_infra_comodo_funcao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: infra_comodo_funcao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE infra_comodo_funcao (
    cod_infra_comodo_funcao integer DEFAULT nextval('infra_comodo_funcao_cod_infra_comodo_funcao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_funcao character varying(255) NOT NULL,
    desc_funcao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_escola integer
);


--
-- Name: infra_predio_cod_infra_predio_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE infra_predio_cod_infra_predio_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: infra_predio; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE infra_predio (
    cod_infra_predio integer DEFAULT nextval('infra_predio_cod_infra_predio_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    nm_predio character varying(255) NOT NULL,
    desc_predio text,
    endereco text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: infra_predio_comodo_cod_infra_predio_comodo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE infra_predio_comodo_cod_infra_predio_comodo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: infra_predio_comodo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE infra_predio_comodo (
    cod_infra_predio_comodo integer DEFAULT nextval('infra_predio_comodo_cod_infra_predio_comodo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_infra_comodo_funcao integer NOT NULL,
    ref_cod_infra_predio integer NOT NULL,
    nm_comodo character varying(255) NOT NULL,
    desc_comodo text,
    area double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: instituicao_cod_instituicao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE instituicao_cod_instituicao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: instituicao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE instituicao (
    cod_instituicao integer DEFAULT nextval('instituicao_cod_instituicao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_idtlog character varying(20) NOT NULL,
    ref_sigla_uf character(2) NOT NULL,
    cep numeric(8,0) NOT NULL,
    cidade character varying(60) NOT NULL,
    bairro character varying(40) NOT NULL,
    logradouro character varying(255) NOT NULL,
    numero numeric(6,0),
    complemento character varying(50),
    nm_responsavel character varying(255) NOT NULL,
    ddd_telefone numeric(2,0),
    telefone numeric(11,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_instituicao character varying(255) NOT NULL
);


--
-- Name: material_didatico_cod_material_didatico_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE material_didatico_cod_material_didatico_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: material_didatico; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE material_didatico (
    cod_material_didatico integer DEFAULT nextval('material_didatico_cod_material_didatico_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_material_tipo integer NOT NULL,
    nm_material character varying(255) NOT NULL,
    desc_material text,
    custo_unitario double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: material_tipo_cod_material_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE material_tipo_cod_material_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: material_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE material_tipo (
    cod_material_tipo integer DEFAULT nextval('material_tipo_cod_material_tipo_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: matricula_cod_matricula_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE matricula_cod_matricula_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: matricula; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE matricula (
    cod_matricula integer DEFAULT nextval('matricula_cod_matricula_seq'::regclass) NOT NULL,
    ref_cod_reserva_vaga integer,
    ref_ref_cod_escola integer,
    ref_ref_cod_serie integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_aluno integer NOT NULL,
    aprovado smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ano integer NOT NULL,
    ultima_matricula smallint DEFAULT (0)::smallint NOT NULL,
    modulo smallint DEFAULT 1 NOT NULL,
    descricao_reclassificacao text,
    formando smallint DEFAULT (0)::smallint NOT NULL,
    matricula_reclassificacao smallint DEFAULT (0)::smallint,
    ref_cod_curso integer,
    matricula_transferencia boolean DEFAULT false NOT NULL,
    semestre smallint
);


--
-- Name: matricula_excessao_cod_aluno_excessao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE matricula_excessao_cod_aluno_excessao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: matricula_excessao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE matricula_excessao (
    cod_aluno_excessao integer DEFAULT nextval('matricula_excessao_cod_aluno_excessao_seq'::regclass) NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    ref_sequencial integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    reprovado_faltas boolean NOT NULL,
    precisa_exame boolean NOT NULL,
    permite_exame boolean
);


SET default_with_oids = true;

--
-- Name: matricula_ocorrencia_disciplinar; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE matricula_ocorrencia_disciplinar (
    ref_cod_matricula integer NOT NULL,
    ref_cod_tipo_ocorrencia_disciplinar integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    observacao text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: matricula_turma; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE matricula_turma (
    ref_cod_matricula integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: menu_tipo_usuario; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE menu_tipo_usuario (
    ref_cod_tipo_usuario integer NOT NULL,
    ref_cod_menu_submenu integer NOT NULL,
    cadastra smallint DEFAULT 0 NOT NULL,
    visualiza smallint DEFAULT 0 NOT NULL,
    exclui smallint DEFAULT 0 NOT NULL
);


--
-- Name: modulo_cod_modulo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE modulo_cod_modulo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: modulo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE modulo (
    cod_modulo integer DEFAULT nextval('modulo_cod_modulo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    num_meses numeric(2,0) NOT NULL,
    num_semanas integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: motivo_afastamento_cod_motivo_afastamento_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE motivo_afastamento_cod_motivo_afastamento_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: motivo_afastamento; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE motivo_afastamento (
    cod_motivo_afastamento integer DEFAULT nextval('motivo_afastamento_cod_motivo_afastamento_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: motivo_baixa_cod_motivo_baixa_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE motivo_baixa_cod_motivo_baixa_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: motivo_baixa; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE motivo_baixa (
    cod_motivo_baixa integer DEFAULT nextval('motivo_baixa_cod_motivo_baixa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo_baixa character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: motivo_suspensao_cod_motivo_suspensao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE motivo_suspensao_cod_motivo_suspensao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: motivo_suspensao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE motivo_suspensao (
    cod_motivo_suspensao integer DEFAULT nextval('motivo_suspensao_cod_motivo_suspensao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);


--
-- Name: nivel_cod_nivel_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE nivel_cod_nivel_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: nivel; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE nivel (
    cod_nivel integer DEFAULT nextval('nivel_cod_nivel_seq'::regclass) NOT NULL,
    ref_cod_categoria_nivel integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_nivel_anterior integer,
    nm_nivel character varying(100) NOT NULL,
    salario_base double precision,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL
);


--
-- Name: nivel_ensino_cod_nivel_ensino_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE nivel_ensino_cod_nivel_ensino_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: nivel_ensino; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE nivel_ensino (
    cod_nivel_ensino integer DEFAULT nextval('nivel_ensino_cod_nivel_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_nivel character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: nota_aluno_cod_nota_aluno_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE nota_aluno_cod_nota_aluno_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: nota_aluno; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE nota_aluno (
    cod_nota_aluno integer DEFAULT nextval('nota_aluno_cod_nota_aluno_seq'::regclass) NOT NULL,
    ref_cod_disciplina integer,
    ref_cod_escola integer,
    ref_cod_serie integer,
    ref_cod_matricula integer NOT NULL,
    ref_sequencial integer,
    ref_ref_cod_tipo_avaliacao integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    modulo smallint NOT NULL,
    ref_cod_curso_disciplina integer,
    nota double precision
);


--
-- Name: operador_cod_operador_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE operador_cod_operador_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: operador; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE operador (
    cod_operador integer DEFAULT nextval('operador_cod_operador_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nome character varying(50) NOT NULL,
    valor text NOT NULL,
    fim_sentenca smallint DEFAULT (1)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: pagamento_multa_cod_pagamento_multa_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pagamento_multa_cod_pagamento_multa_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pagamento_multa; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE pagamento_multa (
    cod_pagamento_multa integer DEFAULT nextval('pagamento_multa_cod_pagamento_multa_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    valor_pago double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);


--
-- Name: pre_requisito_cod_pre_requisito_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pre_requisito_cod_pre_requisito_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pre_requisito; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE pre_requisito (
    cod_pre_requisito integer DEFAULT nextval('pre_requisito_cod_pre_requisito_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    schema_ character varying(50) NOT NULL,
    tabela character varying(50) NOT NULL,
    nome character varying(50) NOT NULL,
    sql text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: quadro_horario_cod_quadro_horario_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE quadro_horario_cod_quadro_horario_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: quadro_horario; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE quadro_horario (
    cod_quadro_horario integer DEFAULT nextval('quadro_horario_cod_quadro_horario_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

--
-- Name: quadro_horario_horarios; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE quadro_horario_horarios (
    ref_cod_quadro_horario integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_instituicao_substituto integer,
    ref_cod_instituicao_servidor integer NOT NULL,
    ref_servidor_substituto integer,
    ref_servidor integer NOT NULL,
    dia_semana integer NOT NULL,
    hora_inicial time without time zone NOT NULL,
    hora_final time without time zone NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: quadro_horario_horarios_aux; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE quadro_horario_horarios_aux (
    ref_cod_quadro_horario integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_instituicao_servidor integer NOT NULL,
    ref_servidor integer NOT NULL,
    dia_semana integer NOT NULL,
    hora_inicial time without time zone NOT NULL,
    hora_final time without time zone NOT NULL,
    identificador character varying(30),
    data_cadastro timestamp without time zone NOT NULL
);


--
-- Name: religiao_cod_religiao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE religiao_cod_religiao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: religiao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE religiao (
    cod_religiao integer DEFAULT nextval('religiao_cod_religiao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_religiao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: reserva_vaga_cod_reserva_vaga_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE reserva_vaga_cod_reserva_vaga_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: reserva_vaga; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE reserva_vaga (
    cod_reserva_vaga integer DEFAULT nextval('reserva_vaga_cod_reserva_vaga_seq'::regclass) NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    ref_ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_aluno integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_aluno character varying(255),
    cpf_responsavel numeric(11,0)
);


--
-- Name: reservas_cod_reserva_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE reservas_cod_reserva_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: reservas; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE reservas (
    cod_reserva integer DEFAULT nextval('reservas_cod_reserva_seq'::regclass) NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    data_reserva timestamp without time zone,
    data_prevista_disponivel timestamp without time zone,
    data_retirada timestamp without time zone,
    ref_cod_exemplar integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: sequencia_serie; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE sequencia_serie (
    ref_serie_origem integer NOT NULL,
    ref_serie_destino integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: serie_cod_serie_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE serie_cod_serie_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: serie; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE serie (
    cod_serie integer DEFAULT nextval('serie_cod_serie_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    nm_serie character varying(255) NOT NULL,
    etapa_curso integer NOT NULL,
    concluinte smallint DEFAULT (0)::smallint NOT NULL,
    carga_horaria double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    intervalo integer NOT NULL,
    idade_inicial numeric(3,0),
    idade_final numeric(3,0),
    media_especial boolean,
    ultima_nota_define boolean
);


--
-- Name: serie_pre_requisito; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE serie_pre_requisito (
    ref_cod_pre_requisito integer NOT NULL,
    ref_cod_operador integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    valor character varying
);


--
-- Name: servidor; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor (
    cod_servidor integer NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_deficiencia integer,
    ref_idesco numeric(2,0),
    carga_horaria double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_subnivel integer
);


--
-- Name: servidor_afastamento; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_afastamento (
    ref_cod_servidor integer NOT NULL,
    sequencial integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_motivo_afastamento integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    data_retorno timestamp without time zone,
    data_saida timestamp without time zone NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: servidor_alocacao_cod_servidor_alocacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE servidor_alocacao_cod_servidor_alocacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: servidor_alocacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_alocacao (
    cod_servidor_alocacao integer DEFAULT nextval('servidor_alocacao_cod_servidor_alocacao_seq'::regclass) NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    carga_horaria time without time zone,
    periodo smallint DEFAULT (1)::smallint,
    hora_final time without time zone,
    hora_inicial time without time zone,
    dia_semana integer
);


--
-- Name: servidor_curso_cod_servidor_curso_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE servidor_curso_cod_servidor_curso_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: servidor_curso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_curso (
    cod_servidor_curso integer DEFAULT nextval('servidor_curso_cod_servidor_curso_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_conclusao timestamp without time zone NOT NULL,
    data_registro timestamp without time zone,
    diplomas_registros text
);


SET default_with_oids = false;

--
-- Name: servidor_curso_ministra; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_curso_ministra (
    ref_cod_curso integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL
);


--
-- Name: servidor_disciplina; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_disciplina (
    ref_cod_disciplina integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL
);


--
-- Name: servidor_formacao_cod_formacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE servidor_formacao_cod_formacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: servidor_formacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_formacao (
    cod_formacao integer DEFAULT nextval('servidor_formacao_cod_formacao_seq'::regclass) NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    nm_formacao character varying(255) NOT NULL,
    tipo character(1) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

--
-- Name: servidor_funcao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_funcao (
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_cod_funcao integer NOT NULL
);


--
-- Name: servidor_titulo_concurso_cod_servidor_titulo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE servidor_titulo_concurso_cod_servidor_titulo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: servidor_titulo_concurso; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE servidor_titulo_concurso (
    cod_servidor_titulo integer DEFAULT nextval('servidor_titulo_concurso_cod_servidor_titulo_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_vigencia_homolog timestamp without time zone NOT NULL,
    data_publicacao timestamp without time zone NOT NULL
);


--
-- Name: situacao_cod_situacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE situacao_cod_situacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: situacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE situacao (
    cod_situacao integer DEFAULT nextval('situacao_cod_situacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_situacao character varying(255) NOT NULL,
    permite_emprestimo smallint DEFAULT (1)::smallint NOT NULL,
    descricao text,
    situacao_padrao smallint DEFAULT (0)::smallint NOT NULL,
    situacao_emprestada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);


--
-- Name: subnivel_cod_subnivel_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE subnivel_cod_subnivel_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = false;

--
-- Name: subnivel; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE subnivel (
    cod_subnivel integer DEFAULT nextval('subnivel_cod_subnivel_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_subnivel_anterior integer,
    ref_cod_nivel integer NOT NULL,
    nm_subnivel character varying(100),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL,
    salario double precision NOT NULL
);


--
-- Name: tipo_avaliacao_cod_tipo_avaliacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_avaliacao_cod_tipo_avaliacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


SET default_with_oids = true;

--
-- Name: tipo_avaliacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_avaliacao (
    cod_tipo_avaliacao integer DEFAULT nextval('tipo_avaliacao_cod_tipo_avaliacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    conceitual smallint DEFAULT 1,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: tipo_avaliacao_valores; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_avaliacao_valores (
    ref_cod_tipo_avaliacao integer NOT NULL,
    sequencial integer NOT NULL,
    nome character varying(255) NOT NULL,
    valor double precision NOT NULL,
    valor_min double precision NOT NULL,
    valor_max double precision NOT NULL,
    ativo boolean DEFAULT true
);


--
-- Name: tipo_dispensa_cod_tipo_dispensa_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_dispensa_cod_tipo_dispensa_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_dispensa; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_dispensa (
    cod_tipo_dispensa integer DEFAULT nextval('tipo_dispensa_cod_tipo_dispensa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: tipo_ensino_cod_tipo_ensino_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_ensino_cod_tipo_ensino_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_ensino; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_ensino (
    cod_tipo_ensino integer DEFAULT nextval('tipo_ensino_cod_tipo_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_ocorrencia_disciplinar; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_ocorrencia_disciplinar (
    cod_tipo_ocorrencia_disciplinar integer DEFAULT nextval('tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    max_ocorrencias integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: tipo_regime_cod_tipo_regime_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_regime_cod_tipo_regime_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_regime; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_regime (
    cod_tipo_regime integer DEFAULT nextval('tipo_regime_cod_tipo_regime_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


--
-- Name: tipo_usuario_cod_tipo_usuario_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE tipo_usuario_cod_tipo_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: tipo_usuario; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE tipo_usuario (
    cod_tipo_usuario integer DEFAULT nextval('tipo_usuario_cod_tipo_usuario_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    nivel integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: transferencia_solicitacao_cod_transferencia_solicitacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE transferencia_solicitacao_cod_transferencia_solicitacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: transferencia_solicitacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE transferencia_solicitacao (
    cod_transferencia_solicitacao integer DEFAULT nextval('transferencia_solicitacao_cod_transferencia_solicitacao_seq'::regclass) NOT NULL,
    ref_cod_transferencia_tipo integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_matricula_entrada integer,
    ref_cod_matricula_saida integer NOT NULL,
    observacao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_transferencia timestamp without time zone
);


--
-- Name: transferencia_tipo_cod_transferencia_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE transferencia_tipo_cod_transferencia_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: transferencia_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE transferencia_tipo (
    cod_transferencia_tipo integer DEFAULT nextval('transferencia_tipo_cod_transferencia_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_escola integer NOT NULL
);


--
-- Name: turma_cod_turma_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE turma_cod_turma_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: turma; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE turma (
    cod_turma integer DEFAULT nextval('turma_cod_turma_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_ref_cod_serie integer,
    ref_ref_cod_escola integer,
    ref_cod_infra_predio_comodo integer,
    nm_turma character varying(255) NOT NULL,
    sgl_turma character varying(15),
    max_aluno integer NOT NULL,
    multiseriada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_turma_tipo integer NOT NULL,
    hora_inicial time without time zone,
    hora_final time without time zone,
    hora_inicio_intervalo time without time zone,
    hora_fim_intervalo time without time zone,
    ref_cod_regente integer,
    ref_cod_instituicao_regente integer,
    ref_cod_instituicao integer,
    ref_cod_curso integer,
    ref_ref_cod_serie_mult integer,
    ref_ref_cod_escola_mult integer,
    visivel boolean
);


--
-- Name: turma_dia_semana; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE turma_dia_semana (
    dia_semana numeric(1,0) NOT NULL,
    ref_cod_turma integer NOT NULL,
    hora_inicial time without time zone,
    hora_final time without time zone
);


--
-- Name: turma_modulo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE turma_modulo (
    ref_cod_turma integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    sequencial integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);


--
-- Name: turma_tipo_cod_turma_tipo_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE turma_tipo_cod_turma_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: turma_tipo; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE turma_tipo (
    cod_turma_tipo integer DEFAULT nextval('turma_tipo_cod_turma_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    sgl_tipo character varying(15) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer
);


--
-- Name: usuario; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE usuario (
    cod_usuario integer NOT NULL,
    ref_cod_escola integer,
    ref_cod_instituicao integer,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    ref_cod_tipo_usuario integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: v_matricula_matricula_turma; Type: VIEW; Schema: pmieducar; Owner: -
--

CREATE VIEW v_matricula_matricula_turma AS
    SELECT ma.cod_matricula, ma.ref_ref_cod_escola AS ref_cod_escola, ma.ref_ref_cod_serie AS ref_cod_serie, ma.ref_cod_aluno, ma.ref_cod_curso, mt.ref_cod_turma, ma.ano, ma.aprovado, ma.ultima_matricula, ma.modulo, mt.sequencial, ma.ativo, (SELECT count(0) AS count FROM dispensa_disciplina dd WHERE (dd.ref_cod_matricula = ma.cod_matricula)) AS qtd_dispensa_disciplina, (SELECT COALESCE((max(n.modulo))::integer, 0) AS "coalesce" FROM nota_aluno n WHERE ((n.ref_cod_matricula = ma.cod_matricula) AND (n.ativo = 1))) AS maior_modulo_com_nota FROM matricula ma, matricula_turma mt WHERE ((mt.ref_cod_matricula = ma.cod_matricula) AND (mt.ativo = ma.ativo));


SET search_path = pmiotopic, pg_catalog;

--
-- Name: funcionario_su; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE funcionario_su (
    ref_ref_cod_pessoa_fj integer NOT NULL
);


--
-- Name: grupomoderador; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE grupomoderador (
    ref_ref_cod_pessoa_fj integer NOT NULL,
    ref_cod_grupos integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: grupopessoa; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE grupopessoa (
    ref_idpes integer NOT NULL,
    ref_cod_grupos integer NOT NULL,
    ref_grupos_exc integer,
    ref_pessoa_exc integer,
    ref_grupos_cad integer,
    ref_pessoa_cad integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_auxiliar_cad integer,
    ref_ref_cod_atendimento_cad integer
);


--
-- Name: grupos_cod_grupos_seq; Type: SEQUENCE; Schema: pmiotopic; Owner: -
--

CREATE SEQUENCE grupos_cod_grupos_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: grupos; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE grupos (
    cod_grupos integer DEFAULT nextval('grupos_cod_grupos_seq'::regclass) NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nm_grupo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    atendimento smallint DEFAULT 0 NOT NULL
);


--
-- Name: notas; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE notas (
    sequencial integer NOT NULL,
    ref_idpes integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nota text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: participante; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE participante (
    sequencial integer NOT NULL,
    ref_ref_cod_grupos integer NOT NULL,
    ref_ref_idpes integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    data_chegada timestamp without time zone NOT NULL,
    data_saida timestamp without time zone
);


--
-- Name: reuniao_cod_reuniao_seq; Type: SEQUENCE; Schema: pmiotopic; Owner: -
--

CREATE SEQUENCE reuniao_cod_reuniao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: reuniao; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE reuniao (
    cod_reuniao integer DEFAULT nextval('reuniao_cod_reuniao_seq'::regclass) NOT NULL,
    ref_grupos_moderador integer NOT NULL,
    ref_moderador integer NOT NULL,
    data_inicio_marcado timestamp without time zone NOT NULL,
    data_fim_marcado timestamp without time zone NOT NULL,
    data_inicio_real timestamp without time zone,
    data_fim_real timestamp without time zone,
    descricao text NOT NULL,
    email_enviado timestamp without time zone,
    publica smallint DEFAULT 0 NOT NULL
);


--
-- Name: topico_cod_topico_seq; Type: SEQUENCE; Schema: pmiotopic; Owner: -
--

CREATE SEQUENCE topico_cod_topico_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: topico; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE topico (
    cod_topico integer DEFAULT nextval('topico_cod_topico_seq'::regclass) NOT NULL,
    ref_idpes_cad integer NOT NULL,
    ref_cod_grupos_cad integer NOT NULL,
    assunto character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_idpes_exc integer,
    ref_cod_grupos_exc integer
);


--
-- Name: topicoreuniao; Type: TABLE; Schema: pmiotopic; Owner: -; Tablespace: 
--

CREATE TABLE topicoreuniao (
    ref_cod_topico integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    parecer text,
    finalizado smallint,
    data_parecer timestamp without time zone
);


SET search_path = portal, pg_catalog;

--
-- Name: acesso_cod_acesso_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE acesso_cod_acesso_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: acesso; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE acesso (
    cod_acesso integer DEFAULT nextval('acesso_cod_acesso_seq'::regclass) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    ip_externo character varying(15) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255) DEFAULT ''::character varying NOT NULL,
    cod_pessoa integer DEFAULT 0 NOT NULL,
    obs text,
    sucesso boolean DEFAULT true NOT NULL
);


--
-- Name: agenda_cod_agenda_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE agenda_cod_agenda_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: agenda; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE agenda (
    cod_agenda integer DEFAULT nextval('agenda_cod_agenda_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_exc integer,
    ref_ref_cod_pessoa_cad integer NOT NULL,
    nm_agenda character varying NOT NULL,
    publica smallint DEFAULT 0 NOT NULL,
    envia_alerta smallint DEFAULT 0 NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    data_edicao timestamp without time zone,
    ref_ref_cod_pessoa_own integer
);


--
-- Name: agenda_compromisso; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE agenda_compromisso (
    cod_agenda_compromisso integer NOT NULL,
    versao integer NOT NULL,
    ref_cod_agenda integer NOT NULL,
    ref_ref_cod_pessoa_cad integer NOT NULL,
    ativo smallint DEFAULT 1,
    data_inicio timestamp without time zone,
    titulo character varying,
    descricao text,
    importante smallint DEFAULT 0 NOT NULL,
    publico smallint DEFAULT 0 NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_fim timestamp without time zone
);


--
-- Name: agenda_pref_cod_comp_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE agenda_pref_cod_comp_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: agenda_pref; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE agenda_pref (
    cod_comp integer DEFAULT nextval('agenda_pref_cod_comp_seq'::regclass) NOT NULL,
    data_comp date NOT NULL,
    hora_comp time without time zone NOT NULL,
    hora_f_comp time without time zone NOT NULL,
    comp_comp text NOT NULL,
    local_comp character(1) DEFAULT 'I'::bpchar NOT NULL,
    publico_comp character(1) DEFAULT 'S'::bpchar NOT NULL,
    agenda_de character(1) DEFAULT 'P'::bpchar,
    ref_cad integer,
    versao integer DEFAULT 1 NOT NULL,
    ref_auto_cod integer
);


--
-- Name: agenda_responsavel; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE agenda_responsavel (
    ref_cod_agenda integer NOT NULL,
    ref_ref_cod_pessoa_fj integer NOT NULL,
    principal smallint
);


--
-- Name: compras_editais_editais_cod_compras_editais_editais_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_editais_editais_cod_compras_editais_editais_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_editais_editais; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_editais_editais (
    cod_compras_editais_editais integer DEFAULT nextval('compras_editais_editais_cod_compras_editais_editais_seq'::regclass) NOT NULL,
    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
    versao integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    motivo_alteracao text,
    visivel smallint DEFAULT 1 NOT NULL
);


--
-- Name: compras_editais_editais_empresas; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_editais_editais_empresas (
    ref_cod_compras_editais_editais integer DEFAULT 0 NOT NULL,
    ref_cod_compras_editais_empresa integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);


--
-- Name: compras_editais_empresa_cod_compras_editais_empresa_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_editais_empresa_cod_compras_editais_empresa_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_editais_empresa; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_editais_empresa (
    cod_compras_editais_empresa integer DEFAULT nextval('compras_editais_empresa_cod_compras_editais_empresa_seq'::regclass) NOT NULL,
    cnpj character varying(20) DEFAULT ''::character varying NOT NULL,
    nm_empresa character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    endereco text,
    ref_sigla_uf character(2),
    cidade character varying(255),
    bairro character varying(255),
    telefone bigint,
    fax bigint,
    cep bigint,
    nome_contato character varying(255),
    senha character varying(32) DEFAULT ''::character varying NOT NULL
);


--
-- Name: compras_final_pregao_cod_compras_final_pregao_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_final_pregao_cod_compras_final_pregao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_final_pregao; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_final_pregao (
    cod_compras_final_pregao integer DEFAULT nextval('compras_final_pregao_cod_compras_final_pregao_seq'::regclass) NOT NULL,
    nm_final character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: compras_funcionarios; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_funcionarios (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);


--
-- Name: compras_licitacoes_cod_compras_licitacoes_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_licitacoes_cod_compras_licitacoes_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_licitacoes; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_licitacoes (
    cod_compras_licitacoes integer DEFAULT nextval('compras_licitacoes_cod_compras_licitacoes_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    ref_cod_compras_modalidade integer DEFAULT 0 NOT NULL,
    numero character varying(30) DEFAULT ''::character varying NOT NULL,
    objeto text NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    cod_licitacao_semasa integer,
    oculto boolean DEFAULT false
);


--
-- Name: compras_modalidade_cod_compras_modalidade_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_modalidade_cod_compras_modalidade_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_modalidade; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_modalidade (
    cod_compras_modalidade integer DEFAULT nextval('compras_modalidade_cod_compras_modalidade_seq'::regclass) NOT NULL,
    nm_modalidade character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: compras_pregao_execucao_cod_compras_pregao_execucao_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_pregao_execucao_cod_compras_pregao_execucao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_pregao_execucao; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_pregao_execucao (
    cod_compras_pregao_execucao integer DEFAULT nextval('compras_pregao_execucao_cod_compras_pregao_execucao_seq'::regclass) NOT NULL,
    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
    ref_pregoeiro integer DEFAULT 0 NOT NULL,
    ref_equipe1 integer DEFAULT 0 NOT NULL,
    ref_equipe2 integer DEFAULT 0 NOT NULL,
    ref_equipe3 integer DEFAULT 0 NOT NULL,
    ano_processo integer,
    mes_processo integer,
    seq_processo integer,
    seq_portaria integer,
    ano_portaria integer,
    valor_referencia double precision,
    valor_real double precision,
    ref_cod_compras_final_pregao integer
);


--
-- Name: compras_prestacao_contas_cod_compras_prestacao_contas_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE compras_prestacao_contas_cod_compras_prestacao_contas_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: compras_prestacao_contas; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE compras_prestacao_contas (
    cod_compras_prestacao_contas integer DEFAULT nextval('compras_prestacao_contas_cod_compras_prestacao_contas_seq'::regclass) NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    mes integer DEFAULT 0 NOT NULL,
    ano integer DEFAULT 0 NOT NULL
);


--
-- Name: foto_portal_cod_foto_portal_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE foto_portal_cod_foto_portal_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: foto_portal; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE foto_portal (
    cod_foto_portal integer DEFAULT nextval('foto_portal_cod_foto_portal_seq'::regclass) NOT NULL,
    ref_cod_foto_secao integer,
    ref_cod_credito integer,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_foto timestamp without time zone,
    titulo character varying(255),
    descricao text,
    caminho character varying(255),
    altura integer,
    largura integer,
    nm_credito character varying(255),
    bkp_ref_secao bigint
);


--
-- Name: foto_secao_cod_foto_secao_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE foto_secao_cod_foto_secao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: foto_secao; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE foto_secao (
    cod_foto_secao integer DEFAULT nextval('foto_secao_cod_foto_secao_seq'::regclass) NOT NULL,
    nm_secao character varying(255)
);


--
-- Name: funcionario; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE funcionario (
    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    matricula character varying(12),
    senha character varying(32),
    ativo smallint,
    ref_sec integer,
    ramal character varying(10),
    sequencial character(3),
    opcao_menu text,
    ref_cod_setor integer,
    ref_cod_funcionario_vinculo integer,
    tempo_expira_senha integer,
    tempo_expira_conta integer,
    data_troca_senha date,
    data_reativa_conta date,
    ref_ref_cod_pessoa_fj integer,
    proibido integer DEFAULT 0 NOT NULL,
    ref_cod_setor_new integer,
    matricula_new bigint,
    matricula_permanente smallint DEFAULT 0,
    tipo_menu smallint DEFAULT 0 NOT NULL,
    ip_logado character varying(15),
    data_login timestamp without time zone
);


--
-- Name: funcionario_vinculo_cod_funcionario_vinculo_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE funcionario_vinculo_cod_funcionario_vinculo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: funcionario_vinculo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE funcionario_vinculo (
    cod_funcionario_vinculo integer DEFAULT nextval('funcionario_vinculo_cod_funcionario_vinculo_seq'::regclass) NOT NULL,
    nm_vinculo character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: imagem_cod_imagem_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE imagem_cod_imagem_seq
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: imagem; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE imagem (
    cod_imagem integer DEFAULT nextval('imagem_cod_imagem_seq'::regclass) NOT NULL,
    ref_cod_imagem_tipo integer NOT NULL,
    caminho character varying(255) NOT NULL,
    nm_imagem character varying(100),
    extensao character(3) NOT NULL,
    altura integer,
    largura integer,
    data_cadastro timestamp without time zone NOT NULL,
    ref_cod_pessoa_cad integer NOT NULL,
    data_exclusao timestamp without time zone,
    ref_cod_pessoa_exc integer
);


--
-- Name: imagem_tipo_cod_imagem_tipo_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE imagem_tipo_cod_imagem_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: imagem_tipo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE imagem_tipo (
    cod_imagem_tipo integer DEFAULT nextval('imagem_tipo_cod_imagem_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(100) NOT NULL
);


--
-- Name: intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: intranet_segur_permissao_negada; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE intranet_segur_permissao_negada (
    cod_intranet_segur_permissao_negada integer DEFAULT nextval('intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer,
    ip_externo character varying(15) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255),
    data_hora timestamp without time zone NOT NULL,
    pagina character varying(255),
    variaveis text
);


--
-- Name: jor_arquivo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE jor_arquivo (
    ref_cod_jor_edicao integer DEFAULT 0 NOT NULL,
    jor_arquivo smallint DEFAULT (0)::smallint NOT NULL,
    jor_caminho character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: jor_edicao_cod_jor_edicao_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE jor_edicao_cod_jor_edicao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: jor_edicao; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE jor_edicao (
    cod_jor_edicao integer DEFAULT nextval('jor_edicao_cod_jor_edicao_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    jor_ano_edicao character varying(5) DEFAULT ''::character varying NOT NULL,
    jor_edicao integer DEFAULT 0 NOT NULL,
    jor_dt_inicial date NOT NULL,
    jor_dt_final date,
    jor_extra smallint DEFAULT (0)::smallint
);


--
-- Name: mailling_email_cod_mailling_email_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE mailling_email_cod_mailling_email_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: mailling_email; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_email (
    cod_mailling_email integer DEFAULT nextval('mailling_email_cod_mailling_email_seq'::regclass) NOT NULL,
    nm_pessoa character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: mailling_email_conteudo_cod_mailling_email_conteudo_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE mailling_email_conteudo_cod_mailling_email_conteudo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: mailling_email_conteudo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_email_conteudo (
    cod_mailling_email_conteudo integer DEFAULT nextval('mailling_email_conteudo_cod_mailling_email_conteudo_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    conteudo text NOT NULL,
    nm_remetente character varying(255),
    email_remetente character varying(255),
    assunto character varying(255)
);


--
-- Name: mailling_fila_envio_cod_mailling_fila_envio_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE mailling_fila_envio_cod_mailling_fila_envio_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: mailling_fila_envio; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_fila_envio (
    cod_mailling_fila_envio integer DEFAULT nextval('mailling_fila_envio_cod_mailling_fila_envio_seq'::regclass) NOT NULL,
    ref_cod_mailling_email_conteudo integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_email integer,
    ref_ref_cod_pessoa_fj integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_envio timestamp without time zone
);


--
-- Name: mailling_grupo_cod_mailling_grupo_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE mailling_grupo_cod_mailling_grupo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: mailling_grupo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_grupo (
    cod_mailling_grupo integer DEFAULT nextval('mailling_grupo_cod_mailling_grupo_seq'::regclass) NOT NULL,
    nm_grupo character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: mailling_grupo_email; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_grupo_email (
    ref_cod_mailling_email integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL
);


--
-- Name: mailling_historico_cod_mailling_historico_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE mailling_historico_cod_mailling_historico_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: mailling_historico; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE mailling_historico (
    cod_mailling_historico integer DEFAULT nextval('mailling_historico_cod_mailling_historico_seq'::regclass) NOT NULL,
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);


--
-- Name: menu_funcionario; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE menu_funcionario (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    cadastra smallint DEFAULT (0)::smallint NOT NULL,
    exclui smallint DEFAULT (0)::smallint NOT NULL,
    ref_cod_menu_submenu integer DEFAULT 0 NOT NULL
);


--
-- Name: menu_menu_cod_menu_menu_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE menu_menu_cod_menu_menu_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: menu_menu; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE menu_menu (
    cod_menu_menu integer DEFAULT nextval('menu_menu_cod_menu_menu_seq'::regclass) NOT NULL,
    nm_menu character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    ref_cod_menu_pai integer
);


--
-- Name: menu_submenu_cod_menu_submenu_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE menu_submenu_cod_menu_submenu_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: menu_submenu; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE menu_submenu (
    cod_menu_submenu integer DEFAULT nextval('menu_submenu_cod_menu_submenu_seq'::regclass) NOT NULL,
    ref_cod_menu_menu integer,
    cod_sistema integer,
    nm_submenu character varying(100) DEFAULT ''::character varying NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    title text,
    nivel smallint DEFAULT (3)::smallint NOT NULL
);


--
-- Name: not_portal_cod_not_portal_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE not_portal_cod_not_portal_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: not_portal; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE not_portal (
    cod_not_portal integer DEFAULT nextval('not_portal_cod_not_portal_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    titulo character varying(255),
    descricao text,
    data_noticia timestamp without time zone NOT NULL
);


--
-- Name: not_portal_tipo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE not_portal_tipo (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_not_tipo integer DEFAULT 0 NOT NULL
);


--
-- Name: not_tipo_cod_not_tipo_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE not_tipo_cod_not_tipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: not_tipo; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE not_tipo (
    cod_not_tipo integer DEFAULT nextval('not_tipo_cod_not_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Name: not_vinc_portal; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE not_vinc_portal (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    vic_num integer DEFAULT 0 NOT NULL,
    tipo character(1) DEFAULT 'F'::bpchar NOT NULL,
    cod_vinc integer,
    caminho character varying(255),
    nome_arquivo character varying(255)
);


--
-- Name: notificacao_cod_notificacao_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE notificacao_cod_notificacao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: notificacao; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE notificacao (
    cod_notificacao integer DEFAULT nextval('notificacao_cod_notificacao_seq'::regclass) NOT NULL,
    ref_cod_funcionario integer NOT NULL,
    titulo character varying,
    conteudo text,
    data_hora_ativa timestamp without time zone,
    url character varying,
    visualizacoes smallint DEFAULT 0 NOT NULL
);


--
-- Name: pessoa_atividade_cod_pessoa_atividade_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE pessoa_atividade_cod_pessoa_atividade_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pessoa_atividade; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_atividade (
    cod_pessoa_atividade integer DEFAULT nextval('pessoa_atividade_cod_pessoa_atividade_seq'::regclass) NOT NULL,
    ref_cod_ramo_atividade integer DEFAULT 0 NOT NULL,
    nm_atividade character varying(255)
);


--
-- Name: pessoa_fj_cod_pessoa_fj_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE pessoa_fj_cod_pessoa_fj_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pessoa_fj; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_fj (
    cod_pessoa_fj integer DEFAULT nextval('pessoa_fj_cod_pessoa_fj_seq'::regclass) NOT NULL,
    nm_pessoa character varying(255) DEFAULT ''::character varying NOT NULL,
    id_federal character varying(30),
    endereco text,
    cep character varying(9),
    ref_bairro integer,
    ddd_telefone_1 integer,
    telefone_1 character varying(15),
    ddd_telefone_2 integer,
    telefone_2 character varying(15),
    ddd_telefone_mov integer,
    telefone_mov character varying(15),
    ddd_telefone_fax integer,
    telefone_fax character varying(15),
    email character varying(255),
    http character varying(255),
    tipo_pessoa character(1) DEFAULT 'F'::bpchar NOT NULL,
    sexo smallint,
    razao_social character varying(255),
    ins_est character varying(30),
    ins_mun character varying(30),
    rg character varying(30),
    ref_cod_pessoa_pai integer,
    ref_cod_pessoa_mae integer,
    data_nasc date,
    ref_ref_cod_pessoa_fj integer
);


--
-- Name: pessoa_fj_pessoa_atividade; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_fj_pessoa_atividade (
    ref_cod_pessoa_atividade integer DEFAULT 0 NOT NULL,
    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);


--
-- Name: pessoa_ramo_atividade_cod_ramo_atividade_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE pessoa_ramo_atividade_cod_ramo_atividade_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: pessoa_ramo_atividade; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE pessoa_ramo_atividade (
    cod_ramo_atividade integer DEFAULT nextval('pessoa_ramo_atividade_cod_ramo_atividade_seq'::regclass) NOT NULL,
    nm_ramo_atividade character varying(255)
);


--
-- Name: portal_banner_cod_portal_banner_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE portal_banner_cod_portal_banner_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: portal_banner; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE portal_banner (
    cod_portal_banner integer DEFAULT nextval('portal_banner_cod_portal_banner_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    prioridade integer DEFAULT 0 NOT NULL,
    link character varying(255) DEFAULT ''::character varying NOT NULL,
    lateral smallint DEFAULT (1)::smallint NOT NULL
);


--
-- Name: portal_concurso_cod_portal_concurso_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE portal_concurso_cod_portal_concurso_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: portal_concurso; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE portal_concurso (
    cod_portal_concurso integer DEFAULT nextval('portal_concurso_cod_portal_concurso_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    nm_concurso character varying(255) DEFAULT ''::character varying NOT NULL,
    descricao text,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    tipo_arquivo character(3) DEFAULT ''::bpchar NOT NULL,
    data_hora timestamp without time zone
);


--
-- Name: sistema_cod_sistema_seq; Type: SEQUENCE; Schema: portal; Owner: -
--

CREATE SEQUENCE sistema_cod_sistema_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: sistema; Type: TABLE; Schema: portal; Owner: -; Tablespace: 
--

CREATE TABLE sistema (
    cod_sistema integer DEFAULT nextval('sistema_cod_sistema_seq'::regclass) NOT NULL,
    nome character varying(255),
    versao smallint NOT NULL,
    "release" smallint NOT NULL,
    patch smallint NOT NULL,
    tipo character varying(255)
);


--
-- Name: v_funcionario; Type: VIEW; Schema: portal; Owner: -
--

CREATE VIEW v_funcionario AS
    SELECT f.ref_cod_pessoa_fj, f.matricula, f.senha, f.ativo, f.ramal, f.sequencial, f.opcao_menu, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.tempo_expira_senha, f.tempo_expira_conta, f.data_troca_senha, f.data_reativa_conta, f.ref_ref_cod_pessoa_fj, f.proibido, f.ref_cod_setor_new, (SELECT pessoa.nome FROM cadastro.pessoa WHERE (pessoa.idpes = (f.ref_cod_pessoa_fj)::numeric)) AS nome FROM funcionario f;


SET search_path = public, pg_catalog;

--
-- Name: bairro_regiao; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE bairro_regiao (
    ref_cod_regiao integer NOT NULL,
    ref_idbai integer NOT NULL
);


SET default_with_oids = false;

--
-- Name: changelog; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE changelog (
    change_number bigint NOT NULL,
    delta_set character varying(10) NOT NULL,
    start_dt timestamp without time zone NOT NULL,
    complete_dt timestamp without time zone,
    applied_by character varying(100) NOT NULL,
    description character varying(500) NOT NULL
);


SET default_with_oids = true;

--
-- Name: logradouro_fonetico; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE logradouro_fonetico (
    fonema character varying(30) NOT NULL,
    idlog numeric(8,0) NOT NULL
);


--
-- Name: pais; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE pais (
    idpais numeric(3,0) NOT NULL,
    nome character varying(60) NOT NULL,
    geom character varying
);


--
-- Name: regiao_cod_regiao_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE regiao_cod_regiao_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: regiao; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE regiao (
    cod_regiao integer DEFAULT nextval('regiao_cod_regiao_seq'::regclass) NOT NULL,
    nm_regiao character varying(100)
);


--
-- Name: seq_bairro; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_bairro
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: seq_logradouro; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_logradouro
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: seq_municipio; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_municipio
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: setor_idset_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE setor_idset_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


--
-- Name: setor; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE setor (
    idset integer DEFAULT nextval('setor_idset_seq'::regclass) NOT NULL,
    nivel numeric(1,0) NOT NULL,
    nome character varying(100) NOT NULL,
    sigla character varying(25),
    idsetsub integer,
    idsetredir integer,
    situacao character(1) NOT NULL,
    localizacao character(1) NOT NULL,
    CONSTRAINT ck_setor_localizacao CHECK (((localizacao = 'E'::bpchar) OR (localizacao = 'I'::bpchar))),
    CONSTRAINT ck_setor_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


--
-- Name: uf; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE uf (
    sigla_uf character(2) NOT NULL,
    nome character varying(30) NOT NULL,
    geom character varying,
    idpais numeric(3,0)
);


--
-- Name: vila; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE vila (
    idvil numeric(4,0) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    nome character varying(50) NOT NULL,
    geom character varying
);


SET search_path = urbano, pg_catalog;

--
-- Name: cep_logradouro; Type: TABLE; Schema: urbano; Owner: -; Tablespace: 
--

CREATE TABLE cep_logradouro (
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    nroini numeric(6,0),
    nrofin numeric(6,0),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_cep_logradouro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar)))
);


--
-- Name: cep_logradouro_bairro; Type: TABLE; Schema: urbano; Owner: -; Tablespace: 
--

CREATE TABLE cep_logradouro_bairro (
    idlog numeric(6,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idbai numeric(6,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_cep_logradouro_bairro_origem_gravacao CHECK (((((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar)) OR (origem_gravacao = 'C'::bpchar)) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK ((((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar)) OR (operacao = 'E'::bpchar)))
);


--
-- Name: tipo_logradouro; Type: TABLE; Schema: urbano; Owner: -; Tablespace: 
--

CREATE TABLE tipo_logradouro (
    idtlog character varying(5) NOT NULL,
    descricao character varying(40) NOT NULL
);


SET search_path = acesso, pg_catalog;

--
-- Name: pk_funcao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT pk_funcao PRIMARY KEY (idfunc, idsis, idmen);


--
-- Name: pk_grupo; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo
    ADD CONSTRAINT pk_grupo PRIMARY KEY (idgrp);


--
-- Name: pk_grupo_funcao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo_funcao
    ADD CONSTRAINT pk_grupo_funcao PRIMARY KEY (idmen, idsis, idgrp, idfunc);


--
-- Name: pk_grupo_menu; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo_menu
    ADD CONSTRAINT pk_grupo_menu PRIMARY KEY (idgrp, idsis, idmen);


--
-- Name: pk_grupo_operacao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo_operacao
    ADD CONSTRAINT pk_grupo_operacao PRIMARY KEY (idfunc, idgrp, idsis, idmen, idope);


--
-- Name: pk_grupo_sistema; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo_sistema
    ADD CONSTRAINT pk_grupo_sistema PRIMARY KEY (idsis, idgrp);


--
-- Name: pk_historico_senha; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY historico_senha
    ADD CONSTRAINT pk_historico_senha PRIMARY KEY ("login", senha);


--
-- Name: pk_instituicao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY instituicao
    ADD CONSTRAINT pk_instituicao PRIMARY KEY (idins);


--
-- Name: pk_menu; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT pk_menu PRIMARY KEY (idsis, idmen);


--
-- Name: pk_operacao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY operacao
    ADD CONSTRAINT pk_operacao PRIMARY KEY (idope);


--
-- Name: pk_operacao_funcao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY operacao_funcao
    ADD CONSTRAINT pk_operacao_funcao PRIMARY KEY (idmen, idsis, idfunc, idope);


--
-- Name: pk_pessoa_instituicao; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_instituicao
    ADD CONSTRAINT pk_pessoa_instituicao PRIMARY KEY (idins, idpes);


--
-- Name: pk_sistema; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sistema
    ADD CONSTRAINT pk_sistema PRIMARY KEY (idsis);


--
-- Name: pk_usuario; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT pk_usuario PRIMARY KEY ("login");


--
-- Name: pk_usuario_grupo; Type: CONSTRAINT; Schema: acesso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT pk_usuario_grupo PRIMARY KEY (idgrp, "login");


SET search_path = alimentos, pg_catalog;

--
-- Name: pk_baixa_guia_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY baixa_guia_produto
    ADD CONSTRAINT pk_baixa_guia_produto PRIMARY KEY (idbap);


--
-- Name: pk_baixa_guia_remessa; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY baixa_guia_remessa
    ADD CONSTRAINT pk_baixa_guia_remessa PRIMARY KEY (idbai);


--
-- Name: pk_calendario; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario
    ADD CONSTRAINT pk_calendario PRIMARY KEY (idcad);


--
-- Name: pk_cardapio; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cardapio
    ADD CONSTRAINT pk_cardapio PRIMARY KEY (idcar);


--
-- Name: pk_cardapio_faixa_unidade; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cardapio_faixa_unidade
    ADD CONSTRAINT pk_cardapio_faixa_unidade PRIMARY KEY (idfeu, idcar);


--
-- Name: pk_cardapio_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cardapio_produto
    ADD CONSTRAINT pk_cardapio_produto PRIMARY KEY (idcpr);


--
-- Name: pk_cardapio_receita; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cardapio_receita
    ADD CONSTRAINT pk_cardapio_receita PRIMARY KEY (idcar, idrec);


--
-- Name: pk_cliente; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT pk_cliente PRIMARY KEY (idcli);


--
-- Name: pk_contrato; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY contrato
    ADD CONSTRAINT pk_contrato PRIMARY KEY (idcon);


--
-- Name: pk_contrato_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY contrato_produto
    ADD CONSTRAINT pk_contrato_produto PRIMARY KEY (idcop);


--
-- Name: pk_cp_quimico; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY composto_quimico
    ADD CONSTRAINT pk_cp_quimico PRIMARY KEY (idcom);


--
-- Name: pk_evento; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY evento
    ADD CONSTRAINT pk_evento PRIMARY KEY (ideve);


--
-- Name: pk_faixa_composto_quimico; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faixa_composto_quimico
    ADD CONSTRAINT pk_faixa_composto_quimico PRIMARY KEY (idfcp);


--
-- Name: pk_faixa_etaria; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faixa_etaria
    ADD CONSTRAINT pk_faixa_etaria PRIMARY KEY (idfae);


--
-- Name: pk_fornecedor; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fornecedor
    ADD CONSTRAINT pk_fornecedor PRIMARY KEY (idfor);


--
-- Name: pk_grp_quimico; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo_quimico
    ADD CONSTRAINT pk_grp_quimico PRIMARY KEY (idgrpq);


--
-- Name: pk_guia_produto_diario; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY guia_produto_diario
    ADD CONSTRAINT pk_guia_produto_diario PRIMARY KEY (idguiaprodiario);


--
-- Name: pk_guia_remessa; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT pk_guia_remessa PRIMARY KEY (idgui);


--
-- Name: pk_guia_remessa_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY guia_remessa_produto
    ADD CONSTRAINT pk_guia_remessa_produto PRIMARY KEY (idgup);


--
-- Name: pk_log_guia_remessa; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY log_guia_remessa
    ADD CONSTRAINT pk_log_guia_remessa PRIMARY KEY (idlogguia);


--
-- Name: pk_medidas_caseiras; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY medidas_caseiras
    ADD CONSTRAINT pk_medidas_caseiras PRIMARY KEY (idmedcas, idcli);


--
-- Name: pk_pessoa; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);


--
-- Name: pk_prod_cp_quimico; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY produto_composto_quimico
    ADD CONSTRAINT pk_prod_cp_quimico PRIMARY KEY (idpcq);


--
-- Name: pk_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY produto
    ADD CONSTRAINT pk_produto PRIMARY KEY (idpro);


--
-- Name: pk_produto_fornecedor; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY produto_fornecedor
    ADD CONSTRAINT pk_produto_fornecedor PRIMARY KEY (idprf);


--
-- Name: pk_produto_medida_caseira; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY produto_medida_caseira
    ADD CONSTRAINT pk_produto_medida_caseira PRIMARY KEY (idpmc);


--
-- Name: pk_rec_cp_quimico; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY receita_composto_quimico
    ADD CONSTRAINT pk_rec_cp_quimico PRIMARY KEY (idrcq);


--
-- Name: pk_rec_prod; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY receita_produto
    ADD CONSTRAINT pk_rec_prod PRIMARY KEY (idrpr);


--
-- Name: pk_receita; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY receita
    ADD CONSTRAINT pk_receita PRIMARY KEY (idrec);


--
-- Name: pk_tipo_unidade; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_unidade
    ADD CONSTRAINT pk_tipo_unidade PRIMARY KEY (idtip);


--
-- Name: pk_tp_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_produto
    ADD CONSTRAINT pk_tp_produto PRIMARY KEY (idtip);


--
-- Name: pk_tp_refeicao; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_refeicao
    ADD CONSTRAINT pk_tp_refeicao PRIMARY KEY (idtre);


--
-- Name: pk_uni_faixa_etaria; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY unidade_faixa_etaria
    ADD CONSTRAINT pk_uni_faixa_etaria PRIMARY KEY (idfeu);


--
-- Name: pk_uni_produto; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY unidade_produto
    ADD CONSTRAINT pk_uni_produto PRIMARY KEY (idunp, idcli);


--
-- Name: pk_unidade_atendida; Type: CONSTRAINT; Schema: alimentos; Owner: -; Tablespace: 
--

ALTER TABLE ONLY unidade_atendida
    ADD CONSTRAINT pk_unidade_atendida PRIMARY KEY (iduni);


SET search_path = cadastro, pg_catalog;

--
-- Name: fisica_foto_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica_foto
    ADD CONSTRAINT fisica_foto_pkey PRIMARY KEY (idpes);


--
-- Name: fisica_sangue_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica_sangue
    ADD CONSTRAINT fisica_sangue_pkey PRIMARY KEY (idpes);


--
-- Name: pk_aviso_nome; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aviso_nome
    ADD CONSTRAINT pk_aviso_nome PRIMARY KEY (idpes, aviso);


--
-- Name: pk_cadastro_escolaridade; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY deficiencia
    ADD CONSTRAINT pk_cadastro_escolaridade PRIMARY KEY (cod_deficiencia);


--
-- Name: pk_documento; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT pk_documento PRIMARY KEY (idpes);


--
-- Name: pk_endereco_externo; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT pk_endereco_externo PRIMARY KEY (idpes, tipo);


--
-- Name: pk_endereco_pessoa; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT pk_endereco_pessoa PRIMARY KEY (idpes, tipo);


--
-- Name: pk_escolaridade; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escolaridade
    ADD CONSTRAINT pk_escolaridade PRIMARY KEY (idesco);


--
-- Name: pk_estado_civil; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY estado_civil
    ADD CONSTRAINT pk_estado_civil PRIMARY KEY (ideciv);


--
-- Name: pk_fisica; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT pk_fisica PRIMARY KEY (idpes);


--
-- Name: pk_fisica_cpf; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT pk_fisica_cpf PRIMARY KEY (idpes);


--
-- Name: pk_fisica_deficiencia; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica_deficiencia
    ADD CONSTRAINT pk_fisica_deficiencia PRIMARY KEY (ref_idpes, ref_cod_deficiencia);


--
-- Name: pk_fisica_raca; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fisica_raca
    ADD CONSTRAINT pk_fisica_raca PRIMARY KEY (ref_idpes);


--
-- Name: pk_fone_pessoa; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT pk_fone_pessoa PRIMARY KEY (idpes, tipo);


--
-- Name: pk_funcionario; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT pk_funcionario PRIMARY KEY (matricula, idins);


--
-- Name: pk_juridica; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT pk_juridica PRIMARY KEY (idpes);


--
-- Name: pk_ocupacao; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ocupacao
    ADD CONSTRAINT pk_ocupacao PRIMARY KEY (idocup);


--
-- Name: pk_orgao_emissor_rg; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY orgao_emissor_rg
    ADD CONSTRAINT pk_orgao_emissor_rg PRIMARY KEY (idorg_rg);


--
-- Name: pk_pessoa; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);


--
-- Name: pk_pessoa_fonetico; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_fonetico
    ADD CONSTRAINT pk_pessoa_fonetico PRIMARY KEY (fonema, idpes);


--
-- Name: pk_socio; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT pk_socio PRIMARY KEY (idpes_juridica, idpes_fisica);


--
-- Name: raca_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY raca
    ADD CONSTRAINT raca_pkey PRIMARY KEY (cod_raca);


--
-- Name: religiao_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);


SET search_path = consistenciacao, pg_catalog;

--
-- Name: pk_campo_consistenciacao; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY campo_consistenciacao
    ADD CONSTRAINT pk_campo_consistenciacao PRIMARY KEY (idcam);


--
-- Name: pk_campo_metadado; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY campo_metadado
    ADD CONSTRAINT pk_campo_metadado PRIMARY KEY (id_campo_met);


--
-- Name: pk_confrontacao; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY confrontacao
    ADD CONSTRAINT pk_confrontacao PRIMARY KEY (idcon);


--
-- Name: pk_fonte; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fonte
    ADD CONSTRAINT pk_fonte PRIMARY KEY (idfon);


--
-- Name: pk_historico_campo; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY historico_campo
    ADD CONSTRAINT pk_historico_campo PRIMARY KEY (idpes, idcam);


--
-- Name: pk_inc_pessoa_possivel; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia_pessoa_possivel
    ADD CONSTRAINT pk_inc_pessoa_possivel PRIMARY KEY (idinc, idpes);


--
-- Name: pk_incoerencia; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia
    ADD CONSTRAINT pk_incoerencia PRIMARY KEY (idinc);


--
-- Name: pk_incoerencia_documento; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia_documento
    ADD CONSTRAINT pk_incoerencia_documento PRIMARY KEY (id_inc_doc);


--
-- Name: pk_incoerencia_endereco; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia_endereco
    ADD CONSTRAINT pk_incoerencia_endereco PRIMARY KEY (id_inc_end);


--
-- Name: pk_incoerencia_fone; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia_fone
    ADD CONSTRAINT pk_incoerencia_fone PRIMARY KEY (id_inc_fone);


--
-- Name: pk_incoerencia_tipo_incoerencia; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY incoerencia_tipo_incoerencia
    ADD CONSTRAINT pk_incoerencia_tipo_incoerencia PRIMARY KEY (id_tipo_inc, idinc);


--
-- Name: pk_metadado; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY metadado
    ADD CONSTRAINT pk_metadado PRIMARY KEY (idmet);


--
-- Name: pk_ocorrencia_regra_campo; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ocorrencia_regra_campo
    ADD CONSTRAINT pk_ocorrencia_regra_campo PRIMARY KEY (idreg, conteudo_padrao);


--
-- Name: pk_regra_campo; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY regra_campo
    ADD CONSTRAINT pk_regra_campo PRIMARY KEY (idreg);


--
-- Name: pk_temp_cadastro_unificacao_cmf; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY temp_cadastro_unificacao_cmf
    ADD CONSTRAINT pk_temp_cadastro_unificacao_cmf PRIMARY KEY (idpes);


--
-- Name: pk_temp_cadastro_unificacao_siam; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY temp_cadastro_unificacao_siam
    ADD CONSTRAINT pk_temp_cadastro_unificacao_siam PRIMARY KEY (idpes);


--
-- Name: pk_tipo_incoerencia; Type: CONSTRAINT; Schema: consistenciacao; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_incoerencia
    ADD CONSTRAINT pk_tipo_incoerencia PRIMARY KEY (id_tipo_inc);


SET search_path = pmiacoes, pg_catalog;

--
-- Name: acao_governo_arquivo_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_pkey PRIMARY KEY (cod_acao_governo_arquivo);


--
-- Name: acao_governo_categoria_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_pkey PRIMARY KEY (ref_cod_categoria, ref_cod_acao_governo);


--
-- Name: acao_governo_foto_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_pkey PRIMARY KEY (cod_acao_governo_foto);


--
-- Name: acao_governo_foto_portal_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_foto_portal);


--
-- Name: acao_governo_noticia_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_not_portal);


--
-- Name: acao_governo_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo
    ADD CONSTRAINT acao_governo_pkey PRIMARY KEY (cod_acao_governo);


--
-- Name: acao_governo_setor_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_setor);


--
-- Name: categoria_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (cod_categoria);


--
-- Name: secretaria_responsavel_pkey; Type: CONSTRAINT; Schema: pmiacoes; Owner: -; Tablespace: 
--

ALTER TABLE ONLY secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_pkey PRIMARY KEY (ref_cod_setor);


SET search_path = pmicontrolesis, pg_catalog;

--
-- Name: acontecimento_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acontecimento
    ADD CONSTRAINT acontecimento_pkey PRIMARY KEY (cod_acontecimento);


--
-- Name: artigo_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY artigo
    ADD CONSTRAINT artigo_pkey PRIMARY KEY (cod_artigo);


--
-- Name: foto_evento_pk; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY foto_evento
    ADD CONSTRAINT foto_evento_pk PRIMARY KEY (cod_foto_evento);


--
-- Name: foto_vinc_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY foto_vinc
    ADD CONSTRAINT foto_vinc_pkey PRIMARY KEY (cod_foto_vinc);


--
-- Name: itinerario_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY itinerario
    ADD CONSTRAINT itinerario_pkey PRIMARY KEY (cod_itinerario);


--
-- Name: menu_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (cod_menu);


--
-- Name: menu_portal_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_portal
    ADD CONSTRAINT menu_portal_pkey PRIMARY KEY (cod_menu_portal);


--
-- Name: portais_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portais
    ADD CONSTRAINT portais_pkey PRIMARY KEY (cod_portais);


--
-- Name: servicos_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servicos
    ADD CONSTRAINT servicos_pkey PRIMARY KEY (cod_servicos);


--
-- Name: sistema_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sistema
    ADD CONSTRAINT sistema_pkey PRIMARY KEY (cod_sistema);


--
-- Name: submenu_portal_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY submenu_portal
    ADD CONSTRAINT submenu_portal_pkey PRIMARY KEY (cod_submenu_portal);


--
-- Name: telefones_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY telefones
    ADD CONSTRAINT telefones_pkey PRIMARY KEY (cod_telefones);


--
-- Name: tipo_acontecimento_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_acontecimento
    ADD CONSTRAINT tipo_acontecimento_pkey PRIMARY KEY (cod_tipo_acontecimento);


--
-- Name: topo_portal_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY topo_portal
    ADD CONSTRAINT topo_portal_pkey PRIMARY KEY (cod_topo_portal);


--
-- Name: tutormenu_pkey; Type: CONSTRAINT; Schema: pmicontrolesis; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tutormenu
    ADD CONSTRAINT tutormenu_pkey PRIMARY KEY (cod_tutormenu);


SET search_path = pmidrh, pg_catalog;

--
-- Name: diaria_grupo_pkey; Type: CONSTRAINT; Schema: pmidrh; Owner: -; Tablespace: 
--

ALTER TABLE ONLY diaria_grupo
    ADD CONSTRAINT diaria_grupo_pkey PRIMARY KEY (cod_diaria_grupo);


--
-- Name: diaria_pkey; Type: CONSTRAINT; Schema: pmidrh; Owner: -; Tablespace: 
--

ALTER TABLE ONLY diaria
    ADD CONSTRAINT diaria_pkey PRIMARY KEY (cod_diaria);


--
-- Name: diaria_valores_pkey; Type: CONSTRAINT; Schema: pmidrh; Owner: -; Tablespace: 
--

ALTER TABLE ONLY diaria_valores
    ADD CONSTRAINT diaria_valores_pkey PRIMARY KEY (cod_diaria_valores);


--
-- Name: setor_pkey; Type: CONSTRAINT; Schema: pmidrh; Owner: -; Tablespace: 
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT setor_pkey PRIMARY KEY (cod_setor);


SET search_path = pmieducar, pg_catalog;

--
-- Name: acervo_acervo_assunto_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_pkey PRIMARY KEY (ref_cod_acervo, ref_cod_acervo_assunto);


--
-- Name: acervo_acervo_autor_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_pkey PRIMARY KEY (ref_cod_acervo_autor, ref_cod_acervo);


--
-- Name: acervo_assunto_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_assunto
    ADD CONSTRAINT acervo_assunto_pkey PRIMARY KEY (cod_acervo_assunto);


--
-- Name: acervo_autor_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_autor
    ADD CONSTRAINT acervo_autor_pkey PRIMARY KEY (cod_acervo_autor);


--
-- Name: acervo_colecao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_colecao
    ADD CONSTRAINT acervo_colecao_pkey PRIMARY KEY (cod_acervo_colecao);


--
-- Name: acervo_editora_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_pkey PRIMARY KEY (cod_acervo_editora);


--
-- Name: acervo_idioma_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo_idioma
    ADD CONSTRAINT acervo_idioma_pkey PRIMARY KEY (cod_acervo_idioma);


--
-- Name: acervo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_pkey PRIMARY KEY (cod_acervo);


--
-- Name: aluno_beneficio_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_pkey PRIMARY KEY (cod_aluno_beneficio);


--
-- Name: aluno_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_pkey PRIMARY KEY (cod_aluno);


--
-- Name: aluno_ref_idpes_un; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_ref_idpes_un UNIQUE (ref_idpes);


--
-- Name: ano_letivo_modulo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_pkey PRIMARY KEY (ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo);


--
-- Name: avaliacao_desempenho_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_pkey PRIMARY KEY (sequencial, ref_cod_servidor, ref_ref_cod_instituicao);


--
-- Name: biblioteca_dia_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_pkey PRIMARY KEY (ref_cod_biblioteca, dia);


--
-- Name: biblioteca_feriados_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_pkey PRIMARY KEY (cod_feriado);


--
-- Name: biblioteca_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY biblioteca
    ADD CONSTRAINT biblioteca_pkey PRIMARY KEY (cod_biblioteca);


--
-- Name: biblioteca_usuario_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_pkey PRIMARY KEY (ref_cod_biblioteca, ref_cod_usuario);


--
-- Name: calendario_ano_letivo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_pkey PRIMARY KEY (cod_calendario_ano_letivo);


--
-- Name: calendario_anotacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_pkey PRIMARY KEY (cod_calendario_anotacao);


--
-- Name: calendario_dia_anotacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_pkey PRIMARY KEY (ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao);


--
-- Name: calendario_dia_motivo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_pkey PRIMARY KEY (cod_calendario_dia_motivo);


--
-- Name: calendario_dia_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY calendario_dia
    ADD CONSTRAINT calendario_dia_pkey PRIMARY KEY (ref_cod_calendario_ano_letivo, mes, dia);


--
-- Name: categoria_nivel_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY categoria_nivel
    ADD CONSTRAINT categoria_nivel_pkey PRIMARY KEY (cod_categoria_nivel);


--
-- Name: cliente_login_ukey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_login_ukey UNIQUE ("login");


--
-- Name: cliente_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (cod_cliente);


--
-- Name: cliente_ref_idpes_ukey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_ref_idpes_ukey UNIQUE (ref_idpes);


--
-- Name: cliente_suspensao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_pkey PRIMARY KEY (sequencial, ref_cod_cliente, ref_cod_motivo_suspensao);


--
-- Name: cliente_tipo_cliente_pk; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_pk PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_cliente);


--
-- Name: cliente_tipo_exemplar_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_pkey PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_exemplar_tipo);


--
-- Name: cliente_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente_tipo
    ADD CONSTRAINT cliente_tipo_pkey PRIMARY KEY (cod_cliente_tipo);


--
-- Name: cod_dispensa_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT cod_dispensa_pkey PRIMARY KEY (cod_dispensa);


--
-- Name: coffebreak_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_pkey PRIMARY KEY (cod_coffebreak_tipo);


--
-- Name: curso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_pkey PRIMARY KEY (cod_curso);


--
-- Name: disciplina_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY disciplina
    ADD CONSTRAINT disciplina_pkey PRIMARY KEY (cod_disciplina);


--
-- Name: disciplina_serie_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY disciplina_serie
    ADD CONSTRAINT disciplina_serie_pkey PRIMARY KEY (ref_cod_disciplina, ref_cod_serie);


--
-- Name: disciplina_topico_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY disciplina_topico
    ADD CONSTRAINT disciplina_topico_pkey PRIMARY KEY (cod_disciplina_topico);


--
-- Name: escola_ano_letivo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_pkey PRIMARY KEY (ref_cod_escola, ano);


--
-- Name: escola_complemento_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_complemento
    ADD CONSTRAINT escola_complemento_pkey PRIMARY KEY (ref_cod_escola);


--
-- Name: escola_curso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_curso
    ADD CONSTRAINT escola_curso_pkey PRIMARY KEY (ref_cod_escola, ref_cod_curso);


--
-- Name: escola_localizacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_localizacao
    ADD CONSTRAINT escola_localizacao_pkey PRIMARY KEY (cod_escola_localizacao);


--
-- Name: escola_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);


--
-- Name: escola_rede_ensino_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_pkey PRIMARY KEY (cod_escola_rede_ensino);


--
-- Name: escola_serie_disciplina_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_pkey PRIMARY KEY (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina);


--
-- Name: escola_serie_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escola_serie
    ADD CONSTRAINT escola_serie_pkey PRIMARY KEY (ref_cod_escola, ref_cod_serie);


--
-- Name: exemplar_emprestimo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_pkey PRIMARY KEY (cod_emprestimo);


--
-- Name: exemplar_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_pkey PRIMARY KEY (cod_exemplar);


--
-- Name: exemplar_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_pkey PRIMARY KEY (cod_exemplar_tipo);


--
-- Name: falta_aluno_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY falta_aluno
    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (cod_falta_aluno);


--
-- Name: falta_atraso_compensado_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_pkey PRIMARY KEY (cod_compensado);


--
-- Name: falta_atraso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY falta_atraso
    ADD CONSTRAINT falta_atraso_pkey PRIMARY KEY (cod_falta_atraso);


--
-- Name: faltas_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faltas
    ADD CONSTRAINT faltas_pkey PRIMARY KEY (ref_cod_matricula, sequencial);


--
-- Name: fonte_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fonte
    ADD CONSTRAINT fonte_pkey PRIMARY KEY (cod_fonte);


--
-- Name: funcao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT funcao_pkey PRIMARY KEY (cod_funcao);


--
-- Name: habilitacao_curso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_pkey PRIMARY KEY (ref_cod_habilitacao, ref_cod_curso);


--
-- Name: habilitacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY habilitacao
    ADD CONSTRAINT habilitacao_pkey PRIMARY KEY (cod_habilitacao);


--
-- Name: historico_disciplinas_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_pkey PRIMARY KEY (sequencial, ref_ref_cod_aluno, ref_sequencial);


--
-- Name: historico_escolar_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY historico_escolar
    ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY (ref_cod_aluno, sequencial);


--
-- Name: infra_comodo_funcao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_pkey PRIMARY KEY (cod_infra_comodo_funcao);


--
-- Name: infra_predio_comodo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_pkey PRIMARY KEY (cod_infra_predio_comodo);


--
-- Name: infra_predio_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infra_predio
    ADD CONSTRAINT infra_predio_pkey PRIMARY KEY (cod_infra_predio);


--
-- Name: instituicao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY instituicao
    ADD CONSTRAINT instituicao_pkey PRIMARY KEY (cod_instituicao);


--
-- Name: material_didatico_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY material_didatico
    ADD CONSTRAINT material_didatico_pkey PRIMARY KEY (cod_material_didatico);


--
-- Name: material_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY material_tipo
    ADD CONSTRAINT material_tipo_pkey PRIMARY KEY (cod_material_tipo);


--
-- Name: matricula_excessao_pk; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY matricula_excessao
    ADD CONSTRAINT matricula_excessao_pk PRIMARY KEY (cod_aluno_excessao);


--
-- Name: matricula_ocorrencia_disciplinar_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_tipo_ocorrencia_disciplinar, sequencial);


--
-- Name: matricula_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_pkey PRIMARY KEY (cod_matricula);


--
-- Name: matricula_turma_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY matricula_turma
    ADD CONSTRAINT matricula_turma_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_turma, sequencial);


--
-- Name: menu_tipo_usuario_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_pkey PRIMARY KEY (ref_cod_tipo_usuario, ref_cod_menu_submenu);


--
-- Name: modulo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modulo
    ADD CONSTRAINT modulo_pkey PRIMARY KEY (cod_modulo);


--
-- Name: motivo_afastamento_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_pkey PRIMARY KEY (cod_motivo_afastamento);


--
-- Name: motivo_baixa_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY motivo_baixa
    ADD CONSTRAINT motivo_baixa_pkey PRIMARY KEY (cod_motivo_baixa);


--
-- Name: motivo_suspensao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_pkey PRIMARY KEY (cod_motivo_suspensao);


--
-- Name: nivel_ensino_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY nivel_ensino
    ADD CONSTRAINT nivel_ensino_pkey PRIMARY KEY (cod_nivel_ensino);


--
-- Name: nivel_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY nivel
    ADD CONSTRAINT nivel_pkey PRIMARY KEY (cod_nivel);


--
-- Name: nota_aluno_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (cod_nota_aluno);


--
-- Name: operador_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY operador
    ADD CONSTRAINT operador_pkey PRIMARY KEY (cod_operador);


--
-- Name: pagamento_multa_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pagamento_multa
    ADD CONSTRAINT pagamento_multa_pkey PRIMARY KEY (cod_pagamento_multa);


--
-- Name: pre_requisito_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pre_requisito
    ADD CONSTRAINT pre_requisito_pkey PRIMARY KEY (cod_pre_requisito);


--
-- Name: quadro_horario_horarios_aux_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_aux_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);


--
-- Name: quadro_horario_horarios_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);


--
-- Name: quadro_horario_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quadro_horario
    ADD CONSTRAINT quadro_horario_pkey PRIMARY KEY (cod_quadro_horario);


--
-- Name: religiao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);


--
-- Name: reserva_vaga_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reserva_vaga
    ADD CONSTRAINT reserva_vaga_pkey PRIMARY KEY (cod_reserva_vaga);


--
-- Name: reservas_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (cod_reserva);


--
-- Name: sequencia_serie_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sequencia_serie
    ADD CONSTRAINT sequencia_serie_pkey PRIMARY KEY (ref_serie_origem, ref_serie_destino);


--
-- Name: serie_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY serie
    ADD CONSTRAINT serie_pkey PRIMARY KEY (cod_serie);


--
-- Name: serie_pre_requisito_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_pkey PRIMARY KEY (ref_cod_pre_requisito, ref_cod_operador, ref_cod_serie);


--
-- Name: servidor_afastamento_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_pkey PRIMARY KEY (ref_cod_servidor, sequencial, ref_ref_cod_instituicao);


--
-- Name: servidor_alocacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_pkey PRIMARY KEY (cod_servidor_alocacao);


--
-- Name: servidor_curso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_curso
    ADD CONSTRAINT servidor_curso_pkey PRIMARY KEY (cod_servidor_curso);


--
-- Name: servidor_cuso_ministra_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_pkey PRIMARY KEY (ref_cod_curso, ref_ref_cod_instituicao, ref_cod_servidor);


--
-- Name: servidor_disciplina_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_pkey PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor);


--
-- Name: servidor_formacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_formacao
    ADD CONSTRAINT servidor_formacao_pkey PRIMARY KEY (cod_formacao);


--
-- Name: servidor_funcao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_funcao
    ADD CONSTRAINT servidor_funcao_pkey PRIMARY KEY (ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_funcao);


--
-- Name: servidor_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_pkey PRIMARY KEY (cod_servidor, ref_cod_instituicao);


--
-- Name: servidor_titulo_concurso_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_pkey PRIMARY KEY (cod_servidor_titulo);


--
-- Name: situacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY situacao
    ADD CONSTRAINT situacao_pkey PRIMARY KEY (cod_situacao);


--
-- Name: subnivel_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY subnivel
    ADD CONSTRAINT subnivel_pkey PRIMARY KEY (cod_subnivel);


--
-- Name: tipo_avaliacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_pkey PRIMARY KEY (cod_tipo_avaliacao);


--
-- Name: tipo_avaliacao_valores_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_pkey PRIMARY KEY (ref_cod_tipo_avaliacao, sequencial);


--
-- Name: tipo_dispensa_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_pkey PRIMARY KEY (cod_tipo_dispensa);


--
-- Name: tipo_ensino_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_ensino
    ADD CONSTRAINT tipo_ensino_pkey PRIMARY KEY (cod_tipo_ensino);


--
-- Name: tipo_ocorrencia_disciplinar_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_pkey PRIMARY KEY (cod_tipo_ocorrencia_disciplinar);


--
-- Name: tipo_regime_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_regime
    ADD CONSTRAINT tipo_regime_pkey PRIMARY KEY (cod_tipo_regime);


--
-- Name: tipo_usuario_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_usuario
    ADD CONSTRAINT tipo_usuario_pkey PRIMARY KEY (cod_tipo_usuario);


--
-- Name: transferencia_solicitacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_pkey PRIMARY KEY (cod_transferencia_solicitacao);


--
-- Name: transferencia_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_pkey PRIMARY KEY (cod_transferencia_tipo);


--
-- Name: turma_dia_semana_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY turma_dia_semana
    ADD CONSTRAINT turma_dia_semana_pkey PRIMARY KEY (dia_semana, ref_cod_turma);


--
-- Name: turma_modulo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY turma_modulo
    ADD CONSTRAINT turma_modulo_pkey PRIMARY KEY (ref_cod_turma, ref_cod_modulo, sequencial);


--
-- Name: turma_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_pkey PRIMARY KEY (cod_turma);


--
-- Name: turma_tipo_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY turma_tipo
    ADD CONSTRAINT turma_tipo_pkey PRIMARY KEY (cod_turma_tipo);


--
-- Name: usuario_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (cod_usuario);


SET search_path = pmiotopic, pg_catalog;

--
-- Name: funcionario_su_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcionario_su
    ADD CONSTRAINT funcionario_su_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj);


--
-- Name: grupomoderador_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupomoderador
    ADD CONSTRAINT grupomoderador_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_grupos);


--
-- Name: grupopessoa_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupopessoa
    ADD CONSTRAINT grupopessoa_pkey PRIMARY KEY (ref_idpes, ref_cod_grupos);


--
-- Name: grupos_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupos
    ADD CONSTRAINT grupos_pkey PRIMARY KEY (cod_grupos);


--
-- Name: notas_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY notas
    ADD CONSTRAINT notas_pkey PRIMARY KEY (sequencial, ref_idpes);


--
-- Name: participante_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY participante
    ADD CONSTRAINT participante_pkey PRIMARY KEY (sequencial, ref_ref_cod_grupos, ref_ref_idpes, ref_cod_reuniao);


--
-- Name: reuniao_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reuniao
    ADD CONSTRAINT reuniao_pkey PRIMARY KEY (cod_reuniao);


--
-- Name: topico_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY topico
    ADD CONSTRAINT topico_pkey PRIMARY KEY (cod_topico);


--
-- Name: topicoreuniao_pkey; Type: CONSTRAINT; Schema: pmiotopic; Owner: -; Tablespace: 
--

ALTER TABLE ONLY topicoreuniao
    ADD CONSTRAINT topicoreuniao_pkey PRIMARY KEY (ref_cod_topico, ref_cod_reuniao);


SET search_path = portal, pg_catalog;

--
-- Name: acesso_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acesso
    ADD CONSTRAINT acesso_pk PRIMARY KEY (cod_acesso);


--
-- Name: agenda_compromisso_pkey; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_pkey PRIMARY KEY (cod_agenda_compromisso, versao, ref_cod_agenda);


--
-- Name: agenda_pkey; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (cod_agenda);


--
-- Name: agenda_pref_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY agenda_pref
    ADD CONSTRAINT agenda_pref_pk PRIMARY KEY (cod_comp);


--
-- Name: agenda_responsavel_pkey; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_pkey PRIMARY KEY (ref_cod_agenda, ref_ref_cod_pessoa_fj);


--
-- Name: compras_editais_editais_empresas_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_pk PRIMARY KEY (ref_cod_compras_editais_editais, ref_cod_compras_editais_empresa, data_hora);


--
-- Name: compras_editais_editais_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_pk PRIMARY KEY (cod_compras_editais_editais);


--
-- Name: compras_editais_empresa_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_pk PRIMARY KEY (cod_compras_editais_empresa);


--
-- Name: compras_final_pregao_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_final_pregao
    ADD CONSTRAINT compras_final_pregao_pk PRIMARY KEY (cod_compras_final_pregao);


--
-- Name: compras_funcionarios_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_pk PRIMARY KEY (ref_ref_cod_pessoa_fj);


--
-- Name: compras_licitacoes_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_pk PRIMARY KEY (cod_compras_licitacoes);


--
-- Name: compras_modalidade_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_modalidade
    ADD CONSTRAINT compras_modalidade_pk PRIMARY KEY (cod_compras_modalidade);


--
-- Name: compras_pregao_execucao_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_pk PRIMARY KEY (cod_compras_pregao_execucao);


--
-- Name: compras_prestacao_contas_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compras_prestacao_contas
    ADD CONSTRAINT compras_prestacao_contas_pk PRIMARY KEY (cod_compras_prestacao_contas);


--
-- Name: foto_portal_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY foto_portal
    ADD CONSTRAINT foto_portal_pk PRIMARY KEY (cod_foto_portal);


--
-- Name: foto_secao_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY foto_secao
    ADD CONSTRAINT foto_secao_pk PRIMARY KEY (cod_foto_secao);


--
-- Name: funcionario_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT funcionario_pk PRIMARY KEY (ref_cod_pessoa_fj);


--
-- Name: funcionario_vinculo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY funcionario_vinculo
    ADD CONSTRAINT funcionario_vinculo_pk PRIMARY KEY (cod_funcionario_vinculo);


--
-- Name: imagem_pkey; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY imagem
    ADD CONSTRAINT imagem_pkey PRIMARY KEY (cod_imagem);


--
-- Name: imagem_tipo_pkey; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY imagem_tipo
    ADD CONSTRAINT imagem_tipo_pkey PRIMARY KEY (cod_imagem_tipo);


--
-- Name: intranet_segur_permissao_negada_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_pk PRIMARY KEY (cod_intranet_segur_permissao_negada);


--
-- Name: jor_arquivo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jor_arquivo
    ADD CONSTRAINT jor_arquivo_pk PRIMARY KEY (ref_cod_jor_edicao, jor_arquivo);


--
-- Name: jor_edicao_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jor_edicao
    ADD CONSTRAINT jor_edicao_pk PRIMARY KEY (cod_jor_edicao);


--
-- Name: mailling_email_conteudo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_pk PRIMARY KEY (cod_mailling_email_conteudo);


--
-- Name: mailling_email_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_email
    ADD CONSTRAINT mailling_email_pk PRIMARY KEY (cod_mailling_email);


--
-- Name: mailling_fila_envio_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_pk PRIMARY KEY (cod_mailling_fila_envio);


--
-- Name: mailling_grupo_email_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_pk PRIMARY KEY (ref_cod_mailling_email, ref_cod_mailling_grupo);


--
-- Name: mailling_grupo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_grupo
    ADD CONSTRAINT mailling_grupo_pk PRIMARY KEY (cod_mailling_grupo);


--
-- Name: mailling_historico_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mailling_historico
    ADD CONSTRAINT mailling_historico_pk PRIMARY KEY (cod_mailling_historico);


--
-- Name: menu_funcionario_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_funcionario
    ADD CONSTRAINT menu_funcionario_pk PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_menu_submenu);


--
-- Name: menu_menu_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_menu
    ADD CONSTRAINT menu_menu_pk PRIMARY KEY (cod_menu_menu);


--
-- Name: menu_submenu_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_submenu
    ADD CONSTRAINT menu_submenu_pk PRIMARY KEY (cod_menu_submenu);


--
-- Name: not_portal_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY not_portal
    ADD CONSTRAINT not_portal_pk PRIMARY KEY (cod_not_portal);


--
-- Name: not_portal_tipo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_pk PRIMARY KEY (ref_cod_not_portal, ref_cod_not_tipo);


--
-- Name: not_tipo_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY not_tipo
    ADD CONSTRAINT not_tipo_pk PRIMARY KEY (cod_not_tipo);


--
-- Name: not_vinc_portal_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_pk PRIMARY KEY (ref_cod_not_portal, vic_num);


--
-- Name: pessoa_atividade_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_pk PRIMARY KEY (cod_pessoa_atividade);


--
-- Name: pessoa_fj_pessoa_atividade_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_pk PRIMARY KEY (ref_cod_pessoa_atividade, ref_cod_pessoa_fj);


--
-- Name: pessoa_fj_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_fj
    ADD CONSTRAINT pessoa_fj_pk PRIMARY KEY (cod_pessoa_fj);


--
-- Name: pessoa_ramo_atividade_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pessoa_ramo_atividade
    ADD CONSTRAINT pessoa_ramo_atividade_pk PRIMARY KEY (cod_ramo_atividade);


--
-- Name: portal_banner_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portal_banner
    ADD CONSTRAINT portal_banner_pk PRIMARY KEY (cod_portal_banner);


--
-- Name: portal_concurso_pk; Type: CONSTRAINT; Schema: portal; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portal_concurso
    ADD CONSTRAINT portal_concurso_pk PRIMARY KEY (cod_portal_concurso);


SET search_path = public, pg_catalog;

--
-- Name: bairro_regiao_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY bairro_regiao
    ADD CONSTRAINT bairro_regiao_pkey PRIMARY KEY (ref_cod_regiao, ref_idbai);


--
-- Name: pk_bairro; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT pk_bairro PRIMARY KEY (idbai);


--
-- Name: pk_logradouro; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT pk_logradouro PRIMARY KEY (idlog);


--
-- Name: pk_logradouro_fonetico; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY logradouro_fonetico
    ADD CONSTRAINT pk_logradouro_fonetico PRIMARY KEY (fonema, idlog);


--
-- Name: pk_municipio; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY municipio
    ADD CONSTRAINT pk_municipio PRIMARY KEY (idmun);


--
-- Name: pk_pais; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pais
    ADD CONSTRAINT pk_pais PRIMARY KEY (idpais);


--
-- Name: pk_setor; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT pk_setor PRIMARY KEY (idset);


--
-- Name: pk_uf; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY uf
    ADD CONSTRAINT pk_uf PRIMARY KEY (sigla_uf);


--
-- Name: pk_vila; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY vila
    ADD CONSTRAINT pk_vila PRIMARY KEY (idvil);


--
-- Name: pkchangelog; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY changelog
    ADD CONSTRAINT pkchangelog PRIMARY KEY (change_number, delta_set);


--
-- Name: regiao_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY regiao
    ADD CONSTRAINT regiao_pkey PRIMARY KEY (cod_regiao);


SET search_path = urbano, pg_catalog;

--
-- Name: pk_cep_logradouro; Type: CONSTRAINT; Schema: urbano; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT pk_cep_logradouro PRIMARY KEY (cep, idlog);


--
-- Name: pk_cep_logradouro_bairro; Type: CONSTRAINT; Schema: urbano; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT pk_cep_logradouro_bairro PRIMARY KEY (idbai, idlog, cep);


--
-- Name: pk_tipo_logradouro; Type: CONSTRAINT; Schema: urbano; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tipo_logradouro
    ADD CONSTRAINT pk_tipo_logradouro PRIMARY KEY (idtlog);


SET search_path = acesso, pg_catalog;

--
-- Name: un_usuario_idpes; Type: INDEX; Schema: acesso; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_usuario_idpes ON usuario USING btree (idpes);


SET search_path = alimentos, pg_catalog;

--
-- Name: un_baixa_guia_remessa; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_baixa_guia_remessa ON baixa_guia_remessa USING btree (idgui, dt_recebimento);


--
-- Name: un_cardapio_produto; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_cardapio_produto ON cardapio_produto USING btree (idcar, idpro);


--
-- Name: un_cliente; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_cliente ON cliente USING btree (idcli, identificacao);


--
-- Name: un_contrato; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_contrato ON contrato USING btree (idcli, codigo, num_aditivo);


--
-- Name: un_contrato_produto; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_contrato_produto ON contrato_produto USING btree (idcon, idpro);


--
-- Name: un_evento; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_evento ON evento USING btree (idcad, mes, dia);


--
-- Name: un_faixa_cp_quimico; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_faixa_cp_quimico ON faixa_composto_quimico USING btree (idcom, idfae);


--
-- Name: un_fornecedor; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_fornecedor ON fornecedor USING btree (idcli, nome_fantasia);


--
-- Name: un_fornecedor_unidade_atend; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_fornecedor_unidade_atend ON fornecedor_unidade_atendida USING btree (iduni, idfor);


--
-- Name: un_guia_remessa; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_guia_remessa ON guia_remessa USING btree (idcli, ano, sequencial);


--
-- Name: un_guia_remessa_produto; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_guia_remessa_produto ON guia_remessa_produto USING btree (idgui, idpro);


--
-- Name: un_prod_cp_quimico; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_prod_cp_quimico ON produto_composto_quimico USING btree (idpro, idcom);


--
-- Name: un_produto; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_produto ON produto USING btree (idcli, nome_compra);


--
-- Name: un_produto_fornecedor; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_produto_fornecedor ON produto_fornecedor USING btree (idfor, idpro);


--
-- Name: un_produto_medida_caseira; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_produto_medida_caseira ON produto_medida_caseira USING btree (idmedcas, idcli, idpro);


--
-- Name: un_rec_cp_quimico; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_rec_cp_quimico ON receita_composto_quimico USING btree (idcom, idrec);


--
-- Name: un_rec_prod; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_rec_prod ON receita_produto USING btree (idpro, idrec);


--
-- Name: un_uni_faixa_etaria; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_uni_faixa_etaria ON unidade_faixa_etaria USING btree (iduni, idfae);


--
-- Name: un_unidade_atendida; Type: INDEX; Schema: alimentos; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_unidade_atendida ON unidade_atendida USING btree (idcli, codigo);


SET search_path = cadastro, pg_catalog;

--
-- Name: un_fisica_cpf; Type: INDEX; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_fisica_cpf ON fisica_cpf USING btree (cpf);


--
-- Name: un_juridica_cnpj; Type: INDEX; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX un_juridica_cnpj ON juridica USING btree (cnpj);


SET search_path = pmieducar, pg_catalog;

--
-- Name: fki_biblioteca_usuario_ref_cod_biblioteca_fk; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX fki_biblioteca_usuario_ref_cod_biblioteca_fk ON biblioteca_usuario USING btree (ref_cod_biblioteca);


--
-- Name: fki_servidor_ref_cod_subnivel; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX fki_servidor_ref_cod_subnivel ON servidor USING btree (ref_cod_subnivel);


--
-- Name: fki_servidor_ref_cod_subnivel_; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX fki_servidor_ref_cod_subnivel_ ON servidor USING btree (ref_cod_subnivel);


--
-- Name: i_aluno_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_ativo ON aluno USING btree (ativo);


--
-- Name: i_aluno_beneficio_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_beneficio_ativo ON aluno_beneficio USING btree (ativo);


--
-- Name: i_aluno_beneficio_nm_beneficio; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_beneficio_nm_beneficio ON aluno_beneficio USING btree (nm_beneficio);


--
-- Name: i_aluno_beneficio_nm_beneficio_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_beneficio_nm_beneficio_asc ON aluno_beneficio USING btree (to_ascii((nm_beneficio)::text));


--
-- Name: i_aluno_beneficio_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_beneficio_ref_usuario_cad ON aluno_beneficio USING btree (ref_usuario_cad);


--
-- Name: i_aluno_ref_cod_aluno_beneficio; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_ref_cod_aluno_beneficio ON aluno USING btree (ref_cod_aluno_beneficio);


--
-- Name: i_aluno_ref_cod_religiao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_ref_cod_religiao ON aluno USING btree (ref_cod_religiao);


--
-- Name: i_aluno_ref_idpes; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_ref_idpes ON aluno USING btree (ref_idpes);


--
-- Name: i_aluno_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_aluno_ref_usuario_cad ON aluno USING btree (ref_usuario_cad);


--
-- Name: i_calendario_ano_letivo_ano; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_ano_letivo_ano ON calendario_ano_letivo USING btree (ano);


--
-- Name: i_calendario_ano_letivo_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_ano_letivo_ativo ON calendario_ano_letivo USING btree (ativo);


--
-- Name: i_calendario_ano_letivo_ref_cod_escola; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_ano_letivo_ref_cod_escola ON calendario_ano_letivo USING btree (ref_cod_escola);


--
-- Name: i_calendario_ano_letivo_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_ano_letivo_ref_usuario_cad ON calendario_ano_letivo USING btree (ref_usuario_cad);


--
-- Name: i_calendario_dia_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_ativo ON calendario_dia USING btree (ativo);


--
-- Name: i_calendario_dia_dia; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_dia ON calendario_dia USING btree (dia);


--
-- Name: i_calendario_dia_mes; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_mes ON calendario_dia USING btree (mes);


--
-- Name: i_calendario_dia_motivo_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_ativo ON calendario_dia_motivo USING btree (ativo);


--
-- Name: i_calendario_dia_motivo_ref_cod_escola; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_ref_cod_escola ON calendario_dia_motivo USING btree (ref_cod_escola);


--
-- Name: i_calendario_dia_motivo_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_ref_usuario_cad ON calendario_dia_motivo USING btree (ref_usuario_cad);


--
-- Name: i_calendario_dia_motivo_sigla; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_sigla ON calendario_dia_motivo USING btree (sigla);


--
-- Name: i_calendario_dia_motivo_sigla_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_sigla_asc ON calendario_dia_motivo USING btree (to_ascii((sigla)::text));


--
-- Name: i_calendario_dia_motivo_tipo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_motivo_tipo ON calendario_dia_motivo USING btree (tipo);


--
-- Name: i_calendario_dia_ref_cod_calendario_dia_motivo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_ref_cod_calendario_dia_motivo ON calendario_dia USING btree (ref_cod_calendario_dia_motivo);


--
-- Name: i_calendario_dia_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_calendario_dia_ref_usuario_cad ON calendario_dia USING btree (ref_usuario_cad);


--
-- Name: i_coffebreak_tipo_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_coffebreak_tipo_ativo ON coffebreak_tipo USING btree (ativo);


--
-- Name: i_coffebreak_tipo_custo_unitario; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_coffebreak_tipo_custo_unitario ON coffebreak_tipo USING btree (custo_unitario);


--
-- Name: i_coffebreak_tipo_nm_tipo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_coffebreak_tipo_nm_tipo ON coffebreak_tipo USING btree (nm_tipo);


--
-- Name: i_coffebreak_tipo_nm_tipo_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_coffebreak_tipo_nm_tipo_asc ON coffebreak_tipo USING btree (to_ascii((nm_tipo)::text));


--
-- Name: i_coffebreak_tipo_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_coffebreak_tipo_ref_usuario_cad ON coffebreak_tipo USING btree (ref_usuario_cad);


--
-- Name: i_curso_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ativo ON curso USING btree (ativo);


--
-- Name: i_curso_ato_poder_publico; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ato_poder_publico ON curso USING btree (ato_poder_publico);


--
-- Name: i_curso_carga_horaria; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_carga_horaria ON curso USING btree (carga_horaria);


--
-- Name: i_curso_edicao_final; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_edicao_final ON curso USING btree (edicao_final);


--
-- Name: i_curso_falta_ch_globalizada; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_falta_ch_globalizada ON curso USING btree (falta_ch_globalizada);


--
-- Name: i_curso_nm_curso; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_nm_curso ON curso USING btree (nm_curso);


--
-- Name: i_curso_nm_curso_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_nm_curso_asc ON curso USING btree (to_ascii((nm_curso)::text));


--
-- Name: i_curso_objetivo_curso; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_objetivo_curso ON curso USING btree (objetivo_curso);


--
-- Name: i_curso_objetivo_curso_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_objetivo_curso_asc ON curso USING btree (to_ascii(objetivo_curso));


--
-- Name: i_curso_qtd_etapas; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_qtd_etapas ON curso USING btree (qtd_etapas);


--
-- Name: i_curso_ref_cod_nivel_ensino; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ref_cod_nivel_ensino ON curso USING btree (ref_cod_nivel_ensino);


--
-- Name: i_curso_ref_cod_tipo_avaliacao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ref_cod_tipo_avaliacao ON curso USING btree (ref_cod_tipo_avaliacao);


--
-- Name: i_curso_ref_cod_tipo_ensino; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ref_cod_tipo_ensino ON curso USING btree (ref_cod_tipo_ensino);


--
-- Name: i_curso_ref_cod_tipo_regime; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ref_cod_tipo_regime ON curso USING btree (ref_cod_tipo_regime);


--
-- Name: i_curso_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_ref_usuario_cad ON curso USING btree (ref_usuario_cad);


--
-- Name: i_curso_sgl_curso; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_sgl_curso ON curso USING btree (sgl_curso);


--
-- Name: i_curso_sgl_curso_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_curso_sgl_curso_asc ON curso USING btree (to_ascii((sgl_curso)::text));


--
-- Name: i_disciplina_abreviatura; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_abreviatura ON disciplina USING btree (abreviatura);


--
-- Name: i_disciplina_abreviatura_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_abreviatura_asc ON disciplina USING btree (to_ascii((abreviatura)::text));


--
-- Name: i_disciplina_apura_falta; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_apura_falta ON disciplina USING btree (apura_falta);


--
-- Name: i_disciplina_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_ativo ON disciplina USING btree (ativo);


--
-- Name: i_disciplina_carga_horaria; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_carga_horaria ON disciplina USING btree (carga_horaria);


--
-- Name: i_disciplina_nm_disciplina; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_nm_disciplina ON disciplina USING btree (nm_disciplina);


--
-- Name: i_disciplina_nm_disciplina_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_nm_disciplina_asc ON disciplina USING btree (to_ascii((nm_disciplina)::text));


--
-- Name: i_disciplina_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_ref_usuario_cad ON disciplina USING btree (ref_usuario_cad);


--
-- Name: i_disciplina_topico_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_topico_ativo ON disciplina_topico USING btree (ativo);


--
-- Name: i_disciplina_topico_nm_topico; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_topico_nm_topico ON disciplina_topico USING btree (nm_topico);


--
-- Name: i_disciplina_topico_nm_topico_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_topico_nm_topico_asc ON disciplina_topico USING btree (to_ascii((nm_topico)::text));


--
-- Name: i_disciplina_topico_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_disciplina_topico_ref_usuario_cad ON disciplina_topico USING btree (ref_usuario_cad);


--
-- Name: i_dispensa_disciplina_ref_cod_matricula; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_dispensa_disciplina_ref_cod_matricula ON dispensa_disciplina USING btree (ref_cod_matricula);


--
-- Name: i_escola_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ativo ON escola USING btree (ativo);


--
-- Name: i_escola_complemento_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_ativo ON escola_complemento USING btree (ativo);


--
-- Name: i_escola_complemento_bairro; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_bairro ON escola_complemento USING btree (bairro);


--
-- Name: i_escola_complemento_bairro_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_bairro_asc ON escola_complemento USING btree (to_ascii((bairro)::text));


--
-- Name: i_escola_complemento_cep; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_cep ON escola_complemento USING btree (cep);


--
-- Name: i_escola_complemento_cep_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_cep_asc ON escola_complemento USING btree (to_ascii((cep)::text));


--
-- Name: i_escola_complemento_complemento; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_complemento ON escola_complemento USING btree (complemento);


--
-- Name: i_escola_complemento_complemento_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_complemento_asc ON escola_complemento USING btree (to_ascii((complemento)::text));


--
-- Name: i_escola_complemento_email; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_email ON escola_complemento USING btree (email);


--
-- Name: i_escola_complemento_email_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_email_asc ON escola_complemento USING btree (to_ascii((email)::text));


--
-- Name: i_escola_complemento_logradouro; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_logradouro ON escola_complemento USING btree (logradouro);


--
-- Name: i_escola_complemento_logradouro_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_logradouro_asc ON escola_complemento USING btree (to_ascii((bairro)::text));


--
-- Name: i_escola_complemento_municipio; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_municipio ON escola_complemento USING btree (municipio);


--
-- Name: i_escola_complemento_municipio_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_municipio_asc ON escola_complemento USING btree (to_ascii((municipio)::text));


--
-- Name: i_escola_complemento_nm_escola; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_nm_escola ON escola_complemento USING btree (nm_escola);


--
-- Name: i_escola_complemento_nm_escola_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_nm_escola_asc ON escola_complemento USING btree (to_ascii((nm_escola)::text));


--
-- Name: i_escola_complemento_numero; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_numero ON escola_complemento USING btree (numero);


--
-- Name: i_escola_complemento_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_complemento_ref_usuario_cad ON escola_complemento USING btree (ref_usuario_cad);


--
-- Name: i_escola_curso_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_curso_ativo ON escola_curso USING btree (ativo);


--
-- Name: i_escola_curso_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_curso_ref_usuario_cad ON escola_curso USING btree (ref_usuario_cad);


--
-- Name: i_escola_localizacao_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_localizacao_ativo ON escola_localizacao USING btree (ativo);


--
-- Name: i_escola_localizacao_nm_localizacao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_localizacao_nm_localizacao ON escola_localizacao USING btree (nm_localizacao);


--
-- Name: i_escola_localizacao_nm_localizacao_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_localizacao_nm_localizacao_asc ON escola_localizacao USING btree (to_ascii((nm_localizacao)::text));


--
-- Name: i_escola_localizacao_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_localizacao_ref_usuario_cad ON escola_localizacao USING btree (ref_usuario_cad);


--
-- Name: i_escola_rede_ensino_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_rede_ensino_ativo ON escola_rede_ensino USING btree (ativo);


--
-- Name: i_escola_rede_ensino_nm_rede; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_rede_ensino_nm_rede ON escola_rede_ensino USING btree (nm_rede);


--
-- Name: i_escola_rede_ensino_nm_rede_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_rede_ensino_nm_rede_asc ON escola_rede_ensino USING btree (to_ascii((nm_rede)::text));


--
-- Name: i_escola_rede_ensino_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_rede_ensino_ref_usuario_cad ON escola_rede_ensino USING btree (ref_usuario_cad);


--
-- Name: i_escola_ref_cod_escola_localizacao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ref_cod_escola_localizacao ON escola USING btree (ref_cod_escola_localizacao);


--
-- Name: i_escola_ref_cod_escola_rede_ensino; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ref_cod_escola_rede_ensino ON escola USING btree (ref_cod_escola_rede_ensino);


--
-- Name: i_escola_ref_cod_instituicao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ref_cod_instituicao ON escola USING btree (ref_cod_instituicao);


--
-- Name: i_escola_ref_idpes; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ref_idpes ON escola USING btree (ref_idpes);


--
-- Name: i_escola_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_ref_usuario_cad ON escola USING btree (ref_usuario_cad);


--
-- Name: i_escola_serie_ensino_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_serie_ensino_ativo ON escola_serie USING btree (ativo);


--
-- Name: i_escola_serie_hora_final; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_serie_hora_final ON escola_serie USING btree (hora_final);


--
-- Name: i_escola_serie_hora_inicial; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_serie_hora_inicial ON escola_serie USING btree (hora_inicial);


--
-- Name: i_escola_serie_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_serie_ref_usuario_cad ON escola_serie USING btree (ref_usuario_cad);


--
-- Name: i_escola_sigla; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_sigla ON escola USING btree (sigla);


--
-- Name: i_escola_sigla_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_escola_sigla_asc ON escola USING btree (to_ascii((sigla)::text));


--
-- Name: i_funcao_abreviatura; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_abreviatura ON funcao USING btree (abreviatura);


--
-- Name: i_funcao_abreviatura_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_abreviatura_asc ON funcao USING btree (to_ascii((abreviatura)::text));


--
-- Name: i_funcao_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_ativo ON funcao USING btree (ativo);


--
-- Name: i_funcao_nm_funcao; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_nm_funcao ON funcao USING btree (nm_funcao);


--
-- Name: i_funcao_nm_funcao_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_nm_funcao_asc ON funcao USING btree (to_ascii((nm_funcao)::text));


--
-- Name: i_funcao_professor; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_professor ON funcao USING btree (professor);


--
-- Name: i_funcao_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_funcao_ref_usuario_cad ON funcao USING btree (ref_usuario_cad);


--
-- Name: i_habilitacao_ativo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_habilitacao_ativo ON habilitacao USING btree (ativo);


--
-- Name: i_habilitacao_nm_tipo; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_habilitacao_nm_tipo ON habilitacao USING btree (nm_tipo);


--
-- Name: i_habilitacao_ref_usuario_cad; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_habilitacao_ref_usuario_cad ON habilitacao USING btree (ref_usuario_cad);


--
-- Name: i_habilitacaoo_nm_tipo_asc; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_habilitacaoo_nm_tipo_asc ON habilitacao USING btree (to_ascii((nm_tipo)::text));


--
-- Name: i_matricula_turma_ref_cod_turma; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_matricula_turma_ref_cod_turma ON matricula_turma USING btree (ref_cod_turma);


--
-- Name: i_nota_aluno_ref_cod_matricula; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_nota_aluno_ref_cod_matricula ON nota_aluno USING btree (ref_cod_matricula);


--
-- Name: i_turma_nm_turma; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE INDEX i_turma_nm_turma ON turma USING btree (nm_turma);


SET search_path = portal, pg_catalog;

--
-- Name: mailling_fila_envio_data_envio_idx; Type: INDEX; Schema: portal; Owner: -; Tablespace: 
--

CREATE INDEX mailling_fila_envio_data_envio_idx ON mailling_fila_envio USING btree (data_envio);


--
-- Name: mailling_fila_envio_ref_cod_mailling_email; Type: INDEX; Schema: portal; Owner: -; Tablespace: 
--

CREATE INDEX mailling_fila_envio_ref_cod_mailling_email ON mailling_fila_envio USING btree (ref_cod_mailling_email);


--
-- Name: mailling_fila_envio_ref_cod_mailling_email_conteudo; Type: INDEX; Schema: portal; Owner: -; Tablespace: 
--

CREATE INDEX mailling_fila_envio_ref_cod_mailling_email_conteudo ON mailling_fila_envio USING btree (ref_cod_mailling_email_conteudo);


--
-- Name: mailling_fila_envio_ref_cod_mailling_fila_envio; Type: INDEX; Schema: portal; Owner: -; Tablespace: 
--

CREATE INDEX mailling_fila_envio_ref_cod_mailling_fila_envio ON mailling_fila_envio USING btree (cod_mailling_fila_envio);


SET search_path = cadastro, pg_catalog;

--
-- Name: trg_aft_documento; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_documento
    AFTER INSERT OR UPDATE ON documento
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_documento();


--
-- Name: trg_aft_documento_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_documento_historico_campo
    AFTER INSERT OR UPDATE ON documento
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_documento_historico_campo();


--
-- Name: trg_aft_documento_provisorio; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_documento_provisorio
    AFTER INSERT OR UPDATE ON documento
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_documento_provisorio();


--
-- Name: trg_aft_endereco_externo_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_endereco_externo_historico_campo
    AFTER INSERT OR UPDATE ON endereco_externo
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_endereco_externo_historico_campo();


--
-- Name: trg_aft_endereco_pessoa_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_endereco_pessoa_historico_campo
    AFTER INSERT OR UPDATE ON endereco_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_endereco_pessoa_historico_campo();


--
-- Name: trg_aft_fisica; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fisica
    AFTER INSERT OR UPDATE ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_fisica();


--
-- Name: trg_aft_fisica_cpf_provisorio; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fisica_cpf_provisorio
    AFTER INSERT OR UPDATE ON fisica_cpf
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_fisica_cpf_provisorio();


--
-- Name: trg_aft_fisica_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fisica_historico_campo
    AFTER INSERT OR UPDATE ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_fisica_historico_campo();


--
-- Name: trg_aft_fisica_provisorio; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fisica_provisorio
    AFTER INSERT OR UPDATE ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_fisica_provisorio();


--
-- Name: trg_aft_fone_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fone_historico_campo
    AFTER INSERT OR UPDATE ON fone_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_fone_historico_campo();


--
-- Name: trg_aft_fone_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_fone_pessoa_historico
    AFTER DELETE ON fone_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_fone_pessoa();


--
-- Name: trg_aft_funcionario_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_funcionario_historico
    AFTER DELETE ON funcionario
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_funcionario();


--
-- Name: trg_aft_ins_endereco_externo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_ins_endereco_externo
    AFTER INSERT OR UPDATE ON endereco_externo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_ins_endereco_externo();


--
-- Name: trg_aft_ins_endereco_pessoa; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_ins_endereco_pessoa
    AFTER INSERT OR UPDATE ON endereco_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_ins_endereco_pessoa();


--
-- Name: trg_aft_juridica_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_juridica_historico_campo
    AFTER INSERT OR UPDATE ON juridica
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_juridica_historico_campo();


--
-- Name: trg_aft_pessoa_fonetiza; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_pessoa_fonetiza
    AFTER INSERT OR UPDATE ON pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE public.fcn_aft_pessoa_fonetiza();


--
-- Name: trg_aft_pessoa_historico_campo; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_aft_pessoa_historico_campo
    AFTER INSERT OR UPDATE ON pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE consistenciacao.fcn_pessoa_historico_campo();


--
-- Name: trg_bef_documento_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_documento_historico
    BEFORE UPDATE ON documento
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_documento();


--
-- Name: trg_bef_endereco_externo_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_endereco_externo_historico
    BEFORE UPDATE ON endereco_externo
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_endereco_externo();


--
-- Name: trg_bef_endereco_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_endereco_pessoa_historico
    BEFORE UPDATE ON endereco_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_endereco_pessoa();


--
-- Name: trg_bef_fisica_cpf_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_fisica_cpf_historico
    BEFORE UPDATE ON fisica_cpf
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_fisica_cpf();


--
-- Name: trg_bef_fisica_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_fisica_historico
    BEFORE UPDATE ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_fisica();


--
-- Name: trg_bef_fone_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_fone_pessoa_historico
    BEFORE UPDATE ON fone_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_fone_pessoa();


--
-- Name: trg_bef_funcionario_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_funcionario_historico
    BEFORE UPDATE ON funcionario
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_funcionario();


--
-- Name: trg_bef_ins_fisica; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_ins_fisica
    BEFORE INSERT ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE public.fcn_bef_ins_fisica();


--
-- Name: trg_bef_ins_juridica; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_ins_juridica
    BEFORE INSERT ON juridica
    FOR EACH ROW
    EXECUTE PROCEDURE public.fcn_bef_ins_juridica();


--
-- Name: trg_bef_juridica_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_juridica_historico
    BEFORE UPDATE ON juridica
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_juridica();


--
-- Name: trg_bef_pessoa_fonetiza; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_pessoa_fonetiza
    BEFORE DELETE ON pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE public.fcn_bef_pessoa_fonetiza();


--
-- Name: trg_bef_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_pessoa_historico
    BEFORE UPDATE ON pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_pessoa();


--
-- Name: trg_bef_socio_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_bef_socio_historico
    BEFORE UPDATE ON socio
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_socio();


--
-- Name: trg_delete_documento_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_documento_historico
    AFTER DELETE ON documento
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_documento();


--
-- Name: trg_delete_endereco_externo_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_endereco_externo_historico
    AFTER DELETE ON endereco_externo
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_endereco_externo();


--
-- Name: trg_delete_endereco_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_endereco_pessoa_historico
    AFTER DELETE ON endereco_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_endereco_pessoa();


--
-- Name: trg_delete_fisica_cpf_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_fisica_cpf_historico
    AFTER DELETE ON fisica_cpf
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fisica_cpf();


--
-- Name: trg_delete_fisica_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_fisica_historico
    AFTER DELETE ON fisica
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fisica();


--
-- Name: trg_delete_fone_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_fone_pessoa_historico
    AFTER DELETE ON fone_pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fone_pessoa();


--
-- Name: trg_delete_funcionario_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_funcionario_historico
    AFTER DELETE ON funcionario
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_funcionario();


--
-- Name: trg_delete_juridica_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_juridica_historico
    AFTER DELETE ON juridica
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_juridica();


--
-- Name: trg_delete_pessoa_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_pessoa_historico
    AFTER DELETE ON pessoa
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_pessoa();


--
-- Name: trg_delete_socio_historico; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER trg_delete_socio_historico
    AFTER DELETE ON socio
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_socio();


SET search_path = pmieducar, pg_catalog;

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON instituicao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_acervo_assunto
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_acervo_autor
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_assunto
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_autor
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_colecao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_editora
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON acervo_idioma
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON aluno
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON aluno_beneficio
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON ano_letivo_modulo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON avaliacao_desempenho
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON biblioteca
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON biblioteca_dia
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON biblioteca_feriados
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON biblioteca_usuario
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON calendario_ano_letivo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON calendario_anotacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON calendario_dia
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON calendario_dia_anotacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON calendario_dia_motivo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON cliente
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON cliente_suspensao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON cliente_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON cliente_tipo_cliente
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON cliente_tipo_exemplar_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON coffebreak_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON curso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON disciplina
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON disciplina_topico
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_ano_letivo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_complemento
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_curso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_localizacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_rede_ensino
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON escola_serie
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON exemplar
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON exemplar_emprestimo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON exemplar_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON falta_atraso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON falta_atraso_compensado
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON fonte
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON funcao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON habilitacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON habilitacao_curso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON historico_disciplinas
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON historico_escolar
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON infra_comodo_funcao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON infra_predio
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON infra_predio_comodo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON material_didatico
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON material_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON matricula
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON matricula_ocorrencia_disciplinar
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON menu_tipo_usuario
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON modulo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON motivo_afastamento
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON motivo_baixa
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON motivo_suspensao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON nivel_ensino
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON operador
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON pagamento_multa
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON pre_requisito
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON quadro_horario
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON religiao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON reserva_vaga
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON reservas
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON sequencia_serie
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON serie
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON serie_pre_requisito
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor_afastamento
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor_alocacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor_curso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor_formacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON servidor_titulo_concurso
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON situacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_avaliacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_avaliacao_valores
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_dispensa
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_ensino
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_ocorrencia_disciplinar
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_regime
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON tipo_usuario
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON transferencia_solicitacao
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON transferencia_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON turma
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON turma_dia_semana
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON turma_modulo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON turma_tipo
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
    AFTER INSERT OR UPDATE ON usuario
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_update();


SET search_path = public, pg_catalog;

--
-- Name: trg_aft_logradouro_fonetiza; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_aft_logradouro_fonetiza
    AFTER INSERT OR UPDATE ON logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_aft_logradouro_fonetiza();


--
-- Name: trg_bef_bairro_historico; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_bef_bairro_historico
    BEFORE UPDATE ON bairro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_bairro();


--
-- Name: trg_bef_logradouro_fonetiza; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_bef_logradouro_fonetiza
    BEFORE DELETE ON logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE fcn_bef_logradouro_fonetiza();


--
-- Name: trg_bef_logradouro_historico; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_bef_logradouro_historico
    BEFORE UPDATE ON logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_logradouro();


--
-- Name: trg_delete_bairro_historico; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_delete_bairro_historico
    AFTER DELETE ON bairro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_bairro();


--
-- Name: trg_delete_logradouro_historico; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_delete_logradouro_historico
    AFTER DELETE ON logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_logradouro();


SET search_path = urbano, pg_catalog;

--
-- Name: trg_bef_cep_logradouro_bairro_historico; Type: TRIGGER; Schema: urbano; Owner: -
--

CREATE TRIGGER trg_bef_cep_logradouro_bairro_historico
    BEFORE UPDATE ON cep_logradouro_bairro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_cep_logradouro_bairro();


--
-- Name: trg_bef_cep_logradouro_historico; Type: TRIGGER; Schema: urbano; Owner: -
--

CREATE TRIGGER trg_bef_cep_logradouro_historico
    BEFORE UPDATE ON cep_logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_cep_logradouro();


--
-- Name: trg_delete_cep_logradouro_bairro_historico; Type: TRIGGER; Schema: urbano; Owner: -
--

CREATE TRIGGER trg_delete_cep_logradouro_bairro_historico
    AFTER DELETE ON cep_logradouro_bairro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_cep_logradouro_bairro();


--
-- Name: trg_delete_cep_logradouro_historico; Type: TRIGGER; Schema: urbano; Owner: -
--

CREATE TRIGGER trg_delete_cep_logradouro_historico
    AFTER DELETE ON cep_logradouro
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_cep_logradouro();


SET search_path = acesso, pg_catalog;

--
-- Name: fk_funcao_grp_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_funcao
    ADD CONSTRAINT fk_funcao_grp_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES funcao(idfunc, idsis, idmen);


--
-- Name: fk_funcao_operacao_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY operacao_funcao
    ADD CONSTRAINT fk_funcao_operacao_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES funcao(idfunc, idsis, idmen);


--
-- Name: fk_grp_fun_grp_operacao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_operacao
    ADD CONSTRAINT fk_grp_fun_grp_operacao FOREIGN KEY (idmen, idsis, idgrp, idfunc) REFERENCES grupo_funcao(idmen, idsis, idgrp, idfunc);


--
-- Name: fk_grp_menu_grp_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_funcao
    ADD CONSTRAINT fk_grp_menu_grp_funcao FOREIGN KEY (idgrp, idsis, idmen) REFERENCES grupo_menu(idgrp, idsis, idmen);


--
-- Name: fk_grp_sis_grp_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_menu
    ADD CONSTRAINT fk_grp_sis_grp_menu FOREIGN KEY (idsis, idgrp) REFERENCES grupo_sistema(idsis, idgrp);


--
-- Name: fk_grupo_grupo_sistema; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_sistema
    ADD CONSTRAINT fk_grupo_grupo_sistema FOREIGN KEY (idgrp) REFERENCES grupo(idgrp);


--
-- Name: fk_grupo_usuario_grupo; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT fk_grupo_usuario_grupo FOREIGN KEY (idgrp) REFERENCES grupo(idgrp);


--
-- Name: fk_inst_pessoa_instituicao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY pessoa_instituicao
    ADD CONSTRAINT fk_inst_pessoa_instituicao FOREIGN KEY (idins) REFERENCES instituicao(idins);


--
-- Name: fk_menu_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT fk_menu_funcao FOREIGN KEY (idmen, idsis) REFERENCES menu(idmen, idsis);


--
-- Name: fk_menu_grp_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_menu
    ADD CONSTRAINT fk_menu_grp_menu FOREIGN KEY (idmen, idsis) REFERENCES menu(idmen, idsis);


--
-- Name: fk_menu_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT fk_menu_menu FOREIGN KEY (menu_idsis, menu_idmen) REFERENCES menu(idsis, idmen);


--
-- Name: fk_oper_func_grp_oper; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_operacao
    ADD CONSTRAINT fk_oper_func_grp_oper FOREIGN KEY (idmen, idsis, idfunc, idope) REFERENCES operacao_funcao(idmen, idsis, idfunc, idope);


--
-- Name: fk_operacao_operacao_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY operacao_funcao
    ADD CONSTRAINT fk_operacao_operacao_funcao FOREIGN KEY (idope) REFERENCES operacao(idope);


--
-- Name: fk_pes_pessoa_instituicao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY pessoa_instituicao
    ADD CONSTRAINT fk_pes_pessoa_instituicao FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- Name: fk_pessoa_usuario; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT fk_pessoa_usuario FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- Name: fk_sistema_grupo_sistema; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY grupo_sistema
    ADD CONSTRAINT fk_sistema_grupo_sistema FOREIGN KEY (idsis) REFERENCES sistema(idsis);


--
-- Name: fk_sistema_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT fk_sistema_menu FOREIGN KEY (idsis) REFERENCES sistema(idsis);


--
-- Name: fk_sistema_operacao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY operacao
    ADD CONSTRAINT fk_sistema_operacao FOREIGN KEY (idsis) REFERENCES sistema(idsis);


--
-- Name: fk_usuario_usuario_grupo; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT fk_usuario_usuario_grupo FOREIGN KEY ("login") REFERENCES usuario("login");


SET search_path = alimentos, pg_catalog;

--
-- Name: fk_alterar_usuario_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio
    ADD CONSTRAINT fk_alterar_usuario_cardapio FOREIGN KEY (login_alteracao) REFERENCES acesso.usuario("login");


--
-- Name: fk_baixa_guia_baixa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY baixa_guia_produto
    ADD CONSTRAINT fk_baixa_guia_baixa_produto FOREIGN KEY (idbai) REFERENCES baixa_guia_remessa(idbai);


--
-- Name: fk_calendario_evento; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY evento
    ADD CONSTRAINT fk_calendario_evento FOREIGN KEY (idcad) REFERENCES calendario(idcad);


--
-- Name: fk_calendario_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_atendida
    ADD CONSTRAINT fk_calendario_unidade FOREIGN KEY (idcad) REFERENCES calendario(idcad);


--
-- Name: fk_cancelar_usuario_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_cancelar_usuario_guia_remessa FOREIGN KEY (login_cancelamento) REFERENCES acesso.usuario("login");


--
-- Name: fk_cardapio_cardapio_faixa_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_faixa_unidade
    ADD CONSTRAINT fk_cardapio_cardapio_faixa_unidade FOREIGN KEY (idcar) REFERENCES cardapio(idcar);


--
-- Name: fk_cardapio_cardapio_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_produto
    ADD CONSTRAINT fk_cardapio_cardapio_produto FOREIGN KEY (idcar) REFERENCES cardapio(idcar);


--
-- Name: fk_cardapio_cardapio_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_receita
    ADD CONSTRAINT fk_cardapio_cardapio_receita FOREIGN KEY (idcar) REFERENCES cardapio(idcar);


--
-- Name: fk_cliente_calendario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY calendario
    ADD CONSTRAINT fk_cliente_calendario FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio
    ADD CONSTRAINT fk_cliente_cardapio FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY contrato
    ADD CONSTRAINT fk_cliente_contrato FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_cpquimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY composto_quimico
    ADD CONSTRAINT fk_cliente_cpquimico FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY fornecedor
    ADD CONSTRAINT fk_cliente_fornecedor FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_grpatencao; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY faixa_etaria
    ADD CONSTRAINT fk_cliente_grpatencao FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_grpquimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY grupo_quimico
    ADD CONSTRAINT fk_cliente_grpquimico FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_cliente_guia_remessa FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_log_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY log_guia_remessa
    ADD CONSTRAINT fk_cliente_log_guia FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY receita
    ADD CONSTRAINT fk_cliente_receita FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_tpproduto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY tipo_produto
    ADD CONSTRAINT fk_cliente_tpproduto FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_tprefeicao; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY tipo_refeicao
    ADD CONSTRAINT fk_cliente_tprefeicao FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_tpunidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY tipo_unidade
    ADD CONSTRAINT fk_cliente_tpunidade FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_atendida
    ADD CONSTRAINT fk_cliente_unidade FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_cliente_uniproduto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_produto
    ADD CONSTRAINT fk_cliente_uniproduto FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_contrato_contrato_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY contrato_produto
    ADD CONSTRAINT fk_contrato_contrato_produto FOREIGN KEY (idcon) REFERENCES contrato(idcon);


--
-- Name: fk_contrato_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_contrato_guia_remessa FOREIGN KEY (idcon) REFERENCES contrato(idcon);


--
-- Name: fk_cp_quimico_faixa_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY faixa_composto_quimico
    ADD CONSTRAINT fk_cp_quimico_faixa_cp_quimico FOREIGN KEY (idcom) REFERENCES composto_quimico(idcom);


--
-- Name: fk_emitir_usuario_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_emitir_usuario_guia_remessa FOREIGN KEY (login_emissao) REFERENCES acesso.usuario("login");


--
-- Name: fk_faixa_etaria_unidade_faixa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_faixa_etaria
    ADD CONSTRAINT fk_faixa_etaria_unidade_faixa FOREIGN KEY (idfae) REFERENCES faixa_etaria(idfae);


--
-- Name: fk_faixa_faixa_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY faixa_composto_quimico
    ADD CONSTRAINT fk_faixa_faixa_cp_quimico FOREIGN KEY (idfae) REFERENCES faixa_etaria(idfae);


--
-- Name: fk_faixa_uni_cardapio_faixa_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_faixa_unidade
    ADD CONSTRAINT fk_faixa_uni_cardapio_faixa_unidade FOREIGN KEY (idfeu) REFERENCES unidade_faixa_etaria(idfeu);


--
-- Name: fk_fornecedor_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY contrato
    ADD CONSTRAINT fk_fornecedor_contrato FOREIGN KEY (idfor) REFERENCES fornecedor(idfor);


--
-- Name: fk_fornecedor_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_fornecedor_guia_remessa FOREIGN KEY (idfor) REFERENCES fornecedor(idfor);


--
-- Name: fk_fornecedor_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_fornecedor
    ADD CONSTRAINT fk_fornecedor_produto_fornecedor FOREIGN KEY (idfor) REFERENCES fornecedor(idfor);


--
-- Name: fk_fornecedor_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY fornecedor_unidade_atendida
    ADD CONSTRAINT fk_fornecedor_unidade FOREIGN KEY (idfor) REFERENCES fornecedor(idfor);


--
-- Name: fk_grupo_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY composto_quimico
    ADD CONSTRAINT fk_grupo_cp_quimico_cp_quimico FOREIGN KEY (idgrpq) REFERENCES grupo_quimico(idgrpq);


--
-- Name: fk_guia_guia_remessa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa_produto
    ADD CONSTRAINT fk_guia_guia_remessa_produto FOREIGN KEY (idgui) REFERENCES guia_remessa(idgui);


--
-- Name: fk_guia_produto_baixa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY baixa_guia_produto
    ADD CONSTRAINT fk_guia_produto_baixa_produto FOREIGN KEY (idgup) REFERENCES guia_remessa_produto(idgup);


--
-- Name: fk_guia_remessa_baixa_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY baixa_guia_remessa
    ADD CONSTRAINT fk_guia_remessa_baixa_guia FOREIGN KEY (idgui) REFERENCES guia_remessa(idgui);


--
-- Name: fk_guia_remessa_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_produto_diario
    ADD CONSTRAINT fk_guia_remessa_guia_pro_diario FOREIGN KEY (idgui) REFERENCES guia_remessa(idgui);


--
-- Name: fk_incluir_usuario_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio
    ADD CONSTRAINT fk_incluir_usuario_cardapio FOREIGN KEY (login_inclusao) REFERENCES acesso.usuario("login");


--
-- Name: fk_medidas_caseiras_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY medidas_caseiras
    ADD CONSTRAINT fk_medidas_caseiras_cliente FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_pessoa_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT fk_pessoa_cliente FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_pessoa_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY fornecedor
    ADD CONSTRAINT fk_pessoa_fornecedor FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_pessoa_unidade_atend; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_atendida
    ADD CONSTRAINT fk_pessoa_unidade_atend FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_prod_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES composto_quimico(idcom);


--
-- Name: fk_prod_cp_quimico_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_cardapio_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_produto
    ADD CONSTRAINT fk_produto_cardapio_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto
    ADD CONSTRAINT fk_produto_cliente FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_produto_contrato_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY contrato_produto
    ADD CONSTRAINT fk_produto_contrato_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto
    ADD CONSTRAINT fk_produto_fornecedor FOREIGN KEY (idfor) REFERENCES fornecedor(idfor);


--
-- Name: fk_produto_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_produto_diario
    ADD CONSTRAINT fk_produto_guia_pro_diario FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_guia_remessa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa_produto
    ADD CONSTRAINT fk_produto_guia_remessa_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_medida_caseira_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_cliente FOREIGN KEY (idcli) REFERENCES cliente(idcli);


--
-- Name: fk_produto_medida_caseira_medidas; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_medidas FOREIGN KEY (idmedcas, idcli) REFERENCES medidas_caseiras(idmedcas, idcli);


--
-- Name: fk_produto_medida_caseira_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto_fornecedor
    ADD CONSTRAINT fk_produto_produto_fornecedor FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_produto_tipo; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto
    ADD CONSTRAINT fk_produto_tipo FOREIGN KEY (idtip) REFERENCES tipo_produto(idtip);


--
-- Name: fk_produto_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY produto
    ADD CONSTRAINT fk_produto_unidade FOREIGN KEY (idunp, idcli) REFERENCES unidade_produto(idunp, idcli);


--
-- Name: fk_rec_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES composto_quimico(idcom);


--
-- Name: fk_rec_cp_quimico_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_receita FOREIGN KEY (idrec) REFERENCES receita(idrec);


--
-- Name: fk_rec_prod_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY receita_produto
    ADD CONSTRAINT fk_rec_prod_produto FOREIGN KEY (idpro) REFERENCES produto(idpro);


--
-- Name: fk_rec_prod_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY receita_produto
    ADD CONSTRAINT fk_rec_prod_receita FOREIGN KEY (idrec) REFERENCES receita(idrec);


--
-- Name: fk_receita_cardapio_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio_receita
    ADD CONSTRAINT fk_receita_cardapio_receita FOREIGN KEY (idrec) REFERENCES receita(idrec);


--
-- Name: fk_tipo_uni_uni_atendida; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_atendida
    ADD CONSTRAINT fk_tipo_uni_uni_atendida FOREIGN KEY (idtip) REFERENCES tipo_unidade(idtip);


--
-- Name: fk_tp_refeicao_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY cardapio
    ADD CONSTRAINT fk_tp_refeicao_cardapio FOREIGN KEY (idtre) REFERENCES tipo_refeicao(idtre);


--
-- Name: fk_uni_atend_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_remessa
    ADD CONSTRAINT fk_uni_atend_guia_remessa FOREIGN KEY (iduni) REFERENCES unidade_atendida(iduni);


--
-- Name: fk_uni_atend_uni_faixa_eta; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY unidade_faixa_etaria
    ADD CONSTRAINT fk_uni_atend_uni_faixa_eta FOREIGN KEY (iduni) REFERENCES unidade_atendida(iduni);


--
-- Name: fk_unidade_atendida_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY guia_produto_diario
    ADD CONSTRAINT fk_unidade_atendida_guia_pro_diario FOREIGN KEY (iduni) REFERENCES unidade_atendida(iduni);


--
-- Name: fk_unidade_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY fornecedor_unidade_atendida
    ADD CONSTRAINT fk_unidade_fornecedor FOREIGN KEY (iduni) REFERENCES unidade_atendida(iduni);


--
-- Name: fk_usuario_baixa_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY baixa_guia_produto
    ADD CONSTRAINT fk_usuario_baixa_guia FOREIGN KEY (login_baixa) REFERENCES acesso.usuario("login");


--
-- Name: fk_usuario_baixa_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY baixa_guia_remessa
    ADD CONSTRAINT fk_usuario_baixa_guia_remessa FOREIGN KEY (login_baixa) REFERENCES acesso.usuario("login");


--
-- Name: fk_usuario_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY contrato
    ADD CONSTRAINT fk_usuario_contrato FOREIGN KEY ("login") REFERENCES acesso.usuario("login");


--
-- Name: fk_usuario_log_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY log_guia_remessa
    ADD CONSTRAINT fk_usuario_log_guia FOREIGN KEY ("login") REFERENCES acesso.usuario("login");


SET search_path = cadastro, pg_catalog;

--
-- Name: fisica_foto_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_foto
    ADD CONSTRAINT fisica_foto_idpes_fkey FOREIGN KEY (idpes) REFERENCES pessoa(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fisica_ref_cod_religiao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fisica_ref_cod_religiao FOREIGN KEY (ref_cod_religiao) REFERENCES religiao(cod_religiao);


--
-- Name: fisica_sangue_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_sangue
    ADD CONSTRAINT fisica_sangue_idpes_fkey FOREIGN KEY (idpes) REFERENCES fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_aviso_nome_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY aviso_nome
    ADD CONSTRAINT fk_aviso_nome_fisica FOREIGN KEY (idpes) REFERENCES fisica(idpes) ON DELETE RESTRICT;


--
-- Name: fk_documento_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_fisica FOREIGN KEY (idpes) REFERENCES fisica(idpes);


--
-- Name: fk_documento_orgao_emissor_rg; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_orgao_emissor_rg FOREIGN KEY (idorg_exp_rg) REFERENCES orgao_emissor_rg(idorg_rg) ON DELETE RESTRICT;


--
-- Name: fk_documento_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_documento_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_documento_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_documento_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_documento_uf_cart_trabalho; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_uf_cart_trabalho FOREIGN KEY (sigla_uf_cart_trabalho) REFERENCES public.uf(sigla_uf);


--
-- Name: fk_documento_uf_cert_civil; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_uf_cert_civil FOREIGN KEY (sigla_uf_cert_civil) REFERENCES public.uf(sigla_uf);


--
-- Name: fk_documento_uf_rg; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY documento
    ADD CONSTRAINT fk_documento_uf_rg FOREIGN KEY (sigla_uf_exp_rg) REFERENCES public.uf(sigla_uf);


--
-- Name: fk_endereco_externo_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_endereco_externo_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_endereco_externo_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_endereco_externo_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_endereco_externo_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_endereco_externo_tipo_log; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);


--
-- Name: fk_endereco_externo_uf; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_externo
    ADD CONSTRAINT fk_endereco_externo_uf FOREIGN KEY (sigla_uf) REFERENCES public.uf(sigla_uf);


--
-- Name: fk_endereco_pes_cep_log_bai; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pes_cep_log_bai FOREIGN KEY (idbai, idlog, cep) REFERENCES urbano.cep_logradouro_bairro(idbai, idlog, cep);


--
-- Name: fk_endereco_pessoa_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_endereco_pessoa_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_endereco_pessoa_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_endereco_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_endereco_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fisica_cpf_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_fisica FOREIGN KEY (idpes) REFERENCES fisica(idpes) ON DELETE RESTRICT;


--
-- Name: fk_fisica_cpf_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_cpf_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_cpf_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fisica_cpf_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fisica_escolaridade; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_escolaridade FOREIGN KEY (idesco) REFERENCES escolaridade(idesco);


--
-- Name: fk_fisica_estado_civil; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_estado_civil FOREIGN KEY (ideciv) REFERENCES estado_civil(ideciv);


--
-- Name: fk_fisica_municipio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_municipio FOREIGN KEY (idmun_nascimento) REFERENCES public.municipio(idmun);


--
-- Name: fk_fisica_ocupacao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_ocupacao FOREIGN KEY (idocup) REFERENCES ocupacao(idocup);


--
-- Name: fk_fisica_pais; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pais FOREIGN KEY (idpais_estrangeiro) REFERENCES public.pais(idpais);


--
-- Name: fk_fisica_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes) ON DELETE RESTRICT;


--
-- Name: fk_fisica_pessoa_conjuge; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pessoa_conjuge FOREIGN KEY (idpes_con) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_pessoa_mae; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pessoa_mae FOREIGN KEY (idpes_mae) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_pessoa_pai; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pessoa_pai FOREIGN KEY (idpes_pai) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_pessoa_responsavel; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_pessoa_responsavel FOREIGN KEY (idpes_responsavel) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fisica_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fisica_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fone_pessoa_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_fone_pessoa_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fone_pessoa_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_fone_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_fone_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_funcionario_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_fisica FOREIGN KEY (idpes) REFERENCES fisica(idpes);


--
-- Name: fk_funcionario_instituicao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_instituicao FOREIGN KEY (idins) REFERENCES acesso.instituicao(idins);


--
-- Name: fk_funcionario_setor; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_setor FOREIGN KEY (idset) REFERENCES public.setor(idset);


--
-- Name: fk_funcionario_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_funcionario_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_funcionario_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_funcionario_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_hist_cartao_pes_cidadao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_cidadao FOREIGN KEY (idpes_cidadao) REFERENCES pessoa(idpes);


--
-- Name: fk_hist_cartao_pes_emitiu; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_emitiu FOREIGN KEY (idpes_emitiu) REFERENCES pessoa(idpes);


--
-- Name: fk_juridica_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT fk_juridica_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_juridica_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_juridica_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_juridica_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_juridica_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_juridica_socio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_juridica_socio FOREIGN KEY (idpes_juridica) REFERENCES juridica(idpes);


--
-- Name: fk_pessoa_fonetico_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY pessoa_fonetico
    ADD CONSTRAINT fk_pessoa_fonetico_pessoa FOREIGN KEY (idpes) REFERENCES pessoa(idpes);


--
-- Name: fk_pessoa_pessoa_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_pessoa_pessoa_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_pessoa_socio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_pessoa_socio FOREIGN KEY (idpes_fisica) REFERENCES pessoa(idpes);


--
-- Name: fk_socio_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_socio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_socio_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_socio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_socio_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_socio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_socio_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY socio
    ADD CONSTRAINT fk_socio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: pessoa_deficiencia_ref_cod_deficiencia_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_deficiencia) REFERENCES deficiencia(cod_deficiencia) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_deficiencia_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_raca_ref_cod_deficiencia_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_raca) REFERENCES raca(cod_raca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_raca_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: religiao_idpes_cad_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_idpes_cad_fkey FOREIGN KEY (idpes_cad) REFERENCES fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: religiao_idpes_exc_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_idpes_exc_fkey FOREIGN KEY (idpes_exc) REFERENCES fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = consistenciacao, pg_catalog;

--
-- Name: fk_campo_metadado_campo_consis; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY campo_metadado
    ADD CONSTRAINT fk_campo_metadado_campo_consis FOREIGN KEY (idcam) REFERENCES campo_consistenciacao(idcam);


--
-- Name: fk_campo_metadado_metadado; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY campo_metadado
    ADD CONSTRAINT fk_campo_metadado_metadado FOREIGN KEY (idmet) REFERENCES metadado(idmet);


--
-- Name: fk_campo_metadado_regra_campo; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY campo_metadado
    ADD CONSTRAINT fk_campo_metadado_regra_campo FOREIGN KEY (idreg) REFERENCES regra_campo(idreg);


--
-- Name: fk_confrontacao_metadado; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY confrontacao
    ADD CONSTRAINT fk_confrontacao_metadado FOREIGN KEY (idmet) REFERENCES metadado(idmet);


--
-- Name: fk_confrontacao_pessoa_instituicao; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY confrontacao
    ADD CONSTRAINT fk_confrontacao_pessoa_instituicao FOREIGN KEY (idins, idpes) REFERENCES acesso.pessoa_instituicao(idins, idpes);


--
-- Name: fk_hist_campo_campo_consist; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY historico_campo
    ADD CONSTRAINT fk_hist_campo_campo_consist FOREIGN KEY (idcam) REFERENCES campo_consistenciacao(idcam) ON DELETE CASCADE;


--
-- Name: fk_historico_campo_pessoa; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY historico_campo
    ADD CONSTRAINT fk_historico_campo_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON DELETE CASCADE;


--
-- Name: fk_inc_pessoa_possivel_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_incoerencia FOREIGN KEY (idinc) REFERENCES incoerencia(idinc) ON DELETE CASCADE;


--
-- Name: fk_inc_pessoa_possivel_pessoa; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- Name: fk_inc_tipo_inc_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_incoerencia FOREIGN KEY (idinc) REFERENCES incoerencia(idinc) ON DELETE CASCADE;


--
-- Name: fk_inc_tipo_inc_tipo_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_tipo_incoerencia FOREIGN KEY (id_tipo_inc) REFERENCES tipo_incoerencia(id_tipo_inc);


--
-- Name: fk_incoerencia_confrontacao; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia
    ADD CONSTRAINT fk_incoerencia_confrontacao FOREIGN KEY (idcon) REFERENCES confrontacao(idcon);


--
-- Name: fk_incoerencia_documento_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_documento
    ADD CONSTRAINT fk_incoerencia_documento_incoerencia FOREIGN KEY (idinc) REFERENCES incoerencia(idinc) ON DELETE CASCADE;


--
-- Name: fk_incoerencia_endereco_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_endereco
    ADD CONSTRAINT fk_incoerencia_endereco_incoerencia FOREIGN KEY (idinc) REFERENCES incoerencia(idinc) ON DELETE CASCADE;


--
-- Name: fk_incoerencia_fone_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY incoerencia_fone
    ADD CONSTRAINT fk_incoerencia_fone_incoerencia FOREIGN KEY (idinc) REFERENCES incoerencia(idinc) ON DELETE CASCADE;


--
-- Name: fk_metadado_fonte; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY metadado
    ADD CONSTRAINT fk_metadado_fonte FOREIGN KEY (idfon) REFERENCES fonte(idfon);


--
-- Name: fk_oco_reg_cam_regra_campo; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY ocorrencia_regra_campo
    ADD CONSTRAINT fk_oco_reg_cam_regra_campo FOREIGN KEY (idreg) REFERENCES regra_campo(idreg);


--
-- Name: fk_tipo_incoerencia_campo_consis; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY tipo_incoerencia
    ADD CONSTRAINT fk_tipo_incoerencia_campo_consis FOREIGN KEY (idcam) REFERENCES campo_consistenciacao(idcam);


SET search_path = pmiacoes, pg_catalog;

--
-- Name: acao_governo_arquivo_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_arquivo_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_categoria_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_categoria_ref_cod_categoria_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_categoria_fkey FOREIGN KEY (ref_cod_categoria) REFERENCES categoria(cod_categoria) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_foto_portal_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_foto_portal_ref_cod_foto_portal_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_foto_portal_fkey FOREIGN KEY (ref_cod_foto_portal) REFERENCES portal.foto_portal(cod_foto_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_foto_portal_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_foto_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_foto_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_noticia_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_noticia_ref_cod_not_portal_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_not_portal_fkey FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_noticia_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_setor_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_setor_ref_cod_setor_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acao_governo_setor_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: categoria_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY categoria
    ADD CONSTRAINT categoria_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: categoria_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY categoria
    ADD CONSTRAINT categoria_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: secretaria_responsavel_ref_cod_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_funcionario_cad_fkey FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: secretaria_responsavel_ref_cod_setor_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = pmicontrolesis, pg_catalog;

--
-- Name: acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: acontecimento_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: acontecimento_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: fk_to_imagem_ico; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT fk_to_imagem_ico FOREIGN KEY (ref_cod_ico) REFERENCES portal.imagem(cod_imagem) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_to_tutor; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT fk_to_tutor FOREIGN KEY (ref_cod_tutormenu) REFERENCES tutormenu(cod_tutormenu);


--
-- Name: foto_evento_ibfk_1; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY foto_evento
    ADD CONSTRAINT foto_evento_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: foto_vinc_ref_cod_acontecimento_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_acontecimento_fkey FOREIGN KEY (ref_cod_acontecimento) REFERENCES acontecimento(cod_acontecimento);


--
-- Name: foto_vinc_ref_cod_foto_evento_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_foto_evento_fkey FOREIGN KEY (ref_cod_foto_evento) REFERENCES foto_evento(cod_foto_evento);


--
-- Name: itinerario_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: itinerario_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: menu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: menu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: menu_ref_cod_menu_pai_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_ref_cod_menu_pai_fkey FOREIGN KEY (ref_cod_menu_pai) REFERENCES menu(cod_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: portais_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY portais
    ADD CONSTRAINT portais_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: portais_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY portais
    ADD CONSTRAINT portais_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: servicos_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY servicos
    ADD CONSTRAINT servicos_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: servicos_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY servicos
    ADD CONSTRAINT servicos_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: sistema_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY sistema
    ADD CONSTRAINT sistema_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: sistema_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY sistema
    ADD CONSTRAINT sistema_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: submenu_portal_ref_cod_menu_portal_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY submenu_portal
    ADD CONSTRAINT submenu_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES menu_portal(cod_menu_portal);


--
-- Name: submenu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: submenu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: submenu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: submenu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: telefones_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY telefones
    ADD CONSTRAINT telefones_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: telefones_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY telefones
    ADD CONSTRAINT telefones_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- Name: tipo_acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY acontecimento
    ADD CONSTRAINT tipo_acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_tipo_acontecimento) REFERENCES tipo_acontecimento(cod_tipo_acontecimento);


--
-- Name: topo_portal_ref_cod_menu_portal_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY topo_portal
    ADD CONSTRAINT topo_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES menu_portal(cod_menu_portal);


SET search_path = pmidrh, pg_catalog;

--
-- Name: diaria_ref_cod_diaria_grupo_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria
    ADD CONSTRAINT diaria_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: diaria_ref_cod_setor; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria
    ADD CONSTRAINT diaria_ref_cod_setor FOREIGN KEY (ref_cod_setor) REFERENCES setor(cod_setor);


--
-- Name: diaria_ref_funcionario_cadastro_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria
    ADD CONSTRAINT diaria_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: diaria_ref_funcionario_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria
    ADD CONSTRAINT diaria_ref_funcionario_fkey FOREIGN KEY (ref_funcionario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: diaria_valores_ref_cod_diaria_grupo_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria_valores
    ADD CONSTRAINT diaria_valores_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: diaria_valores_ref_funcionario_cadastro_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY diaria_valores
    ADD CONSTRAINT diaria_valores_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_setor_pai; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT fk_setor_pai FOREIGN KEY (ref_cod_setor) REFERENCES setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_to_idpes_resp; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT fk_to_idpes_resp FOREIGN KEY (ref_idpes_resp) REFERENCES cadastro.fisica(idpes);


--
-- Name: setor_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT setor_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: setor_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT setor_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = pmieducar, pg_catalog;

--
-- Name: acervo_acervo_assunto_ref_cod_acervo_assunto_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_assunto_fkey FOREIGN KEY (ref_cod_acervo_assunto) REFERENCES acervo_assunto(cod_acervo_assunto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_acervo_assunto_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_acervo_autor_ref_cod_acervo_autor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_autor_fkey FOREIGN KEY (ref_cod_acervo_autor) REFERENCES acervo_autor(cod_acervo_autor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_acervo_autor_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_assunto_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_assunto_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_assunto_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_autor_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_autor_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_colecao_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_colecao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_colecao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_editora_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_editora_ref_idtlog_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_editora_ref_sigla_uf_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_ref_sigla_uf_fkey FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_editora_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_editora_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_idioma_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_idioma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_idioma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_acervo_colecao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_colecao_fkey FOREIGN KEY (ref_cod_acervo_colecao) REFERENCES acervo_colecao(cod_acervo_colecao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_acervo_editora_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_editora_fkey FOREIGN KEY (ref_cod_acervo_editora) REFERENCES acervo_editora(cod_acervo_editora) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_acervo_idioma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_idioma_fkey FOREIGN KEY (ref_cod_acervo_idioma) REFERENCES acervo_idioma(cod_acervo_idioma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo_autor
    ADD CONSTRAINT acervo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_cod_exemplar_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: acervo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY acervo
    ADD CONSTRAINT acervo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_beneficio_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_beneficio_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_ref_cod_aluno_beneficio_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_ref_cod_aluno_beneficio_fkey FOREIGN KEY (ref_cod_aluno_beneficio) REFERENCES aluno_beneficio(cod_aluno_beneficio) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY aluno
    ADD CONSTRAINT aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ano_letivo_modulo_ref_cod_modulo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ano_letivo_modulo_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ano) REFERENCES escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_desempenho_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_desempenho_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_desempenho_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: biblioteca_dia_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: biblioteca_feriados_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: biblioteca_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: biblioteca_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: biblioteca_usuario_ref_cod_biblioteca_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_ref_cod_biblioteca_fk FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_ano_letivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_ano_letivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_ano_letivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_anotacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_anotacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_anotacao_ref_cod_calendario_anotacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_cod_calendario_anotacao_fkey FOREIGN KEY (ref_cod_calendario_anotacao) REFERENCES calendario_anotacao(cod_calendario_anotacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_ref_cod_calendario_ano_letivo, ref_mes, ref_dia) REFERENCES calendario_dia(ref_cod_calendario_ano_letivo, mes, dia) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_motivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_motivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_motivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_ref_cod_calendario_ano_letivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_cod_calendario_ano_letivo) REFERENCES calendario_ano_letivo(cod_calendario_ano_letivo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_ref_cod_calendario_dia_motivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_dia_motivo_fkey FOREIGN KEY (ref_cod_calendario_dia_motivo) REFERENCES calendario_dia_motivo(cod_calendario_dia_motivo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: calendario_dia_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: categoria_nivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: categoria_nivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_suspensao_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_suspensao_ref_cod_motivo_suspensao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_motivo_suspensao_fkey FOREIGN KEY (ref_cod_motivo_suspensao) REFERENCES motivo_suspensao(cod_motivo_suspensao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_suspensao_ref_usuario_libera_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_suspensao_ref_usuario_suspende_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_suspende_fkey FOREIGN KEY (ref_usuario_suspende) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_cliente_ibfk1; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk1 FOREIGN KEY (ref_cod_cliente) REFERENCES cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_cliente_ibfk2; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk2 FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_exemplar_tipo_ref_cod_cliente_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_cliente_tipo_fkey FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_exemplar_tipo_ref_cod_exemplar_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cliente_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: coffebreak_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: coffebreak_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_instituicao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_nivel_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_nivel_ensino_fkey FOREIGN KEY (ref_cod_nivel_ensino) REFERENCES nivel_ensino(cod_nivel_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_tipo_avaliacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_cod_tipo_avaliacao) REFERENCES tipo_avaliacao(cod_tipo_avaliacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_tipo_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_tipo_ensino_fkey FOREIGN KEY (ref_cod_tipo_ensino) REFERENCES tipo_ensino(cod_tipo_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_tipo_regime_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_tipo_regime_fkey FOREIGN KEY (ref_cod_tipo_regime) REFERENCES tipo_regime(cod_tipo_regime) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_cod_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_cod_usuario_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: curso_ref_usuario_exc_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY curso
    ADD CONSTRAINT curso_ref_usuario_exc_fk FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina
    ADD CONSTRAINT disciplina_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina
    ADD CONSTRAINT disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina
    ADD CONSTRAINT disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_serie_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_serie_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_topico_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: disciplina_topico_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dispensa_disciplina_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dispensa_disciplina_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dispensa_disciplina_ref_cod_tipo_dispensa_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_tipo_dispensa_fkey FOREIGN KEY (ref_cod_tipo_dispensa) REFERENCES tipo_dispensa(cod_tipo_dispensa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dispensa_disciplina_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dispensa_disciplina_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ano_letivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ano_letivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ano_letivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_complemento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_complemento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_curso_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_curso_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_curso_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_curso_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_localizacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_localizacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_localizacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_rede_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_rede_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_cod_escola_localizacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_cod_escola_localizacao_fkey FOREIGN KEY (ref_cod_escola_localizacao) REFERENCES escola_localizacao(cod_escola_localizacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_cod_escola_rede_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_cod_escola_rede_ensino_fkey FOREIGN KEY (ref_cod_escola_rede_ensino) REFERENCES escola_rede_ensino(cod_escola_rede_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola
    ADD CONSTRAINT escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_disciplina_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_disciplina_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: escola_serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_emprestimo_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_emprestimo_ref_cod_exemplar_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_exemplar_fkey FOREIGN KEY (ref_cod_exemplar) REFERENCES exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_emprestimo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_emprestimo_ref_usuario_devolucao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_devolucao_fkey FOREIGN KEY (ref_usuario_devolucao) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_cod_fonte_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_cod_fonte_fkey FOREIGN KEY (ref_cod_fonte) REFERENCES fonte(cod_fonte) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_cod_motivo_baixa_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_cod_motivo_baixa_fkey FOREIGN KEY (ref_cod_motivo_baixa) REFERENCES motivo_baixa(cod_motivo_baixa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_cod_situacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_cod_situacao_fkey FOREIGN KEY (ref_cod_situacao) REFERENCES situacao(cod_situacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar
    ADD CONSTRAINT exemplar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_tipo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: exemplar_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_aluno_ref_cod_curso_disciplina; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_aluno_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_compensado_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_compensado_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_compensado_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_compensado_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: falta_atraso_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: faltas_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY faltas
    ADD CONSTRAINT faltas_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: faltas_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY faltas
    ADD CONSTRAINT faltas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ref_cod_biblioteca_cliente; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY cliente_tipo_cliente
    ADD CONSTRAINT fk_ref_cod_biblioteca_cliente FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ref_cod_exemplar; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reservas
    ADD CONSTRAINT fk_ref_cod_exemplar FOREIGN KEY (ref_cod_exemplar) REFERENCES exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fonte_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY fonte
    ADD CONSTRAINT fonte_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fonte_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY fonte
    ADD CONSTRAINT fonte_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fonte_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY fonte
    ADD CONSTRAINT fonte_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: funca_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT funca_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: funcao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: funcao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY funcao
    ADD CONSTRAINT funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: habilitacao_curso_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: habilitacao_curso_ref_cod_habilitacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_habilitacao_fkey FOREIGN KEY (ref_cod_habilitacao) REFERENCES habilitacao(cod_habilitacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: habilitacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY habilitacao
    ADD CONSTRAINT habilitacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: habilitacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: habilitacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: historico_disciplinas_ref_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey FOREIGN KEY (ref_ref_cod_aluno, ref_sequencial) REFERENCES historico_escolar(ref_cod_aluno, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: historico_escolar_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: historico_escolar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: historico_escolar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_comodo_funcao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_comodo_funcao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_comodo_ref_cod_escola; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_comodo_ref_cod_infra_comodo_funcao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_comodo_funcao_fkey FOREIGN KEY (ref_cod_infra_comodo_funcao) REFERENCES infra_comodo_funcao(cod_infra_comodo_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_comodo_ref_cod_infra_predio_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_predio_fkey FOREIGN KEY (ref_cod_infra_predio) REFERENCES infra_predio(cod_infra_predio) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_comodo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_comodo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio
    ADD CONSTRAINT infra_predio_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: infra_predio_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: instituicao_ref_idtlog_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY instituicao
    ADD CONSTRAINT instituicao_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: instituicao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY instituicao
    ADD CONSTRAINT instituicao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: instituicao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY instituicao
    ADD CONSTRAINT instituicao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_didatico_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_didatico_ref_cod_material_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_material_tipo_fkey FOREIGN KEY (ref_cod_material_tipo) REFERENCES material_tipo(cod_material_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_didatico_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_didatico_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_tipo_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_tipo
    ADD CONSTRAINT material_tipo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: material_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_excessao_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_excessao_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula, ref_cod_turma, ref_sequencial) REFERENCES matricula_turma(ref_cod_matricula, ref_cod_turma, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ocorrencia_discipli_ref_cod_tipo_ocorrencia_disc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_discipli_ref_cod_tipo_ocorrencia_disc_fkey FOREIGN KEY (ref_cod_tipo_ocorrencia_disciplinar) REFERENCES tipo_ocorrencia_disciplinar(cod_tipo_ocorrencia_disciplinar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ocorrencia_disciplinar_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ocorrencia_disciplinar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ocorrencia_disciplinar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ref_cod_reserva_vaga_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_ref_cod_reserva_vaga_fkey FOREIGN KEY (ref_cod_reserva_vaga) REFERENCES reserva_vaga(cod_reserva_vaga) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT matricula_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_turma_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_turma_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_turma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: matricula_turma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_tipo_usuario_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_tipo_usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: modulo_ref_cod_instituicao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY modulo
    ADD CONSTRAINT modulo_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: modulo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY modulo
    ADD CONSTRAINT modulo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: modulo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY modulo
    ADD CONSTRAINT modulo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_afastamento_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_afastamento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_afastamento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_baixa_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_baixa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_baixa_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_suspensao_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_suspensao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: motivo_suspensao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ensino_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ref_cod_categoria_nivel_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel
    ADD CONSTRAINT nivel_ref_cod_categoria_nivel_fkey FOREIGN KEY (ref_cod_categoria_nivel) REFERENCES categoria_nivel(cod_categoria_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ref_cod_nivel_anterior_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel
    ADD CONSTRAINT nivel_ref_cod_nivel_anterior_fkey FOREIGN KEY (ref_cod_nivel_anterior) REFERENCES nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel
    ADD CONSTRAINT nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nivel
    ADD CONSTRAINT nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_cod_curso_disciplina; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_ref_cod_tipo_avaliacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_ref_cod_tipo_avaliacao, ref_sequencial) REFERENCES tipo_avaliacao_valores(ref_cod_tipo_avaliacao, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: nota_aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: operador_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY operador
    ADD CONSTRAINT operador_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: operador_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY operador
    ADD CONSTRAINT operador_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pagamento_divida_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pagamento_multa
    ADD CONSTRAINT pagamento_divida_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pagamento_multa_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pagamento_multa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pre_requisito_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pre_requisito_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_cod_quadro_horario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_cod_quadro_horario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_horarios_ref_servidor_substituto_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_substituto_fkey FOREIGN KEY (ref_servidor_substituto, ref_cod_instituicao_substituto) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario
    ADD CONSTRAINT quadro_horario_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: quadro_horario_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: religiao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: religiao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY religiao
    ADD CONSTRAINT religiao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reserva_vaga_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reserva_vaga_ref_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie, ref_ref_cod_escola) REFERENCES escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reserva_vaga_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reserva_vaga_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reservas_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reservas
    ADD CONSTRAINT reservas_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reservas_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reservas
    ADD CONSTRAINT reservas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reservas_ref_usuario_libera_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY reservas
    ADD CONSTRAINT reservas_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: sequencia_serie_ref_serie_destino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_destino_fkey FOREIGN KEY (ref_serie_destino) REFERENCES serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: sequencia_serie_ref_serie_origem_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_origem_fkey FOREIGN KEY (ref_serie_origem) REFERENCES serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: sequencia_serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: sequencia_serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_pre_requisito_ref_cod_operador_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_operador_fkey FOREIGN KEY (ref_cod_operador) REFERENCES operador(cod_operador) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_pre_requisito_ref_cod_pre_requisito_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_pre_requisito_fkey FOREIGN KEY (ref_cod_pre_requisito) REFERENCES pre_requisito(cod_pre_requisito) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_pre_requisito_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie
    ADD CONSTRAINT serie_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie
    ADD CONSTRAINT serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY serie
    ADD CONSTRAINT serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_afastamento_ref_cod_motivo_afastamento_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_motivo_afastamento_fkey FOREIGN KEY (ref_cod_motivo_afastamento) REFERENCES motivo_afastamento(cod_motivo_afastamento) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_afastamento_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_afastamento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_afastamento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_alocacao_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_alocacao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_alocacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_alocacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_cod_servidor_fkey FOREIGN KEY (cod_servidor) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_curso_ref_cod_formacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_curso
    ADD CONSTRAINT servidor_curso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_cuso_ministra_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_cuso_ministra_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_disciplina_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_disciplina_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_formacao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_formacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_formacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_funcao_ref_cod_funcao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_funcao_fkey FOREIGN KEY (ref_cod_funcao) REFERENCES funcao(cod_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_funcao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_ref_cod_deficiencia_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_deficiencia) REFERENCES cadastro.deficiencia(cod_deficiencia) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_ref_cod_subnivel_; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_ref_cod_subnivel_ FOREIGN KEY (ref_cod_subnivel) REFERENCES subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_ref_idesco_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor
    ADD CONSTRAINT servidor_ref_idesco_fkey FOREIGN KEY (ref_idesco) REFERENCES cadastro.escolaridade(idesco) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: servidor_titulo_concurso_ref_cod_formacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: situacao_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY situacao
    ADD CONSTRAINT situacao_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: situacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY situacao
    ADD CONSTRAINT situacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: situacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY situacao
    ADD CONSTRAINT situacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: subnivel_ref_cod_nivel_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY subnivel
    ADD CONSTRAINT subnivel_ref_cod_nivel_fkey FOREIGN KEY (ref_cod_nivel) REFERENCES nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: subnivel_ref_cod_subnivel_anterior_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY subnivel
    ADD CONSTRAINT subnivel_ref_cod_subnivel_anterior_fkey FOREIGN KEY (ref_cod_subnivel_anterior) REFERENCES subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: subnivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY subnivel
    ADD CONSTRAINT subnivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: subnivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY subnivel
    ADD CONSTRAINT subnivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_avaliacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_avaliacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_avaliacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_avaliacao_valores_ref_cod_tipo_avaliacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_cod_tipo_avaliacao) REFERENCES tipo_avaliacao(cod_tipo_avaliacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_dispensa_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_dispensa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_dispensa_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ensino_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ocorrencia_disciplinar_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ocorrencia_disciplinar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_ocorrencia_disciplinar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_regime_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_regime
    ADD CONSTRAINT tipo_regime_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_regime_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_regime_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_usuario_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: tipo_usuario_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_solicitacao_ref_cod_matricula_entrada_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_entrada_fkey FOREIGN KEY (ref_cod_matricula_entrada) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_solicitacao_ref_cod_matricula_saida_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_saida_fkey FOREIGN KEY (ref_cod_matricula_saida) REFERENCES matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_solicitacao_ref_cod_transferencia_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_transferencia_tipo_fkey FOREIGN KEY (ref_cod_transferencia_tipo) REFERENCES transferencia_tipo(cod_transferencia_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_solicitacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_solicitacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_tipo_ref_cod_escola; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: transferencia_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_dia_semana_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma_dia_semana
    ADD CONSTRAINT turma_dia_semana_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_escola_serie_muil; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_escola_serie_muil FOREIGN KEY (ref_ref_cod_serie_mult, ref_ref_cod_escola_mult) REFERENCES escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_modulo_ref_cod_modulo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_modulo_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_cod_infra_predio_comodo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_cod_infra_predio_comodo_fkey FOREIGN KEY (ref_cod_infra_predio_comodo) REFERENCES infra_predio_comodo(cod_infra_predio_comodo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_cod_regente; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_cod_regente FOREIGN KEY (ref_cod_regente, ref_cod_instituicao_regente) REFERENCES servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: turma_turma_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY turma
    ADD CONSTRAINT turma_turma_tipo_fkey FOREIGN KEY (ref_cod_turma_tipo) REFERENCES turma_tipo(cod_turma_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_cod_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_cod_usuario_fkey FOREIGN KEY (cod_usuario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: usuario_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = pmiotopic, pg_catalog;

--
-- Name: funcionario_su_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY funcionario_su
    ADD CONSTRAINT funcionario_su_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: grupomoderador_ref_cod_grupos_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupomoderador
    ADD CONSTRAINT grupomoderador_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupomoderador_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupomoderador_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupomoderador_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupomoderador
    ADD CONSTRAINT grupomoderador_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupopessoa_ref_cod_grupos_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupopessoa
    ADD CONSTRAINT grupopessoa_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupopessoa_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupopessoa
    ADD CONSTRAINT grupopessoa_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupopessoa_ref_pessoa_cadatro_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_cadatro_fkey FOREIGN KEY (ref_pessoa_cad, ref_grupos_cad) REFERENCES grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupopessoa_ref_pessoa_exclusao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_exclusao_fkey FOREIGN KEY (ref_pessoa_exc, ref_grupos_exc) REFERENCES grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupos_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupos
    ADD CONSTRAINT grupos_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: grupos_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY grupos
    ADD CONSTRAINT grupos_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: notas_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY notas
    ADD CONSTRAINT notas_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: notas_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY notas
    ADD CONSTRAINT notas_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: notas_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY notas
    ADD CONSTRAINT notas_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: participante_ref_cod_reuniao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY participante
    ADD CONSTRAINT participante_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: participante_ref_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY participante
    ADD CONSTRAINT participante_ref_ref_idpes_fkey FOREIGN KEY (ref_ref_idpes, ref_ref_cod_grupos) REFERENCES grupopessoa(ref_idpes, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: reuniao_ref_moderador_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY reuniao
    ADD CONSTRAINT reuniao_ref_moderador_fkey FOREIGN KEY (ref_moderador, ref_grupos_moderador) REFERENCES grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: topicoreuniao_ref_cod_reuniao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: topicoreuniao_ref_cod_topico_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_topico_fkey FOREIGN KEY (ref_cod_topico) REFERENCES topico(cod_topico) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = portal, pg_catalog;

--
-- Name: agenda_compromisso_ref_cod_agenda_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_compromisso_ref_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_ref_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_ref_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_ref_cod_pessoa_exc) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_ref_ref_cod_pessoa_own_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_own_fkey FOREIGN KEY (ref_ref_cod_pessoa_own) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_responsavel_ref_cod_agenda_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: agenda_responsavel_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_editais_editais_empresas_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_1 FOREIGN KEY (ref_cod_compras_editais_editais) REFERENCES compras_editais_editais(cod_compras_editais_editais) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_editais_editais_empresas_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_2 FOREIGN KEY (ref_cod_compras_editais_empresa) REFERENCES compras_editais_empresa(cod_compras_editais_empresa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_editais_editais_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_editais_editais_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_2 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_editais_empresa_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_ibfk_1 FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_funcionarios_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_licitacoes_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_1 FOREIGN KEY (ref_cod_compras_modalidade) REFERENCES compras_modalidade(cod_compras_modalidade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_licitacoes_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_1 FOREIGN KEY (ref_equipe3) REFERENCES compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_2 FOREIGN KEY (ref_pregoeiro) REFERENCES compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_3 FOREIGN KEY (ref_equipe1) REFERENCES compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_4; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_4 FOREIGN KEY (ref_equipe2) REFERENCES compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_5; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_5 FOREIGN KEY (ref_cod_compras_final_pregao) REFERENCES compras_final_pregao(cod_compras_final_pregao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: compras_pregao_execucao_ibfk_6; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_6 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_to_setor_new; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT fk_to_setor_new FOREIGN KEY (ref_cod_setor_new) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: foto_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY foto_portal
    ADD CONSTRAINT foto_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: foto_portal_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY foto_portal
    ADD CONSTRAINT foto_portal_ibfk_2 FOREIGN KEY (ref_cod_credito) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT funcionario_ibfk_1 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: funcionario_ibfk_5; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT funcionario_ibfk_5 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: imagem_ref_cod_imagem_tipo_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY imagem
    ADD CONSTRAINT imagem_ref_cod_imagem_tipo_fkey FOREIGN KEY (ref_cod_imagem_tipo) REFERENCES imagem_tipo(cod_imagem_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: imagem_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: imagem_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: intranet_segur_permissao_negada_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: jor_arquivo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY jor_arquivo
    ADD CONSTRAINT jor_arquivo_ibfk_1 FOREIGN KEY (ref_cod_jor_edicao) REFERENCES jor_edicao(cod_jor_edicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: jor_edicao_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY jor_edicao
    ADD CONSTRAINT jor_edicao_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_email_conteudo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_fila_envio_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_fila_envio_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_2 FOREIGN KEY (ref_cod_mailling_email) REFERENCES mailling_email(cod_mailling_email);


--
-- Name: mailling_fila_envio_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_3 FOREIGN KEY (ref_cod_mailling_email_conteudo) REFERENCES mailling_email_conteudo(cod_mailling_email_conteudo);


--
-- Name: mailling_grupo_email_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_1 FOREIGN KEY (ref_cod_mailling_email) REFERENCES mailling_email(cod_mailling_email) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_grupo_email_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_historico_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_historico_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: mailling_historico_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_3 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_1 FOREIGN KEY (ref_cod_menu_submenu) REFERENCES menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_funcionario_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: menu_submenu_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY menu_submenu
    ADD CONSTRAINT menu_submenu_ibfk_1 FOREIGN KEY (ref_cod_menu_menu) REFERENCES menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: not_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY not_portal
    ADD CONSTRAINT not_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: not_portal_tipo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: not_portal_tipo_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_2 FOREIGN KEY (ref_cod_not_tipo) REFERENCES not_tipo(cod_not_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: not_vinc_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: notificacao_ref_cod_funcionario_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY notificacao
    ADD CONSTRAINT notificacao_ref_cod_funcionario_fkey FOREIGN KEY (ref_cod_funcionario) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_atividade_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_ramo_atividade) REFERENCES pessoa_ramo_atividade(cod_ramo_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_fj_pessoa_atividade_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_pessoa_atividade) REFERENCES pessoa_atividade(cod_pessoa_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: pessoa_fj_pessoa_atividade_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_2 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: portal_banner_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal_banner
    ADD CONSTRAINT portal_banner_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: portal_concurso_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal_concurso
    ADD CONSTRAINT portal_concurso_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ref_cod_menu_pai_fk; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY menu_menu
    ADD CONSTRAINT ref_cod_menu_pai_fk FOREIGN KEY (ref_cod_menu_pai) REFERENCES menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


SET search_path = public, pg_catalog;

--
-- Name: bairro_regiao_ref_cod_regiao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_cod_regiao_fkey FOREIGN KEY (ref_cod_regiao) REFERENCES regiao(cod_regiao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: bairro_regiao_ref_idbai_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_idbai_fkey FOREIGN KEY (ref_idbai) REFERENCES bairro(idbai) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_bairro_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT fk_bairro_municipio FOREIGN KEY (idmun) REFERENCES municipio(idmun);


--
-- Name: fk_bairro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_bairro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_bairro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_bairro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_logr_logr_fonetico; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro_fonetico
    ADD CONSTRAINT fk_logr_logr_fonetico FOREIGN KEY (idlog) REFERENCES logradouro(idlog);


--
-- Name: fk_logradouro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_logradouro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_logradouro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_logradouro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_logradouro_tipo_log; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY logradouro
    ADD CONSTRAINT fk_logradouro_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);


--
-- Name: fk_setor_idsetredir; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT fk_setor_idsetredir FOREIGN KEY (idsetredir) REFERENCES setor(idset) ON DELETE RESTRICT;


--
-- Name: fk_setor_idsetsub; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT fk_setor_idsetsub FOREIGN KEY (idsetsub) REFERENCES setor(idset) ON DELETE CASCADE;


--
-- Name: fk_vila_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY vila
    ADD CONSTRAINT fk_vila_municipio FOREIGN KEY (idmun) REFERENCES municipio(idmun);


SET search_path = urbano, pg_catalog;

--
-- Name: fk_cep_log_bairro_bai; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_bai FOREIGN KEY (idbai) REFERENCES public.bairro(idbai);


--
-- Name: fk_cep_log_bairro_cep_log; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_cep_log FOREIGN KEY (cep, idlog) REFERENCES cep_logradouro(cep, idlog) ON DELETE CASCADE;


--
-- Name: fk_cep_logradouro_bairro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_bairro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_bairro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_bairro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_logradouro; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_logradouro FOREIGN KEY (idlog) REFERENCES public.logradouro(idlog) ON DELETE CASCADE;


--
-- Name: fk_cep_logradouro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- Name: fk_cep_logradouro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

--
-- Alter sequences to start with 1
--
ALTER SEQUENCE acesso.funcao_idfunc_seq
  MINVALUE 0;
SELECT setval('acesso.funcao_idfunc_seq', 1, false);
ALTER SEQUENCE acesso.grupo_idgrp_seq
  MINVALUE 0;
SELECT setval('acesso.grupo_idgrp_seq', 1, false);
ALTER SEQUENCE acesso.instituicao_idins_seq
  MINVALUE 0;
SELECT setval('acesso.instituicao_idins_seq', 1, false);
ALTER SEQUENCE acesso.menu_idmen_seq
  MINVALUE 0;
SELECT setval('acesso.menu_idmen_seq', 1, false);
ALTER SEQUENCE acesso.operacao_idope_seq
  MINVALUE 0;
SELECT setval('acesso.operacao_idope_seq', 1, false);
ALTER SEQUENCE acesso.sistema_idsis_seq
  MINVALUE 0;
SELECT setval('acesso.sistema_idsis_seq', 1, false);
ALTER SEQUENCE alimentos.baixa_guia_produto_idbap_seq
  MINVALUE 0;
SELECT setval('alimentos.baixa_guia_produto_idbap_seq', 1, false);
ALTER SEQUENCE alimentos.baixa_guia_remessa_idbai_seq
  MINVALUE 0;
SELECT setval('alimentos.baixa_guia_remessa_idbai_seq', 1, false);
ALTER SEQUENCE alimentos.calendario_idcad_seq
  MINVALUE 0;
SELECT setval('alimentos.calendario_idcad_seq', 1, false);
ALTER SEQUENCE alimentos.cardapio_idcar_seq
  MINVALUE 0;
SELECT setval('alimentos.cardapio_idcar_seq', 1, false);
ALTER SEQUENCE alimentos.cardapio_produto_idcpr_seq
  MINVALUE 0;
SELECT setval('alimentos.cardapio_produto_idcpr_seq', 1, false);
ALTER SEQUENCE alimentos.composto_quimico_idcom_seq
  MINVALUE 0;
SELECT setval('alimentos.composto_quimico_idcom_seq', 1, false);
ALTER SEQUENCE alimentos.contrato_idcon_seq
  MINVALUE 0;
SELECT setval('alimentos.contrato_idcon_seq', 1, false);
ALTER SEQUENCE alimentos.contrato_produto_idcop_seq
  MINVALUE 0;
SELECT setval('alimentos.contrato_produto_idcop_seq', 1, false);
ALTER SEQUENCE alimentos.evento_ideve_seq
  MINVALUE 0;
SELECT setval('alimentos.evento_ideve_seq', 1, false);
ALTER SEQUENCE alimentos.faixa_composto_quimico_idfcp_seq
  MINVALUE 0;
SELECT setval('alimentos.faixa_composto_quimico_idfcp_seq', 1, false);
ALTER SEQUENCE alimentos.faixa_etaria_idfae_seq
  MINVALUE 0;
SELECT setval('alimentos.faixa_etaria_idfae_seq', 1, false);
ALTER SEQUENCE alimentos.fornecedor_idfor_seq
  MINVALUE 0;
SELECT setval('alimentos.fornecedor_idfor_seq', 1, false);
ALTER SEQUENCE alimentos.grupo_quimico_idgrpq_seq
  MINVALUE 0;
SELECT setval('alimentos.grupo_quimico_idgrpq_seq', 1, false);
ALTER SEQUENCE alimentos.guia_produto_diario_idguiaprodiario_seq
  MINVALUE 0;
SELECT setval('alimentos.guia_produto_diario_idguiaprodiario_seq', 1, false);
ALTER SEQUENCE alimentos.guia_remessa_idgui_seq
  MINVALUE 0;
SELECT setval('alimentos.guia_remessa_idgui_seq', 1, false);
ALTER SEQUENCE alimentos.guia_remessa_produto_idgup_seq
  MINVALUE 0;
SELECT setval('alimentos.guia_remessa_produto_idgup_seq', 1, false);
ALTER SEQUENCE alimentos.log_guia_remessa_idlogguia_seq
  MINVALUE 0;
SELECT setval('alimentos.log_guia_remessa_idlogguia_seq', 1, false);
ALTER SEQUENCE alimentos.pessoa_idpes_seq
  MINVALUE 0;
SELECT setval('alimentos.pessoa_idpes_seq', 1, false);
ALTER SEQUENCE alimentos.produto_idpro_seq
  MINVALUE 0;
SELECT setval('alimentos.produto_idpro_seq', 1, false);
ALTER SEQUENCE alimentos.produto_composto_quimico_idpcq_seq
  MINVALUE 0;
SELECT setval('alimentos.produto_composto_quimico_idpcq_seq', 1, false);
ALTER SEQUENCE alimentos.produto_fornecedor_idprf_seq
  MINVALUE 0;
SELECT setval('alimentos.produto_fornecedor_idprf_seq', 1, false);
ALTER SEQUENCE alimentos.produto_medida_caseira_idpmc_seq
  MINVALUE 0;
SELECT setval('alimentos.produto_medida_caseira_idpmc_seq', 1, false);
ALTER SEQUENCE alimentos.receita_idrec_seq
  MINVALUE 0;
SELECT setval('alimentos.receita_idrec_seq', 1, false);
ALTER SEQUENCE alimentos.receita_composto_quimico_idrcq_seq
  MINVALUE 0;
SELECT setval('alimentos.receita_composto_quimico_idrcq_seq', 1, false);
ALTER SEQUENCE alimentos.receita_produto_idrpr_seq
  MINVALUE 0;
SELECT setval('alimentos.receita_produto_idrpr_seq', 1, false);
ALTER SEQUENCE alimentos.tipo_produto_idtip_seq
  MINVALUE 0;
SELECT setval('alimentos.tipo_produto_idtip_seq', 1, false);
ALTER SEQUENCE alimentos.tipo_refeicao_idtre_seq
  MINVALUE 0;
SELECT setval('alimentos.tipo_refeicao_idtre_seq', 1, false);
ALTER SEQUENCE alimentos.tipo_unidade_idtip_seq
  MINVALUE 0;
SELECT setval('alimentos.tipo_unidade_idtip_seq', 1, false);
ALTER SEQUENCE alimentos.unidade_atendida_iduni_seq
  MINVALUE 0;
SELECT setval('alimentos.unidade_atendida_iduni_seq', 1, false);
ALTER SEQUENCE alimentos.unidade_faixa_etaria_idfeu_seq
  MINVALUE 0;
SELECT setval('alimentos.unidade_faixa_etaria_idfeu_seq', 1, false);
ALTER SEQUENCE cadastro.deficiencia_cod_deficiencia_seq
  MINVALUE 0;
SELECT setval('cadastro.deficiencia_cod_deficiencia_seq', 1, false);
ALTER SEQUENCE cadastro.orgao_emissor_rg_idorg_rg_seq
  MINVALUE 0;
SELECT setval('cadastro.orgao_emissor_rg_idorg_rg_seq', 1, false);
ALTER SEQUENCE cadastro.raca_cod_raca_seq
  MINVALUE 0;
SELECT setval('cadastro.raca_cod_raca_seq', 1, false);
ALTER SEQUENCE cadastro.religiao_cod_religiao_seq
  MINVALUE 0;
SELECT setval('cadastro.religiao_cod_religiao_seq', 1, false);
ALTER SEQUENCE cadastro.seq_pessoa
  MINVALUE 0;
SELECT setval('cadastro.seq_pessoa', 1, false);
ALTER SEQUENCE consistenciacao.campo_metadado_id_campo_met_seq
  MINVALUE 0;
SELECT setval('consistenciacao.campo_metadado_id_campo_met_seq', 1, false);
ALTER SEQUENCE consistenciacao.confrontacao_idcon_seq
  MINVALUE 0;
SELECT setval('consistenciacao.confrontacao_idcon_seq', 1, false);
ALTER SEQUENCE consistenciacao.fonte_idfon_seq
  MINVALUE 0;
SELECT setval('consistenciacao.fonte_idfon_seq', 1, false);
ALTER SEQUENCE consistenciacao.incoerencia_idinc_seq
  MINVALUE 0;
SELECT setval('consistenciacao.incoerencia_idinc_seq', 1, false);
ALTER SEQUENCE consistenciacao.incoerencia_documento_id_inc_doc_seq
  MINVALUE 0;
SELECT setval('consistenciacao.incoerencia_documento_id_inc_doc_seq', 1, false);
ALTER SEQUENCE consistenciacao.incoerencia_endereco_id_inc_end_seq
  MINVALUE 0;
SELECT setval('consistenciacao.incoerencia_endereco_id_inc_end_seq', 1, false);
ALTER SEQUENCE consistenciacao.incoerencia_fone_id_inc_fone_seq
  MINVALUE 0;
SELECT setval('consistenciacao.incoerencia_fone_id_inc_fone_seq', 1, false);
ALTER SEQUENCE consistenciacao.metadado_idmet_seq
  MINVALUE 0;
SELECT setval('consistenciacao.metadado_idmet_seq', 1, false);
ALTER SEQUENCE consistenciacao.regra_campo_idreg_seq
  MINVALUE 0;
SELECT setval('consistenciacao.regra_campo_idreg_seq', 1, false);
ALTER SEQUENCE pmiacoes.acao_governo_cod_acao_governo_seq
  MINVALUE 0;
SELECT setval('pmiacoes.acao_governo_cod_acao_governo_seq', 1, false);
ALTER SEQUENCE pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq
  MINVALUE 0;
SELECT setval('pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq', 1, false);
ALTER SEQUENCE pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq
  MINVALUE 0;
SELECT setval('pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq', 1, false);
ALTER SEQUENCE pmiacoes.categoria_cod_categoria_seq
  MINVALUE 0;
SELECT setval('pmiacoes.categoria_cod_categoria_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.acontecimento_cod_acontecimento_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.acontecimento_cod_acontecimento_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.artigo_cod_artigo_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.artigo_cod_artigo_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.foto_evento_cod_foto_evento_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.foto_evento_cod_foto_evento_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.foto_vinc_cod_foto_vinc_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.foto_vinc_cod_foto_vinc_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.itinerario_cod_itinerario_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.itinerario_cod_itinerario_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.menu_cod_menu_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.menu_cod_menu_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.menu_portal_cod_menu_portal_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.menu_portal_cod_menu_portal_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.portais_cod_portais_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.portais_cod_portais_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.servicos_cod_servicos_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.servicos_cod_servicos_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.sistema_cod_sistema_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.sistema_cod_sistema_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.submenu_portal_cod_submenu_portal_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.submenu_portal_cod_submenu_portal_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.telefones_cod_telefones_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.telefones_cod_telefones_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.topo_portal_cod_topo_portal_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.topo_portal_cod_topo_portal_seq', 1, false);
ALTER SEQUENCE pmicontrolesis.tutormenu_cod_tutormenu_seq
  MINVALUE 0;
SELECT setval('pmicontrolesis.tutormenu_cod_tutormenu_seq', 1, false);
ALTER SEQUENCE pmidrh.diaria_cod_diaria_seq
  MINVALUE 0;
SELECT setval('pmidrh.diaria_cod_diaria_seq', 1, false);
ALTER SEQUENCE pmidrh.diaria_grupo_cod_diaria_grupo_seq
  MINVALUE 0;
SELECT setval('pmidrh.diaria_grupo_cod_diaria_grupo_seq', 1, false);
ALTER SEQUENCE pmidrh.diaria_valores_cod_diaria_valores_seq
  MINVALUE 0;
SELECT setval('pmidrh.diaria_valores_cod_diaria_valores_seq', 1, false);
ALTER SEQUENCE pmidrh.setor_cod_setor_seq
  MINVALUE 0;
SELECT setval('pmidrh.setor_cod_setor_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_cod_acervo_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_cod_acervo_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_assunto_cod_acervo_assunto_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_assunto_cod_acervo_assunto_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_autor_cod_acervo_autor_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_autor_cod_acervo_autor_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_colecao_cod_acervo_colecao_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_colecao_cod_acervo_colecao_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_editora_cod_acervo_editora_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_editora_cod_acervo_editora_seq', 1, false);
ALTER SEQUENCE pmieducar.acervo_idioma_cod_acervo_idioma_seq
  MINVALUE 0;
SELECT setval('pmieducar.acervo_idioma_cod_acervo_idioma_seq', 1, false);
ALTER SEQUENCE pmieducar.aluno_cod_aluno_seq
  MINVALUE 0;
SELECT setval('pmieducar.aluno_cod_aluno_seq', 1, false);
ALTER SEQUENCE pmieducar.aluno_beneficio_cod_aluno_beneficio_seq
  MINVALUE 0;
SELECT setval('pmieducar.aluno_beneficio_cod_aluno_beneficio_seq', 1, false);
ALTER SEQUENCE pmieducar.biblioteca_cod_biblioteca_seq
  MINVALUE 0;
SELECT setval('pmieducar.biblioteca_cod_biblioteca_seq', 1, false);
ALTER SEQUENCE pmieducar.biblioteca_feriados_cod_feriado_seq
  MINVALUE 0;
SELECT setval('pmieducar.biblioteca_feriados_cod_feriado_seq', 1, false);
ALTER SEQUENCE pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq
  MINVALUE 0;
SELECT setval('pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq', 1, false);
ALTER SEQUENCE pmieducar.calendario_anotacao_cod_calendario_anotacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.calendario_anotacao_cod_calendario_anotacao_seq', 1, false);
ALTER SEQUENCE pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq
  MINVALUE 0;
SELECT setval('pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq', 1, false);
ALTER SEQUENCE pmieducar.categoria_nivel_cod_categoria_nivel_seq
  MINVALUE 0;
SELECT setval('pmieducar.categoria_nivel_cod_categoria_nivel_seq', 1, false);
ALTER SEQUENCE pmieducar.cliente_cod_cliente_seq
  MINVALUE 0;
SELECT setval('pmieducar.cliente_cod_cliente_seq', 1, false);
ALTER SEQUENCE pmieducar.cliente_tipo_cod_cliente_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.cliente_tipo_cod_cliente_tipo_seq', 1, false);
ALTER SEQUENCE pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq', 1, false);
ALTER SEQUENCE pmieducar.curso_cod_curso_seq
  MINVALUE 0;
SELECT setval('pmieducar.curso_cod_curso_seq', 1, false);
ALTER SEQUENCE pmieducar.disciplina_cod_disciplina_seq
  MINVALUE 0;
SELECT setval('pmieducar.disciplina_cod_disciplina_seq', 1, false);
ALTER SEQUENCE pmieducar.disciplina_topico_cod_disciplina_topico_seq
  MINVALUE 0;
SELECT setval('pmieducar.disciplina_topico_cod_disciplina_topico_seq', 1, false);
ALTER SEQUENCE pmieducar.escola_cod_escola_seq
  MINVALUE 0;
SELECT setval('pmieducar.escola_cod_escola_seq', 1, false);
ALTER SEQUENCE pmieducar.escola_localizacao_cod_escola_localizacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.escola_localizacao_cod_escola_localizacao_seq', 1, false);
ALTER SEQUENCE pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq
  MINVALUE 0;
SELECT setval('pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq', 1, false);
ALTER SEQUENCE pmieducar.exemplar_cod_exemplar_seq
  MINVALUE 0;
SELECT setval('pmieducar.exemplar_cod_exemplar_seq', 1, false);
ALTER SEQUENCE pmieducar.exemplar_emprestimo_cod_emprestimo_seq
  MINVALUE 0;
SELECT setval('pmieducar.exemplar_emprestimo_cod_emprestimo_seq', 1, false);
ALTER SEQUENCE pmieducar.exemplar_tipo_cod_exemplar_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.exemplar_tipo_cod_exemplar_tipo_seq', 1, false);
ALTER SEQUENCE pmieducar.falta_aluno_cod_falta_aluno_seq
  MINVALUE 0;
SELECT setval('pmieducar.falta_aluno_cod_falta_aluno_seq', 1, false);
ALTER SEQUENCE pmieducar.falta_atraso_cod_falta_atraso_seq
  MINVALUE 0;
SELECT setval('pmieducar.falta_atraso_cod_falta_atraso_seq', 1, false);
ALTER SEQUENCE pmieducar.falta_atraso_compensado_cod_compensado_seq
  MINVALUE 0;
SELECT setval('pmieducar.falta_atraso_compensado_cod_compensado_seq', 1, false);
ALTER SEQUENCE pmieducar.faltas_sequencial_seq
  MINVALUE 0;
SELECT setval('pmieducar.faltas_sequencial_seq', 1, false);
ALTER SEQUENCE pmieducar.fonte_cod_fonte_seq
  MINVALUE 0;
SELECT setval('pmieducar.fonte_cod_fonte_seq', 1, false);
ALTER SEQUENCE pmieducar.funcao_cod_funcao_seq
  MINVALUE 0;
SELECT setval('pmieducar.funcao_cod_funcao_seq', 1, false);
ALTER SEQUENCE pmieducar.habilitacao_cod_habilitacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.habilitacao_cod_habilitacao_seq', 1, false);
ALTER SEQUENCE pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq
  MINVALUE 0;
SELECT setval('pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq', 1, false);
ALTER SEQUENCE pmieducar.infra_predio_cod_infra_predio_seq
  MINVALUE 0;
SELECT setval('pmieducar.infra_predio_cod_infra_predio_seq', 1, false);
ALTER SEQUENCE pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq
  MINVALUE 0;
SELECT setval('pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq', 1, false);
ALTER SEQUENCE pmieducar.instituicao_cod_instituicao_seq
  MINVALUE 0;
SELECT setval('pmieducar.instituicao_cod_instituicao_seq', 1, false);
ALTER SEQUENCE pmieducar.material_didatico_cod_material_didatico_seq
  MINVALUE 0;
SELECT setval('pmieducar.material_didatico_cod_material_didatico_seq', 1, false);
ALTER SEQUENCE pmieducar.material_tipo_cod_material_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.material_tipo_cod_material_tipo_seq', 1, false);
ALTER SEQUENCE pmieducar.matricula_cod_matricula_seq
  MINVALUE 0;
SELECT setval('pmieducar.matricula_cod_matricula_seq', 1, false);
ALTER SEQUENCE pmieducar.matricula_excessao_cod_aluno_excessao_seq
  MINVALUE 0;
SELECT setval('pmieducar.matricula_excessao_cod_aluno_excessao_seq', 1, false);
ALTER SEQUENCE pmieducar.modulo_cod_modulo_seq
  MINVALUE 0;
SELECT setval('pmieducar.modulo_cod_modulo_seq', 1, false);
ALTER SEQUENCE pmieducar.motivo_afastamento_cod_motivo_afastamento_seq
  MINVALUE 0;
SELECT setval('pmieducar.motivo_afastamento_cod_motivo_afastamento_seq', 1, false);
ALTER SEQUENCE pmieducar.motivo_baixa_cod_motivo_baixa_seq
  MINVALUE 0;
SELECT setval('pmieducar.motivo_baixa_cod_motivo_baixa_seq', 1, false);
ALTER SEQUENCE pmieducar.motivo_suspensao_cod_motivo_suspensao_seq
  MINVALUE 0;
SELECT setval('pmieducar.motivo_suspensao_cod_motivo_suspensao_seq', 1, false);
ALTER SEQUENCE pmieducar.nivel_cod_nivel_seq
  MINVALUE 0;
SELECT setval('pmieducar.nivel_cod_nivel_seq', 1, false);
ALTER SEQUENCE pmieducar.nivel_ensino_cod_nivel_ensino_seq
  MINVALUE 0;
SELECT setval('pmieducar.nivel_ensino_cod_nivel_ensino_seq', 1, false);
ALTER SEQUENCE pmieducar.nota_aluno_cod_nota_aluno_seq
  MINVALUE 0;
SELECT setval('pmieducar.nota_aluno_cod_nota_aluno_seq', 1, false);
ALTER SEQUENCE pmieducar.operador_cod_operador_seq
  MINVALUE 0;
SELECT setval('pmieducar.operador_cod_operador_seq', 1, false);
ALTER SEQUENCE pmieducar.pagamento_multa_cod_pagamento_multa_seq
  MINVALUE 0;
SELECT setval('pmieducar.pagamento_multa_cod_pagamento_multa_seq', 1, false);
ALTER SEQUENCE pmieducar.pre_requisito_cod_pre_requisito_seq
  MINVALUE 0;
SELECT setval('pmieducar.pre_requisito_cod_pre_requisito_seq', 1, false);
ALTER SEQUENCE pmieducar.quadro_horario_cod_quadro_horario_seq
  MINVALUE 0;
SELECT setval('pmieducar.quadro_horario_cod_quadro_horario_seq', 1, false);
ALTER SEQUENCE pmieducar.religiao_cod_religiao_seq
  MINVALUE 0;
SELECT setval('pmieducar.religiao_cod_religiao_seq', 1, false);
ALTER SEQUENCE pmieducar.reserva_vaga_cod_reserva_vaga_seq
  MINVALUE 0;
SELECT setval('pmieducar.reserva_vaga_cod_reserva_vaga_seq', 1, false);
ALTER SEQUENCE pmieducar.reservas_cod_reserva_seq
  MINVALUE 0;
SELECT setval('pmieducar.reservas_cod_reserva_seq', 1, false);
ALTER SEQUENCE pmieducar.serie_cod_serie_seq
  MINVALUE 0;
SELECT setval('pmieducar.serie_cod_serie_seq', 1, false);
ALTER SEQUENCE pmieducar.servidor_alocacao_cod_servidor_alocacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.servidor_alocacao_cod_servidor_alocacao_seq', 1, false);
ALTER SEQUENCE pmieducar.servidor_curso_cod_servidor_curso_seq
  MINVALUE 0;
SELECT setval('pmieducar.servidor_curso_cod_servidor_curso_seq', 1, false);
ALTER SEQUENCE pmieducar.servidor_formacao_cod_formacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.servidor_formacao_cod_formacao_seq', 1, false);
ALTER SEQUENCE pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq
  MINVALUE 0;
SELECT setval('pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq', 1, false);
ALTER SEQUENCE pmieducar.situacao_cod_situacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.situacao_cod_situacao_seq', 1, false);
ALTER SEQUENCE pmieducar.subnivel_cod_subnivel_seq
  MINVALUE 0;
SELECT setval('pmieducar.subnivel_cod_subnivel_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_dispensa_cod_tipo_dispensa_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_dispensa_cod_tipo_dispensa_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_ensino_cod_tipo_ensino_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_ensino_cod_tipo_ensino_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_regime_cod_tipo_regime_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_regime_cod_tipo_regime_seq', 1, false);
ALTER SEQUENCE pmieducar.tipo_usuario_cod_tipo_usuario_seq
  MINVALUE 0;
SELECT setval('pmieducar.tipo_usuario_cod_tipo_usuario_seq', 1, false);
ALTER SEQUENCE pmieducar.transferencia_solicitacao_cod_transferencia_solicitacao_seq
  MINVALUE 0;
SELECT setval('pmieducar.transferencia_solicitacao_cod_transferencia_solicitacao_seq', 1, false);
ALTER SEQUENCE pmieducar.transferencia_tipo_cod_transferencia_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.transferencia_tipo_cod_transferencia_tipo_seq', 1, false);
ALTER SEQUENCE pmieducar.turma_cod_turma_seq
  MINVALUE 0;
SELECT setval('pmieducar.turma_cod_turma_seq', 1, false);
ALTER SEQUENCE pmieducar.turma_tipo_cod_turma_tipo_seq
  MINVALUE 0;
SELECT setval('pmieducar.turma_tipo_cod_turma_tipo_seq', 1, false);
ALTER SEQUENCE pmiotopic.grupos_cod_grupos_seq
  MINVALUE 0;
SELECT setval('pmiotopic.grupos_cod_grupos_seq', 1, false);
ALTER SEQUENCE pmiotopic.reuniao_cod_reuniao_seq
  MINVALUE 0;
SELECT setval('pmiotopic.reuniao_cod_reuniao_seq', 1, false);
ALTER SEQUENCE pmiotopic.topico_cod_topico_seq
  MINVALUE 0;
SELECT setval('pmiotopic.topico_cod_topico_seq', 1, false);
ALTER SEQUENCE portal.acesso_cod_acesso_seq
  MINVALUE 0;
SELECT setval('portal.acesso_cod_acesso_seq', 1, false);
ALTER SEQUENCE portal.agenda_cod_agenda_seq
  MINVALUE 0;
SELECT setval('portal.agenda_cod_agenda_seq', 1, false);
ALTER SEQUENCE portal.agenda_pref_cod_comp_seq
  MINVALUE 0;
SELECT setval('portal.agenda_pref_cod_comp_seq', 1, false);
ALTER SEQUENCE portal.compras_editais_editais_cod_compras_editais_editais_seq
  MINVALUE 0;
SELECT setval('portal.compras_editais_editais_cod_compras_editais_editais_seq', 1, false);
ALTER SEQUENCE portal.compras_editais_empresa_cod_compras_editais_empresa_seq
  MINVALUE 0;
SELECT setval('portal.compras_editais_empresa_cod_compras_editais_empresa_seq', 1, false);
ALTER SEQUENCE portal.compras_final_pregao_cod_compras_final_pregao_seq
  MINVALUE 0;
SELECT setval('portal.compras_final_pregao_cod_compras_final_pregao_seq', 1, false);
ALTER SEQUENCE portal.compras_licitacoes_cod_compras_licitacoes_seq
  MINVALUE 0;
SELECT setval('portal.compras_licitacoes_cod_compras_licitacoes_seq', 1, false);
ALTER SEQUENCE portal.compras_modalidade_cod_compras_modalidade_seq
  MINVALUE 0;
SELECT setval('portal.compras_modalidade_cod_compras_modalidade_seq', 1, false);
ALTER SEQUENCE portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq
  MINVALUE 0;
SELECT setval('portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq', 1, false);
ALTER SEQUENCE portal.compras_prestacao_contas_cod_compras_prestacao_contas_seq
  MINVALUE 0;
SELECT setval('portal.compras_prestacao_contas_cod_compras_prestacao_contas_seq', 1, false);
ALTER SEQUENCE portal.foto_portal_cod_foto_portal_seq
  MINVALUE 0;
SELECT setval('portal.foto_portal_cod_foto_portal_seq', 1, false);
ALTER SEQUENCE portal.foto_secao_cod_foto_secao_seq
  MINVALUE 0;
SELECT setval('portal.foto_secao_cod_foto_secao_seq', 1, false);
ALTER SEQUENCE portal.funcionario_vinculo_cod_funcionario_vinculo_seq
  MINVALUE 0;
SELECT setval('portal.funcionario_vinculo_cod_funcionario_vinculo_seq', 1, false);
ALTER SEQUENCE portal.imagem_cod_imagem_seq
  MINVALUE 0;
SELECT setval('portal.imagem_cod_imagem_seq', 1, false);
ALTER SEQUENCE portal.imagem_tipo_cod_imagem_tipo_seq
  MINVALUE 0;
SELECT setval('portal.imagem_tipo_cod_imagem_tipo_seq', 1, false);
ALTER SEQUENCE portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq
  MINVALUE 0;
SELECT setval('portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq', 1, false);
ALTER SEQUENCE portal.jor_edicao_cod_jor_edicao_seq
  MINVALUE 0;
SELECT setval('portal.jor_edicao_cod_jor_edicao_seq', 1, false);
ALTER SEQUENCE portal.mailling_email_cod_mailling_email_seq
  MINVALUE 0;
SELECT setval('portal.mailling_email_cod_mailling_email_seq', 1, false);
ALTER SEQUENCE portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq
  MINVALUE 0;
SELECT setval('portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq', 1, false);
ALTER SEQUENCE portal.mailling_fila_envio_cod_mailling_fila_envio_seq
  MINVALUE 0;
SELECT setval('portal.mailling_fila_envio_cod_mailling_fila_envio_seq', 1, false);
ALTER SEQUENCE portal.mailling_grupo_cod_mailling_grupo_seq
  MINVALUE 0;
SELECT setval('portal.mailling_grupo_cod_mailling_grupo_seq', 1, false);
ALTER SEQUENCE portal.mailling_historico_cod_mailling_historico_seq
  MINVALUE 0;
SELECT setval('portal.mailling_historico_cod_mailling_historico_seq', 1, false);
ALTER SEQUENCE portal.menu_menu_cod_menu_menu_seq
  MINVALUE 0;
SELECT setval('portal.menu_menu_cod_menu_menu_seq', 1, false);
ALTER SEQUENCE portal.menu_submenu_cod_menu_submenu_seq
  MINVALUE 0;
SELECT setval('portal.menu_submenu_cod_menu_submenu_seq', 1, false);
ALTER SEQUENCE portal.not_portal_cod_not_portal_seq
  MINVALUE 0;
SELECT setval('portal.not_portal_cod_not_portal_seq', 1, false);
ALTER SEQUENCE portal.not_tipo_cod_not_tipo_seq
  MINVALUE 0;
SELECT setval('portal.not_tipo_cod_not_tipo_seq', 1, false);
ALTER SEQUENCE portal.notificacao_cod_notificacao_seq
  MINVALUE 0;
SELECT setval('portal.notificacao_cod_notificacao_seq', 1, false);
ALTER SEQUENCE portal.pessoa_atividade_cod_pessoa_atividade_seq
  MINVALUE 0;
SELECT setval('portal.pessoa_atividade_cod_pessoa_atividade_seq', 1, false);
ALTER SEQUENCE portal.pessoa_fj_cod_pessoa_fj_seq
  MINVALUE 0;
SELECT setval('portal.pessoa_fj_cod_pessoa_fj_seq', 1, false);
ALTER SEQUENCE portal.pessoa_ramo_atividade_cod_ramo_atividade_seq
  MINVALUE 0;
SELECT setval('portal.pessoa_ramo_atividade_cod_ramo_atividade_seq', 1, false);
ALTER SEQUENCE portal.portal_banner_cod_portal_banner_seq
  MINVALUE 0;
SELECT setval('portal.portal_banner_cod_portal_banner_seq', 1, false);
ALTER SEQUENCE portal.portal_concurso_cod_portal_concurso_seq
  MINVALUE 0;
SELECT setval('portal.portal_concurso_cod_portal_concurso_seq', 1, false);
ALTER SEQUENCE portal.sistema_cod_sistema_seq
  MINVALUE 0;
SELECT setval('portal.sistema_cod_sistema_seq', 1, false);
ALTER SEQUENCE public.regiao_cod_regiao_seq
  MINVALUE 0;
SELECT setval('public.regiao_cod_regiao_seq', 1, false);
ALTER SEQUENCE public.seq_bairro
  MINVALUE 0;
SELECT setval('public.seq_bairro', 1, false);
ALTER SEQUENCE public.seq_logradouro
  MINVALUE 0;
SELECT setval('public.seq_logradouro', 1, false);
ALTER SEQUENCE public.seq_municipio
  MINVALUE 0;
SELECT setval('public.seq_municipio', 1, false);
ALTER SEQUENCE public.setor_idset_seq
  MINVALUE 0;
SELECT setval('public.setor_idset_seq', 1, false);

--
-- Configure app search_path so, the next batch file will not have problems with incorrect
-- session configuration.
--
SET search_path TO "$user", public, portal, cadastro, acesso, alimentos, consistenciacao, historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano;

--
-- PostgreSQL database dump complete
--

-- //@UNDO

-- //
