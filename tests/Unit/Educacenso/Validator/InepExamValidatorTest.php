<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\InepExamValidator;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use Tests\TestCase;

class InepExamValidatorTest extends TestCase
{
    public function testJustNenhumSelected()
    {
        $resources = [ RecursosRealizacaoProvas::NENHUM ];
        $deficiencies = [];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testNenhumAndAnotherResourceSelected()
    {
        $resources = [ RecursosRealizacaoProvas::NENHUM, RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('Não é possível informar mais de uma opção no campo: Recursos necessários para realização de provas, quando a opção: <b>Nenhum</b> estiver selecionada', $validator->getMessage());
    }

    public function testProvaAmpliadaAndAnotherPermitedOptionChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testProvaAmpliadaAndAnotherForbiddenOptionChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24 ];
        $deficiencies = [ Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testAuxilioLedorAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testAuxilioLedorAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::CEGUEIRA, Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testAuxilioLedorAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_AUDITIVA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testAuxilioTranscricaoAndAllowedAloneDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO ];
        $deficiencies = [ Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testAuxilioTranscricaoAndNotAllowedAloneDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testAuxilioTranscricaoAndNotAllowedAloneDeficiencyWithAnotherResourceChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO, RecursosRealizacaoProvas::AUXILIO_LEDOR ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testAuxilioTranscricaoAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_AUDITIVA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testGuiaInterpreteAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::GUIA_INTERPRETE ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_AUDITIVA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testGuiaInterpreteAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::GUIA_INTERPRETE ];
        $deficiencies = [ Deficiencias::SURDOCEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testTradutorInterpreteAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS ];
        $deficiencies = [ Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testTradutorInterpreteAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS ];
        $deficiencies = [ Deficiencias::CEGUEIRA, Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testTradutorInterpreteAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_FISICA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testLeituraLabialAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::LEITURA_LABIAL ];
        $deficiencies = [ Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testLeituraLabialAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::LEITURA_LABIAL ];
        $deficiencies = [ Deficiencias::CEGUEIRA, Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testLeituraLabialAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::LEITURA_LABIAL ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_FISICA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaAmpliadaFonte18AndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18 ];
        $deficiencies = [ Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testProvaAmpliadaFonte18AndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18 ];
        $deficiencies = [ Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaAmpliadaFonte18AndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18 ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_FISICA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaSuperAmpliadaFonte24AndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24 ];
        $deficiencies = [ Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testProvaSuperAmpliadaFonte24AndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24 ];
        $deficiencies = [ Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaSuperAmpliadaFonte24AndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24 ];
        $deficiencies = [ Deficiencias::DEFICIENCIA_FISICA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testCdComAudioAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testCdComAudioAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL ];
        $deficiencies = [ Deficiencias::SURDEZ, Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testCdComAudioAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL ];
        $deficiencies = [ Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaLinguaPortuguesaSegundaLinguaAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS ];
        $deficiencies = [ Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testProvaLinguaPortuguesaSegundaLinguaAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS ];
        $deficiencies = [ Deficiencias::SURDEZ, Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaLinguaPortuguesaSegundaLinguaAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS ];
        $deficiencies = [ Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaVideoLibrasAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS ];
        $deficiencies = [ Deficiencias::SURDEZ ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testProvaVideoLibrasAndAllowedAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS ];
        $deficiencies = [ Deficiencias::SURDEZ, Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testProvaVideoLibrasAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS ];
        $deficiencies = [ Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testMaterialDidaticoProvaBrailleAndAllowedDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function testMaterialDidaticoProvaBrailleAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE ];
        $deficiencies = [ Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testNenhumAndForbiddenDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::NENHUM ];
        $deficiencies = [ Deficiencias::CEGUEIRA ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertContains('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function testNenhumAndNeutralDeficiencyChoosed()
    {
        $resources = [ RecursosRealizacaoProvas::NENHUM ];
        $deficiencies = [ Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }
}
