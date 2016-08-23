-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE TABLE pmieducar.configuracoes_gerais (ref_cod_instituicao integer NOT NULL, permite_relacionamento_posvendas integer NOT NULL DEFAULT 1);


INSERT INTO pmieducar.configuracoes_gerais
VALUES (1) -- undo

DROP TABLE pmieducar.configuracoes_gerais;