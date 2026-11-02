<?php

use App\Http\Requests\Api\UpdateStateRegistrationRequest;
use App\Models\LegacyStudent;
use App\Rules\StateRegistrationFormatRule;
use App\Rules\StateRegistrationUniqueRule;

test('CT01 [Statement Coverage]: executa a instrucao de retorno com sucesso no metodo passes', function () {
    $rule = new StateRegistrationFormatRule;

    $result = $rule->passes('state_registration_id', '123.456.789');

    expect((bool) $result)->toBeTrue();
});

test('CT02 [Statement Coverage]: executa a instrucao de retorno com falha no metodo passes', function () {
    $rule = new StateRegistrationFormatRule;

    $result = $rule->passes('state_registration_id', 'invalido');

    expect((bool) $result)->toBeFalse();
});

test('CT03 [Statement Coverage]: executa a instrucao de retorno do metodo message', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->message())->toBe('O número de inscrição é inválido.');
});

test('CT04 [Branch Coverage]: Ramo Verdadeiro do bloco principal da expressao regular', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '987.654.321'))->toBeTrue();
});

test('CT05 [Branch Coverage]: Ramo Falso do bloco principal da expressao regular', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '987654321'))->toBeFalse();
});

test('CT06 [Branch Coverage]: Ramo do subgrupo opcional com digito verificador presente e casado', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '987.654.321-8'))->toBeTrue();
});

test('CT07 [Branch Coverage]: Ramo do subgrupo opcional com digito verificador ausente', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '987.654.321'))->toBeTrue();
});

test('CT08 [Branch Coverage]: Ramo do subgrupo opcional onde o hifen existe mas o digito e invalido', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '987.654.321-X'))->toBeTrue();
});

test('CT09 [Condition Coverage]: condicao do 1o bloco de 3 digitos (Verdadeiro e Falso)', function (string $input, bool $expected) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', $input))->toBe($expected);
})->with([
    'C1 Verdadeira: 3 digitos' => ['123.456.789', true],
    'C1 Falsa: letras no 1o bloco' => ['12X.456.789', false],
    'C1 Falsa: menos de 3 digitos' => ['12.456.789', false],
]);

test('CT10 [Condition Coverage]: condicao do 1o separador ponto (Verdadeiro e Falso)', function (string $input, bool $expected) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', $input))->toBe($expected);
})->with([
    'C2 Verdadeira: ponto presente' => ['123.456.789', true],
    'C2 Falsa: traco em vez de ponto' => ['123-456.789', false],
    'C2 Falsa: barra em vez de ponto' => ['123/456.789', false],
]);

test('CT11 [Condition Coverage]: condicao do 2o bloco de 3 digitos (Verdadeiro e Falso)', function (string $input, bool $expected) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', $input))->toBe($expected);
})->with([
    'C3 Verdadeira: 3 digitos' => ['123.456.789', true],
    'C3 Falsa: letras no 2o bloco' => ['123.45X.789', false],
    'C3 Falsa: menos de 3 digitos' => ['123.45.789', false],
]);

test('CT12 [Condition Coverage]: condicao do 2o separador ponto (Verdadeiro e Falso)', function (string $input, bool $expected) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', $input))->toBe($expected);
})->with([
    'C4 Verdadeira: ponto presente' => ['123.456.789', true],
    'C4 Falsa: espaco em vez de ponto' => ['123.456 789', false],
    'C4 Falsa: traco em vez de ponto' => ['123.456-789', false],
]);

test('CT13 [Condition Coverage]: condicao do 3o bloco de 3 digitos (Verdadeiro e Falso)', function (string $input, bool $expected) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', $input))->toBe($expected);
})->with([
    'C5 Verdadeira: 3 digitos' => ['123.456.789', true],
    'C5 Falsa: letras no 3o bloco' => ['123.456.78X', false],
    'C5 Falsa: menos de 3 digitos' => ['123.456.78', false],
]);

test('CT14 [Basis Path 1]: Caminho de rejeicao imediata (falha de casamento global)', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', 'texto_invalido'))->toBeFalse();
});

test('CT15 [Basis Path 2]: Caminho basico sem DV (sucesso direto sem ramo opcional)', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '321.654.987'))->toBeTrue();
});

test('CT16 [Basis Path 3]: Caminho completo com DV (sucesso percorrendo ramo opcional)', function () {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration', '321.654.987-2'))->toBeTrue();
});

test('CT17 [Basis Path 4]: Caminho de excecao de tipo primitivo estrutural', function () {
    $rule = new StateRegistrationFormatRule;

    expect(fn () => $rule->passes('state_registration', ['array_invalido']))->toThrow(TypeError::class);
});

test('CT18 [FormRequest]: authorize retorna true incondicionalmente', function () {
    $request = new UpdateStateRegistrationRequest;

    expect($request->authorize())->toBeTrue();
});

test('CT19 [FormRequest]: rules retorna as regras com StateRegistrationFormatRule instanciada', function () {
    $student = Mockery::mock(LegacyStudent::class);
    $request = new UpdateStateRegistrationRequest;
    $request->student = $student;

    $rules = $request->rules();

    expect($rules)->toHaveKey('state_registration_id');
    expect($rules['state_registration_id'])->toHaveCount(2);
    expect($rules['state_registration_id'][0])->toBeInstanceOf(StateRegistrationFormatRule::class);
    expect($rules['state_registration_id'][1])->toBeInstanceOf(StateRegistrationUniqueRule::class);
});

test('CT20 [FormRequest]: getStateRegistration delega para input do atributo', function () {
    $request = new UpdateStateRegistrationRequest;
    $request->merge(['state_registration_id' => '123.456.789-0']);

    expect($request->getStateRegistration())->toBe('123.456.789-0');
});
