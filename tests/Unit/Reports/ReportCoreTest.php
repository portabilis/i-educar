<?php

use iEducar\Reports\BaseModifier;
use Tests\TestCase;

uses(TestCase::class);

class RelatorioDeTeste extends Portabilis_Report_ReportCore
{
    public function templateName()
    {
        return 'relatorio-de-teste';
    }

    public function requiredArgs()
    {
        $this->addRequiredArg('ano');
    }
}

class DobraOsValoresDoRelatorio extends BaseModifier
{
    public function modify($data)
    {
        return array_map(fn ($value) => $value * 2, $data) + ['template' => $this->templateName, 'ano' => $this->args['ano']];
    }
}

class FabricaDeRelatorioDeTeste extends Portabilis_Report_ReportFactory
{
    public function setSettings($config) {}

    public function dumps($report, $options = [])
    {
        return $report->templateName() . ':' . json_encode($options);
    }
}

test('guarda os argumentos com nome e exige os obrigatórios antes de gerar', function () {
    $report = new RelatorioDeTeste;

    expect($report->addArg('', 'ignorado'))->toBeFalse()
        ->and($report->requiredArgs)->toBe(['ano'])
        ->and(fn () => $report->dumps())->toThrow(Exception::class, "The required arg 'ano' wasn't set or is empty!");

    $report->addArg('ano', 2030);

    expect($report->dumps(['report_factory' => new FabricaDeRelatorioDeTeste, 'options' => ['a' => 1]]))->toBe('relatorio-de-teste:{"a":1}');
});

test('usa a fábrica configurada e avisa quando ela não existe', function () {
    config(['legacy.report.default_factory' => 'Portabilis_Report_ReportsRenderServerFactory']);

    expect((new RelatorioDeTeste)->reportFactory())->toBeInstanceOf(Portabilis_Report_ReportsRenderServerFactory::class);

    config(['legacy.report.default_factory' => '']);

    expect(fn () => (new RelatorioDeTeste)->reportFactory())->toThrow(CoreExt_Exception::class, 'No report.default_factory defined in configurations!');

    config(['legacy.report.default_factory' => 'Portabilis_Report_FabricaInexistente']);

    // As telas legadas rodam sem avisos do PHP; o include do arquivo que não existe só avisa.
    $level = error_reporting(0);

    try {
        expect(fn () => (new RelatorioDeTeste)->reportFactory())->toThrow(CoreExt_Exception::class, "Class 'Portabilis_Report_FabricaInexistente' not found");
    } finally {
        error_reporting($level);
    }
});

test('por padrão o relatório usa o banco, sem HTML, sem autenticação e sem dados em JSON', function () {
    $report = new RelatorioDeTeste;

    expect($report->useJson())->toBeFalse()
        ->and($report->useHtml())->toBeFalse()
        ->and($report->authenticate())->toBeFalse()
        ->and($report->getJsonQuery())->toBe('')
        ->and($report->getJsonData())->toBe([]);
});

test('aplica só os modificadores que estendem o modificador base', function () {
    $report = new RelatorioDeTeste;
    $report->addArg('ano', 2030);
    $report->modifiers = [stdClass::class, DobraOsValoresDoRelatorio::class];

    expect($report->modify(['nota' => 4]))->toBe(['nota' => 8, 'template' => 'relatorio-de-teste', 'ano' => 2030]);
});
