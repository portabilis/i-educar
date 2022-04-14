<?php

namespace Database\Seeders;

use App\Menu;
use App\Process;
use App\Support\Database\IncrementSequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultMenusTableSeeder extends Seeder
{
    use IncrementSequence;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/inserts/public.menus.sql')
        );

        $this->incrementSequence('public.menus');

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONSULTAS)->firstOrFail()->getKey(),
            'title' => 'Consulta de dispensas',
            'link' => '/consulta-dispensas',
            'order' => 1,
            'process' => Process::EXEMPTION_LIST,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::MENU_SCHOOL)->firstOrFail()->getKey(),
            'title' => 'Reclassificar matrícula',
            'process' => Process::RECLASSIFY_REGISTRATION,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'title' => 'Atualização da situação de matrículas em lote',
            'link' => '/atualiza-situacao-matriculas',
            'process' => Process::UPDATE_REGISTRATION_STATUS,
        ]);
    }
}
