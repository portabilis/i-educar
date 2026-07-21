<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pmieducar.escola_complemento');
    }
};
