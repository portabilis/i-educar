<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingEmailTable extends Migration
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

                CREATE SEQUENCE portal.mailling_email_cod_mailling_email_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.mailling_email (
                    cod_mailling_email integer DEFAULT nextval(\'portal.mailling_email_cod_mailling_email_seq\'::regclass) NOT NULL,
                    nm_pessoa character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    email character varying(255) DEFAULT \'\'::character varying NOT NULL
                );
                
                ALTER TABLE ONLY portal.mailling_email
                    ADD CONSTRAINT mailling_email_pk PRIMARY KEY (cod_mailling_email);

                SELECT pg_catalog.setval(\'portal.mailling_email_cod_mailling_email_seq\', 1, false);
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
        Schema::dropIfExists('portal.mailling_email');

        DB::unprepared('DROP SEQUENCE portal.mailling_email_cod_mailling_email_seq;');
    }
}
