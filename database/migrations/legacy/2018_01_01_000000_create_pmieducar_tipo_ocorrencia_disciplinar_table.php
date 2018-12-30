<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoOcorrenciaDisciplinarTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.tipo_ocorrencia_disciplinar (
                    cod_tipo_ocorrencia_disciplinar integer DEFAULT nextval(\'pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq\'::regclass) NOT NULL,
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
                
                ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
                    ADD CONSTRAINT tipo_ocorrencia_disciplinar_pkey PRIMARY KEY (cod_tipo_ocorrencia_disciplinar);

                SELECT pg_catalog.setval(\'pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.tipo_ocorrencia_disciplinar');

        DB::unprepared('DROP SEQUENCE pmieducar.tipo_ocorrencia_disciplinar_cod_tipo_ocorrencia_disciplinar_seq;');
    }
}
