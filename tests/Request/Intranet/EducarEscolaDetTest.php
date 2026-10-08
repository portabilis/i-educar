<?php

use App\Models\LegacyPerson;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution + ['sigla' => 'EALFA']);
    $this->url = '/intranet/educar_escola_det.php?cod_escola=' . $this->school->getKey();

    $this->year = fn (int $ano, int $andamento) => LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ano' => $ano,
        'andamento' => $andamento,
    ]);

    // O link de cada ação da tabela de anos letivos chama preencheForm(ano, escola, ação).
    $this->action = fn (int $ano, string $acao) => "preencheForm('{$ano}','{$this->school->getKey()}','{$acao}')";

    // Fora do nível de administrador, a instituição precisa estar ativa no
    // i-Educar para o usuário não ser desviado para a tela de suspensão.
    $this->actingAsLevel = function (LegacyUserFactory $factory) {
        LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

        $this->actingAs($factory->create());
    };
});

test('mostra os dados básicos da escola', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee('Escola - Detalhe')
        ->assertSee($this->school->institution->nm_instituicao)
        ->assertSee('Escola Alfa')
        ->assertSee('EALFA')
        ->assertSee('Urbana')
        ->assertDontSee('Logradouro')
        ->assertDontSee('Telefone 1')
        ->assertDontSee('Anos letivos');
});

test('mostra endereço, site, e-mail e os quatro telefones quando existem', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Beta')->withAddress()->withPhones()->create($this->institution);

    LegacyPerson::query()->whereKey($school->ref_idpes)->update([
        'url' => 'https://escola-beta.test',
        'email' => 'contato@escola-beta.test',
    ]);

    // Endereço fixo: a tela imprime sem escapar, e um nome gerado com
    // apóstrofo não bateria com o assertSee.
    $school->addresses()->first()->update([
        'address' => 'Rua das Palmeiras',
        'neighborhood' => 'Jardim América',
        'number' => '742',
    ]);

    $this->get('/intranet/educar_escola_det.php?cod_escola=' . $school->getKey())
        ->assertOk()
        ->assertSee('Rua das Palmeiras')
        ->assertSee('Jardim América')
        ->assertSee('742')
        ->assertSee('https://escola-beta.test')
        ->assertSee('contato@escola-beta.test')
        ->assertSeeInOrder(['Telefone 1', '(48) 34221001', 'Telefone 2', '(48) 34221002', 'Celular', '(48) 991221003', 'Fax', '(48) 34221004']);
});

test('lista os cursos ativos da escola', function () {
    foreach (['Curso Alfa', 'Curso Beta', 'Curso Inativo'] as $name) {
        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $this->school,
            'ref_cod_curso' => LegacyCourseFactory::new()->withName($name)->create(),
            'ativo' => $name === 'Curso Inativo' ? 0 : 1,
        ]);
    }

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Curso Alfa')
        ->assertSee('Curso Beta')
        ->assertDontSee('Curso Inativo');
});

test('oferece iniciar e editar um ano letivo ainda não iniciado', function () {
    ($this->year)(2030, 0);

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Anos letivos')
        ->assertSee(($this->action)(2030, 'iniciar'), false)
        ->assertSee(($this->action)(2030, 'editar'), false)
        ->assertDontSee(($this->action)(2030, 'finalizar'), false);
});

test('oferece finalizar um ano em andamento sem matrículas em andamento', function () {
    ($this->year)(2030, 1);
    ($this->year)(2031, 0);

    $this->get($this->url)
        ->assertOk()
        ->assertSee(($this->action)(2030, 'finalizar'), false)
        ->assertSee(($this->action)(2030, 'editar'), false)
        ->assertDontSee(($this->action)(2030, 'iniciar'), false)
        // Um ano não iniciado pode ser iniciado mesmo havendo outro em andamento.
        ->assertSee(($this->action)(2031, 'iniciar'), false);
});

test('não oferece finalizar enquanto houver matrícula em andamento no ano', function () {
    ($this->year)(2030, 1);

    LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_escola' => $this->school,
        'ano' => 2030,
        'ativo' => 1,
        'ultima_matricula' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Anos letivos')
        ->assertDontSee(($this->action)(2030, 'finalizar'), false);
});

test('oferece reabrir um ano finalizado ao administrador', function () {
    ($this->year)(2029, 2);

    $this->get($this->url)
        ->assertOk()
        ->assertSee('--- Ano Finalizado ---')
        ->assertSee(($this->action)(2029, 'reabrir'), false)
        ->assertDontSee(($this->action)(2029, 'editar'), false);
});

test('não oferece reabrir um ano finalizado ao usuário de nível escola', function () {
    ($this->year)(2029, 2);

    ($this->actingAsLevel)(LegacyUserFactory::new()->school()->withSchool($this->school)->withAccess(561));

    $this->get($this->url)
        ->assertOk()
        ->assertSee('--- Ano Finalizado ---')
        ->assertDontSee(($this->action)(2029, 'reabrir'), false);
});

test('oferece novo, editar e definir ano letivo a quem pode alterar escola', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee('educar_escola_cad.php?cod_escola=' . $this->school->getKey(), false)
        ->assertSee('educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey(), false)
        ->assertSee('Definir Ano Letivo');
});

test('quem só pode visualizar não recebe botões nem ações nos anos letivos', function () {
    ($this->year)(2030, 1);

    ($this->actingAsLevel)(LegacyUserFactory::new()->institutional()->withAccess(561, modify: false, remove: false));

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Somente usuários com permissão de edição de escola podem alterar anos letivos.')
        ->assertDontSee(($this->action)(2030, 'finalizar'), false)
        ->assertDontSee(($this->action)(2030, 'editar'), false)
        ->assertDontSee('Definir Ano Letivo')
        ->assertDontSee('educar_escola_cad.php?cod_escola=' . $this->school->getKey(), false);
});

test('volta para a listagem de escolas avisando quando a escola não existe', function () {
    $this->get('/intranet/educar_escola_det.php?cod_escola=0')
        ->assertRedirect('educar_escola_lst.php')
        ->assertSessionHas('error', 'Escola não encontrada. O registro não existe ou foi excluído. Use a busca abaixo para localizá-la.');
});
