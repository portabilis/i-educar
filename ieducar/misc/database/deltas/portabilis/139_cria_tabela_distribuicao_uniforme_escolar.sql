  -- 
  -- Cria tabela para registrar dados de uniforme escolar
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

	CREATE SEQUENCE pmieducar.distribuicao_uniforme_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;

	CREATE TABLE pmieducar.distribuicao_uniforme
	(
	cod_distribuicao_uniforme BOOLEAN NOT NULL DEFAULT nextval('pmieducar.distribuicao_uniforme_seq'::regclass),
	ref_cod_aluno BOOLEAN NOT NULL,
	ano BOOLEAN NOT NULL,
	kit_completo BOOLEAN,
	agasalho_qtd SMALLINT,
	camiseta_curta_qtd SMALLINT,
	camiseta_longa_qtd SMALLINT,
	meias_qtd SMALLINT,
	bermudas_tectels_qtd SMALLINT,
	bermudas_coton_qtd SMALLINT,
	tenis_qtd SMALLINT,
	CONSTRAINT distribuicao_uniforme_cod_distribuicao_uniforme_pkey PRIMARY KEY (cod_distribuicao_uniforme ),
	CONSTRAINT distribuicao_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
	REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT
	)	
	WITH (
	OIDS=TRUE
	);

	UPDATE portal.menu_submenu SET nm_submenu = 'Distribuição de uniforme por aluno', arquivo = 'module/Reports/DistribuicaoUniformePorAluno' WHERE cod_menu_submenu = 999224;
	UPDATE pmicontrolesis.menu SET tt_menu = 'Distribuição de uniforme por aluno', caminho = 'module/Reports/DistribuicaoUniformePorAluno' WHERE cod_menu = 999224;
	
  -- //