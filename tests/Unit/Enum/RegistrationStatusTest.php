<?php

use App\Models\RegistrationStatus;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->getDescriptiveValues = function (): array {
        return [
            1 => 'Aprovado',
            2 => 'Retido',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Deixou de Frequentar',
            7 => 'Em exame',
            8 => 'Aprovado após exame',
            10 => 'Aprovado sem exame',
            11 => 'Pré-matrícula',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
            15 => 'Falecido',
        ];
    };
});

test('descriptive values', function () {
    $values = (new RegistrationStatus)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect(($this->getDescriptiveValues)()), collect($values));
});

test('inactive statuses', function () {
    $this->assertSame([
        RegistrationStatus::ABANDONED,
        RegistrationStatus::TRANSFERRED,
        RegistrationStatus::DECEASED,
    ], RegistrationStatus::getStatusInactive());
});

test('registration and enrollment status calls the retained student reproved', function () {
    $values = RegistrationStatus::getRegistrationAndEnrollmentStatus();

    $this->assertSame('Reprovado', $values[RegistrationStatus::REPROVED]);
    $this->assertSame('Aprovado', $values[RegistrationStatus::APPROVED]);
    $this->assertCount(count(($this->getDescriptiveValues)()), $values);
});
