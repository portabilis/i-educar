<?php

use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    // Matrícula enturmada, com etapa e um componente curricular lançáveis.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 1);
    $this->createDisciplines($this->schoolClass, 1);
    $this->query = http_build_query([
        'matricula' => $this->registration->getKey(),
        'etapa' => 1,
        'componenteCurricular' => $this->schoolClass->disciplines()->first()->getKey(),
    ]);
});

test('abre o lançamento de faltas da matrícula', function () {
    $this->get('/module/Avaliacao/falta?' . $this->query)->assertOk();
});
