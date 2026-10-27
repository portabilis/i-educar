<?php

use Tests\Browser\Support\Scenario;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-filtros');
});

it('abre e recolhe os filtros avançados pelo botão', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_aluno_lst.php');

    $toggle = "[data-filter-section='advanced-toggle'] button";

    $page->assertAttributeContains($toggle, 'aria-expanded', 'false')
        ->click($toggle)
        ->assertAttributeContains($toggle, 'aria-expanded', 'true');

    expect((string) $page->script("document.querySelector('#filtros-avancados').hidden ? 'hidden' : 'visible'"))->toBe('visible');

    $page->click($toggle)
        ->assertAttributeContains($toggle, 'aria-expanded', 'false');

    expect((string) $page->script("document.querySelector('#filtros-avancados').hidden ? 'hidden' : 'visible'"))->toBe('hidden');
});

it('limpa filtros avançados e preserva os básicos na busca', function () {
    StudentScenario::named('Aluno Filtro Ana')->mother('Marta Filtro');
    StudentScenario::named('Aluno Filtro Bia')->mother('Olga Filtro');

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_aluno_lst.php?nome_aluno=Aluno+Filtro&nome_mae=Marta');

    $page->assertSee('Filtros avançados (1 ativo)')
        ->assertSee('Aluno Filtro Ana')
        ->assertDontSee('Aluno Filtro Bia');

    expect((string) $page->script("document.querySelector('#filtros-avancados').hidden ? 'hidden' : 'visible'"))->toBe('visible');

    $page->click('#filtros-avancados button')
        ->waitForEvent('networkidle')
        ->assertValue('#nome_aluno', 'Aluno Filtro')
        ->assertSee('Aluno Filtro Ana')
        ->assertSee('Aluno Filtro Bia')
        ->assertDontSee('Filtros avançados (1 ativo)')
        ->assertAttributeContains("[data-filter-section='advanced-toggle'] button", 'aria-expanded', 'false');

    expect((string) $page->script("document.querySelector('#filtros-avancados').hidden ? 'hidden' : 'visible'"))->toBe('hidden');
});
