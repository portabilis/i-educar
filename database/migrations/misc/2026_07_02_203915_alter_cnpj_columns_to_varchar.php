<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS cadastro.v_pessoa_fj;');
        DB::statement('DROP VIEW IF EXISTS cadastro.v_pessoafj_count;');

        DB::statement(
            "ALTER TABLE cadastro.juridica
                ALTER COLUMN cnpj TYPE varchar(14)
                USING lpad(cnpj::text, 14, '0');"
        );

        DB::statement(
            "ALTER TABLE pmieducar.escola
                ALTER COLUMN cnpj_mantenedora_principal TYPE varchar(14)
                USING lpad(cnpj_mantenedora_principal::text, 14, '0');"
        );
    }
};
