<?php

use App\Listeners\LoginLegacySession;
use App\Models\Individual;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LogUnificationFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\StudentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->unification = fn (string $name, string $cpf, string $duplicates = '[]') => LogUnificationFactory::new()->create([
        'type' => Individual::class,
        'main_id' => StudentScenario::individual($name, ['cpf' => $cpf])->getKey(),
        'duplicates_id' => $duplicates,
        'duplicates_name' => json_encode(["Duplicata de {$name}"]),
    ]);
    $this->ids = fn (array $query = []) => $this->get(route('person-log-unification.index', $query))->assertOk()->viewData('unifications')->pluck('id')->all();
});

test('lista as unificações de pessoa filtrando por nome e CPF', function () {
    $ana = ($this->unification)('Ana Alves', '11111111111');
    $bia = ($this->unification)('Bia Borges', '22222222222');

    expect(($this->ids)())->toEqualCanonicalizing([$ana->getKey(), $bia->getKey()])
        ->and(($this->ids)(['name' => 'ana']))->toBe([$ana->getKey()])
        ->and(($this->ids)(['cpf' => '222.222.222-22']))->toBe([$bia->getKey()]);
});

test('mostra o detalhe de uma unificação', function () {
    $ana = ($this->unification)('Ana Alves', '11111111111');

    $this->get(route('person-log-unification.show', $ana))
        ->assertOk()
        ->assertViewHas('unification', fn ($given) => $given->is($ana));
});
