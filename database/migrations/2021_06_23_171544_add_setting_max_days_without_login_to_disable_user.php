<?php

use App\Setting;
use App\Support\Database\SettingCategoryTrait;
use Illuminate\Database\Migrations\Migration;

class AddSettingMaxDaysWithoutLoginToDisableUser extends Migration
{
    use SettingCategoryTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::query()->updateOrCreate([
            'key' => 'legacy.app.user_accounts.max_days_without_login_to_disable_user',
        ], [
            'type' => 'integer',
            'description' => 'Quantidade de dias permitidos sem acessar o sistema para inativação automática de conta',
            'hint' => 'A contagem será efetuada em dias corridos. Se o valor preenchido for zero (0) ou nenhum, não ocorrerá automatização',
            'setting_category_id' => $this->getSettingCategoryIdByName('Validações de sistema'),
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
        Setting::query()->where('key', 'legacy.app.max_days_without_login_to_disable_user')->delete();
    }
}
