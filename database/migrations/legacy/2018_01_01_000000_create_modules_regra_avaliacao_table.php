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
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;
                
                CREATE TABLE modules.regra_avaliacao (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    formula_media_id integer NOT NULL,
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
                    regra_diferenciada_id integer
                );

                -- ALTER SEQUENCE modules.regra_avaliacao_id_seq OWNED BY modules.regra_avaliacao.id;
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
