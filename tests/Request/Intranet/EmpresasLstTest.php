<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->organization = fn (string $name, string $trade, array $attributes = []) => LegacyOrganizationFactory::new()->create($attributes + [
        'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'J']),
        'fantasia' => $trade,
    ]);
    $this->detail = fn ($organization) => 'empresas_det.php?cod_empresa=' . $organization->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/empresas_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as empresas em ordem de nome fantasia, com a razão social e o botão Novo', function () {
    $beta = ($this->organization)('Registro Beta Ltda', 'Fantasia Beta');
    $alfa = ($this->organization)('Registro Alfa Ltda', 'Fantasia Alfa');

    ($this->list)(['fantasia' => 'Fantasia'])
        ->assertSeeInOrder(['Registro Alfa Ltda', 'Fantasia Alfa', 'Registro Beta Ltda', 'Fantasia Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("empresas_cad.php")', false);
});

test('filtra pelo nome fantasia ou razão social, pela razão social e pelo CNPJ', function (array $query) {
    $alfa = ($this->organization)('Registro Álfa Ltda', 'Fantasia Um', ['cnpj' => '12345678000195']);
    $beta = ($this->organization)('Registro Beta Ltda', 'Fantasia Dois', ['cnpj' => '98765432000100']);

    ($this->list)($query)
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
})->with([
    'nome fantasia' => [['fantasia' => 'tasia um']],
    'razão social pelo campo de fantasia' => [['fantasia' => 'alfa ltda']],
    'razão social, sem acento e com as palavras separadas' => [['razao_social' => 'alfa ltda']],
    'CNPJ com máscara' => [['id_federal' => '12.345.678/0001-95']],
]);

test('pagina de 10 em 10', function () {
    foreach (range(1, 11) as $number) {
        ($this->organization)(sprintf('Registro %02d Ltda', $number), sprintf('Fantasia %02d', $number));
    }

    ($this->list)(['fantasia' => 'Fantasia'])->assertSee('Fantasia 10')->assertDontSee('Fantasia 11');
    ($this->list)(['fantasia' => 'Fantasia', 'pagina_formulario' => 2])->assertSee('Fantasia 11')->assertDontSee('Fantasia 10');
});

test('o usuário de biblioteca só vê as empresas das suas escolas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $mine = ($this->organization)('Registro Alfa Ltda', 'Fantasia Alfa');
    $other = ($this->organization)('Registro Beta Ltda', 'Fantasia Beta');
    $school = LegacySchoolFactory::new()->create(['ref_idpes' => $mine->getKey(), 'ref_cod_instituicao' => $this->user->ref_cod_instituicao]);

    $this->actingAs(LegacyUserFactory::new()->withSchool($school)->withAccess(41)->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    ($this->list)(['fantasia' => 'Fantasia'])
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(41, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)()->assertDontSee('go("empresas_cad.php")', false);
});
