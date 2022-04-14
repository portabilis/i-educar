CREATE OR REPLACE FUNCTION public.retira_data_cancel_matricula_fun() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN

  UPDATE pmieducar.matricula
  SET    data_cancel = NULL
  WHERE  cod_matricula = new.cod_matricula
  AND    data_cancel IS DISTINCT FROM NULL
  AND    aprovado = 3
  AND (SELECT 1 FROM pmieducar.transferencia_solicitacao WHERE ativo = 1 AND ref_cod_matricula_saida = new.cod_matricula limit 1) is null;

  RETURN NULL;
  END
  $$;
