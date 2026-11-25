<?php

use App\Models\SchoolNotice;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolNoticeFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->notice = SchoolNotice::create([
        'institution_id' => $this->user->ref_cod_instituicao,
        'user_id' => $this->user->getKey(),
        'title' => 'Comunicado Alfa',
        'description' => 'Texto do comunicado',
        'date' => now(),
        'hour' => '10:00',
        'local' => 'Auditório',
    ]);
    $this->school = LegacySchoolFactory::new()->create();
    $this->notice->schools()->attach($this->school);
});

test('cadastra um comunicado escolar', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_comunicados_escolares_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'escolas' => [$this->school->getKey()],
        'titulo' => 'Comunicado Beta',
        'descricao' => 'Texto do comunicado',
        'data' => now()->format('d/m/Y'),
        'hora' => '14:00',
        'local' => 'Pátio',
    ]);

    $this->post('/intranet/educar_comunicados_escolares_cad.php', $payload)
        ->assertRedirect('educar_comunicados_escolares_lst.php');

    expect(SchoolNotice::query()->where('title', 'Comunicado Beta')->exists())->toBeTrue();
});

test('edita e exclui o comunicado escolar', function () {
    $url = '/intranet/educar_comunicados_escolares_cad.php?id=' . $this->notice->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['titulo' => 'Comunicado renomeado', 'escolas' => [$this->school->getKey()]]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->notice->refresh()->title)->toBe('Comunicado renomeado');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});

test('usuario de escola publica para escola no alcance e e bloqueado para escola fora do alcance', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);

    $allowedSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $otherSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $schoolUser = LegacyUserFactory::new()
        ->school()
        ->withSchool($allowedSchool)
        ->withAccess(SchoolNotice::PROCESS)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);

    $this->actingAs($schoolUser);

    $formHtml = $this->get('/intranet/educar_comunicados_escolares_cad.php')->assertOk()->getContent();

    $blockedPayload = LegacyFields::from($formHtml, [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'escolas' => [$allowedSchool->getKey(), $otherSchool->getKey()],
        'titulo' => 'Comunicado Bloqueado',
        'descricao' => 'Texto',
        'data' => now()->format('d/m/Y'),
        'hora' => '15:00',
        'local' => 'Pátio',
    ]);

    $this->post('/intranet/educar_comunicados_escolares_cad.php', $blockedPayload)
        ->assertOk()
        ->assertSee('Você não tem permissão para publicar comunicados para uma ou mais escolas selecionadas.');

    expect(SchoolNotice::query()->where('title', 'Comunicado Bloqueado')->exists())->toBeFalse();

    $allowedPayload = LegacyFields::from($formHtml, [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'escolas' => [$allowedSchool->getKey()],
        'titulo' => 'Comunicado Permitido',
        'descricao' => 'Texto',
        'data' => now()->format('d/m/Y'),
        'hora' => '15:00',
        'local' => 'Pátio',
    ]);

    $this->post('/intranet/educar_comunicados_escolares_cad.php', $allowedPayload)
        ->assertRedirect('educar_comunicados_escolares_lst.php');

    expect(SchoolNotice::query()->where('title', 'Comunicado Permitido')->exists())->toBeTrue();
});

test('usuario de escola e bloqueado ao tentar editar ou excluir comunicado com escola fora do alcance', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);

    $allowedSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $otherSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    $schoolUser = LegacyUserFactory::new()
        ->school()
        ->withSchool($allowedSchool)
        ->withAccess(SchoolNotice::PROCESS)
        ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);

    $mixedNotice = SchoolNoticeFactory::new()
        ->withSchools($allowedSchool, $otherSchool)
        ->create([
            'institution_id' => $this->user->ref_cod_instituicao,
            'user_id' => $this->user->getKey(),
            'title' => 'Comunicado Misto',
        ]);

    $editUrl = '/intranet/educar_comunicados_escolares_cad.php?id=' . $mixedNotice->getKey();
    $adminFormHtml = $this->get($editUrl)->assertOk()->getContent();

    $this->actingAs($schoolUser);

    $this->get($editUrl)
        ->assertRedirect("educar_comunicados_escolares_det.php?id={$mixedNotice->getKey()}");

    $editPayload = LegacyFields::from($adminFormHtml, [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'escolas' => [$allowedSchool->getKey()],
        'titulo' => 'Comunicado Alterado Indevidamente',
    ]);

    $this->post($editUrl, $editPayload)
        ->assertOk()
        ->assertSee('Você não tem permissão para editar este comunicado.');

    expect($mixedNotice->refresh()->title)->toBe('Comunicado Misto');

    $this->post($editUrl, ['tipoacao' => 'Excluir'] + $editPayload)
        ->assertOk()
        ->assertSee('Você não tem permissão para excluir este comunicado.');

    expect(SchoolNotice::query()->whereKey($mixedNotice->getKey())->exists())->toBeTrue();
});
