<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarSerieTable extends Migration
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
                CREATE SEQUENCE pmieducar.serie_cod_serie_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.serie (
                    cod_serie integer DEFAULT nextval(\'pmieducar.serie_cod_serie_seq\'::regclass) NOT NULL,
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
                    exigir_inep boolean,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.serie
                    ADD CONSTRAINT serie_pkey PRIMARY KEY (cod_serie);

                CREATE INDEX idx_serie_cod_regra_avaliacao_id ON pmieducar.serie USING btree (cod_serie, regra_avaliacao_id);

                CREATE INDEX idx_serie_regra_avaliacao_id ON pmieducar.serie USING btree (regra_avaliacao_id);

                SELECT pg_catalog.setval(\'pmieducar.serie_cod_serie_seq\', 2, true);
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
        Schema::dropIfExists('pmieducar.serie');

        DB::unprepared('DROP SEQUENCE pmieducar.serie_cod_serie_seq;');
    }
}
