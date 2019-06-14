<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AdicionaColunaCustomLabels extends Migration
{

    private function getDefaultCustomLabels()
    {
        $customLabel = new CustomLabel();
        $defaults = $customLabel->getDefaults();
        $defaults['report.termo_recuperacao_final'] = 'Exame final';

        return $defaults;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = DB::table('pmieducar.configuracoes_gerais')
            ->select('ref_cod_instituicao', 'custom_labels')
            ->where('active_on_ieducar', 1)
            ->first();

        if (empty($setting)) {
            return;
        }

        $customLabels = json_decode($setting->custom_labels, true);
        $customLabels = is_array($customLabels) ? array_filter($customLabels) : [];

        $newCustomLabels = array_merge($this->getDefaultCustomLabels(), $customLabels);
        $newCustomLabels = json_encode($newCustomLabels);

        DB::unprepared(
            "
                UPDATE pmieducar.configuracoes_gerais
                SET custom_labels = '{$newCustomLabels}'
                WHERE ref_cod_instituicao = {$setting->ref_cod_instituicao}
            "
        );
    }
}
