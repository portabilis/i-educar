<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class RemoveNotNullFormulaMediaTabelaRegraAvaliacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table modules.regra_avaliacao alter column formula_media_id drop not null;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table modules.regra_avaliacao alter column formula_media_id set not null;');
    }
}
