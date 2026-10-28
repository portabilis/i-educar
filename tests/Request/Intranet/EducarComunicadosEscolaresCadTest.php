<?php

use App\Models\SchoolNotice;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

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

test('cadastra um comunicado escolar', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_comunicados_escolares_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_escola' => $this->notice->school_id,
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

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['titulo' => 'Comunicado renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->notice->refresh()->title)->toBe('Comunicado renomeado');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
