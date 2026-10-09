<?php

namespace Tests\Unit\Exceptions;

use App_Model_Exception;
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
        $this->runningInConsole(false);

        $this->app->instance(Tracker::class, new class($this->notified) implements Tracker
        {
            public function __construct(private array &$notified) {}

            public function notify(Throwable $exception, array $data = [])
            {
                $this->notified[] = [$exception, $data];
            }
        });
    }

    private function runningInConsole(bool $console): void
    {
        (new ReflectionProperty($this->app, 'isRunningInConsole'))->setValue($this->app, $console);
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

    private function routeTo(string $uri, string $controller): Request
    {
        $request = Request::create('/' . $uri, 'GET', ['busca' => 'Ana']);
        $request->setRouteResolver(fn () => (new Route('GET', $uri, ['uses' => $controller, 'controller' => $controller]))->bind($request));

        return $request;
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

    public function test_nao_envia_com_o_rastreador_desligado_nem_excecao_que_nao_deve_ser_reportada()
    {
        $this->app->make(ExceptionHandler::class)->report(new App_Model_Exception('não reportável'));

        config(['app.trackerror' => false]);
        $this->app->make(ExceptionHandler::class)->report(new RuntimeException('desligado'));

        $this->assertSame([], $this->notified);
    }

    public function test_no_console_nao_envia_contexto_nem_rota()
    {
        $this->runningInConsole(true);

        $this->app->make(ExceptionHandler::class)->report(new RuntimeException('no console'));

        $this->assertSame(['context' => [], 'controller' => null, 'action' => null], $this->notified[0][1]);
    }

    public function test_na_rota_do_legado_o_controller_e_o_caminho_da_tela()
    {
        $data = $this->report($this->routeTo('intranet/educar_aluno_lst.php', 'App\Http\Controllers\LegacyController@intranet'));

        $this->assertSame(['context' => ['busca' => 'Ana'], 'controller' => 'intranet/educar_aluno_lst.php', 'action' => 'intranet'], $data);
    }

    public function test_controller_invocavel_usa_o_proprio_nome_como_acao()
    {
        $data = $this->report($this->routeTo('auth/callback', 'App\Http\Controllers\SocialiteCallbackController'));

        $this->assertSame('SocialiteCallbackController', $data['controller']);
        $this->assertSame('SocialiteCallbackController', $data['action']);
    }
}
