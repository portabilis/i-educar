<?php

use App\Events\EmployeeCreated;
use App\Models\EmployeeGraduation;
use App\Models\EmployeePosgraduate;
use App\Models\LegacyAbsenceDelay;
use App\Models\LegacyEmployeeRole;
use Database\Factories\EducacensoDegreeFactory;
use Database\Factories\EducacensoInstitutionFactory;
use Database\Factories\LegacyAbsenceDelayFactory;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, (int) date('Y'));
    $this->institution = $this->school->institution;

    $this->teacher = LegacyRoleFactory::new()->create(['nm_funcao' => 'Professor', 'professor' => 1, 'ref_cod_instituicao' => $this->institution]);
    $this->coordinator = LegacyRoleFactory::new()->create(['nm_funcao' => 'Coordenador', 'professor' => 0, 'ref_cod_instituicao' => $this->institution]);
    $this->higher = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Superior completo', 'escolaridade' => Escolaridade::EDUCACAO_SUPERIOR]);
    $this->middle = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Médio completo', 'escolaridade' => Escolaridade::ENSINO_MEDIO]);

    $this->new = '/intranet/educar_servidor_cad.php';
    $this->edit = fn (EmployeeScenario $scenario) => "{$this->new}?cod_servidor={$scenario->employee->getKey()}&ref_cod_instituicao={$scenario->institution}";
    $this->detail = fn (int $employee, ?int $institution = null) => "educar_servidor_det.php?cod_servidor={$employee}&ref_cod_instituicao=" . ($institution ?? $this->institution);

    $this->payload = fn (int $person, array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'cod_servidor' => (string) $person,
        'ref_cod_instituicao' => (string) $this->institution,
        'carga_horaria' => '20:30',
        'ref_idesco' => (string) $this->middle->getKey(),
        'ref_cod_funcao' => [$this->teacher->getKey() . '-1', $this->coordinator->getKey() . '-0'],
        'matricula' => ['1001', '2002'],
        'cod_servidor_funcao' => ['', ''],
    ];

    $this->row = fn (int $employee, ?int $institution = null) => DB::table('pmieducar.servidor')
        ->where('cod_servidor', $employee)
        ->where('ref_cod_instituicao', $institution ?? $this->institution)
        ->first();
    $this->roles = fn (int $employee) => LegacyEmployeeRole::query()->where('ref_cod_servidor', $employee)->orderBy('matricula')->pluck('ref_cod_funcao', 'matricula')->all();
    $this->disciplines = fn (int $employee) => DB::table('pmieducar.servidor_disciplina')->where('ref_cod_servidor', $employee)->orderBy('ref_cod_disciplina')->pluck('ref_cod_disciplina')->all();
    $this->courses = fn (int $employee) => DB::table('pmieducar.servidor_curso_ministra')->where('ref_cod_servidor', $employee)->pluck('ref_cod_curso')->all();

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('abre o cadastro com a pesquisa de pessoa, as funções ativas e as escolaridades', function () {
    LegacyRoleFactory::new()->create(['nm_funcao' => 'Função extinta', 'ref_cod_instituicao' => $this->institution])->forceFill(['ativo' => 0])->save();

    $html = $this->get($this->new . '?ref_cod_instituicao=' . $this->institution)->assertOk()->getContent();

    expect(LegacyFields::from($html)['tipoacao'])->toBe('Novo')
        ->and($html)->toContain('pesquisa_pessoa_lst.php', '>Professor<', '>Coordenador<', 'Superior completo', 'Médio completo')
        ->not->toContain('Função extinta', "id='btn_excluir'", 'Total de Horas Alocadadas');
});

test('cadastra o servidor com as funções, os componentes escolhidos na sessão, o Inep e as deficiências', function () {
    Event::fake([EmployeeCreated::class]);

    $person = StudentScenario::individual('Ana Alves')->getKey();
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');
    $deficiency = LegacyDeficiencyFactory::new()->create();

    $this->withSession(["servant:{$person}" => ['new_0' => [$this->school->course->getKey() => [$arts->getKey(), $botany->getKey()]]]])
        ->post($this->new, ($this->payload)($person, [
            'cod_docente_inep' => '123456789',
            'deficiencias' => [(string) $deficiency->getKey()],
            'multi_seriado' => 'on',
            'tipo_ensino_medio_cursado' => '2',
            'curso_formacao_continuada' => ['1', '5'],
        ]))
        ->assertRedirect(($this->detail)($person))
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br>');

    expect(($this->row)($person))
        ->ativo->toBe(1)
        ->ref_idesco->toEqual($this->middle->getKey())
        ->multi_seriado->toBeTrue()
        ->tipo_ensino_medio_cursado->toEqual(2)
        ->curso_formacao_continuada->toBe('{1,5}')
        ->complementacao_pedagogica->toBeNull()
        ->and((float) ($this->row)($person)->carga_horaria)->toBe(20.5)
        ->and(($this->roles)($person))->toBe(['1001' => $this->teacher->getKey(), '2002' => $this->coordinator->getKey()])
        ->and(($this->disciplines)($person))->toBe([$arts->getKey(), $botany->getKey()])
        ->and(($this->courses)($person))->toBe([$this->school->course->getKey()])
        ->and(DB::table('modules.educacenso_cod_docente')->where('cod_servidor', $person)->value('cod_docente_inep'))->toEqual(123456789)
        ->and(DB::table('cadastro.fisica_deficiencia')->where('ref_idpes', $person)->pluck('ref_cod_deficiencia')->all())->toBe([$deficiency->getKey()]);

    $role = LegacyEmployeeRole::query()->where('ref_cod_servidor', $person)->where('matricula', '1001')->value('cod_servidor_funcao');

    expect(DB::table('pmieducar.servidor_disciplina')->where('ref_cod_servidor', $person)->distinct()->pluck('ref_cod_funcao')->all())->toBe([$role]);

    Event::assertDispatched(EmployeeCreated::class);
});

test('com ensino superior grava a complementação pedagógica, as graduações e as pós-graduações', function () {
    Event::fake([EmployeeCreated::class]);

    $person = StudentScenario::individual('Ana Alves')->getKey();
    $course = EducacensoDegreeFactory::new()->create();
    $college = EducacensoInstitutionFactory::new()->create();

    $this->post($this->new, ($this->payload)($person, [
        'ref_idesco' => (string) $this->higher->getKey(),
        'complementacao_pedagogica' => ['3'],
        'employee_course_id' => [(string) $course->getKey(), ''],
        'employee_completion_year' => ['2010', ''],
        'employee_college_id' => [(string) $college->getKey(), ''],
        'posgraduate_type_id' => ['2', ''],
        'posgraduate_area_id' => ['1', ''],
        'posgraduate_completion_year' => ['2015', ''],
    ]))->assertRedirect(($this->detail)($person));

    expect(($this->row)($person)->complementacao_pedagogica)->toBe('{3}')
        ->and(EmployeeGraduation::query()->where('employee_id', $person)->sole())
        ->course_id->toBe($course->getKey())
        ->college_id->toBe($college->getKey())
        ->completion_year->toBe(2010)
        ->and(EmployeePosgraduate::query()->where('employee_id', $person)->sole())
        ->type_id->toBe(2)
        ->area_id->toBe(1)
        ->completion_year->toBe(2015)
        ->entity_id->toBe($this->institution);
});

test('sem ensino superior ignora as graduações e as pós-graduações enviadas', function () {
    Event::fake([EmployeeCreated::class]);

    $person = StudentScenario::individual('Ana Alves')->getKey();

    $this->post($this->new, ($this->payload)($person, [
        'ref_idesco' => '',
        'complementacao_pedagogica' => ['3'],
        'employee_course_id' => [(string) EducacensoDegreeFactory::new()->create()->getKey()],
        'employee_completion_year' => ['2010'],
        'employee_college_id' => [(string) EducacensoInstitutionFactory::new()->create()->getKey()],
        'posgraduate_type_id' => ['2'],
    ]))->assertRedirect(($this->detail)($person));

    expect(($this->row)($person))
        ->ref_idesco->toBeNull()
        ->complementacao_pedagogica->toBeNull()
        ->and(EmployeeGraduation::query()->where('employee_id', $person)->exists())->toBeFalse()
        ->and(EmployeePosgraduate::query()->where('employee_id', $person)->exists())->toBeFalse();
});

test('defeito: aceita uma carga horária que não é hora, gravando zero', function () {
    Event::fake([EmployeeCreated::class]);

    $person = StudentScenario::individual('Ana Alves')->getKey();

    $this->post($this->new, ($this->payload)($person, ['carga_horaria' => 'abc']))->assertRedirect(($this->detail)($person));

    expect((float) ($this->row)($person)->carga_horaria)->toBe(0.0);
});

test('cadastrar de novo quem já é servidor da instituição atualiza o cadastro', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution, ['ativo' => 0]);

    $this->post($this->new, ($this->payload)($ana->employee->getKey(), ['carga_horaria' => '30:00']))
        ->assertRedirect(($this->detail)($ana->employee->getKey()));

    expect(($this->row)($ana->employee->getKey()))
        ->ativo->toBe(1)
        ->and((float) ($this->row)($ana->employee->getKey())->carga_horaria)->toBe(30.0)
        ->and(($this->roles)($ana->employee->getKey()))->toBe(['1001' => $this->teacher->getKey(), '2002' => $this->coordinator->getKey()]);
});

test('abre a edição com os dados, as funções e o total de horas alocadas no ano', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution, ['carga_horaria' => 20.5, 'multi_seriado' => true, 'ref_idesco' => $this->higher])->inep(123456789);
    $role = $ana->role($this->teacher, '1001');
    $ana->discipline($role, $this->school->discipline('Artes'), $this->school->course);
    $ana->allocation($this->school->school, ['carga_horaria' => '12:00:00']);
    $ana->allocation($this->school->school, ['carga_horaria' => '04:00:00', 'periodo' => 2]);
    EmployeePosgraduate::query()->create(['employee_id' => $ana->employee->getKey(), 'entity_id' => $this->institution, 'type_id' => 2, 'area_id' => 1, 'completion_year' => 2015]);

    $html = $this->get(($this->edit)($ana))
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertSee("id='btn_excluir'", false)
        ->assertSee('Total de Horas Alocadadas')
        ->getContent();

    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_servidor'])->toBe((string) $ana->employee->getKey())
        ->and($fields['ref_cod_instituicao_original'])->toBe((string) $this->institution)
        ->and($fields['carga_horaria'])->toBe('20:30')
        ->and($fields['total_horas_alocadas'])->toBe('16')
        ->and($fields['ref_idesco'])->toBe((string) $this->higher->getKey())
        ->and($fields)->toHaveKey('multi_seriado')
        ->and($html)->toContain('value="1001"', "value='{$role->getKey()}'", 'value="2015"');

    expect(session("servant:{$ana->employee->getKey()}"))->toHaveKey($role->getKey());
});

test('abre a edição com as graduações e a complementação pedagógica do servidor', function () {
    $course = EducacensoDegreeFactory::new()->create();
    $college = EducacensoInstitutionFactory::new()->create();

    $ana = EmployeeScenario::named('Ana Alves', $this->institution, ['ref_idesco' => $this->higher, 'complementacao_pedagogica' => '{3}']);
    EmployeeGraduation::query()->create(['employee_id' => $ana->employee->getKey(), 'course_id' => $course->getKey(), 'college_id' => $college->getKey(), 'completion_year' => 2010]);

    $html = $this->get(($this->edit)($ana))->assertOk()->getContent();

    expect($html)->toContain('value="2010"', "value='{$course->getKey()}'", "value='{$college->getKey()}'");
});

test('depois de uma validação recusada, reabre as graduações que tinham sido digitadas', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);

    $html = $this->withSession(['_old_input' => [
        'course_id' => ['1'],
        'employee_course' => ['Curso digitado'],
        'employee_course_id' => ['77'],
        'employee_completion_year' => ['1999'],
        'employee_college' => ['Faculdade digitada'],
        'employee_college_id' => ['88'],
    ]])->get(($this->edit)($ana))->assertOk()->getContent();

    expect($html)->toContain('Curso digitado', 'Faculdade digitada', 'value="1999"');
});

test('reaberta por um envio sem ação, a edição mantém a instituição original enviada', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);

    $fields = LegacyFields::from($this->post(($this->edit)($ana), ['ref_cod_instituicao_original' => '77'])->assertOk()->getContent());

    expect($fields['ref_cod_instituicao_original'])->toBe('77');
});

test('ignora na sessão os componentes que vieram sem curso', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $kept = $ana->role($this->teacher, '1001');
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');

    $this->withSession(["servant:{$id}" => [
        $kept->getKey() => ['' => [$botany->getKey()], $this->school->course->getKey() => [$arts->getKey()]],
    ]])->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [$this->teacher->getKey() . '-1'],
        'matricula' => ['1001'],
        'cod_servidor_funcao' => [(string) $kept->getKey()],
    ]))->assertRedirect(($this->detail)($id));

    expect(($this->disciplines)($id))->toBe([$arts->getKey()]);
});

test('defeito: tirar uma função do professor apaga os componentes de todas as outras', function () {
    // Ao apagar os componentes da função removida, o filtro por função não
    // chega ao DELETE (o `when()` da relação descarta o `wherePivotIn`), e
    // todos os componentes do servidor na instituição vão embora.
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $kept = $ana->role($this->teacher, '1001');
    $gone = $ana->role($this->teacher, '1002');
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');
    $ana->discipline($kept, $arts, $this->school->course)->discipline($gone, $botany, $this->school->course);

    $this->withSession(["servant:{$id}" => [
        $kept->getKey() => [$this->school->course->getKey() => [$arts->getKey()]],
        $gone->getKey() => [$this->school->course->getKey() => [$botany->getKey()]],
    ]])->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [$this->teacher->getKey() . '-1'],
        'matricula' => ['1001'],
        'cod_servidor_funcao' => [(string) $kept->getKey()],
    ]))->assertRedirect(($this->detail)($id));

    expect(($this->disciplines)($id))->toBe([])
        ->and(LegacyEmployeeRole::query()->whereKey($kept->getKey())->exists())->toBeTrue()
        ->and(LegacyEmployeeRole::query()->whereKey($gone->getKey())->exists())->toBeFalse();
});

test('edita o servidor: atualiza uma função, acrescenta outra e remove a que saiu do envio', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $kept = $ana->role($this->teacher, '1001');
    $removed = $ana->role($this->coordinator, '2002', teacher: false);
    $arts = $this->school->discipline('Artes');
    $botany = $this->school->discipline('Botânica');
    $ana->discipline($kept, $arts, $this->school->course);

    $other = LegacyRoleFactory::new()->create(['nm_funcao' => 'Diretor', 'professor' => 0, 'ref_cod_instituicao' => $this->institution]);

    $this->withSession(["servant:{$id}" => [$kept->getKey() => [$this->school->course->getKey() => [$botany->getKey()]]]])
        ->post(($this->edit)($ana), ($this->payload)($id, [
            'tipoacao' => 'Editar',
            'total_horas_alocadas' => '0',
            'ref_cod_instituicao_original' => (string) $this->institution,
            'carga_horaria' => '32:00',
            'ref_cod_funcao' => [$this->teacher->getKey() . '-1', $other->getKey() . '-0'],
            'matricula' => ['1001-B', '3003'],
            'cod_servidor_funcao' => [(string) $kept->getKey(), ''],
        ]))
        ->assertRedirect(($this->detail)($id))
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.<br>');

    expect((float) ($this->row)($id)->carga_horaria)->toBe(32.0)
        ->and(($this->roles)($id))->toBe(['1001-B' => $this->teacher->getKey(), '3003' => $other->getKey()])
        ->and($kept->refresh()->matricula)->toBe('1001-B')
        ->and(LegacyEmployeeRole::query()->whereKey($removed->getKey())->exists())->toBeFalse()
        ->and(($this->disciplines)($id))->toBe([$botany->getKey()]);
});

test('sem função de professor, a edição apaga os componentes e os cursos do servidor', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $role = $ana->role($this->teacher, '1001');
    $ana->discipline($role, $this->school->discipline('Artes'), $this->school->course);

    $this->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [$this->coordinator->getKey() . '-0'],
        'matricula' => ['2002'],
        'cod_servidor_funcao' => [''],
    ]))->assertRedirect(($this->detail)($id));

    expect(($this->disciplines)($id))->toBe([])
        ->and(($this->courses)($id))->toBe([])
        ->and(($this->roles)($id))->toBe(['2002' => $this->coordinator->getKey()]);
});

test('recusa a edição que remove uma função usada em falta ou atraso', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $role = $ana->role($this->teacher, '1001');
    LegacyAbsenceDelayFactory::new()->create(['ref_cod_servidor' => $id, 'ref_ref_cod_instituicao' => $this->institution, 'ref_cod_servidor_funcao' => $role->getKey(), 'ref_cod_escola' => $this->school->school]);

    $this->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [$this->coordinator->getKey() . '-0'],
        'matricula' => ['2002'],
        'cod_servidor_funcao' => [''],
    ]))
        ->assertOk()
        ->assertSee('Edição não realizada. O servidor possui funções vinculadas a falta/atraso!');

    expect(LegacyEmployeeRole::query()->whereKey($role->getKey())->exists())->toBeTrue();
});

test('ao trocar a instituição desativa o servidor na antiga e cadastra na nova', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $target = LegacyInstitutionFactory::new()->create()->getKey();

    $this->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao' => (string) $target,
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [],
        'matricula' => [],
        'cod_servidor_funcao' => [],
    ]))->assertRedirect(($this->detail)($id, $target));

    expect(($this->row)($id)->ativo)->toBe(0)
        ->and(($this->row)($id, $target)->ativo)->toBe(1);
});

test('ao trocar para uma instituição em que já foi servidor, reativa o cadastro de lá', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $target = LegacyInstitutionFactory::new()->create()->getKey();
    DB::table('pmieducar.servidor')->insert(['cod_servidor' => $id, 'ref_cod_instituicao' => $target, 'carga_horaria' => 10, 'data_cadastro' => now(), 'ativo' => 0]);

    $this->post(($this->edit)($ana), ($this->payload)($id, [
        'tipoacao' => 'Editar',
        'total_horas_alocadas' => '0',
        'ref_cod_instituicao' => (string) $target,
        'ref_cod_instituicao_original' => (string) $this->institution,
        'ref_cod_funcao' => [],
        'matricula' => [],
        'cod_servidor_funcao' => [],
    ]))->assertRedirect(($this->detail)($id, $target));

    expect(($this->row)($id, $target)->ativo)->toBe(1)
        ->and(DB::table('pmieducar.servidor')->where('cod_servidor', $id)->count())->toBe(2);
});

test('exclui o servidor: desativa o cadastro e apaga funções, componentes e faltas', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();
    $role = $ana->role($this->teacher, '1001');
    $ana->discipline($role, $this->school->discipline('Artes'), $this->school->course);
    LegacyAbsenceDelayFactory::new()->create(['ref_cod_servidor' => $id, 'ref_ref_cod_instituicao' => $this->institution, 'ref_cod_servidor_funcao' => null, 'ref_cod_escola' => $this->school->school]);

    $this->post(($this->edit)($ana), ['tipoacao' => 'Excluir', 'cod_servidor' => (string) $id, 'ref_cod_instituicao' => (string) $this->institution, 'ref_cod_instituicao_original' => (string) $this->institution])
        ->assertRedirect('educar_servidor_lst.php')
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect(($this->row)($id))
        ->ativo->toBe(0)
        ->data_exclusao->not->toBeNull()
        ->and(($this->roles)($id))->toBe([])
        ->and(($this->disciplines)($id))->toBe([])
        ->and(LegacyAbsenceDelay::query()->withTrashed()->where('ref_cod_servidor', $id)->exists())->toBeFalse();
});

test('recusa excluir o servidor com falta ou atraso numa função ou sem a instituição', function (Closure $setup, array $overrides, string $message) {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();

    $setup->call($this, $ana);

    $this->post(($this->edit)($ana), $overrides + ['tipoacao' => 'Excluir', 'cod_servidor' => (string) $id, 'ref_cod_instituicao' => (string) $this->institution, 'ref_cod_instituicao_original' => (string) $this->institution])
        ->assertOk()
        ->assertSee($message);

    expect(($this->row)($id)->ativo)->toBe(1);
})->with([
    'falta numa função' => [fn ($ana) => LegacyAbsenceDelayFactory::new()->create([
        'ref_cod_servidor' => $ana->employee->getKey(),
        'ref_ref_cod_instituicao' => $this->institution,
        'ref_cod_servidor_funcao' => $ana->role($this->teacher, '1001')->getKey(),
        'ref_cod_escola' => $this->school->school,
    ]), [], 'Exclusão não realizada. O servidor possui funções vinculadas a falta/atrasos!'],
    'sem a instituição original' => [fn () => null, ['ref_cod_instituicao_original' => ''], 'Exclusão não realizada.'],
]);

test('quem só pode visualizar não abre, não grava e não exclui', function (string $action) {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $id = $ana->employee->getKey();

    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect('educar_servidor_lst.php');

    if ($action !== 'Editar') {
        $this->post(($this->edit)($ana), ($this->payload)($id, ['tipoacao' => $action, 'ref_cod_instituicao_original' => (string) $this->institution, 'carga_horaria' => '32:00']))
            ->assertRedirect('educar_servidor_lst.php');
    }

    expect((float) ($this->row)($id)->carga_horaria)->toBe(40.0)
        ->and(($this->row)($id)->ativo)->toBe(1);
})->with(['Novo', 'Excluir']);

test('quem não pode remover não vê o botão de excluir', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);

    ($this->viewer)(true, false);

    $this->get(($this->edit)($ana))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('volta para a listagem quando o servidor da URL não existe', function () {
    $this->get("{$this->new}?cod_servidor=999999999&ref_cod_instituicao={$this->institution}")->assertRedirect(url('intranet/educar_servidor_lst.php'));
});

test('defeito: uma linha de função deixada em branco dá erro 500 depois de gravar o servidor e as funções anteriores', function () {
    // A função vazia não é gravada e a tela tenta procurá-la passando texto a um método que exige inteiro.
    $person = StudentScenario::individual('Ana Alves')->getKey();

    $this->post($this->new, ($this->payload)($person, [
        'ref_cod_funcao' => [$this->teacher->getKey() . '-1', ''],
        'matricula' => ['1001', ''],
    ]))->assertStatus(500);

    expect(($this->row)($person))->not->toBeNull()
        ->and(($this->roles)($person))->toBe([1001 => $this->teacher->getKey()]);
});

test('cadastra como servidor uma pessoa que não é física, sem tocar em deficiências', function () {
    $company = LegacyPersonFactory::new()->create(['nome' => 'Cooperativa Aurora', 'tipo' => 'J'])->getKey();

    $this->post($this->new, ($this->payload)($company, ['deficiencias' => [(string) LegacyDeficiencyFactory::new()->create()->getKey()]]))
        ->assertRedirect(($this->detail)($company));

    expect(($this->row)($company))->not->toBeNull()
        ->and(DB::table('cadastro.fisica_deficiencia')->where('ref_idpes', $company)->count())->toBe(0);
});

test('abre a edição de um professor que ainda não tem código do Inep', function () {
    $ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $ana->role($this->teacher, '1001');

    $fields = LegacyFields::from($this->get(($this->edit)($ana))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_docente_inep'] ?? '')->toBe('');
});
