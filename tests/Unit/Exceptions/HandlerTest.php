<?php

namespace Tests\Unit\Exceptions;

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
