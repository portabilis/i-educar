  --
  -- Cria função para retornar data por extenso
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

CREATE OR REPLACE FUNCTION public.data_para_extenso(data date)
  RETURNS character varying AS
$BODY$
DECLARE
	data_extenso varchar := '';
	mes_extenso varchar := '';
	dia integer := 0;
	mes integer := 0;
	ano integer := 0;
BEGIN

	dia := date_part('day', data)::integer;
	mes := date_part('month', data)::integer;
	ano := date_part('year', data)::integer;

	mes_extenso := case
				    when mes = 1  then 'Janeiro'
				    when mes = 2  then 'Fevereiro'
				    when mes = 3  then 'Março'
				    when mes = 4  then 'Abril'
				    when mes = 5  then 'Maio'
				    when mes = 6  then 'Junho'
				    when mes = 7  then 'Julho'
				    when mes = 8  then 'Agosto'
				    when mes = 9  then 'Setembro'
				    when mes = 10 then 'Outubro'
				    when mes = 11 then 'Novembro'
				    when mes = 12 then 'Dezembro'
				   else
				   	''
				   end;

	data_extenso := dia::varchar || ' de ' || mes_extenso || ' de ' || ano::varchar;

	return data_extenso;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION public.data_para_extenso(date)
  OWNER TO ieducar;

  -- //@UNDO
  DROP FUNCTION public.data_para_extenso(date);
  -- //