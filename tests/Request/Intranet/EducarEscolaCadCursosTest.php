<?php

use App\Models\LegacySchoolCourse;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;
use Tests\Request\Support\SchoolFormData;

/**
 * Os cursos da escola são gravados dentro do formulário de escola. O JavaScript
 * da tela reescreve as linhas da tabela como `cursos[i][curso_id|autorizacao|
 * anos_letivos]` e acrescenta `cursos_ready`; é esse formato que o servidor lê,
 * e é ele que os testes enviam.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution + ['sigla' => 'EALFA']);
    SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true]);

    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();

    $this->course = fn (string $name) => LegacyCourseFactory::new()->withName($name)->create($this->institution);

    $this->link = fn ($course, string $autorizacao = 'Portaria 1', string $anos = '{2025,2026}') => LegacySchoolCourseFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_curso' => $course,
        'autorizacao' => $autorizacao,
        'anos_letivos' => $anos,
    ]);

    $this->save = function (array $cursos) {
        $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
            'cursos_ready' => 1,
            'cursos' => $cursos,
        ]);

        return $this->post($this->url, $payload);
    };

    // Curso => [autorização, anos letivos] dos vínculos da escola, como estão no banco.
    $this->linked = fn () => LegacySchoolCourse::query()
        ->where('ref_cod_escola', $this->school->getKey())
        ->get()
        ->mapWithKeys(fn ($link) => [$link->ref_cod_curso => [$link->autorizacao, $link->anos_letivos]])
        ->all();
});

// Rede de segurança: se uma asserção falhar antes de o teste desfazer a
// transação que a tela deixou aberta, ela é desfeita aqui. Sem isso o teste
// seguinte ficaria esperando o que ela trancou, e a suíte travaria em vez de
// acusar a falha.
afterEach(function () {
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('abre a edição com os cursos da escola na tabela', function () {
    $course = ($this->course)('Curso Alfa');
    ($this->link)($course);

    $response = $this->get($this->url)->assertOk();
    $fields = LegacyFields::from($response->getContent());

    expect($fields['ref_cod_curso'][0])->toBe((string) $course->getKey())
        ->and($fields['curso_autorizacao'][0])->toBe('Portaria 1')
        ->and($fields['curso_anos_letivos'][0])->toBe('2025,2026')
        ->and(LegacyForm::from($response->getContent())->options('ref_cod_curso[0]'))->toHaveKey((string) $course->getKey());
});

test('mostra como excluído um curso inativo que ainda está ligado à escola', function () {
    $course = ($this->course)('Curso Extinto');
    ($this->link)($course);
    $course->update(['ativo' => 0]);

    $response = $this->get($this->url)->assertOk();

    expect(LegacyForm::from($response->getContent())->options('ref_cod_curso[0]')[(string) $course->getKey()])->toBe('Curso Extinto (excluído)')
        ->and(LegacyFields::from($response->getContent())['cursos_inativos'])->toContain((string) $course->getKey());
});

test('liga um curso novo à escola, com os anos letivos em ordem', function () {
    $course = ($this->course)('Curso Alfa');

    ($this->save)([['curso_id' => $course->getKey(), 'autorizacao' => ' Portaria 9 ', 'anos_letivos' => '2026,2024,2025']])
        ->assertRedirect('educar_escola_lst.php');

    expect(($this->linked)())->toBe([$course->getKey() => ['Portaria 9', '{2024,2025,2026}']]);

    $link = LegacySchoolCourse::query()->where('ref_cod_escola', $this->school->getKey())->sole();

    expect($link->ativo)->toBe(1)
        ->and($link->ref_usuario_cad)->toBe($this->user->getKey());
});

test('atualiza o curso que mudou, mantém o que não mudou e remove o que saiu', function () {
    $changed = ($this->course)('Curso Alterado');
    $same = ($this->course)('Curso Igual');
    $removed = ($this->course)('Curso Removido');
    $added = ($this->course)('Curso Novo');

    ($this->link)($changed);
    ($this->link)($same);
    ($this->link)($removed);

    ($this->save)([
        ['curso_id' => $changed->getKey(), 'autorizacao' => 'Portaria 2', 'anos_letivos' => '2026'],
        ['curso_id' => $same->getKey(), 'autorizacao' => 'Portaria 1', 'anos_letivos' => '2026,2025'],
        ['curso_id' => $added->getKey(), 'autorizacao' => '', 'anos_letivos' => '2027'],
    ])->assertRedirect('educar_escola_lst.php');

    $linked = ($this->linked)();
    ksort($linked);

    $expected = [
        $changed->getKey() => ['Portaria 2', '{2026}'],
        $same->getKey() => ['Portaria 1', '{2025,2026}'],
        $added->getKey() => ['', '{2027}'],
    ];
    ksort($expected);

    expect($linked)->toBe($expected);
});

test('ignora linha repetida e linha sem curso', function () {
    $course = ($this->course)('Curso Alfa');

    ($this->save)([
        ['curso_id' => $course->getKey(), 'autorizacao' => 'Portaria 1', 'anos_letivos' => '2026'],
        ['curso_id' => $course->getKey(), 'autorizacao' => 'Portaria repetida', 'anos_letivos' => '2030'],
        ['curso_id' => '', 'autorizacao' => 'Sem curso', 'anos_letivos' => '2031'],
    ])->assertRedirect('educar_escola_lst.php');

    expect(($this->linked)())->toBe([$course->getKey() => ['Portaria 1', '{2026}']]);
});

test('recusa a tabela de cursos sem nenhum curso válido e devolve as linhas enviadas', function () {
    $course = ($this->course)('Curso Alfa');
    ($this->link)($course);

    $response = ($this->save)([['curso_id' => '', 'autorizacao' => 'Sem curso', 'anos_letivos' => '2031']])
        ->assertOk()
        ->assertSee("Preencha o campo 'Curso' corretamente", false);

    // A tabela volta com o que foi digitado, e não com o que está no banco.
    expect(LegacyFields::from($response->getContent())['curso_autorizacao'][0])->toBe('Sem curso');

    // Defeito conhecido: a tela abre uma transação antes de gravar a escola e,
    // nesta recusa, devolve o formulário sem desfazê-la. Em produção ela morre
    // com a requisição; aqui é preciso desfazê-la, ou o teste seguinte fica
    // esperando o que ela trancou.
    expect(DB::transactionLevel())->toBe(2);

    DB::rollBack();

    expect(($this->linked)())->toBe([$course->getKey() => ['Portaria 1', '{2025,2026}']])
        ->and($this->school->refresh()->sigla)->toBe('EALFA');
});

test('não mexe nos cursos quando o formulário chega sem o sinal do JavaScript', function () {
    $course = ($this->course)('Curso Alfa');
    ($this->link)($course);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['cursos' => []]);

    expect($payload)->not->toHaveKey('cursos_ready');

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect(($this->linked)())->toBe([$course->getKey() => ['Portaria 1', '{2025,2026}']]);
});

test('liga os cursos já no cadastro de uma escola nova', function () {
    $organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Escola Nova']);
    $course = ($this->course)('Curso Alfa');
    $manager = SchoolManagerFactory::new()->create(['school_id' => $this->school])->employee_id;

    $form = $this->post('/intranet/educar_escola_cad.php', ['tipoacao' => '', 'pessoaj_id' => $organization->idpes])->assertOk();

    $payload = LegacyFields::from($form->getContent(), SchoolFormData::managers([$manager]) + $this->institution + [
        'sigla' => 'ENOVA',
        'zona_localizacao' => 1,
        'nao_ha_funcionarios_para_funcoes' => 'on',
        'cursos_ready' => 1,
        'cursos' => [['curso_id' => $course->getKey(), 'autorizacao' => 'Portaria 5', 'anos_letivos' => '2026']],
    ]);

    $this->post('/intranet/educar_escola_cad.php', $payload)->assertRedirect('educar_escola_lst.php');

    $link = LegacySchoolCourse::query()->where('ref_cod_curso', $course->getKey())->sole();

    expect($link->school->ref_idpes)->toBe($organization->idpes)
        ->and($link->autorizacao)->toBe('Portaria 5')
        ->and($link->anos_letivos)->toBe('{2026}');
});
