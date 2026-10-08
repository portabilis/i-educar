<?php

namespace Tests\Unit\Services;

use App\Services\IndividualCpfTimelineService;
use Tests\TestCase;

class IndividualCpfTimelineServiceTest extends TestCase
{
    public function test_summary_describes_each_kind_of_change(): void
    {
        $service = app(IndividualCpfTimelineService::class);

        $this->assertSame('Informou o CPF 123.456.789-09', $service->summary(['after' => ['CPF' => '123.456.789-09']]));
        $this->assertSame('Marcou "Não possui" com o motivo Estrangeiro', $service->summary(['after' => ['Motivo da ausência' => 'Estrangeiro']]));
        $this->assertSame('Removeu o CPF', $service->summary(['after' => ['CPF' => null]]));
        $this->assertSame('Removeu o CPF', $service->summary([]));
    }
}
