<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalPessoaFjTable extends Migration
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

                CREATE TABLE portal.pessoa_fj (
                    cod_pessoa_fj integer DEFAULT nextval(\'portal.pessoa_fj_cod_pessoa_fj_seq\'::regclass) NOT NULL,
                    nm_pessoa character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    id_federal character varying(30),
                    endereco text,
                    cep character varying(9),
                    ref_bairro integer,
                    ddd_telefone_1 integer,
                    telefone_1 character varying(15),
                    ddd_telefone_2 integer,
                    telefone_2 character varying(15),
                    ddd_telefone_mov integer,
                    telefone_mov character varying(15),
                    ddd_telefone_fax integer,
                    telefone_fax character varying(15),
                    email character varying(255),
                    http character varying(255),
                    tipo_pessoa character(1) DEFAULT \'F\'::bpchar NOT NULL,
                    sexo smallint,
                    razao_social character varying(255),
                    ins_est character varying(30),
                    ins_mun character varying(30),
                    rg character varying(30),
                    ref_cod_pessoa_pai integer,
                    ref_cod_pessoa_mae integer,
                    data_nasc date,
                    ref_ref_cod_pessoa_fj integer
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
        Schema::dropIfExists('portal.pessoa_fj');
    }
}
