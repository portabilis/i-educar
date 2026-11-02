<?php

namespace Tests\Unit\Models;

use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Models\LegacyUser;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('a operação pertence ao usuário que a criou e começa aguardando', function () {
    $user = LegacyUserFactory::new()->create();

    $operation = ComponentBatchOperation::query()->create(['user_id' => $user->getKey(), 'data' => []])->refresh();

    expect($operation->status())->toBe(ComponentBatchStatus::WAITING)
        ->and($operation->user)->toBeInstanceOf(LegacyUser::class)
        ->and($operation->user->getKey())->toBe($user->getKey());
});
