<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE IF EXISTS pmieducar.escola ADD COLUMN formas_contratacao_parceria_escola_secretaria smallint[];');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'pmieducar.escola',
            static fn (Blueprint $table) => $table->dropColumn('formas_contratacao_parceria_escola_secretaria')
        );
    }
};
