<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateComponentName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE modules.componente_curricular SET nome = replace(nome, \'extrangeira\', \'estrangeira\') WHERE nome ILIKE \'%extrangeira%\';');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('UPDATE modules.componente_curricular set nome = replace(nome, \'estrangeira\', \'extrangeira\') WHERE nome ILIKE \'%estrangeira%\';');
    }
}
