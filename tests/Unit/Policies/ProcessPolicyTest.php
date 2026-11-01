<?php

use App\Policies\ProcessPolicy;
use App\User;
use Tests\TestCase;

uses(TestCase::class);

test('sem processo informado, cadastrar e excluir são liberados', function () {
    $policy = new ProcessPolicy;

    expect($policy->modify(new User, null))->toBeTrue()
        ->and($policy->remove(new User, 0))->toBeTrue();
});
