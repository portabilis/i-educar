<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrimaryKeyServidorAfastamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servidor_afastamento', function (Blueprint $table) {
            $table->dropPrimary();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servidor_afastamento', function (Blueprint $table) {
            $table->primary([
                'ref_cod_servidor',
                'sequencial',
                'ref_ref_cod_instituicao',
            ]);
        });
    }
}
