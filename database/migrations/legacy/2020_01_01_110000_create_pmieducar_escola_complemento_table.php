<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaComplementoTable extends Migration
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
                CREATE TABLE pmieducar.escola_complemento (
                    ref_cod_escola integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    cep numeric(8,0) NOT NULL,
                    numero numeric(10) NULL,
                    complemento character varying(50),
                    email character varying(50),
                    nm_escola character varying(255) NOT NULL,
                    municipio character varying(60) NOT NULL,
                    bairro character varying(40) NOT NULL,
                    logradouro character varying(150) NOT NULL,
                    ddd_telefone numeric(2,0),
                    telefone numeric(11,0),
                    ddd_fax numeric(2,0),
                    fax numeric(11,0),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.escola_complemento
                    ADD CONSTRAINT escola_complemento_pkey PRIMARY KEY (ref_cod_escola);

                CREATE INDEX i_escola_complemento_ativo ON pmieducar.escola_complemento USING btree (ativo);

                CREATE INDEX i_escola_complemento_bairro ON pmieducar.escola_complemento USING btree (bairro);

                CREATE INDEX i_escola_complemento_cep ON pmieducar.escola_complemento USING btree (cep);

                CREATE INDEX i_escola_complemento_complemento ON pmieducar.escola_complemento USING btree (complemento);

                CREATE INDEX i_escola_complemento_email ON pmieducar.escola_complemento USING btree (email);

                CREATE INDEX i_escola_complemento_logradouro ON pmieducar.escola_complemento USING btree (logradouro);

                CREATE INDEX i_escola_complemento_municipio ON pmieducar.escola_complemento USING btree (municipio);

                CREATE INDEX i_escola_complemento_nm_escola ON pmieducar.escola_complemento USING btree (nm_escola);

                CREATE INDEX i_escola_complemento_numero ON pmieducar.escola_complemento USING btree (numero);

                CREATE INDEX i_escola_complemento_ref_usuario_cad ON pmieducar.escola_complemento USING btree (ref_usuario_cad);
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
        Schema::dropIfExists('pmieducar.escola_complemento');
    }
}
