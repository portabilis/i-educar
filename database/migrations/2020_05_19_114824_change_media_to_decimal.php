<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMediaToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->decimal('media', 6, 3)->change();
            $table->decimal('media_recuperacao', 6, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->numeric('media', 5, 3)->change();
            $table->numeric('media_recuperacao', 5, 3)->change();
        });
    }
}
