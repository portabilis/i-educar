<?php

use App\Models\LegacyEvaluationRule;
use Illuminate\Database\Migrations\Migration;

class MigrateColumnMediaRecuperacaoParalela extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LegacyEvaluationRule::query()->where('calcula_media_rec_paralela', 1)->update([
            'tipo_calculo_recuperacao_paralela' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        LegacyEvaluationRule::query()->where('tipo_calculo_recuperacao_paralela', 2)->update([
            'calcula_media_rec_paralela' => 1,
        ]);
    }
}
