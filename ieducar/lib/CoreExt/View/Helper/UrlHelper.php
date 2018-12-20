<?php

require_once 'CoreExt/View/Helper/Abstract.php';

class CoreExt_View_Helper_UrlHelper extends CoreExt_View_Helper_Abstract
{
    /**
     * Constantes para definir que parte da URL deve ser gerada no método
     * url().
     */
    const URL_SCHEME = 1;
    const URL_HOST = 4;
    const URL_PORT = 16;
    const URL_USER = 32;
    const URL_PASS = 64;
    const URL_PATH = 128;
    const URL_QUERY = 128;
    const URL_FRAGMENT = 256;

    /**
     * @var array
     */
    private $_components = [
        'scheme' => self::URL_SCHEME,
        'host' => self::URL_HOST,
        'port' => self::URL_PORT,
        'user' => self::URL_USER,
        'pass' => self::URL_PASS,
        'path' => self::URL_PATH,
        'query' => self::URL_QUERY,
        'fragment' => self::URL_FRAGMENT
    ];

    /**
     * URL base para a geração de url absoluta.
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * Schema padrão para a geração de url absoluta.
     *
     * @var string
     */
    protected $_schemeUrl = 'http://';

    /**
     * Construtor singleton.
     */
    protected function __construct()
    {
    }

    /**
     * Retorna uma instância singleton.
     *
     * @return CoreExt_View_Helper_Abstract
     */
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    /**
     * Setter para $_baseUrl.
     *
     * @param string $baseUrl
     */
    public static function setBaseUrl($baseUrl)
    {
        $instance = self::getInstance();
        $instance->_baseUrl = $baseUrl;
    }

    /**
     * Retorna uma URL formatada. Interface externa.
     *
     * A geração da URL é bastante dinâmica e simples, já que aceita tanto
     * caminhos absolutos ou relativos.
     *
     * As opções para o array $options são:
     * - absolute: gera uma URL absoluta
     * - query: array associativo para criar uma query string (ver ex.)
     * - fragment: adiciona um fragmento ao final da URL
     * - components: um valor numérico que define até que componente deverá ser
     * retornado na URL parseada. Uma valor de URL_HOST, por exemplo, retornaria
     * os componentes URL_FRAGMENT e URL_HOST. Veja valores das constantes URL_*
     * para saber qual o nível de detalhe desejado
     *
     * Exemplo:
     * <code>
     * <?php
     * $options = array(
     *   'absolute' => TRUE,
     *   'query' => array('param1' => 'value1', 'param2' => 'value2'),
     *   'fragment' => 'Fragment',
     *   'components' => CoreExt_View_Helper_UrlHelper::URL_HOST
     * );
     * // http://example.com/index?param1=value1&param2=value2#Fragment
     *
     * $options = array(
     *   'absolute' => TRUE,
     *   'query' => array('param1' => 'value1', 'param2' => 'value2'),
     *   'fragment' => 'Fragment',
     *   'components' => CoreExt_View_Helper_UrlHelper::URL_HOST
     * );
     * print CoreExt_View_Helper_UrlHelper::url('example.com/index', $options);
     * // http://example.com
     * </code>
     *
     * @param string $path    O caminho relativo ou absoluto da URL
     * @param array  $options Opções para geração da URL
     *
     * @return string
     */
    public static function url($path, array $options = [])
    {
        $instance = self::getInstance();

        return $instance->_url($path, $options);
    }

    /**
     * Retorna uma URL formatada. Veja a documentação de url().
     *
     * @param string $path
     * @param array  $options
     *
     * @return string
     */
    protected function _url($path, array $options = [])
    {
        $url = [
            'scheme' => '',
            'host' => '',
            'user' => '',
            'pass' => '',
            'path' => '',
            'query' => '',
            'fragment' => ''
        ];

        $parsedUrl = parse_url($path);
        $url = array_merge($url, $parsedUrl);

        // Adiciona "://" caso o scheme seja parseado (caso das URLs absolutas implícitas)
        if ('' != $url['scheme']) {
            $url['scheme'] = $url['scheme'] . '://';
        }

        // Opções do método
        if (isset($options['absolute'])) {
            $url['scheme'] = $url['scheme'] != '' ? $url['scheme'] : $this->_schemeUrl;
            $url['host'] = $url['host'] != '' ? $url['host'] : $this->_baseUrl . '/';
        }

        if (isset($options['query'])) {
            $url['query'] = '?' . http_build_query($options['query']);
        }
        if (isset($options['fragment'])) {
            $url['fragment'] = '#' . (string) $options['fragment'];
        }

        // Remove da URL final os componentes que tem valor maior que o especificado
        // por 'components'.
        if (isset($options['components'])) {
            foreach ($this->_components as $key => $value) {
                if ($value > $options['components']) {
                    unset($url[$key]);
                }
            }
        }

        return implode('', $url);
    }

    /**
     * Retorna um link HTML simples. Interface externa.
     *
     * @param string $text    O texto a ser apresentado como link HTML
     * @param string $path    O caminho relativo ou absoluto da URL do link
     * @param array  $options Opções para gerar a URL do link
     *
     * @return string
     */
    public static function l($text, $path, array $options = [])
    {
        $instance = self::getInstance();

        return $instance->_link($text, $path, $options);
    }

    /**
     * Retorna um link HTML simples.
     *
     * @param string $text    O texto a ser apresentado como link HTML
     * @param string $path    O caminho relativo ou absoluto da URL do link
     * @param array  $options Opções para gerar a URL do link
     *
     * @return string
     */
    protected function _link($text, $path, array $options = [])
    {
        return sprintf('<a href="%s">%s</a>', self::url($path, $options), $text);
    }
}
