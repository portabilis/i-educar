<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('uniform_distributions', function (Blueprint $table) {
            $table->smallInteger('coat_jacket_qty')->nullable();
            $table->string('coat_jacket_tm', 20)->nullable();
            $table->string('type', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('uniform_distributions', function (Blueprint $table) {
            $table->dropColumn('coat_jacket_qty');
            $table->dropColumn('coat_jacket_tm');
            $table->dropColumn('type');
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
};
