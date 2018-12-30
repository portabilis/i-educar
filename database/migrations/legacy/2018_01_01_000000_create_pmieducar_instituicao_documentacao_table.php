<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarInstituicaoDocumentacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE TABLE pmieducar.instituicao_documentacao (
                    id integer DEFAULT nextval(\'pmieducar.instituicao_documentacao_seq\'::regclass) NOT NULL,
                    instituicao_id integer NOT NULL,
                    titulo_documento character varying(100) NOT NULL,
                    url_documento character varying(255) NOT NULL,
                    ref_usuario_cad integer DEFAULT 0 NOT NULL,
                    ref_cod_escola integer
                );
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
        Schema::dropIfExists('pmieducar.instituicao_documentacao');
    }
}
