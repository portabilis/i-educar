<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForceResetPasswordEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.funcionario', function (Blueprint $table) {
            $table->boolean('force_reset_password')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal.funcionario', function (Blueprint $table) {
            $table->dropColumn('force_reset_password');
        });
    }
}
