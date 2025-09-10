<?php

use App\Setting;
use App\SettingCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $category = SettingCategory::query()->firstOrCreate([
            'name' => 'Validações de sistema',
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'legacy.app.busca_ativa.sigla_programa_evasao',
        ], [
            'setting_category_id' => $category->getKey(),
            'value' => null,
            'type' => 'string',
            'description' => 'Sigla do programa de combate a evasão escolar estadual ou municipal',
            'hint' => 'Habilita o nome personalizado do programa na tela de registro de busca ativa e no relatório.',
            'maxlength' => 7,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::query()->where('key', 'legacy.app.busca_ativa.sigla_programa_evasao')->delete();
    }
};
