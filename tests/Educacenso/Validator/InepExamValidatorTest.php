<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use iEducar\Modules\Educacenso\Validator\InepExamValidator;
use Tests\TestCase;

class InepExamValidatorTest extends TestCase
{
    public function test_just_nenhum_selected()
    {
        $resources = [RecursosRealizacaoProvas::NENHUM];
        $deficiencies = [];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_nenhum_and_another_resource_selected()
    {
        $resources = [RecursosRealizacaoProvas::NENHUM, RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Não é possível informar mais de uma opção no campo: Recursos necessários para realização de provas, quando a opção: <b>Nenhum</b> estiver selecionada', $validator->getMessage());
    }

    public function test_prova_ampliada_and_another_permited_option_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_prova_ampliada_and_another_forbidden_option_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
        $deficiencies = [Deficiencias::BAIXA_VISAO, Deficiencias::VISAO_MONOCULAR];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_auxilio_ledor_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_auxilio_ledor_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_auxilio_ledor_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::DEFICIENCIA_AUDITIVA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_auxilio_transcricao_and_allowed_alone_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
        $deficiencies = [Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_auxilio_transcricao_and_not_allowed_alone_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_auxilio_transcricao_and_not_allowed_alone_deficiency_with_another_resource_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO, RecursosRealizacaoProvas::AUXILIO_LEDOR];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_auxilio_transcricao_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
        $deficiencies = [Deficiencias::DEFICIENCIA_AUDITIVA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_guia_interprete_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::GUIA_INTERPRETE];
        $deficiencies = [Deficiencias::DEFICIENCIA_AUDITIVA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_guia_interprete_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::GUIA_INTERPRETE];
        $deficiencies = [Deficiencias::SURDOCEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_tradutor_interprete_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
        $deficiencies = [Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_tradutor_interprete_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_tradutor_interprete_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
        $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_leitura_labial_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
        $deficiencies = [Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_leitura_labial_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_leitura_labial_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
        $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_ampliada_fonte18_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
        $deficiencies = [Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_prova_ampliada_fonte18_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_ampliada_fonte18_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
        $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_super_ampliada_fonte24_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
        $deficiencies = [Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_prova_super_ampliada_fonte24_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_super_ampliada_fonte24_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
        $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_cd_com_audio_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
        $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::VISAO_MONOCULAR];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_cd_com_audio_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
        $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_cd_com_audio_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
        $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_lingua_portuguesa_segunda_lingua_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
        $deficiencies = [Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_prova_lingua_portuguesa_segunda_lingua_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
        $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_lingua_portuguesa_segunda_lingua_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
        $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_video_libras_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
        $deficiencies = [Deficiencias::SURDEZ];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_prova_video_libras_and_allowed_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
        $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_prova_video_libras_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
        $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_material_didatico_prova_braille_and_allowed_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }

    public function test_material_didatico_prova_braille_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE];
        $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_nenhum_and_forbidden_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::NENHUM];
        $deficiencies = [Deficiencias::CEGUEIRA];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
    }

    public function test_nenhum_and_neutral_deficiency_choosed()
    {
        $resources = [RecursosRealizacaoProvas::NENHUM];
        $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
        $validator = new InepExamValidator($resources, $deficiencies);

        $this->assertTrue($validator->isValid());
    }
}
