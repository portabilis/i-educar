<?php

use App\Events\SystemSettingsUpdatedEvent;
use App\Listeners\LoginLegacySession;
use App\Setting;
use App\SettingCategory;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SettingFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->login = function ($user) {
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->category = SettingCategory::query()->create(['name' => 'Geral']);
    $this->settings = collect([Setting::TYPE_STRING, Setting::TYPE_INTEGER, Setting::TYPE_FLOAT, Setting::TYPE_BOOLEAN, 'desconhecido'])
        ->map(fn ($type) => SettingFactory::new()->create(['setting_category_id' => $this->category->getKey(), 'type' => $type, 'key' => "chave_{$type}", 'value' => $type === Setting::TYPE_BOOLEAN ? '1' : '10']));
});

test('a tela de configurações mostra cada tipo de campo para administradores', function () {
    $this->get(route('settings.index'))
        ->assertOk()
        ->assertViewHas('categories', fn ($categories) => $categories->first()->settings->count() === 5)
        ->assertSee('Geral');

    ($this->login)(LegacyUserFactory::new()->institutional()->create());

    $this->from('/intranet/educar_configuracoes_index.php')
        ->get(route('settings.index'))
        ->assertRedirect('/intranet/educar_configuracoes_index.php')
        ->assertSessionHasErrors('Error');
});

test('salva os valores e avisa que as configurações mudaram', function () {
    Event::fake([SystemSettingsUpdatedEvent::class]);
    $setting = $this->settings->first();

    $this->post(route('settings.update'), [$setting->getKey() => 'novo valor'])
        ->assertRedirect(route('settings.index'))
        ->assertSessionHas('success', 'Configurações de sistema salvas com sucesso.');

    expect($setting->refresh()->value)->toBe('novo valor');
    Event::assertDispatched(SystemSettingsUpdatedEvent::class);
});
