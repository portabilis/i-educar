<?php

use iEducar\Modules\Unification\PersonLogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUnificationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_unification_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('adapter');
        });

        DB::table('log_unification_types')->insert(
            [
                'name' => 'person',
                'adapter' => PersonLogUnification::class
            ]
        );

        DB::table('log_unification_types')->insert(
            [
                'name' => 'student',
                'adapter' => StudentLogUnification::class
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_unification_type');
    }
}
