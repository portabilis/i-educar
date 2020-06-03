<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class Upgrade extends Migration
{
    /**
     * @var array
     */
    protected $files = [
        __DIR__ . '/../upgrade2.3.txt',
        __DIR__ . '/../../ieducar/modules/Reports/database/upgrade2.3.txt',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $counter = 1;

        foreach ($this->files as $file) {
            if (file_exists($file) === false) {
                continue;
            }
            
            $migrations = file($file, FILE_SKIP_EMPTY_LINES);

            foreach ($migrations as $migration) {
                DB::table('public.migrations')->insert([
                    'migration' => $migration,
                    'batch' => $counter++,
                ]);
            }
        }

        DB::unprepared($this->sql());
    }

    public function sql()
    {
        return <<<SQL

DROP SEQUENCE IF EXISTS public.seq_distrito;

DROP FUNCTION IF EXISTS cadastro.fcn_aft_ins_endereco_externo();

DROP FUNCTION IF EXISTS cadastro.fcn_aft_ins_endereco_pessoa();

DROP TABLE IF EXISTS cadastro.fisica_cpf;

DROP TABLE IF EXISTS pmieducar.auditoria_falta_componente_dispensa;

DROP TABLE IF EXISTS pmieducar.auditoria_nota_dispensa;

DROP TABLE IF EXISTS pmieducar.coffebreak_tipo;

DROP SEQUENCE IF EXISTS pmieducar.auditoria_falta_componente_dispensa_id_seq;

DROP SEQUENCE IF EXISTS pmieducar.auditoria_nota_dispensa_id_seq;

DROP SEQUENCE IF EXISTS pmieducar.coffebreak_tipo_cod_coffebreak_tipo_seq;

SQL;
    }
}
