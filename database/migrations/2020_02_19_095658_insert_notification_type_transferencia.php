<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertNotificationTypeTransferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.notification_type')->insert([
            'id' => NotificationType::TRANSFER,
            'name' => 'Transferência'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.notification_type')->delete(NotificationType::OTHER);
    }
}
