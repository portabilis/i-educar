<?php

use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->notified = [];

    $this->runningInConsole = function (bool $console) {
        (new ReflectionProperty($this->app, 'isRunningInConsole'))->setValue($this->app, $console);
    };

    $this->report = function (Request $request): array {
        $this->app->instance('request', $request);

        $exception = new RuntimeException('falha antes do roteamento');

        $this->app->make(ExceptionHandler::class)->report($exception);

        $this->assertCount(1, $this->notified);
        $this->assertSame($exception, $this->notified[0][0]);

        return $this->notified[0][1];
    };

    $this->routeTo = function (string $uri, string $controller): Request {
        $request = Request::create('/' . $uri, 'GET', ['busca' => 'Ana']);
        $request->setRouteResolver(fn () => (new Route('GET', $uri, ['uses' => $controller, 'controller' => $controller]))->bind($request));

        return $request;
    };

    config(['app.trackerror' => true]);

    // O handler só lê a rota fora do console, como numa requisição HTTP
    ($this->runningInConsole)(false);

    $this->app->instance(Tracker::class, new class($this->notified) implements Tracker
    {
        public function __construct(private array &$notified) {}

        public function notify(Throwable $exception, array $data = [])
        {
            $this->notified[] = [$exception, $data];
        }
    });
});

test('reports exception raised before the route is resolved', function () {
    $data = ($this->report)(Request::create('/api/v2/notas', 'POST'));

    $this->assertSame('', $data['controller']);
    $this->assertSame('', $data['action']);
});

test('reports exception from a closure route', function () {
    $request = Request::create('/closure');
    $request->setRouteResolver(fn () => new Route('GET', '/closure', fn () => 'ok'));

    $data = ($this->report)($request);

    $this->assertSame('', $data['controller']);
});

test('reports controller and action of the route', function () {
    $request = Request::create('/notas', 'POST');
    $request->setRouteResolver(fn () => new Route('POST', '/notas', ['controller' => 'App\Http\Controllers\NotasController@store']));

    $data = ($this->report)($request);

    $this->assertSame('NotasController', $data['controller']);
    $this->assertSame('store', $data['action']);
});

test('nao envia com o rastreador desligado nem excecao que nao deve ser reportada', function () {
    $this->app->make(ExceptionHandler::class)->report(new App_Model_Exception('não reportável'));

    config(['app.trackerror' => false]);
    $this->app->make(ExceptionHandler::class)->report(new RuntimeException('desligado'));

    $this->assertSame([], $this->notified);
});

test('no console nao envia contexto nem rota', function () {
    ($this->runningInConsole)(true);

    $this->app->make(ExceptionHandler::class)->report(new RuntimeException('no console'));

    $this->assertSame(['context' => [], 'controller' => null, 'action' => null], $this->notified[0][1]);
});

test('na rota do legado o controller e o caminho da tela', function () {
    $data = ($this->report)(($this->routeTo)('intranet/educar_aluno_lst.php', 'App\Http\Controllers\LegacyController@intranet'));

    $this->assertSame(['context' => ['busca' => 'Ana'], 'controller' => 'intranet/educar_aluno_lst.php', 'action' => 'intranet'], $data);
});

test('controller invocavel usa o proprio nome como acao', function () {
    $data = ($this->report)(($this->routeTo)('auth/callback', 'App\Http\Controllers\SocialiteCallbackController'));

    $this->assertSame('SocialiteCallbackController', $data['controller']);
    $this->assertSame('SocialiteCallbackController', $data['action']);
});
