<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\ConfiguresInstitutionToken;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, ConfiguresInstitutionToken::class);

test('rejects empty token when institution has no token configured', function () {
    $this->institutionWithToken(null);

    $this->withHeaders(['token' => ''])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects empty token when institution token is blank', function () {
    $this->institutionWithToken('   ');

    $this->withHeaders(['token' => ''])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects request without token when institution has no token configured', function () {
    $this->institutionWithToken(null);

    $this->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects request when institution has no general configuration', function () {
    $institution = $this->institutionWithToken('valid-token');
    $institution->generalConfiguration()->delete();

    $this->withHeaders(['token' => ''])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('accepts configured token ignoring surrounding spaces in the configuration', function () {
    $this->institutionWithToken(' valid-token ');

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(422);
});
