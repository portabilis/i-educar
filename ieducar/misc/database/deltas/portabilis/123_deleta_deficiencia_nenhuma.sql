  -- //
  -- Esta migração deleta a deficiência nenhuma
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  DELETE FROM cadastro.fisica_deficiencia WHERE ref_cod_deficiencia IN (SELECT cod_deficiencia FROM cadastro.deficiencia WHERE nm_deficiencia ILIKE 'Nenhuma');  
  DELETE FROM cadastro.deficiencia WHERE nm_deficiencia ILIKE 'Nenhuma';

  -- //