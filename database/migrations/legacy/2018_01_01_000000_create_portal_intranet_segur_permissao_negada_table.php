<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalIntranetSegurPermissaoNegadaTable extends Migration
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
                SET default_with_oids = true;

                CREATE SEQUENCE portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.intranet_segur_permissao_negada (
                    cod_intranet_segur_permissao_negada integer DEFAULT nextval(\'portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer,
                    ip_externo character varying(15) DEFAULT \'\'::character varying NOT NULL,
                    ip_interno character varying(255),
                    data_hora timestamp without time zone NOT NULL,
                    pagina character varying(255),
                    variaveis text
                );
                
                SELECT pg_catalog.setval(\'portal.intranet_segur_permissao_nega_cod_intranet_segur_permissao__seq\', 1, false);
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
        Schema::dropIfExists('portal.intranet_segur_permissao_negada');
    }
}
