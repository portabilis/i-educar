<?php

use App\Models\LegacyInstitution;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        LegacyInstitution::query()
            ->update([
                'data_educacenso' => '2025-05-28',
            ]);
    }

    public function down(): void
    {
        LegacyInstitution::query()
            ->update([
                'data_educacenso' => '2024-05-29',
            ]);
    }
};
