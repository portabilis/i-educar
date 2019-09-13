<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use Tests\TestCase;

class DeficiencyValidatorTest extends TestCase
{
    public function testOnlyOneDeficiencyChoosed()
    {
        $values = [ Deficiencias::CEGUEIRA ];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function testChooseCegueiraAndAllowedDeficiency()
    {
        $values = [ Deficiencias::CEGUEIRA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA ];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function testChooseCegueiraAndForbiddenDeficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::BAIXA_VISAO,
            Deficiencias::SURDEZ,
            Deficiencias::SURDOCEGUEIRA,
        ];

        $randomIndex = array_rand($forbiddenDeficiencies);

        $values = [ Deficiencias::CEGUEIRA, $forbiddenDeficiencies[$randomIndex] ];
        $validator = new DeficiencyValidator($values);

        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::CEGUEIRA]);

        $this->assertFalse($validator->isValid());
        $this->assertContains("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function testChooseBaixaVisaoAndAllowedDeficiency()
    {
        $values = [ Deficiencias::BAIXA_VISAO, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA ];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function testChooseBaixaVisaoAndForbiddenDeficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::SURDOCEGUEIRA,
        ];

        $values = [ Deficiencias::BAIXA_VISAO, $forbiddenDeficiencies[0] ];
        $validator = new DeficiencyValidator($values);

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::BAIXA_VISAO]);

        $this->assertFalse($validator->isValid());
        $this->assertContains("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function testChooseSurdezAndAllowedDeficiency()
    {
        $values = [ Deficiencias::SURDEZ, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA ];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function testChooseSurdezAndForbiddenDeficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::DEFICIENCIA_AUDITIVA,
            Deficiencias::SURDOCEGUEIRA,
        ];

        $randomIndex = array_rand($forbiddenDeficiencies);

        $values = [ Deficiencias::SURDEZ, $forbiddenDeficiencies[$randomIndex] ];
        $validator = new DeficiencyValidator($values);

        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::SURDEZ]);

        $this->assertFalse($validator->isValid());
        $this->assertContains("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    public function testChooseDeficienciaAuditivaAndAllowedDeficiency()
    {
        $values = [ Deficiencias::DEFICIENCIA_AUDITIVA, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA ];
        $validator = new DeficiencyValidator($values);

        $this->assertTrue($validator->isValid());
    }

    public function testChooseDeficienciaAuditivaAndForbiddenDeficiency()
    {
        $forbiddenDeficiencies = [
            Deficiencias::SURDOCEGUEIRA,
        ];

        $values = [ Deficiencias::DEFICIENCIA_AUDITIVA, $forbiddenDeficiencies[0] ];
        $validator = new DeficiencyValidator($values);

        $descriptions = Deficiencias::getDescriptiveValues();

        $forbiddenDescriptions = $this->getDeficienciesDescriptions($forbiddenDeficiencies);
        $choosedDescription = $this->getDeficienciesDescriptions([Deficiencias::DEFICIENCIA_AUDITIVA]);

        $this->assertFalse($validator->isValid());
        $this->assertContains("Quando a deficiência for: {$choosedDescription}, não pode ser preenchido também com {$forbiddenDescriptions}.", $validator->getMessage());
    }

    private function getDeficienciesDescriptions($values)
    {
        $descriptions = Deficiencias::getDescriptiveValues();

        $descriptions = array_filter($descriptions, function($key) use ($values){
            return in_array($key, $values);
        }, ARRAY_FILTER_USE_KEY);

        return implode(', ', $descriptions);
    }
}
