CREATE OR REPLACE FUNCTION relatorio.get_nota_exame(integer, integer) RETURNS character varying
    LANGUAGE sql
AS $_$
(SELECT CASE WHEN nota_componente_curricular.nota_arredondada = '10' THEN '10,0' WHEN char_length(nota_componente_curricular.nota_arredondada) = 1 THEN replace(nota_componente_curricular.nota_arredondada,'.',',') || ',0' ELSE replace(nota_componente_curricular.nota_arredondada,'.',',') END
 FROM modules.nota_componente_curricular, modules.nota_aluno
 WHERE nota_componente_curricular.componente_curricular_id = $1
   AND nota_componente_curricular.etapa = 'Rc'
   AND nota_aluno.id = nota_componente_curricular.nota_aluno_id
   AND nota_aluno.matricula_id = $2); $_$;
