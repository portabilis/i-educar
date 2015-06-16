--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE public.municipio ALTER COLUMN cod_ibge TYPE numeric(7,0);
ALTER TABLE public.distrito ALTER COLUMN cod_ibge TYPE VARCHAR(7);