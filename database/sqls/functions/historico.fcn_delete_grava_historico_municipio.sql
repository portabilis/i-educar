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
