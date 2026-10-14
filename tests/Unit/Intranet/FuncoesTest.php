<?php

describe('formatDecimalBr', function () {
    test('formats decimal with default 2 decimals', function () {
        expect(formatDecimalBr(689.2))->toBe('689,20');
        expect(formatDecimalBr(833.33))->toBe('833,33');
        expect(formatDecimalBr(800))->toBe('800,00');
        expect(formatDecimalBr(0))->toBe('0,00');
    });

    test('formats values above one thousand with thousands separator', function () {
        expect(formatDecimalBr(1000.5))->toBe('1.000,50');
        expect(formatDecimalBr(1500))->toBe('1.500,00');
        expect(formatDecimalBr(1234567.89))->toBe('1.234.567,89');
    });

    test('accepts numeric strings', function () {
        expect(formatDecimalBr('689.2'))->toBe('689,20');
        expect(formatDecimalBr('1000'))->toBe('1.000,00');
    });

    test('respects custom decimal places', function () {
        expect(formatDecimalBr(689.234, 3))->toBe('689,234');
        expect(formatDecimalBr(689, 0))->toBe('689');
        expect(formatDecimalBr(689.5, 1))->toBe('689,5');
    });

    test('returns null for invalid input', function () {
        expect(formatDecimalBr('abc'))->toBeNull();
        expect(formatDecimalBr(null))->toBeNull();
        expect(formatDecimalBr(''))->toBeNull();
    });
});

describe('parseDecimalBr', function () {
    test('parses Brazilian format with comma as decimal', function () {
        expect(parseDecimalBr('689,20'))->toBe('689.20');
        expect(parseDecimalBr('833,33'))->toBe('833.33');
    });

    test('parses Brazilian format with thousands separator', function () {
        expect(parseDecimalBr('1.000,50'))->toBe('1000.50');
        expect(parseDecimalBr('1.234.567,89'))->toBe('1234567.89');
    });

    test('accepts ISO format unchanged', function () {
        expect(parseDecimalBr('689.2'))->toBe('689.2');
        expect(parseDecimalBr('1000.5'))->toBe('1000.5');
    });

    test('accepts plain integers', function () {
        expect(parseDecimalBr('800'))->toBe('800');
        expect(parseDecimalBr(800))->toBe('800');
    });

    test('trims whitespace', function () {
        expect(parseDecimalBr('  689,20  '))->toBe('689.20');
    });

    test('returns null for invalid input', function () {
        expect(parseDecimalBr(null))->toBeNull();
        expect(parseDecimalBr(''))->toBeNull();
        expect(parseDecimalBr('abc'))->toBeNull();
        expect(parseDecimalBr('12.34.56'))->toBeNull();
        expect(parseDecimalBr('689,20,30'))->toBeNull();
    });
});

describe('transformDBArrayInString', function () {
    test('converts array to Postgres array literal', function () {
        expect(transformDBArrayInString(['1', '2']))->toBe('{1,2}');
        expect(transformDBArrayInString([12]))->toBe('{12}');
    });

    test('converts empty array to empty Postgres array', function () {
        expect(transformDBArrayInString([]))->toBe('{}');
    });

    test('discards empty items', function () {
        expect(transformDBArrayInString(['1', '', '2']))->toBe('{1,2}');
    });

    test('returns null when value is not an array', function () {
        expect(transformDBArrayInString(null))->toBeNull();
        expect(transformDBArrayInString('{1,2}'))->toBeNull();
        expect(transformDBArrayInString(12))->toBeNull();
    });
});

describe('transformStringFromDBInArray', function () {
    test('converts Postgres array literal to array', function () {
        expect(transformStringFromDBInArray('{1,2}'))->toBe(['1', '2']);
        expect(transformStringFromDBInArray('{12}'))->toBe(['12']);
    });

    test('converts empty Postgres array to array with one empty item', function () {
        expect(transformStringFromDBInArray('{}'))->toBe(['']);
    });

    test('returns null when value is not a string', function () {
        expect(transformStringFromDBInArray(null))->toBeNull();
        expect(transformStringFromDBInArray(['1', '2']))->toBeNull();
        expect(transformStringFromDBInArray(12))->toBeNull();
    });
});

describe('validaCPF', function () {
    test('recusa o CPF com dígito verificador errado', function () {
        expect(validaCPF('52998224725'))->toBeTrue()
            ->and(validaCPF('52998224726'))->toBeFalse();
    });
});

describe('dataToBrasil', function () {
    test('mantém hora e minuto, ou hora, minuto e segundo, quando pedido', function () {
        expect(dataToBrasil('2030-02-10 08:30:45', true))->toBe('10/02/2030 08:30')
            ->and(dataToBrasil('2030-02-10 08:30:45', false, true))->toBe('10/02/2030 08:30:45');
    });
});

describe('dataToBanco', function () {
    test('converte a data com o início ou o fim do dia e recusa vazio', function () {
        expect(dataToBanco('10/02/2030', true))->toBe('2030-02-10 00:00:00')
            ->and(dataToBanco('10/02/2030', false))->toBe('2030-02-10 23:59:59')
            ->and(dataToBanco(''))->toBeFalse();
    });
});

describe('extendChars', function () {
    test('troca acentos por entidades e desfaz a troca', function () {
        expect(extendChars('Ação'))->toBe('A&ccedil;&atilde;o')
            ->and(extendChars('A&ccedil;&atilde;o', true))->toBe('Ação');
    });
});

describe('int2IdFederal', function () {
    test('formata CPF e CNPJ completando com zeros à esquerda', function () {
        expect(int2IdFederal(52998224725))->toBe('529.982.247-25')
            ->and(int2IdFederal(1234567890))->toBe('012.345.678-90')
            ->and(int2IdFederal(11222333000181))->toBe('11.222.333/0001-81')
            ->and(int2IdFederal(1222333000181))->toBe('01.222.333/0001-81');
    });
});

describe('isEmptyDbArray', function () {
    test('reconhece o array vazio do banco', function () {
        expect(isEmptyDbArray('{}'))->toBeTrue()
            ->and(isEmptyDbArray('{1,2}'))->toBeFalse();
    });
});
