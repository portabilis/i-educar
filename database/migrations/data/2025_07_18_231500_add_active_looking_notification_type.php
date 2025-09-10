<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        NotificationType::updateOrCreate(
            ['id' => NotificationType::ACTIVE_LOOKING],
            [
                'name' => 'Busca Ativa',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('public.notification_type')->where('id', NotificationType::ACTIVE_LOOKING)->delete();
    }
};
