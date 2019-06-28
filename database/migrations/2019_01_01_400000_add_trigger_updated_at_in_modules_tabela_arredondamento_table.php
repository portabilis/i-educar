<?php

use App\Support\Database\UpdatedAtTrigger;
use Illuminate\Database\Migrations\Migration;

class AddTriggerUpdatedAtInModulesTabelaArredondamentoTable extends Migration
{
    use UpdatedAtTrigger;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createUpdatedAtTrigger('modules.tabela_arredondamento');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropUpdatedAtTrigger('modules.tabela_arredondamento');
    }
}
