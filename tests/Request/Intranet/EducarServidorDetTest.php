<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, (int) date('Y'));
    $this->institution = $this->school->institution;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->employee = EmployeeScenario::named('Ana Alves', $this->institution, ['carga_horaria' => 20.5, 'multi_seriado' => true]);
    $this->keys = "ref_cod_servidor={$this->employee->employee->getKey()}&ref_cod_instituicao={$this->institution}";

    $this->detail = fn () => $this->get("/intranet/educar_servidor_det.php?cod_servidor={$this->employee->employee->getKey()}&ref_cod_instituicao={$this->institution}")->assertOk();
});

test('mostra os dados do servidor, a carga horária em horas e minutos e os botões', function () {
    $this->employee->inep(123456789, 'ANA ALVES INEP');

    ($this->detail)()
        ->assertSeeInOrder([
            'Servidor', (string) $this->employee->employee->getKey(),
            'Nome', 'Ana Alves',
            'Código Educacenso/Inep', '123456789',
            'Nome Educacenso/Inep', 'ANA ALVES INEP',
            'Instituição', 'Rede Aurora',
            'Escolaridade', 'Ensino Superior',
            'Multisseriado', 'Sim',
            'Carga Horária', '20:30',
        ])
        ->assertSee('<a href="/intranet/atendidos_det.php?cod_pessoa=' . $this->employee->employee->getKey() . '" target="_blank">Ana Alves</a>', false)
        ->assertSee('educar_servidor_cad.php?cod_servidor=' . $this->employee->employee->getKey() . '&ref_cod_instituicao=' . $this->institution, false)
        ->assertSee("go(\"educar_avaliacao_desempenho_lst.php?{$this->keys}\");", false)
        ->assertSee("go(\"educar_falta_atraso_lst.php?{$this->keys}\");", false)
        ->assertSee("go(\"educar_servidor_alocacao_lst.php?{$this->keys}\");", false)
        ->assertSee("go(\"educar_servidor_afastamento_cad.php?{$this->keys}\");", false)
        ->assertDontSee('educar_servidor_substituicao_cad.php', false)
        ->assertDontSee('educar_servidor_vinculo_turma_lst.php', false)
        ->assertDontSee('Histórico de afastamentos')
        ->assertDontSee('Horários de trabalho')
        ->assertDontSee('Horários de aula');
});

test('lista as funções com a matrícula e, para professor, os cursos e componentes que leciona', function () {
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');

    $teacher = $this->employee->role('Professor', '1001');
    $this->employee->discipline($teacher, $arts, $this->school->course)->discipline($teacher, $botany, $this->school->course);
    $this->employee->role('Coordenador', '2002', teacher: false);

    ($this->detail)()
        ->assertSeeInOrder(['Função', 'Matrícula', 'Cursos ministrados', 'Componentes curriculares', 'Professor', '1001', 'Ensino Fundamental', 'Coordenador', '2002'])
        ->assertSee('Artes; Botânica')
        ->assertSee("go(\"educar_servidor_vinculo_turma_lst.php?{$this->keys}\");", false);
});

test('mostra as alocações ativas do ano corrente e oferece substituir o horário', function () {
    $this->employee->allocation($this->school->school, ['periodo' => 1, 'carga_horaria' => '20:00:00']);
    $this->employee->allocation($this->school->school, ['periodo' => 2, 'carga_horaria' => '10:00:00']);
    $this->employee->allocation($this->school->school, ['periodo' => 3, 'carga_horaria' => '05:00:00']);
    $this->employee->allocation($this->school->school, ['periodo' => 1, 'carga_horaria' => '33:00:00', 'ano' => (int) date('Y') - 1]);
    $this->employee->allocation($this->school->school, ['periodo' => 1, 'carga_horaria' => '44:00:00', 'data_saida' => now()]);

    ($this->detail)()
        ->assertSeeInOrder(['Horários de trabalho', 'Carga Horária', 'Período', 'Escola', '20:00:00', 'Matutino', 'Escola Aurora', '10:00:00', 'Vespertino', '05:00:00', 'Noturno'])
        ->assertDontSee('33:00:00')
        ->assertDontSee('44:00:00')
        ->assertSee("go(\"educar_servidor_substituicao_cad.php?{$this->keys}\");", false);
});

test('com um afastamento em aberto oferece retornar o servidor e mostra o histórico', function () {
    $open = $this->employee->withdrawal('Licença médica', ['data_saida' => '2030-03-02']);
    $this->employee->withdrawal('Licença prêmio', ['data_saida' => '2029-01-10', 'data_retorno' => '2029-02-10']);

    ($this->detail)()
        ->assertSee("go(\"educar_servidor_afastamento_cad.php?{$this->keys}&sequencial={$open->sequencial}&retornar_servidor=", false)
        ->assertDontSee("go(\"educar_servidor_afastamento_cad.php?{$this->keys}\");", false)
        ->assertSee('Histórico de afastamentos')
        ->assertSee('Licença médica')
        ->assertSee('02/03/2030')
        ->assertSeeInOrder(['Licença prêmio', '10/01/2029', '10/02/2029'])
        ->assertSee('title="Editar histórico"', false)
        ->assertSee('title="Excluir histórico"', false);
});

test('quem só pode visualizar não vê os botões nem edita o histórico', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->employee->withdrawal();

    $this->actingAs(
        LegacyUserFactory::new()->school()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->detail)()
        ->assertSee('Ana Alves')
        ->assertSee('Histórico de afastamentos')
        ->assertDontSee('educar_servidor_cad.php', false)
        ->assertDontSee('educar_falta_atraso_lst.php', false)
        ->assertDontSee('title="Editar histórico"', false)
        ->assertDontSee('title="Excluir histórico"', false);
});

test('volta para a listagem quando o servidor não existe ou é de outra instituição', function (Closure $query) {
    $this->get('/intranet/educar_servidor_det.php?' . $query->call($this))->assertRedirect('educar_servidor_lst.php');
})->with([
    'código inexistente' => [fn () => "cod_servidor=0&ref_cod_instituicao={$this->institution}"],
    'outra instituição' => [fn () => "cod_servidor={$this->employee->employee->getKey()}&ref_cod_instituicao=0"],
]);
