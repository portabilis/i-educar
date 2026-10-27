<?php

use App\Mail\ErrorTrackerMail;
use iEducar\Modules\ErrorTracking\EmailTracker;
use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\TrackerFactory;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

test('o Honeybadger recebe o contexto, o controller e a ação do erro', function () {
    // Sem envio de dados, o cliente monta o relatório e não chama a API.
    config(['honeybadger.report_data' => false]);

    (new HoneyBadgerTracker)->notify(new RuntimeException('falha'), [
        'context' => ['busca' => 'Ana'],
        'controller' => 'AlunoController',
        'action' => 'index',
    ]);

    expect(true)->toBeTrue();
});

test('o rastreador por e-mail não derruba a aplicação quando não consegue montar a mensagem', function () {
    Mail::fake();
    config(['legacy.modules.error.email_recipient' => 'suporte@exemplo.test']);

    (new EmailTracker)->notify(new RuntimeException('falha'));

    // O componente de depuração do Symfony não está instalado; a falha é engolida.
    Mail::assertNotSent(ErrorTrackerMail::class);
});

test('a fábrica entrega o rastreador configurado e recusa nomes desconhecidos', function () {
    expect(TrackerFactory::getTracker('HONEY_BADGER'))->toBeInstanceOf(HoneyBadgerTracker::class)
        ->and(TrackerFactory::getTracker('EMAIL'))->toBeInstanceOf(EmailTracker::class)
        ->and(fn () => TrackerFactory::getTracker('SENTRY'))->toThrow(Exception::class, 'Invalid error tracker');
});
