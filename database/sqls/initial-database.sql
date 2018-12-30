SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;


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


CREATE FUNCTION alimentos.fcn_gerar_guia_remessa(text, text, integer, integer, character varying, character varying, character varying, integer) RETURNS text
    LANGUAGE plpgsql
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

END;$_$;


CREATE FUNCTION cadastro.fcn_aft_documento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                DECLARE
                  v_idpes   numeric;
                  BEGIN
                    v_idpes := NEW.idpes;
                    EXECUTE E'DELETE FROM cadastro.documento WHERE ( (rg = \'0\' OR rg IS NULL) AND (idorg_exp_rg IS NULL) AND data_exp_rg IS NULL AND (sigla_uf_exp_rg IS NULL OR length(trim(sigla_uf_exp_rg))=0) AND (tipo_cert_civil = 0 OR tipo_cert_civil IS NULL) AND (num_termo = 0 OR num_termo IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_folha = 0 OR num_folha IS NULL) AND data_emissao_cert_civil IS NULL AND (sigla_uf_cert_civil IS NULL OR length(trim(sigla_uf_cert_civil))=0) AND (sigla_uf_cart_trabalho IS NULL OR length(trim(sigla_uf_cart_trabalho))=0) AND (cartorio_cert_civil IS NULL OR length(trim(cartorio_cert_civil))=0) AND (num_cart_trabalho = 0 OR num_cart_trabalho IS NULL) AND (serie_cart_trabalho = 0 OR serie_cart_trabalho IS NULL) AND data_emissao_cart_trabalho IS NULL AND (num_tit_eleitor = 0 OR num_tit_eleitor IS NULL) AND (zona_tit_eleitor = 0 OR zona_tit_eleitor IS NULL) AND (secao_tit_eleitor = 0 OR secao_tit_eleitor IS NULL) ) AND idpes='||quote_literal(v_idpes)||' AND certidao_nascimento is null';
                  RETURN NEW;
                END; $$;


CREATE FUNCTION cadastro.fcn_aft_documento_provisorio() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  DECLARE
    v_idpes       numeric;
    v_rg        text;
    v_uf_expedicao      text;
    v_verificacao_provisorio  numeric;

    v_comando     text;
    v_registro      record;

    BEGIN
      v_idpes     := NEW.idpes;
      v_rg      := COALESCE(NEW.rg, '-1');
      v_uf_expedicao    := TRIM(COALESCE(NEW.sigla_uf_exp_rg, ''));

      v_verificacao_provisorio:= 0;

      -- verificar se a situação do cadastro da pessoa é provisório
      FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
        IF v_registro.situacao = 'P' THEN
          v_verificacao_provisorio := 1;
        END IF;
      END LOOP;

      -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
      IF LENGTH(v_uf_expedicao) > 0 AND v_rg != '' AND v_rg != '-1' AND v_verificacao_provisorio = 1 THEN
        EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
      END IF;
    RETURN NEW;
  END; $$;


CREATE FUNCTION cadastro.fcn_aft_fisica() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION cadastro.fcn_aft_fisica_cpf_provisorio() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION cadastro.fcn_aft_fisica_provisorio() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION cadastro.fcn_aft_ins_endereco_externo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes   numeric;
  v_tipo_endereco text;
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_endereco := NEW.tipo;
    EXECUTE 'DELETE FROM cadastro.endereco_pessoa WHERE idpes='||quote_literal(v_idpes)||' AND tipo='||v_tipo_endereco||';';
  RETURN NEW;
END; $$;


CREATE FUNCTION cadastro.fcn_aft_ins_endereco_pessoa() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes   numeric;
  v_tipo_endereco text;
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_endereco := NEW.tipo;
    EXECUTE 'DELETE FROM cadastro.endereco_externo WHERE idpes='||quote_literal(v_idpes)||' AND tipo='||v_tipo_endereco||';';
  RETURN NEW;
END; $$;


CREATE FUNCTION consistenciacao.fcn_delete_temp_cadastro_unificacao_cmf(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpes ALIAS for $1;
BEGIN
  -- Deleta dados da tabela temp_cadastro_unificacao_cmf
  DELETE FROM consistenciacao.temp_cadastro_unificacao_cmf WHERE idpes = v_idpes;
  RETURN 0;
END;$_$;


CREATE FUNCTION consistenciacao.fcn_delete_temp_cadastro_unificacao_siam(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpes ALIAS for $1;
BEGIN
  -- Deleta dados da tabela temp_cadastro_unificacao_siam
  DELETE FROM consistenciacao.temp_cadastro_unificacao_siam WHERE idpes = v_idpes;
  RETURN 0;
END;$_$;


CREATE FUNCTION consistenciacao.fcn_documento_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
                          v_numero_rg_novo      text;
                          v_numero_rg_antigo      text;
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
                          v_numero_livro_certidao_civil_novo  varchar;
                          v_numero_livro_certidao_civil_antigo  varchar;
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
                          v_idcam_cartorio_certidao_civil        numeric;
                          v_idcam_uf_emissao_certidao_civil numeric;
                          v_idcam_numero_carteira_trabalho  numeric;
                          v_idcam_numero_serie_carteira_trabalho  numeric;
                          v_idcam_data_emissao_carteira_trabalho  numeric;
                          v_idcam_uf_emissao_carteira_trabalho  numeric;
                          v_idcam_numero_titulo_eleitor   numeric;
                          v_idcam_numero_zona_titulo_eleitor  numeric;
                          v_idcam_numero_secao_titulo_eleitor numeric;

                          /*
                          consistenciacao.historico_campo.credibilidade: 1 = Maxima, 2 = Alta, 3 = Media, 4 = Baixa, 5 = Sem credibilidade
                          cadastro.pessoa.origem_gravacao: M = Migracao, U = Usuario, C = Rotina de confrontacao, O = Oscar
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
                              v_numero_rg_antigo      := '';
                              v_numero_titulo_eleitor_antigo    := 0;
                              v_numero_zona_titulo_antigo   := 0;
                              v_numero_secao_titulo_antigo    := 0;
                              v_numero_cart_trabalho_antigo   := 0;
                              v_numero_serie_cart_trabalho_antigo := 0;
                              v_tipo_certidao_civil_antigo    := 0;
                              v_numero_termo_certidao_civil_antigo  := 0;
                              v_numero_livro_certidao_civil_antigo  := '';
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
                              v_numero_rg_antigo      := COALESCE(OLD.rg, '');
                              v_numero_titulo_eleitor_antigo    := COALESCE(OLD.num_tit_eleitor, 0);
                              v_numero_zona_titulo_antigo   := COALESCE(OLD.zona_tit_eleitor, 0);
                              v_numero_secao_titulo_antigo    := COALESCE(OLD.secao_tit_eleitor, 0);
                              v_numero_cart_trabalho_antigo   := COALESCE(OLD.num_cart_trabalho, 0);
                              v_numero_serie_cart_trabalho_antigo := COALESCE(OLD.serie_cart_trabalho, 0);
                              v_tipo_certidao_civil_antigo    := COALESCE(OLD.tipo_cert_civil, 0);
                              v_numero_termo_certidao_civil_antigo  := COALESCE(OLD.num_termo, 0);
                              v_numero_livro_certidao_civil_antigo  := OLD.num_livro;
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

                            IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuario ou pelo usuario do Oscar
                              v_nova_credibilidade := v_credibilidade_maxima;
                            ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migracao
                              v_nova_credibilidade := v_credibilidade_alta;
                            END IF;

                            IF v_nova_credibilidade > 0 THEN

                              -- DATA DE EXPEDICAO DO RG
                              v_aux_data_nova := COALESCE(TO_CHAR (v_data_expedicao_rg_nova, 'DD/MM/YYYY'), '');
                              v_aux_data_antiga := COALESCE(TO_CHAR (v_data_expedicao_rg_antiga, 'DD/MM/YYYY'), '');

                              IF v_aux_data_nova <> v_aux_data_antiga THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_nova_credibilidade||');';
                              END IF;

                              -- DATA DE EMISSAO CERTIDAO CIVIL
                              v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cert_civil_nova, 'DD/MM/YYYY'), '');
                              v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cert_civil_antiga, 'DD/MM/YYYY'), '');

                              IF v_aux_data_nova <> v_aux_data_antiga THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- DATA DE EMISSAO CARTEIRA DE TRABALHO
                              v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_nova, 'DD/MM/YYYY'), '');
                              v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_antiga, 'DD/MM/YYYY'), '');

                              IF v_aux_data_nova <> v_aux_data_antiga THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
                              END IF;

                              -- ORGAO EXPEDIDOR DO RG
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

                              -- NUMERO TERMO CERTIDAO CIVIL
                              IF v_numero_termo_certidao_civil_novo <> v_numero_termo_certidao_civil_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- NUMERO LIVRO CERTIDAO CIVIL
                              IF v_numero_livro_certidao_civil_novo <> v_numero_livro_certidao_civil_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- NUMERO FOLHA CERTIDAO CIVIL
                              IF v_numero_folha_certidao_civil_novo <> v_numero_folha_certidao_civil_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- CARTORIO CERTIDAO CIVIL
                              IF v_cartorio_certidao_civil_novo <> v_cartorio_certidao_civil_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- UF EXPEDICAO RG
                              IF v_uf_expedicao_rg_novo <> v_uf_expedicao_rg_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_nova_credibilidade||');';
                              END IF;

                              -- UF EMISSAO CERTIDAO CIVIL
                              IF v_uf_emissao_certidao_civil_novo <> v_uf_emissao_certidao_civil_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_nova_credibilidade||');';
                              END IF;

                              -- UF EMISSAO CARTEIRA DE TRABALHO
                              IF v_uf_emissao_carteira_trabalho_novo <> v_uf_emissao_carteira_trabalho_antigo THEN
                                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
                              END IF;

                            END IF;
                            -- Verificar os campos Vazios ou Nulos
                            -- DATA DE EXPEDICAO DO RG
                            IF TRIM(v_data_expedicao_rg_nova::varchar)='' OR v_data_expedicao_rg_nova IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_sem_credibilidade||');';
                            END IF;
                            -- DATA DE EMISSAO CERTIDAO CIVIL
                            IF TRIM(v_data_emissao_cert_civil_nova::varchar)='' OR v_data_emissao_cert_civil_nova IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- DATA DE EMISSAO CARTEIRA DE TRABALHO
                            IF TRIM(v_data_emissao_cart_trabalho_nova::varchar)='' OR v_data_emissao_cart_trabalho_nova IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
                            END IF;
                            -- ORGAO EXPEDIDOR DO RG
                            IF v_orgao_expedicao_rg_novo <= 0 OR v_orgao_expedicao_rg_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_orgao_expedidor_rg||','||v_sem_credibilidade||');';
                            END IF;
                            -- RG
                            IF v_numero_rg_novo = '' OR v_numero_rg_novo IS NULL THEN
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
                            -- NUMERO TERMO CERTIDAO CIVIL
                            IF v_numero_termo_certidao_civil_novo <= 0 OR v_numero_termo_certidao_civil_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- NUMERO LIVRO CERTIDAO CIVIL
                            IF v_numero_livro_certidao_civil_novo = '' OR v_numero_livro_certidao_civil_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- NUMERO FOLHA CERTIDAO CIVIL
                            IF v_numero_folha_certidao_civil_novo <= 0 OR v_numero_folha_certidao_civil_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- CARTORIO CERTIDAO CIVIL
                            IF TRIM(v_cartorio_certidao_civil_novo)='' OR v_cartorio_certidao_civil_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- UF EXPEDICAO RG
                            IF TRIM(v_uf_expedicao_rg_novo)='' OR v_uf_expedicao_rg_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_sem_credibilidade||');';
                            END IF;
                            -- UF EMISSAO CERTIDAO CIVIL
                            IF TRIM(v_uf_emissao_certidao_civil_novo)='' OR v_uf_emissao_certidao_civil_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_sem_credibilidade||');';
                            END IF;
                            -- UF EMISSAO CARTEIRA DE TRABALHO
                            IF TRIM(v_uf_emissao_carteira_trabalho_novo)='' OR v_uf_emissao_carteira_trabalho_novo IS NULL THEN
                              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
                            END IF;

                          RETURN NEW;
                        END;$$;


CREATE FUNCTION consistenciacao.fcn_endereco_externo_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION consistenciacao.fcn_endereco_pessoa_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION consistenciacao.fcn_fisica_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
                          IF TRIM(v_data_nasc_nova::varchar)='' OR v_data_nasc_nova IS NULL THEN
                            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_nasc||','||v_sem_credibilidade||');';
                          END IF;
                          -- DATA DE UNIÃO
                          IF TRIM(v_data_uniao_nova::varchar)='' OR v_data_uniao_nova IS NULL THEN
                            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_uniao||','||v_sem_credibilidade||');';
                          END IF;
                          -- DATA DE ÓBITO
                          IF TRIM(v_data_obito_nova::varchar)='' OR v_data_obito_nova IS NULL THEN
                            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_obito||','||v_sem_credibilidade||');';
                          END IF;
                          -- DATA DE CHEGADA AO BRASIL
                          IF TRIM(v_data_chegada_brasil_nova::varchar)='' OR v_data_chegada_brasil_nova IS NULL THEN
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
                      END; $$;


CREATE FUNCTION consistenciacao.fcn_fone_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION consistenciacao.fcn_gravar_historico_campo(numeric, numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END; $_$;


CREATE FUNCTION consistenciacao.fcn_juridica_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION consistenciacao.fcn_pessoa_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION consistenciacao.fcn_unifica_cadastro(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_cmf(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_sca(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_scd(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_sgp(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_sgpa(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION consistenciacao.fcn_unifica_sgsp(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION conv_functions.pr_normaliza_enderecos() RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur 	record;
i_counter integer;
i_idmun integer;
i_iddis integer;
i_idbai integer;
i_idlog integer;

BEGIN

i_counter := 0;

for cur in SELECT
      ee.idpes,
      ee.idtlog,
      ee.logradouro,
      ee.numero,
      ee.letra,
      ee.complemento,
      ee.bairro,
      ee.cep,
      ee.bloco,
      ee.andar,
      ee.cidade,
      ee.zona_localizacao,
      upper(ee.sigla_uf) as sigla_uf

    FROM cadastro.pessoa p
    LEFT JOIN cadastro.endereco_pessoa ep
      ON ep.idpes = p.idpes
    LEFT JOIN cadastro.endereco_externo ee
      ON ee.idpes = p.idpes
    WHERE ep.idpes is null
    and ee.idpes is not null loop

  i_counter := i_counter + 1;

  i_idmun := 0;
  i_iddis := 0;
  i_idbai := 0;
  i_idlog := 0;

  raise notice 'counter: %', i_counter;

  SELECT COALESCE((select m.idmun
            from public.municipio m
            WHERE unaccent(nome) ilike unaccent(cur.cidade)
            AND sigla_uf = cur.sigla_uf
            LIMIT 1),0) INTO i_idmun;

  If i_idmun = 0 then
    INSERT INTO public.municipio (nome, sigla_uf, tipo, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad)
      VALUES (cur.cidade, cur.sigla_uf, 'M', 1, NOW(), 'M', 'I', 9) RETURNING idmun INTO i_idmun ;
  End if;

  SELECT COALESCE((SELECT b.idbai
              FROM public.bairro b
              WHERE trim(unaccent(nome)) ilike trim(unaccent(cur.bairro))
              AND b.idmun = i_idmun
              LIMIT 1),0) INTO i_idbai;

  If i_idbai = 0 then
    SELECT COALESCE((SELECT d.iddis
                  FROM public.distrito d
                  WHERE d.idmun = i_idmun
                  LIMIT 1),0) INTO i_iddis;


    If i_iddis = 0 then
      INSERT INTO public.distrito (idmun, nome, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
        VALUES (i_idmun, cur.cidade, 'M', 1, now(), 'I', 1) returning iddis INTO i_iddis;
    End if;

    INSERT INTO public.bairro (idmun, iddis, nome, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad, zona_localizacao)
      VALUES (i_idmun, i_iddis, cur.bairro, 'M', 1, NOW(), 'I', 1, cur.zona_localizacao) RETURNING idbai INTO i_idbai;
  End if;

  SELECT COALESCE((SELECT idlog
    FROM public.logradouro
    WHERE idmun = i_idmun
    AND trim(unaccent(nome)) ILIKE trim(unaccent(cur.logradouro))
    LIMIT 1
    ),0) INTO i_idlog;

  If i_idlog = 0 THEN
    INSERT INTO public.logradouro (idtlog, nome, idmun, ident_oficial, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
    VALUES (cur.idtlog, cur.logradouro, i_idmun, 'S', 'M', 1, NOW(), 'I', 1) RETURNING idlog INTO i_idlog;

  End if;

  If NOT EXISTS (SELECT 1
              FROM urbano.cep_logradouro
              WHERE cep = cur.cep
              AND idlog = i_idlog) THEN
    INSERT INTO urbano.cep_logradouro (cep, idlog, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
      VALUES (cur.cep, i_idlog, 'M', 1, NOW(), 'I', 1);
  END if;

  If NOT EXISTS (SELECT 1
              FROM urbano.cep_logradouro_bairro
              WHERE cep = cur.cep
              AND idlog = i_idlog
              AND idbai = i_idbai
              ) THEN
    INSERT INTO urbano.cep_logradouro_bairro (cep, idlog, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
      VALUES (cur.cep, i_idlog, i_idbai, 'M', 1, NOW(), 'I', 1);
  END if;

  INSERT INTO cadastro.endereco_pessoa (idpes, tipo, cep, idlog, numero, letra, complemento, idbai, bloco, andar,  origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
    VALUES (cur.idpes, 1, cur.cep, i_idlog, cur.numero, cur.letra, cur.complemento, i_idbai, cur.bloco, cur.andar, 'M', 1, NOW(), 'I', 1);

end loop;

end;
$$;


CREATE FUNCTION historico.fcn_delete_grava_historico_bairro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_cep_logradouro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_cep_logradouro_bairro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_documento() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_endereco_externo() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_endereco_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_fisica() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_fisica_cpf() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_fone_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_funcionario() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_juridica() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_logradouro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_municipio() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_delete_grava_historico_socio() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_bairro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_cep_logradouro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_cep_logradouro_bairro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_documento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_idpes    numeric;
   v_sigla_uf_exp_rg  char(2);
   v_rg     text;
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_endereco_externo() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_endereco_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_fisica() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_fisica_cpf() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_fone_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_funcionario() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_juridica() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_logradouro() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_municipio() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_pessoa() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION historico.fcn_grava_historico_socio() RETURNS trigger
    LANGUAGE plpgsql
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

END; $$;


CREATE FUNCTION modules.audita_falta_componente_curricular() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_FALTA_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),NULL,NOW(),OLD.id ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_FALTA_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_FALTA_COMPONENTE_CURRICULAR', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_falta_geral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_FALTA_GERAL', TO_JSON(OLD.*),NULL,NOW(),OLD.id, nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_FALTA_GERAL', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_FALTA_GERAL', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_media_geral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MEDIA_GERAL', TO_JSON(OLD.*),NULL,NOW(),json_build_object('nota_aluno_id',OLD.nota_aluno_id,'etapa',OLD.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MEDIA_GERAL', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id',NEW.nota_aluno_id,'etapa',NEW.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MEDIA_GERAL', NULL,TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id',NEW.nota_aluno_id,'etapa',NEW.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_nota_componente_curricular() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),NULL,NOW(),OLD.id ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_nota_componente_curricular_media() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA', TO_JSON(OLD.*),NULL,NOW(),json_build_object('nota_aluno_id', OLD.nota_aluno_id, 'componente_curricular_id',OLD.componente_curricular_id, 'etapa',OLD.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id', NEW.nota_aluno_id, 'componente_curricular_id',OLD.componente_curricular_id, 'etapa',OLD.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA', NULL,TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id', NEW.nota_aluno_id, 'componente_curricular_id',NEW.componente_curricular_id, 'etapa',NEW.etapa),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_nota_exame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),NULL,NOW(),json_build_object('ref_cod_matricula', OLD.ref_cod_matricula, 'ref_cod_componente_curricular',OLD.ref_cod_componente_curricular) ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular) ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_EXAME', NULL,TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_nota_geral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_GERAL', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_GERAL', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_GERAL', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_parecer_componente_curricular() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_PARECER_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_PARECER_COMPONENTE_CURRICULAR', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_PARECER_COMPONENTE_CURRICULAR', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.audita_parecer_geral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_PARECER_GERAL', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_PARECER_GERAL', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_PARECER_GERAL', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION modules.copia_notas_transf(old_matricula_id integer, new_matricula_id integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
              DECLARE
              cur_comp RECORD;
              cur_comp_media RECORD;
              cur_geral RECORD;
              cur_geral_media RECORD;
              cur_falta_geral RECORD;
              cur_falta_comp RECORD;
              cur_parecer_geral RECORD;
              cur_parecer_comp RECORD;
              v_tipo_nota integer;
              v_tipo_parecer integer;
              v_tipo_falta integer;
              v_nota_id integer;
              v_old_nota_id integer;
              v_falta_id integer;
              v_old_falta_id integer;
              v_parecer_id integer;
              v_old_parecer_id integer;

              old_nota_aluno_id integer;
              new_nota_aluno_id integer;
              old_ano_matricula integer;
              new_ano_matricula integer;
              begin

              old_nota_aluno_id := (select id from modules.nota_aluno where matricula_id = old_matricula_id);
              new_nota_aluno_id := (select id from modules.nota_aluno where matricula_id = new_matricula_id);

              old_nota_aluno_id := (select (case when count(1) >= 1 then 1 else 0 end) from modules.nota_componente_curricular where nota_aluno_id = old_nota_aluno_id);
              new_nota_aluno_id := (select (case when count(1) >= 1 then 1 else 0 end) from modules.nota_componente_curricular where nota_aluno_id = new_nota_aluno_id);

              old_ano_matricula := (SELECT ano FROM pmieducar.matricula WHERE cod_matricula = old_matricula_id);
              new_ano_matricula := (SELECT ano FROM pmieducar.matricula WHERE cod_matricula = new_matricula_id);

              IF (old_nota_aluno_id = 1 and new_nota_aluno_id = 0) THEN
                /* VERIFICA SE AS MATRICULAS FAZEM PARTE DO MESMO ANO LETIVO*/
                IF (old_ano_matricula = new_ano_matricula) THEN

                  IF (
                   (  CASE WHEN (select padrao_ano_escolar from pmieducar.curso
                      where cod_curso = (select ref_cod_curso from pmieducar.matricula
                      where cod_matricula = new_matricula_id)) = 1
                     THEN  (select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                      inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                                  where mat.cod_matricula = new_matricula_id)
                           ELSE (select count(ref_cod_modulo) from pmieducar.turma_modulo
                      where ref_cod_turma = (select ref_cod_turma from pmieducar.matricula_turma
                      where ref_cod_matricula = new_matricula_id))
                           END
                 ) = (CASE WHEN (select padrao_ano_escolar from pmieducar.curso
                      where cod_curso = (select ref_cod_curso from pmieducar.matricula
                      where cod_matricula = old_matricula_id)) = 1
                     THEN  (select max(sequencial) as qtd_etapa from pmieducar.ano_letivo_modulo mod
                            inner join pmieducar.matricula mat on (mat.ref_ref_cod_escola = mod.ref_ref_cod_escola)
                                  where mat.cod_matricula = old_matricula_id)
                           ELSE  (select count(ref_cod_modulo) from pmieducar.turma_modulo
                      where ref_cod_turma = (select max(ref_cod_turma) from pmieducar.matricula_turma
                      where ref_cod_matricula = old_matricula_id))
                           END
                      )
                ) THEN

                    /* VERIFICA SE UTILIZAM A MESMA REGRA DE AVALIAÇÃO*/
                    IF ((SELECT id FROM modules.regra_avaliacao rg
                        INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                        INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                        where m.cod_matricula = old_matricula_id ) =
                          (SELECT id FROM modules.regra_avaliacao rg
                            INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                            INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                            where m.cod_matricula = new_matricula_id ) ) THEN


                      v_tipo_nota := (SELECT tipo_nota FROM modules.regra_avaliacao rg
                                INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                where m.cod_matricula = old_matricula_id);

                      v_tipo_falta := (SELECT tipo_presenca FROM modules.regra_avaliacao rg
                                INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                where m.cod_matricula = old_matricula_id);

                      v_tipo_parecer := (SELECT parecer_descritivo FROM modules.regra_avaliacao rg
                                INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                where m.cod_matricula = old_matricula_id);
                      /* SE A REGRA UTILIZAR NOTA, COPIA AS NOTAS*/
                      IF (v_tipo_nota >0) THEN

                        INSERT INTO modules.nota_aluno (matricula_id) VALUES (new_matricula_id);
                        v_nota_id := (SELECT max(id) FROM modules.nota_aluno WHERE matricula_id = new_matricula_id);

                        v_old_nota_id := (SELECT max(id) FROM modules.nota_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_comp IN (SELECT * FROM modules.nota_componente_curricular where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_componente_curricular (nota_aluno_id,componente_curricular_id,nota,nota_arredondada,etapa, nota_recuperacao, nota_original, nota_recuperacao_especifica)
                          VALUES(v_nota_id,cur_comp.componente_curricular_id,cur_comp.nota,cur_comp.nota_arredondada,cur_comp.etapa,cur_comp.nota_recuperacao,cur_comp.nota_original,cur_comp.nota_recuperacao_especifica);
                        END LOOP;

                        FOR cur_comp_media IN (SELECT * FROM modules.nota_componente_curricular_media where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_componente_curricular_media (nota_aluno_id,componente_curricular_id,media,media_arredondada,etapa, situacao)
                          VALUES(v_nota_id,cur_comp_media.componente_curricular_id,cur_comp_media.media,cur_comp_media.media_arredondada,cur_comp_media.etapa, cur_comp_media.situacao);
                        END LOOP;

                        FOR cur_geral IN (SELECT * FROM modules.nota_geral where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.nota_geral (nota_aluno_id,nota,nota_arredondada,etapa)
                          VALUES(v_nota_id,cur_geral.nota,cur_geral.nota_arredondada,cur_geral.etapa);
                        END LOOP;

                        FOR cur_geral_media IN (SELECT * FROM modules.media_geral where nota_aluno_id = v_old_nota_id) LOOP
                          INSERT INTO modules.media_geral (nota_aluno_id,media,media_arredondada,etapa)
                          VALUES(v_nota_id,cur_geral_media.media,cur_geral_media.media_arredondada,cur_geral_media.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_falta = 1) THEN

                          INSERT INTO modules.falta_aluno (matricula_id, tipo_falta) VALUES (new_matricula_id,1);
                          v_falta_id = (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = new_matricula_id);
                        v_old_falta_id := (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_falta_geral IN (SELECT * FROM modules.falta_geral where falta_aluno_id = v_old_falta_id) LOOP
                          INSERT INTO modules.falta_geral (falta_aluno_id,quantidade,etapa)
                          VALUES(v_falta_id,cur_falta_geral.quantidade, cur_falta_geral.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_falta = 2) THEN

                        INSERT INTO modules.falta_aluno (matricula_id, tipo_falta) VALUES (new_matricula_id,2);
                        v_falta_id = (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = new_matricula_id);
                        v_old_falta_id := (SELECT max(id) FROM modules.falta_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_falta_comp IN (SELECT * FROM modules.falta_componente_curricular where falta_aluno_id = v_old_falta_id) LOOP
                          INSERT INTO modules.falta_componente_curricular (falta_aluno_id,componente_curricular_id,quantidade,etapa)
                          VALUES(v_falta_id,cur_falta_comp.componente_curricular_id,cur_falta_comp.quantidade, cur_falta_comp.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_parecer = 2) THEN

                        INSERT INTO modules.parecer_aluno (matricula_id, parecer_descritivo)VALUES (new_matricula_id,2);
                        v_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = new_matricula_id);
                        v_old_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_parecer_comp IN (SELECT * FROM modules.parecer_componente_curricular where parecer_aluno_id = v_old_parecer_id) LOOP
                          INSERT INTO modules.parecer_componente_curricular (parecer_aluno_id,componente_curricular_id,parecer,etapa)
                          VALUES(v_parecer_id,cur_parecer_comp.componente_curricular_id,cur_parecer_comp.parecer, cur_parecer_comp.etapa);
                        END LOOP;
                      END IF;

                      IF (v_tipo_parecer = 3) THEN

                        INSERT INTO modules.parecer_aluno (matricula_id, parecer_descritivo)VALUES (new_matricula_id,3);
                        v_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = new_matricula_id);
                        v_old_parecer_id := (SELECT max(id) FROM modules.parecer_aluno WHERE matricula_id = old_matricula_id);

                        FOR cur_parecer_geral IN (SELECT * FROM modules.parecer_geral where parecer_aluno_id = v_old_parecer_id) LOOP
                          INSERT INTO modules.parecer_geral (parecer_aluno_id,parecer,etapa)
                          VALUES(v_parecer_id,cur_parecer_geral.parecer, cur_parecer_geral.etapa);
                        END LOOP;
                      END IF;

                      RETURN 'OK';

                    ELSE RETURN 'REGRA AVALIACAO DIFERENTE'; END IF;
                  ELSE RETURN 'ETAPA DIFERENTE'; END IF;
                ELSE RETURN 'MATRICULAS DE ANOS DIFERENTES';
                END IF;
              ELSE RETURN 'NAO EXISTE NOTAS';END IF;

              end;$$;


CREATE FUNCTION modules.corrige_sequencial_historico() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_aluno RECORD;
  cur_hist RECORD;
  contador integer;
  begin

  ALTER TABLE pmieducar.historico_escolar DISABLE TRIGGER USER;
  ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_escolar_pkey cascade;
  FOR cur_aluno IN (SELECT cod_aluno as id FROM pmieducar.aluno) LOOP
    update pmieducar.historico_escolar set sequencial = sequencial + 100 where ref_cod_aluno = cur_aluno.id;
    contador:=1;
    FOR cur_hist IN (SELECT sequencial FROM pmieducar.historico_escolar WHERE ref_cod_aluno = cur_aluno.id ORDER BY ano, nm_serie, data_cadastro) LOOP
      update pmieducar.historico_escolar set sequencial = contador where ref_cod_aluno = cur_aluno.id and sequencial = cur_hist.sequencial;
      contador:=contador+1;
    END LOOP;
  END LOOP;
  ALTER TABLE pmieducar.historico_escolar ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY(ref_cod_aluno, sequencial);
  ALTER TABLE pmieducar.historico_escolar ENABLE TRIGGER USER;
  end;$$;


CREATE FUNCTION modules.frequencia_da_matricula(p_matricula_id integer) RETURNS double precision
    LANGUAGE plpgsql
    AS $$
                      DECLARE
                        v_regra_falta integer;
                        v_falta_aluno_id  integer;
                        v_qtd_dias_letivos_serie NUMERIC;
                        v_total_faltas integer;
                        v_qtd_horas_serie integer;
                        v_hora_falta FLOAT;
                      BEGIN
                          /*
                            regra_falta:
                            1- Global
                            2- Por componente
                          */
                          v_regra_falta:= (SELECT rg.tipo_presenca
                                             FROM modules.regra_avaliacao rg
                                            INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                                            INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                                            WHERE m.cod_matricula = p_matricula_id);
                          v_falta_aluno_id := (SELECT id
                                                 FROM modules.falta_aluno
                                                WHERE matricula_id = p_matricula_id
                                                ORDER BY id DESC
                                                LIMIT 1 );
                          IF (v_regra_falta = 1) THEN
                                v_qtd_dias_letivos_serie := (SELECT s.dias_letivos
                                                               FROM pmieducar.serie s
                                                              INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                                                              WHERE m.cod_matricula = p_matricula_id);
                                v_total_faltas := (SELECT SUM(quantidade)
                                                     FROM modules.falta_geral
                                                    WHERE falta_aluno_id = v_falta_aluno_id);
                                RETURN TRUNC((((v_qtd_dias_letivos_serie - v_total_faltas) * 100 ) / v_qtd_dias_letivos_serie ),2);
                          ELSE

                            v_qtd_horas_serie := ( SELECT s.carga_horaria
                                                     FROM pmieducar.serie s
                                                    INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                                                    WHERE m.cod_matricula = p_matricula_id);

                            v_total_faltas := (SELECT SUM(quantidade)
                                                 FROM falta_componente_curricular
                                                WHERE falta_aluno_id = v_falta_aluno_id);
                            v_hora_falta := (SELECT hora_falta
                                               FROM pmieducar.curso c
                                              INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
                                              WHERE m.cod_matricula = p_matricula_id);
                            RETURN  (100 - ((v_total_faltas * (v_hora_falta*100))/v_qtd_horas_serie));
                          END IF;
                      END;$$;


CREATE FUNCTION modules.frequencia_etapa_padrao_ano_escolar_um(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
                      		DECLARE
                      			dias_letivos_escola decimal;
                      			tipo_falta_aluno integer;
                      			faltas_aluno_geral decimal;
                      			faltas_aluno_componente decimal;
                      			cod_serie_ano_escolar integer;
                      		begin
                      			cod_serie_ano_escolar := (select ref_ref_cod_serie from pmieducar.matricula where matricula.cod_matricula = cod_matricula_aluno);
                      			tipo_falta_aluno := (select tipo_falta from modules.falta_aluno where matricula_id = cod_matricula_aluno);

                      			faltas_aluno_geral := (select quantidade
                      						 from modules.falta_aluno
                      					   inner join modules.falta_geral on (falta_aluno.id = falta_geral.falta_aluno_id)
                      					        where falta_aluno.matricula_id = cod_matricula_aluno
                      						  and etapa = cod_etapa::varchar);

                      			dias_letivos_escola := (select dias_letivos
                      						  from pmieducar.matricula
                      					    inner join pmieducar.ano_letivo_modulo on (matricula.ref_ref_cod_escola = ano_letivo_modulo.ref_ref_cod_escola
                      										       and matricula.ano = ano_letivo_modulo.ref_ano)
                      						 where cod_matricula = cod_matricula_aluno
                      						   and sequencial = cod_etapa);

                      			faltas_aluno_componente := (select quantidade
                      						      from modules.falta_aluno
                      					        inner join modules.falta_componente_curricular on (falta_aluno.id = falta_componente_curricular.falta_aluno_id)
                      						     where falta_aluno.matricula_id = cod_matricula_aluno
                      						       and etapa = cod_etapa::varchar
                      						       and componente_curricular_id = id_componente_curricular);

                      			if(dias_letivos_escola is not null and dias_letivos_escola <> 0) then

                      				if(tipo_falta_aluno = 1) then
                      					return round((((dias_letivos_escola - faltas_aluno_geral) * 100) / dias_letivos_escola), 2);
                      				else
                      					return round((((dias_letivos_escola - faltas_aluno_componente) * 100) / dias_letivos_escola), 2);
                      				end if;
                      			else
                      				return null;
                      			end if;
                      		end;
                      	$$;


CREATE FUNCTION modules.frequencia_etapa_padrao_ano_escolar_zero(cod_matricula_aluno integer, cod_etapa integer, id_componente_curricular integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
		DECLARE
			dias_letivos_turma decimal;
			tipo_falta_aluno integer;
			faltas_aluno_geral decimal;
			faltas_aluno_componente decimal;
			cod_serie_ano_escolar integer;
		begin
			cod_serie_ano_escolar := (select ref_ref_cod_serie from pmieducar.matricula where matricula.cod_matricula = cod_matricula_aluno);
			tipo_falta_aluno := (select tipo_falta from modules.falta_aluno where matricula_id = cod_matricula_aluno);

			dias_letivos_turma := (select turma_modulo.dias_letivos
						 from pmieducar.matricula
					   inner join pmieducar.matricula_turma on (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
					   inner join pmieducar.turma on (matricula_turma.ref_cod_turma = turma.cod_turma)
					    left join pmieducar.turma_modulo on (turma.cod_turma = turma_modulo.ref_cod_turma)
						where matricula.cod_matricula = cod_matricula_aluno
						  and turma_modulo.sequencial = cod_etapa
						  and matricula_turma.ativo = 1);

			faltas_aluno_geral := (select quantidade
						 from modules.falta_aluno
					   inner join modules.falta_geral on (falta_aluno.id = falta_geral.falta_aluno_id)
						where falta_aluno.matricula_id = cod_matricula_aluno
						  and etapa = cod_etapa);

			faltas_aluno_componente := (select quantidade
						      from modules.falta_aluno
						inner join modules.falta_componente_curricular on (falta_aluno.id = falta_componente_curricular.falta_aluno_id)
						     where falta_aluno.matricula_id = cod_matricula_aluno
						       and etapa = cod_etapa
						       and componente_curricular_id = id_componente_curricular);

			if(dias_letivos_turma is not null and dias_letivos_turma <> 0) then

				if(tipo_falta_aluno = 1) then
					return round((((dias_letivos_turma - faltas_aluno_geral) * 100) / dias_letivos_turma), 2);
				else
					return round((((dias_letivos_turma - faltas_aluno_componente) * 100) / dias_letivos_turma), 2);
				end if;
			else
				return null;
			end if;
		end;
	$$;


CREATE FUNCTION modules.frequencia_matricula_por_etapa(matricula integer, etapa character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
                          DECLARE
                            matricula integer;
                            etapa varchar;
                            ano_matricula integer;
                            escola_matricula integer;
                            turma integer;
                            faltas_geral_matricula decimal;
                            var_dias_letivos_turma_etapa decimal;
                            dias_letivos_escola_etapa decimal;

                            BEGIN
                              matricula := $1;
                              etapa := $2;
                              turma := (SELECT ref_cod_turma
                                          FROM pmieducar.matricula_turma
                                         WHERE ref_cod_matricula = $1
                                            AND matricula_turma.sequencial = relatorio.get_max_sequencial_matricula(ref_cod_matricula));
                              ano_matricula := (SELECT ano
                                                  FROM pmieducar.matricula
                                                 WHERE cod_matricula = $1);
                              escola_matricula := (SELECT ref_ref_cod_escola
                                                     FROM pmieducar.matricula
                                                    WHERE cod_matricula = $1);
                              faltas_geral_matricula := (SELECT sum(falta_geral.quantidade)
                                                               FROM modules.falta_geral,
                                                                    modules.falta_aluno
                                                              WHERE falta_geral.falta_aluno_id = falta_aluno.id
                                                                AND falta_aluno.matricula_id = $1
                                                                AND falta_geral.etapa = $2
                                                                AND falta_aluno.tipo_falta = 1);
                              var_dias_letivos_turma_etapa := (SELECT dias_letivos
                                                                 FROM pmieducar.turma_modulo
                                                                WHERE sequencial::varchar = $2
                                                                  AND ref_cod_turma = turma);
                              dias_letivos_escola_etapa := (SELECT dias_letivos
                                                                  FROM pmieducar.ano_letivo_modulo
                                                                 WHERE sequencial::varchar = $2
                                                                   AND ref_ano = ano_matricula
                                                                   AND ref_ref_cod_escola = escola_matricula);
                              IF (var_dias_letivos_turma_etapa IS NOT NULL AND var_dias_letivos_turma_etapa <> 0) THEN
                                RETURN ((var_dias_letivos_turma_etapa - faltas_geral_matricula) * 100) / var_dias_letivos_turma_etapa;
                              ELSE
                                IF (dias_letivos_escola_etapa IS NOT NULL AND dias_letivos_escola_etapa <> 0) THEN
                                  RETURN ((var_dias_letivos_turma_etapa - faltas_geral_matricula) * 100) / var_dias_letivos_turma_etapa;
                                END IF;
                              END IF;
                            END;
                          $_$;


CREATE FUNCTION modules.frequencia_por_componente(cod_matricula_id integer, cod_disciplina_id integer, cod_turma_id integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
            DECLARE
                                         cod_falta_aluno_id integer;
                                         v_qtd_dias_letivos_serie integer;
                                         v_total_faltas integer;
                                         qtde_carga_horaria integer;
                                         v_hora_falta float;
                                         cod_serie_id integer;
                                      BEGIN
            
                                        cod_falta_aluno_id := (SELECT id FROM modules.falta_aluno WHERE matricula_id = cod_matricula_id ORDER BY id DESC LIMIT 1);
            
                                        qtde_carga_horaria := (SELECT carga_horaria :: int
                                              FROM modules.componente_curricular_turma
                                             WHERE componente_curricular_turma.componente_curricular_id = cod_disciplina_id
                                               AND componente_curricular_turma.turma_id = cod_turma_id);
            
                                        IF (qtde_carga_horaria IS NULL) THEN
                                            cod_serie_id := (SELECT ref_ref_cod_serie FROM pmieducar.turma WHERE cod_turma = cod_turma_id);
                                            qtde_carga_horaria := (SELECT carga_horaria :: int
                                                                 FROM modules.componente_curricular_ano_escolar
                                                            WHERE componente_curricular_ano_escolar.componente_curricular_id = cod_disciplina_id
                                                              AND componente_curricular_ano_escolar.ano_escolar_id = cod_serie_id);
                                        END IF;
            
                                        v_total_faltas := (SELECT SUM(quantidade)
                                                             FROM falta_componente_curricular
                                                            WHERE falta_aluno_id = cod_falta_aluno_id
                                                              AND componente_curricular_id = cod_disciplina_id);
            
                                        v_hora_falta := (SELECT hora_falta FROM pmieducar.curso c
                                                     INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
                                                          WHERE m.cod_matricula = cod_matricula_id);
            
                                        IF (qtde_carga_horaria = 0) THEN
                                            RETURN 0;
                                        END IF;
            
                                        RETURN  trunc((100 - ((v_total_faltas * (v_hora_falta*100))/qtde_carga_horaria))::numeric, 2);
            
                                    END;
            $$;


CREATE FUNCTION modules.impede_duplicacao_falta_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.falta_aluno
                        WHERE falta_aluno.matricula_id = NEW.matricula_id
                          AND falta_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela falta_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                    $$;


CREATE FUNCTION modules.impede_duplicacao_nota_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.nota_aluno
                                WHERE nota_aluno.matricula_id = NEW.matricula_id
                                  AND nota_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela nota_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                    $$;


CREATE FUNCTION modules.impede_duplicacao_parecer_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.parecer_aluno
                                 WHERE parecer_aluno.matricula_id = NEW.matricula_id
                                   AND parecer_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela parecer_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                $$;


CREATE FUNCTION modules.preve_data_emprestimo(biblioteca_id integer, data_prevista date) RETURNS date
    LANGUAGE plpgsql
    AS $$
  DECLARE
  begin

  IF (( select 1 from pmieducar.biblioteca_dia WHERE ref_cod_biblioteca = biblioteca_id AND dia = ((SELECT EXTRACT(DOW FROM data_prevista))+1) limit 1) IS NOT null) THEN
    IF ((SELECT 1 FROM pmieducar.biblioteca_feriados WHERE ref_cod_biblioteca = biblioteca_id and data_feriado = data_prevista) IS NULL) THEN
      RETURN data_prevista;
    ELSE
      RETURN modules.preve_data_emprestimo(biblioteca_id, data_prevista+1);
    END IF;
  ELSE
    RETURN modules.preve_data_emprestimo(biblioteca_id, data_prevista+1);
  END IF;

  end;$$;


CREATE FUNCTION pmieducar.audita_matricula() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MATRICULA', TO_JSON(OLD.*),NULL,NOW(),OLD.cod_matricula ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MATRICULA', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.cod_matricula,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MATRICULA', NULL,TO_JSON(NEW.*),NOW(),NEW.cod_matricula,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;    
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION pmieducar.audita_matricula_turma() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),NULL,NOW(),json_build_object('ref_cod_matricula',OLD.ref_cod_matricula,'sequencial',OLD.sequencial),nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;    
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula',NEW.ref_cod_matricula,'sequencial',NEW.sequencial),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MATRICULA_TURMA', NULL,TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula',NEW.ref_cod_matricula,'sequencial',NEW.sequencial),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;


CREATE FUNCTION pmieducar.copiaanosletivos(ianonovo smallint, icodescola integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
            iAnoAnterior smallint;
            BEGIN

            SELECT COALESCE(MAX(ano),0) INTO iAnoAnterior
            FROM pmieducar.escola_ano_letivo
            WHERE ref_cod_escola = iCodEscola
            AND ano < iAnoNovo;

            If iAnoAnterior IS NOT NULL THEN

                UPDATE pmieducar.escola_curso
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_cod_escola = iCodEscola;

                UPDATE pmieducar.escola_serie
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_cod_escola = iCodEscola;

                UPDATE pmieducar.escola_serie_disciplina
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_ref_cod_escola = iCodEscola;

                UPDATE modules.componente_curricular_ano_escolar
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE EXISTS(
                    SELECT 1
                    FROM pmieducar.escola_serie_disciplina
                    WHERE iAnoAnterior = ANY(anos_letivos)
                    AND ref_ref_cod_escola = iCodEscola
                    AND escola_serie_disciplina.ref_cod_disciplina = componente_curricular_ano_escolar.componente_curricular_id
                    AND escola_serie_disciplina.ref_ref_cod_serie = componente_curricular_ano_escolar.ano_escolar_id
                )
                AND NOT (iAnoNovo = ANY(anos_letivos));

                INSERT INTO modules.regra_avaliacao_serie_ano
                (serie_id, regra_avaliacao_id, regra_avaliacao_diferenciada_id, ano_letivo)
                SELECT distinct serie, rasa.regra_avaliacao_id, rasa.regra_avaliacao_diferenciada_id, iAnoNovo
                FROM (
                SELECT distinct ref_cod_serie serie
                FROM pmieducar.escola_serie
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND ref_cod_escola = iCodEscola
                ) AS myqq
                JOIN modules.regra_avaliacao_serie_ano rasa
                ON rasa.serie_id = serie
                AND iAnoAnterior = rasa.ano_letivo
                AND NOT EXISTS(
                    SELECT 1
                    FROM modules.regra_avaliacao_serie_ano
                    WHERE serie_id = rasa.serie_id
                    AND ano_letivo = iAnoNovo
                );

            END IF;
            END;
            $$;


CREATE FUNCTION pmieducar.fcn_aft_update() RETURNS trigger
    LANGUAGE plpgsql
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
END; $$;


CREATE FUNCTION pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_aluno RECORD;
  begin

  FOR cur_aluno IN (SELECT cod_aluno as id, ref_cod_aluno_beneficio as beneficio_id FROM pmieducar.aluno WHERE ref_cod_aluno_beneficio IS NOT NULL) LOOP
    INSERT INTO pmieducar.aluno_aluno_beneficio VALUES (cur_aluno.id, cur_aluno.beneficio_id);
  END LOOP;

  ALTER TABLE pmieducar.aluno DROP COLUMN ref_cod_aluno_beneficio;
  end;$$;


CREATE FUNCTION pmieducar.normalizadeficienciaservidor() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_servidor RECORD;
  begin

    FOR cur_servidor IN (SELECT cod_servidor, ref_cod_deficiencia
                      FROM pmieducar.servidor
                      WHERE ref_cod_deficiencia is not null) LOOP


      IF ((SELECT 1 FROM cadastro.fisica_deficiencia fd WHERE fd.ref_idpes = cur_servidor.cod_servidor AND fd.ref_cod_deficiencia = cur_servidor.ref_cod_deficiencia) IS NULL ) THEN
        INSERT INTO cadastro.fisica_deficiencia VALUES (cur_servidor.cod_servidor, cur_servidor.ref_cod_deficiencia);
      END IF;

    END LOOP;

  end;$$;


CREATE FUNCTION pmieducar.unifica_alunos(alunoprincipal numeric, alunos numeric[], usuario integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
        DECLARE
            alunoPrincipal ALIAS FOR $1;
            alunos ALIAS FOR $2;
            usuario ALIAS FOR $3;
        BEGIN

            UPDATE pmieducar.historico_escolar
                SET ref_cod_aluno = alunoPrincipal, sequencial = he.seq+he.max_seq
            FROM
                (
                    SELECT ref_cod_aluno AS aluno, sequencial AS seq, COALESCE((
                        SELECT max(sequencial) FROM pmieducar.historico_escolar WHERE ref_cod_aluno = alunoPrincipal
                    ),0) AS max_seq
                    FROM pmieducar.historico_escolar
                    WHERE ref_cod_aluno = ANY(alunos)
                ) AS he
              WHERE sequencial = he.seq
              AND ref_cod_aluno = he.aluno;

            UPDATE pmieducar.matricula
                SET ref_cod_aluno = alunoPrincipal
            WHERE ref_cod_aluno = ANY(alunos);

            UPDATE pmieducar.aluno
                SET ativo = 0, data_exclusao = now(), ref_usuario_exc = usuario
            WHERE cod_aluno = ANY(alunos);

        END;$_$;


CREATE FUNCTION pmieducar.unifica_pessoas(pessoaprincipal numeric, pessoas numeric[], usuario integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
        DECLARE
            pessoaPrincipal ALIAS FOR $1;
            pessoas ALIAS FOR $2;
            usuario ALIAS FOR $3;
        BEGIN

            SET session_replication_role = REPLICA;

            IF
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas)) > 0 AND
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal) = 0
                THEN
                    UPDATE pmieducar.aluno
                        SET ref_idpes = pessoaPrincipal
                    WHERE ref_idpes = ANY(pessoas)
                    AND ref_idpes <> pessoaPrincipal
                    AND cod_aluno = (SELECT MAX(cod_aluno) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas));

                    PERFORM pmieducar.unifica_alunos(
                        (SELECT cod_aluno FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal),
                        (SELECT ARRAY(SELECT cod_aluno::numeric FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas))),
                        1
                    );
            ELSEIF
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal) > 0 AND
                (SELECT COUNT(1) FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas)) > 0
                THEN
                    PERFORM pmieducar.unifica_alunos(
                        (SELECT cod_aluno FROM pmieducar.aluno WHERE ref_idpes = pessoaPrincipal),
                        (SELECT ARRAY(SELECT cod_aluno::numeric FROM pmieducar.aluno WHERE ref_idpes = ANY(pessoas))),
                        1
                    );
	        END IF;

	        UPDATE cadastro.fisica
                SET idpes_pai = pessoaPrincipal
            WHERE idpes_pai = ANY(pessoas);

            UPDATE cadastro.fisica
                SET idpes_mae = pessoaPrincipal
            WHERE idpes_mae = ANY(pessoas);

	        UPDATE pmieducar.servidor_alocacao
                SET ref_cod_servidor = pessoaPrincipal
            WHERE ref_cod_servidor = ANY(pessoas);

	        UPDATE pmieducar.servidor_funcao
                SET ref_cod_servidor = pessoaPrincipal
            WHERE ref_cod_servidor = ANY(pessoas);

	        IF
	            (SELECT COUNT(1) FROM pmieducar.servidor WHERE cod_servidor = ANY(pessoas)) > 0 AND
	            (SELECT COUNT(1) FROM pmieducar.servidor WHERE cod_servidor = pessoaPrincipal) = 0
	            THEN
	                INSERT INTO pmieducar.servidor SELECT
	                pessoaPrincipal as cod_servidor,
        	            ref_cod_instituicao, ref_idesco, carga_horaria,
        	            data_cadastro, data_exclusao, ativo, ref_cod_subnivel,
        	            situacao_curso_superior_1, formacao_complementacao_pedagogica_1,
        	            codigo_curso_superior_1, ano_inicio_curso_superior_1,
        	            ano_conclusao_curso_superior_1, tipo_instituicao_curso_superior_1,
        	            instituicao_curso_superior_1, situacao_curso_superior_2,
        	            formacao_complementacao_pedagogica_2, codigo_curso_superior_2,
        	            ano_inicio_curso_superior_2, ano_conclusao_curso_superior_2,
        	            tipo_instituicao_curso_superior_2, instituicao_curso_superior_2,
        	            situacao_curso_superior_3, formacao_complementacao_pedagogica_3,
        	            codigo_curso_superior_3, ano_inicio_curso_superior_3,
        	            ano_conclusao_curso_superior_3, tipo_instituicao_curso_superior_3,
        	            instituicao_curso_superior_3, pos_especializacao,
        	            pos_mestrado, pos_doutorado, pos_nenhuma,
        	            curso_creche, curso_pre_escola, curso_anos_iniciais,
        	            curso_anos_finais, curso_ensino_medio, curso_eja, curso_educacao_especial,
        	            curso_educacao_indigena, curso_educacao_campo, curso_educacao_ambiental,
        	            curso_educacao_direitos_humanos, curso_genero_diversidade_sexual,
        	            curso_direito_crianca_adolescente, curso_relacoes_etnicorraciais,
        	            curso_outros, curso_nenhum, multi_seriado
    	        FROM pmieducar.servidor
    	        WHERE cod_servidor = ANY(pessoas)
    	        ORDER BY cod_servidor ASC
                LIMIT 1;
	        END IF;

	        DELETE FROM pmieducar.servidor WHERE cod_servidor = ANY(pessoas) AND cod_servidor <> pessoaPrincipal;
            DELETE FROM cadastro.documento WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.fisica WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.fone_pessoa WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;
            DELETE FROM cadastro.pessoa WHERE idpes = ANY(pessoas) AND idpes <> pessoaPrincipal;

            SET session_replication_role = DEFAULT;

        END;$_$;


CREATE FUNCTION pmieducar.unifica_tipos_transferencia() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_tipo RECORD;
  v_cod_tt INTEGER;
  begin

    ALTER TABLE pmieducar.transferencia_tipo ADD COLUMN ref_cod_instituicao INTEGER;

    ALTER TABLE pmieducar.transferencia_tipo ADD CONSTRAINT transferencia_tipo_ref_cod_instituicao
    FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
    ON UPDATE RESTRICT ON DELETE RESTRICT;

    FOR cur_tipo IN (SELECT tt.cod_transferencia_tipo AS id, tt.nm_tipo, escola.ref_cod_instituicao
                      FROM pmieducar.transferencia_tipo tt
                      INNER JOIN pmieducar.escola ON (tt.ref_cod_escola = escola.cod_escola)) LOOP

      v_cod_tt := (SELECT cod_transferencia_tipo FROM transferencia_tipo WHERE to_ascii(nm_tipo) ilike to_ascii(cur_tipo.nm_tipo)
                      AND cod_transferencia_tipo <= cur_tipo.id ORDER BY cod_transferencia_tipo LIMIT 1 );

      IF (v_cod_tt = cur_tipo.id) THEN
        UPDATE pmieducar.transferencia_tipo SET ref_cod_instituicao = cur_tipo.ref_cod_instituicao WHERE cod_transferencia_tipo = cur_tipo.id;
      ELSE
        UPDATE pmieducar.transferencia_solicitacao SET ref_cod_transferencia_tipo = v_cod_tt
                                                   WHERE ref_cod_transferencia_tipo = cur_tipo.id;
        DELETE FROM pmieducar.transferencia_tipo WHERE cod_transferencia_tipo = cur_tipo.id;

      END IF;

    END LOOP;

    ALTER TABLE pmieducar.transferencia_tipo  DROP CONSTRAINT transferencia_tipo_ref_cod_escola;

    ALTER TABLE pmieducar.transferencia_tipo DROP COLUMN ref_cod_escola;

  end;$$;


CREATE FUNCTION pmieducar.updated_at_matricula() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ BEGIN NEW.updated_at = now(); RETURN NEW; END; $$;


CREATE FUNCTION pmieducar.updated_at_matricula_turma() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ BEGIN NEW.updated_at = now(); RETURN NEW; END; $$;


CREATE FUNCTION public.commacat_ignore_nulls(acc text, instr text) RETURNS text
    LANGUAGE plpgsql
    AS $$
  BEGIN
      IF acc IS NULL OR acc = '' THEN
        RETURN instr;
      ELSIF instr IS NULL OR instr = '' THEN
        RETURN acc || ' <br> ';
      ELSE
        RETURN acc || ' <br> ' || instr;
      END IF;
    END;
  $$;


CREATE FUNCTION public.count_weekdays(date, date) RETURNS integer
    LANGUAGE plpgsql STABLE
    AS $_$
     DECLARE
      start_date alias for $1;
      end_date alias for $2;
      tmp_date date;
      tmp_dow integer;
      -- double precision returned from extract
      tot_dow integer;
     BEGIN
       tmp_date := start_date;
       tot_dow := 0;
       WHILE (tmp_date <= end_date) LOOP
         select into tmp_dow  cast(extract(dow from tmp_date) as integer);
         IF ((tmp_dow >= 2) and (tmp_dow <= 6)) THEN
           tot_dow := (tot_dow + 1);
         END IF;
         select into tmp_date (tmp_date + interval '1 day ');
       END LOOP;
       return tot_dow;

     END;
  $_$;


CREATE FUNCTION public.cria_distritos() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_log RECORD;
  sequence_val INTEGER;
  begin

    FOR cur_log IN (SELECT idmun, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad
                      FROM public.municipio ORDER BY idmun ASC) LOOP

      INSERT INTO public.distrito (idmun, iddis, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad)
                  VALUES(cur_log.idmun, cur_log.idmun, cur_log.nome, cur_log.idpes_cad, cur_log.data_cad,
                         cur_log.origem_gravacao, cur_log.operacao, cur_log.idsis_cad);
    END LOOP;
    sequence_val := (SELECT max(iddis)+1 FROM public.distrito)::INT;
    PERFORM setval('public.seq_distrito', sequence_val);

  end;$$;


CREATE FUNCTION public.data_para_extenso(data date) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
	data_extenso varchar := '';
	mes_extenso varchar := '';
	dia integer := 0;
	mes integer := 0;
	ano integer := 0;
BEGIN

	dia := date_part('day', data)::integer;
	mes := date_part('month', data)::integer;
	ano := date_part('year', data)::integer;

	mes_extenso := case
				    when mes = 1  then 'Janeiro'
				    when mes = 2  then 'Fevereiro'
				    when mes = 3  then 'Março'
				    when mes = 4  then 'Abril'
				    when mes = 5  then 'Maio'
				    when mes = 6  then 'Junho'
				    when mes = 7  then 'Julho'
				    when mes = 8  then 'Agosto'
				    when mes = 9  then 'Setembro'
				    when mes = 10 then 'Outubro'
				    when mes = 11 then 'Novembro'
				    when mes = 12 then 'Dezembro'
				   else
				   	''
				   end;

	data_extenso := dia::varchar || ' de ' || mes_extenso || ' de ' || ano::varchar;

	return data_extenso;

END;
$$;


CREATE FUNCTION public.f_unaccent(text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$
            SELECT public.unaccent('public.unaccent', $1)
            $_$;


CREATE FUNCTION public.fcn_aft_logradouro_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_aft_pessoa_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_bef_ins_fisica() RETURNS trigger
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_bef_ins_juridica() RETURNS trigger
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_bef_logradouro_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idlog    bigint;
   BEGIN
    v_idlog := OLD.idlog;
    EXECUTE 'DELETE FROM public.logradouro_fonetico WHERE idlog = '||quote_literal(v_idlog)||';';
    RETURN OLD;
   END;
  $$;


CREATE FUNCTION public.fcn_bef_pessoa_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idpes    bigint;
   BEGIN
    v_idpes := OLD.idpes;
    EXECUTE 'DELETE FROM cadastro.pessoa_fonetico WHERE idpes = '||quote_literal(v_idpes)||';';
    RETURN OLD;
   END;
  $$;


CREATE FUNCTION public.fcn_compara_nome_pessoa_fonetica(text, numeric) RETURNS integer
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_cons_log_fonetica(text, bigint) RETURNS SETOF public.typ_idlog
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_consulta_fonetica(text) RETURNS SETOF public.typ_idpes
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_delete_endereco_externo(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_idpes ALIAS for $1;
  v_tipo ALIAS for $2;

BEGIN
  -- Deleta dados da tabela endereco_externo
  DELETE FROM cadastro.endereco_externo WHERE idpes = v_idpes AND tipo = v_tipo;
  RETURN 0;
END;$_$;


CREATE FUNCTION public.fcn_delete_endereco_pessoa(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_idpes ALIAS for $1;
  v_tipo ALIAS for $2;

BEGIN
  -- Deleta dados da tabela endereco_pessoa
  DELETE FROM cadastro.endereco_pessoa WHERE idpes = v_idpes AND tipo = v_tipo;
  RETURN 0;
END;$_$;


CREATE FUNCTION public.fcn_delete_fone_pessoa(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;

BEGIN
  -- Deleta dados da tabela fone_pessoa
  DELETE FROM cadastro.fone_pessoa WHERE idpes = v_id_pes;
  RETURN 0;
END;$_$;


CREATE FUNCTION public.fcn_delete_funcionario(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;

BEGIN
  -- Deleta dados da tabela funcionário
  DELETE FROM cadastro.funcionario WHERE matricula = v_matricula AND idins = v_id_ins;
  RETURN 0;
END;$_$;


CREATE FUNCTION public.fcn_dia_util(date, date) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_fonetiza(text) RETURNS SETOF text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_fonetiza_logr_geral() RETURNS text
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_fonetiza_palavra(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_fonetiza_pessoa_geral() RETURNS text
    LANGUAGE plpgsql
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
  $$;


CREATE FUNCTION public.fcn_fonetiza_primeiro_ultimo_nome(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_insert_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_funcionario(integer, integer, integer, integer, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_insert_pessoa(integer, character varying, character varying, character varying, character varying, integer, character varying, character varying, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_obter_primeiro_ultimo_nome(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_obter_primeiro_ultimo_nome_juridica(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_update_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_funcionario(numeric, integer, integer, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_update_pessoa(integer, text, character varying, character varying, character varying, integer, character varying, integer, integer) RETURNS integer
    LANGUAGE plpgsql
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
END;$_$;


CREATE FUNCTION public.fcn_upper(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.fcn_upper_nrm(text) RETURNS text
    LANGUAGE plpgsql
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
  $_$;


CREATE FUNCTION public.formata_cpf(cpf numeric) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
	cpf_formatado varchar := '';
BEGIN
  cpf_formatado := (SUBSTR(TO_CHAR(cpf, '00000000000'), 1, 4) || '.' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 5, 3) || '.' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 8, 3) || '-' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 11, 2)) ;
  RETURN cpf_formatado;
END;
$$;


CREATE FUNCTION public.isnumeric(text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE
    AS $_$
  DECLARE x NUMERIC;
    BEGIN
        x = $1::NUMERIC;
        RETURN TRUE;
    EXCEPTION WHEN others THEN
        RETURN FALSE;
    END;
  $_$;


CREATE FUNCTION public.retira_data_cancel_matricula_fun() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN

  UPDATE pmieducar.matricula
  SET    data_cancel = NULL
  WHERE  cod_matricula = new.cod_matricula
  AND    data_cancel IS DISTINCT FROM NULL
  AND    aprovado = 3
  AND (SELECT 1 FROM pmieducar.transferencia_solicitacao WHERE ativo = 1 AND ref_cod_matricula_saida = new.cod_matricula limit 1) is null;

  RETURN NULL;
  END
  $$;


CREATE FUNCTION public.unifica_bairro(p_idbai_duplicado integer, p_idbai_principal integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur_cep_log_bai RECORD;
begin

FOR cur_cep_log_bai IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idbai = p_idbai_duplicado) LOOP

IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb
      WHERE clb.idlog = cur_cep_log_bai.idlog
      AND clb.cep = cur_cep_log_bai.cep
      AND clb.idbai = p_idbai_principal
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                             VALUES (cur_cep_log_bai.idlog, cur_cep_log_bai.cep, p_idbai_principal, 'U', 1, NOW(), 'I', 9);


END IF;
END LOOP;

UPDATE cadastro.endereco_pessoa SET idbai = p_idbai_principal WHERE idbai = p_idbai_duplicado;
DELETE FROM urbano.cep_logradouro_bairro WHERE idbai = p_idbai_duplicado;
DELETE FROM public.bairro WHERE idbai = p_idbai_duplicado;

end;$$;


CREATE FUNCTION public.unifica_logradouro(p_idlog_duplicado integer, p_idlog_principal integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur_cep_log RECORD;
begin

FOR cur_cep_log IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idlog = p_idlog_duplicado) LOOP

IF (SELECT 1 FROM urbano.cep_logradouro cl
      WHERE cl.idlog = p_idlog_principal
      AND cl.cep = cur_cep_log.cep
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro (idlog, cep, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                             VALUES (p_idlog_principal, cur_cep_log.cep, 'U', 1, NOW(), 'I', 9);


END IF;

IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb
      WHERE clb.idlog = p_idlog_principal
      AND clb.cep = cur_cep_log.cep
      AND clb.idbai = cur_cep_log.idbai
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                             VALUES (p_idlog_principal, cur_cep_log.cep, cur_cep_log.idbai, 'U', 1, NOW(), 'I', 9);


END IF;
END LOOP;

UPDATE cadastro.endereco_pessoa SET idlog = p_idlog_principal WHERE idlog = p_idlog_duplicado;
DELETE FROM urbano.cep_logradouro_bairro WHERE idlog = p_idlog_duplicado;
DELETE FROM urbano.cep_logradouro WHERE idlog = p_idlog_duplicado;
DELETE FROM public.logradouro WHERE idlog = p_idlog_duplicado;

end;$$;


CREATE FUNCTION public.update_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                NEW.updated_at = now();
                RETURN NEW;
            END;
            $$;


CREATE FUNCTION public.verifica_existe_matricula_posterior_mesma_turma(cod_matricula integer, cod_turma integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
                      DECLARE existe_matricula boolean;

                      BEGIN
                        existe_matricula := EXISTS (SELECT *
                                                      FROM pmieducar.matricula_turma mt
                                                     INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
                                                     INNER JOIN pmieducar.matricula m2 ON (m2.cod_matricula = m.cod_matricula)
                                                     INNER JOIN pmieducar.matricula_turma mt2 ON (mt2.ref_cod_matricula = m.cod_matricula
                                                                                                  AND mt2.ref_cod_turma = cod_turma)
                                                     WHERE mt.ref_cod_turma = mt2.ref_cod_turma
                                                       AND mt.ref_cod_matricula <> mt2.ref_cod_matricula
                                                       AND m.ref_cod_aluno = m2.ref_cod_aluno
                                                       AND mt.data_enturmacao > mt2.data_enturmacao
                                                       AND m.ativo = 1
                                                       AND m2.ativo = 1);

                        RETURN existe_matricula;
                      END;
                      $$;


CREATE FUNCTION public.verifica_existe_matricula_posterior_mesma_turma(cod_matricula integer, cod_turma integer, sequencial integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$

      DECLARE existe_matricula boolean;

      BEGIN
        existe_matricula := EXISTS (SELECT *
                                      FROM pmieducar.matricula_turma mt
                                     INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
                                     INNER JOIN pmieducar.matricula m2 ON (m2.cod_matricula = cod_matricula)
                                     INNER JOIN pmieducar.matricula_turma mt2 ON (mt2.ref_cod_matricula = cod_matricula
                                                                                  AND mt2.ref_cod_turma = cod_turma
                                                                                  AND mt2.sequencial = sequencial)
                                     WHERE mt.ref_cod_turma = mt2.ref_cod_turma
                                       AND mt.ref_cod_matricula <> mt2.ref_cod_matricula
                                       AND m.ref_cod_aluno = m2.ref_cod_aluno
                                       AND mt.data_enturmacao > mt2.data_enturmacao);

        RETURN existe_matricula;
      END;

      $$;


CREATE FUNCTION relatorio.count_weekdays(start_date date, end_date date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
                        DECLARE
                          tmp_date date;
                          tmp_dow integer;
                          -- double precision returned from extract
                          tot_dow integer;
                        BEGIN
                          tmp_date := start_date;
                          tot_dow := 0;

                          WHILE (tmp_date <= end_date) LOOP
                            SELECT INTO tmp_dow cast(extract(dow
                              FROM tmp_date) AS integer);

                            IF ((tmp_dow >= 2) AND (tmp_dow <= 6)) THEN
                              tot_dow := (tot_dow + 1);
                            END IF;

                            SELECT INTO tmp_date (tmp_date + interval '1 DAY ');

                          END LOOP;

                          RETURN tot_dow;
                        END; $$;


CREATE FUNCTION relatorio.formata_nome(var text) RETURNS text
    LANGUAGE sql
    AS $_$
                        SELECT array_to_string(array_agg(nomes),' ')
                          FROM(
                               SELECT CASE WHEN lower(x.id_unico[i]) = 'de' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = 'dos' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = 'da' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = 'e' THEN lower(x.id_unico[i])
                                           ELSE upper(substring(x.id_unico[i],1,1)) || lower(substring(x.id_unico[i],2))
                                            END AS nomes
                                 FROM(
                                      SELECT *
                                        FROM string_to_array(cast($1 AS text),' ') AS id_unico) AS x,
            generate_series(1,array_upper(string_to_array(cast($1 as text),' '),1)) AS i) AS x;
                        $_$;


COMMENT ON FUNCTION relatorio.formata_nome(var text) IS 'Função que formata um nome, colocando iniciais em maiúsculas e demais em minúsculas';


CREATE FUNCTION relatorio.get_nome_escola(integer) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT COALESCE(
               (SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
            FROM cadastro.pessoa ps, cadastro.juridica
           WHERE escola.ref_idpes = juridica.idpes
             AND juridica.idpes = ps.idpes
             AND ps.idpes = escola.ref_idpes),
               (SELECT nm_escola
            FROM pmieducar.escola_complemento
           WHERE ref_cod_escola = escola.cod_escola))
          FROM pmieducar.escola
         WHERE escola.cod_escola = $1;$_$;


CREATE FUNCTION relatorio.get_texto_sem_caracter_especial(character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT translate(public.fcn_upper($1),
                       'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                       'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$_$;


CREATE FUNCTION relatorio.get_texto_sem_espaco(character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$
SELECT translate(public.fcn_upper(regexp_replace($1,' ','','g')), 'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ', 'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$_$;


CREATE AGGREGATE public.textcat_all(text) (
    SFUNC = public.commacat_ignore_nulls,
    STYPE = text,
    INITCOND = ''
);




SET default_tablespace = '';

SET default_with_oids = true;

CREATE TABLE acesso.funcao (
    idfunc integer DEFAULT nextval('acesso.funcao_idfunc_seq'::regclass) NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL,
    nome character varying(100) NOT NULL,
    situacao character(1) NOT NULL,
    url character varying(250) NOT NULL,
    ordem numeric(2,0) NOT NULL,
    descricao character varying(250) NOT NULL,
    CONSTRAINT ck_funcao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);




CREATE TABLE acesso.grupo (
    idgrp integer DEFAULT nextval('acesso.grupo_idgrp_seq'::regclass) NOT NULL,
    nome character varying(40) NOT NULL,
    situacao character(1) NOT NULL,
    descricao character varying(250),
    CONSTRAINT ck_grupo_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE acesso.grupo_funcao (
    idmen integer NOT NULL,
    idsis integer NOT NULL,
    idgrp integer NOT NULL,
    idfunc integer NOT NULL
);


CREATE TABLE acesso.grupo_menu (
    idgrp integer NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL
);


CREATE TABLE acesso.grupo_operacao (
    idfunc integer NOT NULL,
    idgrp integer NOT NULL,
    idsis integer NOT NULL,
    idmen integer NOT NULL,
    idope integer NOT NULL
);


CREATE TABLE acesso.grupo_sistema (
    idsis integer NOT NULL,
    idgrp integer NOT NULL
);


CREATE TABLE acesso.historico_senha (
    login character varying(16) NOT NULL,
    senha character varying(60) NOT NULL,
    data_cad timestamp without time zone NOT NULL
);




CREATE TABLE acesso.instituicao (
    idins integer DEFAULT nextval('acesso.instituicao_idins_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_instituicao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE acesso.log_acesso (
    data timestamp without time zone NOT NULL,
    idpes numeric(8,0) NOT NULL,
    idsis integer,
    idins integer,
    idcli character varying(10),
    operacao character(1) NOT NULL,
    CONSTRAINT ck_log_acesso_situacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'O'::bpchar)))
);


CREATE TABLE acesso.log_erro (
    data timestamp without time zone NOT NULL,
    idpes numeric(8,0),
    idsis integer,
    idmen integer,
    idfunc integer,
    idope integer,
    msg_erro text NOT NULL
);




CREATE TABLE acesso.menu (
    idmen integer DEFAULT nextval('acesso.menu_idmen_seq'::regclass) NOT NULL,
    idsis integer NOT NULL,
    menu_idsis integer,
    menu_idmen integer,
    nome character varying(40) NOT NULL,
    descricao character varying(250) NOT NULL,
    situacao character(1) NOT NULL,
    ordem numeric(2,0) NOT NULL,
    CONSTRAINT ck_menu_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);




CREATE TABLE acesso.operacao (
    idope integer DEFAULT nextval('acesso.operacao_idope_seq'::regclass) NOT NULL,
    idsis integer,
    nome character varying(40) NOT NULL,
    situacao character(1) NOT NULL,
    descricao character varying(250) NOT NULL,
    CONSTRAINT ck_operacao_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE acesso.operacao_funcao (
    idmen integer NOT NULL,
    idsis integer NOT NULL,
    idfunc integer NOT NULL,
    idope integer NOT NULL
);


CREATE TABLE acesso.pessoa_instituicao (
    idins integer NOT NULL,
    idpes numeric(8,0) NOT NULL
);




CREATE TABLE acesso.sistema (
    idsis integer DEFAULT nextval('acesso.sistema_idsis_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    descricao character varying(100) NOT NULL,
    contexto character varying(30) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_sistema_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE acesso.usuario (
    login character varying(16) NOT NULL,
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


CREATE TABLE acesso.usuario_grupo (
    idgrp integer NOT NULL,
    login character varying(16) NOT NULL
);




CREATE TABLE alimentos.baixa_guia_produto (
    idbap integer DEFAULT nextval('alimentos.baixa_guia_produto_idbap_seq'::regclass) NOT NULL,
    idgup integer NOT NULL,
    idbai integer NOT NULL,
    dt_validade date,
    qtde_recebida numeric NOT NULL,
    dt_operacao date NOT NULL,
    login_baixa character varying(80) NOT NULL
);




CREATE TABLE alimentos.baixa_guia_remessa (
    idbai integer DEFAULT nextval('alimentos.baixa_guia_remessa_idbai_seq'::regclass) NOT NULL,
    login_baixa character varying(80) NOT NULL,
    idgui integer NOT NULL,
    dt_recebimento date NOT NULL,
    nome_recebedor character varying(40) NOT NULL,
    cargo_recebedor character varying(40) NOT NULL,
    dt_operacao date NOT NULL
);




CREATE TABLE alimentos.calendario (
    idcad integer DEFAULT nextval('alimentos.calendario_idcad_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    ano integer NOT NULL,
    descricao character varying(40) NOT NULL
);




CREATE TABLE alimentos.cardapio (
    idcar integer DEFAULT nextval('alimentos.cardapio_idcar_seq'::regclass) NOT NULL,
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


CREATE TABLE alimentos.cardapio_faixa_unidade (
    idfeu integer NOT NULL,
    idcar integer NOT NULL
);




CREATE TABLE alimentos.cardapio_produto (
    idcpr integer DEFAULT nextval('alimentos.cardapio_produto_idcpr_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idcar integer NOT NULL,
    quantidade numeric NOT NULL,
    valor numeric NOT NULL
);


CREATE TABLE alimentos.cardapio_receita (
    idcar integer NOT NULL,
    idrec integer NOT NULL
);


CREATE TABLE alimentos.cliente (
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




CREATE TABLE alimentos.composto_quimico (
    idcom integer DEFAULT nextval('alimentos.composto_quimico_idcom_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    idgrpq integer NOT NULL,
    descricao character varying(50) NOT NULL,
    unidade character varying(5) NOT NULL
);




CREATE TABLE alimentos.contrato (
    idcon integer DEFAULT nextval('alimentos.contrato_idcon_seq'::regclass) NOT NULL,
    codigo character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    login character varying(80) NOT NULL,
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




CREATE TABLE alimentos.contrato_produto (
    idcop integer DEFAULT nextval('alimentos.contrato_produto_idcop_seq'::regclass) NOT NULL,
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
    CONSTRAINT ck_contrato_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar) OR (operacao = 'N'::bpchar)))
);




CREATE TABLE alimentos.evento (
    ideve integer DEFAULT nextval('alimentos.evento_ideve_seq'::regclass) NOT NULL,
    idcad integer NOT NULL,
    mes integer NOT NULL,
    dia integer NOT NULL,
    dia_util character(1) NOT NULL,
    descricao character varying(50) NOT NULL,
    CONSTRAINT ck_evento_dia_util CHECK (((dia_util = 'S'::bpchar) OR (dia_util = 'N'::bpchar)))
);




CREATE TABLE alimentos.faixa_composto_quimico (
    idfcp integer DEFAULT nextval('alimentos.faixa_composto_quimico_idfcp_seq'::regclass) NOT NULL,
    idcom integer NOT NULL,
    idfae integer NOT NULL,
    quantidade numeric NOT NULL,
    qtde_max_min character(3) NOT NULL,
    CONSTRAINT ck_qtde_max_min CHECK (((qtde_max_min = 'MAX'::bpchar) OR (qtde_max_min = 'MIN'::bpchar)))
);




CREATE TABLE alimentos.faixa_etaria (
    idfae integer DEFAULT nextval('alimentos.faixa_etaria_idfae_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL,
    vlr_base_refeicao numeric NOT NULL
);




CREATE TABLE alimentos.fornecedor (
    idfor integer DEFAULT nextval('alimentos.fornecedor_idfor_seq'::regclass) NOT NULL,
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


CREATE TABLE alimentos.fornecedor_unidade_atendida (
    iduni integer NOT NULL,
    idfor integer NOT NULL
);




CREATE TABLE alimentos.grupo_quimico (
    idgrpq integer DEFAULT nextval('alimentos.grupo_quimico_idgrpq_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);




CREATE TABLE alimentos.guia_produto_diario (
    idguiaprodiario integer DEFAULT nextval('alimentos.guia_produto_diario_idguiaprodiario_seq'::regclass) NOT NULL,
    idgui integer NOT NULL,
    idpro integer NOT NULL,
    iduni integer NOT NULL,
    dt_guia date NOT NULL,
    qtde numeric NOT NULL
);




CREATE TABLE alimentos.guia_remessa (
    idgui integer DEFAULT nextval('alimentos.guia_remessa_idgui_seq'::regclass) NOT NULL,
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
    CONSTRAINT ck_guia_remessa_classe_produto CHECK ((((classe_produto)::text = 'P'::text) OR ((classe_produto)::text = 'N'::text) OR ((classe_produto)::text = 'PN'::text))),
    CONSTRAINT ck_guia_remessa_situacao CHECK (((situacao = 'E'::bpchar) OR (situacao = 'R'::bpchar) OR (situacao = 'C'::bpchar) OR (situacao = 'P'::bpchar)))
);




CREATE TABLE alimentos.guia_remessa_produto (
    idgup integer DEFAULT nextval('alimentos.guia_remessa_produto_idgup_seq'::regclass) NOT NULL,
    idgui integer NOT NULL,
    idpro integer NOT NULL,
    qtde_per_capita numeric NOT NULL,
    qtde_guia numeric NOT NULL,
    peso numeric NOT NULL,
    qtde_recebida numeric NOT NULL,
    peso_total numeric NOT NULL
);




CREATE TABLE alimentos.log_guia_remessa (
    idlogguia integer DEFAULT nextval('alimentos.log_guia_remessa_idlogguia_seq'::regclass) NOT NULL,
    login character varying(80) NOT NULL,
    idcli character varying(10) NOT NULL,
    dt_inicial date NOT NULL,
    dt_final date NOT NULL,
    unidade character varying(80) NOT NULL,
    fornecedor character varying(80) NOT NULL,
    classe character(2),
    dt_geracao timestamp without time zone NOT NULL,
    mensagem text NOT NULL
);


CREATE TABLE alimentos.medidas_caseiras (
    idmedcas character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);




CREATE TABLE alimentos.pessoa (
    idpes integer DEFAULT nextval('alimentos.pessoa_idpes_seq'::regclass) NOT NULL,
    tipo character varying(1) NOT NULL,
    CONSTRAINT ck_pessoa CHECK ((((tipo)::text = 'C'::text) OR ((tipo)::text = 'F'::text) OR ((tipo)::text = 'U'::text)))
);




CREATE TABLE alimentos.produto (
    idpro integer DEFAULT nextval('alimentos.produto_idpro_seq'::regclass) NOT NULL,
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




CREATE TABLE alimentos.produto_composto_quimico (
    idpcq integer DEFAULT nextval('alimentos.produto_composto_quimico_idpcq_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idcom integer NOT NULL,
    quantidade numeric NOT NULL
);




CREATE TABLE alimentos.produto_fornecedor (
    idprf integer DEFAULT nextval('alimentos.produto_fornecedor_idprf_seq'::regclass) NOT NULL,
    idfor integer NOT NULL,
    idpro integer NOT NULL,
    codigo_ean character varying(18) NOT NULL
);




CREATE TABLE alimentos.produto_medida_caseira (
    idpmc integer DEFAULT nextval('alimentos.produto_medida_caseira_idpmc_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    idmedcas character varying(20) NOT NULL,
    idpro integer NOT NULL,
    peso numeric NOT NULL
);




CREATE TABLE alimentos.receita (
    idrec integer DEFAULT nextval('alimentos.receita_idrec_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    valor numeric NOT NULL,
    descricao character varying(60) NOT NULL,
    modo_preparo text NOT NULL,
    rendimento integer NOT NULL,
    valor_percapita numeric NOT NULL
);




CREATE TABLE alimentos.receita_composto_quimico (
    idrcq integer DEFAULT nextval('alimentos.receita_composto_quimico_idrcq_seq'::regclass) NOT NULL,
    idcom integer NOT NULL,
    idrec integer NOT NULL,
    quantidade numeric NOT NULL
);




CREATE TABLE alimentos.receita_produto (
    idrpr integer DEFAULT nextval('alimentos.receita_produto_idrpr_seq'::regclass) NOT NULL,
    idpro integer NOT NULL,
    idrec integer NOT NULL,
    idmedcas character varying(20),
    quantidade numeric NOT NULL,
    valor numeric NOT NULL,
    qtdemedidacaseira integer NOT NULL,
    valor_percapita numeric NOT NULL
);




CREATE TABLE alimentos.tipo_produto (
    idtip integer DEFAULT nextval('alimentos.tipo_produto_idtip_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);




CREATE TABLE alimentos.tipo_refeicao (
    idtre integer DEFAULT nextval('alimentos.tipo_refeicao_idtre_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(30) NOT NULL
);




CREATE TABLE alimentos.tipo_unidade (
    idtip integer DEFAULT nextval('alimentos.tipo_unidade_idtip_seq'::regclass) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL
);




CREATE TABLE alimentos.unidade_atendida (
    iduni integer DEFAULT nextval('alimentos.unidade_atendida_iduni_seq'::regclass) NOT NULL,
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




CREATE TABLE alimentos.unidade_faixa_etaria (
    idfeu integer DEFAULT nextval('alimentos.unidade_faixa_etaria_idfeu_seq'::regclass) NOT NULL,
    iduni integer NOT NULL,
    idfae integer NOT NULL,
    num_inscritos integer NOT NULL,
    num_matriculados integer NOT NULL
);


CREATE TABLE alimentos.unidade_produto (
    idunp character varying(20) NOT NULL,
    idcli character varying(10) NOT NULL,
    descricao character varying(50) NOT NULL,
    peso numeric NOT NULL
);


CREATE TABLE cadastro.aviso_nome (
    idpes numeric(8,0) NOT NULL,
    aviso numeric(1,0) NOT NULL,
    CONSTRAINT ck_aviso_nome_aviso CHECK (((aviso >= (1)::numeric) AND (aviso <= (4)::numeric)))
);


SET default_with_oids = false;

CREATE TABLE cadastro.codigo_cartorio_inep (
    id integer NOT NULL,
    id_cartorio integer NOT NULL,
    descricao character varying,
    cod_serventia integer,
    cod_municipio integer,
    ref_sigla_uf character varying(3)
);




ALTER SEQUENCE cadastro.codigo_cartorio_inep_id_seq OWNED BY cadastro.codigo_cartorio_inep.id;




SET default_with_oids = true;

CREATE TABLE cadastro.deficiencia (
    cod_deficiencia integer DEFAULT nextval('cadastro.deficiencia_cod_deficiencia_seq'::regclass) NOT NULL,
    nm_deficiencia character varying(70) NOT NULL,
    deficiencia_educacenso smallint,
    desconsidera_regra_diferenciada boolean DEFAULT false
);


CREATE TABLE cadastro.documento (
    idpes numeric(8,0) NOT NULL,
    rg character varying(25),
    data_exp_rg date,
    sigla_uf_exp_rg character(2),
    tipo_cert_civil numeric(2,0),
    num_termo numeric(8,0),
    num_livro character varying(8),
    num_folha numeric(4,0),
    data_emissao_cert_civil date,
    sigla_uf_cert_civil character(2),
    cartorio_cert_civil character varying(200),
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
    certidao_nascimento character varying(50),
    cartorio_cert_civil_inep integer,
    certidao_casamento character varying(50),
    passaporte character varying(20),
    comprovante_residencia character varying(255),
    declaracao_trabalho_autonomo character varying,
    CONSTRAINT ck_documento_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_documento_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_documento_tipo_cert CHECK (((tipo_cert_civil >= (91)::numeric) AND (tipo_cert_civil <= (92)::numeric)))
);


CREATE TABLE cadastro.endereco_externo (
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
    zona_localizacao integer DEFAULT 1,
    CONSTRAINT ck_endereco_externo_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_externo_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_externo_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


CREATE TABLE cadastro.endereco_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(50),
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
    observacoes text,
    CONSTRAINT ck_endereco_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


CREATE TABLE cadastro.escolaridade (
    idesco numeric(2,0) NOT NULL,
    descricao character varying(60) NOT NULL,
    escolaridade smallint
);


CREATE TABLE cadastro.estado_civil (
    ideciv numeric(1,0) NOT NULL,
    descricao character varying(15) NOT NULL
);


CREATE TABLE cadastro.fisica (
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
    nis_pis_pasep numeric(11,0),
    sus character varying(20),
    ocupacao character varying(255),
    empresa character varying(255),
    pessoa_contato character varying(255),
    renda_mensal numeric(10,2),
    data_admissao date,
    ddd_telefone_empresa numeric(3,0),
    telefone_empresa numeric(11,0),
    falecido boolean,
    ativo integer DEFAULT 1,
    ref_usuario_exc integer,
    data_exclusao timestamp without time zone,
    zona_localizacao_censo integer,
    tipo_trabalho integer,
    local_trabalho character varying,
    horario_inicial_trabalho time without time zone,
    horario_final_trabalho time without time zone,
	nome_social varchar(150) NULL,
    CONSTRAINT ck_fisica_nacionalidade CHECK (((nacionalidade >= (1)::numeric) AND (nacionalidade <= (3)::numeric))),
    CONSTRAINT ck_fisica_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fisica_sexo CHECK (((sexo = 'M'::bpchar) OR (sexo = 'F'::bpchar)))
);


CREATE TABLE cadastro.fisica_cpf (
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
    CONSTRAINT ck_fisica_cpf_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_cpf_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE cadastro.fisica_deficiencia (
    ref_idpes integer NOT NULL,
    ref_cod_deficiencia integer NOT NULL
);


CREATE TABLE cadastro.fisica_foto (
    idpes integer NOT NULL,
    caminho character varying(255)
);


CREATE TABLE cadastro.fisica_raca (
    ref_idpes integer NOT NULL,
    ref_cod_raca integer NOT NULL
);


CREATE TABLE cadastro.fisica_sangue (
    idpes numeric(8,0) NOT NULL,
    grupo character(2) NOT NULL,
    rh smallint NOT NULL
);


CREATE TABLE cadastro.fone_pessoa (
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
    CONSTRAINT ck_fone_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (4)::numeric)))
);


CREATE TABLE cadastro.funcionario (
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
    CONSTRAINT ck_funcionario_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_funcionario_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_funcionario_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE cadastro.historico_cartao (
    idpes_cidadao numeric(8,0) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    idpes_emitiu numeric(8,0) NOT NULL,
    tipo character(1) NOT NULL,
    CONSTRAINT ck_historico_cartao_tipo CHECK (((tipo = 'P'::bpchar) OR (tipo = 'D'::bpchar)))
);


CREATE TABLE cadastro.juridica (
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
    CONSTRAINT ck_juridica_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_juridica_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE cadastro.ocupacao (
    idocup numeric(6,0) NOT NULL,
    descricao character varying(250) NOT NULL
);




CREATE TABLE cadastro.orgao_emissor_rg (
    idorg_rg integer DEFAULT nextval('cadastro.orgao_emissor_rg_idorg_rg_seq'::regclass) NOT NULL,
    sigla character varying(20) NOT NULL,
    descricao character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    codigo_educacenso integer,
    CONSTRAINT ck_orgao_emissor_rg_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE cadastro.pessoa (
    idpes numeric(8,0) DEFAULT nextval(('cadastro.seq_pessoa'::text)::regclass) NOT NULL,
    nome character varying(150) NOT NULL,
    idpes_cad numeric(8,0),
    data_cad timestamp without time zone NOT NULL,
    url character varying(60),
    tipo character(1) NOT NULL,
    idpes_rev numeric(8,0),
    data_rev timestamp without time zone,
    email character varying(100),
    situacao character(1) NOT NULL,
    origem_gravacao character(1) NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_pessoa_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar) OR (situacao = 'P'::bpchar))),
    CONSTRAINT ck_pessoa_tipo CHECK (((tipo = 'F'::bpchar) OR (tipo = 'J'::bpchar)))
);


CREATE TABLE cadastro.pessoa_fonetico (
    idpes numeric(8,0) NOT NULL,
    fonema character varying(30) NOT NULL
);




CREATE TABLE cadastro.raca (
    cod_raca integer DEFAULT nextval('cadastro.raca_cod_raca_seq'::regclass) NOT NULL,
    idpes_exc integer,
    idpes_cad integer NOT NULL,
    nm_raca character varying(50) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT false,
    raca_educacenso smallint
);




SET default_with_oids = false;

CREATE TABLE cadastro.religiao (
    cod_religiao integer DEFAULT nextval('cadastro.religiao_cod_religiao_seq'::regclass) NOT NULL,
    idpes_exc integer,
    idpes_cad integer NOT NULL,
    nm_religiao character varying(50) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT false
);




SET default_with_oids = true;

CREATE TABLE cadastro.socio (
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
    CONSTRAINT ck_socio_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_socio_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE public.bairro (
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
    zona_localizacao integer DEFAULT 1,
    iddis integer NOT NULL,
    idsetorbai numeric(6,0),
    CONSTRAINT ck_bairro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE public.logradouro (
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
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE public.municipio (
    idmun numeric(6,0) DEFAULT nextval(('public.seq_municipio'::text)::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    sigla_uf character varying(3) NOT NULL,
    area_km2 numeric(6,0),
    idmreg numeric(2,0),
    idasmun numeric(2,0),
    cod_ibge numeric(20,0),
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
    CONSTRAINT ck_municipio_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_municipio_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_municipio_tipo CHECK (((tipo = 'D'::bpchar) OR (tipo = 'M'::bpchar) OR (tipo = 'P'::bpchar) OR (tipo = 'R'::bpchar)))
);


CREATE VIEW cadastro.v_endereco AS
 SELECT e.idpes,
    e.cep,
    e.idlog,
    e.numero,
    e.letra,
    e.complemento,
    e.idbai,
    e.bloco,
    e.andar,
    e.apartamento,
    l.nome AS logradouro,
    l.idtlog,
    b.nome AS bairro,
    m.nome AS cidade,
    m.sigla_uf,
    b.zona_localizacao
   FROM cadastro.endereco_pessoa e,
    public.logradouro l,
    public.bairro b,
    public.municipio m
  WHERE ((e.idlog = l.idlog) AND (e.idbai = b.idbai) AND (b.idmun = m.idmun) AND (e.tipo = (1)::numeric))
UNION
 SELECT e.idpes,
    e.cep,
    NULL::numeric AS idlog,
    e.numero,
    e.letra,
    e.complemento,
    NULL::numeric AS idbai,
    e.bloco,
    e.andar,
    e.apartamento,
    e.logradouro,
    e.idtlog,
    e.bairro,
    e.cidade,
    e.sigla_uf,
    e.zona_localizacao
   FROM cadastro.endereco_externo e
  WHERE (e.tipo = (1)::numeric);


CREATE VIEW cadastro.v_fone_pessoa AS
 SELECT DISTINCT t.idpes,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS ddd_1,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS fone_1,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS ddd_2,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS fone_2,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS ddd_mov,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS fone_mov,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS ddd_fax,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS fone_fax
   FROM cadastro.fone_pessoa t
  ORDER BY t.idpes, ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes)));


CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS
SELECT
    p.idpes,
    p.nome,
    p.url,
    p.email,
    p.situacao,
    f.nome_social,
    f.data_nasc,
    f.sexo,
    f.cpf,
    f.ref_cod_sistema,
    f.idesco,
    f.ativo
FROM cadastro.pessoa p
INNER JOIN cadastro.fisica f ON TRUE
AND f.idpes = p.idpes;


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


CREATE VIEW cadastro.v_pessoa_fj AS
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


CREATE VIEW cadastro.v_pessoa_juridica AS
 SELECT j.idpes,
    j.fantasia,
    j.cnpj,
    j.insc_estadual,
    j.capital_social,
    ( SELECT pessoa.nome
           FROM cadastro.pessoa
          WHERE (pessoa.idpes = j.idpes)) AS nome
   FROM cadastro.juridica j;


CREATE VIEW cadastro.v_pessoafj_count AS
 SELECT fisica.ref_cod_sistema,
    fisica.cpf AS id_federal
   FROM cadastro.fisica
UNION ALL
 SELECT NULL::integer AS ref_cod_sistema,
    juridica.cnpj AS id_federal
   FROM cadastro.juridica;


CREATE TABLE consistenciacao.campo_consistenciacao (
    idcam numeric(3,0) NOT NULL,
    campo character varying(50) NOT NULL,
    permite_regra_cadastrada character(1) NOT NULL,
    tamanho_maximo numeric(4,0),
    CONSTRAINT ck_campo_consistenciacao_permite_regra CHECK (((permite_regra_cadastrada = 'S'::bpchar) OR (permite_regra_cadastrada = 'N'::bpchar)))
);




CREATE TABLE consistenciacao.campo_metadado (
    id_campo_met integer DEFAULT nextval('consistenciacao.campo_metadado_id_campo_met_seq'::regclass) NOT NULL,
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




CREATE TABLE consistenciacao.confrontacao (
    idcon integer DEFAULT nextval('consistenciacao.confrontacao_idcon_seq'::regclass) NOT NULL,
    idins integer NOT NULL,
    idpes integer NOT NULL,
    idmet integer NOT NULL,
    arquivo_fonte_dados character varying(250) NOT NULL,
    ignorar_reg_fonte date,
    desconsiderar_reg_cred_maxima date,
    data_hora timestamp without time zone NOT NULL
);




CREATE TABLE consistenciacao.fonte (
    idfon integer DEFAULT nextval('consistenciacao.fonte_idfon_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    CONSTRAINT ck_fonte_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE consistenciacao.historico_campo (
    idpes numeric(8,0) NOT NULL,
    idcam numeric(3,0) NOT NULL,
    credibilidade numeric(1,0) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    CONSTRAINT ck_historico_campo_cred CHECK (((credibilidade >= (1)::numeric) AND (credibilidade <= (5)::numeric)))
);




CREATE TABLE consistenciacao.incoerencia (
    idinc integer DEFAULT nextval('consistenciacao.incoerencia_idinc_seq'::regclass) NOT NULL,
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




CREATE TABLE consistenciacao.incoerencia_documento (
    id_inc_doc integer DEFAULT nextval('consistenciacao.incoerencia_documento_id_inc_doc_seq'::regclass) NOT NULL,
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




CREATE TABLE consistenciacao.incoerencia_endereco (
    id_inc_end integer DEFAULT nextval('consistenciacao.incoerencia_endereco_id_inc_end_seq'::regclass) NOT NULL,
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




CREATE TABLE consistenciacao.incoerencia_fone (
    id_inc_fone integer DEFAULT nextval('consistenciacao.incoerencia_fone_id_inc_fone_seq'::regclass) NOT NULL,
    idinc integer NOT NULL,
    tipo character varying(60) NOT NULL,
    ddd numeric(3,0),
    fone numeric(8,0),
    CONSTRAINT ck_incoerencia_fone_tipo CHECK ((((tipo)::text >= ((1)::numeric)::text) AND ((tipo)::text <= ((4)::numeric)::text)))
);


CREATE TABLE consistenciacao.incoerencia_pessoa_possivel (
    idinc integer NOT NULL,
    idpes numeric(8,0) NOT NULL
);


CREATE TABLE consistenciacao.incoerencia_tipo_incoerencia (
    id_tipo_inc numeric(3,0) NOT NULL,
    idinc integer NOT NULL
);




CREATE TABLE consistenciacao.metadado (
    idmet integer DEFAULT nextval('consistenciacao.metadado_idmet_seq'::regclass) NOT NULL,
    idfon integer NOT NULL,
    nome character varying(60) NOT NULL,
    situacao character(1) NOT NULL,
    separador character(1),
    CONSTRAINT ck_metadado_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE consistenciacao.ocorrencia_regra_campo (
    idreg integer NOT NULL,
    conteudo_padrao character varying(60) NOT NULL,
    ocorrencias text NOT NULL
);




CREATE TABLE consistenciacao.regra_campo (
    idreg integer DEFAULT nextval('consistenciacao.regra_campo_idreg_seq'::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    tipo character(1) NOT NULL,
    CONSTRAINT ck_regra_campo_tipo CHECK (((tipo = 'S'::bpchar) OR (tipo = 'N'::bpchar)))
);


CREATE TABLE consistenciacao.temp_cadastro_unificacao_cmf (
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


CREATE TABLE consistenciacao.temp_cadastro_unificacao_siam (
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


CREATE TABLE consistenciacao.tipo_incoerencia (
    id_tipo_inc numeric(3,0) NOT NULL,
    idcam numeric(3,0) NOT NULL,
    descricao character varying(250) NOT NULL
);


CREATE TABLE historico.bairro (
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
    CONSTRAINT ck_bairro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.cep_logradouro (
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
    CONSTRAINT ck_cep_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_cep_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.cep_logradouro_bairro (
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
    CONSTRAINT ck_cep_logradouro_bairro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_cep_logradouro_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.documento (
    idpes numeric(8,0) NOT NULL,
    rg character varying(25),
    data_exp_rg date,
    sigla_uf_exp_rg character(2),
    tipo_cert_civil numeric(2,0),
    num_termo numeric(8,0),
    num_livro character varying(8),
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
    CONSTRAINT ck_documento_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_documento_tipo_cert CHECK (((tipo_cert_civil >= (91)::numeric) AND (tipo_cert_civil <= (92)::numeric))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.endereco_externo (
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
    CONSTRAINT ck_endereco_externo_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_externo_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_externo_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


CREATE TABLE historico.endereco_pessoa (
    idpes numeric(8,0) NOT NULL,
    tipo numeric(1,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    idbai numeric(6,0) NOT NULL,
    numero numeric(6,0),
    letra character(1),
    complemento character varying(50),
    reside_desde date,
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_endereco_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_endereco_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
);


CREATE TABLE historico.fisica (
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
    CONSTRAINT ck_fisica_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fisica_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fisica_sexo CHECK (((sexo = 'M'::bpchar) OR (sexo = 'F'::bpchar)))
);


CREATE TABLE historico.fisica_cpf (
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
    CONSTRAINT ck_fisica_cpf_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar)))
);


CREATE TABLE historico.fone_pessoa (
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
    CONSTRAINT ck_fone_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_fone_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (4)::numeric)))
);


CREATE TABLE historico.funcionario (
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
    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_funcionario_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_funcionario_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);


CREATE TABLE historico.juridica (
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
    CONSTRAINT ck_juridica_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_juridica_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.logradouro (
    idlog numeric(6,0) NOT NULL,
    idtlog character varying(5) NOT NULL,
    nome character varying(150) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    ident_oficial character(1),
    idpes_rev numeric,
    idsis_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    idsis_cad numeric NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_logradouro_ident_oficial CHECK (((ident_oficial = 'S'::bpchar) OR (ident_oficial = 'N'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE historico.municipio (
    idmun numeric(6,0) NOT NULL,
    nome character varying(60) NOT NULL,
    sigla_uf character(2) NOT NULL,
    area_km2 numeric(6,0),
    idmreg numeric(2,0),
    idasmun numeric(2,0),
    cod_ibge numeric(20,0),
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
    CONSTRAINT ck_municipio_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_municipio_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_municipio_tipo CHECK (((tipo = 'D'::bpchar) OR (tipo = 'M'::bpchar) OR (tipo = 'P'::bpchar) OR (tipo = 'R'::bpchar)))
);


CREATE TABLE historico.pessoa (
    idpes numeric(8,0) NOT NULL,
    nome character varying(150) NOT NULL,
    idpes_cad numeric(8,0),
    data_cad timestamp without time zone NOT NULL,
    url character varying(60),
    tipo character(1) NOT NULL,
    idpes_rev numeric(8,0),
    data_rev timestamp without time zone,
    email character varying(100),
    situacao character(1) NOT NULL,
    origem_gravacao character(1) NOT NULL,
    idsis_rev numeric,
    idsis_cad numeric NOT NULL,
    operacao character(1) NOT NULL,
    CONSTRAINT ck_pessoa_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_pessoa_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_pessoa_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar) OR (situacao = 'P'::bpchar))),
    CONSTRAINT ck_pessoa_tipo CHECK (((tipo = 'F'::bpchar) OR (tipo = 'J'::bpchar)))
);


CREATE TABLE historico.socio (
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
    CONSTRAINT ck_socio_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_socio_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


SET default_with_oids = false;

CREATE TABLE modules.area_conhecimento (
    id integer NOT NULL,
    instituicao_id integer NOT NULL,
    nome character varying(200) NOT NULL,
    secao character varying(50),
    ordenamento_ac integer DEFAULT 99999
);




ALTER SEQUENCE modules.area_conhecimento_id_seq OWNED BY modules.area_conhecimento.id;


CREATE TABLE modules.auditoria (
    usuario character varying(300),
    operacao smallint,
    rotina character varying(300),
    valor_antigo text,
    valor_novo text,
    data_hora timestamp without time zone
);


CREATE TABLE modules.auditoria_geral (
    usuario_id integer,
    operacao smallint,
    rotina character varying(50),
    valor_antigo json,
    valor_novo json,
    data_hora timestamp without time zone,
    codigo character varying,
    id integer NOT NULL,
    query text
);




ALTER SEQUENCE modules.auditoria_geral_id_seq OWNED BY modules.auditoria_geral.id;


CREATE TABLE modules.calendario_turma (
    calendario_ano_letivo_id integer NOT NULL,
    ano integer NOT NULL,
    mes integer NOT NULL,
    dia integer NOT NULL,
    turma_id integer NOT NULL
);


CREATE TABLE modules.componente_curricular (
    id integer NOT NULL,
    instituicao_id integer NOT NULL,
    area_conhecimento_id integer NOT NULL,
    nome character varying(500) NOT NULL,
    abreviatura character varying(25) NOT NULL,
    tipo_base smallint NOT NULL,
    codigo_educacenso smallint,
    ordenamento integer DEFAULT 99999
);


CREATE TABLE modules.componente_curricular_ano_escolar (
    componente_curricular_id integer NOT NULL,
    ano_escolar_id integer NOT NULL,
    carga_horaria numeric(7,3),
    tipo_nota integer,
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);




ALTER SEQUENCE modules.componente_curricular_id_seq OWNED BY modules.componente_curricular.id;


CREATE TABLE modules.componente_curricular_turma (
    componente_curricular_id integer NOT NULL,
    ano_escolar_id integer NOT NULL,
    escola_id integer NOT NULL,
    turma_id integer NOT NULL,
    carga_horaria numeric(7,3),
    docente_vinculado smallint,
    etapas_especificas smallint,
    etapas_utilizadas character varying,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


CREATE TABLE modules.config_movimento_geral (
    id integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    coluna integer NOT NULL
);




ALTER SEQUENCE modules.config_movimento_geral_id_seq OWNED BY modules.config_movimento_geral.id;


CREATE TABLE modules.docente_licenciatura (
    id integer NOT NULL,
    servidor_id integer NOT NULL,
    licenciatura integer NOT NULL,
    curso_id integer,
    ano_conclusao integer NOT NULL,
    ies_id integer,
    user_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);




ALTER SEQUENCE modules.docente_licenciatura_id_seq OWNED BY modules.docente_licenciatura.id;


CREATE TABLE modules.educacenso_cod_aluno (
    cod_aluno integer NOT NULL,
    cod_aluno_inep bigint NOT NULL,
    nome_inep character varying(255),
    fonte character varying(255),
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE modules.educacenso_cod_docente (
    cod_servidor integer NOT NULL,
    cod_docente_inep bigint NOT NULL,
    nome_inep character varying(255),
    fonte character varying(255),
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE modules.educacenso_cod_escola (
    cod_escola integer NOT NULL,
    cod_escola_inep bigint NOT NULL,
    nome_inep character varying(255),
    fonte character varying(255),
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE modules.educacenso_cod_turma (
    cod_turma integer NOT NULL,
    cod_turma_inep bigint NOT NULL,
    nome_inep character varying(255),
    fonte character varying(255),
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE modules.educacenso_curso_superior (
    id integer NOT NULL,
    curso_id character varying(100) NOT NULL,
    nome character varying(255) NOT NULL,
    classe_id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    grau_academico smallint
);




ALTER SEQUENCE modules.educacenso_curso_superior_id_seq OWNED BY modules.educacenso_curso_superior.id;


CREATE TABLE modules.educacenso_ies (
    id integer NOT NULL,
    ies_id integer NOT NULL,
    nome character varying(255) NOT NULL,
    dependencia_administrativa_id integer NOT NULL,
    tipo_instituicao_id integer NOT NULL,
    uf character(2),
    user_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);




ALTER SEQUENCE modules.educacenso_ies_id_seq OWNED BY modules.educacenso_ies.id;


CREATE TABLE modules.educacenso_orgao_regional (
    sigla_uf character varying(2) NOT NULL,
    codigo character varying(5) NOT NULL
);




SET default_with_oids = true;

CREATE TABLE modules.empresa_transporte_escolar (
    cod_empresa_transporte_escolar integer DEFAULT nextval('modules.empresa_transporte_escolar_seq'::regclass) NOT NULL,
    ref_idpes integer NOT NULL,
    ref_resp_idpes integer NOT NULL,
    observacao character varying(255)
);


SET default_with_oids = false;

CREATE TABLE modules.etapas_curso_educacenso (
    etapa_id integer NOT NULL,
    curso_id integer NOT NULL
);


CREATE TABLE modules.etapas_educacenso (
    id integer NOT NULL,
    nome character varying(255)
);


CREATE TABLE modules.falta_aluno (
    id integer NOT NULL,
    matricula_id integer NOT NULL,
    tipo_falta smallint NOT NULL
);




ALTER SEQUENCE modules.falta_aluno_id_seq OWNED BY modules.falta_aluno.id;


CREATE TABLE modules.falta_componente_curricular (
    id integer NOT NULL,
    falta_aluno_id integer NOT NULL,
    componente_curricular_id integer NOT NULL,
    quantidade integer DEFAULT 0,
    etapa character varying(2) NOT NULL
);




ALTER SEQUENCE modules.falta_componente_curricular_id_seq OWNED BY modules.falta_componente_curricular.id;


CREATE TABLE modules.falta_geral (
    id integer NOT NULL,
    falta_aluno_id integer NOT NULL,
    quantidade integer DEFAULT 0,
    etapa character varying(2) NOT NULL
);




ALTER SEQUENCE modules.falta_geral_id_seq OWNED BY modules.falta_geral.id;


SET default_with_oids = true;

CREATE TABLE modules.ficha_medica_aluno (
    ref_cod_aluno integer NOT NULL,
    altura character varying(4),
    peso character varying(7),
    grupo_sanguineo character varying(2),
    fator_rh character varying(1),
    alergia_medicamento character(1),
    desc_alergia_medicamento character varying(100),
    alergia_alimento character(1),
    desc_alergia_alimento character varying(100),
    doenca_congenita character(1),
    desc_doenca_congenita character varying(100),
    fumante character(1),
    doenca_caxumba character(1),
    doenca_sarampo character(1),
    doenca_rubeola character(1),
    doenca_catapora character(1),
    doenca_escarlatina character(1),
    doenca_coqueluche character(1),
    doenca_outras character varying(100),
    epiletico character(1),
    epiletico_tratamento character(1),
    hemofilico character(1),
    hipertenso character(1),
    asmatico character(1),
    diabetico character(1),
    insulina character(1),
    tratamento_medico character(1),
    desc_tratamento_medico character varying(100),
    medicacao_especifica character(1),
    desc_medicacao_especifica character varying(100),
    acomp_medico_psicologico character(1),
    desc_acomp_medico_psicologico character varying(100),
    restricao_atividade_fisica character(1),
    desc_restricao_atividade_fisica character varying(100),
    fratura_trauma character(1),
    desc_fratura_trauma character varying(100),
    plano_saude character(1),
    desc_plano_saude character varying(50),
    hospital_clinica character varying(100),
    hospital_clinica_endereco character varying(50),
    hospital_clinica_telefone character varying(20),
    responsavel character varying(50),
    responsavel_parentesco character varying(20),
    responsavel_parentesco_telefone character varying(20),
    responsavel_parentesco_celular character varying(20),
    observacao character varying(255)
);


SET default_with_oids = false;

CREATE TABLE modules.formula_media (
    id integer NOT NULL,
    instituicao_id integer NOT NULL,
    nome character varying(50) NOT NULL,
    formula_media character varying(200) NOT NULL,
    tipo_formula smallint DEFAULT 1,
    substitui_menor_nota_rc smallint DEFAULT 0 NOT NULL
);




ALTER SEQUENCE modules.formula_media_id_seq OWNED BY modules.formula_media.id;




SET default_with_oids = true;

CREATE TABLE modules.itinerario_transporte_escolar (
    cod_itinerario_transporte_escolar integer DEFAULT nextval('modules.itinerario_transporte_escolar_seq'::regclass) NOT NULL,
    ref_cod_rota_transporte_escolar integer NOT NULL,
    seq integer NOT NULL,
    ref_cod_ponto_transporte_escolar integer NOT NULL,
    ref_cod_veiculo integer,
    hora time without time zone,
    tipo character(1) NOT NULL
);


SET default_with_oids = false;

CREATE TABLE modules.lingua_indigena_educacenso (
    id integer NOT NULL,
    lingua character varying(255)
);


CREATE TABLE modules.media_geral (
    nota_aluno_id integer NOT NULL,
    media numeric(8,4) DEFAULT 0,
    media_arredondada character varying(10) DEFAULT 0,
    etapa character varying(2) NOT NULL
);


SET default_with_oids = true;

CREATE TABLE modules.moradia_aluno (
    ref_cod_aluno integer NOT NULL,
    moradia character(1),
    material character(1) DEFAULT 'A'::bpchar,
    casa_outra character varying(20),
    moradia_situacao integer,
    quartos integer,
    sala integer,
    copa integer,
    banheiro integer,
    garagem integer,
    empregada_domestica character(1),
    automovel character(1),
    motocicleta character(1),
    computador character(1),
    geladeira character(1),
    fogao character(1),
    maquina_lavar character(1),
    microondas character(1),
    video_dvd character(1),
    televisao character(1),
    celular character(1),
    telefone character(1),
    quant_pessoas integer,
    renda double precision,
    agua_encanada character(1),
    poco character(1),
    energia character(1),
    esgoto character(1),
    fossa character(1),
    lixo character(1)
);




CREATE TABLE modules.motorista (
    cod_motorista integer DEFAULT nextval('modules.motorista_seq'::regclass) NOT NULL,
    ref_idpes integer NOT NULL,
    cnh character varying(15),
    tipo_cnh character varying(2),
    dt_habilitacao date,
    vencimento_cnh date,
    ref_cod_empresa_transporte_escolar integer NOT NULL,
    observacao character varying(255)
);


SET default_with_oids = false;

CREATE TABLE modules.nota_aluno (
    id integer NOT NULL,
    matricula_id integer NOT NULL
);




ALTER SEQUENCE modules.nota_aluno_id_seq OWNED BY modules.nota_aluno.id;


CREATE TABLE modules.nota_componente_curricular (
    id integer NOT NULL,
    nota_aluno_id integer NOT NULL,
    componente_curricular_id integer NOT NULL,
    nota numeric(8,4) DEFAULT 0,
    nota_arredondada character varying(10) DEFAULT 0,
    etapa character varying(2) NOT NULL,
    nota_recuperacao character varying(10),
    nota_original character varying(10),
    nota_recuperacao_especifica character varying(10)
);




ALTER SEQUENCE modules.nota_componente_curricular_id_seq OWNED BY modules.nota_componente_curricular.id;


CREATE TABLE modules.nota_componente_curricular_media (
    nota_aluno_id integer NOT NULL,
    componente_curricular_id integer NOT NULL,
    media numeric(8,4) DEFAULT 0,
    media_arredondada character varying(10) DEFAULT 0,
    etapa character varying(2) NOT NULL,
    situacao integer
);


SET default_with_oids = true;

CREATE TABLE modules.nota_exame (
    ref_cod_matricula integer NOT NULL,
    ref_cod_componente_curricular integer NOT NULL,
    nota_exame numeric(6,3)
);




SET default_with_oids = false;

CREATE TABLE modules.nota_geral (
    id integer DEFAULT nextval('modules.nota_geral_id_seq'::regclass) NOT NULL,
    nota_aluno_id integer NOT NULL,
    nota numeric(8,4) DEFAULT 0,
    nota_arredondada character varying(10) DEFAULT 0,
    etapa character varying(2) NOT NULL
);


CREATE TABLE modules.parecer_aluno (
    id integer NOT NULL,
    matricula_id integer NOT NULL,
    parecer_descritivo smallint NOT NULL
);




ALTER SEQUENCE modules.parecer_aluno_id_seq OWNED BY modules.parecer_aluno.id;


CREATE TABLE modules.parecer_componente_curricular (
    id integer NOT NULL,
    parecer_aluno_id integer NOT NULL,
    componente_curricular_id integer NOT NULL,
    parecer text,
    etapa character varying(2) NOT NULL
);




ALTER SEQUENCE modules.parecer_componente_curricular_id_seq OWNED BY modules.parecer_componente_curricular.id;


CREATE TABLE modules.parecer_geral (
    id integer NOT NULL,
    parecer_aluno_id integer NOT NULL,
    parecer text,
    etapa character varying(2) NOT NULL
);




ALTER SEQUENCE modules.parecer_geral_id_seq OWNED BY modules.parecer_geral.id;




SET default_with_oids = true;

CREATE TABLE modules.pessoa_transporte (
    cod_pessoa_transporte integer DEFAULT nextval('modules.pessoa_transporte_seq'::regclass) NOT NULL,
    ref_idpes integer NOT NULL,
    ref_cod_rota_transporte_escolar integer NOT NULL,
    ref_cod_ponto_transporte_escolar integer,
    ref_idpes_destino integer,
    observacao character varying(255),
    turno character varying(255)
);




CREATE TABLE modules.ponto_transporte_escolar (
    cod_ponto_transporte_escolar integer DEFAULT nextval('modules.ponto_transporte_escolar_seq'::regclass) NOT NULL,
    descricao character varying(70) NOT NULL,
    cep numeric(8,0),
    idlog numeric(6,0),
    idbai numeric(6,0),
    numero numeric(6,0),
    complemento character varying(20),
    latitude character varying(20),
    longitude character varying(20)
);




SET default_with_oids = false;

CREATE TABLE modules.professor_turma (
    id integer DEFAULT nextval('modules.professor_turma_id_seq'::regclass) NOT NULL,
    ano smallint NOT NULL,
    instituicao_id integer NOT NULL,
    turma_id integer NOT NULL,
    servidor_id integer NOT NULL,
    funcao_exercida smallint NOT NULL,
    tipo_vinculo smallint,
    permite_lancar_faltas_componente integer DEFAULT 0,
    updated_at timestamp without time zone,
    turno_id integer
);


CREATE TABLE modules.professor_turma_disciplina (
    professor_turma_id integer NOT NULL,
    componente_curricular_id integer NOT NULL
);


CREATE TABLE modules.regra_avaliacao (
    id integer NOT NULL,
    instituicao_id integer NOT NULL,
    formula_media_id integer NOT NULL,
    formula_recuperacao_id integer,
    tabela_arredondamento_id integer,
    nome character varying(50) NOT NULL,
    tipo_nota smallint NOT NULL,
    tipo_progressao smallint NOT NULL,
    media numeric(5,3) DEFAULT 0.000,
    porcentagem_presenca numeric(6,3) DEFAULT 0.000,
    parecer_descritivo smallint DEFAULT 0,
    tipo_presenca smallint NOT NULL,
    media_recuperacao numeric(5,3) DEFAULT 0.000,
    tipo_recuperacao_paralela smallint DEFAULT 0,
    media_recuperacao_paralela numeric(5,3),
    nota_maxima_geral integer DEFAULT 10 NOT NULL,
    nota_maxima_exame_final integer DEFAULT 10 NOT NULL,
    qtd_casas_decimais integer DEFAULT 2 NOT NULL,
    nota_geral_por_etapa smallint DEFAULT 0,
    qtd_disciplinas_dependencia smallint DEFAULT 0 NOT NULL,
    aprova_media_disciplina smallint DEFAULT 0,
    reprovacao_automatica smallint DEFAULT 0,
    definir_componente_etapa smallint,
    qtd_matriculas_dependencia smallint DEFAULT 0 NOT NULL,
    nota_minima_geral integer DEFAULT 0,
    tabela_arredondamento_id_conceitual integer,
    regra_diferenciada_id integer
);




ALTER SEQUENCE modules.regra_avaliacao_id_seq OWNED BY modules.regra_avaliacao.id;




CREATE TABLE modules.regra_avaliacao_recuperacao (
    id integer DEFAULT nextval('modules.regra_avaliacao_recuperacao_id_seq'::regclass) NOT NULL,
    regra_avaliacao_id integer NOT NULL,
    descricao character varying(25) NOT NULL,
    etapas_recuperadas character varying(25) NOT NULL,
    substitui_menor_nota boolean,
    media numeric(8,4) NOT NULL,
    nota_maxima numeric(8,4) NOT NULL
);


CREATE TABLE modules.regra_avaliacao_serie_ano (
    serie_id integer NOT NULL,
    regra_avaliacao_id integer NOT NULL,
    regra_avaliacao_diferenciada_id integer,
    ano_letivo smallint NOT NULL
);




SET default_with_oids = true;

CREATE TABLE modules.rota_transporte_escolar (
    cod_rota_transporte_escolar integer DEFAULT nextval('modules.rota_transporte_escolar_seq'::regclass) NOT NULL,
    ref_idpes_destino integer NOT NULL,
    descricao character varying(50) NOT NULL,
    ano integer NOT NULL,
    tipo_rota character(1) NOT NULL,
    km_pav double precision,
    km_npav double precision,
    ref_cod_empresa_transporte_escolar integer,
    tercerizado character(1) NOT NULL
);


SET default_with_oids = false;

CREATE TABLE modules.tabela_arredondamento (
    id integer NOT NULL,
    instituicao_id integer NOT NULL,
    nome character varying(50) NOT NULL,
    tipo_nota smallint DEFAULT 1 NOT NULL
);




ALTER SEQUENCE modules.tabela_arredondamento_id_seq OWNED BY modules.tabela_arredondamento.id;


CREATE TABLE modules.tabela_arredondamento_valor (
    id integer NOT NULL,
    tabela_arredondamento_id integer NOT NULL,
    nome character varying(5) NOT NULL,
    descricao character varying(25),
    valor_minimo numeric(5,3),
    valor_maximo numeric(5,3),
    casa_decimal_exata smallint,
    acao smallint
);




ALTER SEQUENCE modules.tabela_arredondamento_valor_id_seq OWNED BY modules.tabela_arredondamento_valor.id;




SET default_with_oids = true;

CREATE TABLE modules.tipo_veiculo (
    cod_tipo_veiculo integer DEFAULT nextval('modules.tipo_veiculo_seq'::regclass) NOT NULL,
    descricao character varying(60)
);


SET default_with_oids = false;

CREATE TABLE modules.transporte_aluno (
    aluno_id integer NOT NULL,
    responsavel integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


SET default_with_oids = true;

CREATE TABLE modules.uniforme_aluno (
    ref_cod_aluno integer NOT NULL,
    recebeu_uniforme character(1),
    quantidade_camiseta integer,
    tamanho_camiseta character(2),
    quantidade_blusa_jaqueta integer,
    tamanho_blusa_jaqueta character(2),
    quantidade_bermuda integer,
    tamanho_bermuda character(2),
    quantidade_calca integer,
    tamanho_calca character(2),
    quantidade_saia integer,
    tamanho_saia character(2),
    quantidade_calcado integer,
    tamanho_calcado character(2),
    quantidade_meia integer,
    tamanho_meia character(2)
);




CREATE TABLE modules.veiculo (
    cod_veiculo integer DEFAULT nextval('modules.veiculo_seq'::regclass) NOT NULL,
    descricao character varying(255) NOT NULL,
    placa character varying(10),
    renavam character varying(15) NOT NULL,
    chassi character varying(30),
    marca character varying(50),
    ano_fabricacao integer,
    ano_modelo integer,
    passageiros integer NOT NULL,
    malha character(1) NOT NULL,
    ref_cod_tipo_veiculo integer NOT NULL,
    exclusivo_transporte_escolar character(1) NOT NULL,
    adaptado_necessidades_especiais character(1) NOT NULL,
    ativo character(1),
    descricao_inativo character(155),
    ref_cod_empresa_transporte_escolar integer NOT NULL,
    ref_cod_motorista integer,
    observacao character varying(255)
);




CREATE TABLE pmiacoes.acao_governo (
    cod_acao_governo integer DEFAULT nextval('pmiacoes.acao_governo_cod_acao_governo_seq'::regclass) NOT NULL,
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




CREATE TABLE pmiacoes.acao_governo_arquivo (
    cod_acao_governo_arquivo integer DEFAULT nextval('pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL,
    nm_arquivo character varying(255) NOT NULL,
    caminho_arquivo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


CREATE TABLE pmiacoes.acao_governo_categoria (
    ref_cod_categoria integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL
);




CREATE TABLE pmiacoes.acao_governo_foto (
    cod_acao_governo_foto integer DEFAULT nextval('pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_cod_acao_governo integer NOT NULL,
    nm_foto character varying(255) NOT NULL,
    caminho character varying(255) NOT NULL,
    data_foto timestamp without time zone,
    data_cadastro timestamp without time zone NOT NULL
);


CREATE TABLE pmiacoes.acao_governo_foto_portal (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_foto_portal integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


CREATE TABLE pmiacoes.acao_governo_noticia (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_not_portal integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);


CREATE TABLE pmiacoes.acao_governo_setor (
    ref_cod_acao_governo integer NOT NULL,
    ref_cod_setor integer NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);




CREATE TABLE pmiacoes.categoria (
    cod_categoria integer DEFAULT nextval('pmiacoes.categoria_cod_categoria_seq'::regclass) NOT NULL,
    ref_funcionario_exc integer,
    ref_funcionario_cad integer NOT NULL,
    nm_categoria character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE TABLE pmiacoes.secretaria_responsavel (
    ref_cod_setor integer NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);




CREATE TABLE pmicontrolesis.acontecimento (
    cod_acontecimento integer DEFAULT nextval('pmicontrolesis.acontecimento_cod_acontecimento_seq'::regclass) NOT NULL,
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
    local character varying,
    contato character varying,
    link character varying
);




CREATE TABLE pmicontrolesis.artigo (
    cod_artigo integer DEFAULT nextval('pmicontrolesis.artigo_cod_artigo_seq'::regclass) NOT NULL,
    texto text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);




CREATE TABLE pmicontrolesis.foto_evento (
    cod_foto_evento integer DEFAULT nextval('pmicontrolesis.foto_evento_cod_foto_evento_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_foto timestamp without time zone,
    titulo character varying(255),
    descricao text,
    caminho character varying(255),
    altura integer,
    largura integer,
    nm_credito character varying(255)
);




SET default_with_oids = false;

CREATE TABLE pmicontrolesis.foto_vinc (
    cod_foto_vinc integer DEFAULT nextval('pmicontrolesis.foto_vinc_cod_foto_vinc_seq'::regclass) NOT NULL,
    ref_cod_acontecimento integer NOT NULL,
    ref_cod_foto_evento integer NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmicontrolesis.itinerario (
    cod_itinerario integer DEFAULT nextval('pmicontrolesis.itinerario_cod_itinerario_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.menu (
    cod_menu integer DEFAULT nextval('pmicontrolesis.menu_cod_menu_seq'::regclass) NOT NULL,
    ref_cod_menu_submenu integer,
    ref_cod_menu_pai integer,
    tt_menu character varying(255) NOT NULL,
    ord_menu integer NOT NULL,
    caminho character varying(255),
    alvo character varying(20),
    suprime_menu smallint DEFAULT 1,
    ref_cod_tutormenu integer,
    ref_cod_ico integer,
    tipo_menu integer
);




CREATE TABLE pmicontrolesis.menu_portal (
    cod_menu_portal integer DEFAULT nextval('pmicontrolesis.menu_portal_cod_menu_portal_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.portais (
    cod_portais integer DEFAULT nextval('pmicontrolesis.portais_cod_portais_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.servicos (
    cod_servicos integer DEFAULT nextval('pmicontrolesis.servicos_cod_servicos_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.sistema (
    cod_sistema integer DEFAULT nextval('pmicontrolesis.sistema_cod_sistema_seq'::regclass) NOT NULL,
    nm_sistema character varying(255) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);




CREATE TABLE pmicontrolesis.submenu_portal (
    cod_submenu_portal integer DEFAULT nextval('pmicontrolesis.submenu_portal_cod_submenu_portal_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.telefones (
    cod_telefones integer DEFAULT nextval('pmicontrolesis.telefones_cod_telefones_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nome character varying(255) NOT NULL,
    numero character varying,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmicontrolesis.tipo_acontecimento (
    cod_tipo_acontecimento integer DEFAULT nextval('pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq'::regclass) NOT NULL,
    ref_cod_funcionario_cad integer NOT NULL,
    ref_cod_funcionario_exc integer,
    nm_tipo character varying(255),
    caminho character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint
);




CREATE TABLE pmicontrolesis.topo_portal (
    cod_topo_portal integer DEFAULT nextval('pmicontrolesis.topo_portal_cod_topo_portal_seq'::regclass) NOT NULL,
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




CREATE TABLE pmicontrolesis.tutormenu (
    cod_tutormenu integer DEFAULT nextval('pmicontrolesis.tutormenu_cod_tutormenu_seq'::regclass) NOT NULL,
    nm_tutormenu character varying(200) NOT NULL
);




CREATE TABLE pmidrh.diaria (
    cod_diaria integer DEFAULT nextval('pmidrh.diaria_cod_diaria_seq'::regclass) NOT NULL,
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




CREATE TABLE pmidrh.diaria_grupo (
    cod_diaria_grupo integer DEFAULT nextval('pmidrh.diaria_grupo_cod_diaria_grupo_seq'::regclass) NOT NULL,
    desc_grupo character varying(255) NOT NULL
);




CREATE TABLE pmidrh.diaria_valores (
    cod_diaria_valores integer DEFAULT nextval('pmidrh.diaria_valores_cod_diaria_valores_seq'::regclass) NOT NULL,
    ref_funcionario_cadastro integer NOT NULL,
    ref_cod_diaria_grupo integer NOT NULL,
    estadual smallint NOT NULL,
    p100 double precision,
    p75 double precision,
    p50 double precision,
    p25 double precision,
    data_vigencia timestamp without time zone NOT NULL
);




CREATE TABLE pmidrh.setor (
    cod_setor integer DEFAULT nextval('pmidrh.setor_cod_setor_seq'::regclass) NOT NULL,
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




SET default_with_oids = false;

CREATE TABLE pmieducar.abandono_tipo (
    cod_abandono_tipo integer DEFAULT nextval('pmieducar.abandono_tipo_cod_abandono_tipo_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer,
    nome character varying(255) NOT NULL,
    data_cadastro timestamp without time zone,
    data_exclusao timestamp without time zone,
    ativo integer
);




SET default_with_oids = true;

CREATE TABLE pmieducar.acervo (
    cod_acervo integer DEFAULT nextval('pmieducar.acervo_cod_acervo_seq'::regclass) NOT NULL,
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
    volume integer,
    num_edicao integer,
    ano character varying(25),
    num_paginas integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    isbn character varying(255),
    cdd character varying(20),
    estante character varying(20),
    dimencao character varying(255),
    material_ilustrativo character varying(255),
    dimencao_ilustrativo character varying(255),
    local character varying(255),
    ref_cod_tipo_autor integer,
    tipo_autor character varying(255)
);


CREATE TABLE pmieducar.acervo_acervo_assunto (
    ref_cod_acervo integer NOT NULL,
    ref_cod_acervo_assunto integer NOT NULL
);


CREATE TABLE pmieducar.acervo_acervo_autor (
    ref_cod_acervo_autor integer NOT NULL,
    ref_cod_acervo integer NOT NULL,
    principal smallint DEFAULT (0)::smallint NOT NULL
);




CREATE TABLE pmieducar.acervo_assunto (
    cod_acervo_assunto integer DEFAULT nextval('pmieducar.acervo_assunto_cod_acervo_assunto_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_assunto character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.acervo_autor (
    cod_acervo_autor integer DEFAULT nextval('pmieducar.acervo_autor_cod_acervo_autor_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_autor character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);




CREATE TABLE pmieducar.acervo_colecao (
    cod_acervo_colecao integer DEFAULT nextval('pmieducar.acervo_colecao_cod_acervo_colecao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_colecao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.acervo_editora (
    cod_acervo_editora integer DEFAULT nextval('pmieducar.acervo_editora_cod_acervo_editora_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_idtlog character varying(20),
    ref_sigla_uf character(2),
    nm_editora character varying(255) NOT NULL,
    cep numeric(8,0),
    cidade character varying(60),
    bairro character varying(60),
    logradouro character varying(255),
    numero numeric(6,0),
    telefone integer,
    ddd_telefone numeric(3,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.acervo_idioma (
    cod_acervo_idioma integer DEFAULT nextval('pmieducar.acervo_idioma_cod_acervo_idioma_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_idioma character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.aluno (
    cod_aluno integer DEFAULT nextval('pmieducar.aluno_cod_aluno_seq'::regclass) NOT NULL,
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
    tipo_responsavel character(1),
    aluno_estado_id character varying(25),
    justificativa_falta_documentacao smallint,
    url_laudo_medico json,
    codigo_sistema character varying(30),
    veiculo_transporte_escolar smallint,
    autorizado_um character varying(150),
    parentesco_um character varying(150),
    autorizado_dois character varying(150),
    parentesco_dois character varying(150),
    autorizado_tres character varying(150),
    parentesco_tres character varying(150),
    autorizado_quatro character varying(150),
    parentesco_quatro character varying(150),
    autorizado_cinco character varying(150),
    parentesco_cinco character varying(150),
    url_documento json,
    recebe_escolarizacao_em_outro_espaco smallint DEFAULT 3 NOT NULL,
    recursos_prova_inep integer[]
);


SET default_with_oids = false;

CREATE TABLE pmieducar.aluno_aluno_beneficio (
    aluno_id integer NOT NULL,
    aluno_beneficio_id integer NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.aluno_beneficio (
    cod_aluno_beneficio integer DEFAULT nextval('pmieducar.aluno_beneficio_cod_aluno_beneficio_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_beneficio character varying(255) NOT NULL,
    desc_beneficio text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

CREATE TABLE pmieducar.aluno_historico_altura_peso (
    ref_cod_aluno integer NOT NULL,
    data_historico date NOT NULL,
    altura numeric(12,2) NOT NULL,
    peso numeric(12,2) NOT NULL
);


SET default_with_oids = true;

CREATE TABLE pmieducar.ano_letivo_modulo (
    ref_ano integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL,
    dias_letivos numeric(5,0)
);


SET default_with_oids = false;

CREATE TABLE pmieducar.auditoria_falta_componente_dispensa (
    id integer NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_componente_curricular integer NOT NULL,
    quantidade integer NOT NULL,
    etapa integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);




ALTER SEQUENCE pmieducar.auditoria_falta_componente_dispensa_id_seq OWNED BY pmieducar.auditoria_falta_componente_dispensa.id;


CREATE TABLE pmieducar.auditoria_nota_dispensa (
    id integer NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_componente_curricular integer NOT NULL,
    nota numeric(8,4) NOT NULL,
    etapa integer NOT NULL,
    nota_recuperacao character varying(10),
    nota_recuperacao_especifica character varying(10),
    data_cadastro timestamp without time zone NOT NULL
);




ALTER SEQUENCE pmieducar.auditoria_nota_dispensa_id_seq OWNED BY pmieducar.auditoria_nota_dispensa.id;


SET default_with_oids = true;

CREATE TABLE pmieducar.avaliacao_desempenho (
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


SET default_with_oids = false;

CREATE TABLE pmieducar.backup (
    id integer NOT NULL,
    caminho character varying(255) NOT NULL,
    data_backup timestamp without time zone
);




ALTER SEQUENCE pmieducar.backup_id_seq OWNED BY pmieducar.backup.id;




SET default_with_oids = true;

CREATE TABLE pmieducar.biblioteca (
    cod_biblioteca integer DEFAULT nextval('pmieducar.biblioteca_cod_biblioteca_seq'::regclass) NOT NULL,
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
    tombo_automatico boolean DEFAULT true,
    bloqueia_emprestimo_em_atraso boolean
);


CREATE TABLE pmieducar.biblioteca_dia (
    ref_cod_biblioteca integer NOT NULL,
    dia numeric(1,0) NOT NULL
);




CREATE TABLE pmieducar.biblioteca_feriados (
    cod_feriado integer DEFAULT nextval('pmieducar.biblioteca_feriados_cod_feriado_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    nm_feriado character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_feriado date NOT NULL
);


CREATE TABLE pmieducar.biblioteca_usuario (
    ref_cod_biblioteca integer NOT NULL,
    ref_cod_usuario integer NOT NULL
);


CREATE TABLE pmieducar.bloqueio_ano_letivo (
    ref_cod_instituicao integer NOT NULL,
    ref_ano integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);




SET default_with_oids = false;

CREATE TABLE pmieducar.bloqueio_lancamento_faltas_notas (
    cod_bloqueio integer DEFAULT nextval('public.bloqueio_lancamento_faltas_notas_seq'::regclass) NOT NULL,
    ano integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    etapa integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.calendario_ano_letivo (
    cod_calendario_ano_letivo integer DEFAULT nextval('pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    data_cadastra timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.calendario_anotacao (
    cod_calendario_anotacao integer DEFAULT nextval('pmieducar.calendario_anotacao_cod_calendario_anotacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_anotacao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL
);


CREATE TABLE pmieducar.calendario_dia (
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


CREATE TABLE pmieducar.calendario_dia_anotacao (
    ref_dia integer NOT NULL,
    ref_mes integer NOT NULL,
    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
    ref_cod_calendario_anotacao integer NOT NULL
);




CREATE TABLE pmieducar.calendario_dia_motivo (
    cod_calendario_dia_motivo integer DEFAULT nextval('pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq'::regclass) NOT NULL,
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




SET default_with_oids = false;

CREATE TABLE pmieducar.candidato_reserva_vaga (
    cod_candidato_reserva_vaga integer DEFAULT nextval('pmieducar.candidato_reserva_vaga_seq'::regclass) NOT NULL,
    ano_letivo integer NOT NULL,
    data_solicitacao date NOT NULL,
    ref_cod_aluno integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_turno integer,
    ref_cod_pessoa_cad integer NOT NULL,
    data_cad timestamp without time zone DEFAULT now() NOT NULL,
    data_update timestamp without time zone DEFAULT now() NOT NULL,
    ref_cod_matricula integer,
    situacao character(1),
    data_situacao date,
    motivo character varying(255),
    ref_cod_escola integer,
    quantidade_membros smallint,
    mae_fez_pre_natal boolean DEFAULT false,
    membros_trabalham smallint,
    hora_solicitacao time without time zone
);




CREATE TABLE pmieducar.categoria_nivel (
    cod_categoria_nivel integer DEFAULT nextval('pmieducar.categoria_nivel_cod_categoria_nivel_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_categoria_nivel character varying(100) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL
);


CREATE TABLE pmieducar.categoria_obra (
    id integer NOT NULL,
    descricao character varying(100) NOT NULL,
    observacoes character varying(300)
);




ALTER SEQUENCE pmieducar.categoria_obra_id_seq OWNED BY pmieducar.categoria_obra.id;




SET default_with_oids = true;

CREATE TABLE pmieducar.cliente (
    cod_cliente integer DEFAULT nextval('pmieducar.cliente_cod_cliente_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_idpes integer NOT NULL,
    login integer,
    senha character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    observacoes text
);


CREATE TABLE pmieducar.cliente_suspensao (
    sequencial integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_motivo_suspensao integer NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_suspende integer NOT NULL,
    dias integer NOT NULL,
    data_suspensao timestamp without time zone NOT NULL,
    data_liberacao timestamp without time zone
);




CREATE TABLE pmieducar.cliente_tipo (
    cod_cliente_tipo integer DEFAULT nextval('pmieducar.cliente_tipo_cod_cliente_tipo_seq'::regclass) NOT NULL,
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

CREATE TABLE pmieducar.cliente_tipo_cliente (
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

CREATE TABLE pmieducar.cliente_tipo_exemplar_tipo (
    ref_cod_cliente_tipo integer NOT NULL,
    ref_cod_exemplar_tipo integer NOT NULL,
    dias_emprestimo numeric(3,0)
);




CREATE TABLE pmieducar.coffebreak_tipo (
    cod_coffebreak_tipo integer DEFAULT nextval('pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    custo_unitario double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


SET default_with_oids = false;

CREATE TABLE pmieducar.configuracoes_gerais (
    ref_cod_instituicao integer NOT NULL,
    permite_relacionamento_posvendas integer DEFAULT 1 NOT NULL,
    url_novo_educacao character varying(100),
    mostrar_codigo_inep_aluno smallint DEFAULT 1,
    justificativa_falta_documentacao_obrigatorio smallint DEFAULT 1,
    tamanho_min_rede_estadual integer,
    modelo_boletim_professor integer DEFAULT 1,
    custom_labels json,
    url_cadastro_usuario character varying(255) DEFAULT NULL::character varying,
    active_on_ieducar smallint DEFAULT 1,
    ieducar_image character varying(255) DEFAULT NULL::character varying,
    ieducar_entity_name character varying(255) DEFAULT NULL::character varying,
    ieducar_login_footer text DEFAULT '<p>Portabilis Tecnologia - suporte@portabilis.com.br - <a class="   light" href="http://suporte.portabilis.com.br" target="_blank"> Obter Suporte </a></p> '::character varying,
    ieducar_external_footer text DEFAULT '<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, acesse nosso <a href="   http://blog.portabilis.com.br">blog</a></p> '::character varying,
    ieducar_internal_footer text DEFAULT '<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, <a href="   http://blog.portabilis.com.br" target="_blank">acesse nosso blog</a> &nbsp;&nbsp;&nbsp; &copy; Portabilis - Todos os direitos reservados</p>'::character varying,
    facebook_url character varying(255) DEFAULT 'https://www.facebook.com/portabilis'::character varying,
    twitter_url character varying(255) DEFAULT 'https://twitter.com/portabilis'::character varying,
    linkedin_url character varying(255) DEFAULT 'https://www.linkedin.com/company/portabilis-tecnologia'::character varying,
    ieducar_suspension_message text
);


COMMENT ON COLUMN pmieducar.configuracoes_gerais.mostrar_codigo_inep_aluno IS 'Mostrar código INEP do aluno nas telas de cadastro';


COMMENT ON COLUMN pmieducar.configuracoes_gerais.justificativa_falta_documentacao_obrigatorio IS 'Campo "Justificativa para a falta de documentação" obrigatório no cadastro de alunos';


COMMENT ON COLUMN pmieducar.configuracoes_gerais.tamanho_min_rede_estadual IS 'Tamanho mínimo do campo "Código rede estadual"';


COMMENT ON COLUMN pmieducar.configuracoes_gerais.modelo_boletim_professor IS 'Modelo do boletim do professor. 1 - Padrão, 2 - Modelo recuperação por etapa, 3 - Modelo recuperação paralela';


COMMENT ON COLUMN pmieducar.configuracoes_gerais.custom_labels IS 'Guarda customizações em labels e textos do sistema.';


COMMENT ON COLUMN pmieducar.configuracoes_gerais.url_cadastro_usuario IS 'URL da ferramenta externa de cadastro de usuários';




SET default_with_oids = true;

CREATE TABLE pmieducar.curso (
    cod_curso integer DEFAULT nextval('pmieducar.curso_cod_curso_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_tipo_regime integer,
    ref_cod_nivel_ensino integer NOT NULL,
    ref_cod_tipo_ensino integer NOT NULL,
    nm_curso character varying(255) NOT NULL,
    sgl_curso character varying(15) NOT NULL,
    qtd_etapas smallint NOT NULL,
    carga_horaria double precision NOT NULL,
    ato_poder_publico character varying(255),
    objetivo_curso text,
    publico_alvo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    padrao_ano_escolar smallint DEFAULT (0)::smallint NOT NULL,
    hora_falta double precision DEFAULT 0.00 NOT NULL,
    multi_seriado integer,
    modalidade_curso integer
);




CREATE TABLE pmieducar.disciplina (
    cod_disciplina integer DEFAULT nextval('pmieducar.disciplina_cod_disciplina_seq'::regclass) NOT NULL,
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

CREATE TABLE pmieducar.disciplina_dependencia (
    ref_cod_matricula integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    observacao text,
    cod_disciplina_dependencia integer NOT NULL
);


CREATE TABLE pmieducar.disciplina_serie (
    ref_cod_disciplina integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.disciplina_topico (
    cod_disciplina_topico integer DEFAULT nextval('pmieducar.disciplina_topico_cod_disciplina_topico_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_topico character varying(255) NOT NULL,
    desc_topico text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




SET default_with_oids = false;

CREATE TABLE pmieducar.dispensa_disciplina (
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
    cod_dispensa integer DEFAULT nextval('pmieducar.dispensa_disciplina_cod_dispensa_seq'::regclass) NOT NULL
);


CREATE TABLE pmieducar.dispensa_etapa (
    ref_cod_dispensa integer,
    etapa integer
);




SET default_with_oids = true;

CREATE TABLE pmieducar.distribuicao_uniforme (
    cod_distribuicao_uniforme integer DEFAULT nextval('pmieducar.distribuicao_uniforme_seq'::regclass) NOT NULL,
    ref_cod_aluno integer NOT NULL,
    ano integer NOT NULL,
    kit_completo boolean,
    agasalho_qtd smallint,
    camiseta_curta_qtd smallint,
    camiseta_longa_qtd smallint,
    meias_qtd smallint,
    bermudas_tectels_qtd smallint,
    bermudas_coton_qtd smallint,
    tenis_qtd smallint,
    data date,
    agasalho_tm character varying(20),
    camiseta_curta_tm character varying(20),
    camiseta_longa_tm character varying(20),
    meias_tm character varying(20),
    bermudas_tectels_tm character varying(20),
    bermudas_coton_tm character varying(20),
    tenis_tm character varying(20),
    ref_cod_escola integer,
    camiseta_infantil_qtd smallint,
    camiseta_infantil_tm character varying(20)
);




CREATE TABLE pmieducar.escola (
    cod_escola integer DEFAULT nextval('pmieducar.escola_cod_escola_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola_rede_ensino integer NOT NULL,
    ref_idpes integer,
    sigla character varying(20) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    bloquear_lancamento_diario_anos_letivos_encerrados integer,
    situacao_funcionamento integer DEFAULT 1,
    dependencia_administrativa integer DEFAULT 3,
    regulamentacao integer DEFAULT 1,
    longitude character varying(20),
    latitude character varying(20),
    acesso integer,
    ref_idpes_gestor integer,
    cargo_gestor integer,
    local_funcionamento integer,
    condicao integer DEFAULT 1,
    codigo_inep_escola_compartilhada integer,
    decreto_criacao character varying(50),
    area_terreno_total character varying(10),
    area_construida character varying(10),
    area_disponivel character varying(10),
    num_pavimentos integer,
    tipo_piso integer,
    medidor_energia integer,
    agua_consumida integer,
    dependencia_sala_diretoria integer,
    dependencia_sala_professores integer,
    dependencia_sala_secretaria integer,
    dependencia_laboratorio_informatica integer,
    dependencia_laboratorio_ciencias integer,
    dependencia_sala_aee integer,
    dependencia_quadra_coberta integer,
    dependencia_quadra_descoberta integer,
    dependencia_cozinha integer,
    dependencia_biblioteca integer,
    dependencia_sala_leitura integer,
    dependencia_parque_infantil integer,
    dependencia_bercario integer,
    dependencia_banheiro_fora integer,
    dependencia_banheiro_dentro integer,
    dependencia_banheiro_infantil integer,
    dependencia_banheiro_deficiente integer,
    dependencia_banheiro_chuveiro integer,
    dependencia_refeitorio integer,
    dependencia_dispensa integer,
    dependencia_aumoxarifado integer,
    dependencia_auditorio integer,
    dependencia_patio_coberto integer,
    dependencia_patio_descoberto integer,
    dependencia_alojamento_aluno integer,
    dependencia_alojamento_professor integer,
    dependencia_area_verde integer,
    dependencia_lavanderia integer,
    dependencia_unidade_climatizada integer,
    dependencia_quantidade_ambiente_climatizado integer,
    dependencia_nenhuma_relacionada integer,
    dependencia_numero_salas_existente integer,
    dependencia_numero_salas_utilizadas integer,
    porte_quadra_descoberta integer,
    porte_quadra_coberta integer,
    tipo_cobertura_patio integer,
    total_funcionario integer,
    atendimento_aee integer DEFAULT 0,
    atividade_complementar integer DEFAULT 0,
    fundamental_ciclo integer,
    localizacao_diferenciada integer DEFAULT 7,
    didatico_nao_utiliza integer,
    didatico_quilombola integer,
    didatico_indigena integer,
    educacao_indigena integer,
    lingua_ministrada integer,
    espaco_brasil_aprendizado integer,
    abre_final_semana integer,
    codigo_lingua_indigena integer,
    proposta_pedagogica integer,
    televisoes smallint,
    videocassetes smallint,
    dvds smallint,
    antenas_parabolicas smallint,
    copiadoras smallint,
    retroprojetores smallint,
    impressoras smallint,
    aparelhos_de_som smallint,
    projetores_digitais smallint,
    faxs smallint,
    maquinas_fotograficas smallint,
    computadores smallint,
    computadores_administrativo smallint,
    computadores_alunos smallint,
    acesso_internet smallint,
    ato_criacao character varying(255),
    dependencia_vias_deficiente smallint,
    utiliza_regra_diferenciada boolean,
    ato_autorizativo character varying(255),
    ref_idpes_secretario_escolar integer,
    impressoras_multifuncionais smallint,
    categoria_escola_privada integer,
    conveniada_com_poder_publico integer,
    cnpj_mantenedora_principal numeric(14,0),
    mantenedora_escola_privada integer[],
    materiais_didaticos_especificos integer,
    abastecimento_agua integer[],
    abastecimento_energia integer[],
    esgoto_sanitario integer[],
    destinacao_lixo integer[],
    email_gestor character varying(255),
    zona_localizacao smallint,
    codigo_inep_escola_compartilhada2 integer,
    codigo_inep_escola_compartilhada3 integer,
    codigo_inep_escola_compartilhada4 integer,
    codigo_inep_escola_compartilhada5 integer,
    codigo_inep_escola_compartilhada6 integer
);


CREATE TABLE pmieducar.escola_ano_letivo (
    ref_cod_escola integer NOT NULL,
    ano integer NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    andamento smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    turmas_por_ano smallint
);


CREATE TABLE pmieducar.escola_complemento (
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


CREATE TABLE pmieducar.escola_curso (
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    autorizacao character varying(255),
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);




CREATE TABLE pmieducar.escola_localizacao (
    cod_escola_localizacao integer DEFAULT nextval('pmieducar.escola_localizacao_cod_escola_localizacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_localizacao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.escola_rede_ensino (
    cod_escola_rede_ensino integer DEFAULT nextval('pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_rede character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


CREATE TABLE pmieducar.escola_serie (
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    hora_inicial time without time zone,
    hora_final time without time zone,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    hora_inicio_intervalo time without time zone,
    hora_fim_intervalo time without time zone,
    bloquear_enturmacao_sem_vagas integer,
    bloquear_cadastro_turma_para_serie_com_vagas integer,
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);


SET default_with_oids = false;

CREATE TABLE pmieducar.escola_serie_disciplina (
    ref_ref_cod_serie integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    carga_horaria numeric(7,3),
    etapas_especificas smallint,
    etapas_utilizadas character varying,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);


CREATE TABLE pmieducar.escola_usuario (
    id integer NOT NULL,
    ref_cod_usuario integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    escola_atual integer
);




ALTER SEQUENCE pmieducar.escola_usuario_id_seq OWNED BY pmieducar.escola_usuario.id;




SET default_with_oids = true;

CREATE TABLE pmieducar.exemplar (
    cod_exemplar integer DEFAULT nextval('pmieducar.exemplar_cod_exemplar_seq'::regclass) NOT NULL,
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
    tombo integer,
    sequencial integer,
    data_baixa_exemplar date
);




CREATE TABLE pmieducar.exemplar_emprestimo (
    cod_emprestimo integer DEFAULT nextval('pmieducar.exemplar_emprestimo_cod_emprestimo_seq'::regclass) NOT NULL,
    ref_usuario_devolucao integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_exemplar integer NOT NULL,
    data_retirada timestamp without time zone NOT NULL,
    data_devolucao timestamp without time zone,
    valor_multa double precision
);




CREATE TABLE pmieducar.exemplar_tipo (
    cod_exemplar_tipo integer DEFAULT nextval('pmieducar.exemplar_tipo_cod_exemplar_tipo_seq'::regclass) NOT NULL,
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

CREATE TABLE pmieducar.falta_aluno (
    cod_falta_aluno integer DEFAULT nextval('pmieducar.falta_aluno_cod_falta_aluno_seq'::regclass) NOT NULL,
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




SET default_with_oids = true;

CREATE TABLE pmieducar.falta_atraso (
    cod_falta_atraso integer DEFAULT nextval('pmieducar.falta_atraso_cod_falta_atraso_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.falta_atraso_compensado (
    cod_compensado integer DEFAULT nextval('pmieducar.falta_atraso_compensado_cod_compensado_seq'::regclass) NOT NULL,
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




SET default_with_oids = false;

CREATE TABLE pmieducar.faltas (
    ref_cod_matricula integer NOT NULL,
    sequencial integer DEFAULT nextval('pmieducar.faltas_sequencial_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    falta integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.fonte (
    cod_fonte integer DEFAULT nextval('pmieducar.fonte_cod_fonte_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_fonte character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.funcao (
    cod_funcao integer DEFAULT nextval('pmieducar.funcao_cod_funcao_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.habilitacao (
    cod_habilitacao integer DEFAULT nextval('pmieducar.habilitacao_cod_habilitacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);


CREATE TABLE pmieducar.habilitacao_curso (
    ref_cod_habilitacao integer NOT NULL,
    ref_cod_curso integer NOT NULL
);


CREATE TABLE pmieducar.historico_disciplinas (
    sequencial integer NOT NULL,
    ref_ref_cod_aluno integer NOT NULL,
    ref_sequencial integer NOT NULL,
    nm_disciplina text NOT NULL,
    nota character varying(255) NOT NULL,
    faltas integer,
    import numeric(1,0),
    ordenamento integer,
    carga_horaria_disciplina integer,
    dependencia boolean DEFAULT false
);


SET default_with_oids = false;

CREATE TABLE pmieducar.historico_educar (
    tabela character varying(50),
    alteracao text,
    data timestamp without time zone,
    insercao smallint DEFAULT 0
);


SET default_with_oids = true;

CREATE TABLE pmieducar.historico_escolar (
    ref_cod_aluno integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    carga_horaria double precision,
    dias_letivos integer,
    escola character varying(255) NOT NULL,
    escola_cidade character varying(255) NOT NULL,
    escola_uf character varying(3),
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
    import numeric(1,0),
    frequencia numeric(5,2) DEFAULT 0.000,
    registro character varying(50),
    livro character varying(50),
    folha character varying(50),
    historico_grade_curso_id integer,
    nm_curso character varying(255),
    aceleracao integer,
    ref_cod_escola integer,
    dependencia boolean,
    posicao integer
);




CREATE TABLE pmieducar.historico_grade_curso (
    id integer DEFAULT nextval('pmieducar.historico_grade_curso_seq'::regclass) NOT NULL,
    descricao_etapa character varying(20) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    quantidade_etapas integer,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.infra_comodo_funcao (
    cod_infra_comodo_funcao integer DEFAULT nextval('pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_funcao character varying(255) NOT NULL,
    desc_funcao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_escola integer
);




CREATE TABLE pmieducar.infra_predio (
    cod_infra_predio integer DEFAULT nextval('pmieducar.infra_predio_cod_infra_predio_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.infra_predio_comodo (
    cod_infra_predio_comodo integer DEFAULT nextval('pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.instituicao (
    cod_instituicao integer DEFAULT nextval('pmieducar.instituicao_cod_instituicao_seq'::regclass) NOT NULL,
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
    nm_instituicao character varying(255) NOT NULL,
    data_base_remanejamento date,
    data_base_transferencia date,
    controlar_espaco_utilizacao_aluno smallint,
    percentagem_maxima_ocupacao_salas numeric(5,2),
    quantidade_alunos_metro_quadrado integer,
    exigir_vinculo_turma_professor smallint,
    gerar_historico_transferencia boolean,
    matricula_apenas_bairro_escola boolean,
    restringir_historico_escolar boolean,
    coordenador_transporte character varying,
    restringir_multiplas_enturmacoes boolean,
    permissao_filtro_abandono_transferencia boolean,
    data_base_matricula date,
    multiplas_reserva_vaga boolean DEFAULT false NOT NULL,
    reserva_integral_somente_com_renda boolean DEFAULT false NOT NULL,
    data_expiracao_reserva_vaga date,
    data_fechamento date,
    componente_curricular_turma boolean,
    reprova_dependencia_ano_concluinte boolean,
    controlar_posicao_historicos boolean,
    data_educacenso date,
    bloqueia_matricula_serie_nao_seguinte boolean,
    permitir_carga_horaria boolean DEFAULT false,
    exigir_dados_socioeconomicos boolean DEFAULT false,
    altera_atestado_para_declaracao boolean,
    orgao_regional integer,
    obrigar_campos_censo boolean,
    obrigar_documento_pessoa boolean DEFAULT false,
    exigir_lancamentos_anteriores boolean DEFAULT false,
    exibir_apenas_professores_alocados boolean DEFAULT false
);


COMMENT ON COLUMN pmieducar.instituicao.exibir_apenas_professores_alocados IS 'Para filtros de emissão de relatórios';




SET default_with_oids = false;

CREATE TABLE pmieducar.instituicao_documentacao (
    id integer DEFAULT nextval('pmieducar.instituicao_documentacao_seq'::regclass) NOT NULL,
    instituicao_id integer NOT NULL,
    titulo_documento character varying(100) NOT NULL,
    url_documento character varying(255) NOT NULL,
    ref_usuario_cad integer DEFAULT 0 NOT NULL,
    ref_cod_escola integer
);




SET default_with_oids = true;

CREATE TABLE pmieducar.material_didatico (
    cod_material_didatico integer DEFAULT nextval('pmieducar.material_didatico_cod_material_didatico_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.material_tipo (
    cod_material_tipo integer DEFAULT nextval('pmieducar.material_tipo_cod_material_tipo_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.matricula (
    cod_matricula integer DEFAULT nextval('pmieducar.matricula_cod_matricula_seq'::regclass) NOT NULL,
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
    semestre smallint,
    observacao character varying(300),
    data_matricula timestamp without time zone,
    data_cancel timestamp without time zone,
    ref_cod_abandono_tipo integer,
    turno_pre_matricula smallint,
    dependencia boolean DEFAULT false,
    updated_at timestamp without time zone,
    saida_escola boolean DEFAULT false,
    data_saida_escola date,
    turno_id integer
);




SET default_with_oids = false;

CREATE TABLE pmieducar.matricula_excessao (
    cod_aluno_excessao integer DEFAULT nextval('pmieducar.matricula_excessao_cod_aluno_excessao_seq'::regclass) NOT NULL,
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

CREATE TABLE pmieducar.matricula_ocorrencia_disciplinar (
    ref_cod_matricula integer NOT NULL,
    ref_cod_tipo_ocorrencia_disciplinar integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    observacao text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    visivel_pais integer,
    cod_ocorrencia_disciplinar integer DEFAULT nextval('pmieducar.ocorrencia_disciplinar_seq'::regclass) NOT NULL
);


CREATE TABLE pmieducar.matricula_turma (
    ref_cod_matricula integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_enturmacao date NOT NULL,
    sequencial_fechamento integer DEFAULT 0 NOT NULL,
    transferido boolean,
    remanejado boolean,
    reclassificado boolean,
    abandono boolean,
    updated_at timestamp without time zone,
    falecido boolean,
    etapa_educacenso smallint,
    turma_unificada smallint
);


CREATE TABLE pmieducar.menu_tipo_usuario (
    ref_cod_tipo_usuario integer NOT NULL,
    ref_cod_menu_submenu integer NOT NULL,
    cadastra smallint DEFAULT 0 NOT NULL,
    visualiza smallint DEFAULT 0 NOT NULL,
    exclui smallint DEFAULT 0 NOT NULL
);




CREATE TABLE pmieducar.modulo (
    cod_modulo integer DEFAULT nextval('pmieducar.modulo_cod_modulo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    num_meses numeric(2,0) DEFAULT NULL::numeric,
    num_semanas integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    num_etapas numeric(2,0) DEFAULT 0 NOT NULL
);




CREATE TABLE pmieducar.motivo_afastamento (
    cod_motivo_afastamento integer DEFAULT nextval('pmieducar.motivo_afastamento_cod_motivo_afastamento_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.motivo_baixa (
    cod_motivo_baixa integer DEFAULT nextval('pmieducar.motivo_baixa_cod_motivo_baixa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo_baixa character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




CREATE TABLE pmieducar.motivo_suspensao (
    cod_motivo_suspensao integer DEFAULT nextval('pmieducar.motivo_suspensao_cod_motivo_suspensao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);




SET default_with_oids = false;

CREATE TABLE pmieducar.nivel (
    cod_nivel integer DEFAULT nextval('pmieducar.nivel_cod_nivel_seq'::regclass) NOT NULL,
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




SET default_with_oids = true;

CREATE TABLE pmieducar.nivel_ensino (
    cod_nivel_ensino integer DEFAULT nextval('pmieducar.nivel_ensino_cod_nivel_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_nivel character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




SET default_with_oids = false;

CREATE TABLE pmieducar.nota_aluno (
    cod_nota_aluno integer DEFAULT nextval('pmieducar.nota_aluno_cod_nota_aluno_seq'::regclass) NOT NULL,
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




SET default_with_oids = true;

CREATE TABLE pmieducar.operador (
    cod_operador integer DEFAULT nextval('pmieducar.operador_cod_operador_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nome character varying(50) NOT NULL,
    valor text NOT NULL,
    fim_sentenca smallint DEFAULT (1)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.pagamento_multa (
    cod_pagamento_multa integer DEFAULT nextval('pmieducar.pagamento_multa_cod_pagamento_multa_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    valor_pago double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);




CREATE TABLE pmieducar.pre_requisito (
    cod_pre_requisito integer DEFAULT nextval('pmieducar.pre_requisito_cod_pre_requisito_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.projeto (
    cod_projeto integer DEFAULT nextval('pmieducar.projeto_seq'::regclass) NOT NULL,
    nome character varying(50),
    observacao character varying(255)
);


CREATE TABLE pmieducar.projeto_aluno (
    ref_cod_projeto integer NOT NULL,
    ref_cod_aluno integer NOT NULL,
    data_inclusao date,
    data_desligamento date,
    turno integer
);




CREATE TABLE pmieducar.quadro_horario (
    cod_quadro_horario integer DEFAULT nextval('pmieducar.quadro_horario_cod_quadro_horario_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ano integer
);


SET default_with_oids = false;

CREATE TABLE pmieducar.quadro_horario_horarios (
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


CREATE TABLE pmieducar.quadro_horario_horarios_aux (
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


CREATE TABLE pmieducar.quantidade_reserva_externa (
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_turma_turno_id integer NOT NULL,
    ano integer NOT NULL,
    qtd_alunos integer NOT NULL
);


CREATE TABLE pmieducar.relacao_categoria_acervo (
    ref_cod_acervo integer NOT NULL,
    categoria_id integer NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.religiao (
    cod_religiao integer DEFAULT nextval('pmieducar.religiao_cod_religiao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_religiao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.reserva_vaga (
    cod_reserva_vaga integer DEFAULT nextval('pmieducar.reserva_vaga_cod_reserva_vaga_seq'::regclass) NOT NULL,
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




CREATE TABLE pmieducar.reservas (
    cod_reserva integer DEFAULT nextval('pmieducar.reservas_cod_reserva_seq'::regclass) NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    data_reserva timestamp without time zone,
    data_prevista_disponivel timestamp without time zone,
    data_retirada timestamp without time zone,
    ref_cod_exemplar integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE TABLE pmieducar.sequencia_serie (
    ref_serie_origem integer NOT NULL,
    ref_serie_destino integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.serie (
    cod_serie integer DEFAULT nextval('pmieducar.serie_cod_serie_seq'::regclass) NOT NULL,
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
    intervalo integer,
    idade_inicial numeric(3,0),
    idade_final numeric(3,0),
    regra_avaliacao_id integer,
    observacao_historico character varying(100),
    dias_letivos integer,
    regra_avaliacao_diferenciada_id integer,
    alerta_faixa_etaria boolean,
    bloquear_matricula_faixa_etaria boolean,
    idade_ideal integer,
    exigir_inep boolean
);


CREATE TABLE pmieducar.serie_pre_requisito (
    ref_cod_pre_requisito integer NOT NULL,
    ref_cod_operador integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    valor character varying
);


SET default_with_oids = false;

CREATE TABLE pmieducar.serie_vaga (
    ano integer NOT NULL,
    cod_serie_vaga integer NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    vagas smallint NOT NULL,
    turno smallint DEFAULT 1 NOT NULL
);


SET default_with_oids = true;

CREATE TABLE pmieducar.servidor (
    cod_servidor integer NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_idesco numeric(2,0),
    carga_horaria double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_subnivel integer,
    situacao_curso_superior_1 smallint,
    formacao_complementacao_pedagogica_1 smallint,
    codigo_curso_superior_1 integer,
    ano_inicio_curso_superior_1 numeric(4,0),
    ano_conclusao_curso_superior_1 numeric(4,0),
    instituicao_curso_superior_1 smallint,
    situacao_curso_superior_2 smallint,
    formacao_complementacao_pedagogica_2 smallint,
    codigo_curso_superior_2 integer,
    ano_inicio_curso_superior_2 numeric(4,0),
    ano_conclusao_curso_superior_2 numeric(4,0),
    instituicao_curso_superior_2 smallint,
    situacao_curso_superior_3 smallint,
    formacao_complementacao_pedagogica_3 smallint,
    codigo_curso_superior_3 integer,
    ano_inicio_curso_superior_3 numeric(4,0),
    ano_conclusao_curso_superior_3 numeric(4,0),
    instituicao_curso_superior_3 smallint,
    multi_seriado boolean,
    pos_graduacao integer[],
    curso_formacao_continuada integer[]
);


CREATE TABLE pmieducar.servidor_afastamento (
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




CREATE TABLE pmieducar.servidor_alocacao (
    cod_servidor_alocacao integer DEFAULT nextval('pmieducar.servidor_alocacao_cod_servidor_alocacao_seq'::regclass) NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    carga_horaria interval,
    periodo smallint DEFAULT (1)::smallint,
    hora_final time without time zone,
    hora_inicial time without time zone,
    dia_semana integer,
    ref_cod_servidor_funcao integer,
    ref_cod_funcionario_vinculo integer,
    ano integer,
    data_admissao date,
    hora_atividade time without time zone,
    horas_excedentes time without time zone
);




CREATE TABLE pmieducar.servidor_curso (
    cod_servidor_curso integer DEFAULT nextval('pmieducar.servidor_curso_cod_servidor_curso_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_conclusao timestamp without time zone NOT NULL,
    data_registro timestamp without time zone,
    diplomas_registros text
);


SET default_with_oids = false;

CREATE TABLE pmieducar.servidor_curso_ministra (
    ref_cod_curso integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL
);


CREATE TABLE pmieducar.servidor_disciplina (
    ref_cod_disciplina integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_cod_curso integer NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.servidor_formacao (
    cod_formacao integer DEFAULT nextval('pmieducar.servidor_formacao_cod_formacao_seq'::regclass) NOT NULL,
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

CREATE TABLE pmieducar.servidor_funcao (
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_cod_funcao integer NOT NULL,
    matricula character varying,
    cod_servidor_funcao integer DEFAULT nextval('pmieducar.servidor_funcao_seq'::regclass) NOT NULL
);




SET default_with_oids = true;

CREATE TABLE pmieducar.servidor_titulo_concurso (
    cod_servidor_titulo integer DEFAULT nextval('pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_vigencia_homolog timestamp without time zone NOT NULL,
    data_publicacao timestamp without time zone NOT NULL
);




CREATE TABLE pmieducar.situacao (
    cod_situacao integer DEFAULT nextval('pmieducar.situacao_cod_situacao_seq'::regclass) NOT NULL,
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




SET default_with_oids = false;

CREATE TABLE pmieducar.subnivel (
    cod_subnivel integer DEFAULT nextval('pmieducar.subnivel_cod_subnivel_seq'::regclass) NOT NULL,
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


CREATE TABLE pmieducar.tipo_autor (
    codigo integer,
    tipo_autor character varying(255)
);




SET default_with_oids = true;

CREATE TABLE pmieducar.tipo_avaliacao (
    cod_tipo_avaliacao integer DEFAULT nextval('pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    conceitual smallint DEFAULT 1,
    ref_cod_instituicao integer NOT NULL
);


CREATE TABLE pmieducar.tipo_avaliacao_valores (
    ref_cod_tipo_avaliacao integer NOT NULL,
    sequencial integer NOT NULL,
    nome character varying(255) NOT NULL,
    valor double precision NOT NULL,
    valor_min double precision NOT NULL,
    valor_max double precision NOT NULL,
    ativo boolean DEFAULT true
);




CREATE TABLE pmieducar.tipo_dispensa (
    cod_tipo_dispensa integer DEFAULT nextval('pmieducar.tipo_dispensa_cod_tipo_dispensa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.tipo_ensino (
    cod_tipo_ensino integer DEFAULT nextval('pmieducar.tipo_ensino_cod_tipo_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    atividade_complementar boolean DEFAULT false
);




CREATE TABLE pmieducar.tipo_ocorrencia_disciplinar (
    cod_tipo_ocorrencia_disciplinar integer DEFAULT nextval('pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    max_ocorrencias integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.tipo_regime (
    cod_tipo_regime integer DEFAULT nextval('pmieducar.tipo_regime_cod_tipo_regime_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);




CREATE TABLE pmieducar.tipo_usuario (
    cod_tipo_usuario integer DEFAULT nextval('pmieducar.tipo_usuario_cod_tipo_usuario_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    nivel integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);




CREATE TABLE pmieducar.transferencia_solicitacao (
    cod_transferencia_solicitacao integer DEFAULT nextval('pmieducar.transferencia_solicitacao_cod_transferencia_solicitacao_seq'::regclass) NOT NULL,
    ref_cod_transferencia_tipo integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_matricula_entrada integer,
    ref_cod_matricula_saida integer NOT NULL,
    observacao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_transferencia timestamp without time zone,
    ref_cod_escola_destino integer,
    escola_destino_externa character varying,
    estado_escola_destino_externa character varying(60),
    municipio_escola_destino_externa character varying(60)
);




CREATE TABLE pmieducar.transferencia_tipo (
    cod_transferencia_tipo integer DEFAULT nextval('pmieducar.transferencia_tipo_cod_transferencia_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer
);




CREATE TABLE pmieducar.turma (
    cod_turma integer DEFAULT nextval('pmieducar.turma_cod_turma_seq'::regclass) NOT NULL,
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
    visivel boolean,
    tipo_boletim integer,
    turma_turno_id integer,
    ano integer,
    tipo_atendimento smallint,
    turma_mais_educacao smallint,
    atividade_complementar_1 integer,
    atividade_complementar_2 integer,
    atividade_complementar_3 integer,
    atividade_complementar_4 integer,
    atividade_complementar_5 integer,
    atividade_complementar_6 integer,
    aee_braille smallint,
    aee_recurso_optico smallint,
    aee_estrategia_desenvolvimento smallint,
    aee_tecnica_mobilidade smallint,
    aee_libras smallint,
    aee_caa smallint,
    aee_curricular smallint,
    aee_soroban smallint,
    aee_informatica smallint,
    aee_lingua_escrita smallint,
    aee_autonomia smallint,
    cod_curso_profissional integer,
    etapa_educacenso smallint,
    ref_cod_disciplina_dispensada integer,
    parecer_1_etapa text,
    parecer_2_etapa text,
    parecer_3_etapa text,
    parecer_4_etapa text,
    nao_informar_educacenso smallint,
    tipo_mediacao_didatico_pedagogico integer,
    dias_semana integer[],
    atividades_complementares integer[],
    atividades_aee integer[],
    tipo_boletim_diferenciado smallint
);


CREATE TABLE pmieducar.turma_modulo (
    ref_cod_turma integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    sequencial integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL,
    dias_letivos integer
);




CREATE TABLE pmieducar.turma_tipo (
    cod_turma_tipo integer DEFAULT nextval('pmieducar.turma_tipo_cod_turma_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    sgl_tipo character varying(15) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer
);




CREATE TABLE pmieducar.turma_turno (
    id integer DEFAULT nextval('pmieducar.turma_turno_id_seq'::regclass) NOT NULL,
    nome character varying(15) NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE TABLE pmieducar.usuario (
    cod_usuario integer NOT NULL,
    ref_cod_instituicao integer,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    ref_cod_tipo_usuario integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE VIEW pmieducar.v_matricula_matricula_turma AS
 SELECT ma.cod_matricula,
    ma.ref_ref_cod_escola AS ref_cod_escola,
    ma.ref_ref_cod_serie AS ref_cod_serie,
    ma.ref_cod_aluno,
    ma.ref_cod_curso,
    mt.ref_cod_turma,
    ma.ano,
    ma.aprovado,
    ma.ultima_matricula,
    ma.modulo,
    mt.sequencial,
    ma.ativo,
    ( SELECT count(0) AS count
           FROM pmieducar.dispensa_disciplina dd
          WHERE (dd.ref_cod_matricula = ma.cod_matricula)) AS qtd_dispensa_disciplina,
    ( SELECT COALESCE((max(n.modulo))::integer, 0) AS "coalesce"
           FROM pmieducar.nota_aluno n
          WHERE ((n.ref_cod_matricula = ma.cod_matricula) AND (n.ativo = 1))) AS maior_modulo_com_nota
   FROM pmieducar.matricula ma,
    pmieducar.matricula_turma mt
  WHERE ((mt.ref_cod_matricula = ma.cod_matricula) AND (mt.ativo = ma.ativo));


CREATE TABLE pmiotopic.funcionario_su (
    ref_ref_cod_pessoa_fj integer NOT NULL
);


CREATE TABLE pmiotopic.grupomoderador (
    ref_ref_cod_pessoa_fj integer NOT NULL,
    ref_cod_grupos integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE TABLE pmiotopic.grupopessoa (
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




CREATE TABLE pmiotopic.grupos (
    cod_grupos integer DEFAULT nextval('pmiotopic.grupos_cod_grupos_seq'::regclass) NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nm_grupo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    atendimento smallint DEFAULT 0 NOT NULL
);


CREATE TABLE pmiotopic.notas (
    sequencial integer NOT NULL,
    ref_idpes integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nota text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);


CREATE TABLE pmiotopic.participante (
    sequencial integer NOT NULL,
    ref_ref_cod_grupos integer NOT NULL,
    ref_ref_idpes integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    data_chegada timestamp without time zone NOT NULL,
    data_saida timestamp without time zone
);




CREATE TABLE pmiotopic.reuniao (
    cod_reuniao integer DEFAULT nextval('pmiotopic.reuniao_cod_reuniao_seq'::regclass) NOT NULL,
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




CREATE TABLE pmiotopic.topico (
    cod_topico integer DEFAULT nextval('pmiotopic.topico_cod_topico_seq'::regclass) NOT NULL,
    ref_idpes_cad integer NOT NULL,
    ref_cod_grupos_cad integer NOT NULL,
    assunto character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_idpes_exc integer,
    ref_cod_grupos_exc integer
);


CREATE TABLE pmiotopic.topicoreuniao (
    ref_cod_topico integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    parecer text,
    finalizado smallint,
    data_parecer timestamp without time zone
);




CREATE TABLE portal.acesso (
    cod_acesso integer DEFAULT nextval('portal.acesso_cod_acesso_seq'::regclass) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    ip_externo character varying(50) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255) DEFAULT ''::character varying NOT NULL,
    cod_pessoa integer DEFAULT 0 NOT NULL,
    obs text,
    sucesso boolean DEFAULT true NOT NULL
);




CREATE TABLE portal.agenda (
    cod_agenda integer DEFAULT nextval('portal.agenda_cod_agenda_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_exc integer,
    ref_ref_cod_pessoa_cad integer NOT NULL,
    nm_agenda character varying NOT NULL,
    publica smallint DEFAULT 0 NOT NULL,
    envia_alerta smallint DEFAULT 0 NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    data_edicao timestamp without time zone,
    ref_ref_cod_pessoa_own integer
);


CREATE TABLE portal.agenda_compromisso (
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




CREATE TABLE portal.agenda_pref (
    cod_comp integer DEFAULT nextval('portal.agenda_pref_cod_comp_seq'::regclass) NOT NULL,
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


CREATE TABLE portal.agenda_responsavel (
    ref_cod_agenda integer NOT NULL,
    ref_ref_cod_pessoa_fj integer NOT NULL,
    principal smallint
);




CREATE TABLE portal.compras_editais_editais (
    cod_compras_editais_editais integer DEFAULT nextval('portal.compras_editais_editais_cod_compras_editais_editais_seq'::regclass) NOT NULL,
    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
    versao integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    motivo_alteracao text,
    visivel smallint DEFAULT 1 NOT NULL
);


CREATE TABLE portal.compras_editais_editais_empresas (
    ref_cod_compras_editais_editais integer DEFAULT 0 NOT NULL,
    ref_cod_compras_editais_empresa integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);




CREATE TABLE portal.compras_editais_empresa (
    cod_compras_editais_empresa integer DEFAULT nextval('portal.compras_editais_empresa_cod_compras_editais_empresa_seq'::regclass) NOT NULL,
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




CREATE TABLE portal.compras_final_pregao (
    cod_compras_final_pregao integer DEFAULT nextval('portal.compras_final_pregao_cod_compras_final_pregao_seq'::regclass) NOT NULL,
    nm_final character varying(255) DEFAULT ''::character varying NOT NULL
);


CREATE TABLE portal.compras_funcionarios (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.compras_licitacoes (
    cod_compras_licitacoes integer DEFAULT nextval('portal.compras_licitacoes_cod_compras_licitacoes_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    ref_cod_compras_modalidade integer DEFAULT 0 NOT NULL,
    numero character varying(30) DEFAULT ''::character varying NOT NULL,
    objeto text NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    cod_licitacao_semasa integer,
    oculto boolean DEFAULT false
);




CREATE TABLE portal.compras_modalidade (
    cod_compras_modalidade integer DEFAULT nextval('portal.compras_modalidade_cod_compras_modalidade_seq'::regclass) NOT NULL,
    nm_modalidade character varying(255) DEFAULT ''::character varying NOT NULL
);




CREATE TABLE portal.compras_pregao_execucao (
    cod_compras_pregao_execucao integer DEFAULT nextval('portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq'::regclass) NOT NULL,
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




CREATE TABLE portal.compras_prestacao_contas (
    cod_compras_prestacao_contas integer DEFAULT nextval('portal.compras_prestacao_contas_cod_compras_prestacao_contas_seq'::regclass) NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    mes integer DEFAULT 0 NOT NULL,
    ano integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.foto_portal (
    cod_foto_portal integer DEFAULT nextval('portal.foto_portal_cod_foto_portal_seq'::regclass) NOT NULL,
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




CREATE TABLE portal.foto_secao (
    cod_foto_secao integer DEFAULT nextval('portal.foto_secao_cod_foto_secao_seq'::regclass) NOT NULL,
    nm_secao character varying(255)
);


CREATE TABLE portal.funcionario (
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
    ip_logado character varying(50),
    data_login timestamp without time zone,
    email character varying(50),
    status_token character varying(50),
    matricula_interna character varying(30),
    receber_novidades smallint,
    atualizou_cadastro smallint
);




CREATE TABLE portal.funcionario_vinculo (
    cod_funcionario_vinculo integer DEFAULT nextval('portal.funcionario_vinculo_cod_funcionario_vinculo_seq'::regclass) NOT NULL,
    nm_vinculo character varying(255) DEFAULT ''::character varying NOT NULL,
    abreviatura character varying(16)
);




CREATE TABLE portal.imagem (
    cod_imagem integer DEFAULT nextval('portal.imagem_cod_imagem_seq'::regclass) NOT NULL,
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




CREATE TABLE portal.imagem_tipo (
    cod_imagem_tipo integer DEFAULT nextval('portal.imagem_tipo_cod_imagem_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(100) NOT NULL
);




CREATE TABLE portal.intranet_segur_permissao_negada (
    cod_intranet_segur_permissao_negada integer DEFAULT nextval('portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer,
    ip_externo character varying(15) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255),
    data_hora timestamp without time zone NOT NULL,
    pagina character varying(255),
    variaveis text
);


CREATE TABLE portal.jor_arquivo (
    ref_cod_jor_edicao integer DEFAULT 0 NOT NULL,
    jor_arquivo smallint DEFAULT (0)::smallint NOT NULL,
    jor_caminho character varying(255) DEFAULT ''::character varying NOT NULL
);




CREATE TABLE portal.jor_edicao (
    cod_jor_edicao integer DEFAULT nextval('portal.jor_edicao_cod_jor_edicao_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    jor_ano_edicao character varying(5) DEFAULT ''::character varying NOT NULL,
    jor_edicao integer DEFAULT 0 NOT NULL,
    jor_dt_inicial date NOT NULL,
    jor_dt_final date,
    jor_extra smallint DEFAULT (0)::smallint
);




CREATE TABLE portal.mailling_email (
    cod_mailling_email integer DEFAULT nextval('portal.mailling_email_cod_mailling_email_seq'::regclass) NOT NULL,
    nm_pessoa character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL
);




CREATE TABLE portal.mailling_email_conteudo (
    cod_mailling_email_conteudo integer DEFAULT nextval('portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    conteudo text NOT NULL,
    nm_remetente character varying(255),
    email_remetente character varying(255),
    assunto character varying(255)
);




CREATE TABLE portal.mailling_fila_envio (
    cod_mailling_fila_envio integer DEFAULT nextval('portal.mailling_fila_envio_cod_mailling_fila_envio_seq'::regclass) NOT NULL,
    ref_cod_mailling_email_conteudo integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_email integer,
    ref_ref_cod_pessoa_fj integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_envio timestamp without time zone
);




CREATE TABLE portal.mailling_grupo (
    cod_mailling_grupo integer DEFAULT nextval('portal.mailling_grupo_cod_mailling_grupo_seq'::regclass) NOT NULL,
    nm_grupo character varying(255) DEFAULT ''::character varying NOT NULL
);


CREATE TABLE portal.mailling_grupo_email (
    ref_cod_mailling_email integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.mailling_historico (
    cod_mailling_historico integer DEFAULT nextval('portal.mailling_historico_cod_mailling_historico_seq'::regclass) NOT NULL,
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);


CREATE TABLE portal.menu_funcionario (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    cadastra smallint DEFAULT (0)::smallint NOT NULL,
    exclui smallint DEFAULT (0)::smallint NOT NULL,
    ref_cod_menu_submenu integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.menu_menu (
    cod_menu_menu integer DEFAULT nextval('portal.menu_menu_cod_menu_menu_seq'::regclass) NOT NULL,
    nm_menu character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    ref_cod_menu_pai integer,
    caminho character varying(255) DEFAULT '#'::character varying,
    ord_menu integer DEFAULT 9999,
    ativo boolean DEFAULT true,
    icon_class character varying(20)
);




CREATE TABLE portal.menu_submenu (
    cod_menu_submenu integer DEFAULT nextval('portal.menu_submenu_cod_menu_submenu_seq'::regclass) NOT NULL,
    ref_cod_menu_menu integer,
    cod_sistema integer,
    nm_submenu character varying(255) DEFAULT ''::character varying NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    title text,
    nivel smallint DEFAULT (3)::smallint NOT NULL
);




CREATE TABLE portal.not_portal (
    cod_not_portal integer DEFAULT nextval('portal.not_portal_cod_not_portal_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    titulo character varying(255),
    descricao text,
    data_noticia timestamp without time zone NOT NULL
);


CREATE TABLE portal.not_portal_tipo (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_not_tipo integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.not_tipo (
    cod_not_tipo integer DEFAULT nextval('portal.not_tipo_cod_not_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(255) DEFAULT ''::character varying NOT NULL
);


CREATE TABLE portal.not_vinc_portal (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    vic_num integer DEFAULT 0 NOT NULL,
    tipo character(1) DEFAULT 'F'::bpchar NOT NULL,
    cod_vinc integer,
    caminho character varying(255),
    nome_arquivo character varying(255)
);




CREATE TABLE portal.notificacao (
    cod_notificacao integer DEFAULT nextval('portal.notificacao_cod_notificacao_seq'::regclass) NOT NULL,
    ref_cod_funcionario integer NOT NULL,
    titulo character varying,
    conteudo text,
    data_hora_ativa timestamp without time zone,
    url character varying,
    visualizacoes smallint DEFAULT 0 NOT NULL
);




CREATE TABLE portal.pessoa_atividade (
    cod_pessoa_atividade integer DEFAULT nextval('portal.pessoa_atividade_cod_pessoa_atividade_seq'::regclass) NOT NULL,
    ref_cod_ramo_atividade integer DEFAULT 0 NOT NULL,
    nm_atividade character varying(255)
);




CREATE TABLE portal.pessoa_fj (
    cod_pessoa_fj integer DEFAULT nextval('portal.pessoa_fj_cod_pessoa_fj_seq'::regclass) NOT NULL,
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


CREATE TABLE portal.pessoa_fj_pessoa_atividade (
    ref_cod_pessoa_atividade integer DEFAULT 0 NOT NULL,
    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);




CREATE TABLE portal.pessoa_ramo_atividade (
    cod_ramo_atividade integer DEFAULT nextval('portal.pessoa_ramo_atividade_cod_ramo_atividade_seq'::regclass) NOT NULL,
    nm_ramo_atividade character varying(255)
);






CREATE TABLE portal.portal_concurso (
    cod_portal_concurso integer DEFAULT nextval('portal.portal_concurso_cod_portal_concurso_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    nm_concurso character varying(255) DEFAULT ''::character varying NOT NULL,
    descricao text,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    tipo_arquivo character(3) DEFAULT ''::bpchar NOT NULL,
    data_hora timestamp without time zone
);




CREATE TABLE portal.sistema (
    cod_sistema integer DEFAULT nextval('portal.sistema_cod_sistema_seq'::regclass) NOT NULL,
    nome character varying(255),
    versao smallint NOT NULL,
    release smallint NOT NULL,
    patch smallint NOT NULL,
    tipo character varying(255)
);


CREATE VIEW portal.v_funcionario AS
 SELECT f.ref_cod_pessoa_fj,
    f.matricula,
    f.matricula_interna,
    f.senha,
    f.ativo,
    f.ramal,
    f.sequencial,
    f.opcao_menu,
    f.ref_cod_setor,
    f.ref_cod_funcionario_vinculo,
    f.tempo_expira_senha,
    f.tempo_expira_conta,
    f.data_troca_senha,
    f.data_reativa_conta,
    f.ref_ref_cod_pessoa_fj,
    f.proibido,
    f.ref_cod_setor_new,
    f.email,
    ( SELECT pessoa.nome
           FROM cadastro.pessoa
          WHERE (pessoa.idpes = (f.ref_cod_pessoa_fj)::numeric)) AS nome
   FROM portal.funcionario f;


CREATE TABLE public.bairro_regiao (
    ref_cod_regiao integer NOT NULL,
    ref_idbai integer NOT NULL
);


SET default_with_oids = false;

CREATE TABLE public.changelog (
    change_number bigint NOT NULL,
    delta_set character varying(10) NOT NULL,
    start_dt timestamp without time zone NOT NULL,
    complete_dt timestamp without time zone,
    applied_by character varying(100) NOT NULL,
    description character varying(500) NOT NULL
);


SET default_with_oids = true;

CREATE TABLE public.distrito (
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    iddis numeric(6,0) DEFAULT nextval(('public.seq_distrito'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    cod_ibge character varying(7),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_distrito_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_distrito_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);


CREATE TABLE public.logradouro_fonetico (
    fonema character varying(30) NOT NULL,
    idlog numeric(8,0) NOT NULL
);


CREATE TABLE public.pais (
    idpais numeric(3,0) NOT NULL,
    nome character varying(60) NOT NULL,
    geom character varying,
    cod_ibge integer
);


SET default_with_oids = false;

CREATE TABLE public.pghero_query_stats (
    id integer NOT NULL,
    database text,
    "user" text,
    query text,
    query_hash bigint,
    total_time double precision,
    calls bigint,
    captured_at timestamp without time zone
);




ALTER SEQUENCE public.pghero_query_stats_id_seq OWNED BY public.pghero_query_stats.id;


CREATE TABLE public.phinxlog (
    version bigint NOT NULL,
    migration_name character varying(100),
    start_time timestamp without time zone,
    end_time timestamp without time zone,
    breakpoint boolean DEFAULT false NOT NULL
);




CREATE TABLE public.portal_banner (
    cod_portal_banner integer DEFAULT nextval('public.portal_banner_cod_portal_banner_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    prioridade integer DEFAULT 0 NOT NULL,
    link character varying(255) DEFAULT ''::character varying NOT NULL,
    lateral_ smallint DEFAULT (1)::smallint NOT NULL
);




SET default_with_oids = true;

CREATE TABLE public.regiao (
    cod_regiao integer DEFAULT nextval('public.regiao_cod_regiao_seq'::regclass) NOT NULL,
    nm_regiao character varying(100)
);














CREATE TABLE public.setor (
    idset integer DEFAULT nextval('public.setor_idset_seq'::regclass) NOT NULL,
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


CREATE TABLE public.setor_bai (
    idsetorbai numeric(6,0) DEFAULT nextval(('public.seq_setor_bai'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL
);


CREATE TABLE public.uf (
    sigla_uf character varying(3) NOT NULL,
    nome character varying(30) NOT NULL,
    geom character varying,
    idpais numeric(3,0),
    cod_ibge numeric(6,0)
);


CREATE TABLE public.vila (
    idvil numeric(4,0) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    nome character varying(50) NOT NULL,
    geom character varying
);


CREATE VIEW relatorio.view_componente_curricular AS
( SELECT escola_serie_disciplina.ref_cod_disciplina AS id,
    turma.cod_turma,
    componente_curricular.nome,
    componente_curricular.abreviatura,
    componente_curricular.ordenamento,
    componente_curricular.area_conhecimento_id,
    escola_serie_disciplina.etapas_especificas,
    escola_serie_disciplina.etapas_utilizadas,
    escola_serie_disciplina.carga_horaria
   FROM (((pmieducar.turma
     JOIN pmieducar.escola_serie_disciplina ON (((escola_serie_disciplina.ref_ref_cod_serie = turma.ref_ref_cod_serie) AND (escola_serie_disciplina.ref_ref_cod_escola = turma.ref_ref_cod_escola) AND (escola_serie_disciplina.ativo = 1) AND (turma.ano = ANY (escola_serie_disciplina.anos_letivos)))))
     JOIN modules.componente_curricular ON (((componente_curricular.id = escola_serie_disciplina.ref_cod_disciplina) AND (( SELECT count(cct.componente_curricular_id) AS count
           FROM modules.componente_curricular_turma cct
          WHERE (cct.turma_id = turma.cod_turma)) = 0))))
     JOIN modules.area_conhecimento ON ((area_conhecimento.id = componente_curricular.area_conhecimento_id)))
  ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome)
UNION ALL
( SELECT componente_curricular_turma.componente_curricular_id AS id,
    componente_curricular_turma.turma_id AS cod_turma,
    componente_curricular.nome,
    componente_curricular.abreviatura,
    componente_curricular.ordenamento,
    componente_curricular.area_conhecimento_id,
    componente_curricular_turma.etapas_especificas,
    componente_curricular_turma.etapas_utilizadas,
    componente_curricular_turma.carga_horaria
   FROM ((modules.componente_curricular_turma
     JOIN modules.componente_curricular ON ((componente_curricular.id = componente_curricular_turma.componente_curricular_id)))
     JOIN modules.area_conhecimento ON ((area_conhecimento.id = componente_curricular.area_conhecimento_id)))
  ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome);


SET default_with_oids = false;

CREATE TABLE serieciasc.aluno_cod_aluno (
    cod_aluno integer NOT NULL,
    cod_ciasc bigint NOT NULL,
    user_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.aluno_uniforme (
    ref_cod_aluno integer NOT NULL,
    data_recebimento timestamp without time zone NOT NULL,
    camiseta character(2),
    quantidade_camiseta integer,
    bermuda character(2),
    quantidade_bermuda integer,
    jaqueta character(2),
    quantidade_jaqueta integer,
    calca character(2),
    quantidade_calca integer,
    meia character(2),
    quantidade_meia integer,
    tenis character(2),
    quantidade_tenis integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_agua (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    poco_artesiano integer DEFAULT 0,
    cisterna integer DEFAULT 0,
    fonte_rio integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_energia (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    gerador_proprio integer DEFAULT 0,
    solar integer DEFAULT 0,
    eolica integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_lingua_indigena (
    ref_cod_escola integer NOT NULL,
    educacao_indigena integer DEFAULT 0,
    lingua_indigena integer DEFAULT 0,
    lingua_portuguesa integer DEFAULT 0,
    materiais_especificos integer DEFAULT 0,
    ue_terra_indigena integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_lixo (
    ref_cod_escola integer NOT NULL,
    coleta integer DEFAULT 0,
    queima integer DEFAULT 0,
    outra_area integer DEFAULT 0,
    recicla integer DEFAULT 0,
    reutiliza integer DEFAULT 0,
    enterra integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_projeto (
    ref_cod_escola integer NOT NULL,
    danca integer DEFAULT 0,
    folclorico integer DEFAULT 0,
    teatral integer DEFAULT 0,
    ambiental integer DEFAULT 0,
    coral integer DEFAULT 0,
    fanfarra integer DEFAULT 0,
    artes_plasticas integer DEFAULT 0,
    integrada integer DEFAULT 0,
    ambiente_alimentacao integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_regulamentacao (
    ref_cod_escola integer NOT NULL,
    regulamentacao integer DEFAULT 1 NOT NULL,
    situacao integer DEFAULT 1 NOT NULL,
    data_criacao date,
    ato_criacao integer DEFAULT 0,
    numero_ato_criacao character varying(20),
    data_ato_criacao date,
    ato_paralizacao integer DEFAULT 0,
    numero_ato_paralizacao character varying(20),
    data_ato_paralizacao date,
    data_extincao date,
    ato_extincao integer DEFAULT 0,
    numero_ato_extincao character varying(20),
    data_ato_extincao date,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


CREATE TABLE serieciasc.escola_sanitario (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    fossa integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


SET default_with_oids = true;

CREATE TABLE urbano.cep_logradouro (
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
    CONSTRAINT ck_cep_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar)))
);


CREATE TABLE urbano.cep_logradouro_bairro (
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
    CONSTRAINT ck_cep_logradouro_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar)))
);


CREATE TABLE urbano.tipo_logradouro (
    idtlog character varying(5) NOT NULL,
    descricao character varying(40) NOT NULL
);


ALTER TABLE ONLY cadastro.codigo_cartorio_inep ALTER COLUMN id SET DEFAULT nextval('cadastro.codigo_cartorio_inep_id_seq'::regclass);


ALTER TABLE ONLY modules.area_conhecimento ALTER COLUMN id SET DEFAULT nextval('modules.area_conhecimento_id_seq'::regclass);


ALTER TABLE ONLY modules.auditoria_geral ALTER COLUMN id SET DEFAULT nextval('modules.auditoria_geral_id_seq'::regclass);


ALTER TABLE ONLY modules.componente_curricular ALTER COLUMN id SET DEFAULT nextval('modules.componente_curricular_id_seq'::regclass);


ALTER TABLE ONLY modules.config_movimento_geral ALTER COLUMN id SET DEFAULT nextval('modules.config_movimento_geral_id_seq'::regclass);


ALTER TABLE ONLY modules.docente_licenciatura ALTER COLUMN id SET DEFAULT nextval('modules.docente_licenciatura_id_seq'::regclass);


ALTER TABLE ONLY modules.educacenso_curso_superior ALTER COLUMN id SET DEFAULT nextval('modules.educacenso_curso_superior_id_seq'::regclass);


ALTER TABLE ONLY modules.educacenso_ies ALTER COLUMN id SET DEFAULT nextval('modules.educacenso_ies_id_seq'::regclass);


ALTER TABLE ONLY modules.falta_aluno ALTER COLUMN id SET DEFAULT nextval('modules.falta_aluno_id_seq'::regclass);


ALTER TABLE ONLY modules.falta_componente_curricular ALTER COLUMN id SET DEFAULT nextval('modules.falta_componente_curricular_id_seq'::regclass);


ALTER TABLE ONLY modules.falta_geral ALTER COLUMN id SET DEFAULT nextval('modules.falta_geral_id_seq'::regclass);


ALTER TABLE ONLY modules.formula_media ALTER COLUMN id SET DEFAULT nextval('modules.formula_media_id_seq'::regclass);


ALTER TABLE ONLY modules.nota_aluno ALTER COLUMN id SET DEFAULT nextval('modules.nota_aluno_id_seq'::regclass);


ALTER TABLE ONLY modules.nota_componente_curricular ALTER COLUMN id SET DEFAULT nextval('modules.nota_componente_curricular_id_seq'::regclass);


ALTER TABLE ONLY modules.parecer_aluno ALTER COLUMN id SET DEFAULT nextval('modules.parecer_aluno_id_seq'::regclass);


ALTER TABLE ONLY modules.parecer_componente_curricular ALTER COLUMN id SET DEFAULT nextval('modules.parecer_componente_curricular_id_seq'::regclass);


ALTER TABLE ONLY modules.parecer_geral ALTER COLUMN id SET DEFAULT nextval('modules.parecer_geral_id_seq'::regclass);


ALTER TABLE ONLY modules.regra_avaliacao ALTER COLUMN id SET DEFAULT nextval('modules.regra_avaliacao_id_seq'::regclass);


ALTER TABLE ONLY modules.tabela_arredondamento ALTER COLUMN id SET DEFAULT nextval('modules.tabela_arredondamento_id_seq'::regclass);


ALTER TABLE ONLY modules.tabela_arredondamento_valor ALTER COLUMN id SET DEFAULT nextval('modules.tabela_arredondamento_valor_id_seq'::regclass);


ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa ALTER COLUMN id SET DEFAULT nextval('pmieducar.auditoria_falta_componente_dispensa_id_seq'::regclass);


ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa ALTER COLUMN id SET DEFAULT nextval('pmieducar.auditoria_nota_dispensa_id_seq'::regclass);


ALTER TABLE ONLY pmieducar.backup ALTER COLUMN id SET DEFAULT nextval('pmieducar.backup_id_seq'::regclass);


ALTER TABLE ONLY pmieducar.categoria_obra ALTER COLUMN id SET DEFAULT nextval('pmieducar.categoria_obra_id_seq'::regclass);


ALTER TABLE ONLY pmieducar.escola_usuario ALTER COLUMN id SET DEFAULT nextval('pmieducar.escola_usuario_id_seq'::regclass);


ALTER TABLE ONLY public.pghero_query_stats ALTER COLUMN id SET DEFAULT nextval('public.pghero_query_stats_id_seq'::regclass);






















































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































ALTER TABLE ONLY acesso.funcao
    ADD CONSTRAINT pk_funcao PRIMARY KEY (idfunc, idsis, idmen);


ALTER TABLE ONLY acesso.grupo
    ADD CONSTRAINT pk_grupo PRIMARY KEY (idgrp);


ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT pk_grupo_funcao PRIMARY KEY (idmen, idsis, idgrp, idfunc);


ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT pk_grupo_menu PRIMARY KEY (idgrp, idsis, idmen);


ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT pk_grupo_operacao PRIMARY KEY (idfunc, idgrp, idsis, idmen, idope);


ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT pk_grupo_sistema PRIMARY KEY (idsis, idgrp);


ALTER TABLE ONLY acesso.historico_senha
    ADD CONSTRAINT pk_historico_senha PRIMARY KEY (login, senha);


ALTER TABLE ONLY acesso.instituicao
    ADD CONSTRAINT pk_instituicao PRIMARY KEY (idins);


ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT pk_menu PRIMARY KEY (idsis, idmen);


ALTER TABLE ONLY acesso.operacao
    ADD CONSTRAINT pk_operacao PRIMARY KEY (idope);


ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT pk_operacao_funcao PRIMARY KEY (idmen, idsis, idfunc, idope);


ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT pk_pessoa_instituicao PRIMARY KEY (idins, idpes);


ALTER TABLE ONLY acesso.sistema
    ADD CONSTRAINT pk_sistema PRIMARY KEY (idsis);


ALTER TABLE ONLY acesso.usuario
    ADD CONSTRAINT pk_usuario PRIMARY KEY (login);


ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT pk_usuario_grupo PRIMARY KEY (idgrp, login);


ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT pk_baixa_guia_produto PRIMARY KEY (idbap);


ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT pk_baixa_guia_remessa PRIMARY KEY (idbai);


ALTER TABLE ONLY alimentos.calendario
    ADD CONSTRAINT pk_calendario PRIMARY KEY (idcad);


ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT pk_cardapio PRIMARY KEY (idcar);


ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT pk_cardapio_faixa_unidade PRIMARY KEY (idfeu, idcar);


ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT pk_cardapio_produto PRIMARY KEY (idcpr);


ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT pk_cardapio_receita PRIMARY KEY (idcar, idrec);


ALTER TABLE ONLY alimentos.cliente
    ADD CONSTRAINT pk_cliente PRIMARY KEY (idcli);


ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT pk_contrato PRIMARY KEY (idcon);


ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT pk_contrato_produto PRIMARY KEY (idcop);


ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT pk_cp_quimico PRIMARY KEY (idcom);


ALTER TABLE ONLY alimentos.evento
    ADD CONSTRAINT pk_evento PRIMARY KEY (ideve);


ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT pk_faixa_composto_quimico PRIMARY KEY (idfcp);


ALTER TABLE ONLY alimentos.faixa_etaria
    ADD CONSTRAINT pk_faixa_etaria PRIMARY KEY (idfae);


ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT pk_fornecedor PRIMARY KEY (idfor);


ALTER TABLE ONLY alimentos.grupo_quimico
    ADD CONSTRAINT pk_grp_quimico PRIMARY KEY (idgrpq);


ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT pk_guia_produto_diario PRIMARY KEY (idguiaprodiario);


ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT pk_guia_remessa PRIMARY KEY (idgui);


ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT pk_guia_remessa_produto PRIMARY KEY (idgup);


ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT pk_log_guia_remessa PRIMARY KEY (idlogguia);


ALTER TABLE ONLY alimentos.medidas_caseiras
    ADD CONSTRAINT pk_medidas_caseiras PRIMARY KEY (idmedcas, idcli);


ALTER TABLE ONLY alimentos.pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);


ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT pk_prod_cp_quimico PRIMARY KEY (idpcq);


ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT pk_produto PRIMARY KEY (idpro);


ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT pk_produto_fornecedor PRIMARY KEY (idprf);


ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT pk_produto_medida_caseira PRIMARY KEY (idpmc);


ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT pk_rec_cp_quimico PRIMARY KEY (idrcq);


ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT pk_rec_prod PRIMARY KEY (idrpr);


ALTER TABLE ONLY alimentos.receita
    ADD CONSTRAINT pk_receita PRIMARY KEY (idrec);


ALTER TABLE ONLY alimentos.tipo_unidade
    ADD CONSTRAINT pk_tipo_unidade PRIMARY KEY (idtip);


ALTER TABLE ONLY alimentos.tipo_produto
    ADD CONSTRAINT pk_tp_produto PRIMARY KEY (idtip);


ALTER TABLE ONLY alimentos.tipo_refeicao
    ADD CONSTRAINT pk_tp_refeicao PRIMARY KEY (idtre);


ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT pk_uni_faixa_etaria PRIMARY KEY (idfeu);


ALTER TABLE ONLY alimentos.unidade_produto
    ADD CONSTRAINT pk_uni_produto PRIMARY KEY (idunp, idcli);


ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT pk_unidade_atendida PRIMARY KEY (iduni);


ALTER TABLE ONLY cadastro.fisica_foto
    ADD CONSTRAINT fisica_foto_pkey PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.fisica_sangue
    ADD CONSTRAINT fisica_sangue_pkey PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.aviso_nome
    ADD CONSTRAINT pk_aviso_nome PRIMARY KEY (idpes, aviso);


ALTER TABLE ONLY cadastro.deficiencia
    ADD CONSTRAINT pk_cadastro_escolaridade PRIMARY KEY (cod_deficiencia);


ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT pk_documento PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT pk_endereco_externo PRIMARY KEY (idpes, tipo);


ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT pk_endereco_pessoa PRIMARY KEY (idpes, tipo);


ALTER TABLE ONLY cadastro.escolaridade
    ADD CONSTRAINT pk_escolaridade PRIMARY KEY (idesco);


ALTER TABLE ONLY cadastro.estado_civil
    ADD CONSTRAINT pk_estado_civil PRIMARY KEY (ideciv);


ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT pk_fisica PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT pk_fisica_cpf PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pk_fisica_deficiencia PRIMARY KEY (ref_idpes, ref_cod_deficiencia);


ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pk_fisica_raca PRIMARY KEY (ref_idpes);


ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT pk_fone_pessoa PRIMARY KEY (idpes, tipo);


ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT pk_funcionario PRIMARY KEY (matricula, idins);


ALTER TABLE ONLY cadastro.codigo_cartorio_inep
    ADD CONSTRAINT pk_id PRIMARY KEY (id);


ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT pk_juridica PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.ocupacao
    ADD CONSTRAINT pk_ocupacao PRIMARY KEY (idocup);


ALTER TABLE ONLY cadastro.orgao_emissor_rg
    ADD CONSTRAINT pk_orgao_emissor_rg PRIMARY KEY (idorg_rg);


ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);


ALTER TABLE ONLY cadastro.pessoa_fonetico
    ADD CONSTRAINT pk_pessoa_fonetico PRIMARY KEY (fonema, idpes);


ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT pk_socio PRIMARY KEY (idpes_juridica, idpes_fisica);


ALTER TABLE ONLY cadastro.raca
    ADD CONSTRAINT raca_pkey PRIMARY KEY (cod_raca);


ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);


ALTER TABLE ONLY consistenciacao.campo_consistenciacao
    ADD CONSTRAINT pk_campo_consistenciacao PRIMARY KEY (idcam);


ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT pk_campo_metadado PRIMARY KEY (id_campo_met);


ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT pk_confrontacao PRIMARY KEY (idcon);


ALTER TABLE ONLY consistenciacao.fonte
    ADD CONSTRAINT pk_fonte PRIMARY KEY (idfon);


ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT pk_historico_campo PRIMARY KEY (idpes, idcam);


ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT pk_inc_pessoa_possivel PRIMARY KEY (idinc, idpes);


ALTER TABLE ONLY consistenciacao.incoerencia
    ADD CONSTRAINT pk_incoerencia PRIMARY KEY (idinc);


ALTER TABLE ONLY consistenciacao.incoerencia_documento
    ADD CONSTRAINT pk_incoerencia_documento PRIMARY KEY (id_inc_doc);


ALTER TABLE ONLY consistenciacao.incoerencia_endereco
    ADD CONSTRAINT pk_incoerencia_endereco PRIMARY KEY (id_inc_end);


ALTER TABLE ONLY consistenciacao.incoerencia_fone
    ADD CONSTRAINT pk_incoerencia_fone PRIMARY KEY (id_inc_fone);


ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT pk_incoerencia_tipo_incoerencia PRIMARY KEY (id_tipo_inc, idinc);


ALTER TABLE ONLY consistenciacao.metadado
    ADD CONSTRAINT pk_metadado PRIMARY KEY (idmet);


ALTER TABLE ONLY consistenciacao.ocorrencia_regra_campo
    ADD CONSTRAINT pk_ocorrencia_regra_campo PRIMARY KEY (idreg, conteudo_padrao);


ALTER TABLE ONLY consistenciacao.regra_campo
    ADD CONSTRAINT pk_regra_campo PRIMARY KEY (idreg);


ALTER TABLE ONLY consistenciacao.temp_cadastro_unificacao_cmf
    ADD CONSTRAINT pk_temp_cadastro_unificacao_cmf PRIMARY KEY (idpes);


ALTER TABLE ONLY consistenciacao.temp_cadastro_unificacao_siam
    ADD CONSTRAINT pk_temp_cadastro_unificacao_siam PRIMARY KEY (idpes);


ALTER TABLE ONLY consistenciacao.tipo_incoerencia
    ADD CONSTRAINT pk_tipo_incoerencia PRIMARY KEY (id_tipo_inc);


ALTER TABLE ONLY modules.area_conhecimento
    ADD CONSTRAINT area_conhecimento_pkey PRIMARY KEY (id, instituicao_id);


ALTER TABLE ONLY modules.auditoria_geral
    ADD CONSTRAINT auditoria_geral_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.calendario_turma
    ADD CONSTRAINT calendario_turma_pk PRIMARY KEY (calendario_ano_letivo_id, ano, mes, dia, turma_id);


ALTER TABLE ONLY modules.config_movimento_geral
    ADD CONSTRAINT cod_config_movimento_geral_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.componente_curricular_ano_escolar
    ADD CONSTRAINT componente_curricular_ano_escolar_pkey PRIMARY KEY (componente_curricular_id, ano_escolar_id);


ALTER TABLE ONLY modules.componente_curricular
    ADD CONSTRAINT componente_curricular_pkey PRIMARY KEY (id, instituicao_id);


ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_pkey PRIMARY KEY (componente_curricular_id, turma_id);


ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_curso_unique UNIQUE (servidor_id, curso_id, ies_id);


ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.educacenso_cod_aluno
    ADD CONSTRAINT educacenso_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_aluno_inep);


ALTER TABLE ONLY modules.educacenso_cod_docente
    ADD CONSTRAINT educacenso_cod_docente_pk PRIMARY KEY (cod_servidor, cod_docente_inep);


ALTER TABLE ONLY modules.educacenso_cod_escola
    ADD CONSTRAINT educacenso_cod_escola_pk PRIMARY KEY (cod_escola, cod_escola_inep);


ALTER TABLE ONLY modules.educacenso_cod_turma
    ADD CONSTRAINT educacenso_cod_turma_pk PRIMARY KEY (cod_turma, cod_turma_inep);


ALTER TABLE ONLY modules.educacenso_curso_superior
    ADD CONSTRAINT educacenso_curso_superior_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.educacenso_ies
    ADD CONSTRAINT educacenso_ies_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_cod_empresa_transporte_escolar_pkey PRIMARY KEY (cod_empresa_transporte_escolar);


ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_pk PRIMARY KEY (etapa_id, curso_id);


ALTER TABLE ONLY modules.etapas_educacenso
    ADD CONSTRAINT etapas_educacenso_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.falta_aluno
    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.falta_componente_curricular
    ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id, etapa);


ALTER TABLE ONLY modules.falta_geral
    ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id, etapa);


ALTER TABLE ONLY modules.ficha_medica_aluno
    ADD CONSTRAINT ficha_medica_cod_aluno_pkey PRIMARY KEY (ref_cod_aluno);


ALTER TABLE ONLY modules.formula_media
    ADD CONSTRAINT formula_media_pkey PRIMARY KEY (id, instituicao_id);


ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT itinerario_transporte_escolar_cod_itinerario_transporte_escolar PRIMARY KEY (cod_itinerario_transporte_escolar);


ALTER TABLE ONLY modules.lingua_indigena_educacenso
    ADD CONSTRAINT lingua_indigena_educacenso_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.media_geral
    ADD CONSTRAINT media_geral_pkey PRIMARY KEY (nota_aluno_id, etapa);


ALTER TABLE ONLY modules.moradia_aluno
    ADD CONSTRAINT moradia_aluno_pkei PRIMARY KEY (ref_cod_aluno);


ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_pkey PRIMARY KEY (cod_motorista);


ALTER TABLE ONLY modules.nota_aluno
    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.nota_componente_curricular_media
    ADD CONSTRAINT nota_componente_curricular_media_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id);


ALTER TABLE ONLY modules.nota_componente_curricular
    ADD CONSTRAINT nota_componente_curricular_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id, etapa);


ALTER TABLE ONLY modules.nota_geral
    ADD CONSTRAINT nota_geral_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.parecer_aluno
    ADD CONSTRAINT parecer_aluno_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.parecer_componente_curricular
    ADD CONSTRAINT parecer_componente_curricular_pkey PRIMARY KEY (parecer_aluno_id, componente_curricular_id, etapa);


ALTER TABLE ONLY modules.parecer_geral
    ADD CONSTRAINT parecer_geral_pkey PRIMARY KEY (parecer_aluno_id, etapa);


ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_cod_pessoa_transporte_pkey PRIMARY KEY (cod_pessoa_transporte);


ALTER TABLE ONLY modules.educacenso_orgao_regional
    ADD CONSTRAINT pk_educacenso_orgao_regional PRIMARY KEY (sigla_uf, codigo);


ALTER TABLE ONLY modules.ponto_transporte_escolar
    ADD CONSTRAINT ponto_transporte_escolar_cod_ponto_transporte_escolar_pkey PRIMARY KEY (cod_ponto_transporte_escolar);


ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_pk PRIMARY KEY (professor_turma_id, componente_curricular_id);


ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_id_pk PRIMARY KEY (id);


ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_pkey PRIMARY KEY (id, instituicao_id);


ALTER TABLE ONLY modules.regra_avaliacao_recuperacao
    ADD CONSTRAINT regra_avaliacao_recuperacao_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_pkey PRIMARY KEY (serie_id, ano_letivo);


ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_cod_rota_transporte_escolar_pkey PRIMARY KEY (cod_rota_transporte_escolar);


ALTER TABLE ONLY modules.tabela_arredondamento
    ADD CONSTRAINT tabela_arredondamento_pkey PRIMARY KEY (id, instituicao_id);


ALTER TABLE ONLY modules.tabela_arredondamento_valor
    ADD CONSTRAINT tabela_arredondamento_valor_pkey PRIMARY KEY (id);


ALTER TABLE ONLY modules.tipo_veiculo
    ADD CONSTRAINT tipo_veiculo_pkey PRIMARY KEY (cod_tipo_veiculo);


ALTER TABLE ONLY modules.transporte_aluno
    ADD CONSTRAINT transporte_aluno_pk PRIMARY KEY (aluno_id);


ALTER TABLE ONLY modules.uniforme_aluno
    ADD CONSTRAINT uniforme_aluno_pkey PRIMARY KEY (ref_cod_aluno);


ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_pkey PRIMARY KEY (cod_veiculo);


ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_pkey PRIMARY KEY (cod_acao_governo_arquivo);


ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_pkey PRIMARY KEY (ref_cod_categoria, ref_cod_acao_governo);


ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_pkey PRIMARY KEY (cod_acao_governo_foto);


ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_foto_portal);


ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_not_portal);


ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_pkey PRIMARY KEY (cod_acao_governo);


ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_setor);


ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (cod_categoria);


ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_pkey PRIMARY KEY (ref_cod_setor);


ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_pkey PRIMARY KEY (cod_acontecimento);


ALTER TABLE ONLY pmicontrolesis.artigo
    ADD CONSTRAINT artigo_pkey PRIMARY KEY (cod_artigo);


ALTER TABLE ONLY pmicontrolesis.foto_evento
    ADD CONSTRAINT foto_evento_pk PRIMARY KEY (cod_foto_evento);


ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_pkey PRIMARY KEY (cod_foto_vinc);


ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_pkey PRIMARY KEY (cod_itinerario);


ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (cod_menu);


ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_pkey PRIMARY KEY (cod_menu_portal);


ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_pkey PRIMARY KEY (cod_portais);


ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_pkey PRIMARY KEY (cod_servicos);


ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_pkey PRIMARY KEY (cod_sistema);


ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_pkey PRIMARY KEY (cod_submenu_portal);


ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_pkey PRIMARY KEY (cod_telefones);


ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT tipo_acontecimento_pkey PRIMARY KEY (cod_tipo_acontecimento);


ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT topo_portal_pkey PRIMARY KEY (cod_topo_portal);


ALTER TABLE ONLY pmicontrolesis.tutormenu
    ADD CONSTRAINT tutormenu_pkey PRIMARY KEY (cod_tutormenu);


ALTER TABLE ONLY pmidrh.diaria_grupo
    ADD CONSTRAINT diaria_grupo_pkey PRIMARY KEY (cod_diaria_grupo);


ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_pkey PRIMARY KEY (cod_diaria);


ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_pkey PRIMARY KEY (cod_diaria_valores);


ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_pkey PRIMARY KEY (cod_setor);


ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_pkey PRIMARY KEY (ref_cod_acervo, ref_cod_acervo_assunto);


ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_pkey PRIMARY KEY (ref_cod_acervo_autor, ref_cod_acervo);


ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_pkey PRIMARY KEY (cod_acervo_assunto);


ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_pkey PRIMARY KEY (cod_acervo_autor);


ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_pkey PRIMARY KEY (cod_acervo_colecao);


ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_pkey PRIMARY KEY (cod_acervo_editora);


ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_pkey PRIMARY KEY (cod_acervo_idioma);


ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_pkey PRIMARY KEY (cod_acervo);


ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_pk PRIMARY KEY (aluno_id, aluno_beneficio_id);


ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_pkey PRIMARY KEY (cod_aluno_beneficio);


ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_pkey PRIMARY KEY (cod_aluno);


ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_idpes_un UNIQUE (ref_idpes);


ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_pkey PRIMARY KEY (ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo);


ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_dispensa_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_pkey PRIMARY KEY (sequencial, ref_cod_servidor, ref_ref_cod_instituicao);


ALTER TABLE ONLY pmieducar.backup
    ADD CONSTRAINT backup_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_pkey PRIMARY KEY (ref_cod_biblioteca, dia);


ALTER TABLE ONLY pmieducar.biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_pkey PRIMARY KEY (cod_feriado);


ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_pkey PRIMARY KEY (cod_biblioteca);


ALTER TABLE ONLY pmieducar.biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_pkey PRIMARY KEY (ref_cod_biblioteca, ref_cod_usuario);


ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_pkey PRIMARY KEY (cod_calendario_ano_letivo);


ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_pkey PRIMARY KEY (cod_calendario_anotacao);


ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_pkey PRIMARY KEY (ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao);


ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_pkey PRIMARY KEY (cod_calendario_dia_motivo);


ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_pkey PRIMARY KEY (ref_cod_calendario_ano_letivo, mes, dia);


ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_pkey PRIMARY KEY (cod_categoria_nivel);


ALTER TABLE ONLY pmieducar.categoria_obra
    ADD CONSTRAINT categoria_obra_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_login_ukey UNIQUE (login);


ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (cod_cliente);


ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_pkey PRIMARY KEY (sequencial, ref_cod_cliente, ref_cod_motivo_suspensao);


ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_pk PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_cliente);


ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_pkey PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_exemplar_tipo);


ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_pkey PRIMARY KEY (cod_cliente_tipo);


ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT cod_candidato_reserva_vaga_pkey PRIMARY KEY (cod_candidato_reserva_vaga);


ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT cod_disciplina_dependencia_pkey PRIMARY KEY (cod_disciplina_dependencia);


ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT cod_dispensa_pkey PRIMARY KEY (cod_dispensa);


ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT cod_serie_vaga_pkey PRIMARY KEY (cod_serie_vaga);


ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT cod_serie_vaga_unique UNIQUE (ano, ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, turno);


ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT cod_servidor_funcao_pkey PRIMARY KEY (cod_servidor_funcao);


ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_pkey PRIMARY KEY (cod_coffebreak_tipo);


ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_pkey PRIMARY KEY (cod_curso);


ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_pkey PRIMARY KEY (cod_disciplina);


ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_pkey PRIMARY KEY (ref_cod_disciplina, ref_cod_serie);


ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_pkey PRIMARY KEY (cod_disciplina_topico);


ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT distribuicao_uniforme_cod_distribuicao_uniforme_pkey PRIMARY KEY (cod_distribuicao_uniforme);


ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_pkey PRIMARY KEY (ref_cod_escola, ano);


ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_pkey PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_pkey PRIMARY KEY (ref_cod_escola, ref_cod_curso);


ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_pkey PRIMARY KEY (cod_escola_localizacao);


ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);


ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_pkey PRIMARY KEY (cod_escola_rede_ensino);


ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_pkey PRIMARY KEY (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina);


ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_pkey PRIMARY KEY (ref_cod_escola, ref_cod_serie);


ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_pkey PRIMARY KEY (cod_emprestimo);


ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_pkey PRIMARY KEY (cod_exemplar);


ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_pkey PRIMARY KEY (cod_exemplar_tipo);


ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (cod_falta_aluno);


ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_pkey PRIMARY KEY (cod_compensado);


ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_pkey PRIMARY KEY (cod_falta_atraso);


ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_pkey PRIMARY KEY (ref_cod_matricula, sequencial);


ALTER TABLE ONLY pmieducar.bloqueio_lancamento_faltas_notas
    ADD CONSTRAINT fk_bloqueio_lancamento_faltas_notas PRIMARY KEY (cod_bloqueio);


ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_pkey PRIMARY KEY (cod_fonte);


ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_pkey PRIMARY KEY (cod_funcao);


ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_pkey PRIMARY KEY (ref_cod_habilitacao, ref_cod_curso);


ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_pkey PRIMARY KEY (cod_habilitacao);


ALTER TABLE ONLY pmieducar.historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_pkey PRIMARY KEY (sequencial, ref_ref_cod_aluno, ref_sequencial);


ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY (ref_cod_aluno, sequencial);


ALTER TABLE ONLY pmieducar.historico_grade_curso
    ADD CONSTRAINT historico_grade_curso_pk PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_pkey PRIMARY KEY (cod_infra_comodo_funcao);


ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_pkey PRIMARY KEY (cod_infra_predio_comodo);


ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_pkey PRIMARY KEY (cod_infra_predio);


ALTER TABLE ONLY pmieducar.instituicao_documentacao
    ADD CONSTRAINT instituicao_documentacao_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_pkey PRIMARY KEY (cod_instituicao);


ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_pkey PRIMARY KEY (cod_material_didatico);


ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_pkey PRIMARY KEY (cod_material_tipo);


ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_pk PRIMARY KEY (cod_aluno_excessao);


ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_tipo_ocorrencia_disciplinar, sequencial);


ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_pkey PRIMARY KEY (cod_matricula);


ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_turma, sequencial);


ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_pkey PRIMARY KEY (ref_cod_tipo_usuario, ref_cod_menu_submenu);


ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_pkey PRIMARY KEY (cod_modulo);


ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_pkey PRIMARY KEY (cod_motivo_afastamento);


ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_pkey PRIMARY KEY (cod_motivo_baixa);


ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_pkey PRIMARY KEY (cod_motivo_suspensao);


ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_pkey PRIMARY KEY (cod_nivel_ensino);


ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_pkey PRIMARY KEY (cod_nivel);


ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (cod_nota_aluno);


ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_pkey PRIMARY KEY (cod_operador);


ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_pkey PRIMARY KEY (cod_pagamento_multa);


ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT pk_cod_abandono_tipo PRIMARY KEY (cod_abandono_tipo);


ALTER TABLE ONLY pmieducar.bloqueio_ano_letivo
    ADD CONSTRAINT pmieducar_bloqueio_ano_letivo_pkey PRIMARY KEY (ref_cod_instituicao, ref_ano);


ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_pk PRIMARY KEY (ref_cod_projeto, ref_cod_aluno);


ALTER TABLE ONLY pmieducar.projeto
    ADD CONSTRAINT pmieducar_projeto_cod_projeto PRIMARY KEY (cod_projeto);


ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_pkey PRIMARY KEY (cod_pre_requisito);


ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_aux_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);


ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);


ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_pkey PRIMARY KEY (cod_quadro_horario);


ALTER TABLE ONLY pmieducar.quantidade_reserva_externa
    ADD CONSTRAINT quantidade_reserva_externa_pkey PRIMARY KEY (ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, ref_turma_turno_id, ano);


ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);


ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_pkey PRIMARY KEY (cod_reserva_vaga);


ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (cod_reserva);


ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_pkey PRIMARY KEY (ref_serie_origem, ref_serie_destino);


ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_pkey PRIMARY KEY (cod_serie);


ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_pkey PRIMARY KEY (ref_cod_pre_requisito, ref_cod_operador, ref_cod_serie);


ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_pkey PRIMARY KEY (ref_cod_servidor, sequencial, ref_ref_cod_instituicao);


ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_pkey PRIMARY KEY (cod_servidor_alocacao);


ALTER TABLE ONLY pmieducar.servidor_curso
    ADD CONSTRAINT servidor_curso_pkey PRIMARY KEY (cod_servidor_curso);


ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_pkey PRIMARY KEY (ref_cod_curso, ref_ref_cod_instituicao, ref_cod_servidor);


ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_pkey PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso);


ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_pkey PRIMARY KEY (cod_formacao);


ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_pkey PRIMARY KEY (cod_servidor, ref_cod_instituicao);


ALTER TABLE ONLY pmieducar.servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_pkey PRIMARY KEY (cod_servidor_titulo);


ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_pkey PRIMARY KEY (cod_situacao);


ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_pkey PRIMARY KEY (cod_subnivel);


ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_pkey PRIMARY KEY (cod_tipo_avaliacao);


ALTER TABLE ONLY pmieducar.tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_pkey PRIMARY KEY (ref_cod_tipo_avaliacao, sequencial);


ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_pkey PRIMARY KEY (cod_tipo_dispensa);


ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_pkey PRIMARY KEY (cod_tipo_ensino);


ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_pkey PRIMARY KEY (cod_tipo_ocorrencia_disciplinar);


ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_pkey PRIMARY KEY (cod_tipo_regime);


ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_pkey PRIMARY KEY (cod_tipo_usuario);


ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_pkey PRIMARY KEY (cod_transferencia_solicitacao);


ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_pkey PRIMARY KEY (cod_transferencia_tipo);


ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_pkey PRIMARY KEY (ref_cod_turma, ref_cod_modulo, sequencial);


ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_pkey PRIMARY KEY (cod_turma);


ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_pkey PRIMARY KEY (cod_turma_tipo);


ALTER TABLE ONLY pmieducar.turma_turno
    ADD CONSTRAINT turma_turno_pkey PRIMARY KEY (id);


ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (cod_usuario);


ALTER TABLE ONLY pmiotopic.funcionario_su
    ADD CONSTRAINT funcionario_su_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj);


ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_grupos);


ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_pkey PRIMARY KEY (ref_idpes, ref_cod_grupos);


ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_pkey PRIMARY KEY (cod_grupos);


ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_pkey PRIMARY KEY (sequencial, ref_idpes);


ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_pkey PRIMARY KEY (sequencial, ref_ref_cod_grupos, ref_ref_idpes, ref_cod_reuniao);


ALTER TABLE ONLY pmiotopic.reuniao
    ADD CONSTRAINT reuniao_pkey PRIMARY KEY (cod_reuniao);


ALTER TABLE ONLY pmiotopic.topico
    ADD CONSTRAINT topico_pkey PRIMARY KEY (cod_topico);


ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_pkey PRIMARY KEY (ref_cod_topico, ref_cod_reuniao);


ALTER TABLE ONLY portal.acesso
    ADD CONSTRAINT acesso_pk PRIMARY KEY (cod_acesso);


ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_pkey PRIMARY KEY (cod_agenda_compromisso, versao, ref_cod_agenda);


ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (cod_agenda);


ALTER TABLE ONLY portal.agenda_pref
    ADD CONSTRAINT agenda_pref_pk PRIMARY KEY (cod_comp);


ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_pkey PRIMARY KEY (ref_cod_agenda, ref_ref_cod_pessoa_fj);


ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_pk PRIMARY KEY (ref_cod_compras_editais_editais, ref_cod_compras_editais_empresa, data_hora);


ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_pk PRIMARY KEY (cod_compras_editais_editais);


ALTER TABLE ONLY portal.compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_pk PRIMARY KEY (cod_compras_editais_empresa);


ALTER TABLE ONLY portal.compras_final_pregao
    ADD CONSTRAINT compras_final_pregao_pk PRIMARY KEY (cod_compras_final_pregao);


ALTER TABLE ONLY portal.compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_pk PRIMARY KEY (ref_ref_cod_pessoa_fj);


ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_pk PRIMARY KEY (cod_compras_licitacoes);


ALTER TABLE ONLY portal.compras_modalidade
    ADD CONSTRAINT compras_modalidade_pk PRIMARY KEY (cod_compras_modalidade);


ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_pk PRIMARY KEY (cod_compras_pregao_execucao);


ALTER TABLE ONLY portal.compras_prestacao_contas
    ADD CONSTRAINT compras_prestacao_contas_pk PRIMARY KEY (cod_compras_prestacao_contas);


ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_pk PRIMARY KEY (cod_foto_portal);


ALTER TABLE ONLY portal.foto_secao
    ADD CONSTRAINT foto_secao_pk PRIMARY KEY (cod_foto_secao);


ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_pk PRIMARY KEY (ref_cod_pessoa_fj);


ALTER TABLE ONLY portal.funcionario_vinculo
    ADD CONSTRAINT funcionario_vinculo_pk PRIMARY KEY (cod_funcionario_vinculo);


ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_pkey PRIMARY KEY (cod_imagem);


ALTER TABLE ONLY portal.imagem_tipo
    ADD CONSTRAINT imagem_tipo_pkey PRIMARY KEY (cod_imagem_tipo);


ALTER TABLE ONLY portal.intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_pk PRIMARY KEY (cod_intranet_segur_permissao_negada);


ALTER TABLE ONLY portal.jor_arquivo
    ADD CONSTRAINT jor_arquivo_pk PRIMARY KEY (ref_cod_jor_edicao, jor_arquivo);


ALTER TABLE ONLY portal.jor_edicao
    ADD CONSTRAINT jor_edicao_pk PRIMARY KEY (cod_jor_edicao);


ALTER TABLE ONLY portal.mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_pk PRIMARY KEY (cod_mailling_email_conteudo);


ALTER TABLE ONLY portal.mailling_email
    ADD CONSTRAINT mailling_email_pk PRIMARY KEY (cod_mailling_email);


ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_pk PRIMARY KEY (cod_mailling_fila_envio);


ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_pk PRIMARY KEY (ref_cod_mailling_email, ref_cod_mailling_grupo);


ALTER TABLE ONLY portal.mailling_grupo
    ADD CONSTRAINT mailling_grupo_pk PRIMARY KEY (cod_mailling_grupo);


ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_pk PRIMARY KEY (cod_mailling_historico);


ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_pk PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_menu_submenu);


ALTER TABLE ONLY portal.menu_menu
    ADD CONSTRAINT menu_menu_pk PRIMARY KEY (cod_menu_menu);


ALTER TABLE ONLY portal.menu_submenu
    ADD CONSTRAINT menu_submenu_pk PRIMARY KEY (cod_menu_submenu);


ALTER TABLE ONLY portal.not_portal
    ADD CONSTRAINT not_portal_pk PRIMARY KEY (cod_not_portal);


ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_pk PRIMARY KEY (ref_cod_not_portal, ref_cod_not_tipo);


ALTER TABLE ONLY portal.not_tipo
    ADD CONSTRAINT not_tipo_pk PRIMARY KEY (cod_not_tipo);


ALTER TABLE ONLY portal.not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_pk PRIMARY KEY (ref_cod_not_portal, vic_num);


ALTER TABLE ONLY portal.pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_pk PRIMARY KEY (cod_pessoa_atividade);


ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_pk PRIMARY KEY (ref_cod_pessoa_atividade, ref_cod_pessoa_fj);


ALTER TABLE ONLY portal.pessoa_fj
    ADD CONSTRAINT pessoa_fj_pk PRIMARY KEY (cod_pessoa_fj);


ALTER TABLE ONLY portal.pessoa_ramo_atividade
    ADD CONSTRAINT pessoa_ramo_atividade_pk PRIMARY KEY (cod_ramo_atividade);


ALTER TABLE ONLY portal.portal_concurso
    ADD CONSTRAINT portal_concurso_pk PRIMARY KEY (cod_portal_concurso);


ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_pkey PRIMARY KEY (ref_cod_regiao, ref_idbai);


ALTER TABLE ONLY public.pghero_query_stats
    ADD CONSTRAINT pghero_query_stats_pkey PRIMARY KEY (id);


ALTER TABLE ONLY public.phinxlog
    ADD CONSTRAINT phinxlog_pkey PRIMARY KEY (version);


ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT pk_bairro PRIMARY KEY (idbai);


ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT pk_distrito PRIMARY KEY (iddis);


ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT pk_logradouro PRIMARY KEY (idlog);


ALTER TABLE ONLY public.logradouro_fonetico
    ADD CONSTRAINT pk_logradouro_fonetico PRIMARY KEY (fonema, idlog);


ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT pk_municipio PRIMARY KEY (idmun);


ALTER TABLE ONLY public.pais
    ADD CONSTRAINT pk_pais PRIMARY KEY (idpais);


ALTER TABLE ONLY public.setor
    ADD CONSTRAINT pk_setor PRIMARY KEY (idset);


ALTER TABLE ONLY public.setor_bai
    ADD CONSTRAINT pk_setorbai PRIMARY KEY (idsetorbai);


ALTER TABLE ONLY public.uf
    ADD CONSTRAINT pk_uf PRIMARY KEY (sigla_uf);


ALTER TABLE ONLY public.vila
    ADD CONSTRAINT pk_vila PRIMARY KEY (idvil);


ALTER TABLE ONLY public.changelog
    ADD CONSTRAINT pkchangelog PRIMARY KEY (change_number, delta_set);


ALTER TABLE ONLY public.regiao
    ADD CONSTRAINT regiao_pkey PRIMARY KEY (cod_regiao);


ALTER TABLE ONLY serieciasc.aluno_uniforme
    ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_pk PRIMARY KEY (ref_cod_aluno, data_recebimento);


ALTER TABLE ONLY serieciasc.aluno_cod_aluno
    ADD CONSTRAINT cod_aluno_serie_ref_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_ciasc);


ALTER TABLE ONLY serieciasc.escola_regulamentacao
    ADD CONSTRAINT educacenso_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_agua
    ADD CONSTRAINT escola_agua_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_energia
    ADD CONSTRAINT escola_energia_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_lingua_indigena
    ADD CONSTRAINT escola_lingua_indigena_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_lixo
    ADD CONSTRAINT escola_lixo_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_projeto
    ADD CONSTRAINT escola_projeto_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY serieciasc.escola_sanitario
    ADD CONSTRAINT escola_sanitario_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);


ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT pk_cep_logradouro PRIMARY KEY (cep, idlog);


ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT pk_cep_logradouro_bairro PRIMARY KEY (idbai, idlog, cep);


ALTER TABLE ONLY urbano.tipo_logradouro
    ADD CONSTRAINT pk_tipo_logradouro PRIMARY KEY (idtlog);


CREATE UNIQUE INDEX un_usuario_idpes ON acesso.usuario USING btree (idpes);


CREATE UNIQUE INDEX un_baixa_guia_remessa ON alimentos.baixa_guia_remessa USING btree (idgui, dt_recebimento);


CREATE UNIQUE INDEX un_cardapio_produto ON alimentos.cardapio_produto USING btree (idcar, idpro);


CREATE UNIQUE INDEX un_cliente ON alimentos.cliente USING btree (idcli, identificacao);


CREATE UNIQUE INDEX un_contrato ON alimentos.contrato USING btree (idcli, codigo, num_aditivo);


CREATE UNIQUE INDEX un_contrato_produto ON alimentos.contrato_produto USING btree (idcon, idpro);


CREATE UNIQUE INDEX un_evento ON alimentos.evento USING btree (idcad, mes, dia);


CREATE UNIQUE INDEX un_faixa_cp_quimico ON alimentos.faixa_composto_quimico USING btree (idcom, idfae);


CREATE UNIQUE INDEX un_fornecedor ON alimentos.fornecedor USING btree (idcli, nome_fantasia);


CREATE UNIQUE INDEX un_fornecedor_unidade_atend ON alimentos.fornecedor_unidade_atendida USING btree (iduni, idfor);


CREATE UNIQUE INDEX un_guia_remessa ON alimentos.guia_remessa USING btree (idcli, ano, sequencial);


CREATE UNIQUE INDEX un_guia_remessa_produto ON alimentos.guia_remessa_produto USING btree (idgui, idpro);


CREATE UNIQUE INDEX un_prod_cp_quimico ON alimentos.produto_composto_quimico USING btree (idpro, idcom);


CREATE UNIQUE INDEX un_produto ON alimentos.produto USING btree (idcli, nome_compra);


CREATE UNIQUE INDEX un_produto_fornecedor ON alimentos.produto_fornecedor USING btree (idfor, idpro);


CREATE UNIQUE INDEX un_produto_medida_caseira ON alimentos.produto_medida_caseira USING btree (idmedcas, idcli, idpro);


CREATE UNIQUE INDEX un_rec_cp_quimico ON alimentos.receita_composto_quimico USING btree (idcom, idrec);


CREATE UNIQUE INDEX un_rec_prod ON alimentos.receita_produto USING btree (idpro, idrec);


CREATE UNIQUE INDEX un_uni_faixa_etaria ON alimentos.unidade_faixa_etaria USING btree (iduni, idfae);


CREATE UNIQUE INDEX un_unidade_atendida ON alimentos.unidade_atendida USING btree (idcli, codigo);


CREATE UNIQUE INDEX un_fisica_cpf ON cadastro.fisica_cpf USING btree (cpf);


CREATE UNIQUE INDEX un_juridica_cnpj ON cadastro.juridica USING btree (cnpj);


CREATE UNIQUE INDEX alunocomponenteetapa ON modules.parecer_componente_curricular USING btree (parecer_aluno_id, componente_curricular_id, etapa);


CREATE INDEX area_conhecimento_nome_key ON modules.area_conhecimento USING btree (nome);


CREATE INDEX componente_curricular_area_conhecimento_key ON modules.componente_curricular USING btree (area_conhecimento_id);


CREATE UNIQUE INDEX componente_curricular_id_key ON modules.componente_curricular USING btree (id);


CREATE INDEX componente_curricular_turma_turma_idx ON modules.componente_curricular_turma USING btree (turma_id);


CREATE INDEX docente_licenciatura_ies_idx ON modules.docente_licenciatura USING btree (ies_id);


CREATE INDEX idx_educacenso_ies_ies_id ON modules.educacenso_ies USING btree (ies_id);


CREATE INDEX idx_falta_aluno_matricula_id ON modules.falta_aluno USING btree (matricula_id);


CREATE INDEX idx_falta_aluno_matricula_id_tipo ON modules.falta_aluno USING btree (matricula_id, tipo_falta);


CREATE INDEX idx_falta_componente_curricular_id1 ON modules.falta_componente_curricular USING btree (falta_aluno_id, componente_curricular_id, etapa);


CREATE INDEX idx_falta_geral_falta_aluno_id ON modules.falta_geral USING btree (falta_aluno_id);


CREATE INDEX idx_nota_aluno_matricula ON modules.nota_aluno USING btree (matricula_id);


CREATE INDEX idx_nota_aluno_matricula_id ON modules.nota_aluno USING btree (id, matricula_id);


CREATE INDEX idx_nota_componente_curricular_etapa ON modules.nota_componente_curricular USING btree (nota_aluno_id, componente_curricular_id, etapa);


CREATE INDEX idx_nota_componente_curricular_etp ON modules.nota_componente_curricular USING btree (componente_curricular_id, etapa);


CREATE INDEX idx_nota_componente_curricular_id ON modules.nota_componente_curricular USING btree (componente_curricular_id);


CREATE INDEX idx_parecer_aluno_matricula_id ON modules.parecer_aluno USING btree (matricula_id);


CREATE INDEX idx_parecer_geral_parecer_aluno_etp ON modules.parecer_geral USING btree (parecer_aluno_id, etapa);


CREATE INDEX idx_tabela_arredondamento_valor_tabela_id ON modules.tabela_arredondamento_valor USING btree (tabela_arredondamento_id);


CREATE UNIQUE INDEX regra_avaliacao_id_key ON modules.regra_avaliacao USING btree (id);


CREATE UNIQUE INDEX tabela_arredondamento_id_key ON modules.tabela_arredondamento USING btree (id);


CREATE INDEX exemplar_tombo_idx ON pmieducar.exemplar USING btree (tombo);


CREATE INDEX fki_biblioteca_usuario_ref_cod_biblioteca_fk ON pmieducar.biblioteca_usuario USING btree (ref_cod_biblioteca);


CREATE INDEX fki_servidor_ref_cod_subnivel ON pmieducar.servidor USING btree (ref_cod_subnivel);


CREATE INDEX fki_servidor_ref_cod_subnivel_ ON pmieducar.servidor USING btree (ref_cod_subnivel);


CREATE INDEX historico_escolar_ano_idx ON pmieducar.historico_escolar USING btree (ano);


CREATE INDEX historico_escolar_ativo_idx ON pmieducar.historico_escolar USING btree (ativo);


CREATE INDEX historico_escolar_nm_serie_idx ON pmieducar.historico_escolar USING btree (nm_serie);


CREATE INDEX i_aluno_ativo ON pmieducar.aluno USING btree (ativo);


CREATE INDEX i_aluno_beneficio_ativo ON pmieducar.aluno_beneficio USING btree (ativo);


CREATE INDEX i_aluno_beneficio_nm_beneficio ON pmieducar.aluno_beneficio USING btree (nm_beneficio);


CREATE INDEX i_aluno_beneficio_ref_usuario_cad ON pmieducar.aluno_beneficio USING btree (ref_usuario_cad);


CREATE INDEX i_aluno_ref_cod_religiao ON pmieducar.aluno USING btree (ref_cod_religiao);


CREATE INDEX i_aluno_ref_idpes ON pmieducar.aluno USING btree (ref_idpes);


CREATE INDEX i_aluno_ref_usuario_cad ON pmieducar.aluno USING btree (ref_usuario_cad);


CREATE INDEX i_calendario_ano_letivo_ano ON pmieducar.calendario_ano_letivo USING btree (ano);


CREATE INDEX i_calendario_ano_letivo_ativo ON pmieducar.calendario_ano_letivo USING btree (ativo);


CREATE INDEX i_calendario_ano_letivo_ref_cod_escola ON pmieducar.calendario_ano_letivo USING btree (ref_cod_escola);


CREATE INDEX i_calendario_ano_letivo_ref_usuario_cad ON pmieducar.calendario_ano_letivo USING btree (ref_usuario_cad);


CREATE INDEX i_calendario_dia_ativo ON pmieducar.calendario_dia USING btree (ativo);


CREATE INDEX i_calendario_dia_dia ON pmieducar.calendario_dia USING btree (dia);


CREATE INDEX i_calendario_dia_mes ON pmieducar.calendario_dia USING btree (mes);


CREATE INDEX i_calendario_dia_motivo_ativo ON pmieducar.calendario_dia_motivo USING btree (ativo);


CREATE INDEX i_calendario_dia_motivo_ref_cod_escola ON pmieducar.calendario_dia_motivo USING btree (ref_cod_escola);


CREATE INDEX i_calendario_dia_motivo_ref_usuario_cad ON pmieducar.calendario_dia_motivo USING btree (ref_usuario_cad);


CREATE INDEX i_calendario_dia_motivo_sigla ON pmieducar.calendario_dia_motivo USING btree (sigla);


CREATE INDEX i_calendario_dia_motivo_tipo ON pmieducar.calendario_dia_motivo USING btree (tipo);


CREATE INDEX i_calendario_dia_ref_cod_calendario_dia_motivo ON pmieducar.calendario_dia USING btree (ref_cod_calendario_dia_motivo);


CREATE INDEX i_calendario_dia_ref_usuario_cad ON pmieducar.calendario_dia USING btree (ref_usuario_cad);


CREATE INDEX i_coffebreak_tipo_ativo ON pmieducar.coffebreak_tipo USING btree (ativo);


CREATE INDEX i_coffebreak_tipo_custo_unitario ON pmieducar.coffebreak_tipo USING btree (custo_unitario);


CREATE INDEX i_coffebreak_tipo_nm_tipo ON pmieducar.coffebreak_tipo USING btree (nm_tipo);


CREATE INDEX i_coffebreak_tipo_ref_usuario_cad ON pmieducar.coffebreak_tipo USING btree (ref_usuario_cad);


CREATE INDEX i_curso_ativo ON pmieducar.curso USING btree (ativo);


CREATE INDEX i_curso_ato_poder_publico ON pmieducar.curso USING btree (ato_poder_publico);


CREATE INDEX i_curso_carga_horaria ON pmieducar.curso USING btree (carga_horaria);


CREATE INDEX i_curso_nm_curso ON pmieducar.curso USING btree (nm_curso);


CREATE INDEX i_curso_objetivo_curso ON pmieducar.curso USING btree (objetivo_curso);


CREATE INDEX i_curso_qtd_etapas ON pmieducar.curso USING btree (qtd_etapas);


CREATE INDEX i_curso_ref_cod_nivel_ensino ON pmieducar.curso USING btree (ref_cod_nivel_ensino);


CREATE INDEX i_curso_ref_cod_tipo_ensino ON pmieducar.curso USING btree (ref_cod_tipo_ensino);


CREATE INDEX i_curso_ref_cod_tipo_regime ON pmieducar.curso USING btree (ref_cod_tipo_regime);


CREATE INDEX i_curso_ref_usuario_cad ON pmieducar.curso USING btree (ref_usuario_cad);


CREATE INDEX i_curso_sgl_curso ON pmieducar.curso USING btree (sgl_curso);


CREATE INDEX i_disciplina_abreviatura ON pmieducar.disciplina USING btree (abreviatura);


CREATE INDEX i_disciplina_apura_falta ON pmieducar.disciplina USING btree (apura_falta);


CREATE INDEX i_disciplina_ativo ON pmieducar.disciplina USING btree (ativo);


CREATE INDEX i_disciplina_carga_horaria ON pmieducar.disciplina USING btree (carga_horaria);


CREATE INDEX i_disciplina_nm_disciplina ON pmieducar.disciplina USING btree (nm_disciplina);


CREATE INDEX i_disciplina_ref_usuario_cad ON pmieducar.disciplina USING btree (ref_usuario_cad);


CREATE INDEX i_disciplina_topico_ativo ON pmieducar.disciplina_topico USING btree (ativo);


CREATE INDEX i_disciplina_topico_nm_topico ON pmieducar.disciplina_topico USING btree (nm_topico);


CREATE INDEX i_disciplina_topico_ref_usuario_cad ON pmieducar.disciplina_topico USING btree (ref_usuario_cad);


CREATE INDEX i_dispensa_disciplina_ref_cod_matricula ON pmieducar.dispensa_disciplina USING btree (ref_cod_matricula);


CREATE INDEX i_escola_ativo ON pmieducar.escola USING btree (ativo);


CREATE INDEX i_escola_complemento_ativo ON pmieducar.escola_complemento USING btree (ativo);


CREATE INDEX i_escola_complemento_bairro ON pmieducar.escola_complemento USING btree (bairro);


CREATE INDEX i_escola_complemento_cep ON pmieducar.escola_complemento USING btree (cep);


CREATE INDEX i_escola_complemento_complemento ON pmieducar.escola_complemento USING btree (complemento);


CREATE INDEX i_escola_complemento_email ON pmieducar.escola_complemento USING btree (email);


CREATE INDEX i_escola_complemento_logradouro ON pmieducar.escola_complemento USING btree (logradouro);


CREATE INDEX i_escola_complemento_municipio ON pmieducar.escola_complemento USING btree (municipio);


CREATE INDEX i_escola_complemento_nm_escola ON pmieducar.escola_complemento USING btree (nm_escola);


CREATE INDEX i_escola_complemento_numero ON pmieducar.escola_complemento USING btree (numero);


CREATE INDEX i_escola_complemento_ref_usuario_cad ON pmieducar.escola_complemento USING btree (ref_usuario_cad);


CREATE INDEX i_escola_curso_ativo ON pmieducar.escola_curso USING btree (ativo);


CREATE INDEX i_escola_curso_ref_usuario_cad ON pmieducar.escola_curso USING btree (ref_usuario_cad);


CREATE INDEX i_escola_localizacao_ativo ON pmieducar.escola_localizacao USING btree (ativo);


CREATE INDEX i_escola_localizacao_nm_localizacao ON pmieducar.escola_localizacao USING btree (nm_localizacao);


CREATE INDEX i_escola_localizacao_ref_usuario_cad ON pmieducar.escola_localizacao USING btree (ref_usuario_cad);


CREATE INDEX i_escola_rede_ensino_ativo ON pmieducar.escola_rede_ensino USING btree (ativo);


CREATE INDEX i_escola_rede_ensino_nm_rede ON pmieducar.escola_rede_ensino USING btree (nm_rede);


CREATE INDEX i_escola_rede_ensino_ref_usuario_cad ON pmieducar.escola_rede_ensino USING btree (ref_usuario_cad);


CREATE INDEX i_escola_ref_cod_escola_rede_ensino ON pmieducar.escola USING btree (ref_cod_escola_rede_ensino);


CREATE INDEX i_escola_ref_cod_instituicao ON pmieducar.escola USING btree (ref_cod_instituicao);


CREATE INDEX i_escola_ref_idpes ON pmieducar.escola USING btree (ref_idpes);


CREATE INDEX i_escola_ref_usuario_cad ON pmieducar.escola USING btree (ref_usuario_cad);


CREATE INDEX i_escola_serie_ensino_ativo ON pmieducar.escola_serie USING btree (ativo);


CREATE INDEX i_escola_serie_hora_final ON pmieducar.escola_serie USING btree (hora_final);


CREATE INDEX i_escola_serie_hora_inicial ON pmieducar.escola_serie USING btree (hora_inicial);


CREATE INDEX i_escola_serie_ref_usuario_cad ON pmieducar.escola_serie USING btree (ref_usuario_cad);


CREATE INDEX i_escola_sigla ON pmieducar.escola USING btree (sigla);


CREATE INDEX i_funcao_abreviatura ON pmieducar.funcao USING btree (abreviatura);


CREATE INDEX i_funcao_ativo ON pmieducar.funcao USING btree (ativo);


CREATE INDEX i_funcao_nm_funcao ON pmieducar.funcao USING btree (nm_funcao);


CREATE INDEX i_funcao_professor ON pmieducar.funcao USING btree (professor);


CREATE INDEX i_funcao_ref_usuario_cad ON pmieducar.funcao USING btree (ref_usuario_cad);


CREATE INDEX i_habilitacao_ativo ON pmieducar.habilitacao USING btree (ativo);


CREATE INDEX i_habilitacao_nm_tipo ON pmieducar.habilitacao USING btree (nm_tipo);


CREATE INDEX i_habilitacao_ref_usuario_cad ON pmieducar.habilitacao USING btree (ref_usuario_cad);


CREATE INDEX i_matricula_turma_ref_cod_turma ON pmieducar.matricula_turma USING btree (ref_cod_turma);


CREATE INDEX i_nota_aluno_ref_cod_matricula ON pmieducar.nota_aluno USING btree (ref_cod_matricula);


CREATE INDEX i_turma_nm_turma ON pmieducar.turma USING btree (nm_turma);


CREATE INDEX idx_historico_disciplinas_id ON pmieducar.historico_disciplinas USING btree (sequencial, ref_ref_cod_aluno, ref_sequencial);


CREATE INDEX idx_historico_disciplinas_id1 ON pmieducar.historico_disciplinas USING btree (ref_ref_cod_aluno, ref_sequencial);


CREATE INDEX idx_historico_escolar_aluno_ativo ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ativo);


CREATE INDEX idx_historico_escolar_id1 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial);


CREATE INDEX idx_historico_escolar_id2 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial, ano);


CREATE INDEX idx_historico_escolar_id3 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ano);


CREATE INDEX idx_matricula_cod_escola_aluno ON pmieducar.matricula USING btree (ref_ref_cod_escola, ref_cod_aluno);


CREATE INDEX idx_serie_cod_regra_avaliacao_id ON pmieducar.serie USING btree (cod_serie, regra_avaliacao_id);


CREATE INDEX idx_serie_regra_avaliacao_id ON pmieducar.serie USING btree (regra_avaliacao_id);


CREATE INDEX matricula_ano_idx ON pmieducar.matricula USING btree (ano);


CREATE INDEX matricula_ativo_idx ON pmieducar.matricula USING btree (ativo);


CREATE INDEX quadro_horario_horarios_busca_horarios_idx ON pmieducar.quadro_horario_horarios USING btree (ref_servidor, ref_cod_instituicao_servidor, dia_semana, hora_inicial, hora_final, ativo);


COMMENT ON INDEX pmieducar.quadro_horario_horarios_busca_horarios_idx IS 'Índice para otimizar a busca por professores na criação de quadro de horários.';


CREATE INDEX servidor_alocacao_busca_horarios_idx ON pmieducar.servidor_alocacao USING btree (ref_ref_cod_instituicao, ref_cod_escola, ativo, periodo, carga_horaria);


CREATE INDEX servidor_idx ON pmieducar.servidor USING btree (cod_servidor, ref_cod_instituicao, ativo);


COMMENT ON INDEX pmieducar.servidor_idx IS 'Índice para otimização de acesso aos campos mais usados para queries na tabela.';


CREATE INDEX mailling_fila_envio_data_envio_idx ON portal.mailling_fila_envio USING btree (data_envio);


CREATE INDEX mailling_fila_envio_ref_cod_mailling_email ON portal.mailling_fila_envio USING btree (ref_cod_mailling_email);


CREATE INDEX mailling_fila_envio_ref_cod_mailling_email_conteudo ON portal.mailling_fila_envio USING btree (ref_cod_mailling_email_conteudo);


CREATE INDEX mailling_fila_envio_ref_cod_mailling_fila_envio ON portal.mailling_fila_envio USING btree (cod_mailling_fila_envio);


CREATE INDEX pghero_query_stats_database_captured_at_idx ON public.pghero_query_stats USING btree (database, captured_at);


CREATE TRIGGER trg_aft_documento AFTER INSERT OR UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_documento();


CREATE TRIGGER trg_aft_documento_historico_campo AFTER INSERT OR UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_documento_historico_campo();


CREATE TRIGGER trg_aft_documento_provisorio AFTER INSERT OR UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_documento_provisorio();


CREATE TRIGGER trg_aft_endereco_externo_historico_campo AFTER INSERT OR UPDATE ON cadastro.endereco_externo FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_endereco_externo_historico_campo();


CREATE TRIGGER trg_aft_endereco_pessoa_historico_campo AFTER INSERT OR UPDATE ON cadastro.endereco_pessoa FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_endereco_pessoa_historico_campo();


CREATE TRIGGER trg_aft_fisica AFTER INSERT OR UPDATE ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_fisica();


CREATE TRIGGER trg_aft_fisica_cpf_provisorio AFTER INSERT OR UPDATE ON cadastro.fisica_cpf FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_fisica_cpf_provisorio();


CREATE TRIGGER trg_aft_fisica_historico_campo AFTER INSERT OR UPDATE ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_fisica_historico_campo();


CREATE TRIGGER trg_aft_fisica_provisorio AFTER INSERT OR UPDATE ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_fisica_provisorio();


CREATE TRIGGER trg_aft_fone_historico_campo AFTER INSERT OR UPDATE ON cadastro.fone_pessoa FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_fone_historico_campo();


CREATE TRIGGER trg_aft_fone_pessoa_historico AFTER DELETE ON cadastro.fone_pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_fone_pessoa();


CREATE TRIGGER trg_aft_funcionario_historico AFTER DELETE ON cadastro.funcionario FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_funcionario();


CREATE TRIGGER trg_aft_ins_endereco_externo AFTER INSERT OR UPDATE ON cadastro.endereco_externo FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_ins_endereco_externo();


CREATE TRIGGER trg_aft_ins_endereco_pessoa AFTER INSERT OR UPDATE ON cadastro.endereco_pessoa FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_ins_endereco_pessoa();


CREATE TRIGGER trg_aft_juridica_historico_campo AFTER INSERT OR UPDATE ON cadastro.juridica FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_juridica_historico_campo();


CREATE TRIGGER trg_aft_pessoa_fonetiza AFTER INSERT OR UPDATE ON cadastro.pessoa FOR EACH ROW EXECUTE PROCEDURE public.fcn_aft_pessoa_fonetiza();


CREATE TRIGGER trg_aft_pessoa_historico_campo AFTER INSERT OR UPDATE ON cadastro.pessoa FOR EACH ROW EXECUTE PROCEDURE consistenciacao.fcn_pessoa_historico_campo();


CREATE TRIGGER trg_bef_documento_historico BEFORE UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_documento();


CREATE TRIGGER trg_bef_endereco_externo_historico BEFORE UPDATE ON cadastro.endereco_externo FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_endereco_externo();


CREATE TRIGGER trg_bef_endereco_pessoa_historico BEFORE UPDATE ON cadastro.endereco_pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_endereco_pessoa();


CREATE TRIGGER trg_bef_fisica_cpf_historico BEFORE UPDATE ON cadastro.fisica_cpf FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_fisica_cpf();


CREATE TRIGGER trg_bef_fisica_historico BEFORE UPDATE ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_fisica();


CREATE TRIGGER trg_bef_fone_pessoa_historico BEFORE UPDATE ON cadastro.fone_pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_fone_pessoa();


CREATE TRIGGER trg_bef_funcionario_historico BEFORE UPDATE ON cadastro.funcionario FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_funcionario();


CREATE TRIGGER trg_bef_ins_fisica BEFORE INSERT ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE public.fcn_bef_ins_fisica();


CREATE TRIGGER trg_bef_ins_juridica BEFORE INSERT ON cadastro.juridica FOR EACH ROW EXECUTE PROCEDURE public.fcn_bef_ins_juridica();


CREATE TRIGGER trg_bef_juridica_historico BEFORE UPDATE ON cadastro.juridica FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_juridica();


CREATE TRIGGER trg_bef_pessoa_fonetiza BEFORE DELETE ON cadastro.pessoa FOR EACH ROW EXECUTE PROCEDURE public.fcn_bef_pessoa_fonetiza();


CREATE TRIGGER trg_bef_pessoa_historico BEFORE UPDATE ON cadastro.pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_pessoa();


CREATE TRIGGER trg_bef_socio_historico BEFORE UPDATE ON cadastro.socio FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_socio();


CREATE TRIGGER trg_delete_documento_historico AFTER DELETE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_documento();


CREATE TRIGGER trg_delete_endereco_externo_historico AFTER DELETE ON cadastro.endereco_externo FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_endereco_externo();


CREATE TRIGGER trg_delete_endereco_pessoa_historico AFTER DELETE ON cadastro.endereco_pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_endereco_pessoa();


CREATE TRIGGER trg_delete_fisica_cpf_historico AFTER DELETE ON cadastro.fisica_cpf FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fisica_cpf();


CREATE TRIGGER trg_delete_fisica_historico AFTER DELETE ON cadastro.fisica FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fisica();


CREATE TRIGGER trg_delete_fone_pessoa_historico AFTER DELETE ON cadastro.fone_pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_fone_pessoa();


CREATE TRIGGER trg_delete_funcionario_historico AFTER DELETE ON cadastro.funcionario FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_funcionario();


CREATE TRIGGER trg_delete_juridica_historico AFTER DELETE ON cadastro.juridica FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_juridica();


CREATE TRIGGER trg_delete_pessoa_historico AFTER DELETE ON cadastro.pessoa FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_pessoa();


CREATE TRIGGER trg_delete_socio_historico AFTER DELETE ON cadastro.socio FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_socio();


CREATE TRIGGER impede_duplicacao_falta_aluno BEFORE INSERT OR UPDATE ON modules.falta_aluno FOR EACH ROW EXECUTE PROCEDURE modules.impede_duplicacao_falta_aluno();


CREATE TRIGGER impede_duplicacao_nota_aluno BEFORE INSERT OR UPDATE ON modules.nota_aluno FOR EACH ROW EXECUTE PROCEDURE modules.impede_duplicacao_nota_aluno();


CREATE TRIGGER impede_duplicacao_parecer_aluno BEFORE INSERT OR UPDATE ON modules.parecer_aluno FOR EACH ROW EXECUTE PROCEDURE modules.impede_duplicacao_parecer_aluno();


CREATE TRIGGER trigger_audita_falta_componente_curricular AFTER INSERT OR DELETE OR UPDATE ON modules.falta_componente_curricular FOR EACH ROW EXECUTE PROCEDURE modules.audita_falta_componente_curricular();


CREATE TRIGGER trigger_audita_falta_geral AFTER INSERT OR DELETE OR UPDATE ON modules.falta_geral FOR EACH ROW EXECUTE PROCEDURE modules.audita_falta_geral();


CREATE TRIGGER trigger_audita_media_geral AFTER INSERT OR DELETE OR UPDATE ON modules.media_geral FOR EACH ROW EXECUTE PROCEDURE modules.audita_media_geral();


CREATE TRIGGER trigger_audita_nota_componente_curricular AFTER INSERT OR DELETE OR UPDATE ON modules.nota_componente_curricular FOR EACH ROW EXECUTE PROCEDURE modules.audita_nota_componente_curricular();


CREATE TRIGGER trigger_audita_nota_componente_curricular_media AFTER INSERT OR DELETE OR UPDATE ON modules.nota_componente_curricular_media FOR EACH ROW EXECUTE PROCEDURE modules.audita_nota_componente_curricular_media();


CREATE TRIGGER trigger_audita_nota_exame AFTER INSERT OR DELETE OR UPDATE ON modules.nota_exame FOR EACH ROW EXECUTE PROCEDURE modules.audita_nota_exame();


CREATE TRIGGER trigger_audita_nota_geral AFTER INSERT OR DELETE OR UPDATE ON modules.nota_geral FOR EACH ROW EXECUTE PROCEDURE modules.audita_nota_geral();


CREATE TRIGGER trigger_audita_parecer_componente_curricular AFTER INSERT OR DELETE OR UPDATE ON modules.parecer_componente_curricular FOR EACH ROW EXECUTE PROCEDURE modules.audita_parecer_componente_curricular();


CREATE TRIGGER trigger_audita_parecer_geral AFTER INSERT OR DELETE OR UPDATE ON modules.parecer_geral FOR EACH ROW EXECUTE PROCEDURE modules.audita_parecer_geral();


CREATE TRIGGER update_componente_curricular_turma_updated_at BEFORE UPDATE ON modules.componente_curricular_turma FOR EACH ROW EXECUTE PROCEDURE public.update_updated_at();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.instituicao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_acervo_assunto FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_acervo_autor FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_assunto FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_autor FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_colecao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_editora FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.acervo_idioma FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.aluno FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.aluno_beneficio FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.ano_letivo_modulo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.avaliacao_desempenho FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.biblioteca FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.biblioteca_dia FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.biblioteca_feriados FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.biblioteca_usuario FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.calendario_ano_letivo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.calendario_anotacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.calendario_dia FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.calendario_dia_anotacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.calendario_dia_motivo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.cliente FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.cliente_suspensao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.cliente_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.cliente_tipo_cliente FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.cliente_tipo_exemplar_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.coffebreak_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.curso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.disciplina FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.disciplina_topico FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_ano_letivo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_complemento FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_curso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_localizacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_rede_ensino FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.escola_serie FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.exemplar FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.exemplar_emprestimo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.exemplar_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.falta_atraso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.falta_atraso_compensado FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.fonte FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.funcao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.habilitacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.habilitacao_curso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.historico_disciplinas FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.historico_escolar FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.infra_comodo_funcao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.infra_predio FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.infra_predio_comodo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.material_didatico FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.material_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.matricula_ocorrencia_disciplinar FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.menu_tipo_usuario FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.modulo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.motivo_afastamento FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.motivo_baixa FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.motivo_suspensao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.nivel_ensino FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.operador FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.pagamento_multa FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.pre_requisito FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.quadro_horario FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.religiao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.reserva_vaga FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.reservas FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.sequencia_serie FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.serie FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.serie_pre_requisito FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor_afastamento FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor_alocacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor_curso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor_formacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.servidor_titulo_concurso FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.situacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_avaliacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_avaliacao_valores FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_dispensa FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_ensino FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_ocorrencia_disciplinar FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_regime FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.tipo_usuario FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.transferencia_solicitacao FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.transferencia_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.turma FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.turma_modulo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.turma_tipo FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER fcn_aft_update AFTER INSERT OR UPDATE ON pmieducar.usuario FOR EACH ROW EXECUTE PROCEDURE pmieducar.fcn_aft_update();


CREATE TRIGGER retira_data_cancel_matricula_trg AFTER UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE public.retira_data_cancel_matricula_fun();


CREATE TRIGGER trigger_audita_matricula AFTER INSERT OR DELETE OR UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE pmieducar.audita_matricula();


CREATE TRIGGER trigger_audita_matricula_turma AFTER INSERT OR DELETE OR UPDATE ON pmieducar.matricula_turma FOR EACH ROW EXECUTE PROCEDURE pmieducar.audita_matricula_turma();


CREATE TRIGGER trigger_updated_at_matricula BEFORE UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula();


CREATE TRIGGER trigger_updated_at_matricula_turma BEFORE UPDATE ON pmieducar.matricula_turma FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula_turma();


CREATE TRIGGER update_escola_serie_disciplina_updated_at BEFORE UPDATE ON pmieducar.escola_serie_disciplina FOR EACH ROW EXECUTE PROCEDURE public.update_updated_at();


CREATE TRIGGER trg_aft_logradouro_fonetiza AFTER INSERT OR UPDATE ON public.logradouro FOR EACH ROW EXECUTE PROCEDURE public.fcn_aft_logradouro_fonetiza();


CREATE TRIGGER trg_bef_bairro_historico BEFORE UPDATE ON public.bairro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_bairro();


CREATE TRIGGER trg_bef_logradouro_fonetiza BEFORE DELETE ON public.logradouro FOR EACH ROW EXECUTE PROCEDURE public.fcn_bef_logradouro_fonetiza();


CREATE TRIGGER trg_bef_logradouro_historico BEFORE UPDATE ON public.logradouro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_logradouro();


CREATE TRIGGER trg_bef_municipio_historico BEFORE UPDATE ON public.municipio FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_municipio();


CREATE TRIGGER trg_delete_bairro_historico AFTER DELETE ON public.bairro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_bairro();


CREATE TRIGGER trg_delete_logradouro_historico AFTER DELETE ON public.logradouro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_logradouro();


CREATE TRIGGER trg_delete_municipio_historico AFTER DELETE ON public.municipio FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_municipio();


CREATE TRIGGER trg_bef_cep_logradouro_bairro_historico BEFORE UPDATE ON urbano.cep_logradouro_bairro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_cep_logradouro_bairro();


CREATE TRIGGER trg_bef_cep_logradouro_historico BEFORE UPDATE ON urbano.cep_logradouro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_grava_historico_cep_logradouro();


CREATE TRIGGER trg_delete_cep_logradouro_bairro_historico AFTER DELETE ON urbano.cep_logradouro_bairro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_cep_logradouro_bairro();


CREATE TRIGGER trg_delete_cep_logradouro_historico AFTER DELETE ON urbano.cep_logradouro FOR EACH ROW EXECUTE PROCEDURE historico.fcn_delete_grava_historico_cep_logradouro();
