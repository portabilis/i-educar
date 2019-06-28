CREATE OR REPLACE FUNCTION public.verifica_existe_matricula_posterior_mesma_turma(cod_matricula integer, cod_turma integer, sequencial integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$

      DECLARE existe_matricula boolean;

      BEGIN
        existe_matricula := EXISTS (SELECT *
                                      FROM pmieducar.matricula_turma mt
                                     INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
                                     INNER JOIN pmieducar.matricula m2 ON (m2.cod_matricula = cod_matricula)
                                     INNER JOIN pmieducar.matricula_turma mt2 ON (mt2.ref_cod_matricula = cod_matricula
                                                                                  AND mt2.ref_cod_turma = cod_turma
                                                                                  AND mt2.sequencial = sequencial)
                                     WHERE mt.ref_cod_turma = mt2.ref_cod_turma
                                       AND mt.ref_cod_matricula <> mt2.ref_cod_matricula
                                       AND m.ref_cod_aluno = m2.ref_cod_aluno
                                       AND mt.data_enturmacao > mt2.data_enturmacao);

        RETURN existe_matricula;
      END;

      $$;
