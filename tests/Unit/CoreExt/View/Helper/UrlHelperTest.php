<?php

class CoreExt_View_UrlHelperTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        CoreExt_View_Helper_UrlHelper::setBaseUrl('');
    }

    public function testCriaUrlRelativa()
    {
        $expected = 'index.php';
        $this->assertEquals($expected, CoreExt_View_Helper_UrlHelper::url('index.php'));
    }

    public function testCriaUrlRelativaComQuerystring()
    {
        $expected = 'index.php?param1=value1';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'index.php',
                ['query' => ['param1' => 'value1']]
            )
        );
    }

    public function testCriaUrlRelativaComFragmento()
    {
        $expected = 'index.php#fragment';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'index.php',
                ['fragment' => 'fragment']
            )
        );
    }

    public function testCriaUrlRelativaComQuerystringEFragmento()
    {
        $expected = 'index.php?param1=value1#fragment';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'index.php',
                [
                    'query' => ['param1' => 'value1'],
                    'fragment' => 'fragment'
                ]
            )
        );
    }

    public function testCriaUrlAbsolutaComHostnameConfigurado()
    {
        CoreExt_View_Helper_UrlHelper::setBaseUrl('localhost');
        $expected = 'http://localhost/index.php?param1=value1#fragment';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'index.php',
                [
                    'query' => ['param1' => 'value1'],
                    'fragment' => 'fragment',
                    'absolute' => true
                ]
            )
        );
    }

    public function testCriaUrlAbsolutaComHostnameImplicito()
    {
        $expected = 'http://localhost/index.php?param1=value1#fragment';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'http://localhost/index.php',
                [
                    'query' => ['param1' => 'value1'],
                    'fragment' => 'fragment',
                ]
            )
        );
    }

    public function testUrlRetornaApenasSchemeEHost()
    {
        $expected = 'http://www.example.com';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'http://www.example.com/controller/name',
                [
                    'absolute' => true,
                    'components' => CoreExt_View_Helper_UrlHelper::URL_SCHEME +
                        CoreExt_View_Helper_UrlHelper::URL_HOST
                ]
            )
        );
    }

    public function testUrlRetornaComPath()
    {
        $expected = 'http://www.example.com/controller';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'http://www.example.com/controller',
                [
                    'absolute' => true,
                    'components' => CoreExt_View_Helper_UrlHelper::URL_PATH
                ]
            )
        );
    }

    public function testCriaLinkComUrlRelativa()
    {
        $expected = '<a href="index.php?param1=value1">Index</a>';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::l(
                'Index',
                'index.php',
                ['query' => ['param1' => 'value1']]
            )
        );
    }

    public function testCriaLinkComUrlAbsolutaImplicita()
    {
        $expected = '<a href="http://localhost/index.php?param1=value1">Index</a>';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::l(
                'Index',
                'http://localhost/index.php',
                ['query' => ['param1' => 'value1']]
            )
        );
    }
}
