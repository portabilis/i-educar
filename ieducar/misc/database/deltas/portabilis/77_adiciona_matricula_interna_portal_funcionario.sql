  -- //

  --
  -- Adiciona coluna em portal.funcionario para registro de matrícula interna
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE portal.funcionario ADD COLUMN matricula_interna character varying(30);


  -- //@UNDO

  ALTER TABLE portal.funcionario DROP COLUMN matricula_interna;  

  -- //
