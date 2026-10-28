<?php

beforeEach(function () {
    CoreExt_View_Helper_UrlHelper::setBaseUrl('');
});

test('cria url relativa', function () {
    $expected = 'index.php';
    $this->assertEquals($expected, CoreExt_View_Helper_UrlHelper::url('index.php'));
});

test('cria url relativa com querystring', function () {
    $expected = 'index.php?param1=value1';
    $this->assertEquals(
        $expected,
        CoreExt_View_Helper_UrlHelper::url(
            'index.php',
            ['query' => ['param1' => 'value1']]
        )
    );
});

test('cria url relativa com fragmento', function () {
    $expected = 'index.php#fragment';
    $this->assertEquals(
        $expected,
        CoreExt_View_Helper_UrlHelper::url(
            'index.php',
            ['fragment' => 'fragment']
        )
    );
});

test('cria url relativa com querystring e fragmento', function () {
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
});

test('cria url absoluta com hostname configurado', function () {
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
});

test('cria url absoluta com hostname implicito', function () {
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
});

test('url retorna apenas scheme e host', function () {
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
});

test('url retorna com path', function () {
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
});

test('cria link com url relativa', function () {
    $expected = '<a href="index.php?param1=value1">Index</a>';
    $this->assertEquals(
        $expected,
        CoreExt_View_Helper_UrlHelper::l(
            'Index',
            'index.php',
            ['query' => ['param1' => 'value1']]
        )
    );
});

test('cria link com url absoluta implicita', function () {
    $expected = '<a href="http://localhost/index.php?param1=value1">Index</a>';
    $this->assertEquals(
        $expected,
        CoreExt_View_Helper_UrlHelper::l(
            'Index',
            'http://localhost/index.php',
            ['query' => ['param1' => 'value1']]
        )
    );
});
