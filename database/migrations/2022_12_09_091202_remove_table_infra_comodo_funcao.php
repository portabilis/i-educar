<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::dropIfExists('pmieducar.infra_comodo_funcao');
    }
};
