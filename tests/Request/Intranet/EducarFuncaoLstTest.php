<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->role = fn (string $name, array $attributes = []) => LegacyRoleFactory::new()->create($attributes + [
        'nm_funcao' => $name,
        'abreviatura' => 'ABR',
        'professor' => 1,
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->detail = fn ($role) => "educar_funcao_det.php?cod_funcao={$role->getKey()}&ref_cod_instituicao={$role->ref_cod_instituicao}";
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_funcao_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as funções em ordem de nome, com a abreviatura, se é professor, a instituição e o botão Novo', function () {
    $beta = ($this->role)('Registro Beta', ['abreviatura' => 'RB', 'professor' => 0]);
    $alfa = ($this->role)('Registro Alfa', ['abreviatura' => 'RA']);

    ($this->list)(['nm_funcao' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'RA', 'Sim', 'Rede Aurora', 'Registro Beta', 'RB', 'Não', 'Rede Aurora'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_funcao_cad.php")', false);
});

test('filtra pelo nome, por ser ou não professor e pela instituição', function (Closure $query) {
    $alfa = ($this->role)('Registro Alfa', ['professor' => 1]);
    $beta = ($this->role)('Registro Beta', ['professor' => 0, 'ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]);

    ($this->list)($query->call($this) + ['nm_funcao' => 'Registro'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
})->with([
    'nome' => [fn () => ['nm_funcao' => 'tro alf']],
    'professor' => [fn () => ['professor' => 'S']],
    'instituição' => [fn () => ['ref_cod_instituicao' => $this->institution]],
]);

test('filtra as funções que não são de professor', function () {
    $alfa = ($this->role)('Registro Alfa', ['professor' => 1]);
    $beta = ($this->role)('Registro Beta', ['professor' => 0]);

    ($this->list)(['nm_funcao' => 'Registro', 'professor' => 'N'])
        ->assertSee(($this->detail)($beta), false)
        ->assertDontSee(($this->detail)($alfa), false);
});

test('defeito: o filtro de abreviatura procura no nome da função', function () {
    $role = ($this->role)('Registro Alfa', ['abreviatura' => 'XYZ']);

    ($this->list)(['abreviatura' => 'XYZ'])->assertDontSee(($this->detail)($role), false);
    ($this->list)(['abreviatura' => 'tro Alf'])->assertSee(($this->detail)($role), false);
});

test('não lista função inativa', function () {
    $inactive = ($this->role)('Registro Alfa');
    $inactive->forceFill(['ativo' => 0])->save();

    ($this->list)(['nm_funcao' => 'Registro'])->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->role)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_funcao' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_funcao' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('fora do nível de administrador a instituição não é coluna, e sem permissão não há botão Novo', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $role = ($this->role)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(634, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)(['nm_funcao' => 'Registro'])
        ->assertSee(($this->detail)($role), false)
        ->assertDontSee('Rede Aurora</a>', false)
        ->assertDontSee('go("educar_funcao_cad.php")', false);
});
