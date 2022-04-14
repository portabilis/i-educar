<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInNotaExameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.nota_exame', function (Blueprint $table) {
            $table->index(['ref_cod_matricula']);
            $table->index(['ref_cod_componente_curricular']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.nota_exame', function (Blueprint $table) {
            $table->dropIndex(['ref_cod_matricula']);
            $table->dropIndex(['ref_cod_componente_curricular']);
        });
    }
}
