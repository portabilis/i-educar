CREATE OR REPLACE VIEW relatorio.view_dados_aluno AS
SELECT pessoa.idpes,
    fisica.cpf,
    aluno.cod_aluno,
    public.fcn_upper(pessoa.nome::text) AS nome_aluno,
    endereco_pessoa.cep,
    logradouro.nome AS nome_logradouro,
    endereco_pessoa.complemento,
    endereco_pessoa.numero,
    bairro.nome AS nome_bairro,
    municipio.nome AS nome_cidade,
    uf.nome AS nome_estado,
    pais.nome,
    pessoa.email,
    matricula.cod_matricula,
    matricula.ano,
    matricula.ref_ref_cod_escola AS cod_escola,
    relatorio.get_nome_escola(matricula.ref_ref_cod_escola) AS escola_aluno,
    curso.cod_curso,
    curso.nm_curso AS nome_curso,
    serie.cod_serie,
    serie.nm_serie AS nome_serie,
    turma.cod_turma,
    turma.nm_turma AS nome_turma,
    fisica.sexo
FROM pmieducar.matricula
JOIN pmieducar.matricula_turma ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
JOIN pmieducar.turma ON turma.cod_turma = matricula_turma.ref_cod_turma AND turma.ref_ref_cod_escola = matricula.ref_ref_cod_escola AND turma.ref_ref_cod_serie = matricula.ref_ref_cod_serie AND turma.ref_cod_curso = matricula.ref_cod_curso AND turma.ano = matricula.ano
JOIN pmieducar.serie ON serie.cod_serie = matricula.ref_ref_cod_serie
JOIN pmieducar.curso ON curso.cod_curso = matricula.ref_cod_curso
JOIN pmieducar.aluno ON aluno.cod_aluno = matricula.ref_cod_aluno
JOIN cadastro.pessoa ON pessoa.idpes = aluno.ref_idpes::numeric
JOIN cadastro.fisica ON fisica.idpes = pessoa.idpes
LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
LEFT JOIN public.bairro ON bairro.idbai = endereco_pessoa.idbai
LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
LEFT JOIN public.municipio ON municipio.idmun = bairro.idmun
LEFT JOIN public.uf ON uf.sigla_uf::text = municipio.sigla_uf::text
LEFT JOIN public.pais ON pais.idpais = uf.idpais
WHERE matricula_turma.sequencial = (SELECT MAX(submt.sequencial) FROM pmieducar.matricula_turma submt where submt.ref_cod_matricula = matricula_turma.ref_cod_matricula);
