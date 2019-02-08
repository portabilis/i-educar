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
