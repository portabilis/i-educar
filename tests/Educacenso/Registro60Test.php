<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro60;
use App_Model_LocalFuncionamentoDiferenciado;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\PoderPublicoTransporte;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use Tests\TestCase;

class Registro60Test extends TestCase
{
    private function aluno(array $attributes): Registro60
    {
        $aluno = new Registro60;

        foreach ($attributes as $key => $value) {
            $aluno->$key = $value;
        }

        return $aluno;
    }

    public function test_transporte_publico_e_exigido_de_quem_mora_no_brasil_e_estuda_presencialmente_em_turma_curricular(): void
    {
        $aluno = $this->aluno([
            'tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO],
            'tipoMediacaoTurma' => TipoMediacaoDidaticoPedagogico::PRESENCIAL,
            'paisResidenciaAluno' => PaisResidencia::BRASIL,
        ]);

        $this->assertTrue($aluno->transportePublicoRequired());

        $aluno->paisResidenciaAluno = PaisResidencia::ARGENTINA;

        $this->assertFalse($aluno->transportePublicoRequired());
    }

    public function test_o_veiculo_e_exigido_do_transporte_municipal_ou_estadual(): void
    {
        $this->assertTrue($this->aluno(['transportePublico' => PoderPublicoTransporte::MUNICIPAL])->veiculoTransporteEscolarRequired());
        $this->assertFalse($this->aluno(['transportePublico' => null])->veiculoTransporteEscolarRequired());
    }

    public function test_reconhece_turma_de_atividade_complementar_ou_aee(): void
    {
        $this->assertTrue($this->aluno(['tipoAtendimentoTurma' => [TipoAtendimentoTurma::AEE]])->isAtividadeComplementarOrAee());
        $this->assertFalse($this->aluno(['tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO]])->isAtividadeComplementarOrAee());
    }

    public function test_escolarizacao_em_outro_espaco_e_exigida_na_turma_presencial_fora_de_local_diferenciado(): void
    {
        $aluno = $this->aluno([
            'tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO],
            'tipoMediacaoTurma' => TipoMediacaoDidaticoPedagogico::PRESENCIAL,
            'localFuncionamentoDiferenciadoTurma' => App_Model_LocalFuncionamentoDiferenciado::NAO_ESTA,
        ]);

        $this->assertTrue($aluno->recebeEscolarizacaoOutroEspacoIsRequired());
    }

    public function test_itinerario_nao_preenchido_ate_marcar_um_tipo_e_uma_composicao(): void
    {
        $aluno = new Registro60;

        $this->assertTrue($aluno->tipoItinerarioNaoPreenchido());
        $this->assertTrue($aluno->composicaoItinerarioNaoPreenchido());

        $aluno->tipoItinerarioIntegrado = 1;
        $aluno->composicaoItinerarioFormacaoTecnica = 1;

        $this->assertFalse($aluno->tipoItinerarioNaoPreenchido());
        $this->assertFalse($aluno->composicaoItinerarioNaoPreenchido());
    }
}
