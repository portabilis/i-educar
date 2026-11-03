<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\LoginFirstUser;
use Tests\TestCase;

class EducarFaltaAtrasoValidationTest extends TestCase
{
    use DatabaseTransactions, LoginFirstUser, WithoutMiddleware;

    public const ROUTE = '/intranet/educar_falta_atraso_cad.php';

    // Teste 1: rejeita cadastro com horas negativas
    public function test_rejeita_cadastro_com_horas_negativas(): void
    {
        $dados = [
            'tipoacao' => 'Novo',
            'tipo' => 1,
            'data_falta_atraso' => '15/10/2026',
            'qtd_horas' => -2,
            'qtd_min' => 10,
            'ref_cod_servidor' => 1,
            'ref_cod_escola' => 1,
            'ref_cod_instituicao' => auth()->user()->ref_cod_instituicao,
            'justificada' => 0,
        ];

        $url = self::ROUTE . '?ref_cod_servidor=1&ref_cod_escola=1&ref_cod_instituicao='
            . $dados['ref_cod_instituicao'];

        $response = $this->post($url, $dados);

        $response->assertSee(
            'A quantidade de horas e minutos não pode ser negativa.'
        );
    }

    // Teste 2: rejeita cadastro com minutos negativos
    public function test_rejeita_cadastro_com_minutos_negativos(): void
    {
        $dados = [
            'tipoacao' => 'Novo',
            'tipo' => 1,
            'data_falta_atraso' => '15/10/2026',
            'qtd_horas' => 2,
            'qtd_min' => -15,
            'ref_cod_servidor' => 1,
            'ref_cod_escola' => 1,
            'ref_cod_instituicao' => auth()->user()->ref_cod_instituicao,
            'justificada' => 0,
        ];

        $url = self::ROUTE . '?ref_cod_servidor=1&ref_cod_escola=1&ref_cod_instituicao='
            . $dados['ref_cod_instituicao'];

        $response = $this->post($url, $dados);

        $response->assertSee(
            'A quantidade de horas e minutos não pode ser negativa.'
        );
    }

    // Teste 3: rejeita tentativa de edição com minutos negativos
    public function test_rejeita_edicao_com_minutos_negativos(): void
    {
        $dados = [
            'tipoacao' => 'Editar',
            'cod_falta_atraso' => 99999999,
            'tipo' => 1,
            'data_falta_atraso' => '15/10/2026',
            'qtd_horas' => 2,
            'qtd_min' => -15,
            'ref_cod_servidor' => 1,
            'ref_cod_escola' => 1,
            'ref_cod_instituicao' => auth()->user()->ref_cod_instituicao,
            'justificada' => 0,
        ];

        $url = self::ROUTE . '?ref_cod_servidor=1&ref_cod_escola=1&ref_cod_instituicao='
            . $dados['ref_cod_instituicao'];

        $response = $this->post($url, $dados);

        $response->assertSee(
            'A quantidade de horas e minutos não pode ser negativa.'
        );
    }
}
