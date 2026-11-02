<?php

namespace Tests\Unit\Services;

use App\Services\AgendaService;
use ReflectionMethod;
use Tests\TestCase;

class AgendaServiceFormatDateTDDTest extends TestCase
{
    private AgendaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AgendaService();
    }

    private function formatDateForDisplay(?string $date): ?string
    {
        $method = new ReflectionMethod(AgendaService::class, 'formatDateForDisplay');
        $method->setAccessible(true);
        return $method->invoke($this->service, $date);
    }

    // Ciclo 1: converte aaaa-mm-dd → dd/mm/aaaa
    public function test_converte_data_banco_para_exibicao(): void
    {
        $this->assertSame('15/06/2026', $this->formatDateForDisplay('2026-06-15'));
    }

    // Ciclo 2: entrada inválida/null retorna null
    public function test_entrada_null_retorna_null(): void
    {
        $this->assertNull($this->formatDateForDisplay(null));
    }

    public function test_formato_invalido_retorna_null(): void
    {
        $this->assertNull($this->formatDateForDisplay('15/06/2026'));
    }
}
