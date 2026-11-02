<?php

use App\Models\LegacyBondType;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->bond = fn (string $name, string $short = 'RG') => LegacyBondType::query()->create(['nm_vinculo' => $name, 'abreviatura' => $short]);
    $this->detail = fn ($bond) => 'funcionario_vinculo_det.php?cod_func=' . $bond->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/funcionario_vinculo_lst.php?' . http_build_query($query))->assertOk();
});

test('lista os vínculos em ordem de nome, com o link do detalhe e o botão Novo', function () {
    $beta = ($this->bond)('Registro Beta');
    $alfa = ($this->bond)('Registro Alfa');

    ($this->list)(['nome_' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("funcionario_vinculo_cad.php")', false);
});

test('filtra pelo nome por trecho', function () {
    $alfa = ($this->bond)('Registro Alfa');
    $beta = ($this->bond)('Registro Beta');

    ($this->list)(['nome_' => 'tro Alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('pagina de 10 em 10', function () {
    foreach (range(1, 11) as $number) {
        ($this->bond)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nome_' => 'Registro'])->assertSee('Registro 10')->assertDontSee('Registro 11');
    ($this->list)(['nome_' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 11')->assertDontSee('Registro 10');
});
