<?php

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->getDeficienciesDescriptions = function ($values) {
        $descriptions = Deficiencias::getDescriptiveValues();

        $descriptions = array_filter($descriptions, function ($key) use ($values) {
            return in_array($key, $values);
        }, ARRAY_FILTER_USE_KEY);

        return implode(', ', $descriptions);
    };
});

test('only one deficiency choosed', function () {
    $values = [Deficiencias::CEGUEIRA];
    $validator = new DeficiencyValidator($values);

    $this->assertTrue($validator->isValid());
});

test('choose cegueira and allowed deficiency', function () {
    $values = [Deficiencias::CEGUEIRA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
    $validator = new DeficiencyValidator($values);

    $this->assertTrue($validator->isValid());
});

test('choose cegueira and forbidden deficiency', function () {
    $forbiddenDeficiencies = [
        Deficiencias::BAIXA_VISAO,
        Deficiencias::SURDEZ,
        Deficiencias::SURDOCEGUEIRA,
        Deficiencias::VISAO_MONOCULAR,
    ];

    $randomIndex = array_rand($forbiddenDeficiencies);

    $values = [Deficiencias::CEGUEIRA, $forbiddenDeficiencies[$randomIndex]];
    $validator = new DeficiencyValidator($values);

    $descriptions = Deficiencias::getDescriptiveValues();

    $forbiddenDescriptions = ($this->getDeficienciesDescriptions)($forbiddenDeficiencies);
    $choosedDescription = ($this->getDeficienciesDescriptions)([Deficiencias::CEGUEIRA]);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
});

test('choose baixa visao and allowed deficiency', function () {
    $values = [Deficiencias::BAIXA_VISAO, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
    $validator = new DeficiencyValidator($values);

    $this->assertTrue($validator->isValid());
});

test('choose baixa visao and forbidden deficiency', function () {
    $forbiddenDeficiencies = [
        Deficiencias::SURDOCEGUEIRA,
    ];

    $values = [Deficiencias::BAIXA_VISAO, $forbiddenDeficiencies[0]];
    $validator = new DeficiencyValidator($values);

    $forbiddenDescriptions = ($this->getDeficienciesDescriptions)($forbiddenDeficiencies);
    $choosedDescription = ($this->getDeficienciesDescriptions)([Deficiencias::BAIXA_VISAO]);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
});

test('choose surdez and allowed deficiency', function () {
    $values = [Deficiencias::SURDEZ, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
    $validator = new DeficiencyValidator($values);

    $this->assertTrue($validator->isValid());
});

test('choose surdez and forbidden deficiency', function () {
    $forbiddenDeficiencies = [
        Deficiencias::DEFICIENCIA_AUDITIVA,
        Deficiencias::SURDOCEGUEIRA,
    ];

    $randomIndex = array_rand($forbiddenDeficiencies);

    $values = [Deficiencias::SURDEZ, $forbiddenDeficiencies[$randomIndex]];
    $validator = new DeficiencyValidator($values);

    $descriptions = Deficiencias::getDescriptiveValues();

    $forbiddenDescriptions = ($this->getDeficienciesDescriptions)($forbiddenDeficiencies);
    $choosedDescription = ($this->getDeficienciesDescriptions)([Deficiencias::SURDEZ]);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
});

test('choose deficiencia auditiva and allowed deficiency', function () {
    $values = [Deficiencias::DEFICIENCIA_AUDITIVA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
    $validator = new DeficiencyValidator($values);

    $this->assertTrue($validator->isValid());
});

test('choose deficiencia auditiva and forbidden deficiency', function () {
    $forbiddenDeficiencies = [
        Deficiencias::SURDOCEGUEIRA,
    ];

    $values = [Deficiencias::DEFICIENCIA_AUDITIVA, $forbiddenDeficiencies[0]];
    $validator = new DeficiencyValidator($values);

    $descriptions = Deficiencias::getDescriptiveValues();

    $forbiddenDescriptions = ($this->getDeficienciesDescriptions)($forbiddenDeficiencies);
    $choosedDescription = ($this->getDeficienciesDescriptions)([Deficiencias::DEFICIENCIA_AUDITIVA]);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
});
