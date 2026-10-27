<?php

use App\Listeners\LoginLegacySession;
use App\Models\Announcement;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Support\Exceptions\Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->type = LegacyUserTypeFactory::new()->create();
    $this->form = fn (array $overrides = []) => $overrides + [
        'name' => 'Volta às aulas',
        'description' => '<p>Bem-vindos</p><script>alert(1)</script>',
        'tipo_usuario' => [[$this->type->getKey()]],
    ];
    // A data de criação não é preenchível; entra depois, para ordenar.
    $this->announcement = fn (array $attributes = []) => tap(Announcement::query()->create($attributes + ['name' => 'Antigo', 'description' => 'x', 'created_by_user_id' => $this->user->getKey()]), fn ($announcement) => $announcement->forceFill(['created_at' => $attributes['created_at'] ?? now()])->saveQuietly());
});

test('a listagem traz os avisos, inclusive os inativos, do mais novo ao mais velho', function () {
    $old = ($this->announcement)(['created_at' => now()->subMinute()]);
    $old->delete();
    $new = ($this->announcement)(['name' => 'Novo']);

    $this->get(route('announcement.publish.index'))
        ->assertOk()
        ->assertViewHas('announcements', fn ($announcements) => $announcements->pluck('id')->all() === [$new->getKey(), $old->getKey()]);
});

test('o formulário abre vazio para criar e preenchido para editar', function () {
    $this->get(route('announcement.publish.create'))
        ->assertOk()
        ->assertViewHas('userTypes', null);

    $announcement = ($this->announcement)();
    $announcement->userTypes()->sync([$this->type->getKey()]);

    $this->get(route('announcement.publish.edit', $announcement))
        ->assertOk()
        ->assertViewHas('userTypes', fn ($types) => $types->all() === [$this->type->getKey()]);
});

test('o aviso exige nome, conteúdo e tipos de usuário, e só um aviso pode estar ativo', function () {
    $this->post(route('announcement.publish.store'), ['active' => 1, 'tipo_usuario' => [[999999999]]])
        ->assertSessionHasErrors(['name', 'description', 'tipo_usuario.0']);

    ($this->announcement)();

    $this->post(route('announcement.publish.store'), ($this->form)(['active' => 1]))
        ->assertSessionHasErrors(['active' => 'Já existe um aviso ativo.']);
});

test('cadastra o aviso limpando as tags, vinculando os tipos e desativando quando não marcado', function () {
    $this->post(route('announcement.publish.store'), ($this->form)(['repeat_on_login' => 1, 'show_vacancy' => 1]))
        ->assertRedirect(route('announcement.publish.index'))
        ->assertSessionHas('success', 'Cadastro efetuado com sucesso.');

    $announcement = Announcement::query()->withTrashed()->latest('id')->first();

    expect($announcement->trashed())->toBeTrue()
        ->and($announcement->description)->toBe('<p>Bem-vindos</p>alert(1)')
        ->and((bool) $announcement->repeat_on_login)->toBeTrue()
        ->and((bool) $announcement->show_confirmation)->toBeFalse()
        ->and($announcement->created_by_user_id)->toBe($this->user->getKey())
        ->and($announcement->userTypes->pluck('cod_tipo_usuario')->all())->toBe([$this->type->getKey()]);

    $this->post(route('announcement.publish.store'), ($this->form)(['active' => 1]));

    expect(Announcement::query()->withTrashed()->latest('id')->first()->trashed())->toBeFalse();
});

test('edita o aviso, ativando ou desativando', function () {
    $announcement = ($this->announcement)();
    $announcement->delete();
    $other = LegacyUserTypeFactory::new()->create();

    $this->post(route('announcement.publish.update', $announcement), ($this->form)(['name' => 'Editado', 'active' => 1, 'tipo_usuario' => [[$other->getKey()]]]))
        ->assertRedirect(route('announcement.publish.edit', $announcement))
        ->assertSessionHas('success', 'Edição efetuada com sucesso.');

    $announcement = Announcement::query()->withTrashed()->find($announcement->getKey());

    expect($announcement->name)->toBe('Editado')
        ->and($announcement->trashed())->toBeFalse()
        ->and($announcement->userTypes->pluck('cod_tipo_usuario')->all())->toBe([$other->getKey()]);

    $this->post(route('announcement.publish.update', $announcement), ($this->form)(['name' => 'Editado']));

    expect(Announcement::query()->withTrashed()->find($announcement->getKey())->trashed())->toBeTrue();
});

test('avisa quando não consegue cadastrar nem editar o aviso', function () {
    $announcement = ($this->announcement)();
    // O controller só captura as exceções do i-Educar.
    Announcement::saving(fn () => throw new Exception('banco indisponível'));

    $this->post(route('announcement.publish.store'), ($this->form)())
        ->assertRedirect(route('announcement.publish.index'))
        ->assertSessionHas('error', 'Cadastro não realizado.');

    $this->post(route('announcement.publish.update', $announcement), ($this->form)())
        ->assertRedirect(route('announcement.publish.edit', $announcement))
        ->assertSessionHas('error', 'Edição não realizada.');
});
