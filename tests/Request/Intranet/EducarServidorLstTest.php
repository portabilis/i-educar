<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->employee = fn (string $name, array $employee = [], array $individual = []) => EmployeeScenario::named($name, $this->institution, $employee, $individual);
    $this->detail = fn (EmployeeScenario $scenario) => "educar_servidor_det.php?cod_servidor={$scenario->employee->getKey()}&ref_cod_instituicao={$scenario->institution}";
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_servidor_lst.php?' . http_build_query($query + ['servidor_sem_alocacao' => 'on']))->assertOk();
});

test('lista os servidores em ordem de nome, com as matrículas, o CPF, a instituição e o botão Novo', function () {
    $beta = ($this->employee)('Registro Beta');
    $alfa = ($this->employee)('Registro Alfa', individual: ['cpf' => '12345678909']);
    $alfa->role('Professor', '1001');
    $alfa->role('Coordenador', '2002', teacher: false);

    ($this->list)(['nome' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', '123.456.789-09', 'Rede Aurora', 'Registro Beta', 'Rede Aurora'])
        ->assertSee('1001, 2002')
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_servidor_cad.php")', false);
});

test('filtra por cada campo', function (Closure $query) {
    $degree = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Mestrado']);

    $alfa = ($this->employee)('Registro Álfa', ['ref_idesco' => $degree]);
    $alfa->role('Professor', 'MAT-1001');

    $beta = EmployeeScenario::named('Registro Beta', LegacyInstitutionFactory::new()->create()->getKey());
    $beta->role('Professor', 'MAT-2002');

    ($this->list)($query->call($this, $alfa, $degree))
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
})->with([
    'nome, sem acento e por trecho' => [fn () => ['nome' => 'tro alf']],
    'matrícula' => [fn () => ['matricula_servidor' => 'mat-1001']],
    'instituição' => [fn ($alfa) => ['ref_cod_instituicao' => $alfa->institution]],
    'escolaridade' => [fn ($alfa, $degree) => ['idesco' => $degree->getKey()]],
    'código' => [fn ($alfa) => ['cod_servidor' => $alfa->employee->getKey()]],
]);

test('com escola ou ano informados só lista quem tem alocação ativa neles, salvo se marcar "sem alocação"', function () {
    $school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);

    $allocated = ($this->employee)('Registro Alfa');
    $allocated->allocation($school, ['ano' => 2030]);
    $free = ($this->employee)('Registro Beta');

    $list = fn (array $query) => $this->get('/intranet/educar_servidor_lst.php?' . http_build_query($query + ['nome' => 'Registro']))->assertOk();

    // Sem escola nem ano o filtro de alocação não entra.
    $list([])->assertSee(($this->detail)($allocated), false)->assertSee(($this->detail)($free), false);

    $list(['ref_cod_escola' => $school->getKey(), 'ano_letivo' => 2030])
        ->assertSee(($this->detail)($allocated), false)
        ->assertDontSee(($this->detail)($free), false);

    $list(['ref_cod_escola' => $school->getKey(), 'ano_letivo' => 2031])->assertDontSee(($this->detail)($allocated), false);

    $list(['ref_cod_escola' => $school->getKey(), 'ano_letivo' => 2030, 'servidor_sem_alocacao' => 'on'])
        ->assertSee(($this->detail)($allocated), false)
        ->assertSee(($this->detail)($free), false);
});

test('não lista servidor inativo', function () {
    $inactive = ($this->employee)('Registro Alfa', ['ativo' => 0]);

    ($this->list)(['nome' => 'Registro'])->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->employee)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nome' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nome' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)()->assertDontSee('go("educar_servidor_cad.php")', false);
});
