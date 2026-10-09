<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use ReflectionProperty;
use RuntimeException;
use Tests\TestCase;
use Throwable;

class HandlerTest extends TestCase
{
    private array $notified = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.trackerror' => true]);

        // O handler só lê a rota fora do console, como numa requisição HTTP
        (new ReflectionProperty($this->app, 'isRunningInConsole'))->setValue($this->app, false);

        $this->app->instance(Tracker::class, new class($this->notified) implements Tracker
        {
            public function __construct(private array &$notified) {}

            public function notify(Throwable $exception, array $data = [])
            {
                $this->notified[] = [$exception, $data];
            }
        });
    }

    private function report(Request $request): array
    {
        $this->app->instance('request', $request);

        $exception = new RuntimeException('falha antes do roteamento');

        $this->app->make(ExceptionHandler::class)->report($exception);

        $this->assertCount(1, $this->notified);
        $this->assertSame($exception, $this->notified[0][0]);

        return $this->notified[0][1];
    }

    public function test_reports_exception_raised_before_the_route_is_resolved()
    {
        $data = $this->report(Request::create('/api/v2/notas', 'POST'));

        $this->assertSame('', $data['controller']);
        $this->assertSame('', $data['action']);
    }

    public function test_reports_exception_from_a_closure_route()
    {
        $request = Request::create('/closure');
        $request->setRouteResolver(fn () => new Route('GET', '/closure', fn () => 'ok'));

        $data = $this->report($request);

        $this->assertSame('', $data['controller']);
    }

    public function test_reports_controller_and_action_of_the_route()
    {
        $request = Request::create('/notas', 'POST');
        $request->setRouteResolver(fn () => new Route('POST', '/notas', ['controller' => 'App\Http\Controllers\NotasController@store']));

        $data = $this->report($request);

        $this->assertSame('NotasController', $data['controller']);
        $this->assertSame('store', $data['action']);
    }
}

uses(TestCase::class);

// Guarda o que o rastreador de erros recebeu de cada exceção reportada.
function rastreadorDeErros(): object
{
    $tracker = new class implements Tracker
    {
        public array $reports = [];

        public function notify(Throwable $exception, array $data = [])
        {
            $this->reports[] = [$exception->getMessage(), $data];
        }
    };

    app()->instance(Tracker::class, $tracker);

    return $tracker;
}

function reportaNaRota(string $uses, string $uri = 'intranet/educar_aluno_lst.php'): void
{
    $request = Request::create('/' . $uri, 'GET', ['busca' => 'Ana']);
    $request->setRouteResolver(fn () => (new Route('GET', $uri, ['uses' => $uses, 'controller' => $uses]))->bind($request));
    app()->instance('request', $request);

    app(Handler::class)->report(new RuntimeException($uses));
}

test('só envia ao rastreador quando está ligado e a exceção deve ser reportada', function () {
    $tracker = rastreadorDeErros();

    app(Handler::class)->report(new RuntimeException('desligado'));
    config(['app.trackerror' => true]);
    app(Handler::class)->report(new App_Model_Exception('não reportável'));
    app(Handler::class)->report(new RuntimeException('no console'));

    expect($tracker->reports)->toBe([
        ['no console', ['context' => [], 'controller' => null, 'action' => null]],
    ]);
});

test('na web envia os dados da requisição, o controller e a ação', function () {
    $tracker = rastreadorDeErros();
    config(['app.trackerror' => true]);
    $console = new ReflectionProperty(app(), 'isRunningInConsole');
    $console->setValue(app(), false);

    try {
        reportaNaRota('App\Http\Controllers\LegacyController@intranet');
        reportaNaRota('App\Http\Controllers\AccessLevelController@show', 'usuarios/tipos/1');
        reportaNaRota('App\Http\Controllers\SocialiteCallbackController', 'auth/callback');
    } finally {
        $console->setValue(app(), true);
    }

    expect(array_column($tracker->reports, 1))->toBe([
        ['context' => ['busca' => 'Ana'], 'controller' => 'intranet/educar_aluno_lst.php', 'action' => 'intranet'],
        ['context' => ['busca' => 'Ana'], 'controller' => 'AccessLevelController', 'action' => 'show'],
        ['context' => ['busca' => 'Ana'], 'controller' => 'SocialiteCallbackController', 'action' => 'SocialiteCallbackController'],
    ]);
});
