<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesRegraAvaliacaoTable extends Migration
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
                SET default_with_oids = false;
                
                CREATE SEQUENCE modules.regra_avaliacao_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.regra_avaliacao (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    formula_media_id integer NULL,
                    formula_recuperacao_id integer,
                    tabela_arredondamento_id integer,
                    nome character varying(50) NOT NULL,
                    tipo_nota smallint NOT NULL,
                    tipo_progressao smallint NOT NULL,
                    media numeric(5,3) DEFAULT 0.000,
                    porcentagem_presenca numeric(6,3) DEFAULT 0.000,
                    parecer_descritivo smallint DEFAULT 0,
                    tipo_presenca smallint NOT NULL,
                    media_recuperacao numeric(5,3) DEFAULT 0.000,
                    tipo_recuperacao_paralela smallint DEFAULT 0,
                    media_recuperacao_paralela numeric(5,3),
                    nota_maxima_geral integer DEFAULT 10 NOT NULL,
                    nota_maxima_exame_final integer DEFAULT 10 NOT NULL,
                    qtd_casas_decimais integer DEFAULT 2 NOT NULL,
                    nota_geral_por_etapa smallint DEFAULT 0,
                    qtd_disciplinas_dependencia smallint DEFAULT 0 NOT NULL,
                    aprova_media_disciplina smallint DEFAULT 0,
                    reprovacao_automatica smallint DEFAULT 0,
                    definir_componente_etapa smallint,
                    qtd_matriculas_dependencia smallint DEFAULT 0 NOT NULL,
                    nota_minima_geral integer DEFAULT 0,
                    tabela_arredondamento_id_conceitual integer,
                    regra_diferenciada_id integer,
	                updated_at timestamp NULL DEFAULT now(),
	                calcula_media_rec_paralela int2 NOT NULL DEFAULT \'0\'::smallint,
                    tipo_calculo_recuperacao_paralela int4 NOT NULL DEFAULT 1,
                    disciplinas_aglutinadas varchar(191) NULL
                );

                ALTER SEQUENCE modules.regra_avaliacao_id_seq OWNED BY modules.regra_avaliacao.id;
                
                ALTER TABLE ONLY modules.regra_avaliacao
                    ADD CONSTRAINT regra_avaliacao_pkey PRIMARY KEY (id, instituicao_id);

                ALTER TABLE ONLY modules.regra_avaliacao ALTER COLUMN id SET DEFAULT nextval(\'modules.regra_avaliacao_id_seq\'::regclass);
                
                CREATE UNIQUE INDEX regra_avaliacao_id_key ON modules.regra_avaliacao USING btree (id);

                SELECT pg_catalog.setval(\'modules.regra_avaliacao_id_seq\', 2, true);
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
        Schema::dropIfExists('modules.regra_avaliacao');
    }
}
