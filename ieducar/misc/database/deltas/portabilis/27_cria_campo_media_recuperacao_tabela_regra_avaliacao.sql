-- //

--
-- Adiciona os campo media_recuperacao na tebala modules.regra_avaliacao.
--
-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE modules.regra_avaliacao ADD COLUMN media_recuperacao numeric(5,3) DEFAULT 0.000;
UPDATE modules.regra_avaliacao SET media_recuperacao = media;

-- //@UNDO

ALTER TABLE modules.regra_avaliacao DROP COLUMN media_recuperacao;

-- //
