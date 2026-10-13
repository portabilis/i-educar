<?php

use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\PersonHasPlace;
use Database\Factories\CityFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyPhoneFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->city = CityFactory::new()->create(['name' => 'Cidade Aurora']);
    $this->new = '/intranet/empresas_cad.php';
    $this->edit = fn ($organization) => $this->new . '?idpes=' . $organization->getKey();

    $this->organization = fn (array $attributes = []) => LegacyOrganizationFactory::new()->create($attributes + [
        'idpes' => LegacyPersonFactory::new()->create(['nome' => 'Registro Original Ltda', 'tipo' => 'J', 'email' => 'contato@alfa.test', 'url' => 'https://alfa.test']),
        'fantasia' => 'Fantasia Original',
        'cnpj' => '12345678000195',
        'insc_estadual' => '250123456',
        'capital_social' => 150000,
    ]);

    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'fantasia' => 'Fantasia Alfa',
        'razao_social' => 'Registro Alfa Ltda',
        'capital_social' => '150000',
        'cnpj' => '12.345.678/0001-95',
        'city_id' => (string) $this->city->getKey(),
        'address' => 'Avenida Central',
        'number' => '500',
        'complement' => 'Sala 2',
        'neighborhood' => 'Centro',
        'postal_code' => '88800-123',
        'ddd_telefone_1' => '48',
        'telefone_1' => '34320001',
        'ddd_telefone_2' => '48',
        'telefone_2' => '34320002',
        'ddd_telefone_mov' => '48',
        'telefone_mov' => '999990003',
        'ddd_telefone_fax' => '48',
        'telefone_fax' => '34320004',
        'url' => 'https://alfa.test',
        'email' => 'contato@alfa.test',
        'insc_est' => '250.123.456',
    ];

    $this->stored = fn () => LegacyPerson::query()->where('nome', 'Registro Alfa Ltda')->sole();
});

test('abre o cadastro vazio', function () {
    $fields = LegacyFields::from($this->get($this->new)->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['idpes_cad'])->toBe((string) $this->user->getKey())
        ->and($fields['fantasia'])->toBe('')
        ->and($fields)->toHaveKeys(['cnpj', 'ddd_telefone_1', 'telefone_fax', 'insc_est']);
});

test('cadastra a empresa com os dados, o endereço e os telefones', function () {
    $this->post($this->new, ($this->payload)())
        ->assertRedirect('empresas_lst.php')
        ->assertSessionHas('legacy', 'Cadastro salvo com sucesso.');

    $person = ($this->stored)();

    expect($person)
        ->tipo->toBe('J')
        ->url->toBe('https://alfa.test')
        ->email->toBe('contato@alfa.test')
        ->idpes_cad->toEqual($this->user->getKey())
        ->and($person->organization)
        ->fantasia->toBe('Fantasia Alfa')
        ->cnpj->toBe('12345678000195')
        ->insc_estadual->toBe('250123456')
        ->capital_social->toBe('150000')
        ->and($person->phones->sortBy('tipo')->pluck('fone', 'tipo')->all())->toBe([
            LegacyPhone::TYPE_LANDLINE => '34320001',
            LegacyPhone::TYPE_MOBILE => '34320002',
            LegacyPhone::TYPE_MOBILE_ALT => '999990003',
            LegacyPhone::TYPE_FAX => '34320004',
        ])
        ->and(PersonHasPlace::query()->where('person_id', $person->getKey())->first()->place)
        ->address->toBe('Avenida Central')
        ->number->toBe('500')
        ->complement->toBe('Sala 2')
        ->neighborhood->toBe('Centro')
        ->city_id->toEqual($this->city->getKey())
        ->postal_code->toBe('88800123');
});

test('cadastra sem CNPJ, sem contato e sem endereço completo', function () {
    $this->post($this->new, ($this->payload)([
        'cnpj' => '',
        'url' => '',
        'email' => '',
        'insc_est' => 'ISENTO',
        'capital_social' => '',
        'address' => '',
        'complement' => '',
        'ddd_telefone_1' => '',
        'telefone_1' => '',
    ]))->assertRedirect('empresas_lst.php');

    $person = ($this->stored)();

    expect($person)
        ->url->toBeNull()
        ->email->toBeNull()
        ->and($person->organization)
        ->cnpj->toBeNull()
        ->insc_estadual->toBeNull()
        ->and($person->phones->pluck('tipo')->all())->not->toContain(LegacyPhone::TYPE_LANDLINE)
        ->and(PersonHasPlace::query()->where('person_id', $person->getKey())->exists())->toBeFalse();
});

test('recusa cada dado inválido com a sua mensagem', function (array $overrides, string $message) {
    $this->post($this->new, ($this->payload)($overrides))
        ->assertOk()
        ->assertSee($message);

    expect(LegacyPerson::query()->where('nome', 'Registro Alfa Ltda')->exists())->toBeFalse();
})->with([
    'e-mail inválido' => [['email' => 'contato@'], 'O campo E-mail deve conter um endereço de e-mail válido.'],
    'razão social curta' => [['razao_social' => 'AB'], 'O campo Razão Social deve conter no mínimo 3 caracteres.'],
    'nome fantasia curto' => [['fantasia' => 'AB'], 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.'],
    'capital social com letras' => [['capital_social' => '12a'], 'O campo Capital Social deve conter apenas valores numéricos.'],
    'capital social negativo' => [['capital_social' => '-10'], 'O campo Capital Social não pode conter valores negativos.'],
    'inscrição estadual com letras' => [['insc_est' => 'ABC123'], 'O campo Inscrição Estadual deve conter apenas números ou a palavra ISENTO.'],
    'CNPJ inválido' => [['cnpj' => '11.111.111/1111-11'], 'CNPJ inválido'],
    'complemento com caractere proibido' => [['complement' => 'Sala #2'], 'O campo Complemento só permite os caracteres'],
    'DDD sem telefone' => [['telefone_1' => ''], 'O campo: Telefone 1, deve ser preenchido quando o DDD estiver preenchido.'],
    'telefone sem DDD' => [['ddd_telefone_2' => ''], 'O campo: DDD, deve ser preenchido quando o Telefone 2 estiver preenchido.'],
    'celular sem DDD' => [['ddd_telefone_mov' => ''], 'O campo: DDD, deve ser preenchido quando o Celular estiver preenchido.'],
    'fax sem telefone' => [['telefone_fax' => ''], 'O campo: Fax, deve ser preenchido quando o DDD estiver preenchido.'],
]);

test('defeito: aceita um site que não é URL', function () {
    // filter_var() recebe FILTER_VALIDATE_URL como `options`, não como filtro,
    // então nada é validado.
    $this->post($this->new, ($this->payload)(['url' => 'alfa']))->assertRedirect('empresas_lst.php');

    expect(($this->stored)()->url)->toBe('alfa');
});

test('recusa o CNPJ de outra empresa', function () {
    ($this->organization)();

    $this->post($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('Já existe uma empresa cadastrada com este CNPJ.');

    expect(LegacyPerson::query()->where('nome', 'Registro Alfa Ltda')->exists())->toBeFalse();
});

test('abre a edição com os dados gravados e edita a empresa', function () {
    $organization = ($this->organization)();
    LegacyPhoneFactory::new()->create(['idpes' => $organization->getKey(), 'tipo' => LegacyPhone::TYPE_LANDLINE, 'ddd' => '48', 'fone' => '34320009']);
    PersonHasPlaceFactory::new()->create([
        'person_id' => $organization->getKey(),
        'place_id' => PlaceFactory::new()->create(['city_id' => $this->city, 'address' => 'Rua Antiga', 'neighborhood' => 'Bairro Antigo', 'postal_code' => '88800999']),
    ]);

    $fields = LegacyFields::from($this->get(($this->edit)($organization))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_pessoa_fj'])->toBe((string) $organization->getKey())
        ->and($fields['razao_social'])->toBe('Registro Original Ltda')
        ->and($fields['fantasia'])->toBe('Fantasia Original')
        ->and($fields['cnpj'])->toBe('12.345.678/0001-95')
        ->and($fields['insc_est'])->toBe('250123456')
        ->and($fields['ddd_telefone_1'])->toBe('48')
        ->and($fields['telefone_1'])->toBe('34320009')
        ->and($fields['address'])->toBe('Rua Antiga');

    $this->post(($this->edit)($organization), ($this->payload)(['tipoacao' => 'Editar', 'cod_pessoa_fj' => $organization->getKey(), 'insc_est' => '']))
        ->assertRedirect('empresas_lst.php')
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.');

    $person = ($this->stored)();

    expect($person->getKey())->toBe($organization->getKey())
        ->and($person->idpes_rev)->toEqual($this->user->getKey())
        ->and($organization->refresh())
        ->fantasia->toBe('Fantasia Alfa')
        ->insc_estadual->toBeNull()
        ->idpes_rev->toEqual($this->user->getKey())
        ->and($person->phones->firstWhere('tipo', LegacyPhone::TYPE_LANDLINE)->fone)->toBe('34320001')
        ->and(PersonHasPlace::query()->where('person_id', $person->getKey())->first()->place)
        ->address->toBe('Avenida Central')
        ->postal_code->toBe('88800123');
});

test('na edição aceita o próprio CNPJ e recusa o de outra empresa', function () {
    $organization = ($this->organization)();
    ($this->organization)(['idpes' => LegacyPersonFactory::new()->create(['nome' => 'Outra Ltda', 'tipo' => 'J']), 'cnpj' => '11222333000181']);

    $this->post(($this->edit)($organization), ($this->payload)(['tipoacao' => 'Editar', 'cod_pessoa_fj' => $organization->getKey()]))
        ->assertRedirect('empresas_lst.php');

    $this->post(($this->edit)($organization), ($this->payload)(['tipoacao' => 'Editar', 'cod_pessoa_fj' => $organization->getKey(), 'cnpj' => '11.222.333/0001-81']))
        ->assertOk()
        ->assertSee('Já existe uma empresa cadastrada com este CNPJ.');

    expect($organization->refresh()->cnpj)->toBe('12345678000195');
});

test('na edição recusa os mesmos dados inválidos do cadastro', function (array $overrides, string $message) {
    $organization = ($this->organization)();

    $this->post(($this->edit)($organization), ($this->payload)(['tipoacao' => 'Editar', 'cod_pessoa_fj' => $organization->getKey()] + $overrides))
        ->assertOk()
        ->assertSee($message);

    expect($organization->refresh()->fantasia)->toBe('Fantasia Original');
})->with([
    'e-mail inválido' => [['email' => 'contato@'], 'O campo E-mail deve conter um endereço de e-mail válido.'],
    'razão social curta' => [['razao_social' => 'AB'], 'O campo Razão Social deve conter no mínimo 3 caracteres.'],
    'nome fantasia curto' => [['fantasia' => 'AB'], 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.'],
    'capital social com letras' => [['capital_social' => '12a'], 'O campo Capital Social deve conter apenas valores numéricos.'],
    'capital social negativo' => [['capital_social' => '-10'], 'O campo Capital Social não pode conter valores negativos.'],
    'inscrição estadual com letras' => [['insc_est' => 'ABC123'], 'O campo Inscrição Estadual deve conter apenas números ou a palavra ISENTO.'],
    'CNPJ inválido' => [['cnpj' => '11.111.111/1111-11'], 'CNPJ inválido'],
    'complemento com caractere proibido' => [['complement' => 'Sala #2'], 'O campo Complemento só permite os caracteres'],
    'DDD sem telefone' => [['telefone_1' => ''], 'O campo: Telefone 1, deve ser preenchido quando o DDD estiver preenchido.'],
]);

test('abaixo do nível institucional o CNPJ vira rótulo e campo oculto', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $organization = ($this->organization)();

    $this->actingAs(LegacyUserFactory::new()->school()->withAccess(41)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]));

    $html = $this->get(($this->edit)($organization))->assertOk()->getContent();

    // O campo oculto sai com a barra codificada; a tela decodifica ao gravar.
    expect(LegacyFields::from($html)['cnpj'])->toBe('12.345.678%2F0001-95')
        ->and($html)->toContain("name='cnpj' id='cnpj' type='hidden'")
        ->not->toContain('formataCNPJ(this, event)');
});

test('a ação de excluir só volta para a listagem', function () {
    $organization = ($this->organization)();

    $this->post(($this->edit)($organization), ($this->payload)(['tipoacao' => 'Excluir', 'cod_pessoa_fj' => $organization->getKey()]))
        ->assertRedirect('empresas_lst.php');

    $this->assertModelExists($organization);
});

test('com um código inexistente abre a edição vazia', function () {
    $fields = LegacyFields::from($this->get($this->new . '?idpes=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['razao_social'])->toBe('');
});
