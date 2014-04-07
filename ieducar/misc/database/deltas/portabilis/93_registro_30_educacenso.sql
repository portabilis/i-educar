  -- //

  --
  -- Alterações na parte de servidores referentes ao registro 30 do educacenso 2013
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE OR REPLACE FUNCTION pmieducar.normalizaDeficienciaServidor()
  RETURNS void AS
  $BODY$
  DECLARE 
  cur_servidor RECORD;  
  begin 

    FOR cur_servidor IN (SELECT cod_servidor, ref_cod_deficiencia
                      FROM pmieducar.servidor
                      WHERE ref_cod_deficiencia is not null) LOOP

      
      IF ((SELECT 1 FROM cadastro.fisica_deficiencia fd WHERE fd.ref_idpes = cur_servidor.cod_servidor AND fd.ref_cod_deficiencia = cur_servidor.ref_cod_deficiencia) IS NULL ) THEN
        INSERT INTO cadastro.fisica_deficiencia VALUES (cur_servidor.cod_servidor, cur_servidor.ref_cod_deficiencia);
      END IF;

    END LOOP;
        
  end;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

  SELECT pmieducar.normalizaDeficienciaServidor();  

  ALTER TABLE pmieducar.servidor DROP COLUMN ref_cod_deficiencia;

  ALTER TABLE cadastro.deficiencia ADD COLUMN deficiencia_educacenso SMALLINT;
  
  ALTER TABLE cadastro.raca ADD COLUMN raca_educacenso SMALLINT;


  -- //@UNDO

  DROP FUNCTION pmieducar.normalizaDeficienciaServidor();

  ALTER TABLE cadastro.deficiencia DROP COLUMN deficiencia_educacenso;

  ALTER TABLE cadastro.raca DROP COLUMN raca_educacenso;

  -- //
