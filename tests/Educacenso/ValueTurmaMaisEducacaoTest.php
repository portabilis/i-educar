<?php

namespace Tests\Educacenso;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\ValueTurmaMaisEducacao;
use Tests\TestCase;

class ValueTurmaMaisEducacaoTest extends TestCase
{
    public function test_turma_nao_presencial_value_null()
    {
        $value = $this->getValueObject();
        $value->setTipoMediacao(2);
        $this->assertNull($value->getValue());
    }

    public function test_dependencia_administrativa_invalido_value_null()
    {
        $value = $this->getValueObject();
        $value->setDependenciaAdministrativa(1);
        $this->assertNull($value->getValue());
    }

    public function test_tipo_atendimento_invalido_value_null()
    {
        $value = $this->getValueObject();
        $value->setTipoAtendimento(TipoAtendimentoTurma::CLASSE_HOSPITALAR);
        $this->assertNull($value->getValue());
    }

    public function test_tipo_atendimento_valido_modalidade_invalida_value_null()
    {
        $value = $this->getValueObject();
        $value->setTipoAtendimento(3);
        $value->setModalidade(ModalidadeCurso::EJA);
        $this->assertNull($value->getValue());
    }

    public function test_tipo_atendimento_valido_modalidade_valida_etapa_ensino_invalida_value_null()
    {
        $value = $this->getValueObject();
        $value->setTipoAtendimento(3);
        $value->setEtapaEnsino(3);
        $this->assertNull($value->getValue());
    }

    public function test_value_nao_nulo()
    {
        $value = $this->getValueObject();
        $this->assertEquals(1, $value->getValue());

        $value->setTurmaMaisEducacao(0);
        $this->assertEquals(0, $value->getValue());
    }

    /**
     * @return ValueTurmaMaisEducacao
     */
    private function getValueObject()
    {
        $valueObject = new ValueTurmaMaisEducacao;
        $valueObject->setDependenciaAdministrativa(DependenciaAdministrativaEscola::MUNICIPAL);
        $valueObject->setTipoAtendimento(TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR);
        $valueObject->setModalidade(1);
        $valueObject->setEtapaEnsino(4);
        $valueObject->setTipoMediacao(TipoMediacaoDidaticoPedagogico::PRESENCIAL);
        $valueObject->setTurmaMaisEducacao(1);

        return $valueObject;
    }
}
