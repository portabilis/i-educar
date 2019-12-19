<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisciplinasAglutinadasToRegraAvaliacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->string('disciplinas_aglutinadas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropColumn('disciplinas_aglutinadas');
        });
    }
}
