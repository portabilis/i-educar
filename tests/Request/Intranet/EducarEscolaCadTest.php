<?php

use App\Models\LegacyOrganization;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Models\SchoolManager;
use App\Models\SchoolSpace;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPhoneFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;
use Database\Factories\SchoolManagerFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\SchoolFormData;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    // Um servidor que pode ser gestor: pessoa física com registro de servidor.
    $this->manager = function () {
        $individual = LegacyIndividualFactory::new()->create();
        EmployeeFactory::new()->create(['id' => $individual->idpes]);

        return $individual->idpes;
    };

    $this->row = fn (int $id) => DB::table('pmieducar.escola')->where('cod_escola', $id)->first();
});

test('uma escola nova começa pela busca da pessoa jurídica', function () {
    $fields = LegacyFields::from($this->get('/intranet/educar_escola_cad.php')->assertOk()->getContent());

    expect($fields)->toHaveKey('pessoaj_id')
        ->and($fields)->not->toHaveKey('sigla')
        ->and($fields)->not->toHaveKey('fantasia');
});

test('escolhida a pessoa jurídica, abre o formulário com o nome, o endereço e os telefones dela', function () {
    $organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Escola Nova']);
    $place = PlaceFactory::new()->create();
    PersonHasPlaceFactory::new()->create(['person_id' => $organization->idpes, 'place_id' => $place]);
    LegacyPhoneFactory::new()->create(['idpes' => $organization->idpes, 'tipo' => LegacyPhone::TYPE_LANDLINE, 'ddd' => '48', 'fone' => '34221001']);

    $fields = LegacyFields::from($this->post('/intranet/educar_escola_cad.php', [
        'tipoacao' => '',
        'pessoaj_id' => $organization->idpes,
    ])->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['pessoaj_id_oculto'])->toBe((string) $organization->idpes)
        ->and($fields['fantasia'])->toBe('Escola Nova')
        ->and($fields['address'])->toBe($place->address)
        ->and($fields['neighborhood'])->toBe($place->neighborhood)
        ->and($fields['p_ddd_telefone_1'])->toBe('48')
        ->and($fields['p_telefone_1'])->toBe('34221001');
});

test('avisa quando a pessoa jurídica escolhida já tem uma escola', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution);

    $response = $this->post('/intranet/educar_escola_cad.php', [
        'tipoacao' => '',
        'pessoaj_id' => $school->ref_idpes,
    ])->assertOk()->assertSee('Escola criada, para');

    $response->assertSee('educar_escola_cad.php?cod_escola=' . $school->getKey(), false);

    // Continua na busca: o formulário da escola não é desenhado.
    expect(LegacyFields::from($response->getContent()))->not->toHaveKey('sigla');
});

test('cadastra uma escola com todos os dados', function () {
    $organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Nome antigo']);
    $place = PlaceFactory::new()->create();
    $manager = ($this->manager)();

    $form = $this->post('/intranet/educar_escola_cad.php', ['tipoacao' => '', 'pessoaj_id' => $organization->idpes])->assertOk();

    $payload = LegacyFields::from($form->getContent(), SchoolFormData::FIELDS + SchoolFormData::MULTIPLE + SchoolFormData::managers([$manager]) + $this->institution + [
        'fantasia' => 'Escola Nova',
        'sigla' => 'ENOVA',
        'zona_localizacao' => 1,
        'situacao_funcionamento' => 1,
        'dependencia_administrativa' => 3,
        'regulamentacao' => 1,
        'latitude' => '-28.680000',
        'longitude' => '-49.370000',
        'escola_inep_id' => '42000123',
        'city_id' => $place->city_id,
        'address' => 'Rua da Escola',
        'number' => '100',
        'complement' => 'Bloco A',
        'neighborhood' => 'Centro',
        'postal_code' => '88900-000',
        'p_ddd_telefone_1' => '48',
        'p_telefone_1' => '34221001',
        'p_ddd_telefone_2' => '48',
        'p_telefone_2' => '34221002',
        'p_ddd_telefone_mov' => '48',
        'p_telefone_mov' => '991221003',
        'p_ddd_telefone_fax' => '48',
        'p_telefone_fax' => '34221004',
        'espaco_escolar_nome' => ['Quadra', 'Biblioteca'],
        'espaco_escolar_tamanho' => ['300', '80'],
        'espaco_escolar_id' => ['', ''],
        'utiliza_regra_diferenciada' => 'on',
        'bloquear_lancamento_diario_anos_letivos_encerrados' => 'on',
        'qtd_secretario_escolar' => 2,
        'qtd_vice_diretor' => 1,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_escola_cad.php', $payload)->assertRedirect('educar_escola_lst.php');

    $school = LegacySchool::query()->where('ref_idpes', $organization->idpes)->sole();
    $row = ($this->row)($school->getKey());

    expect($row)
        ->sigla->toBe('ENOVA')
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->user->ref_cod_instituicao)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->zona_localizacao->toBe(1)
        ->situacao_funcionamento->toBe(1)
        ->dependencia_administrativa->toBe(3)
        ->regulamentacao->toBe(1)
        ->latitude->toBe('-28.680000')
        ->longitude->toBe('-49.370000')
        ->qtd_secretario_escolar->toBe(2)
        ->qtd_vice_diretor->toBe(1)
        ->nao_ha_funcionarios_para_funcoes->toBeFalse();

    foreach (SchoolFormData::FIELDS as $field => $value) {
        expect($row->{$field})->toEqual($value, "o campo [{$field}] não foi gravado");
    }

    foreach (array_diff_key(SchoolFormData::MULTIPLE, ['orgao_vinculado_escola' => true]) as $field => $values) {
        expect($row->{$field})->toBe('{' . implode(',', $values) . '}', "o campo [{$field}] não foi gravado");
    }

    // Defeito conhecido: só no cadastro, e só neste campo, a classe envolve o
    // valor em chaves duas vezes, e o vetor nasce com duas dimensões. A edição
    // grava "{2}".
    expect($row->orgao_vinculado_escola)->toBe('{{2}}');

    // O que o formulário grava fora da tabela da escola.
    expect(LegacyOrganization::query()->find($organization->idpes)->fantasia)->toBe('Escola Nova')
        ->and(SchoolInep::query()->where('cod_escola', $school->getKey())->value('cod_escola_inep'))->toEqual(42000123)
        ->and(LegacyPhone::query()->where('idpes', $organization->idpes)->orderBy('tipo')->pluck('fone', 'tipo')->all())->toEqual([
            LegacyPhone::TYPE_LANDLINE => 34221001,
            LegacyPhone::TYPE_MOBILE => 34221002,
            LegacyPhone::TYPE_MOBILE_ALT => 991221003,
            LegacyPhone::TYPE_FAX => 34221004,
        ])
        ->and(SchoolSpace::query()->where('school_id', $school->getKey())->orderBy('name')->pluck('size', 'name')->all())->toEqual(['Biblioteca' => 80, 'Quadra' => 300]);

    $address = $school->addresses()->sole();

    expect($address)
        ->address->toBe('Rua da Escola')
        ->number->toBe('100')
        ->complement->toBe('Bloco A')
        ->neighborhood->toBe('Centro')
        ->postal_code->toBe('88900000')
        ->city_id->toBe($place->city_id);

    $saved = SchoolManager::query()->where('school_id', $school->getKey())->sole();

    expect($saved->employee_id)->toBe($manager)
        ->and((bool) $saved->chief)->toBeTrue();
});

test('reabre e regrava uma escola completa sem perder nenhum campo', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->complete()->withAddress()->withPhones()->withInep()->withSpaces()->create($this->institution);
    SchoolManagerFactory::new()->create(['school_id' => $school, 'chief' => true]);

    $before = ($this->row)($school->getKey());
    $url = '/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey();

    // As listas de múltipla escolha são montadas por JavaScript a partir dos
    // valores da escola; aqui elas vão como o navegador as enviaria.
    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), SchoolFormData::MULTIPLE + ['organizacao_ensino' => [1]]);

    expect($payload['tipoacao'])->toBe('Editar')
        ->and($payload['qtd_secretario_escolar'])->toBe('1')
        ->and($payload)->not->toHaveKey('nao_ha_funcionarios_para_funcoes');

    $this->post($url, $payload)->assertRedirect('educar_escola_lst.php');

    $after = ($this->row)($school->getKey());

    foreach (array_keys(SchoolFormData::FIELDS + SchoolFormData::MULTIPLE) as $field) {
        expect($after->{$field})->toEqual($before->{$field}, "o campo [{$field}] mudou ao regravar");
    }

    expect($after->qtd_revisor_braile)->toBe(1)
        ->and($after->ref_usuario_exc)->toBe($this->user->getKey())
        ->and(SchoolSpace::query()->where('school_id', $school->getKey())->count())->toBe(2)
        ->and(LegacyPhone::query()->where('idpes', $school->ref_idpes)->count())->toBe(4)
        ->and($school->addresses()->count())->toBe(1);
});

test('edita o nome fantasia e a sigla', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution);
    SchoolManagerFactory::new()->create(['school_id' => $school, 'chief' => true]);

    $url = '/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['fantasia' => 'Escola Renomeada', 'sigla' => 'EREN']);

    $this->post($url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($school->refresh()->sigla)->toBe('EREN')
        ->and(LegacyOrganization::query()->find($school->ref_idpes)->fantasia)->toBe('Escola Renomeada');
});

// Defeito conhecido: a exclusão é uma edição com ativo = 0, e a edição grava
// NULL em toda coluna que não recebeu valor. A escola "excluída" perde os dados.
test('defeito: excluir a escola desativa e apaga os demais dados dela', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->complete()->create($this->institution);
    SchoolManagerFactory::new()->create(['school_id' => $school, 'chief' => true]);

    $url = '/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect('educar_escola_lst.php');

    expect(($this->row)($school->getKey()))
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->latitude->toBeNull()
        ->regulamentacao->toBeNull()
        ->caracteristica_escolar->toBeNull()
        ->qtd_secretario_escolar->toBeNull()
        // As colunas de múltipla escolha de infraestrutura escapam.
        ->abastecimento_agua->toBe('{1,2}');
});

test('volta para a listagem quando a escola não existe', function () {
    $this->get('/intranet/educar_escola_cad.php?cod_escola=999999999')->assertRedirect('educar_escola_lst.php');
});

test('volta para a listagem quando o usuário só pode visualizar escola', function () {
    $school = LegacySchoolFactory::new()->create($this->institution);

    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(561, modify: false, remove: false)->create());

    $this->get('/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey())->assertRedirect('educar_escola_lst.php');
});

test('o usuário institucional recebe a própria instituição num campo oculto', function () {
    $school = LegacySchoolFactory::new()->create($this->institution);

    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(561)->create());

    $fields = LegacyFields::from($this->get('/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey())->assertOk()->getContent());

    expect($fields['ref_cod_instituicao'])->toBe((string) $this->user->ref_cod_instituicao);
});
