<?php

use App\Support\Database\UpdatedAtTrigger;
use Illuminate\Database\Migrations\Migration;

class AddTriggerUpdatedAtInCadastroDeficienciaTable extends Migration
{
    use UpdatedAtTrigger;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createUpdatedAtTrigger('cadastro.deficiencia');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropUpdatedAtTrigger('cadastro.deficiencia');
    }
}
