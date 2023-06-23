<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use AsView;

    public function up()
    {
        //ANO LETIVO MODULO
        $this->dropView('public.exporter_stages');
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');
        $this->dropView('relatorio.view_dados_modulo');
        $this->dropView('relatorio.view_modulo');

        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_pkey;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_ref_ref_cod_escola_fkey;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS pmieducar_ano_letivo_modulo_ref_ref_cod_escola_ref_ano_foreign;');

        Schema::table('ano_letivo_modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('sequencial')->change();
            $table->unsignedSmallInteger('ref_cod_modulo')->change();
            $table->unsignedSmallInteger('dias_letivos')->nullable()->change();
            $table->unsignedSmallInteger('ref_ano')->change();
            $table->foreign('ref_ref_cod_escola')->references('cod_escola')->on('pmieducar.escola');
            $table->unique([
                'ref_ano',
                'ref_ref_cod_escola',
                'sequencial',
                'ref_cod_modulo',
            ]);
        });

        //MODULO
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.modulo ALTER COLUMN cod_modulo type smallint USING cod_modulo::smallint;');

        Schema::table('pmieducar.modulo', function (Blueprint $table) {
            $table->unsignedSmallInteger('num_meses')->nullable()->change();
            $table->unsignedSmallInteger('num_semanas')->nullable()->change();
            $table->unsignedSmallInteger('num_etapas')->nullable()->default(0)->change();
        });

        $this->createView('public.exporter_school_class_stages', '2020-09-18');
        $this->createView('public.exporter_school_stages', '2020-07-09');
        $this->createView('public.exporter_stages', '2020-07-10');
        $this->createView('relatorio.view_modulo');
        $this->createView('relatorio.view_dados_modulo');

        //ESCOLA ANO LETIVO
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.escola_ano_letivo DROP CONSTRAINT IF EXISTS escola_ano_letivo_pkey;');
        Schema::table('escola_ano_letivo', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('updated_at')->nullable();
            $table->renameColumn('data_cadastro', 'created_at');
            $table->index([
                'ref_cod_escola',
                'ano',
            ]);
            $table->unique([
                'ref_cod_escola',
                'ano',
            ]);
        });

        Schema::table('ano_letivo_modulo', function (Blueprint $table) {
            $table->unsignedInteger('escola_ano_letivo_id')->nullable();
            $table->foreign('escola_ano_letivo_id')->references('id')->on('pmieducar.escola_ano_letivo')->onDelete('cascade');
        });

        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo ADD CONSTRAINT pmieducar_ano_letivo_modulo_ref_ref_cod_escola_ref_ano_foreign FOREIGN KEY (ref_ref_cod_escola, ref_ano) REFERENCES pmieducar.escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT');
        \DB::statement('UPDATE pmieducar.ano_letivo_modulo alm SET escola_ano_letivo_id = eal.id FROM pmieducar.escola_ano_letivo eal WHERE eal.ano = alm.ref_ano AND eal.ref_cod_escola = alm.ref_ref_cod_escola;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo ALTER COLUMN escola_ano_letivo_id SET NOT NULL;');
    }

    public function down()
    {
        //ANO LETIVO MODULO
        $this->dropView('public.exporter_stages');
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');
        $this->dropView('relatorio.view_dados_modulo');
        $this->dropView('relatorio.view_modulo');

        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_pkey;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_ref_ano_ref_ref_cod_escola_sequencial_ref_cod;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_escola_ano_letivo_id_foreign;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS ano_letivo_modulo_ref_ref_cod_escola_foreign;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo DROP CONSTRAINT IF EXISTS pmieducar_ano_letivo_modulo_ref_ref_cod_escola_ref_ano_foreign;');

        Schema::table('ano_letivo_modulo', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('escola_ano_letivo_id');

            $table->unsignedInteger('sequencial')->change();
            $table->unsignedInteger('ref_cod_modulo')->change();
            $table->decimal('dias_letivos', 5, 0)->nullable()->change();
            $table->unsignedInteger('ref_ano')->change();
        });
        DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo ADD CONSTRAINT ano_letivo_modulo_pkey PRIMARY KEY (ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo)');

        //MODULO
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.modulo ALTER COLUMN cod_modulo type integer USING cod_modulo::integer');

        Schema::table('pmieducar.modulo', function (Blueprint $table) {
            $table->decimal('num_meses', 2, 0)->nullable()->change();
            $table->unsignedInteger('num_semanas')->nullable()->change();
            $table->decimal('num_etapas', 2, 0)->nullable()->default(0)->change();
        });

        $this->createView('public.exporter_school_class_stages', '2020-09-18');
        $this->createView('public.exporter_school_stages', '2020-07-09');
        $this->createView('public.exporter_stages', '2020-07-10');
        $this->createView('relatorio.view_modulo');
        $this->createView('relatorio.view_dados_modulo');

        //ESCOLA ANO LETIVO
        \DB::statement('DROP INDEX IF EXISTS pmieducar.escola_ano_letivo_ref_cod_escola_ano_index;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.escola_ano_letivo DROP CONSTRAINT IF EXISTS pmieducar_escola_ano_letivo_ref_cod_escola_foreign CASCADE;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.escola_ano_letivo DROP CONSTRAINT IF EXISTS escola_ano_letivo_ref_cod_escola_ano_unique;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.escola_ano_letivo DROP CONSTRAINT IF EXISTS escola_ano_letivo_pkey;');
        Schema::table('escola_ano_letivo', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');
            $table->renameColumn('created_at', 'data_cadastro');
        });
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.escola_ano_letivo ADD CONSTRAINT escola_ano_letivo_pkey PRIMARY KEY (ref_cod_escola, ano);');
        //ANO LETIVO MODULO
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.ano_letivo_modulo ADD CONSTRAINT pmieducar_ano_letivo_modulo_ref_ref_cod_escola_ref_ano_foreign FOREIGN KEY (ref_ref_cod_escola, ref_ano) REFERENCES pmieducar.escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT');
    }
};
