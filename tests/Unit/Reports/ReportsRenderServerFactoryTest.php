<?php

use iEducar\Reports\Contracts\ReportRenderContract;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

class RelatorioRenderizado extends Portabilis_Report_ReportCore
{
    public function __construct(private string $template, private string $fonte = 'banco')
    {
        parent::__construct();
    }

    public function templateName()
    {
        return $this->template;
    }

    public function requiredArgs() {}

    public function useJson()
    {
        return $this->fonte === 'json';
    }

    public function useHtml()
    {
        return $this->fonte === 'html';
    }

    public function getJsonData()
    {
        return ['main' => [['nome' => 'Ana']]];
    }

    public function getHtmlData()
    {
        return ['html' => '<p>Boletim</p>', 'main' => ['blade' => 'reports::boletim-escolar'], 'orientation' => 'landscape'];
    }
}

// Sobe uma única vez o servidor PHP embutido que faz o papel do serviço de renderização.
function servidorDeRelatorios(): string
{
    static $url;

    if ($url) {
        return $url;
    }

    $port = random_int(20000, 40000);
    $process = proc_open(
        [PHP_BINARY, '-S', "127.0.0.1:{$port}", __DIR__ . '/fixtures/servidor-de-relatorios.php'],
        [['pipe', 'r'], ['file', '/dev/null', 'w'], ['file', '/dev/null', 'w']],
        $pipes
    );

    register_shutdown_function(fn () => proc_terminate($process));

    foreach (range(1, 50) as $attempt) {
        if (@fsockopen('127.0.0.1', $port)) {
            break;
        }

        usleep(100_000);
    }

    return $url = "http://127.0.0.1:{$port}/deprecated";
}

function fabricaDeRelatorios(array $config = []): Portabilis_Report_ReportsRenderServerFactory
{
    config($config + [
        'legacy.report.remote_factory.url' => servidorDeRelatorios(),
        'legacy.report.remote_factory.token' => 'token-do-servidor',
        'legacy.report.source_path' => '/relatorios/deprecated/',
        'legacy.report.logo_file_name' => 'logo.png',
        'legacy.report.debug' => false,
    ]);

    return new Portabilis_Report_ReportsRenderServerFactory;
}

test('o relatório do banco envia a conexão, o logo, o fuso e o token e devolve o PDF', function () {
    $sent = json_decode(fabricaDeRelatorios()->dumps(new RelatorioRenderizado('sucesso')), true);

    expect($sent['report'])->toBe('sucesso')
        ->and($sent['url'])->toBe('/relatorios/deprecated/')
        ->and($sent['params'])->toMatchArray(['datasource' => 'database', 'connection' => 'postgresql', 'logo' => 'logo.png', 'timezone' => config('legacy.app.locale.timezone')])
        ->and($sent['params'])->toHaveKeys(['host', 'port', 'database', 'username', 'password'])
        ->and($sent['authorization'])->toBe('Token token-do-servidor');
});

test('o relatório em JSON manda os dados e usa o serviço novo', function () {
    $sent = json_decode(fabricaDeRelatorios()->dumps(new RelatorioRenderizado('sucesso', 'json'), ['add_logo_name_arg' => false]), true);

    expect($sent['url'])->toBe('/relatorios/')
        ->and($sent['data'])->toBe(['main' => [['nome' => 'Ana']]])
        ->and($sent['params']['datasource'])->toBe('json')
        ->and($sent['params'])->not->toHaveKey('logo');
});

test('as falhas do serviço viram exceção e ficam no log', function (string $template, string $message) {
    Log::spy();

    expect(fn () => fabricaDeRelatorios(['legacy.report.debug' => true])->dumps(new RelatorioRenderizado($template)))
        ->toThrow(Exception::class, $message);

    Log::shouldHaveReceived('error')->atLeast()->once();
})->with([
    'erro informado' => ['falha', 'Relatório indisponível'],
    'só a mensagem' => ['falha-sem-erro', 'Sem detalhes'],
    'resposta que não é JSON' => ['invalido', 'Não foi possível analisar a resposta do serviço.'],
]);

test('pedir o logo sem logo configurado é um erro', function () {
    expect(fn () => fabricaDeRelatorios(['legacy.report.logo_file_name' => null])->dumps(new RelatorioRenderizado('sucesso')))
        ->toThrow(Exception::class, "The option 'add_logo_name_arg' is true, but no logo_name defined in configurations!");
});

test('o relatório em HTML é renderizado pela aplicação e repassa a falha da renderização', function () {
    $renderer = new class implements ReportRenderContract
    {
        public array $payload = [];

        public bool $falha = false;

        public function render(array $data)
        {
            $this->payload = $data;

            if ($this->falha) {
                throw new RuntimeException('Sem template');
            }

            return new class
            {
                public function content(): string
                {
                    return 'PDF do boletim';
                }
            };
        }
    };
    $this->app->instance(ReportRenderContract::class, $renderer);

    expect(fabricaDeRelatorios()->dumps(new RelatorioRenderizado('boletim', 'html')))->toBe('PDF do boletim')
        ->and($renderer->payload)->toMatchArray(['html' => '<p>Boletim</p>', 'view' => 'reports::boletim', 'orientation' => 'landscape', 'driver' => null]);

    $renderer->falha = true;

    expect(fn () => fabricaDeRelatorios()->dumps(new RelatorioRenderizado('pacote::boletim', 'html')))->toThrow(RuntimeException::class, 'Sem template');
});
