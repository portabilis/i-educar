<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use App\Models\LegacyUserType;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $menu = Menu::query()->updateOrCreate(['old' => Process::DOCUMENT_ABSENCE_REASON], [
            'parent_id' => Menu::query()->where('old', 999917)->valueOrFail('id'),
            'process' => Process::DOCUMENT_ABSENCE_REASON,
            'title' => 'Motivos de ausência de documentação',
            'description' => 'Cadastro de motivos de ausência de documentação',
            'order' => 0,
            'parent_old' => 999917,
            'link' => '/intranet/educar_motivo_ausencia_documentacao_lst.php',
        ]);

        Menu::query()
            ->where('process', 631)
            ->firstOrFail()
            ->userTypes()
            ->withPivot(['visualiza', 'cadastra', 'exclui'])
            ->get()
            ->each(function (LegacyUserType $userType) use ($menu) {
                $userType->menus()->syncWithoutDetaching([
                    $menu->getKey() => [
                        'visualiza' => $userType->pivot->visualiza,
                        'cadastra' => $userType->pivot->cadastra,
                        'exclui' => $userType->pivot->exclui,
                    ],
                ]);
            });
    }

    public function down(): void
    {
        $menu = Menu::query()->where('process', Process::DOCUMENT_ABSENCE_REASON)->first();

        if (!$menu) {
            return;
        }

        LegacyMenuUserType::query()
            ->where('menu_id', $menu->getKey())
            ->delete();

        $menu->delete();
    }
};
