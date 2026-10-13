<?php

use App\Menu;
use App\Models\LegacyUserType;
use App\Process;
use Database\Factories\MenuFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Menu::class));

EloquentModelTestCase::registerModelTests();

test('os processos do menu levam o caminho e escondem as configuracoes de quem nao e administrador', function () {
    $root = MenuFactory::new()->create(['title' => 'Escola', 'process' => null]);
    $cadastro = MenuFactory::new()->create(['parent_id' => $root, 'title' => 'Alunos', 'process' => 9001]);
    $configuracao = MenuFactory::new()->create(['parent_id' => $cadastro, 'title' => 'Configurações', 'process' => Process::CONFIG]);

    $institucional = $root->processes('Escola', collect([$cadastro->getKey() => 2]), LegacyUserType::LEVEL_INSTITUTIONAL);
    $administrador = $root->fresh()->processes('Escola', collect(), LegacyUserType::LEVEL_ADMIN);

    $this->assertSame([$cadastro->getKey()], $institucional->pluck('process')->all());
    $this->assertSame(['title' => 'Alunos', 'description' => 'Escola > Alunos', 'link' => $cadastro->link, 'process' => $cadastro->getKey(), 'allow' => 2], $institucional->first()->all());
    $this->assertEqualsCanonicalizing([$cadastro->getKey(), $configuracao->getKey()], $administrador->pluck('process')->all());
});

test('troca declaracoes por atestados e desfaz a troca', function () {
    // Só entram os menus com código antigo: o filtro das exceções descarta os nulos.
    $menu = MenuFactory::new()->create(['title' => 'Declaração de matrícula', 'description' => 'Declaração de matrícula', 'old' => 123456]);

    Menu::changeMenusToAttestation();

    $this->assertSame('Atestado de matrícula', $menu->fresh()->title);

    Menu::changeMenusToDeclaration();

    $this->assertSame('Declaração de matrícula', $menu->fresh()->title);
});
