<?php

class CoreExt_View_UrlHelperTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        CoreExt_View_Helper_UrlHelper::setBaseUrl('');
    }

    public function test_cria_url_relativa()
    {
        $expected = 'index.php';
        $this->assertEquals($expected, CoreExt_View_Helper_UrlHelper::url('index.php'));
    }

    public function test_cria_url_relativa_com_querystring()
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

    public function test_cria_url_relativa_com_fragmento()
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

    public function test_cria_url_relativa_com_querystring_e_fragmento()
    {
        $expected = 'index.php?param1=value1#fragment';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'index.php',
                [
                    'query' => ['param1' => 'value1'],
                    'fragment' => 'fragment',
                ]
            )
        );
    }

    public function test_cria_url_absoluta_com_hostname_configurado()
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
                    'absolute' => true,
                ]
            )
        );
    }

    public function test_cria_url_absoluta_com_hostname_implicito()
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

    public function test_url_retorna_apenas_scheme_e_host()
    {
        $expected = 'http://www.example.com';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'http://www.example.com/controller/name',
                [
                    'absolute' => true,
                    'components' => CoreExt_View_Helper_UrlHelper::URL_SCHEME +
                        CoreExt_View_Helper_UrlHelper::URL_HOST,
                ]
            )
        );
    }

    public function test_url_retorna_com_path()
    {
        $expected = 'http://www.example.com/controller';
        $this->assertEquals(
            $expected,
            CoreExt_View_Helper_UrlHelper::url(
                'http://www.example.com/controller',
                [
                    'absolute' => true,
                    'components' => CoreExt_View_Helper_UrlHelper::URL_PATH,
                ]
            )
        );
    }

    public function test_cria_link_com_url_relativa()
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

    public function test_cria_link_com_url_absoluta_implicita()
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
