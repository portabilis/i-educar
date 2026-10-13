<?php

use App\Models\LegacySchoolClassTeacher;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->math = $this->scenario->discipline('Matemática');
    $this->origin = $this->scenario->schoolClass(['nm_turma' => 'Turma 2029', 'ano' => 2029]);
    $this->destiny = $this->scenario->schoolClass(['nm_turma' => 'Turma 2030']);
    $this->ana = EmployeeScenario::named('Ana Alves', $this->scenario->institution);
    $this->ana->classLink($this->origin, ['ano' => 2029], [$this->math]);

    $this->url = '/intranet/copia_vinculos_servidores_cad.php?cod_turma=' . $this->destiny->getKey();
    $this->links = fn ($schoolClass) => LegacySchoolClassTeacher::query()->where('turma_id', $schoolClass->getKey())->get();
});

test('oferece as turmas do ano anterior da mesma série como origem', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee('Turma 2029 (2029)')
        ->assertSee('Turma destino');
});

test('copia os vínculos da turma de origem para a turma sem vínculos, e recusa quando já há', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['ref_cod_turma_origem' => $this->origin->getKey()]);

    $this->post($this->url, $payload)
        ->assertRedirect('educar_turma_det.php?cod_turma=' . $this->destiny->getKey())
        ->assertSessionHas('legacy', fn ($message) => str_contains($message, 'A cópia de vínculos dos servidores entre turmas foi efetuada.'));

    $copied = ($this->links)($this->destiny);
    expect($copied)->toHaveCount(1)
        ->and($copied->first()->servidor_id)->toBe($this->ana->employee->getKey())
        ->and($copied->first()->ano)->toBe(2030)
        ->and(($this->links)($this->origin))->toHaveCount(1);

    $this->post($this->url, $payload)
        ->assertRedirect('educar_turma_det.php?cod_turma=' . $this->destiny->getKey())
        ->assertSessionHas('legacy', 'A turma atual já possui vínculos com servidores.<br>');

    expect(($this->links)($this->destiny))->toHaveCount(1);
});
