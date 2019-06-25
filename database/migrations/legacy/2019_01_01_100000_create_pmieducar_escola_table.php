<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarEscolaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET default_with_oids = true;
                
                CREATE SEQUENCE pmieducar.escola_cod_escola_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.escola (
                    cod_escola integer DEFAULT nextval(\'pmieducar.escola_cod_escola_seq\'::regclass) NOT NULL,
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
                    local_funcionamento int4[] NULL,
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
	                codigo_lingua_indigena int4[] NULL,
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
                    codigo_inep_escola_compartilhada6 integer,
	                orgao_vinculado_escola int4[] NULL,
	                esfera_administrativa int4 NULL,
	                unidade_vinculada_outra_instituicao int4 NULL,
	                inep_escola_sede int4 NULL,
	                codigo_ies int4 NULL,
	                predio_compartilhado_outra_escola int4 NULL,
                    agua_potavel_consumo int4 NULL,
                    tratamento_lixo int4[] NULL,
                    salas_gerais int4[] NULL,
                    salas_funcionais int4[] NULL,
                    banheiros int4[] NULL,
                    laboratorios int4[] NULL,
                    salas_atividades int4[] NULL,
                    dormitorios int4[] NULL,
                    areas_externas int4[] NULL,
                    recursos_acessibilidade int4[] NULL,
                    possui_dependencias int4 NULL,
                    numero_salas_utilizadas_dentro_predio int4 NULL,
                    numero_salas_utilizadas_fora_predio int4 NULL,
                    numero_salas_climatizadas int4 NULL,
                    numero_salas_acessibilidade int4 NULL,
                    qtd_secretario_escolar int4 NULL,
                    qtd_auxiliar_administrativo int4 NULL,
                    qtd_apoio_pedagogico int4 NULL,
                    qtd_coordenador_turno int4 NULL,
                    qtd_tecnicos int4 NULL,
                    qtd_bibliotecarios int4 NULL,
                    qtd_segurancas int4 NULL,
                    qtd_auxiliar_servicos_gerais int4 NULL,
                    qtd_nutricionistas int4 NULL,
                    qtd_profissionais_preparacao int4 NULL,
                    qtd_bombeiro int4 NULL,
                    qtd_psicologo int4 NULL,
                    qtd_fonoaudiologo int4 NULL,
                    alimentacao_escolar_alunos int4 NULL,
                    compartilha_espacos_atividades_integracao int4 NULL,
                    usa_espacos_equipamentos_atividades_regulares int4 NULL,
                    equipamentos int4[] NULL,
                    uso_internet int4[] NULL,
                    rede_local int4[] NULL,
                    equipamentos_acesso_internet int4[] NULL,
                    quantidade_computadores_alunos_mesa int4 NULL,
                    quantidade_computadores_alunos_portateis int4 NULL,
                    quantidade_computadores_alunos_tablets int4 NULL,
                    lousas_digitais int4 NULL,
                    organizacao_ensino int4[] NULL,
                    instrumentos_pedagogicos int4[] NULL,
                    orgaos_colegiados int4[] NULL,
                    exame_selecao_ingresso int4 NULL,
                    reserva_vagas_cotas int4[] NULL,
                    projeto_politico_pedagogico int4 NULL
                );
                
                ALTER TABLE ONLY pmieducar.escola
                    ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);

                CREATE INDEX i_escola_ativo ON pmieducar.escola USING btree (ativo);

                CREATE INDEX i_escola_ref_cod_escola_rede_ensino ON pmieducar.escola USING btree (ref_cod_escola_rede_ensino);

                CREATE INDEX i_escola_ref_cod_instituicao ON pmieducar.escola USING btree (ref_cod_instituicao);

                CREATE INDEX i_escola_ref_idpes ON pmieducar.escola USING btree (ref_idpes);

                CREATE INDEX i_escola_ref_usuario_cad ON pmieducar.escola USING btree (ref_usuario_cad);

                CREATE INDEX i_escola_sigla ON pmieducar.escola USING btree (sigla);

                SELECT pg_catalog.setval(\'pmieducar.escola_cod_escola_seq\', 1, true);
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmieducar.escola');

        DB::unprepared('DROP SEQUENCE pmieducar.escola_cod_escola_seq;');
    }
}
