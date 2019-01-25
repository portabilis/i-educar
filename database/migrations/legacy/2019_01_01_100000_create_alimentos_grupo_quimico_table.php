<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosGrupoQuimicoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.grupo_quimico_idgrpq_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.grupo_quimico (
                    idgrpq integer DEFAULT nextval(\'alimentos.grupo_quimico_idgrpq_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(50) NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.grupo_quimico
                    ADD CONSTRAINT pk_grp_quimico PRIMARY KEY (idgrpq);

                SELECT pg_catalog.setval(\'alimentos.grupo_quimico_idgrpq_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.grupo_quimico');

        DB::unprepared('DROP SEQUENCE alimentos.grupo_quimico_idgrpq_seq;');
    }
}
