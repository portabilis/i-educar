<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->url = fn ($link) => '/intranet/educar_servidor_vinculo_turma_det.php?id=' . $link->getKey();
});

test('mostra a turma, a função, o tipo de vínculo, os componentes e os botões', function () {
    $link = $this->ana->classLink(
        $this->school->schoolClass(['nm_turma' => 'Turma Alfa']),
        ['funcao_exercida' => 8, 'tipo_vinculo' => 4],
        [$this->school->discipline('Matemática'), $this->school->discipline('Artes')]
    );

    $query = "ref_cod_instituicao={$this->institution}&ref_cod_servidor={$this->id}";

    $this->get(($this->url)($link))
        ->assertOk()
        ->assertSeeInOrder([
            'Escola', 'Escola Aurora',
            'Curso', 'Ensino Fundamental',
            'Série', 'Primeiro Ano',
            'Turma', 'Turma Alfa',
            'Função exercida', 'Profissional de apoio escolar para aluno(a)s com deficiência (Lei 13.146/2015)',
            'Tipo de vínculo', 'Contrato CLT',
            'Disciplinas', 'Artes', 'Matemática',
        ])
        ->assertSee("educar_servidor_vinculo_turma_cad.php?{$query}", false)
        ->assertSee("educar_servidor_vinculo_turma_cad.php?id={$link->getKey()}&{$query}", false)
        ->assertSee("go(\"educar_servidor_vinculo_turma_cad.php?id={$link->getKey()}&{$query}&copia\");", false)
        ->assertSee("educar_servidor_vinculo_turma_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}", false);
});

test('sem componentes nem tipo de vínculo omite essas linhas', function () {
    $link = $this->ana->classLink($this->school->schoolClass(), ['tipo_vinculo' => null]);

    $this->get(($this->url)($link))
        ->assertOk()
        ->assertSeeInOrder(['Função exercida', 'Docente'])
        ->assertDontSee('Tipo de vínculo')
        ->assertDontSee('Disciplinas');
});

test('não oferece novo, editar nem copiar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $link = $this->ana->classLink($this->school->schoolClass());

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)($link))
        ->assertOk()
        ->assertSee('Docente')
        ->assertDontSee('educar_servidor_vinculo_turma_cad.php', false);
});

test('defeito: com um vínculo que não existe, redireciona para uma tela que não existe', function () {
    $this->get('/intranet/educar_servidor_vinculo_turma_det.php?id=0')->assertRedirect('educar_servidor_professor_vinculo_lst.php');

    expect(file_exists(base_path('ieducar/intranet/educar_servidor_professor_vinculo_lst.php')))->toBeFalse();
});
