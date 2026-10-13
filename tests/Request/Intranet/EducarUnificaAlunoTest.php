<?php

use App\Models\LogUnification;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Unification\StudentLogUnification;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->url = '/intranet/educar_unifica_aluno.php';
    $this->main = LegacyStudentFactory::new()->create();
    $this->duplicate = LegacyStudentFactory::new()->create();

    $this->students = fn (array ...$students) => ['tipoacao' => 'Novo', 'alunos' => json_encode($students)];
    $this->logs = fn () => LogUnification::query()->where('type', StudentLogUnification::getType())->where('main_id', $this->main->getKey());
});

test('abre a tela com a tabela de alunos e o envio por script', function () {
    $html = $this->get($this->url)
        ->assertOk()
        ->assertSee('Aluno(a) a ser unificado(a)')
        ->assertSee('carregaDadosAlunos()', false)
        ->getContent();

    expect(LegacyFields::from($html)['tipoacao'])->toBe('Novo');
});

test('unifica o aluno duplicado no principal, leva as matrículas e registra o log', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $this->duplicate]);

    $this->post($this->url, ($this->students)(
        ['codAluno' => $this->main->getKey(), 'aluno_principal' => true],
        ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => false],
    ))
        ->assertRedirect(route('student-log-unification.index'))
        ->assertSessionHas('legacy', '<span>Alunos unificados com sucesso.</span>');

    expect(($this->logs)()->sole())
        ->duplicates_id->toBe([$this->duplicate->getKey()])
        ->created_by->toBe($this->user->getKey())
        ->updated_by->toBe($this->user->getKey())
        ->and($registration->refresh()->ref_cod_aluno)->toBe($this->main->getKey())
        ->and($this->duplicate->refresh())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($this->main->refresh()->ativo)->toBe(1);
});

test('recusa dados incoerentes sem unificar', function (Closure $payload, string $message) {
    $this->post($this->url, ['tipoacao' => 'Novo', 'alunos' => $payload->call($this)])
        ->assertOk()
        ->assertSee($message);

    expect(($this->logs)()->exists())->toBeFalse()
        ->and($this->duplicate->refresh()->ativo)->toBe(1);
})->with([
    'texto que não é JSON' => [fn () => '{alunos', 'Informações inválidas para unificação'],
    'lista no lugar do texto' => [fn () => [['codAluno' => $this->main->getKey()]], 'Informações inválidas para unificação'],
    'sem o código' => [fn () => json_encode([['aluno_principal' => true], ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => false]]), 'Dados enviados inválidos, recarregue a tela e tente novamente!'],
    'sem a marca de principal' => [fn () => json_encode([['codAluno' => $this->main->getKey()], ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => false]]), 'Dados enviados inválidos, recarregue a tela e tente novamente!'],
    'nenhum principal' => [fn () => json_encode([['codAluno' => $this->main->getKey(), 'aluno_principal' => false], ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => false]]), 'Aluno principal não informado'],
    'dois principais' => [fn () => json_encode([['codAluno' => $this->main->getKey(), 'aluno_principal' => true], ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => true]]), 'Não pode haver mais de uma aluno principal'],
    'o mesmo aluno duas vezes' => [fn () => json_encode([['codAluno' => $this->main->getKey(), 'aluno_principal' => true], ['codAluno' => $this->main->getKey(), 'aluno_principal' => false]]), 'Erro ao tentar unificar Alunos, foi inserido cadastro duplicados'],
]);

test('volta para o início quando o usuário não pode unificar alunos', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(999847, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)->assertRedirect('index.php');

    $this->post($this->url, ($this->students)(
        ['codAluno' => $this->main->getKey(), 'aluno_principal' => true],
        ['codAluno' => $this->duplicate->getKey(), 'aluno_principal' => false],
    ))->assertRedirect('index.php');

    expect($this->duplicate->refresh()->ativo)->toBe(1);
});
