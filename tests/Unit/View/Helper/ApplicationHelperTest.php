<?php

use Tests\TestCase;

uses(TestCase::class);

test('carrega o jQuery e o jQuery UI uma vez, com o noConflict e o estilo do autocomplete', function () {
    Portabilis_View_Helper_Application::forgetLoadedAssets();
    $tela = new class extends clsCadastro {};

    Portabilis_View_Helper_Application::loadJQueryLib($tela);
    Portabilis_View_Helper_Application::loadJQueryUiLib($tela);

    expect(Portabilis_View_Helper_Application::getInstance())->toBeInstanceOf(Portabilis_View_Helper_Application::class)
        ->and($tela->getAppendedOutput())
        ->toContain('scripts/jquery/jquery-1.8.3.min.js')
        ->toContain('jQuery.noConflict()')
        ->toContain('jquery-ui-1.9.2.custom.min.js')
        ->toContain('.ui-autocomplete { font-size: 11px; }');
});
