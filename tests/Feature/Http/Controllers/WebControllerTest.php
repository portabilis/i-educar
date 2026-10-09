<?php

use App\Models\LegacyInstitution;
use App\Process;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->withAccess(Process::REGISTRATIONS, modify: false, remove: false)->create();
    $this->actingAs($this->user);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true, 'ieducar_internal_footer' => 'Rodapé']);
});

test('entrega ao front os dados do usuário, as permissões, os menus, a configuração e a instituição', function () {
    $this->getJson('/user')
        ->assertOk()
        ->assertJsonPath('cod_usuario', $this->user->getKey())
        ->assertJsonPath('employee.matricula', $this->user->employee->matricula)
        ->assertJsonStructure(['person' => ['id', 'name', 'nome', 'url', 'email', 'slug'], 'type' => ['cod_tipo_usuario', 'nm_tipo', 'nivel']]);

    $this->getJson('/authorization')
        ->assertOk()
        ->assertExactJson(['view:' . Process::REGISTRATIONS]);

    $this->getJson('/menus')->assertOk();

    $this->getJson('/config')->assertOk()->assertExactJson(['footer' => 'Rodapé']);

    $institution = app(LegacyInstitution::class);
    $this->getJson('/institution')
        ->assertOk()
        ->assertJson(['name' => $institution->nm_instituicao, 'city' => $institution->cidade . '/' . $institution->ref_sigla_uf]);
});

test('a página inicial vai para o front e os caminhos do front desconhecidos voltam ao legado', function () {
    $this->get('/')->assertRedirect(config('app.home'));
    $this->get('/intranet/index.php')->assertRedirect(config('app.home'));
    $this->get('/web/qualquer-coisa')->assertRedirect('intranet/educar_index.php');
    $this->get('/nao-existe')->assertNotFound();
});

test('as permissões de cadastrar e excluir também vão para o front', function () {
    $user = LegacyUserFactory::new()->admin()->withAccess(Process::ENROLLMENT)->create();
    $this->actingAs($user);

    $this->getJson('/authorization')
        ->assertOk()
        ->assertExactJson(['view:' . Process::ENROLLMENT, 'modify:' . Process::ENROLLMENT, 'remove:' . Process::ENROLLMENT]);
});
