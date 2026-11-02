<?php

use App\Models\LegacyPhone;
use Database\Factories\CityFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyPhoneFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->organization = fn (array $attributes = []) => LegacyOrganizationFactory::new()->create($attributes + [
        'idpes' => LegacyPersonFactory::new()->create(['nome' => 'Registro Alfa Ltda', 'tipo' => 'J', 'email' => 'contato@alfa.test', 'url' => 'https://alfa.test']),
        'fantasia' => 'Fantasia Alfa',
        'cnpj' => '12345678000195',
        'insc_estadual' => '250123456',
        'capital_social' => 150000,
    ]);
    $this->detail = fn ($organization) => $this->get('/intranet/empresas_det.php?cod_empresa=' . $organization->getKey())->assertOk();
});

test('mostra os dados, o endereço e os telefones da empresa', function () {
    $organization = ($this->organization)();

    PersonHasPlaceFactory::new()->create([
        'person_id' => $organization->getKey(),
        'place_id' => PlaceFactory::new()->create([
            'city_id' => CityFactory::new()->create(['name' => 'Cidade Aurora']),
            'address' => 'Avenida Central',
            'neighborhood' => 'Centro',
            'postal_code' => '88800123',
        ]),
    ]);

    foreach ([LegacyPhone::TYPE_LANDLINE => '34320001', LegacyPhone::TYPE_MOBILE => '34320002', LegacyPhone::TYPE_MOBILE_ALT => '999990003', LegacyPhone::TYPE_FAX => '34320004'] as $type => $number) {
        LegacyPhoneFactory::new()->create(['idpes' => $organization->getKey(), 'tipo' => $type, 'ddd' => '48', 'fone' => $number]);
    }

    ($this->detail)($organization)
        ->assertSeeInOrder([
            'Razão Social', 'Registro Alfa Ltda',
            'Nome Fantasia', 'Fantasia Alfa',
            'CNPJ', '12.345.678/0001-95',
            'Endereço', 'Avenida Central',
            'CEP', '88800123',
            'Bairro', 'Centro',
            'Cidade', 'Cidade Aurora',
            'Telefone 1', '(48) 34320001',
            'Telefone 2', '(48) 34320002',
            'Celular', '(48) 999990003',
            'Fax', '(48) 34320004',
            'Site', 'https://alfa.test',
            'E-mail', 'contato@alfa.test',
            'Inscrição Estadual', '250123456',
            'Capital Social', '150000',
        ])
        ->assertSee('empresas_cad.php?idpes=' . $organization->getKey(), false)
        ->assertSee('empresas_lst.php', false);
});

test('sem inscrição estadual mostra "isento"', function () {
    $organization = ($this->organization)(['insc_estadual' => null]);

    ($this->detail)($organization)->assertSeeInOrder(['Inscrição Estadual', 'isento']);
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $organization = ($this->organization)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(41, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->detail)($organization)
        ->assertSee('Registro Alfa Ltda')
        ->assertDontSee('empresas_cad.php', false);
});

test('defeito: com uma empresa que não existe mostra a tela vazia', function () {
    $this->get('/intranet/empresas_det.php?cod_empresa=0')
        ->assertOk()
        ->assertSeeInOrder(['Razão Social', 'Nome Fantasia', 'Inscrição Estadual', 'isento']);
});
