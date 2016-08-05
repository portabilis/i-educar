-- Aumenta tamanho do campo inep da tabela de município
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE public.municipio
ALTER cod_ibge TYPE numeric(20,0);


ALTER TABLE historico.municipio
ALTER cod_ibge TYPE numeric(20,0);