-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE TABLE pmieducar.configuracoes_gerais (ref_cod_instituicao integer NOT NULL, permite_relacionamento_posvendas integer NOT NULL DEFAULT 1,
CONSTRAINT configuracoes_gerais_ref_cod_instituicao_fkey
                                             FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE ON
                                             UPDATE RESTRICT ON
                                             DELETE RESTRICT);

INSERT INTO pmieducar.configuracoes_gerais
VALUES (1); -- código da instituição

 -- undo

DROP TABLE pmieducar.configuracoes_gerais;
