<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DefaultUpdatedAtInMatricula extends Migration
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
                ALTER TABLE pmieducar.matricula ALTER COLUMN updated_at SET DEFAULT now();
            '
        );
    }
}
