<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('modules.auditoria_geral');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE SEQUENCE modules.auditoria_geral_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.auditoria_geral (
                    usuario_id integer,
                    operacao smallint,
                    rotina character varying(50),
                    valor_antigo json,
                    valor_novo json,
                    data_hora timestamp without time zone,
                    codigo character varying,
                    id integer NOT NULL,
                    query text
                );

                ALTER SEQUENCE modules.auditoria_geral_id_seq OWNED BY modules.auditoria_geral.id;

                ALTER TABLE ONLY modules.auditoria_geral ALTER COLUMN id SET DEFAULT nextval(\'modules.auditoria_geral_id_seq\'::regclass);

                SELECT pg_catalog.setval(\'modules.auditoria_geral_id_seq\', 1, false);
            '
        );
    }
}
