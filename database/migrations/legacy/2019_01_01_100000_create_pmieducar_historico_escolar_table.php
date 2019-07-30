<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarHistoricoEscolarTable extends Migration
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
                
                CREATE TABLE pmieducar.historico_escolar (
	                id serial NOT NULL,
                    ref_cod_aluno integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ano integer NOT NULL,
                    carga_horaria double precision,
                    dias_letivos integer,
                    escola character varying(255) NOT NULL,
                    escola_cidade character varying(255) NOT NULL,
                    escola_uf character varying(3),
                    observacao text,
                    aprovado smallint DEFAULT (1)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    faltas_globalizadas integer,
                    nm_serie character varying(255),
                    origem smallint DEFAULT (1)::smallint,
                    extra_curricular smallint DEFAULT (0)::smallint,
                    ref_cod_matricula integer,
                    ref_cod_instituicao integer,
                    import numeric(1,0),
                    frequencia numeric(5,2) DEFAULT 0.000,
                    registro character varying(50),
                    livro character varying(50),
                    folha character varying(50),
                    historico_grade_curso_id integer,
                    nm_curso character varying(255),
                    aceleracao integer,
                    ref_cod_escola integer,
                    dependencia boolean,
                    posicao integer
                );
                
                ALTER TABLE ONLY pmieducar.historico_escolar
	                ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY (id);
                    
                CREATE INDEX historico_escolar_ano_idx ON pmieducar.historico_escolar USING btree (ano);

                CREATE INDEX historico_escolar_ativo_idx ON pmieducar.historico_escolar USING btree (ativo);

                CREATE INDEX historico_escolar_nm_serie_idx ON pmieducar.historico_escolar USING btree (nm_serie);
                
                CREATE INDEX idx_historico_escolar_aluno_ativo ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ativo);

                CREATE INDEX idx_historico_escolar_id1 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial);

                CREATE INDEX idx_historico_escolar_id2 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial, ano);

                CREATE INDEX idx_historico_escolar_id3 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ano);
                
                CREATE UNIQUE INDEX pmieducar_historico_escolar_ref_cod_aluno_sequencial_unique ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial);
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
        Schema::dropIfExists('pmieducar.historico_escolar');
    }
}
