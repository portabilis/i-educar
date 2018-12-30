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

                SET default_with_oids = true;

CREATE TABLE pmidrh.diaria (
    cod_diaria integer DEFAULT nextval('pmidrh.diaria_cod_diaria_seq'::regclass) NOT NULL,
    ref_funcionario_cadastro integer NOT NULL,
    ref_cod_diaria_grupo integer NOT NULL,
    ref_funcionario integer NOT NULL,
    conta_corrente integer,
    agencia integer,
    banco integer,
    dotacao_orcamentaria character varying(50),
    objetivo text,
    data_partida timestamp without time zone,
    data_chegada timestamp without time zone,
    estadual smallint,
    destino character varying(100),
    data_pedido timestamp without time zone,
    vl100 double precision,
    vl75 double precision,
    vl50 double precision,
    vl25 double precision,
    roteiro integer,
    ativo boolean DEFAULT true,
    ref_cod_setor integer,
    num_diaria numeric(6,0)
);

CREATE TABLE pmidrh.diaria_grupo (
    cod_diaria_grupo integer DEFAULT nextval('pmidrh.diaria_grupo_cod_diaria_grupo_seq'::regclass) NOT NULL,
    desc_grupo character varying(255) NOT NULL
);

CREATE TABLE pmidrh.diaria_valores (
    cod_diaria_valores integer DEFAULT nextval('pmidrh.diaria_valores_cod_diaria_valores_seq'::regclass) NOT NULL,
    ref_funcionario_cadastro integer NOT NULL,
    ref_cod_diaria_grupo integer NOT NULL,
    estadual smallint NOT NULL,
    p100 double precision,
    p75 double precision,
    p50 double precision,
    p25 double precision,
    data_vigencia timestamp without time zone NOT NULL
);

CREATE TABLE pmidrh.setor (
    cod_setor integer DEFAULT nextval('pmidrh.setor_cod_setor_seq'::regclass) NOT NULL,
    ref_cod_pessoa_exc integer,
    ref_cod_pessoa_cad integer NOT NULL,
    ref_cod_setor integer,
    nm_setor character varying(255) NOT NULL,
    sgl_setor character varying(15) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nivel smallint DEFAULT (1)::smallint NOT NULL,
    no_paco smallint DEFAULT 1,
    endereco text,
    tipo character(1),
    ref_idpes_resp integer
);

SET default_with_oids = false;

CREATE TABLE pmieducar.abandono_tipo (
    cod_abandono_tipo integer DEFAULT nextval('pmieducar.abandono_tipo_cod_abandono_tipo_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer,
    nome character varying(255) NOT NULL,
    data_cadastro timestamp without time zone,
    data_exclusao timestamp without time zone,
    ativo integer
);

SET default_with_oids = true;

CREATE TABLE pmieducar.acervo (
    cod_acervo integer DEFAULT nextval('pmieducar.acervo_cod_acervo_seq'::regclass) NOT NULL,
    ref_cod_exemplar_tipo integer NOT NULL,
    ref_cod_acervo integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_acervo_colecao integer,
    ref_cod_acervo_idioma integer NOT NULL,
    ref_cod_acervo_editora integer NOT NULL,
    titulo character varying(255) NOT NULL,
    sub_titulo character varying(255),
    cdu character varying(20),
    cutter character varying(20),
    volume integer,
    num_edicao integer,
    ano character varying(25),
    num_paginas integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    isbn character varying(255),
    cdd character varying(20),
    estante character varying(20),
    dimencao character varying(255),
    material_ilustrativo character varying(255),
    dimencao_ilustrativo character varying(255),
    local character varying(255),
    ref_cod_tipo_autor integer,
    tipo_autor character varying(255)
);

CREATE TABLE pmieducar.acervo_acervo_assunto (
    ref_cod_acervo integer NOT NULL,
    ref_cod_acervo_assunto integer NOT NULL
);

CREATE TABLE pmieducar.acervo_acervo_autor (
    ref_cod_acervo_autor integer NOT NULL,
    ref_cod_acervo integer NOT NULL,
    principal smallint DEFAULT (0)::smallint NOT NULL
);

CREATE TABLE pmieducar.acervo_assunto (
    cod_acervo_assunto integer DEFAULT nextval('pmieducar.acervo_assunto_cod_acervo_assunto_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_assunto character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.acervo_autor (
    cod_acervo_autor integer DEFAULT nextval('pmieducar.acervo_autor_cod_acervo_autor_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_autor character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);

CREATE TABLE pmieducar.acervo_colecao (
    cod_acervo_colecao integer DEFAULT nextval('pmieducar.acervo_colecao_cod_acervo_colecao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_colecao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.acervo_editora (
    cod_acervo_editora integer DEFAULT nextval('pmieducar.acervo_editora_cod_acervo_editora_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_idtlog character varying(20),
    ref_sigla_uf character(2),
    nm_editora character varying(255) NOT NULL,
    cep numeric(8,0),
    cidade character varying(60),
    bairro character varying(60),
    logradouro character varying(255),
    numero numeric(6,0),
    telefone integer,
    ddd_telefone numeric(3,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.acervo_idioma (
    cod_acervo_idioma integer DEFAULT nextval('pmieducar.acervo_idioma_cod_acervo_idioma_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_idioma character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.aluno (
    cod_aluno integer DEFAULT nextval('pmieducar.aluno_cod_aluno_seq'::regclass) NOT NULL,
    ref_cod_religiao integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer,
    ref_idpes integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    caminho_foto character varying(255),
    analfabeto smallint DEFAULT (0)::smallint,
    nm_pai character varying(255),
    nm_mae character varying(255),
    tipo_responsavel character(1),
    aluno_estado_id character varying(25),
    justificativa_falta_documentacao smallint,
    url_laudo_medico json,
    codigo_sistema character varying(30),
    veiculo_transporte_escolar smallint,
    autorizado_um character varying(150),
    parentesco_um character varying(150),
    autorizado_dois character varying(150),
    parentesco_dois character varying(150),
    autorizado_tres character varying(150),
    parentesco_tres character varying(150),
    autorizado_quatro character varying(150),
    parentesco_quatro character varying(150),
    autorizado_cinco character varying(150),
    parentesco_cinco character varying(150),
    url_documento json,
    recebe_escolarizacao_em_outro_espaco smallint DEFAULT 3 NOT NULL,
    recursos_prova_inep integer[]
);

SET default_with_oids = false;

CREATE TABLE pmieducar.aluno_aluno_beneficio (
    aluno_id integer NOT NULL,
    aluno_beneficio_id integer NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.aluno_beneficio (
    cod_aluno_beneficio integer DEFAULT nextval('pmieducar.aluno_beneficio_cod_aluno_beneficio_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_beneficio character varying(255) NOT NULL,
    desc_beneficio text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.aluno_historico_altura_peso (
    ref_cod_aluno integer NOT NULL,
    data_historico date NOT NULL,
    altura numeric(12,2) NOT NULL,
    peso numeric(12,2) NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.ano_letivo_modulo (
    ref_ano integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL,
    dias_letivos numeric(5,0)
);

SET default_with_oids = false;

CREATE TABLE pmieducar.auditoria_falta_componente_dispensa (
    id integer NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_componente_curricular integer NOT NULL,
    quantidade integer NOT NULL,
    etapa integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);

ALTER SEQUENCE pmieducar.auditoria_falta_componente_dispensa_id_seq OWNED BY pmieducar.auditoria_falta_componente_dispensa.id;

CREATE TABLE pmieducar.auditoria_nota_dispensa (
    id integer NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_componente_curricular integer NOT NULL,
    nota numeric(8,4) NOT NULL,
    etapa integer NOT NULL,
    nota_recuperacao character varying(10),
    nota_recuperacao_especifica character varying(10),
    data_cadastro timestamp without time zone NOT NULL
);

ALTER SEQUENCE pmieducar.auditoria_nota_dispensa_id_seq OWNED BY pmieducar.auditoria_nota_dispensa.id;

SET default_with_oids = true;

CREATE TABLE pmieducar.avaliacao_desempenho (
    sequencial integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    descricao text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    titulo_avaliacao character varying(255) NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.backup (
    id integer NOT NULL,
    caminho character varying(255) NOT NULL,
    data_backup timestamp without time zone
);

ALTER SEQUENCE pmieducar.backup_id_seq OWNED BY pmieducar.backup.id;

SET default_with_oids = true;

CREATE TABLE pmieducar.biblioteca (
    cod_biblioteca integer DEFAULT nextval('pmieducar.biblioteca_cod_biblioteca_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer,
    ref_cod_escola integer,
    nm_biblioteca character varying(255) NOT NULL,
    valor_multa double precision,
    max_emprestimo integer,
    valor_maximo_multa double precision,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    requisita_senha smallint DEFAULT (0)::smallint NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    dias_espera numeric(2,0),
    tombo_automatico boolean DEFAULT true,
    bloqueia_emprestimo_em_atraso boolean
);

CREATE TABLE pmieducar.biblioteca_dia (
    ref_cod_biblioteca integer NOT NULL,
    dia numeric(1,0) NOT NULL
);

CREATE TABLE pmieducar.biblioteca_feriados (
    cod_feriado integer DEFAULT nextval('pmieducar.biblioteca_feriados_cod_feriado_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    nm_feriado character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_feriado date NOT NULL
);

CREATE TABLE pmieducar.biblioteca_usuario (
    ref_cod_biblioteca integer NOT NULL,
    ref_cod_usuario integer NOT NULL
);

CREATE TABLE pmieducar.bloqueio_ano_letivo (
    ref_cod_instituicao integer NOT NULL,
    ref_ano integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.bloqueio_lancamento_faltas_notas (
    cod_bloqueio integer DEFAULT nextval('public.bloqueio_lancamento_faltas_notas_seq'::regclass) NOT NULL,
    ano integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    etapa integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.calendario_ano_letivo (
    cod_calendario_ano_letivo integer DEFAULT nextval('pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    data_cadastra timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.calendario_anotacao (
    cod_calendario_anotacao integer DEFAULT nextval('pmieducar.calendario_anotacao_cod_calendario_anotacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_anotacao character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL
);

CREATE TABLE pmieducar.calendario_dia (
    ref_cod_calendario_ano_letivo integer NOT NULL,
    mes integer NOT NULL,
    dia integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_calendario_dia_motivo integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    descricao text
);

CREATE TABLE pmieducar.calendario_dia_anotacao (
    ref_dia integer NOT NULL,
    ref_mes integer NOT NULL,
    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
    ref_cod_calendario_anotacao integer NOT NULL
);

CREATE TABLE pmieducar.calendario_dia_motivo (
    cod_calendario_dia_motivo integer DEFAULT nextval('pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    sigla character varying(15) NOT NULL,
    descricao text,
    tipo character(1) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_motivo character varying(255) NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.candidato_reserva_vaga (
    cod_candidato_reserva_vaga integer DEFAULT nextval('pmieducar.candidato_reserva_vaga_seq'::regclass) NOT NULL,
    ano_letivo integer NOT NULL,
    data_solicitacao date NOT NULL,
    ref_cod_aluno integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_turno integer,
    ref_cod_pessoa_cad integer NOT NULL,
    data_cad timestamp without time zone DEFAULT now() NOT NULL,
    data_update timestamp without time zone DEFAULT now() NOT NULL,
    ref_cod_matricula integer,
    situacao character(1),
    data_situacao date,
    motivo character varying(255),
    ref_cod_escola integer,
    quantidade_membros smallint,
    mae_fez_pre_natal boolean DEFAULT false,
    membros_trabalham smallint,
    hora_solicitacao time without time zone
);

CREATE TABLE pmieducar.categoria_nivel (
    cod_categoria_nivel integer DEFAULT nextval('pmieducar.categoria_nivel_cod_categoria_nivel_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_categoria_nivel character varying(100) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL
);

CREATE TABLE pmieducar.categoria_obra (
    id integer NOT NULL,
    descricao character varying(100) NOT NULL,
    observacoes character varying(300)
);

ALTER SEQUENCE pmieducar.categoria_obra_id_seq OWNED BY pmieducar.categoria_obra.id;

SET default_with_oids = true;

CREATE TABLE pmieducar.cliente (
    cod_cliente integer DEFAULT nextval('pmieducar.cliente_cod_cliente_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_idpes integer NOT NULL,
    login integer,
    senha character varying(255),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    observacoes text
);

CREATE TABLE pmieducar.cliente_suspensao (
    sequencial integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_motivo_suspensao integer NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_suspende integer NOT NULL,
    dias integer NOT NULL,
    data_suspensao timestamp without time zone NOT NULL,
    data_liberacao timestamp without time zone
);

CREATE TABLE pmieducar.cliente_tipo (
    cod_cliente_tipo integer DEFAULT nextval('pmieducar.cliente_tipo_cod_cliente_tipo_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.cliente_tipo_cliente (
    ref_cod_cliente_tipo integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ativo smallint DEFAULT (1)::smallint,
    ref_cod_biblioteca integer
);

SET default_with_oids = true;

CREATE TABLE pmieducar.cliente_tipo_exemplar_tipo (
    ref_cod_cliente_tipo integer NOT NULL,
    ref_cod_exemplar_tipo integer NOT NULL,
    dias_emprestimo numeric(3,0)
);

CREATE TABLE pmieducar.coffebreak_tipo (
    cod_coffebreak_tipo integer DEFAULT nextval('pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    custo_unitario double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.configuracoes_gerais (
    ref_cod_instituicao integer NOT NULL,
    permite_relacionamento_posvendas integer DEFAULT 1 NOT NULL,
    url_novo_educacao character varying(100),
    mostrar_codigo_inep_aluno smallint DEFAULT 1,
    justificativa_falta_documentacao_obrigatorio smallint DEFAULT 1,
    tamanho_min_rede_estadual integer,
    modelo_boletim_professor integer DEFAULT 1,
    custom_labels json,
    url_cadastro_usuario character varying(255) DEFAULT NULL::character varying,
    active_on_ieducar smallint DEFAULT 1,
    ieducar_image character varying(255) DEFAULT NULL::character varying,
    ieducar_entity_name character varying(255) DEFAULT NULL::character varying,
    ieducar_login_footer text DEFAULT '<p>Portabilis Tecnologia - suporte@portabilis.com.br - <a class="   light" href="http://suporte.portabilis.com.br" target="_blank"> Obter Suporte </a></p> '::character varying,
    ieducar_external_footer text DEFAULT '<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, acesse nosso <a href="   http://blog.portabilis.com.br">blog</a></p> '::character varying,
    ieducar_internal_footer text DEFAULT '<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, <a href="   http://blog.portabilis.com.br" target="_blank">acesse nosso blog</a> &nbsp;&nbsp;&nbsp; &copy; Portabilis - Todos os direitos reservados</p>'::character varying,
    facebook_url character varying(255) DEFAULT 'https://www.facebook.com/portabilis'::character varying,
    twitter_url character varying(255) DEFAULT 'https://twitter.com/portabilis'::character varying,
    linkedin_url character varying(255) DEFAULT 'https://www.linkedin.com/company/portabilis-tecnologia'::character varying,
    ieducar_suspension_message text
);

COMMENT ON COLUMN pmieducar.configuracoes_gerais.mostrar_codigo_inep_aluno IS 'Mostrar código INEP do aluno nas telas de cadastro';

COMMENT ON COLUMN pmieducar.configuracoes_gerais.justificativa_falta_documentacao_obrigatorio IS 'Campo "Justificativa para a falta de documentação" obrigatório no cadastro de alunos';

COMMENT ON COLUMN pmieducar.configuracoes_gerais.tamanho_min_rede_estadual IS 'Tamanho mínimo do campo "Código rede estadual"';

COMMENT ON COLUMN pmieducar.configuracoes_gerais.modelo_boletim_professor IS 'Modelo do boletim do professor. 1 - Padrão, 2 - Modelo recuperação por etapa, 3 - Modelo recuperação paralela';

COMMENT ON COLUMN pmieducar.configuracoes_gerais.custom_labels IS 'Guarda customizações em labels e textos do sistema.';

COMMENT ON COLUMN pmieducar.configuracoes_gerais.url_cadastro_usuario IS 'URL da ferramenta externa de cadastro de usuários';

SET default_with_oids = true;

CREATE TABLE pmieducar.curso (
    cod_curso integer DEFAULT nextval('pmieducar.curso_cod_curso_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_tipo_regime integer,
    ref_cod_nivel_ensino integer NOT NULL,
    ref_cod_tipo_ensino integer NOT NULL,
    nm_curso character varying(255) NOT NULL,
    sgl_curso character varying(15) NOT NULL,
    qtd_etapas smallint NOT NULL,
    carga_horaria double precision NOT NULL,
    ato_poder_publico character varying(255),
    objetivo_curso text,
    publico_alvo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    padrao_ano_escolar smallint DEFAULT (0)::smallint NOT NULL,
    hora_falta double precision DEFAULT 0.00 NOT NULL,
    multi_seriado integer,
    modalidade_curso integer
);

CREATE TABLE pmieducar.disciplina (
    cod_disciplina integer DEFAULT nextval('pmieducar.disciplina_cod_disciplina_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    desc_disciplina text,
    desc_resumida text,
    abreviatura character varying(15) NOT NULL,
    carga_horaria integer NOT NULL,
    apura_falta smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_disciplina character varying(255) NOT NULL,
    ref_cod_curso integer
);

SET default_with_oids = false;

CREATE TABLE pmieducar.disciplina_dependencia (
    ref_cod_matricula integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    observacao text,
    cod_disciplina_dependencia integer NOT NULL
);

CREATE TABLE pmieducar.disciplina_serie (
    ref_cod_disciplina integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.disciplina_topico (
    cod_disciplina_topico integer DEFAULT nextval('pmieducar.disciplina_topico_cod_disciplina_topico_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_topico character varying(255) NOT NULL,
    desc_topico text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.dispensa_disciplina (
    ref_cod_matricula integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_tipo_dispensa integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    observacao text,
    cod_dispensa integer DEFAULT nextval('pmieducar.dispensa_disciplina_cod_dispensa_seq'::regclass) NOT NULL
);

CREATE TABLE pmieducar.dispensa_etapa (
    ref_cod_dispensa integer,
    etapa integer
);

SET default_with_oids = true;

CREATE TABLE pmieducar.distribuicao_uniforme (
    cod_distribuicao_uniforme integer DEFAULT nextval('pmieducar.distribuicao_uniforme_seq'::regclass) NOT NULL,
    ref_cod_aluno integer NOT NULL,
    ano integer NOT NULL,
    kit_completo boolean,
    agasalho_qtd smallint,
    camiseta_curta_qtd smallint,
    camiseta_longa_qtd smallint,
    meias_qtd smallint,
    bermudas_tectels_qtd smallint,
    bermudas_coton_qtd smallint,
    tenis_qtd smallint,
    data date,
    agasalho_tm character varying(20),
    camiseta_curta_tm character varying(20),
    camiseta_longa_tm character varying(20),
    meias_tm character varying(20),
    bermudas_tectels_tm character varying(20),
    bermudas_coton_tm character varying(20),
    tenis_tm character varying(20),
    ref_cod_escola integer,
    camiseta_infantil_qtd smallint,
    camiseta_infantil_tm character varying(20)
);

CREATE TABLE pmieducar.escola (
    cod_escola integer DEFAULT nextval('pmieducar.escola_cod_escola_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola_rede_ensino integer NOT NULL,
    ref_idpes integer,
    sigla character varying(20) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    bloquear_lancamento_diario_anos_letivos_encerrados integer,
    situacao_funcionamento integer DEFAULT 1,
    dependencia_administrativa integer DEFAULT 3,
    regulamentacao integer DEFAULT 1,
    longitude character varying(20),
    latitude character varying(20),
    acesso integer,
    ref_idpes_gestor integer,
    cargo_gestor integer,
    local_funcionamento integer,
    condicao integer DEFAULT 1,
    codigo_inep_escola_compartilhada integer,
    decreto_criacao character varying(50),
    area_terreno_total character varying(10),
    area_construida character varying(10),
    area_disponivel character varying(10),
    num_pavimentos integer,
    tipo_piso integer,
    medidor_energia integer,
    agua_consumida integer,
    dependencia_sala_diretoria integer,
    dependencia_sala_professores integer,
    dependencia_sala_secretaria integer,
    dependencia_laboratorio_informatica integer,
    dependencia_laboratorio_ciencias integer,
    dependencia_sala_aee integer,
    dependencia_quadra_coberta integer,
    dependencia_quadra_descoberta integer,
    dependencia_cozinha integer,
    dependencia_biblioteca integer,
    dependencia_sala_leitura integer,
    dependencia_parque_infantil integer,
    dependencia_bercario integer,
    dependencia_banheiro_fora integer,
    dependencia_banheiro_dentro integer,
    dependencia_banheiro_infantil integer,
    dependencia_banheiro_deficiente integer,
    dependencia_banheiro_chuveiro integer,
    dependencia_refeitorio integer,
    dependencia_dispensa integer,
    dependencia_aumoxarifado integer,
    dependencia_auditorio integer,
    dependencia_patio_coberto integer,
    dependencia_patio_descoberto integer,
    dependencia_alojamento_aluno integer,
    dependencia_alojamento_professor integer,
    dependencia_area_verde integer,
    dependencia_lavanderia integer,
    dependencia_unidade_climatizada integer,
    dependencia_quantidade_ambiente_climatizado integer,
    dependencia_nenhuma_relacionada integer,
    dependencia_numero_salas_existente integer,
    dependencia_numero_salas_utilizadas integer,
    porte_quadra_descoberta integer,
    porte_quadra_coberta integer,
    tipo_cobertura_patio integer,
    total_funcionario integer,
    atendimento_aee integer DEFAULT 0,
    atividade_complementar integer DEFAULT 0,
    fundamental_ciclo integer,
    localizacao_diferenciada integer DEFAULT 7,
    didatico_nao_utiliza integer,
    didatico_quilombola integer,
    didatico_indigena integer,
    educacao_indigena integer,
    lingua_ministrada integer,
    espaco_brasil_aprendizado integer,
    abre_final_semana integer,
    codigo_lingua_indigena integer,
    proposta_pedagogica integer,
    televisoes smallint,
    videocassetes smallint,
    dvds smallint,
    antenas_parabolicas smallint,
    copiadoras smallint,
    retroprojetores smallint,
    impressoras smallint,
    aparelhos_de_som smallint,
    projetores_digitais smallint,
    faxs smallint,
    maquinas_fotograficas smallint,
    computadores smallint,
    computadores_administrativo smallint,
    computadores_alunos smallint,
    acesso_internet smallint,
    ato_criacao character varying(255),
    dependencia_vias_deficiente smallint,
    utiliza_regra_diferenciada boolean,
    ato_autorizativo character varying(255),
    ref_idpes_secretario_escolar integer,
    impressoras_multifuncionais smallint,
    categoria_escola_privada integer,
    conveniada_com_poder_publico integer,
    cnpj_mantenedora_principal numeric(14,0),
    mantenedora_escola_privada integer[],
    materiais_didaticos_especificos integer,
    abastecimento_agua integer[],
    abastecimento_energia integer[],
    esgoto_sanitario integer[],
    destinacao_lixo integer[],
    email_gestor character varying(255),
    zona_localizacao smallint,
    codigo_inep_escola_compartilhada2 integer,
    codigo_inep_escola_compartilhada3 integer,
    codigo_inep_escola_compartilhada4 integer,
    codigo_inep_escola_compartilhada5 integer,
    codigo_inep_escola_compartilhada6 integer
);

CREATE TABLE pmieducar.escola_ano_letivo (
    ref_cod_escola integer NOT NULL,
    ano integer NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    andamento smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    turmas_por_ano smallint
);

CREATE TABLE pmieducar.escola_complemento (
    ref_cod_escola integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    cep numeric(8,0) NOT NULL,
    numero numeric(6,0),
    complemento character varying(50),
    email character varying(50),
    nm_escola character varying(255) NOT NULL,
    municipio character varying(60) NOT NULL,
    bairro character varying(40) NOT NULL,
    logradouro character varying(150) NOT NULL,
    ddd_telefone numeric(2,0),
    telefone numeric(11,0),
    ddd_fax numeric(2,0),
    fax numeric(11,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.escola_curso (
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    autorizacao character varying(255),
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);

CREATE TABLE pmieducar.escola_localizacao (
    cod_escola_localizacao integer DEFAULT nextval('pmieducar.escola_localizacao_cod_escola_localizacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_localizacao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.escola_rede_ensino (
    cod_escola_rede_ensino integer DEFAULT nextval('pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_rede character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.escola_serie (
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    hora_inicial time without time zone,
    hora_final time without time zone,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    hora_inicio_intervalo time without time zone,
    hora_fim_intervalo time without time zone,
    bloquear_enturmacao_sem_vagas integer,
    bloquear_cadastro_turma_para_serie_com_vagas integer,
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.escola_serie_disciplina (
    ref_ref_cod_serie integer NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    carga_horaria numeric(7,3),
    etapas_especificas smallint,
    etapas_utilizadas character varying,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    anos_letivos smallint[] DEFAULT '{}'::smallint[] NOT NULL
);

CREATE TABLE pmieducar.escola_usuario (
    id integer NOT NULL,
    ref_cod_usuario integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    escola_atual integer
);

ALTER SEQUENCE pmieducar.escola_usuario_id_seq OWNED BY pmieducar.escola_usuario.id;

SET default_with_oids = true;

CREATE TABLE pmieducar.exemplar (
    cod_exemplar integer DEFAULT nextval('pmieducar.exemplar_cod_exemplar_seq'::regclass) NOT NULL,
    ref_cod_fonte integer NOT NULL,
    ref_cod_motivo_baixa integer,
    ref_cod_acervo integer NOT NULL,
    ref_cod_situacao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    permite_emprestimo smallint DEFAULT (1)::smallint NOT NULL,
    preco double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_aquisicao timestamp without time zone,
    tombo integer,
    sequencial integer,
    data_baixa_exemplar date
);

CREATE TABLE pmieducar.exemplar_emprestimo (
    cod_emprestimo integer DEFAULT nextval('pmieducar.exemplar_emprestimo_cod_emprestimo_seq'::regclass) NOT NULL,
    ref_usuario_devolucao integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    ref_cod_exemplar integer NOT NULL,
    data_retirada timestamp without time zone NOT NULL,
    data_devolucao timestamp without time zone,
    valor_multa double precision
);

CREATE TABLE pmieducar.exemplar_tipo (
    cod_exemplar_tipo integer DEFAULT nextval('pmieducar.exemplar_tipo_cod_exemplar_tipo_seq'::regclass) NOT NULL,
    ref_cod_biblioteca integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.falta_aluno (
    cod_falta_aluno integer DEFAULT nextval('pmieducar.falta_aluno_cod_falta_aluno_seq'::regclass) NOT NULL,
    ref_cod_disciplina integer,
    ref_cod_escola integer,
    ref_cod_serie integer,
    ref_cod_matricula integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    faltas integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    modulo smallint NOT NULL,
    ref_cod_curso_disciplina integer
);

SET default_with_oids = true;

CREATE TABLE pmieducar.falta_atraso (
    cod_falta_atraso integer DEFAULT nextval('pmieducar.falta_atraso_cod_falta_atraso_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    tipo smallint NOT NULL,
    data_falta_atraso timestamp without time zone NOT NULL,
    qtd_horas integer,
    qtd_min integer,
    justificada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.falta_atraso_compensado (
    cod_compensado integer DEFAULT nextval('pmieducar.falta_atraso_compensado_cod_compensado_seq'::regclass) NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_inicio timestamp without time zone NOT NULL,
    data_fim timestamp without time zone NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.faltas (
    ref_cod_matricula integer NOT NULL,
    sequencial integer DEFAULT nextval('pmieducar.faltas_sequencial_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    falta integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.fonte (
    cod_fonte integer DEFAULT nextval('pmieducar.fonte_cod_fonte_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_fonte character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.funcao (
    cod_funcao integer DEFAULT nextval('pmieducar.funcao_cod_funcao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_funcao character varying(255) NOT NULL,
    abreviatura character varying(30) NOT NULL,
    professor smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.habilitacao (
    cod_habilitacao integer DEFAULT nextval('pmieducar.habilitacao_cod_habilitacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.habilitacao_curso (
    ref_cod_habilitacao integer NOT NULL,
    ref_cod_curso integer NOT NULL
);

CREATE TABLE pmieducar.historico_disciplinas (
    sequencial integer NOT NULL,
    ref_ref_cod_aluno integer NOT NULL,
    ref_sequencial integer NOT NULL,
    nm_disciplina text NOT NULL,
    nota character varying(255) NOT NULL,
    faltas integer,
    import numeric(1,0),
    ordenamento integer,
    carga_horaria_disciplina integer,
    dependencia boolean DEFAULT false
);

SET default_with_oids = false;

CREATE TABLE pmieducar.historico_educar (
    tabela character varying(50),
    alteracao text,
    data timestamp without time zone,
    insercao smallint DEFAULT 0
);

SET default_with_oids = true;

CREATE TABLE pmieducar.historico_escolar (
    ref_cod_aluno integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ano integer NOT NULL,
    carga_horaria double precision,
    dias_letivos integer,
    escola character varying(255) NOT NULL,
    escola_cidade character varying(255) NOT NULL,
    escola_uf character varying(3),
    observacao text,
    aprovado smallint DEFAULT (1)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    faltas_globalizadas integer,
    nm_serie character varying(255),
    origem smallint DEFAULT (1)::smallint,
    extra_curricular smallint DEFAULT (0)::smallint,
    ref_cod_matricula integer,
    ref_cod_instituicao integer,
    import numeric(1,0),
    frequencia numeric(5,2) DEFAULT 0.000,
    registro character varying(50),
    livro character varying(50),
    folha character varying(50),
    historico_grade_curso_id integer,
    nm_curso character varying(255),
    aceleracao integer,
    ref_cod_escola integer,
    dependencia boolean,
    posicao integer
);

CREATE TABLE pmieducar.historico_grade_curso (
    id integer DEFAULT nextval('pmieducar.historico_grade_curso_seq'::regclass) NOT NULL,
    descricao_etapa character varying(20) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    quantidade_etapas integer,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.infra_comodo_funcao (
    cod_infra_comodo_funcao integer DEFAULT nextval('pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_funcao character varying(255) NOT NULL,
    desc_funcao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_escola integer
);

CREATE TABLE pmieducar.infra_predio (
    cod_infra_predio integer DEFAULT nextval('pmieducar.infra_predio_cod_infra_predio_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    nm_predio character varying(255) NOT NULL,
    desc_predio text,
    endereco text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.infra_predio_comodo (
    cod_infra_predio_comodo integer DEFAULT nextval('pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_infra_comodo_funcao integer NOT NULL,
    ref_cod_infra_predio integer NOT NULL,
    nm_comodo character varying(255) NOT NULL,
    desc_comodo text,
    area double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.instituicao (
    cod_instituicao integer DEFAULT nextval('pmieducar.instituicao_cod_instituicao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_idtlog character varying(20) NOT NULL,
    ref_sigla_uf character(2) NOT NULL,
    cep numeric(8,0) NOT NULL,
    cidade character varying(60) NOT NULL,
    bairro character varying(40) NOT NULL,
    logradouro character varying(255) NOT NULL,
    numero numeric(6,0),
    complemento character varying(50),
    nm_responsavel character varying(255) NOT NULL,
    ddd_telefone numeric(2,0),
    telefone numeric(11,0),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_instituicao character varying(255) NOT NULL,
    data_base_remanejamento date,
    data_base_transferencia date,
    controlar_espaco_utilizacao_aluno smallint,
    percentagem_maxima_ocupacao_salas numeric(5,2),
    quantidade_alunos_metro_quadrado integer,
    exigir_vinculo_turma_professor smallint,
    gerar_historico_transferencia boolean,
    matricula_apenas_bairro_escola boolean,
    restringir_historico_escolar boolean,
    coordenador_transporte character varying,
    restringir_multiplas_enturmacoes boolean,
    permissao_filtro_abandono_transferencia boolean,
    data_base_matricula date,
    multiplas_reserva_vaga boolean DEFAULT false NOT NULL,
    reserva_integral_somente_com_renda boolean DEFAULT false NOT NULL,
    data_expiracao_reserva_vaga date,
    data_fechamento date,
    componente_curricular_turma boolean,
    reprova_dependencia_ano_concluinte boolean,
    controlar_posicao_historicos boolean,
    data_educacenso date,
    bloqueia_matricula_serie_nao_seguinte boolean,
    permitir_carga_horaria boolean DEFAULT false,
    exigir_dados_socioeconomicos boolean DEFAULT false,
    altera_atestado_para_declaracao boolean,
    orgao_regional integer,
    obrigar_campos_censo boolean,
    obrigar_documento_pessoa boolean DEFAULT false,
    exigir_lancamentos_anteriores boolean DEFAULT false,
    exibir_apenas_professores_alocados boolean DEFAULT false
);

COMMENT ON COLUMN pmieducar.instituicao.exibir_apenas_professores_alocados IS 'Para filtros de emissão de relatórios';

SET default_with_oids = false;

CREATE TABLE pmieducar.instituicao_documentacao (
    id integer DEFAULT nextval('pmieducar.instituicao_documentacao_seq'::regclass) NOT NULL,
    instituicao_id integer NOT NULL,
    titulo_documento character varying(100) NOT NULL,
    url_documento character varying(255) NOT NULL,
    ref_usuario_cad integer DEFAULT 0 NOT NULL,
    ref_cod_escola integer
);

SET default_with_oids = true;

CREATE TABLE pmieducar.material_didatico (
    cod_material_didatico integer DEFAULT nextval('pmieducar.material_didatico_cod_material_didatico_seq'::regclass) NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_material_tipo integer NOT NULL,
    nm_material character varying(255) NOT NULL,
    desc_material text,
    custo_unitario double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.material_tipo (
    cod_material_tipo integer DEFAULT nextval('pmieducar.material_tipo_cod_material_tipo_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_usuario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.matricula (
    cod_matricula integer DEFAULT nextval('pmieducar.matricula_cod_matricula_seq'::regclass) NOT NULL,
    ref_cod_reserva_vaga integer,
    ref_ref_cod_escola integer,
    ref_ref_cod_serie integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_aluno integer NOT NULL,
    aprovado smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ano integer NOT NULL,
    ultima_matricula smallint DEFAULT (0)::smallint NOT NULL,
    modulo smallint DEFAULT 1 NOT NULL,
    descricao_reclassificacao text,
    formando smallint DEFAULT (0)::smallint NOT NULL,
    matricula_reclassificacao smallint DEFAULT (0)::smallint,
    ref_cod_curso integer,
    matricula_transferencia boolean DEFAULT false NOT NULL,
    semestre smallint,
    observacao character varying(300),
    data_matricula timestamp without time zone,
    data_cancel timestamp without time zone,
    ref_cod_abandono_tipo integer,
    turno_pre_matricula smallint,
    dependencia boolean DEFAULT false,
    updated_at timestamp without time zone,
    saida_escola boolean DEFAULT false,
    data_saida_escola date,
    turno_id integer
);

SET default_with_oids = false;

CREATE TABLE pmieducar.matricula_excessao (
    cod_aluno_excessao integer DEFAULT nextval('pmieducar.matricula_excessao_cod_aluno_excessao_seq'::regclass) NOT NULL,
    ref_cod_matricula integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    ref_sequencial integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    reprovado_faltas boolean NOT NULL,
    precisa_exame boolean NOT NULL,
    permite_exame boolean
);

SET default_with_oids = true;

CREATE TABLE pmieducar.matricula_ocorrencia_disciplinar (
    ref_cod_matricula integer NOT NULL,
    ref_cod_tipo_ocorrencia_disciplinar integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    observacao text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    visivel_pais integer,
    cod_ocorrencia_disciplinar integer DEFAULT nextval('pmieducar.ocorrencia_disciplinar_seq'::regclass) NOT NULL
);

CREATE TABLE pmieducar.matricula_turma (
    ref_cod_matricula integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    sequencial integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_enturmacao date NOT NULL,
    sequencial_fechamento integer DEFAULT 0 NOT NULL,
    transferido boolean,
    remanejado boolean,
    reclassificado boolean,
    abandono boolean,
    updated_at timestamp without time zone,
    falecido boolean,
    etapa_educacenso smallint,
    turma_unificada smallint
);

CREATE TABLE pmieducar.menu_tipo_usuario (
    ref_cod_tipo_usuario integer NOT NULL,
    ref_cod_menu_submenu integer NOT NULL,
    cadastra smallint DEFAULT 0 NOT NULL,
    visualiza smallint DEFAULT 0 NOT NULL,
    exclui smallint DEFAULT 0 NOT NULL
);

CREATE TABLE pmieducar.modulo (
    cod_modulo integer DEFAULT nextval('pmieducar.modulo_cod_modulo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    num_meses numeric(2,0) DEFAULT NULL::numeric,
    num_semanas integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    num_etapas numeric(2,0) DEFAULT 0 NOT NULL
);

CREATE TABLE pmieducar.motivo_afastamento (
    cod_motivo_afastamento integer DEFAULT nextval('pmieducar.motivo_afastamento_cod_motivo_afastamento_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.motivo_baixa (
    cod_motivo_baixa integer DEFAULT nextval('pmieducar.motivo_baixa_cod_motivo_baixa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo_baixa character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

CREATE TABLE pmieducar.motivo_suspensao (
    cod_motivo_suspensao integer DEFAULT nextval('pmieducar.motivo_suspensao_cod_motivo_suspensao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_motivo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer
);

SET default_with_oids = false;

CREATE TABLE pmieducar.nivel (
    cod_nivel integer DEFAULT nextval('pmieducar.nivel_cod_nivel_seq'::regclass) NOT NULL,
    ref_cod_categoria_nivel integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_nivel_anterior integer,
    nm_nivel character varying(100) NOT NULL,
    salario_base double precision,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.nivel_ensino (
    cod_nivel_ensino integer DEFAULT nextval('pmieducar.nivel_ensino_cod_nivel_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_nivel character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.nota_aluno (
    cod_nota_aluno integer DEFAULT nextval('pmieducar.nota_aluno_cod_nota_aluno_seq'::regclass) NOT NULL,
    ref_cod_disciplina integer,
    ref_cod_escola integer,
    ref_cod_serie integer,
    ref_cod_matricula integer NOT NULL,
    ref_sequencial integer,
    ref_ref_cod_tipo_avaliacao integer,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    modulo smallint NOT NULL,
    ref_cod_curso_disciplina integer,
    nota double precision
);

SET default_with_oids = true;

CREATE TABLE pmieducar.operador (
    cod_operador integer DEFAULT nextval('pmieducar.operador_cod_operador_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nome character varying(50) NOT NULL,
    valor text NOT NULL,
    fim_sentenca smallint DEFAULT (1)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.pagamento_multa (
    cod_pagamento_multa integer DEFAULT nextval('pmieducar.pagamento_multa_cod_pagamento_multa_seq'::regclass) NOT NULL,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    valor_pago double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);

CREATE TABLE pmieducar.pre_requisito (
    cod_pre_requisito integer DEFAULT nextval('pmieducar.pre_requisito_cod_pre_requisito_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    schema_ character varying(50) NOT NULL,
    tabela character varying(50) NOT NULL,
    nome character varying(50) NOT NULL,
    sql text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.projeto (
    cod_projeto integer DEFAULT nextval('pmieducar.projeto_seq'::regclass) NOT NULL,
    nome character varying(50),
    observacao character varying(255)
);

CREATE TABLE pmieducar.projeto_aluno (
    ref_cod_projeto integer NOT NULL,
    ref_cod_aluno integer NOT NULL,
    data_inclusao date,
    data_desligamento date,
    turno integer
);

CREATE TABLE pmieducar.quadro_horario (
    cod_quadro_horario integer DEFAULT nextval('pmieducar.quadro_horario_cod_quadro_horario_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_turma integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ano integer
);

SET default_with_oids = false;

CREATE TABLE pmieducar.quadro_horario_horarios (
    ref_cod_quadro_horario integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_instituicao_substituto integer,
    ref_cod_instituicao_servidor integer NOT NULL,
    ref_servidor_substituto integer,
    ref_servidor integer NOT NULL,
    dia_semana integer NOT NULL,
    hora_inicial time without time zone NOT NULL,
    hora_final time without time zone NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.quadro_horario_horarios_aux (
    ref_cod_quadro_horario integer NOT NULL,
    sequencial integer NOT NULL,
    ref_cod_disciplina integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_cod_instituicao_servidor integer NOT NULL,
    ref_servidor integer NOT NULL,
    dia_semana integer NOT NULL,
    hora_inicial time without time zone NOT NULL,
    hora_final time without time zone NOT NULL,
    identificador character varying(30),
    data_cadastro timestamp without time zone NOT NULL
);

CREATE TABLE pmieducar.quantidade_reserva_externa (
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    ref_turma_turno_id integer NOT NULL,
    ano integer NOT NULL,
    qtd_alunos integer NOT NULL
);

CREATE TABLE pmieducar.relacao_categoria_acervo (
    ref_cod_acervo integer NOT NULL,
    categoria_id integer NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.religiao (
    cod_religiao integer DEFAULT nextval('pmieducar.religiao_cod_religiao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_religiao character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.reserva_vaga (
    cod_reserva_vaga integer DEFAULT nextval('pmieducar.reserva_vaga_cod_reserva_vaga_seq'::regclass) NOT NULL,
    ref_ref_cod_escola integer NOT NULL,
    ref_ref_cod_serie integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_aluno integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    nm_aluno character varying(255),
    cpf_responsavel numeric(11,0)
);

CREATE TABLE pmieducar.reservas (
    cod_reserva integer DEFAULT nextval('pmieducar.reservas_cod_reserva_seq'::regclass) NOT NULL,
    ref_usuario_libera integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_cliente integer NOT NULL,
    data_reserva timestamp without time zone,
    data_prevista_disponivel timestamp without time zone,
    data_retirada timestamp without time zone,
    ref_cod_exemplar integer NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.sequencia_serie (
    ref_serie_origem integer NOT NULL,
    ref_serie_destino integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.serie (
    cod_serie integer DEFAULT nextval('pmieducar.serie_cod_serie_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    nm_serie character varying(255) NOT NULL,
    etapa_curso integer NOT NULL,
    concluinte smallint DEFAULT (0)::smallint NOT NULL,
    carga_horaria double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    intervalo integer,
    idade_inicial numeric(3,0),
    idade_final numeric(3,0),
    regra_avaliacao_id integer,
    observacao_historico character varying(100),
    dias_letivos integer,
    regra_avaliacao_diferenciada_id integer,
    alerta_faixa_etaria boolean,
    bloquear_matricula_faixa_etaria boolean,
    idade_ideal integer,
    exigir_inep boolean
);

CREATE TABLE pmieducar.serie_pre_requisito (
    ref_cod_pre_requisito integer NOT NULL,
    ref_cod_operador integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    valor character varying
);

SET default_with_oids = false;

CREATE TABLE pmieducar.serie_vaga (
    ano integer NOT NULL,
    cod_serie_vaga integer NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_curso integer NOT NULL,
    ref_cod_serie integer NOT NULL,
    vagas smallint NOT NULL,
    turno smallint DEFAULT 1 NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.servidor (
    cod_servidor integer NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    ref_idesco numeric(2,0),
    carga_horaria double precision NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_subnivel integer,
    situacao_curso_superior_1 smallint,
    formacao_complementacao_pedagogica_1 smallint,
    codigo_curso_superior_1 integer,
    ano_inicio_curso_superior_1 numeric(4,0),
    ano_conclusao_curso_superior_1 numeric(4,0),
    instituicao_curso_superior_1 smallint,
    situacao_curso_superior_2 smallint,
    formacao_complementacao_pedagogica_2 smallint,
    codigo_curso_superior_2 integer,
    ano_inicio_curso_superior_2 numeric(4,0),
    ano_conclusao_curso_superior_2 numeric(4,0),
    instituicao_curso_superior_2 smallint,
    situacao_curso_superior_3 smallint,
    formacao_complementacao_pedagogica_3 smallint,
    codigo_curso_superior_3 integer,
    ano_inicio_curso_superior_3 numeric(4,0),
    ano_conclusao_curso_superior_3 numeric(4,0),
    instituicao_curso_superior_3 smallint,
    multi_seriado boolean,
    pos_graduacao integer[],
    curso_formacao_continuada integer[]
);

CREATE TABLE pmieducar.servidor_afastamento (
    ref_cod_servidor integer NOT NULL,
    sequencial integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_motivo_afastamento integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    data_retorno timestamp without time zone,
    data_saida timestamp without time zone NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.servidor_alocacao (
    cod_servidor_alocacao integer DEFAULT nextval('pmieducar.servidor_alocacao_cod_servidor_alocacao_seq'::regclass) NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_escola integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    carga_horaria interval,
    periodo smallint DEFAULT (1)::smallint,
    hora_final time without time zone,
    hora_inicial time without time zone,
    dia_semana integer,
    ref_cod_servidor_funcao integer,
    ref_cod_funcionario_vinculo integer,
    ano integer,
    data_admissao date,
    hora_atividade time without time zone,
    horas_excedentes time without time zone
);

CREATE TABLE pmieducar.servidor_curso (
    cod_servidor_curso integer DEFAULT nextval('pmieducar.servidor_curso_cod_servidor_curso_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_conclusao timestamp without time zone NOT NULL,
    data_registro timestamp without time zone,
    diplomas_registros text
);

SET default_with_oids = false;

CREATE TABLE pmieducar.servidor_curso_ministra (
    ref_cod_curso integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL
);

CREATE TABLE pmieducar.servidor_disciplina (
    ref_cod_disciplina integer NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_cod_curso integer NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.servidor_formacao (
    cod_formacao integer DEFAULT nextval('pmieducar.servidor_formacao_cod_formacao_seq'::regclass) NOT NULL,
    ref_ref_cod_instituicao integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    nm_formacao character varying(255) NOT NULL,
    tipo character(1) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.servidor_funcao (
    ref_ref_cod_instituicao integer NOT NULL,
    ref_cod_servidor integer NOT NULL,
    ref_cod_funcao integer NOT NULL,
    matricula character varying,
    cod_servidor_funcao integer DEFAULT nextval('pmieducar.servidor_funcao_seq'::regclass) NOT NULL
);

SET default_with_oids = true;

CREATE TABLE pmieducar.servidor_titulo_concurso (
    cod_servidor_titulo integer DEFAULT nextval('pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq'::regclass) NOT NULL,
    ref_cod_formacao integer NOT NULL,
    data_vigencia_homolog timestamp without time zone NOT NULL,
    data_publicacao timestamp without time zone NOT NULL
);

CREATE TABLE pmieducar.situacao (
    cod_situacao integer DEFAULT nextval('pmieducar.situacao_cod_situacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_situacao character varying(255) NOT NULL,
    permite_emprestimo smallint DEFAULT (1)::smallint NOT NULL,
    descricao text,
    situacao_padrao smallint DEFAULT (0)::smallint NOT NULL,
    situacao_emprestada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_biblioteca integer NOT NULL
);

SET default_with_oids = false;

CREATE TABLE pmieducar.subnivel (
    cod_subnivel integer DEFAULT nextval('pmieducar.subnivel_cod_subnivel_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_subnivel_anterior integer,
    ref_cod_nivel integer NOT NULL,
    nm_subnivel character varying(100),
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo boolean DEFAULT true NOT NULL,
    salario double precision NOT NULL
);

CREATE TABLE pmieducar.tipo_autor (
    codigo integer,
    tipo_autor character varying(255)
);

SET default_with_oids = true;

CREATE TABLE pmieducar.tipo_avaliacao (
    cod_tipo_avaliacao integer DEFAULT nextval('pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    conceitual smallint DEFAULT 1,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.tipo_avaliacao_valores (
    ref_cod_tipo_avaliacao integer NOT NULL,
    sequencial integer NOT NULL,
    nome character varying(255) NOT NULL,
    valor double precision NOT NULL,
    valor_min double precision NOT NULL,
    valor_max double precision NOT NULL,
    ativo boolean DEFAULT true
);

CREATE TABLE pmieducar.tipo_dispensa (
    cod_tipo_dispensa integer DEFAULT nextval('pmieducar.tipo_dispensa_cod_tipo_dispensa_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.tipo_ensino (
    cod_tipo_ensino integer DEFAULT nextval('pmieducar.tipo_ensino_cod_tipo_ensino_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL,
    atividade_complementar boolean DEFAULT false
);

CREATE TABLE pmieducar.tipo_ocorrencia_disciplinar (
    cod_tipo_ocorrencia_disciplinar integer DEFAULT nextval('pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    max_ocorrencias integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.tipo_regime (
    cod_tipo_regime integer DEFAULT nextval('pmieducar.tipo_regime_cod_tipo_regime_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint NOT NULL,
    ref_cod_instituicao integer NOT NULL
);

CREATE TABLE pmieducar.tipo_usuario (
    cod_tipo_usuario integer DEFAULT nextval('pmieducar.tipo_usuario_cod_tipo_usuario_seq'::regclass) NOT NULL,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    nm_tipo character varying(255) NOT NULL,
    descricao text,
    nivel integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.transferencia_solicitacao (
    cod_transferencia_solicitacao integer DEFAULT nextval('pmieducar.transferencia_solicitacao_cod_transferencia_solicitacao_seq'::regclass) NOT NULL,
    ref_cod_transferencia_tipo integer NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_cod_matricula_entrada integer,
    ref_cod_matricula_saida integer NOT NULL,
    observacao text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    data_transferencia timestamp without time zone,
    ref_cod_escola_destino integer,
    escola_destino_externa character varying,
    estado_escola_destino_externa character varying(60),
    municipio_escola_destino_externa character varying(60)
);

CREATE TABLE pmieducar.transferencia_tipo (
    cod_transferencia_tipo integer DEFAULT nextval('pmieducar.transferencia_tipo_cod_transferencia_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    desc_tipo text,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer
);

CREATE TABLE pmieducar.turma (
    cod_turma integer DEFAULT nextval('pmieducar.turma_cod_turma_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    ref_ref_cod_serie integer,
    ref_ref_cod_escola integer,
    ref_cod_infra_predio_comodo integer,
    nm_turma character varying(255) NOT NULL,
    sgl_turma character varying(15),
    max_aluno integer NOT NULL,
    multiseriada smallint DEFAULT (0)::smallint NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_turma_tipo integer NOT NULL,
    hora_inicial time without time zone,
    hora_final time without time zone,
    hora_inicio_intervalo time without time zone,
    hora_fim_intervalo time without time zone,
    ref_cod_regente integer,
    ref_cod_instituicao_regente integer,
    ref_cod_instituicao integer,
    ref_cod_curso integer,
    ref_ref_cod_serie_mult integer,
    ref_ref_cod_escola_mult integer,
    visivel boolean,
    tipo_boletim integer,
    turma_turno_id integer,
    ano integer,
    tipo_atendimento smallint,
    turma_mais_educacao smallint,
    atividade_complementar_1 integer,
    atividade_complementar_2 integer,
    atividade_complementar_3 integer,
    atividade_complementar_4 integer,
    atividade_complementar_5 integer,
    atividade_complementar_6 integer,
    aee_braille smallint,
    aee_recurso_optico smallint,
    aee_estrategia_desenvolvimento smallint,
    aee_tecnica_mobilidade smallint,
    aee_libras smallint,
    aee_caa smallint,
    aee_curricular smallint,
    aee_soroban smallint,
    aee_informatica smallint,
    aee_lingua_escrita smallint,
    aee_autonomia smallint,
    cod_curso_profissional integer,
    etapa_educacenso smallint,
    ref_cod_disciplina_dispensada integer,
    parecer_1_etapa text,
    parecer_2_etapa text,
    parecer_3_etapa text,
    parecer_4_etapa text,
    nao_informar_educacenso smallint,
    tipo_mediacao_didatico_pedagogico integer,
    dias_semana integer[],
    atividades_complementares integer[],
    atividades_aee integer[],
    tipo_boletim_diferenciado smallint
);

CREATE TABLE pmieducar.turma_modulo (
    ref_cod_turma integer NOT NULL,
    ref_cod_modulo integer NOT NULL,
    sequencial integer NOT NULL,
    data_inicio date NOT NULL,
    data_fim date NOT NULL,
    dias_letivos integer
);

CREATE TABLE pmieducar.turma_tipo (
    cod_turma_tipo integer DEFAULT nextval('pmieducar.turma_tipo_cod_turma_tipo_seq'::regclass) NOT NULL,
    ref_usuario_exc integer,
    ref_usuario_cad integer NOT NULL,
    nm_tipo character varying(255) NOT NULL,
    sgl_tipo character varying(15) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_instituicao integer
);

CREATE TABLE pmieducar.turma_turno (
    id integer DEFAULT nextval('pmieducar.turma_turno_id_seq'::regclass) NOT NULL,
    nome character varying(15) NOT NULL,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmieducar.usuario (
    cod_usuario integer NOT NULL,
    ref_cod_instituicao integer,
    ref_funcionario_cad integer NOT NULL,
    ref_funcionario_exc integer,
    ref_cod_tipo_usuario integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmiotopic.funcionario_su (
    ref_ref_cod_pessoa_fj integer NOT NULL
);

CREATE TABLE pmiotopic.grupomoderador (
    ref_ref_cod_pessoa_fj integer NOT NULL,
    ref_cod_grupos integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmiotopic.grupopessoa (
    ref_idpes integer NOT NULL,
    ref_cod_grupos integer NOT NULL,
    ref_grupos_exc integer,
    ref_pessoa_exc integer,
    ref_grupos_cad integer,
    ref_pessoa_cad integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_cod_auxiliar_cad integer,
    ref_ref_cod_atendimento_cad integer
);

CREATE TABLE pmiotopic.grupos (
    cod_grupos integer DEFAULT nextval('pmiotopic.grupos_cod_grupos_seq'::regclass) NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nm_grupo character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    atendimento smallint DEFAULT 0 NOT NULL
);

CREATE TABLE pmiotopic.notas (
    sequencial integer NOT NULL,
    ref_idpes integer NOT NULL,
    ref_pessoa_exc integer,
    ref_pessoa_cad integer NOT NULL,
    nota text NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL
);

CREATE TABLE pmiotopic.participante (
    sequencial integer NOT NULL,
    ref_ref_cod_grupos integer NOT NULL,
    ref_ref_idpes integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    data_chegada timestamp without time zone NOT NULL,
    data_saida timestamp without time zone
);

CREATE TABLE pmiotopic.reuniao (
    cod_reuniao integer DEFAULT nextval('pmiotopic.reuniao_cod_reuniao_seq'::regclass) NOT NULL,
    ref_grupos_moderador integer NOT NULL,
    ref_moderador integer NOT NULL,
    data_inicio_marcado timestamp without time zone NOT NULL,
    data_fim_marcado timestamp without time zone NOT NULL,
    data_inicio_real timestamp without time zone,
    data_fim_real timestamp without time zone,
    descricao text NOT NULL,
    email_enviado timestamp without time zone,
    publica smallint DEFAULT 0 NOT NULL
);

CREATE TABLE pmiotopic.topico (
    cod_topico integer DEFAULT nextval('pmiotopic.topico_cod_topico_seq'::regclass) NOT NULL,
    ref_idpes_cad integer NOT NULL,
    ref_cod_grupos_cad integer NOT NULL,
    assunto character varying(255) NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_exclusao timestamp without time zone,
    ativo smallint DEFAULT (1)::smallint NOT NULL,
    ref_idpes_exc integer,
    ref_cod_grupos_exc integer
);

CREATE TABLE pmiotopic.topicoreuniao (
    ref_cod_topico integer NOT NULL,
    ref_cod_reuniao integer NOT NULL,
    parecer text,
    finalizado smallint,
    data_parecer timestamp without time zone
);

CREATE TABLE portal.acesso (
    cod_acesso integer DEFAULT nextval('portal.acesso_cod_acesso_seq'::regclass) NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    ip_externo character varying(50) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255) DEFAULT ''::character varying NOT NULL,
    cod_pessoa integer DEFAULT 0 NOT NULL,
    obs text,
    sucesso boolean DEFAULT true NOT NULL
);

CREATE TABLE portal.agenda (
    cod_agenda integer DEFAULT nextval('portal.agenda_cod_agenda_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_exc integer,
    ref_ref_cod_pessoa_cad integer NOT NULL,
    nm_agenda character varying NOT NULL,
    publica smallint DEFAULT 0 NOT NULL,
    envia_alerta smallint DEFAULT 0 NOT NULL,
    data_cad timestamp without time zone NOT NULL,
    data_edicao timestamp without time zone,
    ref_ref_cod_pessoa_own integer
);

CREATE TABLE portal.agenda_compromisso (
    cod_agenda_compromisso integer NOT NULL,
    versao integer NOT NULL,
    ref_cod_agenda integer NOT NULL,
    ref_ref_cod_pessoa_cad integer NOT NULL,
    ativo smallint DEFAULT 1,
    data_inicio timestamp without time zone,
    titulo character varying,
    descricao text,
    importante smallint DEFAULT 0 NOT NULL,
    publico smallint DEFAULT 0 NOT NULL,
    data_cadastro timestamp without time zone NOT NULL,
    data_fim timestamp without time zone
);

CREATE TABLE portal.agenda_pref (
    cod_comp integer DEFAULT nextval('portal.agenda_pref_cod_comp_seq'::regclass) NOT NULL,
    data_comp date NOT NULL,
    hora_comp time without time zone NOT NULL,
    hora_f_comp time without time zone NOT NULL,
    comp_comp text NOT NULL,
    local_comp character(1) DEFAULT 'I'::bpchar NOT NULL,
    publico_comp character(1) DEFAULT 'S'::bpchar NOT NULL,
    agenda_de character(1) DEFAULT 'P'::bpchar,
    ref_cad integer,
    versao integer DEFAULT 1 NOT NULL,
    ref_auto_cod integer
);

CREATE TABLE portal.agenda_responsavel (
    ref_cod_agenda integer NOT NULL,
    ref_ref_cod_pessoa_fj integer NOT NULL,
    principal smallint
);

CREATE TABLE portal.compras_editais_editais (
    cod_compras_editais_editais integer DEFAULT nextval('portal.compras_editais_editais_cod_compras_editais_editais_seq'::regclass) NOT NULL,
    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
    versao integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    motivo_alteracao text,
    visivel smallint DEFAULT 1 NOT NULL
);

CREATE TABLE portal.compras_editais_editais_empresas (
    ref_cod_compras_editais_editais integer DEFAULT 0 NOT NULL,
    ref_cod_compras_editais_empresa integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);

CREATE TABLE portal.compras_editais_empresa (
    cod_compras_editais_empresa integer DEFAULT nextval('portal.compras_editais_empresa_cod_compras_editais_empresa_seq'::regclass) NOT NULL,
    cnpj character varying(20) DEFAULT ''::character varying NOT NULL,
    nm_empresa character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    endereco text,
    ref_sigla_uf character(2),
    cidade character varying(255),
    bairro character varying(255),
    telefone bigint,
    fax bigint,
    cep bigint,
    nome_contato character varying(255),
    senha character varying(32) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.compras_final_pregao (
    cod_compras_final_pregao integer DEFAULT nextval('portal.compras_final_pregao_cod_compras_final_pregao_seq'::regclass) NOT NULL,
    nm_final character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.compras_funcionarios (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.compras_licitacoes (
    cod_compras_licitacoes integer DEFAULT nextval('portal.compras_licitacoes_cod_compras_licitacoes_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    ref_cod_compras_modalidade integer DEFAULT 0 NOT NULL,
    numero character varying(30) DEFAULT ''::character varying NOT NULL,
    objeto text NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    cod_licitacao_semasa integer,
    oculto boolean DEFAULT false
);

CREATE TABLE portal.compras_modalidade (
    cod_compras_modalidade integer DEFAULT nextval('portal.compras_modalidade_cod_compras_modalidade_seq'::regclass) NOT NULL,
    nm_modalidade character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.compras_pregao_execucao (
    cod_compras_pregao_execucao integer DEFAULT nextval('portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq'::regclass) NOT NULL,
    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
    ref_pregoeiro integer DEFAULT 0 NOT NULL,
    ref_equipe1 integer DEFAULT 0 NOT NULL,
    ref_equipe2 integer DEFAULT 0 NOT NULL,
    ref_equipe3 integer DEFAULT 0 NOT NULL,
    ano_processo integer,
    mes_processo integer,
    seq_processo integer,
    seq_portaria integer,
    ano_portaria integer,
    valor_referencia double precision,
    valor_real double precision,
    ref_cod_compras_final_pregao integer
);

CREATE TABLE portal.compras_prestacao_contas (
    cod_compras_prestacao_contas integer DEFAULT nextval('portal.compras_prestacao_contas_cod_compras_prestacao_contas_seq'::regclass) NOT NULL,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    mes integer DEFAULT 0 NOT NULL,
    ano integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.foto_portal (
    cod_foto_portal integer DEFAULT nextval('portal.foto_portal_cod_foto_portal_seq'::regclass) NOT NULL,
    ref_cod_foto_secao integer,
    ref_cod_credito integer,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_foto timestamp without time zone,
    titulo character varying(255),
    descricao text,
    caminho character varying(255),
    altura integer,
    largura integer,
    nm_credito character varying(255),
    bkp_ref_secao bigint
);

CREATE TABLE portal.foto_secao (
    cod_foto_secao integer DEFAULT nextval('portal.foto_secao_cod_foto_secao_seq'::regclass) NOT NULL,
    nm_secao character varying(255)
);

CREATE TABLE portal.funcionario (
    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    matricula character varying(12),
    senha character varying(32),
    ativo smallint,
    ref_sec integer,
    ramal character varying(10),
    sequencial character(3),
    opcao_menu text,
    ref_cod_setor integer,
    ref_cod_funcionario_vinculo integer,
    tempo_expira_senha integer,
    tempo_expira_conta integer,
    data_troca_senha date,
    data_reativa_conta date,
    ref_ref_cod_pessoa_fj integer,
    proibido integer DEFAULT 0 NOT NULL,
    ref_cod_setor_new integer,
    matricula_new bigint,
    matricula_permanente smallint DEFAULT 0,
    tipo_menu smallint DEFAULT 0 NOT NULL,
    ip_logado character varying(50),
    data_login timestamp without time zone,
    email character varying(50),
    status_token character varying(50),
    matricula_interna character varying(30),
    receber_novidades smallint,
    atualizou_cadastro smallint
);

CREATE TABLE portal.funcionario_vinculo (
    cod_funcionario_vinculo integer DEFAULT nextval('portal.funcionario_vinculo_cod_funcionario_vinculo_seq'::regclass) NOT NULL,
    nm_vinculo character varying(255) DEFAULT ''::character varying NOT NULL,
    abreviatura character varying(16)
);

CREATE TABLE portal.imagem (
    cod_imagem integer DEFAULT nextval('portal.imagem_cod_imagem_seq'::regclass) NOT NULL,
    ref_cod_imagem_tipo integer NOT NULL,
    caminho character varying(255) NOT NULL,
    nm_imagem character varying(100),
    extensao character(3) NOT NULL,
    altura integer,
    largura integer,
    data_cadastro timestamp without time zone NOT NULL,
    ref_cod_pessoa_cad integer NOT NULL,
    data_exclusao timestamp without time zone,
    ref_cod_pessoa_exc integer
);

CREATE TABLE portal.imagem_tipo (
    cod_imagem_tipo integer DEFAULT nextval('portal.imagem_tipo_cod_imagem_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(100) NOT NULL
);

CREATE TABLE portal.intranet_segur_permissao_negada (
    cod_intranet_segur_permissao_negada integer DEFAULT nextval('portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer,
    ip_externo character varying(15) DEFAULT ''::character varying NOT NULL,
    ip_interno character varying(255),
    data_hora timestamp without time zone NOT NULL,
    pagina character varying(255),
    variaveis text
);

CREATE TABLE portal.jor_arquivo (
    ref_cod_jor_edicao integer DEFAULT 0 NOT NULL,
    jor_arquivo smallint DEFAULT (0)::smallint NOT NULL,
    jor_caminho character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.jor_edicao (
    cod_jor_edicao integer DEFAULT nextval('portal.jor_edicao_cod_jor_edicao_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    jor_ano_edicao character varying(5) DEFAULT ''::character varying NOT NULL,
    jor_edicao integer DEFAULT 0 NOT NULL,
    jor_dt_inicial date NOT NULL,
    jor_dt_final date,
    jor_extra smallint DEFAULT (0)::smallint
);

CREATE TABLE portal.mailling_email (
    cod_mailling_email integer DEFAULT nextval('portal.mailling_email_cod_mailling_email_seq'::regclass) NOT NULL,
    nm_pessoa character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.mailling_email_conteudo (
    cod_mailling_email_conteudo integer DEFAULT nextval('portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    conteudo text NOT NULL,
    nm_remetente character varying(255),
    email_remetente character varying(255),
    assunto character varying(255)
);

CREATE TABLE portal.mailling_fila_envio (
    cod_mailling_fila_envio integer DEFAULT nextval('portal.mailling_fila_envio_cod_mailling_fila_envio_seq'::regclass) NOT NULL,
    ref_cod_mailling_email_conteudo integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_email integer,
    ref_ref_cod_pessoa_fj integer,
    data_cadastro timestamp without time zone NOT NULL,
    data_envio timestamp without time zone
);

CREATE TABLE portal.mailling_grupo (
    cod_mailling_grupo integer DEFAULT nextval('portal.mailling_grupo_cod_mailling_grupo_seq'::regclass) NOT NULL,
    nm_grupo character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.mailling_grupo_email (
    ref_cod_mailling_email integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.mailling_historico (
    cod_mailling_historico integer DEFAULT nextval('portal.mailling_historico_cod_mailling_historico_seq'::regclass) NOT NULL,
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    data_hora timestamp without time zone NOT NULL
);

CREATE TABLE portal.menu_funcionario (
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    cadastra smallint DEFAULT (0)::smallint NOT NULL,
    exclui smallint DEFAULT (0)::smallint NOT NULL,
    ref_cod_menu_submenu integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.menu_menu (
    cod_menu_menu integer DEFAULT nextval('portal.menu_menu_cod_menu_menu_seq'::regclass) NOT NULL,
    nm_menu character varying(255) DEFAULT ''::character varying NOT NULL,
    title character varying(255),
    ref_cod_menu_pai integer,
    caminho character varying(255) DEFAULT '#'::character varying,
    ord_menu integer DEFAULT 9999,
    ativo boolean DEFAULT true,
    icon_class character varying(20)
);

CREATE TABLE portal.menu_submenu (
    cod_menu_submenu integer DEFAULT nextval('portal.menu_submenu_cod_menu_submenu_seq'::regclass) NOT NULL,
    ref_cod_menu_menu integer,
    cod_sistema integer,
    nm_submenu character varying(255) DEFAULT ''::character varying NOT NULL,
    arquivo character varying(255) DEFAULT ''::character varying NOT NULL,
    title text,
    nivel smallint DEFAULT (3)::smallint NOT NULL
);

CREATE TABLE portal.not_portal (
    cod_not_portal integer DEFAULT nextval('portal.not_portal_cod_not_portal_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    titulo character varying(255),
    descricao text,
    data_noticia timestamp without time zone NOT NULL
);

CREATE TABLE portal.not_portal_tipo (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    ref_cod_not_tipo integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.not_tipo (
    cod_not_tipo integer DEFAULT nextval('portal.not_tipo_cod_not_tipo_seq'::regclass) NOT NULL,
    nm_tipo character varying(255) DEFAULT ''::character varying NOT NULL
);

CREATE TABLE portal.not_vinc_portal (
    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
    vic_num integer DEFAULT 0 NOT NULL,
    tipo character(1) DEFAULT 'F'::bpchar NOT NULL,
    cod_vinc integer,
    caminho character varying(255),
    nome_arquivo character varying(255)
);

CREATE TABLE portal.notificacao (
    cod_notificacao integer DEFAULT nextval('portal.notificacao_cod_notificacao_seq'::regclass) NOT NULL,
    ref_cod_funcionario integer NOT NULL,
    titulo character varying,
    conteudo text,
    data_hora_ativa timestamp without time zone,
    url character varying,
    visualizacoes smallint DEFAULT 0 NOT NULL
);

CREATE TABLE portal.pessoa_atividade (
    cod_pessoa_atividade integer DEFAULT nextval('portal.pessoa_atividade_cod_pessoa_atividade_seq'::regclass) NOT NULL,
    ref_cod_ramo_atividade integer DEFAULT 0 NOT NULL,
    nm_atividade character varying(255)
);

CREATE TABLE portal.pessoa_fj (
    cod_pessoa_fj integer DEFAULT nextval('portal.pessoa_fj_cod_pessoa_fj_seq'::regclass) NOT NULL,
    nm_pessoa character varying(255) DEFAULT ''::character varying NOT NULL,
    id_federal character varying(30),
    endereco text,
    cep character varying(9),
    ref_bairro integer,
    ddd_telefone_1 integer,
    telefone_1 character varying(15),
    ddd_telefone_2 integer,
    telefone_2 character varying(15),
    ddd_telefone_mov integer,
    telefone_mov character varying(15),
    ddd_telefone_fax integer,
    telefone_fax character varying(15),
    email character varying(255),
    http character varying(255),
    tipo_pessoa character(1) DEFAULT 'F'::bpchar NOT NULL,
    sexo smallint,
    razao_social character varying(255),
    ins_est character varying(30),
    ins_mun character varying(30),
    rg character varying(30),
    ref_cod_pessoa_pai integer,
    ref_cod_pessoa_mae integer,
    data_nasc date,
    ref_ref_cod_pessoa_fj integer
);

CREATE TABLE portal.pessoa_fj_pessoa_atividade (
    ref_cod_pessoa_atividade integer DEFAULT 0 NOT NULL,
    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
);

CREATE TABLE portal.pessoa_ramo_atividade (
    cod_ramo_atividade integer DEFAULT nextval('portal.pessoa_ramo_atividade_cod_ramo_atividade_seq'::regclass) NOT NULL,
    nm_ramo_atividade character varying(255)
);

CREATE TABLE portal.portal_concurso (
    cod_portal_concurso integer DEFAULT nextval('portal.portal_concurso_cod_portal_concurso_seq'::regclass) NOT NULL,
    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
    nm_concurso character varying(255) DEFAULT ''::character varying NOT NULL,
    descricao text,
    caminho character varying(255) DEFAULT ''::character varying NOT NULL,
    tipo_arquivo character(3) DEFAULT ''::bpchar NOT NULL,
    data_hora timestamp without time zone
);

CREATE TABLE portal.sistema (
    cod_sistema integer DEFAULT nextval('portal.sistema_cod_sistema_seq'::regclass) NOT NULL,
    nome character varying(255),
    versao smallint NOT NULL,
    release smallint NOT NULL,
    patch smallint NOT NULL,
    tipo character varying(255)
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
