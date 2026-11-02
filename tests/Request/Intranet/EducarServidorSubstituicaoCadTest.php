<?php

use App\Models\EmployeeAllocation;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, (int) date('Y'));
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->bia = EmployeeScenario::named('Bia Borges', $this->institution);
    $this->id = $this->ana->employee->getKey();
    $this->substitute = $this->bia->employee->getKey();

    $this->url = "/intranet/educar_servidor_substituicao_cad.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->detail = "educar_servidor_det.php?cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";

    $this->submit = fn (array $overrides = []) => $this->post(
        $this->url,
        $overrides + ['ref_cod_servidor_todos' => (string) $this->substitute] + LegacyFields::from($this->get($this->url)->assertOk()->getContent())
    );
});

test('abre a substituição com o servidor e as alocações ativas do ano', function () {
    $this->ana->role('Professor', '1001');
    $this->ana->allocation($this->school->school, ['periodo' => 1, 'carga_horaria' => '20:00:00']);
    $this->ana->allocation($this->school->school, ['periodo' => 2, 'carga_horaria' => '10:00:00', 'ano' => (int) date('Y') - 1]);

    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_servidor'])->toBe((string) $this->id)
        ->and(unserialize(urldecode($fields['alocacao_array'])))->toBe([
            ['carga_horaria' => '20:00:00', 'periodo' => 1, 'ref_cod_escola' => $this->school->school->getKey()],
        ])
        ->and($html)->toContain("educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos_&campo2=ref_cod_servidor_todos&ref_cod_instituicao={$this->institution}&ref_cod_servidor={$this->id}");
});

test('passa as alocações do servidor para o substituto', function () {
    $first = $this->ana->allocation($this->school->school, ['periodo' => 1, 'carga_horaria' => '20:00:00']);
    $second = $this->ana->allocation($this->school->school, ['periodo' => 2, 'carga_horaria' => '10:00:00']);
    $lastYear = $this->ana->allocation($this->school->school, ['periodo' => 3, 'carga_horaria' => '05:00:00', 'ano' => (int) date('Y') - 1]);

    ($this->submit)()
        ->assertRedirect($this->detail)
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br>');

    expect($first->refresh()->ref_cod_servidor)->toBe($this->substitute)
        ->and($second->refresh()->ref_cod_servidor)->toBe($this->substitute)
        ->and($lastYear->refresh()->ref_cod_servidor)->toBe($this->id);
});

test('recusa o substituto que não é servidor da instituição ou não foi informado', function (string $substitute) {
    $allocation = $this->ana->allocation($this->school->school);

    ($this->submit)(['ref_cod_servidor_todos' => $substitute])
        ->assertOk()
        ->assertSee('Substituicao não realizado.');

    expect($allocation->refresh()->ref_cod_servidor)->toBe($this->id);
})->with(['inexistente' => ['999999999'], 'vazio' => ['']]);

test('recusa a alocação enviada sem período ou sem carga', function (array $allocation) {
    $this->ana->allocation($this->school->school);

    ($this->submit)(['alocacao_array' => urlencode(serialize([$allocation + ['ref_cod_escola' => $this->school->school->getKey()]]))])
        ->assertOk()
        ->assertSee('Substituicao não realizado.');
})->with([
    'sem período' => [['carga_horaria' => '20:00:00', 'periodo' => '']],
    'sem carga' => [['carga_horaria' => null, 'periodo' => 1]],
]);

test('sem alocações no ano não há o que substituir, e a tela responde sucesso', function () {
    ($this->submit)()->assertRedirect($this->detail);

    expect(EmployeeAllocation::query()->where('ref_cod_servidor', $this->substitute)->exists())->toBeFalse();
});

test('as ações de editar e excluir não existem', function (string $action, string $message) {
    ($this->submit)(['tipoacao' => $action])->assertOk()->assertSee($message);
})->with([
    ['Editar', 'Não foi possível editar a informação. [CAD02]'],
    ['Excluir', 'Não foi possível excluir a informação. [CAD03]'],
]);

test('volta para a listagem de servidores sem servidor na URL ou com um que não é da instituição', function (string $query) {
    $this->get('/intranet/educar_servidor_substituicao_cad.php' . $query)->assertRedirect('educar_servidor_lst.php');
})->with(['sem parâmetros' => [''], 'servidor inexistente' => ['?ref_cod_servidor=999999999&ref_cod_instituicao=1']]);

test('quem só pode visualizar não abre nem substitui', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $allocation = $this->ana->allocation($this->school->school);
    $payload = ['ref_cod_servidor_todos' => (string) $this->substitute] + LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->url)->assertRedirect('educar_servidor_lst.php');
    $this->post($this->url, $payload)->assertRedirect('educar_servidor_alocacao_lst.php');

    expect($allocation->refresh()->ref_cod_servidor)->toBe($this->id);
});

test('a alocação enviada sem escola vale para qualquer escola em que o servidor esteja alocado', function () {
    $allocation = $this->ana->allocation($this->school->school);

    ($this->submit)(['alocacao_array' => urlencode(serialize([['carga_horaria' => '20:00:00', 'periodo' => 1, 'ref_cod_escola' => null]]))])
        ->assertOk()
        ->assertSee('Substituicao não realizado.');

    expect($allocation->refresh()->ref_cod_servidor)->toBe($this->id);
});
