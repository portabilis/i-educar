<?php

use App\Support\Database\DropPrimaryKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
        $this->dropPrimaryKeysFromTable('projeto_aluno');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.projeto_aluno', function (Blueprint $table) {
            $table->primary(['ref_cod_projeto', 'ref_cod_aluno']);
        });
    }
};
