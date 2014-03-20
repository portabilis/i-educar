  -- //

  -- Cria colunas necessárias para atender o registro 10 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
  
  ALTER TABLE pmieducar.escola ADD COLUMN acesso INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN ref_idpes_gestor INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN cargo_gestor INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN condicao INTEGER DEFAULT '2';

  ALTER TABLE pmieducar.escola ADD COLUMN decreto_criacao CHARACTER VARYING(50);

  ALTER TABLE pmieducar.escola ADD COLUMN area_terreno_total CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN area_construida CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN area_disponivel CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN num_pavimentos INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN tipo_piso INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN medidor_energia INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN agua_consumida INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN agua_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_poco_artesiano INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_cacimba_cisterna_poco INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_fonte_rio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_gerador INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_outros INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_fossa INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_coleta_periodica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_queima INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_joga_outra_area INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_recicla INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_enterra INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_outros INTEGER;

  --- nova aba

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_diretoria INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_professores INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_secretaria INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_laboratorio_informatica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_laboratorio_ciencias INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_aee INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_quadra_coberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_quadra_descoberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_cozinha INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_biblioteca INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_leitura INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_parque_infantil INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_bercario INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_fora INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_dentro INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_infantil INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_deficiente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_chuveiro INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_refeitorio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_dispensa INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_aumoxarifado INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_auditorio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_patio_coberto INTEGER;

  -- //@UNDO

  ALTER TABLE pmieducar.escola DROP COLUMN acesso;

  ALTER TABLE pmieducar.escola DROP COLUMN ref_idpes_gestor;
  
  ALTER TABLE pmieducar.escola DROP COLUMN cargo_gestor;

  ALTER TABLE pmieducar.escola DROP COLUMN condicao;

  ALTER TABLE pmieducar.escola DROP COLUMN decreto_criacao;

  ALTER TABLE pmieducar.escola DROP COLUMN area_terreno_total;

  ALTER TABLE pmieducar.escola DROP COLUMN area_construida;

  ALTER TABLE pmieducar.escola DROP COLUMN area_disponivel;

  ALTER TABLE pmieducar.escola DROP COLUMN num_pavimentos;

  ALTER TABLE pmieducar.escola DROP COLUMN tipo_piso;
  
  ALTER TABLE pmieducar.escola DROP COLUMN medidor_energia;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_consumida;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_rede_publica;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_poco_artesiano;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_cacimba_cisterna_poco;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_fonte_rio;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_rede_publica;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_gerador;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_outros;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_rede_publica;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_fossa;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_coleta_periodica;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_queima;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_joga_outra_area;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_recicla;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_enterra;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_outros;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_diretoria;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_professores;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_secretaria;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_laboratorio_informatica;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_laboratorio_ciencias;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_aee;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_quadra_coberta;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_quadra_descoberta;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_cozinha;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_biblioteca;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_leitura;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_parque_infantil;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_bercario;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_fora;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_dentro;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_infantil;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_deficiente;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_chuveiro;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_refeitorio;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_dispensa;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_aumoxarifado;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_auditorio;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_patio_coberto;

  -- //