<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraColunaLocalFuncionamentoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table pmieducar.escola alter column local_funcionamento type integer[] USING array[local_funcionamento]::integer[]');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table pmieducar.escola alter column local_funcionamento type integer USING local_funcionamento[1]::integer');
    }
}
