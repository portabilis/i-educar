<?php

namespace Tests\Unit\Models\Enums;

use App\Models\Enums\PersonLinkType;
use PHPUnit\Framework\TestCase;

class PersonLinkTypeTest extends TestCase
{
    public function test_descriptive_values(): void
    {
        $this->assertSame([
            'student' => 'Aluno',
            'employee' => 'Servidor',
            'mother' => 'Mãe da Pessoa Física',
            'father' => 'Pai da Pessoa Física',
            'responsible' => 'Responsável da Pessoa Física',
        ], PersonLinkType::getDescriptiveValues()->all());
    }
}
