<?php

namespace Tests\Unit;

use App\Rules\AbbreviationLength;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LegacyBondTypeTest extends TestCase
{
    public function test_abbreviation_cannot_be_longer_than_name()
    {
        $data = [
            'nm_vinculo' => 'Prof',
            'abreviatura' => 'Professor',
        ];

        $validator = Validator::make($data, [
            'nm_vinculo' => ['required', 'string'],
            'abreviatura' => ['required', 'string', function ($attribute, $value, $fail) use ($data) {
                if (strlen($value) > strlen($data['nm_vinculo'])) {
                    $fail('A abreviatura não pode ser maior que o nome da função.');
                }
            }],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertEquals('A abreviatura não pode ser maior que o nome da função.', $validator->errors()->first('abreviatura'));
    }

    public function test_abbreviation_length_rule()
    {
        $rule = new AbbreviationLength('Prof');

        $this->assertFalse($rule->passes('abreviatura', 'Professor'));
        $this->assertTrue($rule->passes('abreviatura', 'Prof'));
        $this->assertTrue($rule->passes('abreviatura', 'Pr'));
    }

    public function test_abbreviation_length_rule_with_empty_and_spaces()
    {
        $rule = new AbbreviationLength(' Prof ');

        $this->assertFalse($rule->passes('abreviatura', ' Professor '));

        $ruleEmpty = new AbbreviationLength('');

        $this->assertFalse($ruleEmpty->passes('abreviatura', 'A'));
        $this->assertTrue($ruleEmpty->passes('abreviatura', ''));
    }
}
