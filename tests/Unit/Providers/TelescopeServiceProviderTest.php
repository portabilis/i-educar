<?php

use App\Providers\TelescopeServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $telescope = new ReflectionClass(Telescope::class);
    $this->statics = collect(['filterUsing', 'hiddenRequestParameters', 'hiddenRequestHeaders'])
        ->mapWithKeys(fn ($name) => [$name => $telescope->getStaticPropertyValue($name)]);
});

afterEach(function () {
    foreach ($this->statics as $name => $value) {
        (new ReflectionClass(Telescope::class))->setStaticPropertyValue($name, $value);
    }
});

// Registra o provider no ambiente informado e devolve o filtro que ele adicionou.
function filtroDoTelescope(string $environment): Closure
{
    app()->instance('env', $environment);
    (new TelescopeServiceProvider(app()))->register();

    return last((new ReflectionClass(Telescope::class))->getStaticPropertyValue('filterUsing'));
}

test('fora do ambiente local esconde dados sensíveis e só grava falhas e tarefas agendadas', function () {
    $filter = filtroDoTelescope('production');
    $hidden = new ReflectionClass(Telescope::class);

    expect($hidden->getStaticPropertyValue('hiddenRequestParameters'))->toContain('_token')
        ->and($hidden->getStaticPropertyValue('hiddenRequestHeaders'))->toContain('cookie', 'x-csrf-token', 'x-xsrf-token')
        ->and($filter(IncomingEntry::make([])->type(EntryType::REQUEST)))->toBeFalse()
        ->and($filter(IncomingEntry::make([])->type(EntryType::SCHEDULED_TASK)))->toBeTrue();
});

test('no ambiente local grava tudo e não esconde nada', function () {
    $before = (new ReflectionClass(Telescope::class))->getStaticPropertyValue('hiddenRequestHeaders');
    $filter = filtroDoTelescope('local');

    expect($filter(IncomingEntry::make([])->type(EntryType::REQUEST)))->toBeTrue()
        ->and((new ReflectionClass(Telescope::class))->getStaticPropertyValue('hiddenRequestHeaders'))->toBe($before);
});

test('o portão do Telescope não libera nenhum e-mail', function () {
    (fn () => $this->gate())->call(new TelescopeServiceProvider(app()));

    expect(Gate::abilities()['viewTelescope']((object) ['email' => 'alguem@exemplo.test']))->toBeFalse();
});
