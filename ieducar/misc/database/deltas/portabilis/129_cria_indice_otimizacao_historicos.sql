  -- 
  -- Cria índice para otimizar históricos escolares
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Samuel Brognoli <Samuel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  create index idx_historico_escolar_aluno_ativo on pmieducar.historico_escolar(ref_cod_aluno,ativo);

  -- //