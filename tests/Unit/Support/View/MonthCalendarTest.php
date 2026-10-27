<?php

use App\Support\View\MonthCalendar;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

test('generate form values', function () {
    $formValues = [
        'formFieldKey' => 'formFieldValue',
    ];

    $calendario = new MonthCalendar;

    // Teste sem permissão de troca de ano
    $html = $calendario->getCalendario(1, 2000, 'testGenerateFormValues', [], $formValues);

    $this->assertMatchesRegularExpression(
        '/<input id="cal_formFieldKey" name="formFieldKey" type="hidden" value="formFieldValue" \/>/',
        $html,
        '->getCalendario() gera campos extras de formulário.'
    );
});

test('largura estreita usa largura interna proporcional', function () {
    $calendario = new MonthCalendar;
    $calendario->setLargura(200);

    $this->assertSame('40%', $calendario->largura_interna);
});

test('legenda repetida nao e adicionada de novo e cor de um dia da semana', function () {
    $calendario = new MonthCalendar;
    $calendario->setLegendaPadrao('Padrão');
    $calendario->adicionarLegenda('Feriado', 'verde');
    $calendario->adicionarLegenda('Feriado', 'azul');
    $calendario->setCorDiaSemana(0, '#ffcccc');

    $this->assertSame(['Padrão', 'Feriado'], $calendario->array_legenda);
    $this->assertSame('#FFCCCC', $calendario->array_cor_dia_padrao['0']);
});

test('mes e ano mantidos na navegacao sem troca de ano', function () {
    $calendario = new MonthCalendar;
    $calendario->permite_trocar_ano = false;

    $dezembro = $calendario->getCalendario(1, 2000, 'cal', ['cal_mes' => 12, 'cal_ano' => 1999, 'outro' => 'turma=1']);
    $junho = $calendario->getCalendario(1, 2000, 'cal', 'turma=1', ['nome' => 'ignorado']);

    $this->assertStringContainsString('DEZEMBRO', $dezembro);
    $this->assertStringContainsString('2000', $dezembro);
    $this->assertStringContainsString('turma=1&', $dezembro);
    $this->assertStringContainsString('turma=1&', $junho);
    $this->assertStringNotContainsString('value="ignorado"', $junho);

    $calendario->permite_trocar_ano = true;
    $this->assertStringContainsString('1999', $calendario->getCalendario(1, 2000, 'cal', ['cal_ano' => 1999]));
    $this->assertStringContainsString('JUNHO', $calendario->getCalendario(6, 2000, 'cal', []));
    $calendario->permite_trocar_ano = false;
    $this->assertStringContainsString('JUNHO', $calendario->getCalendario(6, 2000, 'cal', []));
});

test('legenda de icones quebra a linha a cada tres', function () {
    $calendario = new MonthCalendar;
    $calendario->array_icone = [
        'A' => ['nome' => 'Anotações', 'link' => ''],
        'B' => ['nome' => 'Provas', 'link' => ''],
        'C' => ['nome' => 'Reuniões', 'link' => ''],
    ];
    $calendario->adicionarIconeDias([1], 'A');
    $calendario->adicionarIconeDias([2], 'B');
    $calendario->adicionarIconeDias([3], 'C');

    $html = $calendario->getCalendario(3, 2000, 'cal', []);

    $this->assertStringContainsString('Reuniões</td></tr><tr>', $html);
});
