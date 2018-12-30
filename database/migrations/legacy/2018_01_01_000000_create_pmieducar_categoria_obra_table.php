<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCategoriaObraTable extends Migration
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

                CREATE TABLE pmieducar.categoria_obra (
                    id integer NOT NULL,
                    descricao character varying(100) NOT NULL,
                    observacoes character varying(300)
                );

                -- ALTER SEQUENCE pmieducar.categoria_obra_id_seq OWNED BY pmieducar.categoria_obra.id;
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
