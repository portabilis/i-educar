<?php

use App\Models\LegacySchool;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;
use Tests\Request\Support\SchoolFormData;

/**
 * O formulário de escola quando a gravação não se completa: as recusas no
 * cadastro de uma escola nova e as falhas da própria gravação.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution + ['sigla' => 'EALFA']);
    $this->manager = SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true])->employee_id;
    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();

    $this->organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Escola Nova']);

    // O formulário de uma escola nova, já com a pessoa jurídica escolhida e o
    // mínimo para a gravação passar.
    $this->novo = function (array $overrides = [], array $without = []) {
        $form = $this->post('/intranet/educar_escola_cad.php', ['tipoacao' => '', 'pessoaj_id' => $this->organization->idpes])->assertOk();

        $payload = LegacyFields::from($form->getContent(), $overrides + SchoolFormData::managers([$this->manager]) + $this->institution + [
            'sigla' => 'ENOVA',
            'zona_localizacao' => 1,
            'nao_ha_funcionarios_para_funcoes' => 'on',
        ]);

        return array_diff_key($payload, array_flip($without));
    };

    $this->created = fn () => LegacySchool::query()->where('ref_idpes', $this->organization->idpes)->exists();

    // As recusas que acontecem depois de a tela abrir a transação a deixam
    // aberta (defeito registrado em EducarEscolaCadCursosTest); é preciso
    // desfazê-la para não trancar o teste seguinte.
    $this->undo = function () {
        expect(DB::transactionLevel())->toBe(2);

        DB::rollBack();
    };
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

test('no cadastro de uma escola nova valem as mesmas recusas da edição', function (array $overrides, string $message, array $without = []) {
    $this->post('/intranet/educar_escola_cad.php', ($this->novo)($overrides, $without))
        ->assertOk()
        ->assertSee($message, false);

    expect(($this->created)())->toBeFalse();
})->with([
    'complemento' => [['complement' => 'Sala @ 2'], 'O campo foi preenchido com valor não permitido.'],
    'CNPJ da mantenedora' => [['dependencia_administrativa' => 4, 'cnpj_mantenedora_principal' => '11.111.111/1111-11'], 'O CNPJ da mantenedora principal é inválido. Favor verificar.'],
    'INEP da escola' => [['escola_inep_id' => '01234567'], 'O campo: Código INEP não pode iniciar com 0.'],
    'telefone' => [['p_ddd_telefone_1' => '48', 'p_telefone_1' => ''], 'O campo: Telefone 1, deve ser preenchido quando o DDD estiver preenchido.'],
    'dependências' => [['possui_dependencias' => 1], 'Preencha pelo menos um dos campos de Salas gerais à Áreas externas'],
    'quantidade de profissionais' => [[], 'Preencha pelo menos um dos campos <b>da seção Quantidade de profissionais</b> da aba Recursos.', ['nao_ha_funcionarios_para_funcoes']],
    'INEP de escola que divide o prédio' => [['codigo_inep_escola_compartilhada3' => '1234'], 'O campo: Código da escola que compartilha o prédio 3 deve conter 8 dígitos.'],
    'opção exclusiva' => [['abastecimento_agua' => [5, 1]], 'Não é possível informar mais de uma opção no campo: <b>Abastecimento de água</b>'],
    'pessoa jurídica não escolhida' => [[], 'Erro ao selecionar a pessoa jurídica', ['pessoaj_id_oculto', 'pessoaj_id']],
]);

test('no cadastro de uma escola nova valem as regras do Censo quando a instituição as exige', function () {
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->user->ref_cod_instituicao)->update(['obrigar_campos_censo' => true]);

    $this->post('/intranet/educar_escola_cad.php', ($this->novo)())
        ->assertOk()
        ->assertSee('Cidade não informada');

    expect(($this->created)())->toBeFalse();
});

test('estoura quando a pessoa jurídica informada não existe', function () {
    $this->post('/intranet/educar_escola_cad.php', ($this->novo)(['pessoaj_id_oculto' => 999999999]))
        ->assertServerError();

    expect(($this->created)())->toBeFalse();
});

test('avisa quando o cadastro da escola não é gravado', function () {
    // Sem a sigla a classe legada se recusa a inserir a escola.
    $this->post('/intranet/educar_escola_cad.php', ($this->novo)(['sigla' => '']))
        ->assertOk()
        ->assertSee('Cadastro não realizado');

    ($this->undo)();

    expect(($this->created)())->toBeFalse();
});

test('recusa a tabela de cursos vazia já no cadastro de uma escola nova', function () {
    $this->post('/intranet/educar_escola_cad.php', ($this->novo)(['cursos_ready' => 1, 'cursos' => []]))
        ->assertOk()
        ->assertSee("Preencha o campo 'Curso' corretamente", false);

    ($this->undo)();

    expect(($this->created)())->toBeFalse();
});

// Defeito conhecido: com um código de escola não numérico a gravação falha e a
// tela ia avisar "Edição não efetuada.", mas ao se redesenhar ela consulta os
// gestores com esse mesmo código e estoura por tipo.
test('defeito: a edição com código de escola inválido estoura ao redesenhar o formulário', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['cod_escola' => 'abc', 'sigla' => 'MUDOU']);

    $this->post('/intranet/educar_escola_cad.php', $payload)->assertServerError();

    ($this->undo)();

    expect($this->school->refresh()->sigla)->toBe('EALFA');
});

test('avisa quando a exclusão não é gravada', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['cod_escola' => 'abc', 'tipoacao' => 'Excluir']);

    $this->post('/intranet/educar_escola_cad.php', $payload)
        ->assertOk()
        ->assertSee('Exclusão não realizada.');

    expect($this->school->refresh()->ativo)->toBe(1);
});

test('aceita um campo de múltipla escolha que chega como valor único', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['abastecimento_agua' => '2']);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh()->abastecimento_agua)->toBe('{2}');
});

test('lista os cursos da instituição com a descrição entre parênteses quando há uma', function () {
    $plain = LegacyCourseFactory::new()->withName('Curso Sem Descrição')->create($this->institution);
    $plain->update(['descricao' => '']);
    $described = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa', 'descricao' => 'noturno']);

    $options = LegacyForm::from($this->get($this->url)->assertOk()->getContent())->options('ref_cod_curso[0]');

    expect($options[(string) $plain->getKey()])->toBe('Curso Sem Descrição')
        ->and($options[(string) $described->getKey()])->toBe('Curso Alfa (noturno)');
});
