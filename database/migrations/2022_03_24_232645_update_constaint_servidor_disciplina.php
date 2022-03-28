<?php

use App\Support\Database\DropPrimaryKey;
use App\Support\Database\PrimaryKey;
use Illuminate\Database\Migrations\Migration;

class UpdateConstaintServidorDisciplina extends Migration
{
    use DropPrimaryKey;
    use PrimaryKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = [
            'ref_cod_disciplina',
            'ref_ref_cod_instituicao',
            'ref_cod_servidor',
            'ref_cod_curso',
            'ref_cod_funcao'
        ];

        $this->dropPrimaryKeyIn('pmieducar', 'servidor_disciplina', 'servidor_disciplina_pkey');
        $this->createConstraint('pmieducar.servidor_disciplina', $columns, 'servidor_disciplina_pkey');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $columns = [
            'ref_cod_disciplina',
            'ref_ref_cod_instituicao',
            'ref_cod_servidor',
            'ref_cod_curso'
        ];

        $this->dropPrimaryKeyIn('pmieducar', 'servidor_disciplina', 'servidor_disciplina_pkey');
        $this->createConstraint('pmieducar.servidor_disciplina', $columns, 'servidor_disciplina_pkey');
    }
}
