<?php

namespace Tests\Unit\App\Model;

use App_Model_Educacenso;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EducacensoTest extends TestCase
{
    public function test_a_etapa_unificada_e_a_3(): void
    {
        $this->assertSame([3], App_Model_Educacenso::etapasEnsinoUnificadas());
    }

    public static function etapas(): array
    {
        return [
            'educação infantil unificada' => [3, [1, 2]],
            'multietapa do fundamental' => [22, [14, 15, 16, 17, 18, 19, 20, 21, 41]],
            'multietapa infantil e fundamental' => [56, [1, 2, 14, 15, 16, 17, 18, 19, 20, 21, 41]],
            'curso técnico concomitante e subsequente' => [64, [30, 40]],
            'EJA fundamental' => [72, [69, 70]],
            'etapa única' => [14, []],
        ];
    }

    #[DataProvider('etapas')]
    public function test_lista_as_etapas_que_a_turma_multisseriada_atende(int $etapa, array $etapas): void
    {
        $this->assertSame($etapas, App_Model_Educacenso::etapasDaTurma($etapa));
    }
}
