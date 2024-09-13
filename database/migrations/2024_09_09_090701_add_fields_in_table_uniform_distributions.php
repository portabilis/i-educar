<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uniform_distributions', function (Blueprint $table) {
            $table->smallInteger('pants_fem_qty')->nullable();
            $table->string('pants_fem_tm', 20)->nullable();
            $table->smallInteger('pants_mas_qty')->nullable();
            $table->string('pants_mas_tm', 20)->nullable();
            $table->smallInteger('shorts_skirt_qty')->nullable();
            $table->string('shorts_skirt_tm', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('uniform_distributions', function (Blueprint $table) {
            $table->dropColumn('pants_fem_qty');
            $table->dropColumn('pants_fem_tm');
            $table->dropColumn('pants_mas_qty');
            $table->dropColumn('pants_mas_tm');
            $table->dropColumn('shorts_skirt_qty');
            $table->dropColumn('shorts_skirt_tm');
        });
    }
};
