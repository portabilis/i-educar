<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()
            ->where('key', 'legacy.educacenso.enable_export')
            ->update([
                'value' => false,
            ]);
    }

    public function down(): void
    {
        Setting::query()
            ->where('key', 'legacy.educacenso.enable_export')
            ->update([
                'value' => true,
            ]);
    }
};
