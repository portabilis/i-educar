<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->decimal('media', 5, 3)->change();
            $table->decimal('media_recuperacao', 5, 3)->change();
        });
    }
}
