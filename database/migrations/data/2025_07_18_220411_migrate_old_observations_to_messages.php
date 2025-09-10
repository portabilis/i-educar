<?php

use App\Models\LegacyActiveLooking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            INSERT INTO messages (
                messageable_type, 
                messageable_id, 
                user_id, 
                description, 
                created_at, 
                updated_at
            )
            SELECT 
                '" . LegacyActiveLooking::class . "' as messageable_type,
                id as messageable_id,
                NULL as user_id,
                observacoes as description,
                COALESCE(created_at, NOW()) as created_at,
                COALESCE(updated_at, NOW()) as updated_at
            FROM pmieducar.busca_ativa 
            WHERE observacoes IS NOT NULL 
            AND observacoes != ''
        ");

        Schema::table('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->dropColumn('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->text('observacoes')->nullable();
        });

        DB::statement("
            UPDATE pmieducar.busca_ativa ba
            SET observacoes = (
                SELECT description 
                FROM messages m 
                WHERE m.messageable_type = '" . LegacyActiveLooking::class . "'
                AND m.messageable_id = ba.id
                AND m.user_id IS NULL
                LIMIT 1
            )
            WHERE EXISTS (
                SELECT 1 
                FROM messages m 
                WHERE m.messageable_type = '" . LegacyActiveLooking::class . "'
                AND m.messageable_id = ba.id
                AND m.user_id IS NULL
            )
        ");

        DB::statement("
            DELETE FROM messages 
            WHERE messageable_type = '" . LegacyActiveLooking::class . "'
            AND user_id IS NULL
        ");
    }
};
