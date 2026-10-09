<?php

namespace Tests\Unit\Eloquent;

use App\Menu;
use App\Models\LegacyUserType;
use App\Process;
use Database\Factories\MenuFactory;
use Tests\EloquentTestCase;

class MenuTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Menu::class;
    }

    public function test_os_processos_do_menu_levam_o_caminho_e_escondem_as_configuracoes_de_quem_nao_e_administrador(): void
    {
        $root = MenuFactory::new()->create(['title' => 'Escola', 'process' => null]);
        $cadastro = MenuFactory::new()->create(['parent_id' => $root, 'title' => 'Alunos', 'process' => 9001]);
        $configuracao = MenuFactory::new()->create(['parent_id' => $cadastro, 'title' => 'Configurações', 'process' => Process::CONFIG]);

        $institucional = $root->processes('Escola', collect([$cadastro->getKey() => 2]), LegacyUserType::LEVEL_INSTITUTIONAL);
        $administrador = $root->fresh()->processes('Escola', collect(), LegacyUserType::LEVEL_ADMIN);

        $this->assertSame([$cadastro->getKey()], $institucional->pluck('process')->all());
        $this->assertSame(['title' => 'Alunos', 'description' => 'Escola > Alunos', 'link' => $cadastro->link, 'process' => $cadastro->getKey(), 'allow' => 2], $institucional->first()->all());
        $this->assertEqualsCanonicalizing([$cadastro->getKey(), $configuracao->getKey()], $administrador->pluck('process')->all());
    }

    public function test_troca_declaracoes_por_atestados_e_desfaz_a_troca(): void
    {
        // Só entram os menus com código antigo: o filtro das exceções descarta os nulos.
        $menu = MenuFactory::new()->create(['title' => 'Declaração de matrícula', 'description' => 'Declaração de matrícula', 'old' => 123456]);

        Menu::changeMenusToAttestation();

        $this->assertSame('Atestado de matrícula', $menu->fresh()->title);

        Menu::changeMenusToDeclaration();

        $this->assertSame('Declaração de matrícula', $menu->fresh()->title);
    }
}
