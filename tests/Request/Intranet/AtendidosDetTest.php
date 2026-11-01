<?php

use App\Models\LegacyPhone;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->detail = fn ($individual) => $this->get('/intranet/atendidos_det.php?cod_pessoa=' . $individual->getKey())->assertOk();
});

test('mostra os dados, o endereço, os telefones e os vínculos da pessoa', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['nome_social' => 'Nina Alves', 'cpf' => '12345678909', 'data_nasc' => '2015-04-20', 'sexo' => 'F']);
    $ana->person->update(['email' => 'ana@exemplo.test', 'url' => 'https://ana.exemplo.test']);
    $ana->address()->refresh();
    $ana->race('Raça Alfa')
        ->phone(LegacyPhone::TYPE_LANDLINE, '48', '34320001')
        ->phone(LegacyPhone::TYPE_MOBILE, '48', '34320002')
        ->phone(LegacyPhone::TYPE_MOBILE_ALT, '48', '999990003')
        ->phone(LegacyPhone::TYPE_FAX, '48', '34320004')
        ->picture('https://arquivos.test/foto.jpg');

    ($this->detail)($ana->individual)
        ->assertSeeInOrder([
            'Nome', 'Ana Alves',
            'Nome social e/ou afetivo', 'Nina Alves',
            'CPF', '123.456.789-09',
            'Data de Nascimento', '20/04/2015',
            'Raça', 'Raça Alfa',
            'Endereço', 'Rua das Acácias nº 120',
            'Complemento', 'Casa 2',
            'Cidade', 'Cidade Aurora',
            'Estado', 'ZA',
            'CEP', '88800-123',
            'Telefone 1', '(48) 34320001',
            'Telefone 2', '(48) 34320002',
            'Celular', '(48) 999990003',
            'Fax', '(48) 34320004',
            'Site', 'https://ana.exemplo.test',
            'E-mail', 'ana@exemplo.test',
            'Sexo', 'Feminino',
            'Vínculos', 'Aluno',
        ])
        ->assertSee('<img height="117" src="https://arquivos.test/foto.jpg"/>', false)
        ->assertSee('educar_aluno_det.php?cod_aluno=' . $ana->student->getKey(), false)
        ->assertSee('atendidos_cad.php?cod_pessoa_fj=' . $ana->individual->getKey(), false)
        ->assertSee('atendidos_lst.php', false);
});

test('sem vínculos nem dados opcionais mostra só o nome e o CPF vazio', function () {
    $person = StudentScenario::individual('Registro Alfa', ['data_nasc' => null, 'sexo' => null]);

    ($this->detail)($person)
        ->assertSeeInOrder(['Nome', 'Registro Alfa', 'CPF', 'Vínculos', 'Pessoa física não possui vínculos'])
        ->assertDontSee('Nome social')
        ->assertDontSee('Data de Nascimento')
        ->assertDontSee('Endereço')
        ->assertDontSee('Telefone 1')
        ->assertDontSee('Sexo');
});

test('mostra os vínculos de servidor, mãe, pai e responsável de outras pessoas', function () {
    $person = StudentScenario::individual('Registro Alfa', ['sexo' => 'M']);
    $employee = EmployeeFactory::new()->create(['cod_servidor' => $person->getKey(), 'ref_cod_instituicao' => $this->user->ref_cod_instituicao]);

    $child = StudentScenario::named('Ana Alves');
    $child->individual->update(['idpes_mae' => $person->getKey(), 'idpes_pai' => $person->getKey(), 'idpes_responsavel' => $person->getKey()]);

    ($this->detail)($person)
        ->assertSeeInOrder(['Sexo', 'Masculino', 'Vínculos', 'Servidor', 'Mãe da Pessoa Física', 'Pai da Pessoa Física', 'Responsável da Pessoa Física'])
        ->assertSee("educar_servidor_det.php?cod_servidor={$employee->getKey()}&ref_cod_instituicao={$this->user->ref_cod_instituicao}", false)
        ->assertSee('atendidos_det.php?cod_pessoa=' . $child->individual->getKey() . '">Mãe da Pessoa Física', false)
        ->assertDontSee('Aluno<');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $person = StudentScenario::individual('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(43, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->detail)($person)
        ->assertSee('Registro Alfa')
        ->assertDontSee('atendidos_cad.php', false);
});

test('avisa quando a pessoa não existe', function () {
    $this->get('/intranet/atendidos_det.php?cod_pessoa=0')->assertOk()->assertSeeInOrder(['Erro', 'Pessoa não encontrada']);
});
