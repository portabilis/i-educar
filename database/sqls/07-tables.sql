SET default_tablespace = '';

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
