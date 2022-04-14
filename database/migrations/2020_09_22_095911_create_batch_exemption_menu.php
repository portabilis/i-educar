<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateBatchExemptionMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'title' => 'Dispensa em lote',
            'link' => '/dispensa-lote',
            'process' => Process::BATCH_EXEMPTION,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::BATCH_EXEMPTION)->delete();
    }
}
