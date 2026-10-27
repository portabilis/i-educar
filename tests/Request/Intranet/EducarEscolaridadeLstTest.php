<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->degree = fn (string $name) => LegacySchoolingDegreeFactory::new()->create(['descricao' => $name]);
    $this->detail = fn ($degree) => 'educar_escolaridade_det.php?idesco=' . $degree->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_escolaridade_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as escolaridades em ordem de descrição, com o link do detalhe e o botão Novo', function () {
    $beta = ($this->degree)('Registro Beta');
    $alfa = ($this->degree)('Registro Alfa');

    ($this->list)(['descricao' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_escolaridade_cad.php")', false);
});

test('filtra pela descrição, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->degree)('Registro Alfa');
    $beta = ($this->degree)('Registro Beta');

    ($this->list)(['descricao' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->degree)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['descricao' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['descricao' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $degree = ($this->degree)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(632, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['descricao' => 'Registro'])
        ->assertSee(($this->detail)($degree), false)
        ->assertDontSee('go("educar_escolaridade_cad.php")', false);
});
