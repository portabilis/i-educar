-- Cria função para calcular media anual em Rondo do Pará
-- @author Paula Bonot <bonot@portabilis.com.br>

CREATE OR REPLACE FUNCTION relatorio.media_anual_rondon(matricula_id integer, componente_id integer) RETURNS numeric AS $BODY$
DECLARE
   nota1 numeric;
   nota2 numeric;
   nota3 numeric;
BEGIN
   nota1 := (SELECT COALESCE(
                       (CASE WHEN COALESCE(nota_arredondada::numeric, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                  THEN nota_arredondada
                             ELSE nota_recuperacao
                        END)::numeric, 0)
               FROM modules.nota_componente_curricular ncc
              INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                   AND na.matricula_id = matricula_id)
              WHERE ncc.etapa = 1
                AND ncc.componente_curricular_id = componente_id
              );
   nota2 := (SELECT COALESCE(
                       (CASE WHEN COALESCE(nota_arredondada::numeric, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                  THEN nota_arredondada
                             ELSE nota_recuperacao
                        END)::numeric, 0)
               FROM modules.nota_componente_curricular ncc
              INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                   AND na.matricula_id = matricula_id)
              WHERE ncc.etapa = 2
                AND ncc.componente_curricular_id = componente_id);
   nota3 := (SELECT COALESCE(
                       (CASE WHEN COALESCE(nota_arredondada::numeric, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                  THEN nota_arredondada
                             ELSE nota_recuperacao
                        END)::numeric, 0)
               FROM modules.nota_componente_curricular ncc
              INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                   AND na.matricula_id = matricula_id)
              WHERE ncc.etapa = 3
                AND ncc.componente_curricular_id = componente_id);

RETURN TRUNC((( (nota1 * 3) + (nota2 * 3) + (nota3 * 4) ) / 10),1);
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

ALTER FUNCTION relatorio.media_anual_rondon(integer, integer) OWNER TO ieducar;