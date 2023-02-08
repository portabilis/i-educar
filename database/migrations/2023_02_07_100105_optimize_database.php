<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    use MigrationUtils;

    public function up()
    {
        //ANO LETIVO MODULO
        $this->dropView('public.exporter_stages');
        $this->dropView('public.exporter_school_stages');
        $this->dropView('relatorio.view_dados_modulo');
        $this->dropView('relatorio.view_modulo');

        \DB::statement('alter table pmieducar.ano_letivo_modulo drop constraint if exists ano_letivo_modulo_pkey');
        \DB::statement('alter table pmieducar.ano_letivo_modulo drop constraint if exists pmieducar_ano_letivo_modulo_ref_ref_cod_escola_ref_ano_foreign');
        \DB::statement('alter table pmieducar.ano_letivo_modulo drop constraint if exists ano_letivo_modulo_ref_ref_cod_escola_fkey');


        Schema::table('ano_letivo_modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('sequencial')->change();
            $table->unsignedSmallInteger('ref_ano')->change();
            $table->foreign('ref_ref_cod_escola')->references('cod_escola')->on('pmieducar.escola')->onDelete('cascade');
            $table->unique([
                'ref_ano',
                'ref_ref_cod_escola',
                'sequencial',
                'ref_cod_modulo'
            ]);
        });
        $this->executeSqlFile(__DIR__ . '/../sqls/views/public.exporter_school_stages.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/public.exporter_stages.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/relatorio.view_modulo.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/relatorio.view_dados_modulo.sql');

        //ESCOLA ANO LETIVO
        \DB::statement('alter table pmieducar.escola_ano_letivo drop constraint if exists escola_ano_letivo_pkey');
        Schema::table('escola_ano_letivo', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('updated_at')->nullable();
            $table->index([
                'ref_cod_escola',
                'ano'
            ]);
            $table->unique([
                'ref_cod_escola',
                'ano'
            ]);
        });

        Schema::table('ano_letivo_modulo', function (Blueprint $table) {
            $table->unsignedInteger('escola_ano_letivo_id')->nullable();
            $table->foreign('escola_ano_letivo_id')->references('id')->on('pmieducar.escola_ano_letivo')->onDelete('cascade');
        });

        \DB::statement('UPDATE pmieducar.ano_letivo_modulo alm SET escola_ano_letivo_id = eal.id FROM pmieducar.escola_ano_letivo eal WHERE eal.ano = alm.ref_ano AND eal.ref_cod_escola = alm.ref_ref_cod_escola');
        \DB::statement('alter table pmieducar.ano_letivo_modulo alter column escola_ano_letivo_id set not null');
    }
};
