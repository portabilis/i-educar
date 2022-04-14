<?php

class CoreExt_Controller_Request implements CoreExt_Controller_Request_Interface
{
    protected $_options = [
        'baseurl' => null
    ];

    protected $_data = [];

    /**
     * Construtor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['data'])) {
            $this->_data = $options['data'];
            unset($options['data']);
        }

        $this->setOptions($options);
    }

    /**
     * @see CoreExt_Configurable#setOptions($options)
     */
    public function setOptions(array $options = [])
    {
        $defaultOptions = array_keys($this->getOptions());
        $passedOptions = array_keys($options);

        if (0 < count(array_diff($passedOptions, $defaultOptions))) {
            throw new InvalidArgumentException(
                sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
            );
        }

        $this->_options = array_merge($this->getOptions(), $options);

        return $this;
    }

    /**
     * @see CoreExt_Configurable#getOptions()
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Verifica se uma opção está setada.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function _hasOption($key)
    {
        return array_key_exists($key, $this->_options);
    }

    /**
     * Retorna um valor de opção de configuração ou NULL caso a opção não esteja
     * setada.
     *
     * @param string $key
     *
     * @return mixed|NULL
     */
    protected function _getOption($key)
    {
        return $this->_hasOption($key) ? $this->_options[$key] : null;
    }

    /**
     * Implementação do método mágico __get().
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Getter para as variáveis de requisição.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        switch (true) {
            case isset($this->_data[$key]):
                return $this->_data[$key];

            case isset($_GET[$key]):
                return $_GET[$key];

            case isset($_POST[$key]):
                return $_POST[$key];

            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];

            case isset($_SERVER[$key]):
                return $_SERVER[$key];

            default:
                break;
        }

        return null;
    }

    /**
     * Implementação do método mágico __isset().
     *
     * @link http://php.net/manual/en/language.oop5.overloading.php
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        $val = $this->$key;

        return isset($val);
    }

    /**
     * Setter para a opção de configuração baseurl.
     *
     * @param string $url
     *
     * @return CoreExt_Controller_Request_Interface Provê interface fluída
     */
    public function setBaseurl($url)
    {
        $this->setOptions(['baseurl' => $url]);

        return $this;
    }

    /**
     * Getter para a opção de configuração baseurl.
     *
     * Caso a opção não esteja configurada, determina um valor baseado na
     * variável $_SERVER['REQUEST_URI'] da requisição, usando apenas os
     * componentes scheme e path da URL. Veja {@link http://php.net/parse_url}
     * para mais informações sobre os componentes de uma URL.
     *
     * @return string
     */
    public function getBaseurl()
    {
        if (is_null($this->_getOption('baseurl'))) {
            $url = CoreExt_View_Helper_UrlHelper::url(
                $this->get('REQUEST_URI'),
                ['absolute' => true, 'components' => CoreExt_View_Helper_UrlHelper::URL_HOST]
            );

            $this->setBaseurl($url);
        }

        return $this->_getOption('baseurl');
    }
}
