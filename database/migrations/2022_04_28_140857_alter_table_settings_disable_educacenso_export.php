<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::query()->update([
            'key' => 'legacy.educacenso.enable_export',
            'value' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::query()->update([
            'key' => 'legacy.educacenso.enable_export',
            'value' => 1,
        ]);
    }
};
