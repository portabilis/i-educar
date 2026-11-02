<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Títulos de menu fora do padrão: primeira letra maiúscula e o restante
     * em minúsculas, exceto nomes próprios e siglas.
     *
     * @var array<int, array{string, string}>
     */
    private array $titulos = [
        60 => ['Movimentações de Matrícula', 'Movimentações de matrícula'],
        685 => ['Deixou de Frequentar', 'Deixou de frequentar'],
        1026 => ['Exportador de Documentos', 'Exportador de documentos'],
        1027 => ['Comunicados Escolares', 'Comunicados escolares'],
        9999118 => ['Importação de Situação Final', 'Importação de situação final'],
        9999120 => ['Ano Letivo em Lote', 'Ano letivo em lote'],
        9999121 => ['Gerenciamento em Lote de Componentes', 'Gerenciamento em lote de componentes'],
    ];

    public function up(): void
    {
        foreach ($this->titulos as $processo => [$antigo, $novo]) {
            Menu::query()->where('process', $processo)->where('title', $antigo)->update(['title' => $novo]);
        }
    }

    public function down(): void
    {
        foreach ($this->titulos as $processo => [$antigo, $novo]) {
            Menu::query()->where('process', $processo)->where('title', $novo)->update(['title' => $antigo]);
        }
    }
};
