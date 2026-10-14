<?php

use App\Models\SchoolNotice;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->notice = SchoolNotice::create([
        'institution_id' => $this->user->ref_cod_instituicao,
        'user_id' => $this->user->getKey(),
        'school_id' => LegacySchoolFactory::new()->create()->getKey(),
        'title' => 'Comunicado Alfa',
        'description' => 'Texto do comunicado',
        'date' => now(),
        'hour' => '10:00',
        'local' => 'Auditório',
    ]);
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
