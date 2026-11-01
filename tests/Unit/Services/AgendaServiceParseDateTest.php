<?php

namespace Tests\Unit\Services;

use App\Services\AgendaService;
use ReflectionMethod;
use Tests\TestCase;

class AgendaServiceParseDateTest extends TestCase
{
    private AgendaService $service;
    private ReflectionMethod $method;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AgendaService();
        $this->method = new ReflectionMethod(AgendaService::class, 'parseDate');
        $this->method->setAccessible(true);
    }

    private function parseDate(?string $date): ?string
    {
        return $this->method->invoke($this->service, $date);
    }

    public function test_data_valida_tipica(): void
    {
        $this->assertSame('2026-06-15', $this->parseDate('15/06/2026'));
    }

    public function test_data_valida_limite_inferior_ano(): void
    {
        $this->assertSame('0001-01-01', $this->parseDate('01/01/0001'));
    }

    public function test_data_valida_29_fevereiro_ano_bissexto(): void
    {
        $this->assertSame('2024-02-29', $this->parseDate('29/02/2024'));
    }

    public function test_entrada_null_retorna_null(): void
    {
        $this->assertNull($this->parseDate(null));
    }

    public function test_sem_barra_separadora_retorna_null(): void
    {
        $this->assertNull($this->parseDate('15062026'));
    }

    public function test_barras_em_excesso_retorna_null(): void
    {
        $this->assertNull($this->parseDate('15/06/20/26'));
    }

    public function test_ano_com_dois_digitos_retorna_null(): void
    {
        $this->assertNull($this->parseDate('15/06/26'));
    }

    public function test_ano_com_cinco_digitos_retorna_null(): void
    {
        $this->assertNull($this->parseDate('15/06/20260'));
    }

    public function test_dia_inexistente_no_mes_retorna_null(): void
    {
        $this->assertNull($this->parseDate('30/02/2026'));
    }

    public function test_29_fevereiro_ano_nao_bissexto_retorna_null(): void
    {
        $this->assertNull($this->parseDate('29/02/2025'));
    }

    public function test_mcdc_condicao_A_verdadeiro_sem_barra(): void
    {
        $this->assertNull($this->parseDate('15062026'));
    }

    public function test_mcdc_condicao_B_verdadeiro_ano_dois_digitos(): void
    {
        $this->assertNull($this->parseDate('15/06/26'));
    }

    public function test_mcdc_condicao_C_verdadeiro_data_inexistente(): void
    {
        $this->assertNull($this->parseDate('30/02/2026'));
    }

    public function test_mcdc_todas_condicoes_falsas_retorna_data_formatada(): void
    {
        $this->assertSame('2026-06-15', $this->parseDate('15/06/2026'));
    }
}
