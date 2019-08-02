<?php

use App\Support\Database\UpdatedAtTrigger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnUpdatedAtInModulesRegraAvaliacaoSerieAno extends Migration
{
    use UpdatedAtTrigger;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao_serie_ano', function (Blueprint $table) {
            $table->timestamp('updated_at')->default(DB::raw('now()'));
        });

        $this->createUpdatedAtTrigger('modules.regra_avaliacao_serie_ano');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropUpdatedAtTrigger('modules.regra_avaliacao_serie_ano');

        Schema::table('modules.regra_avaliacao_serie_ano', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
