<?php


class ClsCalendarioTest extends PHPUnit\Framework\TestCase
{
  public function testGenerateFormValues()
  {
    $formValues = array(
      'formFieldKey' => 'formFieldValue'
    );

    $calendario = new clsCalendario();

    // Teste sem permissão de troca de ano
    $html = $calendario->getCalendario(1, 2000, 'testGenerateFormValues', array(), $formValues);

    $this->assertRegExp(
      '/<input id="cal_formFieldKey" name="formFieldKey" type="hidden" value="formFieldValue" \/>/',
      $html, '->getCalendario() gera campos extras de formulário.'
    );
  }
}
