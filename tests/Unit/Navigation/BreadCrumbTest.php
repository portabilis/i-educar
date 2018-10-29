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

    public function setUp()
    {
        parent::setUp();
        $this->breadCrumbObject = new Breadcrumb();
    }

    public function testWithoutMakeShouldReturnEmpty()
    {
        $htmlBreadCrumb = $this->breadCrumbObject->getHtmlBreadcrumb();
        $this->assertEmpty($htmlBreadCrumb);
    }

    public function testTitleWithoutParent()
    {
        $this->breadCrumbObject->makeBreadcrumb('Página teste', []);
        $htmlBreadCrumb = $this->breadCrumbObject->getHtmlBreadcrumb();
        $this->assertContains('Página teste', $htmlBreadCrumb);
    }

    public function testTitleAndOneParent()
    {
        $this->breadCrumbObject->makeBreadcrumb('Página teste', ['arquivo_teste.php' => 'Arquivo Teste']);
        $htmlBreadCrumb = $this->breadCrumbObject->getHtmlBreadcrumb();
        $this->assertContains('Página teste', $htmlBreadCrumb);
        $this->assertContains('arquivo_teste.php', $htmlBreadCrumb);
        $this->assertContains('Arquivo Teste', $htmlBreadCrumb);
    }

    public function testTitleAndMultipleParents()
    {
        $this->breadCrumbObject->makeBreadcrumb('Página teste', [
                'arquivo1.php' => 'Arquivo 1',
                'arquivo2.php' => 'Arquivo 2'
            ]
        );

        $htmlBreadCrumb = $this->breadCrumbObject->getHtmlBreadcrumb();
        $this->assertContains('arquivo1.php', $htmlBreadCrumb);
        $this->assertContains('Arquivo 1', $htmlBreadCrumb);
        $this->assertContains('arquivo2.php', $htmlBreadCrumb);
        $this->assertContains('Arquivo 2', $htmlBreadCrumb);
    }
}
