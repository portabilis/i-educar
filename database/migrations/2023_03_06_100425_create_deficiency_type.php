<?php

use App\Models\DeficiencyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->unsignedSmallInteger('deficiency_type_id')->default(DeficiencyType::DEFICIENCY);
        });
    }

    public function down()
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->dropColumn('deficiency_type_id');
        });
    }
};
