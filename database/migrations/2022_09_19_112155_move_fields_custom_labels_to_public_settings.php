<?php

use App\Models\LegacyGeneralConfiguration;
use App\Setting;
use App\SettingCategory;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
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
                $setting->description = $this->getLabel($k, $v);
                $setting->setting_category_id = $this->getCategoryId($rotulo2);
                $setting->hint = $k;
                $setting->save();
            }
        }
    }

    private function getCategoryId($rotulo)
    {
        $id = 1;
        if (
            $rotulo == 'aluno' ||
            $rotulo == 'historico' ||
            $rotulo == 'matricula' ||
            $rotulo == 'turma'
        ) {
            $id = SettingCategory::query()
                ->where('name', 'Validações de sistema')
                ->value('id');
        } elseif ($rotulo == 'report') {
            $id = SettingCategory::query()
                ->where('name', 'Validações de relatórios')
                ->value('id');
        }

        return $id;
    }

    private function getLabel($k, $v)
    {
        $label = $v;
        switch ($k) {
            case 'aluno.detalhe.codigo_estado':
                $label = 'Código rede estadual do aluno (detalhes do aluno)';

                break;
            case 'historico.cadastro.curso_detalhe':
                $label = 'Curso no cadastro do histórico';

                break;
            case 'historico.cadastro.serie':
                $label = 'Série no cadastro do histórico';

                break;
            case 'report.boletim_professor.modelo_padrao':
                $label = 'Boletim do professsor - Modelo padrão';

                break;
            case 'turma.detalhe.sigla':
                $label = 'Sigla da turma';

                break;
            case 'report.boletim_professor.modelo_recuperacao_paralela':
                $label = 'Boletim do professsor - Modelo rec. por etapa';

                break;
            case 'report.boletim_professor.modelo_recuperacao_por_etapa':
                $label = 'Boletim do professsor - Modelo rec. específica';

                break;
            case 'matricula.detalhe.enturmar':
                $label = 'Nome do botão "[Enturmar]" em visualizar matrícula';

                break;
            case 'matricula.detalhe.solicitar_transferencia':
                $label = 'Nome do botão "[Solicitar transferência]" em visualizar matrícula';

                break;
            case 'report.termo_recuperacao_final':
                $label = 'Recuperação final';

                break;
        }

        return $label;
    }
};
