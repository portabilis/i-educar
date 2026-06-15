<?php

use App\Models\LegacyBenefit;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        LegacyBenefit::query()
            ->where('bolsa_familia', false)
            ->whereRaw('unaccent(nm_beneficio) ILIKE unaccent(?)', ['%bolsa familia%'])
            ->update(['bolsa_familia' => true]);
    }
};
