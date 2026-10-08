<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->religion = fn (string $name) => ReligionFactory::new()->create(['name' => $name]);
    $this->detail = fn ($religion) => 'educar_religiao_det.php?cod_religiao=' . $religion->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_religiao_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as religiões em ordem de nome, com o link do detalhe e o botão Novo', function () {
    $beta = ($this->religion)('Registro Beta');
    $alfa = ($this->religion)('Registro Alfa');

    ($this->list)(['nm_religiao' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_religiao_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->religion)('Registro Alfa');
    $beta = ($this->religion)('Registro Beta');

    ($this->list)(['nm_religiao' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('não lista religião excluída', function () {
    $deleted = ($this->religion)('Registro Alfa');
    $deleted->delete();

    ($this->list)(['nm_religiao' => 'Registro'])->assertDontSee(($this->detail)($deleted), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->religion)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_religiao' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_religiao' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $religion = ($this->religion)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(579, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['nm_religiao' => 'Registro'])
        ->assertSee(($this->detail)($religion), false)
        ->assertDontSee('go("educar_religiao_cad.php")', false);
});
