CREATE OR REPLACE FUNCTION pmieducar.normalizadeficienciaservidor() RETURNS void
    LANGUAGE plpgsql
    AS $$
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

  end;$$;
