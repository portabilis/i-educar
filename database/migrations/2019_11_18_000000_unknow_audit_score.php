<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UnknowAuditScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                UPDATE modules.auditoria
                SET usuario = \'1 - desconhecido\'
                WHERE usuario IS NULL
            '
        );
    }
}
