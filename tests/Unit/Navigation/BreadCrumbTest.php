<?php

use iEducar\Modules\Navigation\Breadcrumb;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    /**
     * @var Breadcrumb
     */
    $this->breadCrumbObject = new Breadcrumb;
});

test('title without parent', function () {
    $this->markTestSkipped();
    $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb('Página teste', []);
    $this->assertStringContainsString('Página teste', $htmlBreadCrumb);
});

test('title and one parent', function () {
    $this->markTestSkipped();
    $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb('Página teste', ['arquivo_teste.php' => 'Arquivo Teste']);
    $this->assertStringContainsString('Página teste', $htmlBreadCrumb);
    $this->assertStringContainsString('arquivo_teste.php', $htmlBreadCrumb);
    $this->assertStringContainsString('Arquivo Teste', $htmlBreadCrumb);
});

test('title and multiple parents', function () {
    $this->markTestSkipped();
    $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb(
        'Página teste',
        [
            'arquivo1.php' => 'Arquivo 1',
            'arquivo2.php' => 'Arquivo 2',
        ]
    );

    $this->assertStringContainsString('arquivo1.php', $htmlBreadCrumb);
    $this->assertStringContainsString('Arquivo 1', $htmlBreadCrumb);
    $this->assertStringContainsString('arquivo2.php', $htmlBreadCrumb);
    $this->assertStringContainsString('Arquivo 2', $htmlBreadCrumb);
});
