<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TokenNovoEducacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->string('token_novo_educacao')->after('url_novo_educacao')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->dropColumn('token_novo_educacao');
        });
    }
}
