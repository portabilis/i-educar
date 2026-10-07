<?php

use App\Models\DeficiencyType;
use App\Models\FileRelation;
use App\Models\LegacyPhone;
use App\Models\LegacyStudent;
use App\Models\TransportationProvider;
use App\Services\CacheService;
use App\Setting;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyMaritalStatusFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\UniformDistributionFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->configuration = fn (array $attributes = []) => LegacyGeneralConfigurationFactory::new()->create($attributes + [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);

    $this->detail = fn (StudentScenario $scenario) => $this->get('/intranet/educar_aluno_det.php?cod_aluno=' . $scenario->student->getKey())->assertOk();
    $this->personLink = fn ($individual, string $name) => '<a target="_blank" href="/intranet/atendidos_det.php?cod_pessoa=' . $individual->getKey() . '">' . $name . '</a>';
});

test('mostra os dados pessoais, o endereço e a origem do aluno', function () {
    $ana = StudentScenario::named('Ana Alves', ['aluno_estado_id' => '111.222.333-4', 'analfabeto' => 1], [
        'nome_social' => 'Nina Alves',
        'cpf' => '12345678909',
        'data_nasc' => '2015-04-20',
        'sexo' => 'F',
        'ideciv' => LegacyMaritalStatusFactory::new()->create(['descricao' => 'Solteira']),
        'idmun_nascimento' => CityFactory::new()->create(['name' => 'Cidade Natal']),
        'nacionalidade' => 3,
        // A coluna é numeric(3), então o país precisa de um código pequeno.
        'idpais_estrangeiro' => CountryFactory::new()->create(['id' => 999, 'name' => 'País Vizinho']),
        'nis_pis_pasep' => 12345,
    ]);
    $ana->person->update(['email' => 'ana@exemplo.test', 'url' => 'https://ana.exemplo.test']);
    $ana->address();
    $ana->religion('Religião Alfa')->race('Raça Alfa');

    ($this->detail)($ana)
        ->assertSeeInOrder([
            (string) $ana->student->getKey(),
            '111.222.333-4',
            'Nome Aluno', 'ANA ALVES',
            'Nome social e/ou afetivo', 'NINA ALVES',
            'CPF', '123.456.789-09',
            'Data de Nascimento', '20/04/2015',
            'Analfabeto', 'Sim',
            'Sexo', 'Feminino',
            'Estado Civil', 'Solteira',
            'Logradouro', 'Rua das Acácias',
            'Número', '120',
            'Complemento', 'Casa 2',
            'Bairro', 'Bairro Jardim',
            'Cidade', 'Cidade Aurora',
            'UF', 'ZA',
            'CEP', '88800-123',
            'Naturalidade', 'Cidade Natal',
            'Nacionalidade', 'Estrangeiro',
            'País de Origem', 'País Vizinho',
            'E-mail', 'ana@exemplo.test',
            'Página Pessoal', 'https://ana.exemplo.test',
            'Religião', 'Religião Alfa',
            'Raça', 'Raça Alfa',
            'Transporte escolar', 'Não utiliza',
            'Utiliza transporte rural', 'Não',
            'NIS', '00000012345',
        ])
        ->assertSee(($this->personLink)($ana->individual, 'ANA ALVES'), false)
        ->assertSee("<span class='cpf-situacao cpf-situacao-com'>123.456.789-09</span>", false)
        ->assertDontSee('não possui CPF registrado');
});

test('sem os dados opcionais mostra só o essencial', function () {
    $ana = StudentScenario::named('Ana Alves', ['analfabeto' => 0], ['cpf' => '12345678909', 'sexo' => null, 'ideciv' => null, 'idmun_nascimento' => null]);

    ($this->detail)($ana)
        ->assertSeeInOrder(['Nome Aluno', 'ANA ALVES', 'Analfabeto', 'Não', 'Transporte escolar', 'Não utiliza'])
        ->assertDontSee('Nome social e/ou afetivo')
        ->assertDontSee('Sexo')
        ->assertDontSee('Estado Civil')
        ->assertDontSee('Logradouro')
        ->assertDontSee('Naturalidade')
        ->assertDontSee('Nacionalidade')
        ->assertDontSee('Telefone 1')
        ->assertDontSee('Projetos')
        // Defeito: a tabela de benefícios aparece vazia mesmo sem benefício,
        // porque a coleção nunca passa no empty().
        ->assertSee('Benefícios</td>', false);
});

test('mostra os pais com a situação do CPF de cada um e o responsável conforme o tipo', function (?string $type, array $expected, array $missing) {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->guardianType($type);
    $mother = $ana->mother('Marta Alves', ['cpf' => '98765432100']);
    $father = $ana->father('Pedro Alves');

    $response = ($this->detail)($ana)
        ->assertSee('Pai:</td>', false)
        ->assertSee("Pedro Alves <span class='cpf-situacao cpf-situacao-sem' title='Sem justificativa registrada'>Sem CPF</span>", false)
        ->assertSee("Marta Alves <span class='cpf-situacao cpf-situacao-com'>987.654.321-00</span>", false)
        ->assertSee('O responsável pai Pedro Alves não possui CPF registrado e não há justificativa de ausência.')
        ->assertSee('aproveite para atualizá-la.');

    $links = ['mãe' => ($this->personLink)($mother, 'Marta Alves'), 'pai' => ($this->personLink)($father, 'Pedro Alves')];

    $response->assertSee('Nome do Responsável');

    foreach ($expected as $who) {
        $response->assertSee($links[$who], false);
    }

    foreach ($missing as $who) {
        $response->assertDontSee($links[$who], false);
    }
})->with([
    'mãe' => ['m', ['mãe'], ['pai']],
    'pai' => ['p', ['pai'], ['mãe']],
    'ambos' => ['a', ['pai', 'mãe'], []],
    // Sem tipo definido, o primeiro que existir: o pai.
    'sem tipo' => [null, ['pai'], ['mãe']],
]);

test('com outra pessoa como responsável mostra o vínculo e a situação do CPF dela', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->guardianType('r');
    $guardian = $ana->guardian('Rita Ramos');
    $ana->father('Pedro Alves', ['cpf' => '11122233396']);

    ($this->detail)($ana)
        ->assertSee(($this->personLink)($guardian, 'Rita Ramos') . " <span class='cpf-situacao cpf-situacao-sem'", false)
        ->assertSee("Pedro Alves <span class='cpf-situacao cpf-situacao-com'>111.222.333-96</span>", false)
        ->assertSee('O responsável Rita Ramos não possui CPF registrado e não há justificativa de ausência.')
        ->assertDontSee('Nome do Responsável');
});

test('defeito: os nomes de pai e mãe digitados no aluno nunca aparecem, e o responsável sai em branco', function () {
    // A classe legada não traz `nm_pai` e `nm_mae` do banco; e sem responsável
    // a tela ainda imprime a linha "Nome do Responsável" vazia.
    $ana = StudentScenario::named('Ana Alves', ['nm_pai' => 'Pai Digitado', 'nm_mae' => 'Mãe Digitada'], ['cpf' => '12345678909']);

    ($this->detail)($ana)
        ->assertDontSee('Pai Digitado')
        ->assertDontSee('Mãe Digitada')
        ->assertSee('Nome do Responsável');
});

test('avisa quando o aluno não tem CPF, com ou sem justificativa, e oferece atualizar', function () {
    $ana = StudentScenario::named('Ana Alves');

    ($this->detail)($ana)
        ->assertSee('O aluno ANA ALVES não possui CPF registrado e não há justificativa de ausência.')
        ->assertSee('atualize antes de prosseguir.')
        ->assertSee('data-pessoa-id="' . $ana->individual->getKey() . '"', false)
        ->assertSee("title='Sem justificativa registrada'>Sem CPF</span>", false)
        ->assertSee('var motivosAusenciaCpf = ', false);

    $reason = DocumentAbsenceReasonFactory::new()->create(['name' => 'Documento em emissão', 'requires_observation' => false]);
    $ana->individual->update(['document_absence_reason_id' => $reason->getKey()]);

    ($this->detail)($ana)
        ->assertSee('O aluno ANA ALVES não possui CPF registrado. Motivo: Documento em emissão')
        ->assertSee("title='Documento em emissão'>Sem CPF</span>", false);
});

test('quem só pode visualizar vê o aviso de CPF sem o botão de atualizar', function () {
    ($this->configuration)();

    $ana = StudentScenario::named('Ana Alves');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->detail)($ana)
        ->assertSee('O aluno ANA ALVES não possui CPF registrado')
        ->assertDontSee('atualizar-cpf', false)
        ->assertDontSee('var motivosAusenciaCpf = ', false)
        ->assertDontSee('/module/Cadastro/aluno', false)
        ->assertDontSee('educar_historico_escolar_lst.php', false);
});

test('mostra os telefones com o DDD', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])
        ->phone(LegacyPhone::TYPE_LANDLINE, '48', '34320001')
        ->phone(LegacyPhone::TYPE_MOBILE, '48', '34320002')
        ->phone(LegacyPhone::TYPE_MOBILE_ALT, '48', '999990003')
        ->phone(LegacyPhone::TYPE_FAX, '48', '34320004');

    ($this->detail)($ana)
        ->assertSeeInOrder(['Telefone 1', '34320001', 'Telefone 2', '34320002', 'Celular', '999990003', 'Fax', '34320004'])
        ->assertSee('(48)&nbsp;34320001', false)
        ->assertSee('(48)&nbsp;999990003', false);
});

test('lista os benefícios, as deficiências e os transtornos em tabelas separadas', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $ana->benefit('Benefício Alfa');
    $ana->deficiency('Deficiência Alfa', DeficiencyType::DEFICIENCY);
    $ana->deficiency('Transtorno Alfa', DeficiencyType::DISORDER);

    ($this->detail)($ana)->assertSeeInOrder([
        'Benefícios', 'Benefício Alfa',
        'Deficiências', 'Deficiência Alfa',
        'Transtornos', 'Transtorno Alfa',
    ]);
});

test('lista os documentos anexados e os laudos médicos do aluno', function () {
    $ana = StudentScenario::named('Ana Alves', [
        'url_documento' => json_encode([['url' => 'https://arquivos.test/rg.pdf'], ['url' => 'https://arquivos.test/certidao.pdf']]),
    ], ['cpf' => '12345678909']);

    foreach (['https://arquivos.test/laudo-1.pdf', 'https://arquivos.test/laudo-2.pdf'] as $url) {
        FileRelationFactory::new()->create([
            'relation_type' => LegacyStudent::class,
            'relation_id' => $ana->student->getKey(),
            'file_id' => FileFactory::new()->create(['url' => $url]),
            'type' => FileRelation::TYPE_MEDICAL_REPORT,
        ]);
    }

    ($this->detail)($ana)
        ->assertSeeInOrder(['Documentos do aluno', 'Visualizar documento', 'Laudo médico do aluno', 'Visualizar laudo 1', 'Visualizar laudo 2'])
        ->assertSee("href='https://arquivos.test/rg.pdf'", false)
        ->assertSee("href='https://arquivos.test/certidao.pdf'", false)
        ->assertSee("href='https://arquivos.test/laudo-2.pdf'", false);
});

test('mostra o RG e a certidão civil conforme o formato', function (array $document, array $expected, array $missing) {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);

    if (isset($document['idorg_exp_rg'])) {
        $document['idorg_exp_rg'] = DB::table('cadastro.orgao_emissor_rg')->insertGetId(['sigla' => 'SSP', 'descricao' => 'Secretaria de Segurança', 'situacao' => 'A'], 'idorg_rg');
    }

    $ana->document($document);

    $response = ($this->detail)($ana)->assertSeeInOrder($expected);

    foreach ($missing as $text) {
        $response->assertDontSee($text);
    }
})->with([
    'RG e certidão de nascimento no formato novo' => [
        [
            'rg' => '7654321', 'data_exp_rg' => '2020-03-10', 'sigla_uf_exp_rg' => 'SC', 'idorg_exp_rg' => true,
            'data_emissao_cart_trabalho' => '2021-01-05',
            'certidao_nascimento' => '12345678901234567890123456789012',
            'data_emissao_cert_civil' => '2015-05-02', 'sigla_uf_cert_civil' => 'SC', 'cartorio_cert_civil' => 'Cartório Central',
        ],
        [
            'RG', '7654321', 'Data de Expedição RG', '10/03/2020', 'Órgão Expedição RG', 'SSP', 'Estado Expedidor', 'SC',
            'Tipo Certidão Civil', 'Nascimento (novo formato)', 'Número Certidão Civil', '12345678901234567890123456789012',
            'Emissão Certidão Civil', '02/05/2015', 'Sigla Certidão Civil', 'SC', 'Cartório', 'Cartório Central',
        ],
        ['Termo'],
    ],
    'certidão de casamento no formato novo' => [
        ['rg' => '7654321', 'certidao_casamento' => '99999999901234567890123456789012'],
        ['Tipo Certidão Civil', 'Casamento (novo formato)', 'Número Certidão Civil', '99999999901234567890123456789012'],
        ['Data de Expedição RG', 'Emissão Certidão Civil'],
    ],
    'certidão de nascimento no formato antigo' => [
        ['tipo_cert_civil' => 91, 'num_termo' => '1001', 'num_livro' => 'A-12', 'num_folha' => '77'],
        ['Tipo Certidão Civil', 'Nascimento (antigo formato)', 'Termo', '1001', 'Livro', 'A-12', 'Folha', '77'],
        ['Número Certidão Civil'],
    ],
    'certidão de casamento no formato antigo, sem os números' => [
        ['tipo_cert_civil' => 92],
        ['Tipo Certidão Civil', 'Casamento (antigo formato)'],
        ['Termo', 'Folha'],
    ],
]);

test('mostra quem responde pelo transporte escolar', function (int $provider, string $description) {
    $ana = StudentScenario::named('Ana Alves', ['tipo_transporte' => $provider, 'utiliza_transporte_rural' => true], ['cpf' => '12345678909']);

    ($this->detail)($ana)->assertSeeInOrder(['Transporte escolar', 'Sim', 'Responsável transporte', $description, 'Utiliza transporte rural', 'Sim']);
})->with([
    'estadual' => [TransportationProvider::STATE, 'Estadual'],
    'municipal' => [TransportationProvider::CITY, 'Municipal'],
]);

test('mostra a foto do aluno com os controles de girar', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->picture('https://arquivos.test/foto.jpg');

    ($this->detail)($ana)
        ->assertSee('<img id="student-picture" height="117" src="https://arquivos.test/foto.jpg"/>', false)
        ->assertSee('Girar para esquerda');
});

test('mostra o código INEP quando a instituição pede', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->inep('123456789012');

    ($this->detail)($ana)->assertDontSee('Código inep');

    ($this->configuration)(['mostrar_codigo_inep_aluno' => 1]);

    ($this->detail)($ana)->assertSeeInOrder(['Código inep', '123456789012']);
});

test('oferece os botões de cadastro, matrícula, histórico e uniforme', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $code = $ana->student->getKey();

    ($this->detail)($ana)
        ->assertSee('/module/Cadastro/aluno"', false)
        ->assertSee('/module/Cadastro/aluno?id=' . $code, false)
        ->assertSee("go(\"educar_matricula_cad.php?ref_cod_aluno={$code}\");", false)
        ->assertSee("go(\"educar_historico_escolar_lst.php?ref_cod_aluno={$code}\");", false)
        ->assertSee("go(\"educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$code}\");", false)
        ->assertSee("go(\"educar_reconhecimento_facial_det.php?ref_cod_aluno={$code}\");", false)
        ->assertSee('educar_aluno_lst.php', false);
});

test('com o cadastro de alunos bloqueado não oferece o botão Novo', function () {
    ($this->configuration)(['bloquear_cadastro_aluno' => 1]);

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);

    ($this->detail)($ana)
        ->assertDontSee('/module/Cadastro/aluno"', false)
        ->assertSee('/module/Cadastro/aluno?id=' . $ana->student->getKey(), false);
});

test('só oferece nova matrícula e reconhecimento facial a quem pode ver essas telas', function (Closure $user, bool $allowed) {
    ($this->configuration)();

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $code = $ana->student->getKey();

    $this->actingAs($user->call($this));

    $response = ($this->detail)($ana)->assertSee("go(\"educar_historico_escolar_lst.php?ref_cod_aluno={$code}\");", false);

    foreach (['educar_matricula_cad.php', 'educar_reconhecimento_facial_det.php'] as $screen) {
        $allowed ? $response->assertSee($screen, false) : $response->assertDontSee($screen, false);
    }
})->with([
    'com acesso às duas telas' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->withAccess(680)->withAccess(5781)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]), true],
    'sem acesso às duas telas' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]), false],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(680)->withAccess(5781)->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]), false],
]);

test('põe o botão do sistema externo configurado, trocando aluno, usuário e token no link', function () {
    // Essas chaves vêm da tabela de configurações a cada requisição, por cima
    // do config(); e ficam em cache.
    Setting::query()->where('key', 'legacy.app.alunos.sistema_externo.titulo')->update(['value' => 'Portal do Aluno']);
    Setting::query()->where('key', 'legacy.app.alunos.sistema_externo.link')->update(['value' => 'https://portal.test/?aluno=@aluno&usuario=@usuario&token=@token']);
    Setting::query()->where('key', 'legacy.app.alunos.sistema_externo.token')->update(['value' => 'segredo']);
    CacheService::clearSettings();

    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);

    ($this->detail)($ana)
        ->assertSee('Portal do Aluno')
        ->assertSee("go(\"https://portal.test/?aluno={$ana->student->getKey()}&usuario={$this->user->getKey()}&token=segredo\")", false);
});

test('mostra a ficha médica com as respostas e as descrições preenchidas', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909', 'sus' => '898001234567890'])->medicalRecord([
        'grupo_sanguineo' => 'AB',
        'fator_rh' => '+',
        'alergia_medicamento' => 'S',
        'desc_alergia_medicamento' => 'Dipirona',
        'alergia_alimento' => 'S',
        'desc_alergia_alimento' => 'Amendoim',
        'doenca_congenita' => 'S',
        'desc_doenca_congenita' => 'Cardiopatia',
        'doenca_outras' => 'Dengue',
        'tratamento_medico' => 'S',
        'desc_tratamento_medico' => 'Fisioterapia',
        'medicacao_especifica' => 'S',
        'desc_medicacao_especifica' => 'Insulina',
        'acomp_medico_psicologico' => 'S',
        'desc_acomp_medico_psicologico' => 'Ansiedade',
        'restricao_atividade_fisica' => 'S',
        'desc_restricao_atividade_fisica' => 'Corrida',
        'fratura_trauma' => 'S',
        'desc_fratura_trauma' => 'Braço esquerdo',
        'plano_saude' => 'S',
        'desc_plano_saude' => 'Plano Alfa',
        'desc_aceita_hospital_proximo' => 'Hospital Central',
        'responsavel' => 'Tia Teresa',
        'responsavel_parentesco' => 'Tia',
        'responsavel_parentesco_telefone' => '4834320009',
        'responsavel_parentesco_celular' => '48999990009',
    ]);

    ($this->detail)($ana)->assertSeeInOrder([
        'Grupo sanguíneo', 'AB',
        'Fator RH', '+',
        'Número do cartão do SUS', '898001234567890',
        'Possui alergia a algum medicamento', 'Sim', 'Quais', 'Dipirona',
        'Possui alergia a algum alimento', 'Sim', 'Quais', 'Amendoim',
        'Possui alguma doenca congênita', 'Sim', 'Quais', 'Cardiopatia',
        'É fumante', 'Não',
        'Já contraiu caxumba', 'Não',
        'Outras doenças que o aluno já contraiu', 'Dengue',
        'Epilético', 'Não',
        'Faz tratamento médico', 'Sim', 'Qual', 'Fisioterapia',
        'Ingere medicação específica', 'Sim', 'Qual', 'Insulina',
        'Acompanhamento médico ou psicológico', 'Sim', 'Motivo', 'Ansiedade',
        'Restrição para atividades físicas', 'Sim', 'Qual', 'Corrida',
        'Teve alguma fratura ou trauma', 'Sim', 'Qual', 'Braço esquerdo',
        'Tem plano de saúde', 'Sim', 'Qual', 'Plano Alfa',
        'Hospital Central',
        'Nome', 'Tia Teresa',
        'Parentesco', 'Tia',
        'Telefone', '4834320009',
        'Celular', '48999990009',
    ]);
});

test('na ficha médica em branco só aparecem as respostas', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->medicalRecord();

    ($this->detail)($ana)
        ->assertSeeInOrder(['Possui alergia a algum medicamento', 'Não', 'Tem plano de saúde', 'Não'])
        ->assertDontSee('Grupo sanguíneo')
        ->assertDontSee('Fator RH')
        ->assertDontSee('Número do cartão do SUS')
        ->assertDontSee('Quais')
        ->assertDontSee('Outras doenças que o aluno já contraiu');
});

test('mostra o uniforme distribuído no ano corrente', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);

    $distribution = UniformDistributionFactory::new()->create([
        'student_id' => $ana->student,
        'year' => now()->year,
        'type' => 'Entregue',
        'distribution_date' => '15/03/' . now()->year,
        'complete_kit' => false,
        'coat_jacket_qty' => 11,
        'socks_qty' => 19,
        'sneakers_qty' => null,
    ]);

    ($this->detail)($ana)->assertSeeInOrder([
        'Recebeu kit completo', 'Não',
        'Tipo', 'Entregue',
        'Data da distribuição', '15/03/' . now()->year,
        'Quantidade de agasalhos (jaqueta)', '11',
        'Quantidade de meias', '19',
        'Quantidade de tênis', '0',
    ]);

    $distribution->update(['complete_kit' => true]);

    ($this->detail)($ana)
        ->assertSeeInOrder(['Recebeu kit completo', 'Sim', 'Data da distribuição', '15/03/' . now()->year])
        ->assertDontSee('Quantidade de meias');
});

test('não mostra o uniforme de outro ano', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);

    UniformDistributionFactory::new()->create(['student_id' => $ana->student, 'year' => now()->year - 1]);

    ($this->detail)($ana)->assertDontSee('Recebeu kit completo');
});

test('descreve a moradia do aluno', function (array $housing, string $description, string $situation) {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909'])->housing($housing + [
        'geladeira' => 'S',
        'recursos_tecnologicos' => json_encode(['Internet', 'Computador']),
        'lixo' => 'N',
    ]);

    ($this->detail)($ana)->assertSeeInOrder([
        'Moradia', $description,
        'Situação', $situation,
        'Quantidade de quartos', '3',
        'Quantidade de banheiros', '2',
        'Possui geladeira', 'S',
        'Possui acesso à recursos técnologicos?', 'Internet, Computador',
        'Quantidade de pessoas', '4',
        'Renda familiar', 'R$ 2500',
        'Possui coleta de lixo', 'N',
    ]);
})->with([
    'apartamento alugado' => [['moradia' => 'A', 'moradia_situacao' => 1], 'Apartamento', 'Alugado'],
    'casa de alvenaria própria' => [['moradia' => 'C', 'material' => 'A', 'moradia_situacao' => 2], 'Casa de alvenaria', 'Próprio'],
    'casa de madeira cedida' => [['moradia' => 'C', 'material' => 'M', 'moradia_situacao' => 3], 'Casa de madeira', 'Cedido'],
    'casa mista financiada' => [['moradia' => 'C', 'material' => 'I', 'moradia_situacao' => 4], 'Casa mista', 'Financiado'],
    'outra' => [['moradia' => 'O', 'casa_outra' => 'Barco', 'moradia_situacao' => 5], 'Outra: Barco', 'Outra'],
    'não informada' => [['moradia' => null, 'moradia_situacao' => 1], 'Não informado', 'Alugado'],
]);

test('lista os projetos do aluno em ordem de nome, com as datas e o turno', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['cpf' => '12345678909']);
    $ana->project('Projeto Música', ['turno' => 2, 'data_inclusao' => '2030-03-01', 'data_desligamento' => '2030-06-30']);
    $ana->project('Projeto Esporte', ['turno' => 1, 'data_inclusao' => '2030-02-10']);
    $ana->project('Projeto Teatro', ['turno' => 3]);
    $ana->project('Projeto Xadrez', ['turno' => 4]);

    ($this->detail)($ana)->assertSeeInOrder([
        'Projetos',
        'Projeto Esporte', '10/02/2030', 'Matutino',
        'Projeto Música', '01/03/2030', '30/06/2030', 'Vespertino',
        'Projeto Teatro', 'Noturno',
        'Projeto Xadrez',
    ]);
});

test('sai do detalhe quando o aluno não existe', function () {
    $this->get('/intranet/educar_aluno_det.php?cod_aluno=0')->assertRedirect('/intranet/educar_aluno_lst.php');
});
