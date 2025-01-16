<?php

use App\Setting;
use App\SettingCategory;
use App\Support\Database\IncrementSequence;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use IncrementSequence;

    public function up(): void
    {
        $category = SettingCategory::query()->firstOrCreate([
            'name' => 'Validações de sistema',
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'legacy.app.rematricula.permitir_cursando',
        ], [
            'setting_category_id' => $category->getKey(),
            'value' => '0',
            'type' => 'boolean',
            'description' => 'Permitir rematrícula automática de alunos com situação cursando?',
            'hint' => null,
        ]);
    }

    public function down(): void
    {
        Setting::query()->where('key', 'legacy.app.rematricula.permitir_cursando')->delete();
    }
};
