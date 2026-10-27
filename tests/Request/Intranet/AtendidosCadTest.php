<?php

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\LegacyEmployee;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\PersonHasPlace;
use Database\Factories\CityFactory;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyMaritalStatusFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // A classe guarda o usuário logado num estático, que sobrevive de um teste
    // para o outro no mesmo processo.
    Portabilis_Utils_User::$_currentUserId = null;

    $this->new = '/intranet/atendidos_cad.php';
    $this->edit = fn ($individual) => $this->new . '?cod_pessoa_fj=' . $individual->getKey();

    $this->city = CityFactory::new()->create(['name' => 'Cidade Aurora']);
    $this->maritalStatus = LegacyMaritalStatusFactory::new()->create(['descricao' => 'Solteira']);
    $this->race = LegacyRaceFactory::new()->create(['nm_raca' => 'Raça Alfa']);
    $this->religion = ReligionFactory::new()->create(['name' => 'Religião Alfa']);
    $this->degree = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Ensino Médio']);
    $this->issuer = DB::table('cadastro.orgao_emissor_rg')->insertGetId(['sigla' => 'SSP', 'descricao' => 'Secretaria de Segurança', 'situacao' => 'A'], 'idorg_rg');

    // Vários campos (nacionalidade, naturalidade e todos os documentos) são
    // lidos de $_REQUEST, que o PHP preenche só numa requisição real; o envio
    // preenche o superglobal à mão.
    $this->send = function (string $url, array $payload) {
        $_REQUEST = $payload;

        try {
            return $this->post($url, $payload);
        } finally {
            $_REQUEST = [];
        }
    };

    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'possui_cpf' => '1',
        'id_federal' => '123.456.789-09',
        'nm_pessoa' => 'Ana Alves',
        'nome_social' => 'Nina Alves',
        'sexo' => 'F',
        'estado_civil_id' => (string) $this->maritalStatus->getKey(),
        'data_nasc' => '20/04/2015',
        'rg' => '7654321',
        'data_emissao_rg' => '10/03/2020',
        'orgao_emissao_rg' => (string) $this->issuer,
        'uf_emissao_rg' => 'SC',
        'nis_pis_pasep' => '12345678901',
        'sus' => '898001234567890',
        'tipo_certidao_civil' => 'certidao_nascimento_novo_formato',
        'certidao_nascimento' => '12345601552015100012345678901234',
        'uf_emissao_certidao_civil' => 'SC',
        'data_emissao_certidao_civil' => '02/05/2015',
        'cartorio_emissao_certidao_civil' => 'Cartório Central',
        'passaporte' => 'AB123456',
        'carteira_trabalho' => '55555',
        'serie_carteira_trabalho' => '111',
        'uf_emissao_carteira_trabalho' => 'SC',
        'data_emissao_carteira_trabalho' => '05/01/2021',
        'titulo_eleitor' => '123456789012',
        'zona_titulo_eleitor' => '12',
        'secao_titulo_eleitor' => '345',
        'cor_raca' => (string) $this->race->getKey(),
        'tipo_nacionalidade' => '1',
        'naturalidade_id' => (string) $this->city->getKey(),
        'religiao_id' => (string) $this->religion->getKey(),
        'city_id' => (string) $this->city->getKey(),
        'address' => 'Rua das Acácias',
        'number' => '120',
        'complement' => 'Casa 2',
        'neighborhood' => 'Bairro Jardim',
        'postal_code' => '88800-123',
        'pais_residencia' => '76',
        'zona_localizacao_censo' => '1',
        'localizacao_diferenciada' => '7',
        'ddd_telefone_1' => '48',
        'telefone_1' => '34320001',
        'ddd_telefone_2' => '48',
        'telefone_2' => '999990002',
        'ddd_telefone_mov' => '48',
        'telefone_mov' => '34320003',
        'ddd_telefone_fax' => '48',
        'telefone_fax' => '34320004',
        'email' => 'ana@exemplo.test',
        'ocupacao' => 'Professora',
        'idesco' => (string) $this->degree->getKey(),
        'renda_mensal' => '2.500,50',
        'data_admissao' => '01/02/2020',
        'empresa' => 'Escola Aurora',
        'ddd_telefone_empresa' => '48',
        'telefone_empresa' => '34320009',
        'pessoa_contato' => 'Diretora Dalva',
        'observacao' => 'Sem observações',
    ];

    $this->stored = fn () => LegacyPerson::query()->where('nome', 'Ana Alves')->where('tipo', 'F')->sole();
});

test('abre o cadastro vazio com CPF marcado como existente e as listas de apoio', function () {
    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['possui_cpf'])->toBe('1')
        ->and($fields['nm_pessoa'])->toBe('')
        ->and($form->options('cor_raca'))->toContain('Raça Alfa')
        ->and($form->options('idesco'))->toContain('Ensino Médio')
        ->and($form->options('orgao_emissao_rg'))->toContain('SSP')
        ->and(array_values($form->options('tipo_certidao_civil')))->toBe(['Tipo certidão civil', 'Nascimento (novo formato)', 'Nascimento (antigo formato)', 'Casamento (novo formato)', 'Casamento (antigo formato)'])
        ->and($html)->not->toContain("id='btn_excluir'");
});

test('cadastra a pessoa com todos os dados, documentos, endereço, telefones e raça', function () {
    ($this->send)($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('window.opener.afterChangePessoa', false);

    $person = ($this->stored)();
    $individual = $person->individual;

    expect($person)
        ->email->toBe('ana@exemplo.test')
        ->slug->toBe('nina alves ana alves')
        ->and((int) $person->idpes_cad)->toBe($this->user->getKey())
        ->and($individual->getRawOriginal('cpf'))->toEqual(12345678909)
        ->and($individual)
        ->nome_social->toBe('Nina Alves')
        ->sexo->toBe('F')
        ->ideciv->toEqual($this->maritalStatus->getKey())
        ->nacionalidade->toEqual(1)
        ->idmun_nascimento->toEqual($this->city->getKey())
        ->ref_cod_religiao->toEqual($this->religion->getKey())
        ->sus->toBe('898001234567890')
        ->nis_pis_pasep->toEqual(12345678901)
        ->ocupacao->toBe('Professora')
        ->idesco->toEqual($this->degree->getKey())
        ->empresa->toBe('Escola Aurora')
        ->ddd_telefone_empresa->toEqual(48)
        ->telefone_empresa->toEqual(34320009)
        ->pessoa_contato->toBe('Diretora Dalva')
        ->observacao->toBe('Sem observações')
        ->pais_residencia->toEqual(76)
        ->zona_localizacao_censo->toEqual(1)
        ->localizacao_diferenciada->toEqual(7)
        ->and((float) $individual->renda_mensal)->toBe(2500.5)
        ->and($individual->data_nasc->format('Y-m-d'))->toBe('2015-04-20')
        ->and(substr((string) $individual->data_admissao, 0, 10))->toBe('2020-02-01')
        ->and((bool) $individual->falecido)->toBeFalse()
        ->and($individual->race()->pluck('nm_raca')->all())->toBe(['Raça Alfa'])
        ->and($person->phones->sortBy('tipo')->pluck('fone', 'tipo')->all())->toBe([
            LegacyPhone::TYPE_LANDLINE => '34320001',
            LegacyPhone::TYPE_MOBILE => '999990002',
            LegacyPhone::TYPE_MOBILE_ALT => '34320003',
            LegacyPhone::TYPE_FAX => '34320004',
        ])
        ->and(PersonHasPlace::query()->where('person_id', $person->getKey())->first()->place)
        ->address->toBe('Rua das Acácias')
        ->number->toBe('120')
        ->complement->toBe('Casa 2')
        ->neighborhood->toBe('Bairro Jardim')
        ->postal_code->toBe('88800123');

    expect(DB::table('cadastro.documento')->where('idpes', $person->getKey())->first())
        ->rg->toBe('7654321')
        ->idorg_exp_rg->toEqual($this->issuer)
        ->sigla_uf_exp_rg->toBe('SC')
        ->tipo_cert_civil->toBeNull()
        ->certidao_nascimento->toBe('12345601552015100012345678901234')
        ->certidao_casamento->toBe('')
        ->sigla_uf_cert_civil->toBe('SC')
        ->cartorio_cert_civil->toBe('Cartório Central')
        ->passaporte->toBe('AB123456')
        ->num_cart_trabalho->toEqual(55555)
        ->serie_cart_trabalho->toEqual(111)
        ->sigla_uf_cart_trabalho->toBe('SC')
        ->num_tit_eleitor->toEqual(123456789012)
        ->zona_tit_eleitor->toEqual(12)
        ->secao_tit_eleitor->toEqual(345)
        ->and(substr(DB::table('cadastro.documento')->where('idpes', $person->getKey())->value('data_exp_rg'), 0, 10))->toBe('2020-03-10');
});

test('cadastra uma pessoa sem CPF com o motivo da ausência, estrangeira e com a certidão no formato antigo', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['name' => 'Documento em emissão', 'requires_observation' => true]);
    $mother = StudentScenario::individual('Marta Alves');
    $father = StudentScenario::individual('Pedro Alves');
    $people = DB::table('modules.povo_indigena_educacenso')->orderBy('id')->value('id');

    ($this->send)($this->new, ($this->payload)([
        'pai_id' => (string) $father->getKey(),
        'mae_id' => (string) $mother->getKey(),
        'povo_indigena_educacenso_id' => (string) $people,
        'possui_cpf' => '0',
        'id_federal' => '',
        'document_absence_reason_id' => (string) $reason->getKey(),
        'document_absence_observation' => 'Protocolo 123',
        'tipo_nacionalidade' => '3',
        'pais_origem_id' => '999',
        'tipo_certidao_civil' => '91',
        'certidao_nascimento' => '',
        'termo_certidao_civil' => '1001',
        'livro_certidao_civil' => 'A-12',
        'folha_certidao_civil' => '77',
        'falecido' => 'on',
        'nome_social' => '',
        'religiao_id' => '',
        'nis_pis_pasep' => '',
    ]))->assertOk();

    $person = ($this->stored)();
    $individual = $person->individual;

    expect($individual->getRawOriginal('cpf'))->toBeNull()
        ->and($individual)
        ->document_absence_reason_id->toEqual($reason->getKey())
        ->document_absence_observation->toBe('Protocolo 123')
        ->nacionalidade->toEqual(3)
        ->idpais_estrangeiro->toEqual(999)
        ->nome_social->toBeNull()
        ->ref_cod_religiao->toBeNull()
        ->nis_pis_pasep->toBeNull()
        ->idpes_pai->toEqual($father->getKey())
        ->idpes_mae->toEqual($mother->getKey())
        ->povo_indigena_educacenso_id->toEqual($people)
        ->and((bool) $individual->falecido)->toBeTrue()
        ->and($person->slug)->toBe('ana alves')
        ->and(DB::table('cadastro.documento')->where('idpes', $person->getKey())->first())
        ->tipo_cert_civil->toEqual(91)
        ->num_termo->toEqual(1001)
        ->num_livro->toBe('A-12')
        ->num_folha->toEqual(77)
        ->certidao_nascimento->toBe('');
});

test('registra na trilha do CPF o que mudou', function () {
    ($this->send)($this->new, ($this->payload)())->assertOk();

    $person = ($this->stored)();

    expect(DB::table('timelines')->where('model_id', $person->getKey())->count())->toBe(1);

    $this->get(($this->edit)($person->individual))->assertOk()->assertSee('Última alteração do CPF');
});

test('abre a edição com os dados gravados', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909', 'nis_pis_pasep' => 12345, 'renda_mensal' => 2500.5, 'data_admissao' => '2020-02-01', 'sexo' => 'F']);
    $ana->phone(LegacyPhone::TYPE_LANDLINE, '48', '34320001')->picture('https://arquivos.test/foto.jpg');
    $ana->document(['rg' => '7654321', 'certidao_casamento' => '99999999901234567890123456789012']);
    $ana->address();
    $mother = $ana->mother('Marta Alves');

    $html = $this->get(($this->edit)($ana->individual))->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_pessoa_fj'])->toBe((string) $ana->individual->getKey())
        ->and($fields['nm_pessoa'])->toBe('Ana Alves')
        ->and($fields['id_federal'])->toBe('123.456.789-09')
        ->and($fields['possui_cpf'])->toBe('1')
        ->and($fields['nis_pis_pasep'])->toBe('00000012345')
        ->and($fields['renda_mensal'])->toBe('2.500,50')
        ->and($fields['data_admissao'])->toBe('01/02/2020')
        ->and($fields['data_nasc'])->toBe('20/04/2015')
        ->and($fields['sexo'])->toBe('F')
        ->and($fields['mae_id'])->toBe((string) $mother->getKey())
        ->and($fields['rg'])->toBe('7654321')
        ->and($fields['tipo_certidao_civil'])->toBe('certidao_casamento_novo_formato')
        ->and($fields['ddd_telefone_1'])->toBe('48')
        ->and($fields['telefone_1'])->toBe('34320001')
        ->and($fields['address'])->toBe('Rua das Acácias')
        ->and($html)->toContain('<img height="117" src="https://arquivos.test/foto.jpg"/>')
        ->toContain("id='btn_excluir'")
        ->and($html)->toContain('name="file_delete"');
});

test('na edição de uma pessoa desativada avisa quando e por quem', function (?string $deleted, string $shown) {
    $ana = StudentScenario::named('Ana Alves', individual: ['ativo' => 0, 'data_exclusao' => $deleted, 'ref_usuario_exc' => $this->user->getKey()]);
    LegacyEmployee::query()->whereKey($this->user->getKey())->update(['matricula' => 4321]);

    $this->get(($this->edit)($ana->individual))
        ->assertOk()
        ->assertSee("Este cadastro foi desativado em <strong>{$shown}</strong>, pelo usuário <strong>4321</strong>.", false);
})->with([
    'com data' => ['2030-05-15', '15/05/2030'],
    'sem data' => [null, ''],
]);

test('ao cadastrar o pai ou a mãe de outra pessoa sugere o sexo e devolve o tipo', function (string $type, string $sex) {
    $_REQUEST = ['parent_type' => $type];

    try {
        $fields = LegacyFields::from($this->get($this->new)->assertOk()->getContent());
    } finally {
        $_REQUEST = [];
    }

    expect($fields['sexo'])->toBe($sex)
        ->and($fields['parent_type'])->toBe($type);
})->with(['pai' => ['pai', 'M'], 'mãe' => ['mae', 'F']]);

test('defeito: a dica com o nome do pai digitado no aluno nunca aparece', function () {
    // A classe legada do aluno não traz `nm_pai` e `nm_mae` do banco.
    $ana = StudentScenario::named('Ana Alves', ['nm_pai' => 'Pai Digitado']);

    $this->get(($this->edit)($ana->individual))->assertOk()->assertDontSee('Pai Digitado');
});

test('edita a pessoa, trocando os pais, o CPF e o nome social, e avisa o usuário vinculado', function () {
    Event::fake([UserUpdated::class]);

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909', 'sexo' => 'F', 'nome_social' => null]);
    $ana->phone(LegacyPhone::TYPE_LANDLINE, '48', '34320001');
    $mother = $ana->mother('Marta Alves');
    $father = StudentScenario::individual('Pedro Alves');

    // A pessoa também é um usuário do sistema.
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $ana->individual->getKey()]);
    LegacyUserFactory::new()->create(['cod_usuario' => $ana->individual->getKey()]);

    ($this->send)(($this->edit)($ana->individual), ($this->payload)([
        'tipoacao' => 'Editar',
        'cod_pessoa_fj' => (string) $ana->individual->getKey(),
        'nm_pessoa' => 'Ana Alves Souza',
        'id_federal' => '987.654.321-00',
        'pai_id' => (string) $father->getKey(),
        'mae_id' => (string) $mother->getKey(),
        'religiao_id' => '',
        'povo_indigena_educacenso_id' => (string) DB::table('modules.povo_indigena_educacenso')->orderBy('id')->value('id'),
        'tipo_certidao_civil' => 'certidao_casamento_novo_formato',
        'certidao_casamento' => '99999999901234567890123456789012',
        'certidao_nascimento' => '',
        'telefone_1' => '34329999',
        'tipo_nacionalidade' => '2',
        'pais_origem_id' => '999',
    ]))->assertOk();

    $individual = $ana->individual->refresh();

    expect($individual->person->nome)->toBe('Ana Alves Souza')
        ->and($individual->person->slug)->toBe('nina alves ana alves souza')
        ->and((int) $individual->person->idpes_rev)->toBe($this->user->getKey())
        ->and($individual->getRawOriginal('cpf'))->toEqual(98765432100)
        ->and($individual->idpes_pai)->toEqual($father->getKey())
        ->and($individual->idpes_mae)->toEqual($mother->getKey())
        ->and($individual->ref_cod_religiao)->toBeNull()
        ->and($individual->povo_indigena_educacenso_id)->not->toBeNull()
        ->and(DB::table('cadastro.documento')->where('idpes', $individual->getKey())->first())
        ->certidao_casamento->toBe('99999999901234567890123456789012')
        ->certidao_nascimento->toBe('')
        ->and($individual->nacionalidade)->toEqual(2)
        ->and($individual->idpais_estrangeiro)->toEqual(999)
        ->and($individual->nome_social)->toBe('Nina Alves')
        ->and($individual->person->phones->firstWhere('tipo', LegacyPhone::TYPE_LANDLINE)->fone)->toBe('34329999')
        ->and($individual->race()->pluck('nm_raca')->all())->toBe(['Raça Alfa']);

    Event::assertDispatched(UserUpdated::class);
});

test('a edição limpa o CPF, o país de origem de quem é brasileiro e os campos opcionais vazios', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['name' => 'Documento em emissão', 'requires_observation' => false]);
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909', 'nacionalidade' => 3, 'idpais_estrangeiro' => 999, 'sus' => '1', 'nis_pis_pasep' => 123, 'renda_mensal' => 10]);

    ($this->send)(($this->edit)($ana->individual), ($this->payload)([
        'tipoacao' => 'Editar',
        'cod_pessoa_fj' => (string) $ana->individual->getKey(),
        'possui_cpf' => '0',
        'id_federal' => '',
        'document_absence_reason_id' => (string) $reason->getKey(),
        'tipo_nacionalidade' => '1',
        'pais_origem_id' => '999',
        'sus' => '',
        'nis_pis_pasep' => '',
        'renda_mensal' => '',
        'data_admissao' => '',
        'zona_localizacao_censo' => '',
        'povo_indigena_educacenso_id' => '',
        'nome_social' => null,
    ]))->assertOk();

    $individual = $ana->individual->refresh();

    expect($individual->getRawOriginal('cpf'))->toBeNull()
        ->and($individual)
        ->document_absence_reason_id->toEqual($reason->getKey())
        ->nacionalidade->toEqual(1)
        ->idpais_estrangeiro->toBeNull()
        ->sus->toBeNull()
        ->nis_pis_pasep->toBeNull()
        ->renda_mensal->toBeNull()
        ->data_admissao->toBeNull()
        ->zona_localizacao_censo->toBeNull()
        // Sem o nome social no envio, o slug não é recalculado.
        ->and($individual->person->slug)->toBe($ana->person->slug);
});

test('anexa e remove arquivos da pessoa', function () {
    Storage::fake();

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $old = FileFactory::new()->create(['original_name' => 'antigo.pdf']);
    FileRelationFactory::new()->create(['relation_type' => LegacyIndividual::class, 'relation_id' => $ana->individual->getKey(), 'file_id' => $old->getKey()]);

    $this->get(($this->edit)($ana->individual))->assertOk()->assertSee('antigo.pdf');

    ($this->send)(($this->edit)($ana->individual), ($this->payload)([
        'tipoacao' => 'Editar',
        'cod_pessoa_fj' => (string) $ana->individual->getKey(),
        'file_url' => json_encode([['url' => 'https://arquivos.test/rg.pdf', 'size' => 1234, 'originalName' => 'rg.pdf', 'extension' => 'pdf']]),
        'file_url_deleted' => (string) $old->getKey(),
    ]))->assertOk();

    expect($ana->individual->files()->pluck('original_name')->all())->toBe(['rg.pdf']);

    $this->assertModelMissing($old);
});

test('exclui a pessoa desativando o cadastro e avisa o usuário vinculado', function () {
    Event::fake([UserDeleted::class]);

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $ana->student->delete();
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $ana->individual->getKey()]);
    LegacyUserFactory::new()->create(['cod_usuario' => $ana->individual->getKey(), 'ativo' => 0]);

    ($this->send)(($this->edit)($ana->individual), ['tipoacao' => 'Excluir', 'cod_pessoa_fj' => (string) $ana->individual->getKey()])
        ->assertRedirect('atendidos_lst.php')
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.');

    expect($ana->individual->refresh())
        ->ativo->toEqual(0)
        ->ref_usuario_exc->toEqual($this->user->getKey())
        ->data_exclusao->not->toBeNull();

    Event::assertDispatched(UserDeleted::class);
});

test('recusa excluir a pessoa com vínculos', function (Closure $setup, string $message) {
    $person = StudentScenario::individual('Ana Alves', ['cpf' => '12345678909']);

    $setup->call($this, $person);

    ($this->send)(($this->edit)($person), ['tipoacao' => 'Excluir', 'cod_pessoa_fj' => (string) $person->getKey()])
        ->assertOk()
        ->assertSee($message);

    expect($person->refresh()->ativo)->toBe(1);
})->with([
    'aluno' => [fn ($person) => StudentScenario::named('Bia Borges')->student->update(['ref_idpes' => $person->getKey()]), 'Não foi possível excluir. Esta pessoa possuí vínculo com aluno.'],
    'mãe de outra pessoa' => [fn ($person) => StudentScenario::individual('Bia Borges', ['idpes_mae' => $person->getKey()]), 'Não foi possível excluir. A pessoa possuí vínculo(s) com aluno(s) como mãe, pai ou outro responsável.'],
    'usuário ativo e funcionário ativo' => [function ($person) {
        LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $person->getKey(), 'ativo' => 1]);
        LegacyUserFactory::new()->create(['cod_usuario' => $person->getKey(), 'ativo' => 1]);
    }, 'Não foi possível excluir. Esta pessoa possuí vínculo com usuário do sistema.'],
    'servidor ativo e alocado' => [function ($person) {
        $employee = EmployeeFactory::new()->create(['cod_servidor' => $person->getKey(), 'ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'ativo' => 1]);
        EmployeeAllocationFactory::new()->create(['ref_cod_servidor' => $employee->getKey(), 'ref_ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'ativo' => 1]);
    }, 'Não foi possível excluir. Esta pessoa possuí vínculo com servidor.'],
]);

test('quem só pode visualizar não abre o cadastro, e quem não pode remover não vê o botão de excluir', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $person = StudentScenario::individual('Ana Alves');

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(43, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]));
    $this->get(($this->edit)($person))->assertOk()->assertDontSee("id='btn_excluir'", false);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(43, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]));
    $this->get($this->new)->assertRedirect('atendidos_lst.php');
});
