<?php

use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\LegacyFields;

/**
 * As recusas do formulário de escola que valem sempre, com ou sem a exigência
 * dos campos do Censo. Cada caso parte do formulário válido de uma escola
 * existente, estraga um ponto e confere a mensagem e que nada foi gravado.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'sigla' => 'EALFA',
    ]);
    SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true]);

    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();

    $this->refuse = function (array $overrides, string $message, array $without = []) {
        $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + ['sigla' => 'MUDOU']);

        $this->post($this->url, array_diff_key($payload, array_flip($without)))
            ->assertOk()
            ->assertSee($message, false);

        expect($this->school->refresh()->sigla)->toBe('EALFA');
    };
});

test('recusa complemento com caractere não permitido', function () {
    ($this->refuse)(
        ['complement' => 'Sala @ 2'],
        'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres:',
    );
});

test('recusa CNPJ inválido da mantenedora de escola privada', function () {
    ($this->refuse)(
        ['dependencia_administrativa' => 4, 'cnpj_mantenedora_principal' => '11.111.111/1111-11'],
        'O CNPJ da mantenedora principal é inválido. Favor verificar.',
    );
});

test('recusa código INEP fora do formato', function (string $inep, string $message) {
    ($this->refuse)(['escola_inep_id' => $inep], $message);
})->with([
    'começando com zero' => ['01234567', 'O campo: Código INEP não pode iniciar com 0.'],
    'com menos de oito dígitos' => ['1234567', 'O campo: Código INEP deve conter 8 dígitos.'],
]);

test('recusa telefone inconsistente', function (array $phones, string $message) {
    ($this->refuse)($phones, $message);
})->with([
    'DDD sem o telefone 1' => [['p_ddd_telefone_1' => '48', 'p_telefone_1' => ''], 'O campo: Telefone 1, deve ser preenchido quando o DDD estiver preenchido.'],
    'telefone 1 sem o DDD' => [['p_ddd_telefone_1' => '', 'p_telefone_1' => '34221001'], 'O campo: DDD, deve ser preenchido quando o Telefone 1 estiver preenchido.'],
    'DDD que não existe' => [['p_ddd_telefone_1' => '10', 'p_telefone_1' => '34221001'], 'O campo: DDD, possui um valor inválido'],
    'DDD com um dígito' => [['p_ddd_telefone_1' => '4', 'p_telefone_1' => '34221001'], 'O campo: DDD, possui um valor inválido'],
    'telefone 1 curto' => [['p_ddd_telefone_1' => '48', 'p_telefone_1' => '3422100'], 'O campo: Telefone 1 deve possuir de 8 a 9 números'],
    'telefone 1 de nove dígitos sem começar por 9' => [['p_ddd_telefone_1' => '48', 'p_telefone_1' => '834221001'], 'No campo: Telefone 1 o primeiro dígito deve ser o número 9'],
    'telefone 1 com todos os dígitos iguais' => [['p_ddd_telefone_1' => '48', 'p_telefone_1' => '33333333'], 'Os números do campo: Telefone 1 não podem ser todos repetidos'],
    'DDD sem o telefone 2' => [['p_ddd_telefone_2' => '48', 'p_telefone_2' => ''], 'O campo: Telefone 2, deve ser preenchido quando o DDD estiver preenchido.'],
    'telefone 2 curto' => [['p_ddd_telefone_2' => '48', 'p_telefone_2' => '3422100'], 'O campo: Telefone 2 deve possuir de 8 a 9 números'],
    'celular sem o DDD' => [['p_ddd_telefone_mov' => '', 'p_telefone_mov' => '991221003'], 'O campo: DDD, deve ser preenchido quando o Celular estiver preenchido.'],
    'DDD sem o fax' => [['p_ddd_telefone_fax' => '48', 'p_telefone_fax' => ''], 'O campo: Fax, deve ser preenchido quando o DDD estiver preenchido.'],
    'telefone 2 igual ao telefone 1' => [
        ['p_ddd_telefone_1' => '48', 'p_telefone_1' => '34221001', 'p_ddd_telefone_2' => '48', 'p_telefone_2' => '34221001'],
        'O campo: Telefone 2 não pode ter o mesmo valor do campo: Telefone 1',
    ],
]);

test('exige alguma dependência quando a escola diz que as possui', function () {
    ($this->refuse)(
        ['possui_dependencias' => 1],
        'Preencha pelo menos um dos campos de Salas gerais à Áreas externas',
    );
});

test('exige ao menos uma quantidade de profissionais quando a escola diz ter funcionários', function () {
    ($this->refuse)(
        [],
        'Preencha pelo menos um dos campos <b>da seção Quantidade de profissionais</b> da aba Recursos.',
        without: ['nao_ha_funcionarios_para_funcoes'],
    );
});

test('recusa quantidade de profissionais igual a zero', function () {
    ($this->refuse)(
        ['qtd_secretario_escolar' => '0'],
        'não pode ser preenchido com 0',
        without: ['nao_ha_funcionarios_para_funcoes'],
    );
});

test('recusa código INEP inválido de escola que compartilha o prédio', function () {
    ($this->refuse)(
        ['codigo_inep_escola_compartilhada2' => '01234567'],
        'O campo: Código da escola que compartilha o prédio 2 não pode iniciar com 0.',
    );
});

test('recusa a opção exclusiva de um campo de múltipla escolha junto com outra', function (string $field, array $values, string $label) {
    ($this->refuse)(
        [$field => $values],
        "Não é possível informar mais de uma opção no campo: <b>{$label}",
    );
})->with([
    'parceria com o poder público' => ['poder_publico_parceria_convenio', [3, 1], 'Poder público responsável pela parceria ou convênio'],
    'abastecimento de água' => ['abastecimento_agua', [5, 1], 'Abastecimento de água'],
    'fonte de energia' => ['abastecimento_energia', [4, 1], 'Fonte de energia elétrica'],
    'esgoto' => ['esgoto_sanitario', [3, 1], 'Esgotamento sanitário'],
    'tratamento do lixo' => ['tratamento_lixo', [1, 2], 'Tratamento do lixo/resíduos que a escola realiza'],
    'acessibilidade' => ['recursos_acessibilidade', [1, 2], 'Recursos de acessibilidade'],
    'equipamentos' => ['equipamentos', [7, 1], 'Equipamentos da escola'],
    'acesso à internet' => ['uso_internet', [1, 2], 'Acesso à internet'],
    'órgãos colegiados' => ['orgaos_colegiados', [6, 2], 'Órgãos colegiados em funcionamento na escola'],
    'cotas' => ['reserva_vagas_cotas', [1, 2], 'Reserva de vagas por sistema de cotas para grupos específicos de alunos(as)'],
    'instrumentos pedagógicos' => ['instrumentos_pedagogicos', [12, 1], 'Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola'],
    'educação ambiental' => ['acoes_area_ambiental', [1, 2], 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola'],
]);

test('aceita a opção exclusiva quando ela vem sozinha', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['abastecimento_agua' => [5]]);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh()->abastecimento_agua)->toBe('{5}');
});
