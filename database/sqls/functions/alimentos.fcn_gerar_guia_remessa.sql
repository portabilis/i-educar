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
