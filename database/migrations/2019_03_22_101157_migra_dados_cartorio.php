<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigraDadosCartorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil', 255)->change();
        });

        $sql = <<<'SQL'
                    UPDATE cadastro.documento
                    SET cartorio_cert_civil = aux.id_cartorio || ' - ' || aux.descricao
                    FROM (
                           SELECT id,
                                  descricao,
                                  id_cartorio
                           FROM cadastro.codigo_cartorio_inep
                    
                         ) aux
                    WHERE aux.id = cadastro.documento.cartorio_cert_civil_inep
                    AND cartorio_cert_civil IS NULL
SQL;


        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil', 200)->change();
        });
    }
}
