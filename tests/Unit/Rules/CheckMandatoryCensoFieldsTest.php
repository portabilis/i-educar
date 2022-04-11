<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckMandatoryCensoFields;
use Tests\TestCase;

class CheckMandatoryCensoFieldsTest extends TestCase
{
    public function testCampoEstruturaCurricularValidaPreenchimentoTipodeAtendimento()
    {
        $mandatoryFields = new CheckMandatoryCensoFields();

        $param = new \stdClass();
        $param->tipo_atendimento = '0';
        $param->estrutura_curricular = null;

        $result = $mandatoryFields->validaCampoEstruturaCurricular($param);

        $expectedMessage = 'Campo "Estrutura Curricular" é obrigatório quando o campo tipo de atentimento é "Escolarização".';

        $this->assertEquals($expectedMessage, $mandatoryFields->message());
        $this->assertFalse($result);
    }
}
