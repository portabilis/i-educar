  -- //

  --
  -- Migração que adiciona colunas para cadastro de informações de endereço ao ponto do transporte escolar
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN cep numeric(8,0);
  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN idlog numeric(6,0);
  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN idbai numeric(6,0);
  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN numero numeric(6,0);
  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN complemento character varying(20);

  ALTER TABLE modules.ponto_transporte_escolar ADD CONSTRAINT fk_ponto_cep_log_bai FOREIGN KEY (idbai, idlog, cep)
    REFERENCES urbano.cep_logradouro_bairro (idbai, idlog, cep) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

  -- //@UNDO

  ALTER TABLE modules.ponto_transporte_escolar DROP CONSTRAINT fk_ponto_cep_log_bai;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN;

  -- //