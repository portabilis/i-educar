<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
    $this->query = '?ref_cod_matricula=' . $this->registration->getKey() . '&ref_cod_aluno=' . $this->registration->ref_cod_aluno;
});

test('solicita a transferência da matrícula para uma escola de outro município', function () {
    // O listener do webhook de transferência lê a configuração geral da instituição.
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao]);

    $url = '/intranet/educar_transferencia_solicitacao_cad.php' . $this->query;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'data_cancel' => now()->format('d/m/Y'),
        'ref_cod_transferencia_tipo' => LegacyTransferTypeFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao])->getKey(),
        'escola_em_outro_municipio' => 'on',
        'escola_destino_externa' => 'Escola de destino',
        'estado_escola_destino_externa' => 'SC',
        'municipio_escola_destino_externa' => 'Içara',
    ]);

    $this->post($url, $payload)->assertRedirect();
});
