<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use App\Models\LegacyUserType;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private function attachMenu($userTypes, $menu)
    {
        $userTypes->each(static function (LegacyUserType $userType) use ($menu) {
            $userType->menus()->attach($menu, [
                'visualiza' => 1,
                'cadastra' => 1,
                'exclui' => 1,
            ]);
        });
    }

    public function up(): void
    {
        $userTypes = LegacyUserType::all();
        $mainMenu = Menu::query()->updateOrCreate([
            'process' => Process::REGISTRATION_ACTIONS,
        ], [
            'old' => Process::REGISTRATION_ACTIONS,
            'title' => 'Movimentações de Matrícula',
            'order' => 5,
            'type' => 1,
        ]);

        $menu = Menu::query()->updateOrCreate([
            'process' => 680,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Nova matrícula',
            'type' => 2,
            'order' => 1,
        ]);
        $this->attachMenu($userTypes, $menu);

        //Permissão movida apenas
        Menu::query()->updateOrCreate([
            'process' => 627,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Cancelar matrícula',
            'type' => 2,
            'order' => 2,
        ]);

        $menu = Menu::query()->updateOrCreate([
            'process' => 681,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Ocorrências disciplinares',
            'type' => 2,
            'order' => 10,
        ]);
        $this->attachMenu($userTypes, $menu);

        //Permissão movida apenas
        Menu::query()->updateOrCreate([
            'process' => 628,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Dispensa de componente curriculares',
            'type' => 2,
            'order' => 11,
        ]);

        //Permissão movida apenas
        Menu::query()->updateOrCreate([
            'process' => Process::ACTIVE_LOOKING,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Busca ativa',
            'type' => 2,
            'order' => 12,
        ]);

        $menu = Menu::query()->updateOrCreate([
            'process' => 682,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Disciplinas de dependência',
            'type' => 2,
            'order' => 13,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 683,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Enturmar',
            'type' => 2,
            'order' => 14,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 684,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Modalidade de ensino',
            'type' => 2,
            'order' => 15,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 685,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Abandono',
            'type' => 2,
            'order' => 16,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 686,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Falecido',
            'type' => 2,
            'order' => 17,
        ]);
        $this->attachMenu($userTypes, $menu);

        //Permissão movida apenas
        Menu::query()->updateOrCreate([
            'process' => 1004,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Reclassificar',
            'type' => 2,
            'order' => 18,
        ]);

        $menu = Menu::query()->updateOrCreate([
            'process' => 687,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Etapa do aluno',
            'type' => 2,
            'order' => 19,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 688,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Tipo do AEE do aluno',
            'type' => 2,
            'order' => 20,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 689,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Turno',
            'type' => 2,
            'order' => 21,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 690,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Itinerário formativo',
            'type' => 2,
            'order' => 22,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 691,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Solicitar transferência',
            'type' => 2,
            'order' => 23,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 692,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Formando',
            'type' => 2,
            'order' => 24,
        ]);
        $this->attachMenu($userTypes, $menu);

        $menu = Menu::query()->updateOrCreate([
            'process' => 693,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Saída da escola',
            'type' => 2,
            'order' => 25,
        ]);
        $this->attachMenu($userTypes, $menu);
    }

    public function down(): void
    {
        Menu::query()->updateOrCreate([
            'process' => 627,
            //existe
        ], [
            'old' => 627,
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->value('id'),
            'parent_old' => Process::MENU_SCHOOL,
            'title' => 'Cancelar matrícula',
            'type' => 2,
            'order' => 9999,
        ]);

        Menu::query()->updateOrCreate([
            'process' => 628,
        ], [
            'old' => 628,
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->value('id'),
            'parent_old' => Process::MENU_SCHOOL,
            'title' => 'Dispensar componente curricular na matrícula',
            'type' => 2,
            'order' => 9999,
        ]);

        Menu::query()->updateOrCreate([
            'process' => Process::ACTIVE_LOOKING,
        ], [
            'old' => null,
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->value('id'),
            'parent_old' => null,
            'title' => 'Busca ativa',
            'type' => 1,
            'order' => 99,
        ]);

        Menu::query()->updateOrCreate([
            'process' => 1004,
        ], [
            'old' => null,
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->value('id'),
            'parent_old' => null,
            'title' => 'Reclassificar matrícula',
            'type' => 1,
            'order' => 99,
        ]);

        $menus = Menu::query()
            ->whereIn('process', [
                Process::REGISTRATION_ACTIONS,
                680,
                681,
                682,
                683,
                684,
                685,
                686,
                687,
                688,
                689,
                690,
                691,
                692,
                693,
            ])->get();

        foreach ($menus as $menu) {
            LegacyMenuUserType::query()
                ->where('menu_id', $menu->getKey())
                ->delete();
            $menu->delete();
        }
    }
};
