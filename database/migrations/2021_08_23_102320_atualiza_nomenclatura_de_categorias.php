<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaNomenclaturaDeCategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            UPDATE settings_categories
            SET name = (
                CASE name
                    WHEN \'Integração entre iEducar e iDiário\' THEN \'Integração entre i-Educar e i-Diário\'
                    ELSE settings_categories.name
                END
            );
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
