<?php

use App\Models\LegacyInstitution;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->boolean('obrigar_cpf')->default(false)->change();
        });

        LegacyInstitution::where('obrigar_cpf', true)->update(['obrigar_cpf' => false]);
    }
};
