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
        Schema::create('pmieducar.anthropometric_percentiles', function (Blueprint $table) {
            $table->id();
            $table->integer('age_months');
            $table->enum('gender', ['M', 'F']);
            $table->decimal('l_value', 10, 6);
            $table->decimal('m_value', 10, 6);
            $table->decimal('s_value', 10, 6);
            
            // Percentiles data (from percentile files: bmi-boys-perc-who2007-exp.xlsx, bmi-girls-perc-who2007-exp.xlsx)
            $table->decimal('p01', 10, 6)->nullable();
            $table->decimal('p1', 10, 6)->nullable();
            $table->decimal('p3', 10, 6)->nullable();
            $table->decimal('p5', 10, 6)->nullable();
            $table->decimal('p10', 10, 6)->nullable();
            $table->decimal('p15', 10, 6)->nullable();
            $table->decimal('p25', 10, 6)->nullable();
            $table->decimal('p50', 10, 6)->nullable();
            $table->decimal('p75', 10, 6)->nullable();
            $table->decimal('p85', 10, 6)->nullable();
            $table->decimal('p90', 10, 6)->nullable();
            $table->decimal('p95', 10, 6)->nullable();
            $table->decimal('p97', 10, 6)->nullable();
            $table->decimal('p99', 10, 6)->nullable();
            $table->decimal('p999', 10, 6)->nullable();
            $table->string('source')->default('WHO_2007');
            $table->timestamps();
            
            $table->unique(['age_months', 'gender', 'source'], 'anthropometric_percentiles_unique');
            $table->index(['age_months', 'gender'], 'anthropometric_percentiles_age_gender_index');
            $table->index('source', 'anthropometric_percentiles_source_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmieducar.anthropometric_percentiles');
    }
};