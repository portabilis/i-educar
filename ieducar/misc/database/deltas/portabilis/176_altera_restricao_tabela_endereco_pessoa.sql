--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE cadastro.endereco_pessoa DROP CONSTRAINT fk_endereco_pes_cep_log_bai;

ALTER TABLE cadastro.endereco_pessoa ADD CONSTRAINT fk_endereco_pes_cep_log_bai FOREIGN KEY (cep, idbai, idlog)
REFERENCES urbano.cep_logradouro_bairro (cep, idbai, idlog) ON UPDATE CASCADE ON DELETE NO ACTION;
