<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::query()->where([
            'key' => 'legacy.educacenso.enable_export'
        ])->firstOrFail()->update([
            'value' => false
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::query()->where([
        'key' => 'legacy.educacenso.enable_export'
        ])->firstOrFail()->update([
            'value' => true
        ]);
    }
};
