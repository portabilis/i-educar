<?php

namespace Tests\Unit;

use iEducar\Modules\Navigation\Breadcrumb;
use Tests\TestCase;

class BreadCrumbTest extends TestCase
{
    /**
     * @var Breadcrumb
     */
    private $breadCrumbObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->breadCrumbObject = new Breadcrumb();
    }

    public function testTitleWithoutParent()
    {
        $this->markTestSkipped();
        $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb('Página teste', []);
        $this->assertStringContainsString('Página teste', $htmlBreadCrumb);
    }

    public function testTitleAndOneParent()
    {
        $this->markTestSkipped();
        $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb('Página teste', ['arquivo_teste.php' => 'Arquivo Teste']);
        $this->assertStringContainsString('Página teste', $htmlBreadCrumb);
        $this->assertStringContainsString('arquivo_teste.php', $htmlBreadCrumb);
        $this->assertStringContainsString('Arquivo Teste', $htmlBreadCrumb);
    }

    public function testTitleAndMultipleParents()
    {
        $this->markTestSkipped();
        $htmlBreadCrumb = $this->breadCrumbObject->makeBreadcrumb(
            'Página teste',
            [
                'arquivo1.php' => 'Arquivo 1',
                'arquivo2.php' => 'Arquivo 2'
            ]
        );

        $this->assertStringContainsString('arquivo1.php', $htmlBreadCrumb);
        $this->assertStringContainsString('Arquivo 1', $htmlBreadCrumb);
        $this->assertStringContainsString('arquivo2.php', $htmlBreadCrumb);
        $this->assertStringContainsString('Arquivo 2', $htmlBreadCrumb);
    }
}
