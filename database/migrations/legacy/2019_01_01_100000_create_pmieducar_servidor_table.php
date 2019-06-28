<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarServidorTable extends Migration
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
                
                CREATE TABLE pmieducar.servidor (
                    cod_servidor integer NOT NULL,
                    ref_cod_instituicao integer NOT NULL,
                    ref_idesco numeric(2,0),
                    carga_horaria double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_subnivel integer,
                    situacao_curso_superior_1 smallint,
                    formacao_complementacao_pedagogica_1 smallint,
                    codigo_curso_superior_1 integer,
                    ano_inicio_curso_superior_1 numeric(4,0),
                    ano_conclusao_curso_superior_1 numeric(4,0),
                    instituicao_curso_superior_1 smallint,
                    situacao_curso_superior_2 smallint,
                    formacao_complementacao_pedagogica_2 smallint,
                    codigo_curso_superior_2 integer,
                    ano_inicio_curso_superior_2 numeric(4,0),
                    ano_conclusao_curso_superior_2 numeric(4,0),
                    instituicao_curso_superior_2 smallint,
                    situacao_curso_superior_3 smallint,
                    formacao_complementacao_pedagogica_3 smallint,
                    codigo_curso_superior_3 integer,
                    ano_inicio_curso_superior_3 numeric(4,0),
                    ano_conclusao_curso_superior_3 numeric(4,0),
                    instituicao_curso_superior_3 smallint,
                    multi_seriado boolean,
                    pos_graduacao integer[],
                    curso_formacao_continuada integer[],
                    tipo_ensino_medio_cursado int4 NULL,
	                updated_at timestamp NULL DEFAULT now()
                );
                
                ALTER TABLE ONLY pmieducar.servidor
                    ADD CONSTRAINT servidor_pkey PRIMARY KEY (cod_servidor, ref_cod_instituicao);
                    
                CREATE INDEX fki_servidor_ref_cod_subnivel ON pmieducar.servidor USING btree (ref_cod_subnivel);

                CREATE INDEX fki_servidor_ref_cod_subnivel_ ON pmieducar.servidor USING btree (ref_cod_subnivel);
                
                CREATE INDEX servidor_idx ON pmieducar.servidor USING btree (cod_servidor, ref_cod_instituicao, ativo);
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
        Schema::dropIfExists('pmieducar.servidor');
    }
}
