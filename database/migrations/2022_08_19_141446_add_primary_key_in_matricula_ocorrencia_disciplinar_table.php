<?php

use App\Support\Database\DropPrimaryKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use DropPrimaryKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula_ocorrencia_disciplinar', function (Blueprint $table) {
            $table->primary('cod_ocorrencia_disciplinar');
            $table->unique(['ref_cod_matricula', 'ref_cod_tipo_ocorrencia_disciplinar', 'sequencial']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula_ocorrencia_disciplinar', function (Blueprint $table) {
            $table->dropUnique(['ref_cod_matricula', 'ref_cod_tipo_ocorrencia_disciplinar', 'sequencial']);
            $this->dropPrimaryKeysFromTable('matricula_ocorrencia_disciplinar');
        });
    }
};
