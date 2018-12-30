<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarBackupTable extends Migration
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

                CREATE TABLE pmieducar.backup (
                    id integer NOT NULL,
                    caminho character varying(255) NOT NULL,
                    data_backup timestamp without time zone
                );

                -- ALTER SEQUENCE pmieducar.backup_id_seq OWNED BY pmieducar.backup.id;
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
