<?php

namespace Tests\Unit\Rules;

use App\Rules\FunctionAbbreviationRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FunctionAbbreviationRuleTest extends TestCase
{
    public function test_rejeita_abreviatura_maior_que_nome(): void
    {
        $validator = Validator::make(
            [
                'nm_funcao' => 'Prof',
                'abreviatura' => 'Professor',
            ],
            [
                'abreviatura' => [new FunctionAbbreviationRule()],
            ]
        );
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('abreviatura'));
    }
    

    public function test_aceita_abreviatura_menor_que_nome(): void
    {
        $validator = Validator::make(
            [
                'nm_funcao' => 'Professor',
                'abreviatura' => 'Prof',
            ],
            [
                'abreviatura' => [new FunctionAbbreviationRule()],
            ]
        );
        $this->assertTrue($validator->passes());
    }

    public function test_aceita_abreviatura_igual_ao_nome(): void
    {
        $validator = Validator::make(
            [
                'nm_funcao' => 'Prof',
                'abreviatura' => 'Prof',
            ],
            [
                'abreviatura' => [new FunctionAbbreviationRule()],    
            ]
        );
        $this->assertTrue($validator->passes());
    }
}