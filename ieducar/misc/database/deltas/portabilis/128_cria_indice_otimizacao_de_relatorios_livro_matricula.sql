  -- 
  -- Inserir permissões para
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Samuel Brognoli <Samuel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  create index idx_matricula_cod_escola_aluno on pmieducar.matricula(ref_ref_cod_escola, ref_cod_aluno);

  -- //