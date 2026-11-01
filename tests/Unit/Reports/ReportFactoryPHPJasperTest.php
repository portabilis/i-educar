<?php

use Tests\TestCase;

uses(TestCase::class);

class RelatorioJasper extends Portabilis_Report_ReportCore
{
    public function templateName()
    {
        return 'relatorio-jasper';
    }

    public function requiredArgs() {}
}

function fabricaJasper(?string $logo): Portabilis_Report_ReportFactoryPHPJasper
{
    config(['legacy.report.logo_file_name' => $logo, 'legacy.report.source_path' => sys_get_temp_dir() . '/']);

    return new Portabilis_Report_ReportFactoryPHPJasper;
}

test('sem logo configurado não há caminho do logo', function () {
    expect(fn () => fabricaJasper(null)->logoPath())->toThrow(Exception::class, 'No report.logo_file_name defined in configurations!');
});

test('o logo por URL é baixado uma vez para um arquivo temporário', function () {
    $origem = tempnam(sys_get_temp_dir(), 'logo');
    file_put_contents($origem, 'PNG');
    $url = 'file://' . $origem;
    $baixado = sys_get_temp_dir() . '/logo_' . hash('sha256', $url) . '.png';
    @unlink($baixado);

    try {
        expect(fabricaJasper($url)->logoPath())->toBe($baixado)
            ->and(file_get_contents($baixado))->toBe('PNG');

        unlink($origem);

        expect(fabricaJasper($url)->logoPath())->toBe($baixado);
    } finally {
        @unlink($baixado);
        @unlink($origem);
    }
});

test('o logo por URL que não responde vira erro', function () {
    $url = 'file:///caminho/que/nao/existe/logo.png';

    // As telas legadas rodam sem avisos do PHP; a leitura que falha só avisa.
    $level = error_reporting(0);

    try {
        expect(fn () => fabricaJasper($url)->logoPath())->toThrow(Exception::class, "Erro ao baixar logo da URL: {$url}");
    } finally {
        error_reporting($level);
    }
});

test('o logo pelo nome fica na pasta de logos dos relatórios', function () {
    $pasta = base_path('ieducar/modules/Reports/ReportLogos');
    $criouPasta = !is_dir($pasta) && mkdir($pasta, 0777, true);
    $logo = $pasta . '/logo-de-teste.png';
    file_put_contents($logo, 'PNG');

    try {
        expect(fabricaJasper('logo-de-teste.png')->logoPath())->toBe($logo)
            ->and(fn () => fabricaJasper('logo-que-nao-existe.png')->logoPath())->toThrow(CoreExt_Exception::class, "Report logo 'logo-que-nao-existe.png' not found");
    } finally {
        unlink($logo);

        if ($criouPasta) {
            rmdir($pasta);
            @rmdir(dirname($pasta));
        }
    }
});

test('os relatórios ficam na pasta configurada e o PDF temporário é apagado', function () {
    $fabrica = fabricaJasper('logo.png');
    $pdf = tempnam(sys_get_temp_dir(), 'pdf');

    $fabrica->destroyPDF($pdf);
    $fabrica->destroyPDF($pdf);

    expect($fabrica->getReportsPath())->toBe(sys_get_temp_dir() . '/')
        ->and(file_exists($pdf))->toBeFalse();
});

test('sem o JasperPHP instalado a geração para depois de preparar os argumentos', function () {
    $logo = tempnam(sys_get_temp_dir(), 'logo');
    $url = 'file://' . $logo;
    $report = new RelatorioJasper;
    $report->addArg('mostrar_assinatura', true);

    try {
        expect(fn () => fabricaJasper($url)->dumps($report))->toThrow(Error::class, 'JasperPHP')
            ->and($report->args)->toBe(['mostrar_assinatura' => 'true', 'logo' => sys_get_temp_dir() . '/logo_' . hash('sha256', $url) . '.png']);
    } finally {
        @unlink($logo);
        @unlink($report->args['logo'] ?? '');
    }
});
