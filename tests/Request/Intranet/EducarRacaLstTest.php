<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->race = fn (string $name, array $attributes = []) => LegacyRaceFactory::new()->create($attributes + ['nm_raca' => $name]);
    $this->detail = fn ($race) => 'educar_raca_det.php?cod_raca=' . $race->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_raca_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as raças em ordem de nome, com o link do detalhe e o botão Novo', function () {
    $beta = ($this->race)('Registro Beta');
    $alfa = ($this->race)('Registro Alfa');

    ($this->list)(['nm_raca' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_raca_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->race)('Registro Alfa');
    $beta = ($this->race)('Registro Beta');

    ($this->list)(['nm_raca' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('não lista raça inativa', function () {
    $inactive = ($this->race)('Registro Alfa');
    $inactive->forceFill(['ativo' => false])->save();

    ($this->list)(['nm_raca' => 'Registro'])->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->race)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_raca' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_raca' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $race = ($this->race)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(678, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['nm_raca' => 'Registro'])
        ->assertSee(($this->detail)($race), false)
        ->assertDontSee('go("educar_raca_cad.php")', false);
});
