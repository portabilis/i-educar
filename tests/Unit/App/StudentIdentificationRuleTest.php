<?php

use App\Rules\StudentIdentificationRule;

const CPF_VALIDO = '529.982.247-25';
const CPF_VALIDO_SEM_MASCARA = '52998224725';
const INEP_VALIDO = '123456789012';


function identificacaoPassa(array $dados, array $campos = []): bool
{
    return (new StudentIdentificationRule(...$campos))
        ->setData($dados)
        ->passes('cpf', $dados['cpf'] ?? null);
}

function camposDoFormulario(): array
{
    return ['cpfField' => 'id_federal', 'inepField' => 'aluno_inep_id', 'hasCpfField' => 'possui_cpf'];
}

test('falha quando nenhum identificador é informado', function (array $dados) {
    expect(identificacaoPassa($dados))->toBeFalse();
})->with([
    'sem os dois campos' => [[]],
    'os dois nulos' => [['cpf' => null, 'inep_id' => null]],
    'os dois vazios' => [['cpf' => '', 'inep_id' => '']],
    'os dois só com espaços' => [['cpf' => '   ', 'inep_id' => ' ']],
]);

test('passa com apenas o CPF válido', function (array $dados) {
    expect(identificacaoPassa($dados))->toBeTrue();
})->with([
    'CPF com máscara, INEP ausente' => [['cpf' => CPF_VALIDO]],
    'CPF sem máscara, INEP nulo' => [['cpf' => CPF_VALIDO_SEM_MASCARA, 'inep_id' => null]],
    'CPF com máscara, INEP vazio' => [['cpf' => CPF_VALIDO, 'inep_id' => '']],
]);

test('passa com apenas o código INEP válido', function (array $dados) {
    expect(identificacaoPassa($dados))->toBeTrue();
})->with([
    'INEP válido, CPF ausente' => [['inep_id' => INEP_VALIDO]],
    'INEP válido, CPF nulo' => [['cpf' => null, 'inep_id' => INEP_VALIDO]],
    'INEP válido, CPF vazio' => [['cpf' => '', 'inep_id' => INEP_VALIDO]],
]);

test('passa quando os dois são válidos', function () {
    expect(identificacaoPassa(['cpf' => CPF_VALIDO, 'inep_id' => INEP_VALIDO]))->toBeTrue();
});

test('falha quando o único identificador informado é um CPF inválido', function (string $cpf) {
    expect(identificacaoPassa(['cpf' => $cpf]))->toBeFalse();
})->with([
    'dígitos verificadores errados' => ['123.456.789-00'],
    'todos os dígitos iguais' => ['111.111.111-11'],
    'curto demais' => ['123.456.789'],
    'longo demais' => ['529.982.247-250'],
    'com letras' => ['529.982.247-2a'],
]);

test('falha quando o único identificador informado é um INEP inválido', function (string $inep) {
    expect(identificacaoPassa(['inep_id' => $inep]))->toBeFalse();
})->with([
    'onze dígitos' => ['12345678901'],
    'treze dígitos' => ['1234567890123'],
    'começa com zero' => ['012345678901'],
    'com letras' => ['12345678901a'],
    'com máscara' => ['1234-5678-9012'],
]);

test('um identificador válido basta, mesmo que o outro campo esteja inválido', function (array $dados) {
    expect(identificacaoPassa($dados))->toBeTrue();
})->with([
    'CPF inválido e INEP válido' => [['cpf' => '123.456.789-00', 'inep_id' => INEP_VALIDO]],
    'CPF válido e INEP inválido' => [['cpf' => CPF_VALIDO, 'inep_id' => '12345']],
]);

test('a regra lê os campos dos dados, não do valor recebido', function () {
    $regra = (new StudentIdentificationRule)->setData(['inep_id' => INEP_VALIDO]);

    expect($regra->passes('inep_id', null))->toBeTrue()
        ->and($regra->passes('cpf', ''))->toBeTrue();
});

test('a mensagem diz o que precisa ser informado', function () {
    expect((new StudentIdentificationRule)->message())
        ->toBe('Informe um CPF ou um código INEP válido para o aluno.');
});


test('lê os identificadores dos campos informados no construtor', function (array $dados, bool $esperado) {
    expect(identificacaoPassa($dados, camposDoFormulario()))->toBe($esperado);
})->with([
    'sem nenhum dos dois' => [[], false],
    'só id_federal válido' => [['id_federal' => CPF_VALIDO], true],
    'só aluno_inep_id válido' => [['aluno_inep_id' => INEP_VALIDO], true],
    'id_federal inválido sozinho' => [['id_federal' => '123.456.789-00'], false],
    'aluno_inep_id inválido sozinho' => [['aluno_inep_id' => '12345'], false],
]);

test('com nomes personalizados, ignora os campos de nome padrão', function () {
    $dados = ['cpf' => CPF_VALIDO, 'inep_id' => INEP_VALIDO];

    expect(identificacaoPassa($dados, camposDoFormulario()))->toBeFalse()
        ->and(identificacaoPassa($dados))->toBeTrue();
});

test('com "possui CPF" marcado, exige um CPF válido', function (mixed $possuiCpf) {
    $passa = fn (array $dados) => identificacaoPassa(
        $dados + ['possui_cpf' => $possuiCpf],
        camposDoFormulario()
    );

    expect($passa(['id_federal' => CPF_VALIDO]))->toBeTrue()
        ->and($passa(['id_federal' => CPF_VALIDO, 'aluno_inep_id' => INEP_VALIDO]))->toBeTrue()
        ->and($passa(['aluno_inep_id' => INEP_VALIDO]))->toBeFalse()
        ->and($passa(['id_federal' => '123.456.789-00', 'aluno_inep_id' => INEP_VALIDO]))->toBeFalse()
        ->and($passa([]))->toBeFalse();
})->with([
    'booleano true' => [true],
    'inteiro 1' => [1],
    'texto "1"' => ['1'],
]);

test('com "possui CPF" desmarcado, exige um INEP válido e ignora o CPF', function (mixed $possuiCpf) {
    $passa = fn (array $dados) => identificacaoPassa(
        $dados + ['possui_cpf' => $possuiCpf],
        camposDoFormulario()
    );

    expect($passa(['aluno_inep_id' => INEP_VALIDO]))->toBeTrue()
        ->and($passa(['aluno_inep_id' => INEP_VALIDO, 'id_federal' => CPF_VALIDO]))->toBeTrue()
        ->and($passa(['id_federal' => CPF_VALIDO]))->toBeFalse()
        ->and($passa(['aluno_inep_id' => '012345678901']))->toBeFalse()
        ->and($passa([]))->toBeFalse();
})->with([
    'booleano false' => [false],
    'inteiro 0' => [0],
    'texto "0"' => ['0'],
]);

test('sem um estado reconhecível de "possui CPF", basta um identificador válido', function (array $extra) {
    $passa = fn (array $dados) => identificacaoPassa($dados + $extra, camposDoFormulario());

    expect($passa(['id_federal' => CPF_VALIDO]))->toBeTrue()
        ->and($passa(['aluno_inep_id' => INEP_VALIDO]))->toBeTrue()
        ->and($passa([]))->toBeFalse();
})->with([
    'campo ausente' => [[]],
    'campo nulo' => [['possui_cpf' => null]],
    'campo vazio' => [['possui_cpf' => '']],
    'valor irreconhecível' => [['possui_cpf' => 'talvez']],
]);

test('o campo padrão do estado "possui CPF" é possui_cpf', function () {
    expect(identificacaoPassa(['inep_id' => INEP_VALIDO, 'possui_cpf' => 1]))->toBeFalse()
        ->and(identificacaoPassa(['cpf' => CPF_VALIDO, 'possui_cpf' => 0]))->toBeFalse()
        ->and(identificacaoPassa(['cpf' => CPF_VALIDO, 'possui_cpf' => 1]))->toBeTrue();
});

test('o nome do campo "possui CPF" também é configurável', function () {
    $campos = ['hasCpfField' => 'tem_cpf'];

    expect(identificacaoPassa(['inep_id' => INEP_VALIDO, 'tem_cpf' => 1], $campos))->toBeFalse()
        ->and(identificacaoPassa(['inep_id' => INEP_VALIDO, 'possui_cpf' => 1], $campos))->toBeTrue();
});

test('a mensagem acompanha o estado de "possui CPF"', function (array $dados, string $mensagem) {
    $regra = (new StudentIdentificationRule(...camposDoFormulario()))->setData($dados);

    expect($regra->message())->toBe($mensagem);
})->with([
    'marcado pede o CPF' => [['possui_cpf' => 1], 'Informe um CPF válido para o aluno.'],
    'desmarcado pede o INEP' => [['possui_cpf' => 0], 'Informe um código INEP válido para o aluno, pois ele não possui CPF.'],
    'sem estado pede um dos dois' => [[], 'Informe um CPF ou um código INEP válido para o aluno.'],
]);
