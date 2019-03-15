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
