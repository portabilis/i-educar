<?php

use App\Models\LegacyGeneralConfiguration;
use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $configutarion = LegacyGeneralConfiguration::query()->first();

        if ($configutarion) {
            $custom_labels = json_decode($configutarion->custom_labels, true);

            $customLabel = new CustomLabel();
            $defaults = $customLabel->getDefaults();

            ksort($defaults);
            $rotulo = null;

            foreach ($defaults as $k => $v) {
                $rotulo2 = explode('.', $k)[0];

                $setting = new Setting();
                $setting->key = $k;
                $setting->value = $custom_labels[$k];
                $setting->type = 'string';
                $setting->description = $this->getLabel($v);
                $setting->setting_category_id = $this->getCategoryId($rotulo2);
                $setting->hint = $k;
                $setting->save();
            }
        }
    }

    private function getCategoryId($rotulo)
    {
        $id = 1;
        switch ($rotulo) {
            case "aluno":
                $id = 10;
                break;
            case "matricula":
                $id = 10;
                break;
            case "turma":
                $id = 10;
                break;
            case "report":
                $id = 9;
                break;
        }
        return $id;
    }

    private function getLabel($v)
    {
        $label = $v;
        switch ($v) {
            case "report.boletim_professor.modelo_padrao":
                $label = "Boletim do professsor - Modelo padrão";
                break;
            case "report.boletim_professor.modelo_recuperacao_paralela":
                $label = "Boletim do professsor - Modelo rec. por etapa";
                break;
            case "report.boletim_professor.modelo_recuperacao_por_etapa":
                $label = "Boletim do professsor - Modelo rec. específica";
                break;
        }
        return $label;
    }
};
