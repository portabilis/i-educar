<?php

use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use iEducar\Modules\Educacenso\Validator\InepExamValidator;
use Tests\TestCase;

uses(TestCase::class);

test('just nenhum selected', function () {
    $resources = [RecursosRealizacaoProvas::NENHUM];
    $deficiencies = [];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('nenhum and another resource selected', function () {
    $resources = [RecursosRealizacaoProvas::NENHUM, RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Não é possível informar mais de uma opção no campo: Recursos necessários para realização de provas, quando a opção: <b>Nenhum</b> estiver selecionada', $validator->getMessage());
});

test('prova ampliada and another permited option choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova ampliada and another forbidden option choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18, RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
    $deficiencies = [Deficiencias::BAIXA_VISAO, Deficiencias::VISAO_MONOCULAR];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('auxilio ledor and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('auxilio ledor and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('auxilio ledor and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::DEFICIENCIA_AUDITIVA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('auxilio transcricao and allowed alone deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
    $deficiencies = [Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('auxilio transcricao and not allowed alone deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('auxilio transcricao and not allowed alone deficiency with another resource choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO, RecursosRealizacaoProvas::AUXILIO_LEDOR];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('auxilio transcricao and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO];
    $deficiencies = [Deficiencias::DEFICIENCIA_AUDITIVA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('guia interprete and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::GUIA_INTERPRETE];
    $deficiencies = [Deficiencias::SURDOCEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('guia interprete and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::GUIA_INTERPRETE];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('tradutor interprete and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
    $deficiencies = [Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('tradutor interprete and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('tradutor interprete and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS];
    $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('leitura labial and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
    $deficiencies = [Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('leitura labial and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('leitura labial and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::LEITURA_LABIAL];
    $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova ampliada fonte18 and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
    $deficiencies = [Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova ampliada fonte18 and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova ampliada fonte18 and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18];
    $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova super ampliada fonte24 and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
    $deficiencies = [Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova super ampliada fonte24 and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::BAIXA_VISAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova super ampliada fonte24 and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24];
    $deficiencies = [Deficiencias::DEFICIENCIA_FISICA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('cd com audio and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
    $deficiencies = [Deficiencias::CEGUEIRA, Deficiencias::VISAO_MONOCULAR];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('cd com audio and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
    $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('cd com audio and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL];
    $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova lingua portuguesa segunda lingua and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
    $deficiencies = [Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova lingua portuguesa segunda lingua and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
    $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova lingua portuguesa segunda lingua and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS];
    $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova video libras and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
    $deficiencies = [Deficiencias::SURDEZ];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova video libras and allowed and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
    $deficiencies = [Deficiencias::SURDEZ, Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('prova video libras and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS];
    $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('material didatico braille and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::MATERIAL_DIDATICO_EM_BRAILLE];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova braille and allowed deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_EM_BRAILLE];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('prova braille and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::PROVA_EM_BRAILLE];
    $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('nenhum and forbidden deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::NENHUM];
    $deficiencies = [Deficiencias::CEGUEIRA];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Recursos necessários para realização de provas foi preenchido incorretamente', $validator->getMessage());
});

test('nenhum and neutral deficiency choosed', function () {
    $resources = [RecursosRealizacaoProvas::NENHUM];
    $deficiencies = [Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO];
    $validator = new InepExamValidator($resources, $deficiencies);

    $this->assertTrue($validator->isValid());
});

test('material didatico em braille exige cegueira ou surdocegueira', function () {
    $validator = new InepExamValidator([RecursosRealizacaoProvas::MATERIAL_DIDATICO_EM_BRAILLE], [Deficiencias::BAIXA_VISAO]);

    $this->assertFalse($validator->isValid());
});
