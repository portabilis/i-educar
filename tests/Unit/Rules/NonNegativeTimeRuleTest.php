<?php

namespace Tests\Unit\Rules;

use App\Rules\NonNegativeTimeRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NonNegativeTimeRuleTest extends TestCase
{
    public function test_rejeita_horas_negativas(): void
    {
        $validator = Validator::make(
            ['qtd_horas' => -2],
            ['qtd_horas' => [new NonNegativeTimeRule()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_rejeita_minutos_negativos(): void
    {
        $validator = Validator::make(
            ['qtd_min' => -15],
            ['qtd_min' => [new NonNegativeTimeRule()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_aceita_valores_iguais_a_zero(): void
    {
        $validator = Validator::make(
            [
                'qtd_horas' => 0,
                'qtd_min' => 0,
            ],
            [
                'qtd_horas' => [new NonNegativeTimeRule()],
                'qtd_min' => [new NonNegativeTimeRule()],
            ]
        );

        $this->assertTrue($validator->passes());
    }
}
