  -- //

  --
  -- Cria tabelas e squenciais para controle de rotas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

	--Criar a tabela de rota

	-- Sequence: modules.empresa_transporte_escolar_seq
	CREATE SEQUENCE modules.rota_transporte_escolar_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;
	ALTER TABLE modules.rota_transporte_escolar_seq
	OWNER TO ieducar;


	-- Table: modules.rota_transporte_escolar
	CREATE TABLE modules.rota_transporte_escolar
	(
	cod_rota_transporte_escolar integer NOT NULL DEFAULT nextval('modules.rota_transporte_escolar_seq'::regclass),
	ref_idpes_destino integer NOT NULL,
	descricao character varying(50) NOT NULL,
	ano integer NOT NULL,
	tipo_rota char(1) NOT NULL,
	km_pav float,
	km_npav float,
	ref_cod_empresa_transporte_escolar integer,
	tercerizado character(1) not null,
	CONSTRAINT rota_transporte_escolar_cod_rota_transporte_escolar_pkey PRIMARY KEY (cod_rota_transporte_escolar ),
	CONSTRAINT rota_transporte_escolar_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino)
	REFERENCES cadastro.juridica (idpes) MATCH SIMPLE,
	CONSTRAINT rota_transporte_escolar_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar)
	REFERENCES modules.empresa_transporte_escolar (cod_empresa_transporte_escolar) MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT
	)	
	WITH (
	OIDS=TRUE
	);
	
	
	-- Sequence: modules.itinerario_transporte_escolar_seq
	CREATE SEQUENCE modules.itinerario_transporte_escolar_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;
	ALTER TABLE modules.itinerario_transporte_escolar_seq
	OWNER TO ieducar;
	
	
	-- Table: modules.itinerario_transporte_escolar
	CREATE TABLE modules.itinerario_transporte_escolar
	(
	cod_itinerario_transporte_escolar integer NOT NULL DEFAULT nextval('modules.itinerario_transporte_escolar_seq'::regclass),
	ref_cod_rota_transporte_escolar integer not null,
	seq integer not null,
	ref_cod_ponto_transporte_escolar integer not null,
	ref_cod_veiculo integer,
	hora time without time zone,
	tipo character(1) not null,
	CONSTRAINT itinerario_transporte_escolar_cod_itinerario_transporte_escolar_pkey PRIMARY KEY (cod_itinerario_transporte_escolar),
	CONSTRAINT itinerario_transporte_escolar_ref_cod_rota_transporte_escolar_fkey FOREIGN KEY (ref_cod_rota_transporte_escolar)
	REFERENCES modules.rota_transporte_escolar (cod_rota_transporte_escolar) MATCH SIMPLE,
	CONSTRAINT ponto_transporte_escolar_ref_cod_veiculo_fkey FOREIGN KEY (ref_cod_veiculo)
	REFERENCES modules.veiculo (cod_veiculo) MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT
	)
	WITH (
	OIDS=TRUE
	);	
	
	-- Sequence: modules.ponto_transporte_escolar_seq
	CREATE SEQUENCE modules.ponto_transporte_escolar_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;
	ALTER TABLE modules.ponto_transporte_escolar_seq
	OWNER TO ieducar;
	
	
	-- Table: modules.ponto_transporte_escolar
	CREATE TABLE modules.ponto_transporte_escolar
	(
	cod_ponto_transporte_escolar integer NOT NULL DEFAULT nextval('modules.ponto_transporte_escolar_seq'::regclass),
	descricao varchar(70) not null,
	CONSTRAINT ponto_transporte_escolar_cod_ponto_transporte_escolar_pkey PRIMARY KEY (cod_ponto_transporte_escolar )
	)
	WITH (
	OIDS=TRUE
	);
	
	
	-- Sequence: modules.pessoa_transporte_seq
	CREATE SEQUENCE modules.pessoa_transporte_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;
	ALTER TABLE modules.pessoa_transporte_seq
	OWNER TO ieducar;
	

	-- Table: modules.pessoa_transporte
	CREATE TABLE modules.pessoa_transporte
	(
	cod_pessoa_transporte integer NOT NULL DEFAULT nextval('modules.pessoa_transporte_seq'::regclass),
	ref_idpes integer not null,
	ref_cod_rota_transporte_escolar integer not null,
	ref_cod_ponto_transporte_escolar integer,
	ref_idpes_destino integer,
	observacao varchar(255),
	CONSTRAINT pessoa_transporte_cod_pessoa_transporte_pkey PRIMARY KEY (cod_pessoa_transporte ),
	CONSTRAINT pessoa_transporte_ref_cod_rota_transporte_escolar_fkey FOREIGN KEY (ref_cod_rota_transporte_escolar)
	REFERENCES modules.rota_transporte_escolar (cod_rota_transporte_escolar) MATCH SIMPLE,
	CONSTRAINT pessoa_transporte_ref_cod_ponto_transporte_escolar_fkey FOREIGN KEY (ref_cod_ponto_transporte_escolar)
	REFERENCES modules.ponto_transporte_escolar (cod_ponto_transporte_escolar) MATCH SIMPLE,
	CONSTRAINT pessoa_transporte_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino)
	REFERENCES cadastro.juridica (idpes) MATCH SIMPLE,
	CONSTRAINT pessoa_transporte_ref_idpes_fkey FOREIGN KEY (ref_idpes)
	REFERENCES cadastro.fisica (idpes) MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT
	)
	WITH (
	OIDS=TRUE
	);



  -- //@UNDO

	DROP TABLE modules.pessoa_transporte;
	DROP SEQUENCE modules.pessoa_transporte_seq;
	DROP TABLE modules.ponto_transporte_escolar;
	DROP SEQUENCE modules.ponto_transporte_escolar_seq;
	DROP TABLE modules.itinerario_transporte_escolar;
	DROP SEQUENCE modules.itinerario_transporte_escolar_seq;
	DROP SEQUENCE modules.rotas_transporte_escolar_seq;



  -- //



