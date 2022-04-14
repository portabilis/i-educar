<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddExportNotificationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.notification_type')->insert([
            'id' => NotificationType::EXPORT_STUDENT,
            'name' => 'Exportação de dados de alunos'
        ]);

        DB::table('public.notification_type')->insert([
            'id' => NotificationType::EXPORT_TEACHER,
            'name' => 'Exportação de dados de professores'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.notification_type')->delete(NotificationType::EXPORT_STUDENT);
        DB::table('public.notification_type')->delete(NotificationType::EXPORT_TEACHER);
    }
}
