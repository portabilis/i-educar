<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anthropometric_z_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('age_months');
            $table->enum('gender', ['M', 'F']);
            $table->decimal('l_value', 10, 6);
            $table->decimal('m_value', 10, 6);
            $table->decimal('s_value', 10, 6);

            // Z-score deviations (from z-score files: bmi-boys-z-who-2007-exp.xlsx, bmi-girls-z-who-2007-exp.xlsx)
            $table->decimal('sd4neg', 10, 6)->nullable();
            $table->decimal('sd3neg', 10, 6)->nullable();
            $table->decimal('sd2neg', 10, 6)->nullable();
            $table->decimal('sd1neg', 10, 6)->nullable();
            $table->decimal('sd0', 10, 6)->nullable();
            $table->decimal('sd1', 10, 6)->nullable();
            $table->decimal('sd2', 10, 6)->nullable();
            $table->decimal('sd3', 10, 6)->nullable();
            $table->decimal('sd4', 10, 6)->nullable();
            $table->string('source')->default('WHO_2007');
            $table->timestamps();

            $table->unique(['age_months', 'gender', 'source'], 'anthropometric_z_scores_unique');
            $table->index(['age_months', 'gender'], 'anthropometric_z_scores_age_gender_index');
            $table->index('source', 'anthropometric_z_scores_source_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anthropometric_z_scores');
    }
};
