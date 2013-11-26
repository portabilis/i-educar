  -- //

  --
  -- Apaga registro da deficiência nenhuma no banco
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  UPDATE pmieducar.servidor SET ref_cod_deficiencia = NULL where ref_cod_deficiencia = 1;
  DELETE FROM cadastro.fisica_deficiencia WHERE ref_cod_deficiencia = 1;
  DELETE FROM cadastro.deficiencia WHERE cod_deficiencia = 1;

  -- //@UNDO

  INSERT INTO cadastro.deficiencia VALUES (1,'Nenhuma');

  -- //