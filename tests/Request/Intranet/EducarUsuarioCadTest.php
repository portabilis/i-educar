<?php

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\LegacyEmployee;
use App\Models\LegacyUser;
use App\Models\LegacyUserSchool;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->other = LegacyUserFactory::new()->institutional()->create();
});

test('abre o formulário de um novo usuário', function () {
    $this->get('/intranet/educar_usuario_cad.php')->assertOk();
});

test('edita o usuário', function () {
    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->other->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'email' => 'usuario.alfa@example.com',
        // A matrícula precisa ser única, e a da factory é um dígito aleatório.
        'matricula' => 'u' . $this->other->getKey(),
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
});

// Um novo usuário parte de uma pessoa física que ainda não é funcionário.
function usuarioNovoForm(array $overrides = []): array
{
    $person = StudentScenario::individual('Ana Alves');
    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $person->getKey();

    return [$url, LegacyFields::from(test()->get($url)->assertOk()->getContent(), $overrides + [
        'matricula' => 'ana.alves',
        '_senha' => 'Senha@123',
        'email' => 'ana.alves@example.com',
        'ref_cod_tipo_usuario' => test()->type->getKey(),
        'ref_cod_instituicao' => test()->user->ref_cod_instituicao,
        'escola' => [test()->school->getKey()],
        'data_inicial' => now()->format('d/m/Y'),
        'data_expiracao' => now()->addYear()->format('d/m/Y'),
        'tempo_expira_senha' => 30,
        'force_reset_password' => 'on',
    ])];
}

test('cadastra o usuário com funcionário, tipo, escola e senha forçada a trocar', function () {
    Event::fake([UserUpdated::class, UserDeleted::class]);
    $this->type = LegacyUserTypeFactory::new()->create();
    $this->school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    [$url, $payload] = usuarioNovoForm();

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($url, $payload)
        ->assertRedirect('educar_usuario_lst.php');

    $id = (int) $payload['ref_pessoa'];
    $employee = LegacyEmployee::query()->findOrFail($id);
    $user = LegacyUser::query()->findOrFail($id);

    expect($employee->matricula)->toBe('ana.alves')
        ->and((bool) $employee->force_reset_password)->toBeTrue()
        ->and($employee->email)->toBe('ana.alves@example.com')
        ->and($employee->tempo_expira_senha)->toBe(30)
        ->and($user->ref_cod_tipo_usuario)->toBe($this->type->getKey())
        ->and($user->ref_cod_instituicao)->toBe($this->user->ref_cod_instituicao)
        ->and(LegacyUserSchool::query()->where('ref_cod_usuario', $id)->pluck('ref_cod_escola')->all())->toBe([$this->school->getKey()]);
});

test('recusa data inicial passada, e-mail inválido, matrícula repetida e senha fraca', function (array $overrides, string $message) {
    $this->type = LegacyUserTypeFactory::new()->create();
    $this->school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    [$url, $payload] = usuarioNovoForm($overrides);

    $this->post($url, $payload)->assertOk()->assertSee($message);
})->with([
    'data inicial passada' => [['data_inicial' => '01/01/2020'], 'deve ser uma data posterior ou igual a hoje'],
    'e-mail inválido' => [['email' => 'sem-arroba'], 'Formato do e-mail inválido.'],
    'senha fraca' => [['_senha' => 'fraca'], 'A senha deve conter pelo menos 8 caracteres'],
]);

test('a matrícula não pode ser de outro funcionário', function () {
    $this->type = LegacyUserTypeFactory::new()->create();
    $this->school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    [$url, $payload] = usuarioNovoForm(['matricula' => $this->other->employee->matricula]);

    $this->post($url, $payload)->assertOk()->assertSee('já foi usada, por favor, informe outra.');
});

test('quem não é administrador não atribui tipos acima do seu nível nem edita administradores', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $adminType = $this->user->ref_cod_tipo_usuario;
    // A tela abre pelo processo 561 e grava pelo 555.
    $editor = LegacyUserFactory::new()->institutional()->withAccess(561)->withAccess(555)->create();
    $this->actingAs($editor);

    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->other->getKey();
    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['matricula' => 'u' . $this->other->getKey(), 'ref_cod_tipo_usuario' => $adminType]);

    $this->post($url, $payload)->assertOk()->assertSee('Você não tem permissão para atribuir este tipo de usuário.');

    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->user->getKey();
    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['matricula' => 'u' . $this->user->getKey(), 'email' => 'novo@example.com']);

    $this->post($url, $payload)->assertOk();

    expect($this->user->employee->refresh()->email)->not->toBe('novo@example.com');
});

test('a edição troca a senha, desativa e reativa o usuário', function () {
    Event::fake([UserUpdated::class, UserDeleted::class]);
    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->other->getKey();
    $form = fn (array $overrides) => LegacyFields::from($this->get($url)->assertOk()->getContent(), $overrides + ['matricula' => 'u' . $this->other->getKey()]);

    $this->post($url, $form(['_senha' => 'fraca']))->assertOk()->assertSee('A senha deve conter pelo menos 8 caracteres');

    $this->post($url, $form(['_senha' => 'Nova@1234', 'ativo' => '0', 'motivo' => 'Férias']))
        ->assertRedirect('educar_usuario_lst.php');

    $employee = $this->other->employee->refresh();
    expect(Hash::check('Nova@1234', $employee->senha))->toBeTrue()
        ->and($employee->ativo)->toBe(0)
        ->and($employee->motivo)->toBe('Férias')
        ->and($this->other->refresh()->data_exclusao)->not->toBeNull();
    Event::assertDispatched(UserUpdated::class);

    $this->post($url, $form(['ativo' => '1']))->assertRedirect('educar_usuario_lst.php');

    expect($employee->refresh()->ativo)->toBe(1)
        ->and($employee->data_reativa_conta)->not->toBeNull();
});

test('a exclusão desativa o funcionário', function () {
    Event::fake([UserDeleted::class]);
    $url = '/intranet/educar_usuario_cad.php?ref_pessoa=' . $this->other->getKey();
    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)
        ->assertRedirect('educar_usuario_lst.php');

    expect($this->other->employee->refresh()->ativo)->toBe(0);
    Event::assertDispatched(UserDeleted::class);
});
