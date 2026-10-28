<?php

use App\Helpers\UserCache;
use App\User;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('guarda o usuário com pessoa, tipo e funcionário no cache por id', function () {
    $user = LegacyUserFactory::new()->create();

    $cached = UserCache::user($user->getKey());

    expect($cached)->toBeInstanceOf(User::class)
        ->and($cached->relationLoaded('employee'))->toBeTrue()
        ->and(Cache::has('user_' . $user->getKey()))->toBeTrue()
        ->and(UserCache::user(0))->toBeNull();
});
