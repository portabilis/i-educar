<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddValidacaoAulaNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.notification_type')->insert([
            'id' => NotificationType::VALIDATION_CLASS,
            'name' => 'Validação de Aula (Plano de Aula e Frequência)'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.notification_type')->delete(NotificationType::VALIDATION_CLASS);
    }
}
