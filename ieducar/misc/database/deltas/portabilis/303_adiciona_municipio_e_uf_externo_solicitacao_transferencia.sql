-- Adiciona campos de municipio e estado do municipio na tabela de solicitação de transferência
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE pmieducar.transferencia_solicitacao ADD estado_escola_destino_externa varchar(60);

ALTER TABLE pmieducar.transferencia_solicitacao ADD municipio_escola_destino_externa varchar(60);