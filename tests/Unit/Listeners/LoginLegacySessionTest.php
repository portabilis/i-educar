<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyIndividualPictureFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('os dados do usuário logado trazem a foto da pessoa e um ano atrás quando a data de criação é inválida', function () {
    config(['filesystems.default' => 'local']);
    app()->instance(LegacyInstitution::class, LegacyInstitutionFactory::new()->create());
    $person = LegacyPersonFactory::new()->create();
    LegacyIndividualPictureFactory::new()->create(['idpes' => $person, 'caminho' => 'https://fotos.test/ana.jpg']);
    $user = (object) ['cod_usuario' => $person->getKey(), 'created_at' => 'não é data', 'id' => 1, 'name' => 'Ana', 'email' => null, 'role' => null];

    $info = (fn () => $this->getLoggedUserInfo($user))->call(app(LoginLegacySession::class));

    expect($info->picture)->toBe('https://fotos.test/ana.jpg')
        ->and($info->created_at)->toBeLessThanOrEqual(now()->subYear()->getTimestamp())
        ->and($info->created_at)->toBeGreaterThan(now()->subYear()->subMinute()->getTimestamp());
});
