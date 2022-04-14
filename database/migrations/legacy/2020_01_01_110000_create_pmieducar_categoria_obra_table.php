<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarCategoriaObraTable extends Migration
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
                CREATE SEQUENCE pmieducar.categoria_obra_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.categoria_obra (
                    id integer NOT NULL,
                    descricao character varying(100) NOT NULL,
                    observacoes character varying(300)
                );

                ALTER SEQUENCE pmieducar.categoria_obra_id_seq OWNED BY pmieducar.categoria_obra.id;

                ALTER TABLE ONLY pmieducar.categoria_obra
                    ADD CONSTRAINT categoria_obra_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY pmieducar.categoria_obra ALTER COLUMN id SET DEFAULT nextval(\'pmieducar.categoria_obra_id_seq\'::regclass);

                SELECT pg_catalog.setval(\'pmieducar.categoria_obra_id_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.categoria_obra');
    }
}
