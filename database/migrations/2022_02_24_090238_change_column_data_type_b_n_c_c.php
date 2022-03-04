<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnDataTypeBNCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE modules.bncc ALTER COLUMN campo_experiencia TYPE INT USING campo_experiencia::integer");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE modules.bncc ALTER COLUMN campo_experiencia TYPE VARCHAR USING campo_experiencia::char");
    }
}
