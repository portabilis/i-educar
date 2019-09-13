CREATE OR REPLACE VIEW pmieducar.v_matricula_matricula_turma AS
 SELECT ma.cod_matricula,
    ma.ref_ref_cod_escola AS ref_cod_escola,
    ma.ref_ref_cod_serie AS ref_cod_serie,
    ma.ref_cod_aluno,
    ma.ref_cod_curso,
    mt.ref_cod_turma,
    ma.ano,
    ma.aprovado,
    ma.ultima_matricula,
    ma.modulo,
    mt.sequencial,
    ma.ativo,
    ( SELECT count(0) AS count
           FROM pmieducar.dispensa_disciplina dd
          WHERE (dd.ref_cod_matricula = ma.cod_matricula)) AS qtd_dispensa_disciplina,
    ( SELECT COALESCE((max(n.modulo))::integer, 0) AS "coalesce"
           FROM pmieducar.nota_aluno n
          WHERE ((n.ref_cod_matricula = ma.cod_matricula) AND (n.ativo = 1))) AS maior_modulo_com_nota
   FROM pmieducar.matricula ma,
    pmieducar.matricula_turma mt
  WHERE ((mt.ref_cod_matricula = ma.cod_matricula) AND (mt.ativo = ma.ativo));
