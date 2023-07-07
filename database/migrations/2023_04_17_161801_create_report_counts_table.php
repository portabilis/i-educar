<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('reports_counts', function (Blueprint $table) {
            $table->id();
            $table->string('render', 20);
            $table->string('template', 150);
            $table->boolean('success');
            $table->date('date');
            $table->integer('count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports_counts');
    }
};
