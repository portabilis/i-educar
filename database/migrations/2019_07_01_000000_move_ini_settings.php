<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MoveIniSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        require_once base_path('ieducar/includes/bootstrap.php');

        global $coreExt;

        $config = $coreExt['Config'];

        $connection = DB::getDefaultConnection();

        $settings = $config->getSettings($connection);

        foreach ($settings as $key => $value) {
            Setting::query()->create([
                'key' => $key,
                'value' => $value,
                'type' => 'string',
            ]);
        }
    }
}
