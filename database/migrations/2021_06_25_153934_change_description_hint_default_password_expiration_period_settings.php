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
            'description' => 'Quantidade de dias para expirar automaticamente as senhas dos usuários ativos',
            'hint' => 'A contagem será efetuada em dias corridos. Se o valor preenchido for zero (0), não ocorrerá automatização',
            'value' => 0,
            'type' => 'integer'
        ]);

        Setting::query()->updateOrInsert([
            'key' => 'legacy.app.user_accounts.max_days_without_login_to_disable_user',
        ], [
            'description' => 'Quantidade de dias permitidos sem acessar o sistema para inativação automática de usuário',
            'hint' => 'A contagem será efetuada em dias corridos. Se o valor preenchido for zero (0), não ocorrerá automatização',
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
            'value' => 180,
            'type' => 'string'
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'legacy.app.user_accounts.max_days_without_login_to_disable_user',
        ], [
            'description' => 'Quantidade de dias permitidos sem acessar o sistema para inativação automática de conta',
            'hint' => 'A contagem será efetuada em dias corridos. Se o valor preenchido for zero (0) ou nenhum, não ocorrerá automatização',
        ]);
    }
}
