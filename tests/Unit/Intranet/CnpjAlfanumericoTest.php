<?php

describe('validaCNPJ', function () {
    test('aceita o CNPJ alfanumérico oficial da Receita (com e sem máscara)', function () {
        expect(validaCNPJ('12.ABC.345/01DE-35'))->toBeTrue();
        expect(validaCNPJ('12ABC34501DE35'))->toBeTrue();
    });

    test('aceita CNPJ alfanumérico digitado em minúsculo', function () {
        expect(validaCNPJ('12abc34501de35'))->toBeTrue();
    });

    test('mantém compatibilidade com CNPJ numérico existente', function () {
        expect(validaCNPJ('11.222.333/0001-81'))->toBeTrue();
        expect(validaCNPJ('11222333000181'))->toBeTrue();
        expect(validaCNPJ('60701190000104'))->toBeTrue();
    });

    test('aceita raiz totalmente alfabética e dígito verificador zero', function () {
        expect(validaCNPJ('ZZZZZZZZZZZZ62'))->toBeTrue();
        expect(validaCNPJ('ABCDEFGHIJKL80'))->toBeTrue();
        expect(validaCNPJ('00000000001406'))->toBeTrue();
    });

    test('rejeita dígito verificador incorreto', function () {
        expect(validaCNPJ('12ABC34501DE34'))->toBeFalse();
        expect(validaCNPJ('12ABC34501DE36'))->toBeFalse();
        expect(validaCNPJ('12ABC34501DE-53'))->toBeFalse();
    });

    test('tolera máscara incompleta, espaços e caracteres inválidos quando o conteúdo é íntegro', function () {
        expect(validaCNPJ('12ABC.345/01DE-35'))->toBeTrue();
        expect(validaCNPJ(' 12.ABC.345/01DE-35 '))->toBeTrue();
        expect(validaCNPJ('12@ABC.345/01DE-35'))->toBeTrue();
    });

    test('rejeita digitação incompleta cujo preenchimento com zeros não fecha o dígito verificador', function () {
        expect(validaCNPJ('222333000181'))->toBeFalse();
        expect(validaCNPJ('1234'))->toBeFalse();
        expect(validaCNPJ('123456789012345'))->toBeFalse();
    });

    test('rejeita letra nos dígitos verificadores', function () {
        expect(validaCNPJ('12ABC34501DED5'))->toBeFalse();
    });

    test('rejeita sequência repetida e valor vazio', function () {
        expect(validaCNPJ('00000000000000'))->toBeFalse();
        expect(validaCNPJ(''))->toBeFalse();
        expect(validaCNPJ(null))->toBeFalse();
        expect(validaCNPJ('0'))->toBeFalse();
        expect(validaCNPJ('   '))->toBeFalse();
    });
});

describe('validaDigitosCNPJ', function () {
    test('calcula o dígito verificador do exemplo oficial via valor ASCII', function () {
        expect(validaDigitosCNPJ('12ABC34501DE35'))->toBeTrue();
        expect(validaDigitosCNPJ('12ABC34501DE34'))->toBeFalse();
    });

    test('rejeita tamanho diferente de 14 e aceita entrada em minúsculo', function () {
        expect(validaDigitosCNPJ('123'))->toBeFalse();
        expect(validaDigitosCNPJ(''))->toBeFalse();
        expect(validaDigitosCNPJ('12abc34501de35'))->toBeTrue();
    });
});

describe('normalizaCnpj', function () {
    test('canoniza para 14 posições em maiúsculo, sem máscara', function () {
        expect(normalizaCnpj('12.abc.345/01de-35'))->toBe('12ABC34501DE35');
    });

    test('preenche zeros à esquerda sem perder posições', function () {
        expect(normalizaCnpj('222333000181'))->toBe('00222333000181');
    });

    test('retorna null para vazio (nunca gera o sentinela de zeros)', function () {
        expect(normalizaCnpj(''))->toBeNull();
        expect(normalizaCnpj(null))->toBeNull();
    });

    test('retorna null quando o resultado passa de 14 posições', function () {
        expect(normalizaCnpj('123456789012345'))->toBeNull();
    });

    test('nunca gera o sentinela de zeros a partir de máscara/zeros', function () {
        expect(normalizaCnpj('.-/'))->toBeNull();
        expect(normalizaCnpj(' '))->toBeNull();
        expect(normalizaCnpj('   '))->toBeNull();
        expect(normalizaCnpj('0'))->toBeNull();
        expect(normalizaCnpj('00'))->toBeNull();
        expect(normalizaCnpj('00000000000000'))->toBeNull();
    });
});

describe('limpaCnpj', function () {
    test('remove máscara e converte em maiúsculo sem preencher zeros', function () {
        expect(limpaCnpj('ab.c'))->toBe('ABC');
        expect(limpaCnpj('0012'))->toBe('0012');
    });
});

describe('int2CNPJ', function () {
    test('aplica a máscara preservando as letras', function () {
        expect(int2CNPJ('12ABC34501DE35'))->toBe('12.ABC.345/01DE-35');
        expect(int2CNPJ('222333000181'))->toBe('00.222.333/0001-81');
    });
});

describe('regressão CPF (helpers compartilhados não podem quebrar)', function () {
    test('validaCPF e int2CPF continuam corretos', function () {
        expect(validaCPF('529.982.247-25'))->toBeTrue();
        expect(validaCPF('11111111111'))->toBeFalse();
        expect(int2CPF('52998224725'))->toBe('529.982.247-25');
    });
});
