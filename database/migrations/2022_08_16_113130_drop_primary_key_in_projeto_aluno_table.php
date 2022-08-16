<?php

use Illuminate\Database\Migrations\Migration;
use App\Support\Database\DropPrimaryKey;

return new class extends Migration
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
        DB::unprepared(
            '
                ALTER TABLE ONLY pmieducar.projeto_aluno
                    ADD CONSTRAINT pmieducar_projeto_aluno_pk PRIMARY KEY (ref_cod_projeto, ref_cod_aluno);
            '
        );
    }
};
