<?php

use App\Jobs\BatchDisableUsersWithDaysGoneSinceLastAccess;
use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('confere cada usuário ativo que não é administrador quando há prazo configurado', function () {
    $user = LegacyUserFactory::new()->create();
    $checked = [];
    $service = Mockery::mock(DisableUsersWithDaysGoneSinceLastAccessService::class);
    $service->shouldReceive('execute')->andReturnUsing(function ($given) use (&$checked) {
        $checked[] = $given->getKey();
    });
    app()->instance(DisableUsersWithDaysGoneSinceLastAccessService::class, $service);

    config(['legacy.app.user_accounts.max_days_without_login_to_disable_user' => 60]);
    (new BatchDisableUsersWithDaysGoneSinceLastAccess('pgsql'))->handle(config());

    expect($checked)->toContain($user->getKey());
});
