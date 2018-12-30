SET default_tablespace = '';

SET default_with_oids = true;

CREATE TABLE public.bairro (
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    idbai numeric(6,0) DEFAULT nextval(('public.seq_bairro'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    zona_localizacao integer DEFAULT 1,
    iddis integer NOT NULL,
    idsetorbai numeric(6,0),
    CONSTRAINT ck_bairro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);

CREATE TABLE public.logradouro (
    idlog numeric(6,0) DEFAULT nextval(('public.seq_logradouro'::text)::regclass) NOT NULL,
    idtlog character varying(5) NOT NULL,
    nome character varying(150) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    ident_oficial character(1),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_logradouro_ident_oficial CHECK (((ident_oficial = 'S'::bpchar) OR (ident_oficial = 'N'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);

CREATE TABLE public.municipio (
    idmun numeric(6,0) DEFAULT nextval(('public.seq_municipio'::text)::regclass) NOT NULL,
    nome character varying(60) NOT NULL,
    sigla_uf character varying(3) NOT NULL,
    area_km2 numeric(6,0),
    idmreg numeric(2,0),
    idasmun numeric(2,0),
    cod_ibge numeric(20,0),
    geom character varying,
    tipo character(1) NOT NULL,
    idmun_pai numeric(6,0),
    idpes_rev numeric,
    idpes_cad numeric,
    data_rev timestamp without time zone,
    data_cad timestamp without time zone NOT NULL,
    origem_gravacao character(1) NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_municipio_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_municipio_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_municipio_tipo CHECK (((tipo = 'D'::bpchar) OR (tipo = 'M'::bpchar) OR (tipo = 'P'::bpchar) OR (tipo = 'R'::bpchar)))
);

CREATE TABLE public.bairro_regiao (
    ref_cod_regiao integer NOT NULL,
    ref_idbai integer NOT NULL
);

SET default_with_oids = false;

CREATE TABLE public.changelog (
    change_number bigint NOT NULL,
    delta_set character varying(10) NOT NULL,
    start_dt timestamp without time zone NOT NULL,
    complete_dt timestamp without time zone,
    applied_by character varying(100) NOT NULL,
    description character varying(500) NOT NULL
);

SET default_with_oids = true;

CREATE TABLE public.distrito (
    idmun numeric(6,0) NOT NULL,
    geom character varying,
    iddis numeric(6,0) DEFAULT nextval(('public.seq_distrito'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    cod_ibge character varying(7),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_distrito_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar))),
    CONSTRAINT ck_distrito_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar)))
);

CREATE TABLE public.logradouro_fonetico (
    fonema character varying(30) NOT NULL,
    idlog numeric(8,0) NOT NULL
);

CREATE TABLE public.pais (
    idpais numeric(3,0) NOT NULL,
    nome character varying(60) NOT NULL,
    geom character varying,
    cod_ibge integer
);

SET default_with_oids = false;

CREATE TABLE public.pghero_query_stats (
    id integer NOT NULL,
    database text,
    "user" text,
    query text,
    query_hash bigint,
    total_time double precision,
    calls bigint,
    captured_at timestamp without time zone
);

ALTER SEQUENCE public.pghero_query_stats_id_seq OWNED BY public.pghero_query_stats.id;

CREATE TABLE public.phinxlog (
    version bigint NOT NULL,
    migration_name character varying(100),
    start_time timestamp without time zone,
    end_time timestamp without time zone,
    breakpoint boolean DEFAULT false NOT NULL
);

CREATE TABLE public.portal_banner (
    cod_portal_banner integer DEFAULT nextval('public.portal_banner_cod_portal_banner_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    prioridade integer DEFAULT 0 NOT NULL,
    link character varying(255) DEFAULT ''::character varying NOT NULL,
    lateral_ smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = true;

CREATE TABLE public.regiao (
    cod_regiao integer DEFAULT nextval('public.regiao_cod_regiao_seq'::regclass) NOT NULL,
    nm_regiao character varying(100)
);

CREATE TABLE public.setor (
    idset integer DEFAULT nextval('public.setor_idset_seq'::regclass) NOT NULL,
    nivel numeric(1,0) NOT NULL,
    nome character varying(100) NOT NULL,
    sigla character varying(25),
    idsetsub integer,
    idsetredir integer,
    situacao character(1) NOT NULL,
    localizacao character(1) NOT NULL,
    CONSTRAINT ck_setor_localizacao CHECK (((localizacao = 'E'::bpchar) OR (localizacao = 'I'::bpchar))),
    CONSTRAINT ck_setor_situacao CHECK (((situacao = 'A'::bpchar) OR (situacao = 'I'::bpchar)))
);

CREATE TABLE public.setor_bai (
    idsetorbai numeric(6,0) DEFAULT nextval(('public.seq_setor_bai'::text)::regclass) NOT NULL,
    nome character varying(80) NOT NULL
);

CREATE TABLE public.uf (
    sigla_uf character varying(3) NOT NULL,
    nome character varying(30) NOT NULL,
    geom character varying,
    idpais numeric(3,0),
    cod_ibge numeric(6,0)
);

CREATE TABLE public.vila (
    idvil numeric(4,0) NOT NULL,
    idmun numeric(6,0) NOT NULL,
    nome character varying(50) NOT NULL,
    geom character varying
);

SET default_with_oids = false;

CREATE TABLE serieciasc.aluno_cod_aluno (
    cod_aluno integer NOT NULL,
    cod_ciasc bigint NOT NULL,
    user_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.aluno_uniforme (
    ref_cod_aluno integer NOT NULL,
    data_recebimento timestamp without time zone NOT NULL,
    camiseta character(2),
    quantidade_camiseta integer,
    bermuda character(2),
    quantidade_bermuda integer,
    jaqueta character(2),
    quantidade_jaqueta integer,
    calca character(2),
    quantidade_calca integer,
    meia character(2),
    quantidade_meia integer,
    tenis character(2),
    quantidade_tenis integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_agua (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    poco_artesiano integer DEFAULT 0,
    cisterna integer DEFAULT 0,
    fonte_rio integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_energia (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    gerador_proprio integer DEFAULT 0,
    solar integer DEFAULT 0,
    eolica integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_lingua_indigena (
    ref_cod_escola integer NOT NULL,
    educacao_indigena integer DEFAULT 0,
    lingua_indigena integer DEFAULT 0,
    lingua_portuguesa integer DEFAULT 0,
    materiais_especificos integer DEFAULT 0,
    ue_terra_indigena integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_lixo (
    ref_cod_escola integer NOT NULL,
    coleta integer DEFAULT 0,
    queima integer DEFAULT 0,
    outra_area integer DEFAULT 0,
    recicla integer DEFAULT 0,
    reutiliza integer DEFAULT 0,
    enterra integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_projeto (
    ref_cod_escola integer NOT NULL,
    danca integer DEFAULT 0,
    folclorico integer DEFAULT 0,
    teatral integer DEFAULT 0,
    ambiental integer DEFAULT 0,
    coral integer DEFAULT 0,
    fanfarra integer DEFAULT 0,
    artes_plasticas integer DEFAULT 0,
    integrada integer DEFAULT 0,
    ambiente_alimentacao integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_regulamentacao (
    ref_cod_escola integer NOT NULL,
    regulamentacao integer DEFAULT 1 NOT NULL,
    situacao integer DEFAULT 1 NOT NULL,
    data_criacao date,
    ato_criacao integer DEFAULT 0,
    numero_ato_criacao character varying(20),
    data_ato_criacao date,
    ato_paralizacao integer DEFAULT 0,
    numero_ato_paralizacao character varying(20),
    data_ato_paralizacao date,
    data_extincao date,
    ato_extincao integer DEFAULT 0,
    numero_ato_extincao character varying(20),
    data_ato_extincao date,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

CREATE TABLE serieciasc.escola_sanitario (
    ref_cod_escola integer NOT NULL,
    rede_publica integer DEFAULT 0,
    fossa integer DEFAULT 0,
    inexistente integer DEFAULT 0,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);

SET default_with_oids = true;

CREATE TABLE urbano.cep_logradouro (
    cep numeric(8,0) NOT NULL,
    idlog numeric(6,0) NOT NULL,
    nroini numeric(6,0),
    nrofin numeric(6,0),
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_cep_logradouro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar)))
);

CREATE TABLE urbano.cep_logradouro_bairro (
    idlog numeric(6,0) NOT NULL,
    cep numeric(8,0) NOT NULL,
    idbai numeric(6,0) NOT NULL,
    idpes_rev numeric,
    data_rev timestamp without time zone,
    origem_gravacao character(1) NOT NULL,
    idpes_cad numeric,
    data_cad timestamp without time zone NOT NULL,
    operacao character(1) NOT NULL,
    idsis_rev integer,
    idsis_cad integer NOT NULL,
    CONSTRAINT ck_cep_logradouro_bairro_origem_gravacao CHECK (((origem_gravacao = 'M'::bpchar) OR (origem_gravacao = 'U'::bpchar) OR (origem_gravacao = 'C'::bpchar) OR (origem_gravacao = 'O'::bpchar))),
    CONSTRAINT ck_logradouro_operacao CHECK (((operacao = 'I'::bpchar) OR (operacao = 'A'::bpchar) OR (operacao = 'E'::bpchar)))
);

CREATE TABLE urbano.tipo_logradouro (
    idtlog character varying(5) NOT NULL,
    descricao character varying(40) NOT NULL
);
