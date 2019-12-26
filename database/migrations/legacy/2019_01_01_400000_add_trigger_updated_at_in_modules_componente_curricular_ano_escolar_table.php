<?php

use App\Support\Database\UpdatedAtTrigger;
use Illuminate\Database\Migrations\Migration;

class AddTriggerUpdatedAtInModulesComponenteCurricularAnoEscolarTable extends Migration
{
    use UpdatedAtTrigger;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createUpdatedAtTrigger('modules.componente_curricular_ano_escolar');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropUpdatedAtTrigger('modules.componente_curricular_ano_escolar');
    }
}
