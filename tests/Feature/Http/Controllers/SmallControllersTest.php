<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);
});

test('os arquivos estáticos dos módulos do legado são servidos com o tipo certo', function () {
    $this->get('/module/Educacenso/scripts/extra/educar-turma-cad.js')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=utf-8');
});

test('a abertura de endereço privado e o download de backup redirecionam para o endereço', function () {
    $this->get(route('open_private_url.open', ['url' => 'https://exemplo.test/arquivo.pdf']))->assertRedirect('https://exemplo.test/arquivo.pdf');
    $this->get(route('backup.download', ['url' => 'https://exemplo.test/backup.zip']))->assertRedirect('https://exemplo.test/backup.zip');
});

test('a escolaridade é devolvida em json', function () {
    $degree = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Ensino Superior']);

    $this->getJson("/escolaridade/{$degree->getKey()}")
        ->assertOk()
        ->assertJsonFragment(['description' => 'Ensino Superior']);
});

test('o login social redireciona ao provedor', function () {
    auth()->logout();
    Socialite::shouldReceive('driver->redirect')->once()->andReturn(redirect('https://sso.exemplo.test'));

    $this->get(route('socialite.redirect'))->assertRedirect('https://sso.exemplo.test');
});
