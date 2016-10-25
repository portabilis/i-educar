-- Cria função para calcular media final em Rondo do Pará
-- @author Paula Bonot <bonot@portabilis.com.br>

CREATE OR REPLACE FUNCTION relatorio.media_final_rondon(matricula_id integer, componente_id integer) RETURNS numeric AS $BODY$
DECLARE
   nota1 numeric;
   nota2 numeric;
   nota3 numeric;
   existe_nota_exame boolean;
   nota_exame numeric;
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

   nota_exame := (SELECT COALESCE(
                       (CASE WHEN COALESCE(nota_arredondada::numeric, 0) >= COALESCE(nota_recuperacao::numeric,0)
                                  THEN nota_arredondada
                             ELSE nota_recuperacao
                        END)::numeric, 0)
               FROM modules.nota_componente_curricular ncc
              INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                   AND na.matricula_id = matricula_id)
              WHERE ncc.etapa = 'Rc'
                AND ncc.componente_curricular_id = componente_id);

   existe_nota_exame := EXISTS(SELECT 1
                                         FROM modules.nota_componente_curricular ncc
                                         INNER JOIN modules.nota_aluno na ON (na.id = ncc.nota_aluno_id
                                                                         AND na.matricula_id = matricula_id)
                                         WHERE ncc.etapa = 'Rc'
                                           AND componente_curricular_id = componente_id);
   IF existe_nota_exame = TRUE THEN
       IF (nota3 <= nota2) AND (nota3 <= nota1) AND (nota3 <= nota_exame) THEN
           RETURN TRUNC((((nota1 * 3) + (nota2 * 3) + (nota_exame * 4))/10), 1);
       ELSIF (nota2 <= nota1) AND (nota2 < nota3) AND (nota2 <= nota_exame) THEN
           RETURN TRUNC((((nota1 * 3) + (nota_exame * 3) + (nota3 * 4))/10),1);
       ELSIF (nota1 < nota2) AND (nota1 < nota3) AND (nota1 <= nota_exame) THEN
           RETURN TRUNC((((nota_exame * 3) + (nota2 * 3) + (nota3 * 4))/10),1);
       END IF;
   END IF;

   RETURN relatorio.media_anual_rondon(matricula_id, componente_id);
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

ALTER FUNCTION relatorio.media_final_rondon(integer, integer) OWNER TO ieducar;