<?php

use App\Http\Middleware\ConnectTenantDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'app.default_host' => 'ieducar.test',
        'database.connections.cliente' => config('database.connections.pgsql'),
    ]);
    $this->middleware = new ConnectTenantDatabase;
    $this->next = fn () => DB::getDefaultConnection();
});

afterEach(function () {
    DB::setDefaultConnection('pgsql');
    (new ReflectionProperty(ConnectTenantDatabase::class, 'resolver'))->setValue(null, null);
});

test('usa a conexão do cliente do endereço acessado', function () {
    expect($this->middleware->handle(Request::create('https://cliente.ieducar.test/intranet'), $this->next))->toBe('cliente');
});

test('com vários clientes, endereço sem cliente responde 404', function () {
    config(['app.multi_tenant' => true]);

    expect(fn () => $this->middleware->handle(Request::create('https://desconhecido.ieducar.test/'), $this->next))
        ->toThrow(NotFoundHttpException::class);
});

test('um resolvedor próprio decide o cliente da requisição', function () {
    expect(ConnectTenantDatabase::setTenantResolver(fn () => 'cliente'))->toBeTrue()
        ->and($this->middleware->getTenant(Request::create('https://qualquer.test/')))->toBe('cliente');
});
