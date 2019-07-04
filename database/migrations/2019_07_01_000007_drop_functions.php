<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropFunctions extends Migration
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
                SET search_path = cadastro, pg_catalog;

                DROP FUNCTION IF EXISTS fcn_aft_fisica();
                
                DROP FUNCTION IF EXISTS fcn_aft_fisica_cpf_provisorio();
                
                DROP FUNCTION IF EXISTS fcn_aft_fisica_provisorio();
                
                SET search_path = modules, pg_catalog;
                
                DROP FUNCTION IF EXISTS corrige_sequencial_historico();
                
                SET search_path = pmieducar, pg_catalog;
                
                DROP FUNCTION IF EXISTS fcn_aft_update();
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
