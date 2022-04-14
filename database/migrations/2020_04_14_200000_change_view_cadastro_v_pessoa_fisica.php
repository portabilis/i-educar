<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class ChangeViewCadastroVPessoaFisica extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('cadastro.v_pessoa_fisica');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/cadastro.v_pessoa_fisica-2020-04-14.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('cadastro.v_pessoa_fisica');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/cadastro.v_pessoa_fisica.sql'
        );
    }
}
