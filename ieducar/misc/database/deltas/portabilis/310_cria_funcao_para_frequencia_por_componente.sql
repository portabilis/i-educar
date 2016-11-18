--
-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE OR REPLACE FUNCTION modules.frequencia_por_componente(cod_matricula_id integer, cod_disciplina_id integer, ref_cod_matricula_serie integer)
  RETURNS double precision AS
$BODY$
  DECLARE 
  
  cod_falta_aluno_id integer;
  v_qtd_dias_letivos_serie integer;
  v_total_faltas integer;
  qtde_carga_horaria integer;
  v_hora_falta float;
  
  begin 

    cod_falta_aluno_id := (SELECT id FROM modules.falta_aluno WHERE matricula_id = cod_matricula_id ORDER BY id DESC LIMIT 1);
    
    qtde_carga_horaria := (SELECT carga_horaria :: int
          FROM modules.componente_curricular_ano_escolar
         WHERE componente_curricular_ano_escolar.componente_curricular_id = cod_disciplina_id
           AND componente_curricular_ano_escolar.ano_escolar_id = ref_cod_matricula_serie);
    
    v_total_faltas := (SELECT SUM(quantidade) 
                         FROM falta_componente_curricular 
                        WHERE falta_aluno_id = cod_falta_aluno_id
                          AND componente_curricular_id = cod_disciplina_id);

    v_hora_falta := (SELECT hora_falta FROM pmieducar.curso c 
                 INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
                      WHERE m.cod_matricula = cod_matricula_id);

    RETURN  trunc((100 - ((v_total_faltas * (v_hora_falta*100))/qtde_carga_horaria))::numeric, 2);

  end;$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION modules.frequencia_por_componente(integer, integer, integer)
  OWNER TO ieducar;