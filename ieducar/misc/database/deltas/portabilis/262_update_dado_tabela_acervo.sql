-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

-- Altera campos volume e num_edicao para NULL, para reproduzir erro de campo vazio no relatório
UPDATE acervo set volume = NULL,num_edicao = NULL WHERE titulo = 'BRANCA DE NEVE'