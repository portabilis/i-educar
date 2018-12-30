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
