<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddSocialNameInIndividualView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS cadastro.v_pessoa_fisica;');

        DB::statement(
            '
                CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS 
                SELECT 
                    p.idpes,
                    p.nome,
                    p.url,
                    p.email,
                    p.situacao,
                    f.nome_social,
                    f.data_nasc,
                    f.sexo,
                    f.cpf,
                    f.ref_cod_sistema,
                    f.idesco,
                    f.ativo
                FROM cadastro.pessoa p
                INNER JOIN cadastro.fisica f ON TRUE
                AND f.idpes = p.idpes;
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW cadastro.v_pessoa_fisica;');
    }
}
