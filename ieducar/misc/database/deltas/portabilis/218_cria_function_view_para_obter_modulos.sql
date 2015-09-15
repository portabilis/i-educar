--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
 -- Cria função que retorna o nome do módulo da turma enviada por parâmetro

CREATE OR REPLACE FUNCTION relatorio.get_nome_modulo(integer) RETURNS character varying AS $BODY$
SELECT MIN(modulo.nm_tipo)
FROM pmieducar.turma
INNER JOIN pmieducar.curso ON (curso.cod_curso = turma.ref_cod_curso)
LEFT JOIN pmieducar.ano_letivo_modulo ON (ano_letivo_modulo.ref_ano = turma.ano
                                          AND ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola
                                          AND curso.padrao_ano_escolar = 1)
LEFT JOIN pmieducar.turma_modulo ON (turma_modulo.ref_cod_turma = turma.cod_turma
                                     AND curso.padrao_ano_escolar = 0)
INNER JOIN pmieducar.modulo ON (CASE
                                    WHEN curso.padrao_ano_escolar = 1 THEN modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
                                    ELSE modulo.cod_modulo = turma_modulo.ref_cod_modulo
                                END)
WHERE turma.cod_turma = $1;$BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_nome_modulo(integer) OWNER TO ieducar;

 -- Cria uma view para obter os módulos da turma ou ano letivo de forma organizada

CREATE OR REPLACE VIEW relatorio.view_modulo AS 
 SELECT DISTINCT turma.cod_turma, modulo_curso.cod_modulo AS cod_modulo_curso, modulo_turma.cod_modulo AS cod_modulo_turma, 
        CASE
            WHEN curso.padrao_ano_escolar = 0 AND modulo_turma.cod_modulo IS NOT NULL THEN modulo_turma.nm_tipo
            ELSE modulo_curso.nm_tipo
        END AS nome, 
        CASE
            WHEN curso.padrao_ano_escolar = 0 AND modulo_turma.cod_modulo IS NOT NULL THEN turma_modulo.sequencial
            ELSE ano_letivo_modulo.sequencial
        END AS sequencial
   FROM pmieducar.turma
   JOIN pmieducar.curso ON curso.cod_curso = turma.ref_cod_curso
   LEFT JOIN pmieducar.ano_letivo_modulo ON ano_letivo_modulo.ref_ano = turma.ano AND ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola
   LEFT JOIN pmieducar.turma_modulo ON turma_modulo.ref_cod_turma = turma.cod_turma
   LEFT JOIN pmieducar.modulo modulo_curso ON modulo_curso.cod_modulo = ano_letivo_modulo.ref_cod_modulo
   LEFT JOIN pmieducar.modulo modulo_turma ON modulo_turma.cod_modulo = turma_modulo.ref_cod_modulo
  ORDER BY turma.cod_turma, modulo_curso.cod_modulo, modulo_turma.cod_modulo, 
CASE
    WHEN curso.padrao_ano_escolar = 0 AND modulo_turma.cod_modulo IS NOT NULL THEN modulo_turma.nm_tipo
    ELSE modulo_curso.nm_tipo
END, 
CASE
    WHEN curso.padrao_ano_escolar = 0 AND modulo_turma.cod_modulo IS NOT NULL THEN turma_modulo.sequencial
    ELSE ano_letivo_modulo.sequencial
END;

ALTER TABLE relatorio.view_modulo
  OWNER TO ieducar;

 -- Cria uma view para obter os componentes curriculares da turma corretamente

CREATE OR REPLACE VIEW relatorio.view_componente_curricular AS 
 SELECT escola_serie_disciplina.ref_cod_disciplina AS id, turma.cod_turma, componente_curricular.nome, componente_curricular.abreviatura
   FROM pmieducar.turma
   JOIN pmieducar.escola_serie_disciplina ON escola_serie_disciplina.ref_ref_cod_serie = turma.ref_ref_cod_serie AND escola_serie_disciplina.ref_ref_cod_escola = turma.ref_ref_cod_escola
   JOIN modules.componente_curricular ON componente_curricular.id = escola_serie_disciplina.ref_cod_disciplina AND (( SELECT count(cct.componente_curricular_id) AS count
   FROM modules.componente_curricular_turma cct
  WHERE cct.turma_id = turma.cod_turma)) = 0
UNION ALL 
 SELECT componente_curricular_turma.componente_curricular_id AS id, componente_curricular_turma.turma_id AS cod_turma, componente_curricular.nome, componente_curricular.abreviatura
   FROM modules.componente_curricular_turma
   JOIN modules.componente_curricular ON componente_curricular.id = componente_curricular_turma.componente_curricular_id;

ALTER TABLE relatorio.view_componente_curricular
  OWNER TO ieducar;

 -- Cria função que retorna a nota do exame

CREATE OR REPLACE FUNCTION relatorio.get_nota_exame(integer, integer) RETURNS character varying AS $BODY$
  (SELECT CASE WHEN nota_componente_curricular.nota_arredondada = 10 THEN '10,0' WHEN char_length(nota_componente_curricular.nota_arredondada) = 1 THEN replace(nota_componente_curricular.nota_arredondada,'.',',') || ',0' ELSE replace(nota_componente_curricular.nota_arredondada,'.',',') END
   FROM modules.nota_componente_curricular, modules.nota_aluno
   WHERE nota_componente_curricular.componente_curricular_id = $1
     AND nota_componente_curricular.etapa = 'Rc'
     AND nota_aluno.id = nota_componente_curricular.nota_aluno_id
     AND nota_aluno.matricula_id = $2); $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_nota_exame(integer, integer) OWNER TO ieducar;

-- Permissões de acesso para as views

GRANT ALL PRIVILEGES ON TABLE turma TO ieducar;
GRANT ALL PRIVILEGES ON TABLE curso TO ieducar;
GRANT ALL PRIVILEGES ON TABLE ano_letivo_modulo TO ieducar;
GRANT ALL PRIVILEGES ON TABLE turma_modulo TO ieducar;
GRANT ALL PRIVILEGES ON TABLE modulo TO ieducar;
GRANT ALL PRIVILEGES ON TABLE escola_serie_disciplina TO ieducar;
GRANT ALL PRIVILEGES ON TABLE componente_curricular TO ieducar;
GRANT ALL PRIVILEGES ON TABLE componente_curricular_turma TO ieducar;

 -- undo

DROP FUNCTION relatorio.get_nome_modulo(integer);


DROP FUNCTION relatorio.get_nota_exame(integer, integer);


DROP VIEW relatorio.view_modulo;


DROP VIEW relatorio.view_componente_curricular;

