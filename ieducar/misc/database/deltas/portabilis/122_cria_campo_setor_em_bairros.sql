  -- //
  -- Adiciona campo setor aos bairros
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  ALTER TABLE public.bairro ADD COLUMN setor CHARACTER VARYING(255);

  -- //@UNDO

  ALTER TABLE public.bairro DROP COLUMN setor;

  -- //