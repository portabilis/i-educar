<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropColumns extends Migration
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
                SET search_path = public, pg_catalog;
                        
                ALTER TABLE bairro
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE logradouro
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE municipio
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE distrito
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE users
                    ALTER COLUMN id TYPE bigint;
                
                SET search_path = cadastro, pg_catalog;
                
                ALTER TABLE documento
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad,
                    ALTER COLUMN cartorio_cert_civil TYPE character varying(200);
                
                ALTER TABLE endereco_externo
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE endereco_pessoa
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE fisica
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE fisica_cpf
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE fone_pessoa
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE funcionario
                    DROP COLUMN idins,
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE juridica
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE pessoa
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE socio
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                SET search_path = urbano, pg_catalog;
                
                ALTER TABLE cep_logradouro
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                ALTER TABLE cep_logradouro_bairro
                    DROP COLUMN idsis_rev,
                    DROP COLUMN idsis_cad;
                
                SET search_path = public, pg_catalog;
            '
        );
    }
}
