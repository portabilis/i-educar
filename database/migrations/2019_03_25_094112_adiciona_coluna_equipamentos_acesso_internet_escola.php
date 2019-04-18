<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaEquipamentosAcessoInternetEscola extends Migration
{
    /** Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pmieducar.escola ADD COLUMN equipamentos_acesso_internet integer[]');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('equipamentos_acesso_internet');
        });
    }
}
