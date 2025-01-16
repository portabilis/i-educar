<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
use Tests\TestCase;

class DeficiencyValidatorTest extends TestCase
{
    public function test_only_one_deficiency_choosed()
    {
        $values = [Deficiencias::CEGUEIRA];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function test_choose_cegueira_and_allowed_deficiency()
    {
        $values = [Deficiencias::CEGUEIRA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function test_choose_cegueira_and_forbidden_deficiency()
    {
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

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::CEGUEIRA]);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function test_choose_baixa_visao_and_allowed_deficiency()
    {
        $values = [Deficiencias::BAIXA_VISAO, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function test_choose_baixa_visao_and_forbidden_deficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::SURDOCEGUEIRA,
        ];

        $values = [Deficiencias::BAIXA_VISAO, $forbiddenDeficiencies[0]];
        $validator = new DeficiencyValidator($values);

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::BAIXA_VISAO]);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function test_choose_surdez_and_allowed_deficiency()
    {
        $values = [Deficiencias::SURDEZ, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function test_choose_surdez_and_forbidden_deficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::DEFICIENCIA_AUDITIVA,
            Deficiencias::SURDOCEGUEIRA,
        ];

        $randomIndex = array_rand($forbiddenDeficiencies);

        $values = [Deficiencias::SURDEZ, $forbiddenDeficiencies[$randomIndex]];
        $validator = new DeficiencyValidator($values);

        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::SURDEZ]);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function test_choose_deficiencia_auditiva_and_allowed_deficiency()
    {
        $values = [Deficiencias::DEFICIENCIA_AUDITIVA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function test_choose_deficiencia_auditiva_and_forbidden_deficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::SURDOCEGUEIRA,
        ];

        $values = [Deficiencias::DEFICIENCIA_AUDITIVA, $forbiddenDeficiencies[0]];
        $validator = new DeficiencyValidator($values);

        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::DEFICIENCIA_AUDITIVA]);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    private function getDeficienciesDescriptions($values)
    {
        $descriptions = Deficiencias::getDescriptiveValues();

        $descriptions = array_filter($descriptions, function ($key) use ($values) {
            return in_array($key, $values);
        }, ARRAY_FILTER_USE_KEY);

        return implode(', ', $descriptions);
    }
}
