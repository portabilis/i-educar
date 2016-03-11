-- @author   Caroline Salib <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
 -- Cria function para buscar o telefone da escola

CREATE OR REPLACE FUNCTION relatorio.get_telefone_escola(integer) RETURNS character varying AS $BODY$
SELECT COALESCE(
                  (SELECT min(to_char(fone_pessoa.fone, '99999-9999'))
                   FROM cadastro.fone_pessoa, cadastro.juridica
                   WHERE juridica.idpes = fone_pessoa.idpes
                     AND juridica.idpes =
                       (SELECT idpes
                        FROM cadastro.pessoa
                        INNER JOIN pmieducar.escola ON escola.ref_idpes = pessoa.idpes
                        WHERE cod_escola = $1)),
                  (SELECT min(to_char(telefone, '99999-9999'))
                   FROM pmieducar.escola_complemento
                   WHERE escola_complemento.ref_cod_escola = $1)); $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_telefone_escola(integer) OWNER TO ieducar;

 -- Cria function para buscar o DDD da escola

CREATE OR REPLACE FUNCTION relatorio.get_ddd_escola(integer) RETURNS numeric AS $BODY$
SELECT COALESCE(
                  (SELECT min(fone_pessoa.ddd)
                   FROM cadastro.fone_pessoa, cadastro.juridica
                   WHERE juridica.idpes = fone_pessoa.idpes
                     AND juridica.idpes =
                       (SELECT idpes
                        FROM cadastro.pessoa
                        INNER JOIN pmieducar.escola ON escola.ref_idpes = pessoa.idpes
                        WHERE cod_escola = $1)),
                  (SELECT min(ddd_telefone)
                   FROM pmieducar.escola_complemento
                   WHERE ref_cod_escola = $1)); $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_ddd_escola(integer) OWNER TO ieducar;

 -- Cria view que centraliza os dados da escola

CREATE OR REPLACE VIEW relatorio.view_dados_escola AS
SELECT escola.cod_escola,
       relatorio.get_nome_escola(escola.cod_escola) AS nome,
       pessoa.email,
       COALESCE(endereco_pessoa.cep, endereco_externo.cep) AS cep,
       COALESCE(endereco_pessoa.numero, endereco_externo.numero) AS numero,
       COALESCE(logradouro.nome, endereco_externo.logradouro) AS logradouro,
       COALESCE(bairro.nome, endereco_externo.bairro) AS bairro,
       educacenso_cod_escola.cod_escola_inep AS inep,
       relatorio.get_telefone_escola(escola.cod_escola) AS telefone,
       relatorio.get_ddd_escola(escola.cod_escola) AS telefone_ddd
FROM pmieducar.escola
JOIN cadastro.pessoa ON escola.ref_idpes::numeric = pessoa.idpes
LEFT JOIN modules.educacenso_cod_escola ON educacenso_cod_escola.cod_escola = escola.cod_escola
LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
LEFT JOIN cadastro.endereco_externo ON endereco_externo.idpes = pessoa.idpes
LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
LEFT JOIN public.bairro ON bairro.idbai = endereco_pessoa.idbai;


ALTER TABLE relatorio.view_dados_escola OWNER TO ieducar;

 -- Cria function para retornar o texto da nacionalidade

CREATE OR REPLACE FUNCTION relatorio.get_nacionalidade(integer) RETURNS character varying AS $BODY$
SELECT CASE
           WHEN $1 = 1 THEN 'Brasileiro'
           WHEN $1 = 2 THEN 'Naturalizado Brasileiro'
           WHEN $1 = 3 THEN 'Estrangeiro'
           ELSE 'Brasileiro'
       END; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_nacionalidade(integer) OWNER TO ieducar;

 -- Cria function para retornar o nome da mãe do aluno

CREATE OR REPLACE FUNCTION relatorio.get_mae_aluno(integer) RETURNS character varying AS $BODY$
SELECT coalesce(
                  (SELECT nome
                   FROM cadastro.pessoa
                   WHERE idpes = fisica.idpes_mae), (aluno.nm_mae))
FROM pmieducar.aluno
INNER JOIN cadastro.fisica ON fisica.idpes = aluno.ref_idpes
WHERE aluno.ativo = 1
  AND aluno.cod_aluno = $1; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_mae_aluno(integer) OWNER TO ieducar;

 -- Cria function para retornar o nome do pai do aluno

CREATE OR REPLACE FUNCTION relatorio.get_pai_aluno(integer) RETURNS character varying AS $BODY$
SELECT coalesce(
                  (SELECT nome
                   FROM cadastro.pessoa
                   WHERE idpes = fisica.idpes_pai), (aluno.nm_pai))
FROM pmieducar.aluno
INNER JOIN cadastro.fisica ON fisica.idpes = aluno.ref_idpes
WHERE aluno.ativo = 1
  AND aluno.cod_aluno = $1; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_pai_aluno(integer) OWNER TO ieducar;

 -- Cria function para retornar a situação do histórico escolar abreviado

CREATE OR REPLACE FUNCTION relatorio.get_situacao_historico_abreviado(integer) RETURNS character varying AS $BODY$
SELECT CASE
           WHEN $1 = 1 THEN 'Apr'::character varying
           WHEN $1 = 2 THEN 'Rep'::character varying
           WHEN $1 = 3 THEN 'Cur'::character varying
           WHEN $1 = 4 THEN 'Trs'::character varying
           WHEN $1 = 5 THEN 'Recl'::character varying
           WHEN $1 = 6 THEN 'Aba'::character varying
           WHEN $1 = 12 THEN 'ApDp'::character varying
           WHEN $1 = 13 THEN 'ApCo'::character varying
           WHEN $1 = 14 THEN 'RpFt'::character varying
           ELSE ''::character varying
       END AS situacao; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_situacao_historico_abreviado(integer) OWNER TO ieducar;

 -- Retorna o texto da ultima observação do historico escolar do aluno

CREATE OR REPLACE FUNCTION relatorio.get_ultima_observacao_historico(integer) RETURNS character varying AS $BODY$
SELECT (replace(textcat_all(observacao),'<br>',E'\n'))
FROM pmieducar.historico_escolar she
WHERE she.ativo = 1
  AND she.ref_cod_aluno = $1
  AND she.sequencial =
    (SELECT max(s_he.sequencial)
     FROM pmieducar.historico_escolar s_he
     WHERE s_he.ref_cod_instituicao = she.ref_cod_instituicao
       AND substring(s_he.nm_serie,1,1) = substring(she.nm_serie,1,1)
       AND substring(s_he.nm_curso,1,1) = substring(she.nm_curso,1,1)
       AND s_he.ref_cod_aluno = she.ref_cod_aluno
       AND s_he.ativo = 1); $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_ultima_observacao_historico(integer) OWNER TO ieducar;

 -- Cria function para retornar nome da disciplina do histórico de parauapebas

CREATE OR REPLACE FUNCTION relatorio.get_disciplina_historico_parauapebas(integer, integer) RETURNS character varying AS $BODY$
SELECT cc.nome
FROM historico_disciplinas hd
INNER JOIN modules.componente_curricular cc ON upper(cc.nome) = upper(hd.nm_disciplina)
WHERE hd.ref_ref_cod_aluno = $1
  AND hd.ref_sequencial IN
    (SELECT sshe.sequencial
     FROM pmieducar.historico_escolar sshe
     WHERE sshe.ativo = 1
       AND sshe.ref_cod_aluno = $1
       AND sshe.sequencial =
         (SELECT max(s_sshe.sequencial)
          FROM pmieducar.historico_escolar s_sshe
          WHERE s_sshe.ref_cod_instituicao = sshe.ref_cod_instituicao
            AND substring(s_sshe.nm_serie,1,1) = substring(sshe.nm_serie,1,1)
            AND substring(s_sshe.nm_curso,1,1) = substring(sshe.nm_curso,1,1)
            AND s_sshe.ref_cod_aluno = sshe.ref_cod_aluno
            AND s_sshe.ativo = 1))
GROUP BY cc.ordenamento,
         cc.nome,
         cc.id
ORDER BY cc.ordenamento,
         upper(cc.nome) LIMIT 1
OFFSET $2; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_disciplina_historico_parauapebas(integer, integer) OWNER TO ieducar;

 -- Cria function para retornar a nota do aluno no histórico escolar de parauapebas

CREATE OR REPLACE FUNCTION relatorio.get_nota_historico_parauapebas(integer, integer, integer) RETURNS character varying AS $BODY$
SELECT nota
FROM pmieducar.historico_disciplinas
WHERE ref_ref_cod_aluno = $1
  AND ref_sequencial = $2
  AND nm_disciplina = relatorio.get_disciplina_historico_parauapebas($1,$3) $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_nota_historico_parauapebas(integer, integer, integer) OWNER TO ieducar;

 -- Cria function para retornar a carga_horária do componente no histórico escolar de parauapebas

CREATE OR REPLACE FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer) RETURNS integer AS $BODY$
SELECT ccae.carga_horaria::integer
FROM pmieducar.historico_escolar he
INNER JOIN modules.componente_curricular cc ON (UPPER(cc.nome) = UPPER(relatorio.get_disciplina_historico_parauapebas(he.ref_cod_aluno, $3)))
INNER JOIN modules.componente_curricular_ano_escolar ccae ON (ccae.componente_curricular_id = cc.id)
WHERE he.ref_cod_aluno = $1
  AND he.sequencial = $2
  AND ccae.ano_escolar_id =
    (SELECT s.cod_serie
     FROM pmieducar.serie s
     WHERE s.ativo = 1
       AND relatorio.get_texto_sem_espaco(s.nm_serie) = relatorio.get_texto_sem_espaco(he.nm_serie)
       AND s.ref_cod_curso =
         (SELECT c.cod_curso
          FROM pmieducar.curso c
          WHERE c.ativo = 1
            AND relatorio.get_texto_sem_espaco(c.nm_curso) = relatorio.get_texto_sem_espaco(he.nm_curso) LIMIT 1) LIMIT 1) LIMIT 1; $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer) OWNER TO ieducar;

 -- Cria function para remover espaço em branco em string

CREATE OR REPLACE FUNCTION relatorio.get_texto_sem_espaco(character varying) RETURNS character varying AS $BODY$
SELECT translate(public.fcn_upper(regexp_replace($1,' ','','g')), 'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ', 'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_texto_sem_espaco(character varying) OWNER TO ieducar;

 -- Permissões em tabelas para usuário ieducar
 GRANT ALL PRIVILEGES ON TABLE escola TO ieducar;

 GRANT ALL PRIVILEGES ON TABLE educacenso_cod_escola TO ieducar;