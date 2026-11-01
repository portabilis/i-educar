<?php

use App\Listeners\LoginLegacySession;
use App\Models\Notification;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\NotificationFactory;
use Database\Factories\NotificationTypeFactory;
use iEducar\Modules\Notifications\Status;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->type = NotificationTypeFactory::new()->create();
    $this->notification = fn (array $attributes = []) => NotificationFactory::new()->create($attributes + ['user_id' => $this->user->getKey(), 'type_id' => $this->type]);
    $this->ids = fn ($response) => $response->viewData('notifications')->pluck('id')->all();
});

test('a listagem filtra por tipo, situação e texto, só do usuário', function () {
    $read = ($this->notification)(['text' => 'Relatório pronto', 'created_at' => now()->subMinute()]);
    $unread = ($this->notification)(['text' => 'Exportação concluída', 'read_at' => null, 'type_id' => NotificationTypeFactory::new()->create()]);
    NotificationFactory::new()->create(['user_id' => LegacyUserFactory::new()->create()->getKey()]);

    expect(($this->ids)($this->get(route('notifications.index'))->assertOk()))->toBe([$unread->getKey(), $read->getKey()])
        ->and(($this->ids)($this->get(route('notifications.index', ['type' => $this->type->getKey()]))))->toBe([$read->getKey()])
        ->and(($this->ids)($this->get(route('notifications.index', ['status' => Status::READ]))))->toBe([$read->getKey()])
        ->and(($this->ids)($this->get(route('notifications.index', ['status' => Status::UNREAD]))))->toBe([$unread->getKey()])
        ->and(($this->ids)($this->get(route('notifications.index', ['text' => 'export']))))->toBe([$unread->getKey()]);
});

test('marca como lidas as notificações escolhidas ou todas', function () {
    $first = ($this->notification)(['read_at' => null]);
    $second = ($this->notification)(['read_at' => null]);
    $other = NotificationFactory::new()->create(['user_id' => LegacyUserFactory::new()->create()->getKey(), 'read_at' => null]);

    $this->post(route('notifications.mark-as-read'), ['notifications' => [$first->getKey(), $other->getKey()]])->assertOk();

    expect($first->refresh()->read_at)->not->toBeNull()
        ->and($second->refresh()->read_at)->toBeNull()
        ->and($other->refresh()->read_at)->toBeNull();

    $this->post(route('notifications.mark-all-read'))->assertOk();

    expect($second->refresh()->read_at)->not->toBeNull()
        ->and($other->refresh()->read_at)->toBeNull();
});

test('devolve a quantidade não lida e as últimas notificações, mais delas quando há muitas não lidas', function () {
    foreach (range(1, 6) as $i) {
        ($this->notification)(['read_at' => null, 'link' => "https://exemplo.test/{$i}"]);
    }

    $this->get(route('notifications.get-not-read-count'))->assertOk()->assertSee('6');

    $this->getJson(route('notifications.get-by-logged-user'))
        ->assertOk()
        ->assertJsonCount(6)
        ->assertJsonFragment(['link' => 'https://exemplo.test/6']);

    Notification::query()->where('user_id', $this->user->getKey())->update(['read_at' => now()]);

    $this->getJson(route('notifications.get-by-logged-user'))
        ->assertOk()
        ->assertJsonCount(5);
});
