CREATE OR REPLACE FUNCTION pmieducar.unifica_tipos_transferencia() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_tipo RECORD;
  v_cod_tt INTEGER;
  begin

    ALTER TABLE pmieducar.transferencia_tipo ADD COLUMN ref_cod_instituicao INTEGER;

    ALTER TABLE pmieducar.transferencia_tipo ADD CONSTRAINT transferencia_tipo_ref_cod_instituicao
    FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
    ON UPDATE RESTRICT ON DELETE RESTRICT;

    FOR cur_tipo IN (SELECT tt.cod_transferencia_tipo AS id, tt.nm_tipo, escola.ref_cod_instituicao
                      FROM pmieducar.transferencia_tipo tt
                      INNER JOIN pmieducar.escola ON (tt.ref_cod_escola = escola.cod_escola)) LOOP

      v_cod_tt := (SELECT cod_transferencia_tipo FROM transferencia_tipo WHERE to_ascii(nm_tipo) ilike to_ascii(cur_tipo.nm_tipo)
                      AND cod_transferencia_tipo <= cur_tipo.id ORDER BY cod_transferencia_tipo LIMIT 1 );

      IF (v_cod_tt = cur_tipo.id) THEN
        UPDATE pmieducar.transferencia_tipo SET ref_cod_instituicao = cur_tipo.ref_cod_instituicao WHERE cod_transferencia_tipo = cur_tipo.id;
      ELSE
        UPDATE pmieducar.transferencia_solicitacao SET ref_cod_transferencia_tipo = v_cod_tt
                                                   WHERE ref_cod_transferencia_tipo = cur_tipo.id;
        DELETE FROM pmieducar.transferencia_tipo WHERE cod_transferencia_tipo = cur_tipo.id;

      END IF;

    END LOOP;

    ALTER TABLE pmieducar.transferencia_tipo  DROP CONSTRAINT transferencia_tipo_ref_cod_escola;

    ALTER TABLE pmieducar.transferencia_tipo DROP COLUMN ref_cod_escola;

  end;$$;
