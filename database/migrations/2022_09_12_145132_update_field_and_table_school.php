<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.escola ALTER COLUMN ref_cod_escola_rede_ensino DROP NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.escola ALTER COLUMN ref_cod_escola_rede_ensino SET NOT NULL;');
    }
};
