<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use Database\Factories\CityFactory;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\StudentScenario;

/**
 * As recusas do cadastro de pessoa física, cada uma com a sua mensagem. O
 * caminho feliz está em AtendidosCadTest.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    Portabilis_Utils_User::$_currentUserId = null;

    $this->url = '/intranet/atendidos_cad.php';
    $this->institution = fn (array $attributes) => LegacyInstitution::query()->whereKey($this->user->ref_cod_instituicao)->update($attributes);

    // Os campos de documento e certidão vêm de $_REQUEST, que o PHP preenche
    // só numa requisição real; o envio preenche o superglobal à mão.
    $this->send = function (array $overrides) {
        $_REQUEST = $payload = $overrides + [
            'tipoacao' => 'Novo',
            'possui_cpf' => '1',
            'id_federal' => '123.456.789-09',
            'nm_pessoa' => 'Ana Alves',
            'sexo' => 'F',
            'data_nasc' => '20/04/2015',
            'ddd_telefone_1' => '48',
            'telefone_1' => '34320001',
            'ddd_telefone_2' => '48',
            'telefone_2' => '999990002',
            'ddd_telefone_mov' => '48',
            'telefone_mov' => '34320003',
            'ddd_telefone_fax' => '48',
            'telefone_fax' => '34320004',
            'zona_localizacao_censo' => '1',
        ];

        try {
            return $this->post($this->url, $payload);
        } finally {
            $_REQUEST = [];
        }
    };

    $this->nothingStored = fn () => expect(LegacyPerson::query()->where('nome', 'Ana Alves')->where('tipo', 'F')->exists())->toBeFalse();
});

test('recusa a situação do CPF incoerente, com o erro junto do campo', function (Closure $overrides, string $message) {
    ($this->send)($overrides->call($this))->assertOk()->assertSee($message);

    ($this->nothingStored)();
})->with([
    'possui CPF sem o número' => [fn () => ['id_federal' => ''], 'É necessário o preenchimento do CPF.'],
    'sem CPF e sem motivo' => [fn () => ['possui_cpf' => '0', 'id_federal' => ''], 'É necessário informar o motivo da ausência do CPF.'],
    'motivo que não existe' => [fn () => ['possui_cpf' => '0', 'id_federal' => '', 'document_absence_reason_id' => '999999'], 'O motivo da ausência do CPF informado não existe.'],
    'motivo do sistema' => [fn () => ['possui_cpf' => '0', 'id_federal' => '', 'document_absence_reason_id' => (string) DocumentAbsenceReasonFactory::new()->create(['from_system' => true])->getKey()], 'O motivo da ausência do CPF informado não existe.'],
    'motivo que exige observação' => [fn () => ['possui_cpf' => '0', 'id_federal' => '', 'document_absence_reason_id' => (string) DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true])->getKey()], 'Este motivo exige o preenchimento das observações.'],
    'CPF inválido' => [fn () => ['id_federal' => '111.111.111-11'], 'CPF inválido.'],
]);

test('recusa o CPF de outra pessoa, apontando para ela', function () {
    $other = StudentScenario::individual('Bia Borges', ['cpf' => '12345678909']);

    ($this->send)([])->assertSee("CPF já utilizado pela pessoa <a class='decorated' target='__blank' href='/intranet/atendidos_cad.php?cod_pessoa_fj={$other->getKey()}'>{$other->getKey()}</a>.", false);

    ($this->nothingStored)();
});

test('com a instituição exigindo documento, recusa a pessoa sem CPF, RG ou certidão', function () {
    ($this->institution)(['obrigar_documento_pessoa' => true]);
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);

    ($this->send)(['possui_cpf' => '0', 'id_federal' => '', 'document_absence_reason_id' => (string) $reason->getKey()])
        ->assertSee('É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil.');

    ($this->nothingStored)();

    // Com o RG a mesma pessoa passa.
    ($this->send)(['possui_cpf' => '0', 'id_federal' => '', 'document_absence_reason_id' => (string) $reason->getKey(), 'rg' => '7654321'])
        ->assertSee('window.opener.afterChangePessoa', false);
});

test('recusa cada dado inválido com a sua mensagem', function (Closure $overrides, string $message) {
    ($this->send)($overrides->call($this))->assertOk()->assertSee($message, false);

    ($this->nothingStored)();
})->with([
    'a mesma pessoa como pai e mãe' => [fn () => ['pai_id' => '1', 'mae_id' => '1'], 'Não é possível informar a mesma pessoa para Pai e Mãe'],
    'NIS com menos de 11 dígitos' => [fn () => ['nis_pis_pasep' => '1234567890'], 'O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.'],
    'NIS só com zeros' => [fn () => ['nis_pis_pasep' => '00000000000'], 'Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.'],
    'nome com mais de 100 caracteres' => [fn () => ['nm_pessoa' => str_repeat('Ana ', 26)], 'O Nome deve ter no máximo 100 caracteres.'],
    'nome com quatro letras repetidas' => [fn () => ['nm_pessoa' => 'Annnna Alves'], 'Nome não pode ter a repetição de 4 caracteres seguidos.'],
    'nome com número' => [fn () => ['nm_pessoa' => 'Ana Alves 2'], 'O Nome ou Nome Social não pode conter números ou caracteres especiais'],
    'nome social com símbolo' => [fn () => ['nome_social' => 'Nina@'], 'O Nome ou Nome Social não pode conter números ou caracteres especiais'],
    'data de nascimento no futuro' => [fn () => ['data_nasc' => now()->addDay()->format('d/m/Y')], 'Informe uma data de nascimento menor que o dia de hoje.'],
    'certidão de nascimento curta' => [fn () => ['tipo_certidao_civil' => 'certidao_nascimento_novo_formato', 'certidao_nascimento' => '123'], 'O campo referente a certidão de nascimento deve conter exatos 32 dígitos.'],
    'certidão de casamento curta' => [fn () => ['tipo_certidao_civil' => 'certidao_casamento_novo_formato', 'certidao_casamento' => '123'], 'O campo referente a certidão de casamento deve conter exatos 32 dígitos.'],
    'certidão de nascimento com letras' => [fn () => ['tipo_certidao_civil' => 'certidao_nascimento_novo_formato', 'certidao_nascimento' => 'AB345601552015100012345678901234'], 'O campo: Tipo certidão civil (novo formato) possui valor inválido'],
    'certidão de nascimento com ano anterior ao nascimento' => [fn () => ['tipo_certidao_civil' => 'certidao_nascimento_novo_formato', 'certidao_nascimento' => '12345601552010100012345678901234'], 'foi preenchido com o ano inválido'],
    'área de assentamento em zona urbana' => [fn () => ['localizacao_diferenciada' => '1', 'zona_localizacao_censo' => '1'], 'não pode ser preenchido com <b>Área de assentamento</b> quando o campo: Zona de residência for <b>Urbana</b>.'],
    'complemento com caractere proibido' => [fn () => ['complement' => 'Casa #2'], 'O campo Complemento só permite os caracteres'],
    'DDD sem telefone' => [fn () => ['telefone_1' => ''], 'O campo: Telefone residencial, deve ser preenchido quando o DDD estiver preenchido.'],
    'telefone sem DDD' => [fn () => ['ddd_telefone_2' => ''], 'O campo: DDD, deve ser preenchido quando o Celular estiver preenchido.'],
    'DDD só com zeros' => [fn () => ['ddd_telefone_mov' => '00'], 'O campo: DDD não pode conter apenas zeros.'],
    'fax só com zeros' => [fn () => ['telefone_fax' => '0000'], 'O campo: Fax não pode conter apenas zeros.'],
]);

test('com a instituição exigindo os campos do censo, recusa o CEP sem o município', function () {
    ($this->institution)(['obrigar_campos_censo' => true]);

    ($this->send)(['postal_code' => '88800-123', 'city_id' => ''])
        ->assertSee('O campo Município de residência deve ser preenchido quando o CEP for informado.');

    ($this->nothingStored)();

    ($this->send)(['postal_code' => '88800-123', 'city_id' => (string) CityFactory::new()->create()->getKey(), 'address' => 'Rua A', 'neighborhood' => 'Centro'])
        ->assertSee('window.opener.afterChangePessoa', false);
});

test('defeito: sem o nome a tela estoura em vez de recusar', function () {
    ($this->send)(['nm_pessoa' => ''])->assertStatus(500);
});

test('com a instituição exigindo telefone, recusa a pessoa sem telefone residencial nem celular', function () {
    ($this->institution)(['obrigar_telefone_pessoa' => true]);

    ($this->send)(['telefone_1' => '0', 'telefone_2' => '0'])
        ->assertSee('É necessário informar um Telefone residencial ou Celular.');

    ($this->nothingStored)();
});
