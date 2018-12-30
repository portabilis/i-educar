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
