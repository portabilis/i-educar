<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()->updateOrCreate([
            'key' => 'app.url',
        ], [
            'description' => 'URL da aplicação',
            'value' => 'https://' . DB::getDefaultConnection() . '.' . config('app.default_host'),
        ]);
    }

    public function down(): void
    {
        Setting::query()->where('key', 'app.url')->delete();
    }
};
