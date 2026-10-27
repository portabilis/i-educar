<?php

use App\Events\NotificationEvent;
use App\Models\LegacyUserType;
use App\Models\Notification;
use App\Services\NotificationService;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(fn () => Event::fake([NotificationEvent::class]));

test('notifica os usuários ativos dos tipos que podem ver o processo', function () {
    $menu = MenuFactory::new()->create(['process' => 98765]);
    $viewer = LegacyUserTypeFactory::new()->create();
    $blind = LegacyUserTypeFactory::new()->create();
    LegacyMenuUserTypeFactory::new()->create(['ref_cod_tipo_usuario' => $viewer, 'menu_id' => $menu, 'visualiza' => 1]);
    LegacyMenuUserTypeFactory::new()->create(['ref_cod_tipo_usuario' => $blind, 'menu_id' => $menu, 'visualiza' => 0]);

    $active = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $viewer]);
    $inactive = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $viewer, 'ativo' => 0]);
    $other = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $blind]);

    (new NotificationService)->createByPermission(98765, 'Aviso', '/link', 1);

    expect(Notification::query()->where('text', 'Aviso')->pluck('user_id')->all())->toBe([$active->getKey()]);
    Event::assertDispatchedTimes(NotificationEvent::class, 1);
});

test('notifica os usuários do nível informado', function () {
    $type = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_INSTITUTIONAL]);
    $user = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $type]);

    (new NotificationService)->createByUserLevel(LegacyUserType::LEVEL_INSTITUTIONAL, 'Aviso por nível', '/link', 1);

    expect(Notification::query()->where('text', 'Aviso por nível')->pluck('user_id'))->toContain($user->getKey());
});
