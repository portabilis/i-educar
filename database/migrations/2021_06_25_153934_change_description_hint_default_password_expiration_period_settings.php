<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

class ChangeDescriptionHintDefaultPasswordExpirationPeriodSettings extends Migration
{
    public function up()
    {
        Setting::query()->updateOrInsert([
            'key' => 'legacy.app.user_accounts.default_password_expiration_period',
        ], [
            'description' => 'Quantidade de dias para expiração automática de senhas',
            'hint' => 'A contagem será efetuada em dias corridos. Se o valor preenchido for zero (0) ou nenhum, não ocorrerá automatização',
            'value' => 0
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::query()->updateOrInsert([
            'key' => 'legacy.app.user_accounts.default_password_expiration_period',
        ], [
            'description' => 'Dias para expiração de senha',
            'hint' => '',
        ]);
    }
}
