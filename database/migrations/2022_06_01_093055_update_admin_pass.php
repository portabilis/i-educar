<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $password = '$2y$10$hVQB0qxRPgcNI.qviWDbnueYP1SxaBWXZ5HYKnnDYFazkNAqVkTJO';

        DB::update('UPDATE portal.funcionario SET senha = ? WHERE matricula = ?', [$password, 'admin']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
