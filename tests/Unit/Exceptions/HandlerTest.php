<?php

use App\Exceptions\Handler;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

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
