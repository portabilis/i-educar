SET default_tablespace = '';

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
