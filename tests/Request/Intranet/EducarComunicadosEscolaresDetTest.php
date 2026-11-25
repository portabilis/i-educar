<?php

use App\Models\SchoolNotice;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolNoticeFactory;

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

test('mostra o detalhe do comunicado escolar', function () {
    $this->get('/intranet/educar_comunicados_escolares_det.php?id=' . $this->notice->getKey())
        ->assertOk()
        ->assertSee('Comunicado Alfa');
});

test('volta para a listagem avisando quando o comunicado não existe', function () {
    $this->get('/intranet/educar_comunicados_escolares_det.php?id=0')
        ->assertRedirect('educar_comunicados_escolares_lst.php')
        ->assertSessionHas('error', 'Comunicado escolar não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.');
});

test('mostra botao editar no detalhe apenas quando todas as escolas do comunicado estao no alcance do usuario', function () {
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

    $allowedNotice = SchoolNoticeFactory::new()
        ->withSchools($allowedSchool)
        ->create([
            'institution_id' => $this->user->ref_cod_instituicao,
            'user_id' => $this->user->getKey(),
            'title' => 'Aviso Permitido',
        ]);

    $mixedNotice = SchoolNoticeFactory::new()
        ->withSchools($allowedSchool, $otherSchool)
        ->create([
            'institution_id' => $this->user->ref_cod_instituicao,
            'user_id' => $this->user->getKey(),
            'title' => 'Aviso Restrito',
        ]);

    $this->actingAs($schoolUser);

    $this->get('/intranet/educar_comunicados_escolares_det.php?id=' . $allowedNotice->getKey())
        ->assertOk()
        ->assertSee("educar_comunicados_escolares_cad.php?id={$allowedNotice->getKey()}", false);

    $this->get('/intranet/educar_comunicados_escolares_det.php?id=' . $mixedNotice->getKey())
        ->assertOk()
        ->assertDontSee("educar_comunicados_escolares_cad.php?id={$mixedNotice->getKey()}", false);
});
