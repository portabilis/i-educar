  -- //

  --
  -- Cria tabelas e squenciais para controle de empresas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


    CREATE SEQUENCE modules.empresa_transporte_escolar_seq
    INCREMENT 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;
    ALTER TABLE modules.empresa_transporte_escolar_seq
    OWNER TO ieducar;

    CREATE TABLE modules.empresa_transporte_escolar
    (
    cod_empresa_transporte_escolar integer NOT NULL DEFAULT nextval('empresa_transporte_escolar_seq'::regclass),
    ref_idpes integer NOT NULL,
    ref_resp_idpes character varying(50) NOT NULL,
    observacao character varying(255),
    CONSTRAINT empresa_transporte_escolar_cod_empresa_transporte_escolar_pkey PRIMARY KEY (cod_empresa_transporte_escolar ),
    CONSTRAINT empresa_transporte_escolar_ref_idpes_fkey FOREIGN KEY (ref_idpes)
    REFERENCES cadastro.juridica (idpes) MATCH SIMPLE,
    CONSTRAINT empresa_transporte_escolar_ref_resp_idpes_fkey FOREIGN KEY (ref_resp_idpes)
    REFERENCES cadastro.fisica (idpes) MATCH SIMPLE
    ON UPDATE RESTRICT ON DELETE RESTRICT
    )
    WITH (
    OIDS=TRUE
    );
    
    CREATE SEQUENCE modules.motorista_seq
    INCREMENT 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;
    ALTER TABLE modules.motorista_seq
    OWNER TO ieducar;

  
    CREATE TABLE modules.motorista
    (
    cod_motorista integer NOT NULL DEFAULT nextval('motorista_seq'::regclass),
    ref_idpes integer NOT NULL,
    cnh character varying(15) NOT NULL,
    tipo_cnh character varying(2) NOT NUL,
    dt_habilitacao date,
    vencimento_cnh date,
    ref_cod_empresa_transporte_escolar integer NOT NULL,
    observacao character varying(255),
    CONSTRAINT motorista_pkey PRIMARY KEY (cod_motorista),
    CONSTRAINT motorista_ref_idpes_fkey FOREIGN KEY (ref_idpes)
    REFERENCES cadastro.fisica (idpes) MATCH SIMPLE
    ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT motorista_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar)
    REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT
    )
    WITH (
    OIDS=TRUE
    );
    
    CREATE SEQUENCE modules.tipo_veiculo_seq
    INCREMENT 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;
    ALTER TABLE modules.tipo_veiculo_seq
    OWNER TO ieducar;

    CREATE TABLE modules.tipo_veiculo
    (
    cod_tipo_veiculo integer NOT NULL DEFAULT nextval('tipo_veiculo_seq'::regclass),
    descricao character varying(60),
    CONSTRAINT tipo_veiculo_pkey PRIMARY KEY (cod_tipo_veiculo)
    )
    WITH (
    OIDS=TRUE
    );


    CREATE SEQUENCE modules.veiculo_seq
    INCREMENT 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;
    ALTER TABLE modules.veiculo_seq
    OWNER TO ieducar;
    
    CREATE TABLE modules.veiculo
    (
    cod_veiculo integer NOT NULL DEFAULT nextval('veiculo_seq'::regclass),
    descricao character varying(255) NOT NULL,
    placa character varying(10) NOT NULL,
    renavam character varying(15) NOT NULL,
    chassi character varying(30),
    marca character varying(50),
    ano_fabricacao integer,
    ano_modelo integer,
    passageiros integer NOT NULL,
    malha char(1) NOT NULL,
    ref_cod_tipo_veiculo integer NOT NULL,
    exclusivo_transporte_escolar char(1) NOT NULL,
    adaptado_necessidades_especiais char(1) NOT NULL,
    ativo char(1),
    descricao_inativo char(155),
    ref_cod_empresa_transporte_escolar integer NOT NULL,
    ref_cod_motorista integer NOT NULL,
    observacao character varying(255),
    CONSTRAINT veiculo_pkey PRIMARY KEY (cod_veiculo),
    CONSTRAINT veiculo_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar)
    REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) MATCH SIMPLE,
    CONSTRAINT veiculo_ref_cod_tipo_veiculo_fkey FOREIGN KEY (ref_cod_tipo_veiculo)
    REFERENCES modules.tipo_veiculo(cod_tipo_veiculo) MATCH SIMPLE
    ON UPDATE RESTRICT ON DELETE RESTRICT
    )
    WITH (
    OIDS=TRUE
    );

  -- //@UNDO

  DROP TABLE modules.veiculo;
  DROP SEQUENCE modules.veiculo_seq;
  DROP TABLE modules.tipo_veiculo;
  DROP SEQUENCE modules.tipo_veiculo_seq;
  DROP TABLE modules.motorista;
  DROP SEQUENCE modules.motorista_seq;
  DROP TABLE modules.empresa_transporte_escolar;  
  DROP SEQUENCE modules.empresa_transporte_escolar_seq;  


  -- //



