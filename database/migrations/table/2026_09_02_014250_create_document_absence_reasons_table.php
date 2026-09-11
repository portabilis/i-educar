<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_absence_reasons', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->boolean('requires_observation')->default(false);
            $table->boolean('from_system')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_absence_reasons');
    }
};
