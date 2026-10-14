<?php

use App\Models\State;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\StateFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class, WithoutMiddleware::class);

beforeEach(function () {
    $this->assertEscaped = function (TestResponse $response): void {
        $response->assertSuccessful();
        $response->assertDontSee('<b>CVECANARY</b>', false);
        $response->assertSee(e('<b>CVECANARY</b>'), false);
    };

    $this->loginWithFirstUser();
});

test('paginador escapa os parametros mantidos', function () {
    LegacyRaceFactory::new()->create();

    $response = $this->get('/intranet/educar_raca_lst.php?' . http_build_query(['filtro' => ('x"\'>' . '<b>CVECANARY</b>')]));

    $response->assertSuccessful();
    $response->assertDontSee('<b>CVECANARY</b>', false);
    $response->assertSee('filtro=' . urlencode(('x"\'>' . '<b>CVECANARY</b>')), false);
});

test('listagem escapa a ordenacao enviada', function () {
    LegacyRaceFactory::new()->create();

    ($this->assertEscaped)(
        $this->post('/intranet/educar_raca_lst.php', ['ordenacao' => ('x"\'>' . '<b>CVECANARY</b>')])
    );
});

test('campo cpf escapa o valor informado', function () {
    ($this->assertEscaped)(
        $this->get('/intranet/pesquisa_pessoa_lst.php?' . http_build_query(['cpf' => ('x"\'>' . '<b>CVECANARY</b>')]))
    );
});

test('cadastro escapa o tipoacao enviado', function () {
    ($this->assertEscaped)(
        $this->post('/intranet/educar_funcao_cad.php', ['tipoacao' => ('x"\'>' . '<b>CVECANARY</b>')])
    );
});

test('listagem de matriculas usa o codigo do aluno como inteiro', function () {
    $student = LegacyStudentFactory::new()->create();

    $response = $this->get('/intranet/educar_matricula_lst.php?' . http_build_query([
        'ref_cod_aluno' => $student->getKey() . ('x"\'>' . '<b>CVECANARY</b>'),
    ]));

    $response->assertSuccessful();
    $response->assertDontSee('CVECANARY', false);
});

test('listagem de beneficios escapa o nome gravado', function () {
    LegacyBenefitFactory::new()->create(['name' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_aluno_beneficio_lst.php'));
});

test('detalhe do beneficio escapa nome e descricao gravados', function () {
    $benefit = LegacyBenefitFactory::new()->create([
        'name' => ('x"\'>' . '<b>CVECANARY</b>'),
        'description' => ('x"\'>' . '<b>CVECANARY</b>'),
    ]);

    ($this->assertEscaped)($this->get('/intranet/educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $benefit->getKey()));
});

test('edicao do beneficio escapa a descricao no textarea', function () {
    $benefit = LegacyBenefitFactory::new()->create(['description' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_aluno_beneficio_cad.php?cod_aluno_beneficio=' . $benefit->getKey()));
});

test('listagem de racas escapa o nome gravado', function () {
    LegacyRaceFactory::new()->create(['nm_raca' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_raca_lst.php'));
});

test('detalhe da raca escapa o nome gravado', function () {
    $race = LegacyRaceFactory::new()->create(['nm_raca' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_raca_det.php?cod_raca=' . $race->getKey()));
});

test('tipo de usuario escapa nome e descricao na listagem', function () {
    LegacyUserTypeFactory::new()->create([
        'nm_tipo' => ('x"\'>' . '<b>CVECANARY</b>'),
        'descricao' => ('x"\'>' . '<b>CVECANARY</b>'),
    ]);

    ($this->assertEscaped)($this->get('/intranet/educar_tipo_usuario_lst.php'));
});

test('uf escapa o nome gravado', function () {
    /** @var State $state */
    $state = StateFactory::new()->create(['name' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/public_uf_det.php?id=' . $state->getKey()));
});

test('listagem de escolaridade escapa a descricao gravada', function () {
    LegacySchoolingDegreeFactory::new()->create(['descricao' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_escolaridade_lst.php?descricao=CVECANARY'));
});

test('detalhe de escolaridade escapa a descricao gravada', function () {
    $schoolingDegree = LegacySchoolingDegreeFactory::new()->create(['descricao' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_escolaridade_det.php?idesco=' . $schoolingDegree->getKey()));
});

test('listagem de servidores escapa a matricula gravada', function () {
    LegacyEmployeeRoleFactory::new()->create(['matricula' => ('x"\'>' . '<b>CVECANARY</b>')]);

    ($this->assertEscaped)($this->get('/intranet/educar_servidor_lst.php?matricula_servidor=CVECANARY'));
});

test('calendario escapa motivo e descricao do dia', function () {
    $schoolAcademicYear = LegacySchoolAcademicYearFactory::new()->create(['ano' => now()->year]);
    LegacyAcademicYearStageFactory::new()->create(['escola_ano_letivo_id' => $schoolAcademicYear->getKey()]);
    $calendarYear = LegacyCalendarYearFactory::new()->create([
        'ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
        'ano' => now()->year,
    ]);
    LegacyCalendarDayFactory::new()->create([
        'ref_cod_calendario_ano_letivo' => $calendarYear->getKey(),
        'mes' => now()->month,
        'dia' => now()->day,
        'descricao' => ('x"\'>' . '<b>CVECANARY</b>'),
        'ref_cod_calendario_dia_motivo' => LegacyCalendarDayReasonFactory::new()->create([
            'ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
            'nm_motivo' => ('x"\'>' . '<b>CVECANARY</b>'),
        ]),
    ]);

    $response = $this->post('/intranet/educar_calendario_ano_letivo_lst.php', [
        'ref_cod_instituicao' => $schoolAcademicYear->school->ref_cod_instituicao,
        'ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
        'ano' => now()->year,
        'mes' => now()->month,
    ]);

    ($this->assertEscaped)($response);
    $this->assertSame(2, substr_count($response->getContent(), e('<b>CVECANARY</b>')));
});
