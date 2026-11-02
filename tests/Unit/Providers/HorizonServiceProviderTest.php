<?php

use App\Providers\HorizonServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use Tests\TestCase;

uses(TestCase::class);

test('só o administrador logado vê o Horizon', function () {
    $before = Horizon::$authUsing;
    (new HorizonServiceProvider(app()))->boot();
    $auth = Horizon::$authUsing;
    Horizon::$authUsing = $before;

    $request = fn (?User $user) => tap(Request::create('/horizon'))->setUserResolver(fn () => $user);
    $user = function (bool $admin) {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('isAdmin')->andReturn($admin);

        return $user;
    };

    expect($auth($request(null)))->toBeFalse()
        ->and($auth($request($user(true))))->toBeTrue()
        ->and($auth($request($user(false))))->toBeFalse();
});
