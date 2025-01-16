<?php

class ClsCalendarioTest extends PHPUnit\Framework\TestCase
{
    public function test_generate_form_values()
    {
        $formValues = [
            'formFieldKey' => 'formFieldValue',
        ];

        $calendario = new clsCalendario;

        // Teste sem permissão de troca de ano
        $html = $calendario->getCalendario(1, 2000, 'testGenerateFormValues', [], $formValues);

        $this->assertMatchesRegularExpression(
            '/<input id="cal_formFieldKey" name="formFieldKey" type="hidden" value="formFieldValue" \/>/',
            $html,
            '->getCalendario() gera campos extras de formulário.'
        );
    }
}
