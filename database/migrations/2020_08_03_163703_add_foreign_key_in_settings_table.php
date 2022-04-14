<?php

use App\Support\Database\SettingCategoryTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInSettingsTable extends Migration
{
    use SettingCategoryTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settingCategoryIdDefault = $this->getSettingCategoryIdByName('Sem categoria');
        Schema::table('settings', function (Blueprint $table) use ($settingCategoryIdDefault) {
            $table->integer('setting_category_id')->default($settingCategoryIdDefault);
            $table->foreign('setting_category_id')->on('settings_categories')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['setting_category_id']);
        });
    }
}
