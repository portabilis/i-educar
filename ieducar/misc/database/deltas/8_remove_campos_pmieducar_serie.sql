-- //

--
-- Remove os campos media_especial e ultima_nota define da tabela
-- pmieducar.serie.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE pmieducar.serie DROP COLUMN media_especial;
ALTER TABLE pmieducar.serie DROP COLUMN ultima_nota_define;

-- //@UNDO

ALTER TABLE public.teste ADD COLUMN media_especial boolean;
ALTER TABLE public.teste ADD COLUMN ultima_nota_define boolean;

-- //