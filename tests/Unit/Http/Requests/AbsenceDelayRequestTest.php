<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\AbsenceDelayRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Testes unitários para validação de Falta/Atraso de Servidor
 *
 * Esta suite de testes implementa a metodologia TDD (Test-Driven Development)
 * para garantir a validação correta dos campos de quantidade de horas e minutos
 * no formulário de cadastro de falta/atraso de servidor.
 *
 * Ciclo TDD seguido:
 * 1. RED: Escrever teste que falha
 * 2. GREEN: Implementar código mínimo para passar
 * 3. REFACTOR: Melhorar código mantendo testes verdes
 *
 * Regra de negócio testada:
 * - Os campos qtd_horas e qtd_min devem aceitar apenas valores >= 0
 * - Valores negativos devem ser rejeitados com mensagem de erro clara
 * - Valores zero e positivos devem ser aceitos
 *
 * @package Tests\Unit\Http\Requests
 * @author Sistema I-Educar
 * @version 1.0.0
 */
class AbsenceDelayRequestTest extends TestCase
{
    /**
     * CICLO 1 - GREEN
     * Teste: Horas não podem ser negativas
     *
     * Este teste valida que o campo qtd_horas não aceita valores negativos.
     * Comportamento esperado: Validação deve falhar com mensagem de erro específica.
     *
     * @return void
     */
    public function test_horas_nao_podem_ser_negativas()
    {
        // Arrange - Preparar os dados de teste com horas negativas
        $request = new AbsenceDelayRequest();
        $rules = $request->rules();

        $data = [
            'tipo' => 1,
            'qtd_horas' => -5,
            'qtd_min' => 0,
            'data_falta_atraso' => '01/01/2024',
            'ref_cod_servidor_funcao' => 1,
        ];

        // Act - Executar a validação
        $validator = Validator::make($data, $rules, $request->messages());

        // Assert - Verificar que a validação falhou
        $this->assertTrue(
            $validator->fails(),
            'A validação deveria falhar para horas negativas'
        );

        // Verificar que existe erro específico no campo qtd_horas
        $this->assertTrue(
            $validator->errors()->has('qtd_horas'),
            'Deveria existir erro no campo qtd_horas'
        );
    }

    /**
     * CICLO 2 - GREEN
     * Teste: Minutos não podem ser negativos
     *
     * Este teste valida que o campo qtd_min não aceita valores negativos.
     * Comportamento esperado: Validação deve falhar com mensagem de erro específica.
     *
     * @return void
     */
    public function test_minutos_nao_podem_ser_negativos()
    {
        // Arrange - Preparar os dados de teste com minutos negativos
        $request = new AbsenceDelayRequest();
        $rules = $request->rules();

        $data = [
            'tipo' => 1,
            'qtd_horas' => 0,
            'qtd_min' => -30,
            'data_falta_atraso' => '01/01/2024',
            'ref_cod_servidor_funcao' => 1,
        ];

        // Act - Executar a validação
        $validator = Validator::make($data, $rules, $request->messages());

        // Assert - Verificar que a validação falhou
        $this->assertTrue(
            $validator->fails(),
            'A validação deveria falhar para minutos negativos'
        );

        // Verificar que existe erro específico no campo qtd_min
        $this->assertTrue(
            $validator->errors()->has('qtd_min'),
            'Deveria existir erro no campo qtd_min'
        );
    }

    /**
     * CICLO 3 - GREEN
     * Teste: Valores positivos ou zero são válidos
     *
     * Este teste valida que o sistema aceita valores válidos (positivos ou zero)
     * para os campos qtd_horas e qtd_min.
     * Comportamento esperado: Validação deve passar sem erros.
     *
     * @return void
     */
    public function test_valores_positivos_ou_zero_sao_validos()
    {
        // Arrange - Preparar os dados de teste com valores válidos
        $request = new AbsenceDelayRequest();
        $rules = $request->rules();

        // Teste 1: Valores positivos normais
        $data1 = [
            'tipo' => 1,
            'qtd_horas' => 10,
            'qtd_min' => 59,
            'data_falta_atraso' => '01/01/2024',
            'ref_cod_servidor_funcao' => 1,
        ];

        // Teste 2: Valores zero
        $data2 = [
            'tipo' => 1,
            'qtd_horas' => 0,
            'qtd_min' => 0,
            'data_falta_atraso' => '01/01/2024',
            'ref_cod_servidor_funcao' => 1,
        ];

        // Act - Executar as validações
        $validator1 = Validator::make($data1, $rules, $request->messages());
        $validator2 = Validator::make($data2, $rules, $request->messages());

        // Assert - Verificar que as validações passaram
        $this->assertFalse(
            $validator1->fails(),
            'A validação deveria passar para valores positivos válidos'
        );

        $this->assertFalse(
            $validator2->fails(),
            'A validação deveria passar para valores zero'
        );
    }
}
