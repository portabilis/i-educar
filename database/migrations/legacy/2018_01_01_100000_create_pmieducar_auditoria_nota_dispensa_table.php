<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAuditoriaNotaDispensaTable extends Migration
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

                CREATE SEQUENCE pmieducar.auditoria_nota_dispensa_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.auditoria_nota_dispensa (
                    id integer NOT NULL,
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_componente_curricular integer NOT NULL,
                    nota numeric(8,4) NOT NULL,
                    etapa integer NOT NULL,
                    nota_recuperacao character varying(10),
                    nota_recuperacao_especifica character varying(10),
                    data_cadastro timestamp without time zone NOT NULL
                );

                ALTER SEQUENCE pmieducar.auditoria_nota_dispensa_id_seq OWNED BY pmieducar.auditoria_nota_dispensa.id;
                
                ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
                    ADD CONSTRAINT auditoria_nota_dispensa_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa ALTER COLUMN id SET DEFAULT nextval(\'pmieducar.auditoria_nota_dispensa_id_seq\'::regclass);
                
                SELECT pg_catalog.setval(\'pmieducar.auditoria_nota_dispensa_id_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.auditoria_nota_dispensa');
    }
}
