<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarBackupTable extends Migration
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
                CREATE SEQUENCE pmieducar.backup_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.backup (
                    id integer NOT NULL,
                    caminho character varying(255) NOT NULL,
                    data_backup timestamp without time zone
                );

                ALTER SEQUENCE pmieducar.backup_id_seq OWNED BY pmieducar.backup.id;

                ALTER TABLE ONLY pmieducar.backup ALTER COLUMN id SET DEFAULT nextval(\'pmieducar.backup_id_seq\'::regclass);

                ALTER TABLE ONLY pmieducar.backup
                    ADD CONSTRAINT backup_pkey PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'pmieducar.backup_id_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.backup');
    }
}
