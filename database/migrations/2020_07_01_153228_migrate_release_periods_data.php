<?php

use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateReleasePeriodsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $locks = DB::table('pmieducar.bloqueio_lancamento_faltas_notas')->get();

        foreach ($locks as $lock) {
            $stageType = $this->getStageTipe($lock->ano, $lock->ref_cod_escola);

            if (empty($stageType)) {
                continue;
            }

            $releasePeriod = ReleasePeriod::create([
                'year' => $lock->ano,
                'stage_type_id' => $stageType,
                'stage' => $lock->etapa,
            ]);

            ReleasePeriodDate::create([
                'release_period_id' => $releasePeriod->getKey(),
                'start_date' => $lock->data_inicio,
                'end_date' => $lock->data_fim
            ]);

            $releasePeriod->schools()->attach($lock->ref_cod_escola);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function getStageTipe($year, $school)
    {
        $stageType = DB::table('pmieducar.ano_letivo_modulo')
            ->where('ref_ano', $year)
            ->where('ref_ref_cod_escola', $school)
            ->first();

        if (empty($stageType)) {
            return null;
        }

        return $stageType->ref_cod_modulo;
    }
}
