--
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


alter table pmieducar.servidor_alocacao add column ref_cod_funcionario_vinculo int;


  -- undo

alter table pmieducar.servidor_alocacao drop column ref_cod_funcionario_vinculo;


