<?php

use App\Menu;
use App\Models\LegacyUserType;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private function attachMenuIfNotExists($userTypes, $menu)
    {
        $userTypes->each(static function (LegacyUserType $userType) use ($menu) {
            $exists = $userType->menus()->where('menu_id', $menu->getKey())->exists();

            if (!$exists) {
                $userType->menus()->attach($menu, [
                    'visualiza' => 1,
                    'cadastra' => 1,
                    'exclui' => 1,
                ]);
            }
        });
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userTypes = LegacyUserType::all();

        // Busca o menu principal de Movimentações de Matrícula
        $mainMenu = Menu::query()
            ->where('process', Process::REGISTRATION_ACTIONS)
            ->first();

        // Atualiza a ordem do Enturmar para 14
        Menu::query()->updateOrCreate([
            'process' => 683,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Enturmar',
            'type' => 2,
            'order' => 14,
        ]);

        // Cria/atualiza Desenturmar logo após Enturmar (order 15)
        Menu::query()->updateOrCreate([
            'process' => 696,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Desenturmar',
            'type' => 2,
            'order' => 15,
        ]);

        // Cria/atualiza Remanejar logo após Desenturmar (order 16)
        $menu = Menu::query()->updateOrCreate([
            'process' => 695,
        ], [
            'parent_id' => $mainMenu->getKey(),
            'parent_old' => $mainMenu->getKey(),
            'title' => 'Remanejar',
            'type' => 2,
            'order' => 16,
        ]);
        $this->attachMenuIfNotExists($userTypes, $menu);

        // Ajusta as ordens dos demais itens para acomodar as novas posições
        // Modalidade de ensino passa de 15 para 17
        Menu::query()->where('process', 684)->update(['order' => 17]);

        // Deixou de Frequentar passa de 16 para 18
        Menu::query()->where('process', 685)->update(['order' => 18]);

        // Falecido passa de 17 para 19
        Menu::query()->where('process', 686)->update(['order' => 19]);

        // Reclassificar passa de 18 para 20
        Menu::query()->where('process', 1004)->update(['order' => 20]);

        // Etapa do aluno passa de 19 para 21
        Menu::query()->where('process', 687)->update(['order' => 21]);

        // Tipo do AEE do aluno passa de 20 para 22
        Menu::query()->where('process', 688)->update(['order' => 22]);

        // Turno passa de 21 para 23
        Menu::query()->where('process', 689)->update(['order' => 23]);

        // Itinerário formativo passa de 22 para 24
        Menu::query()->where('process', 690)->update(['order' => 24]);

        // Solicitar transferência passa de 23 para 25
        Menu::query()->where('process', 691)->update(['order' => 25]);

        // Formando passa de 24 para 26
        Menu::query()->where('process', 692)->update(['order' => 26]);

        // Saída da escola passa de 25 para 27
        Menu::query()->where('process', 693)->update(['order' => 27]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Menu::query()->where('process', 684)->update(['order' => 15]);
        Menu::query()->where('process', 685)->update(['order' => 16]);
        Menu::query()->where('process', 686)->update(['order' => 17]);
        Menu::query()->where('process', 1004)->update(['order' => 18]);
        Menu::query()->where('process', 687)->update(['order' => 19]);
        Menu::query()->where('process', 688)->update(['order' => 20]);
        Menu::query()->where('process', 689)->update(['order' => 21]);
        Menu::query()->where('process', 690)->update(['order' => 22]);
        Menu::query()->where('process', 691)->update(['order' => 23]);
        Menu::query()->where('process', 692)->update(['order' => 24]);
        Menu::query()->where('process', 693)->update(['order' => 25]);
    }
};
