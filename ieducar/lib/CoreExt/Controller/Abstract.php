<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once 'CoreExt/Controller/Interface.php';

abstract class CoreExt_Controller_Abstract implements CoreExt_Controller_Interface
{
    /**
     * Uma instância de CoreExt_Controller_Request_Interface
     *
     * @var CoreExt_Controller_Request_Interface
     */
    protected $_request = null;

    /**
     * Uma instância de CoreExt_Session_Abstract
     *
     * @var CoreExt_Session_Abstract
     */
    protected $_session = null;

    /**
     * Uma instância de CoreExt_Controller_Dispatcher_Interface
     *
     * @var CoreExt_Controller_Dispatcher_Interface
     */
    protected $_dispatcher = null;

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
    public function getOption($key)
    {
        return $this->_hasOption($key) ? $this->_options[$key] : null;
    }

    /**
     * Setter.
     *
     * @param CoreExt_Controller_Request_Interface $request
     *
     * @return CoreExt_Controller_Interface
     */
    public function setRequest(CoreExt_Controller_Request_Interface $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Getter para uma instância de CoreExt_Controller_Request_Interface.
     *
     * Instância via lazy initialization uma instância de
     * CoreExt_Controller_Request_Interface caso nenhuma seja explicitamente
     * atribuída a instância atual.
     *
     * @return CoreExt_Controller_Request_Interface
     */
    public function getRequest()
    {
        if (is_null($this->_request)) {
            require_once 'CoreExt/Controller/Request.php';
            $this->setRequest(new CoreExt_Controller_Request());
        }

        return $this->_request;
    }

    /**
     * Setter.
     *
     * @param CoreExt_Session_Abstract $session
     *
     * @return CoreExt_Controller_Interface
     */
    public function setSession(CoreExt_Session_Abstract $session)
    {
        $this->_session = $session;

        return $this;
    }

    /**
     * Getter para uma instância de CoreExt_Session.
     *
     * Instância via lazy initialization uma instância de CoreExt_Session caso
     * nenhuma seja explicitamente atribuída a instância atual.
     *
     * @return CoreExt_Session
     */
    public function getSession()
    {
        if (is_null($this->_session)) {
            require_once 'CoreExt/Session.php';
            $this->setSession(new CoreExt_Session());
        }

        return $this->_session;
    }

    /**
     * Setter.
     *
     * @param CoreExt_Controller_Dispatcher_Interface $dispatcher
     *
     * @return CoreExt_Controller_Interface Provê interface fluída
     */
    public function setDispatcher(CoreExt_Controller_Dispatcher_Interface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;

        return $this;
    }

    /**
     * Getter.
     *
     * @return CoreExt_Controller_Dispatcher_Interface
     */
    public function getDispatcher()
    {
        if (is_null($this->_dispatcher)) {
            require_once 'CoreExt/Controller/Dispatcher/Standard.php';
            $this->setDispatcher(new CoreExt_Controller_Dispatcher_Standard());
        }

        return $this->_dispatcher;
    }

    /**
     * Redirect HTTP simples (espartaníssimo).
     *
     * Se a URL for relativa, prefixa o caminho com o baseurl configurado para
     * o objeto CoreExt_Controller_Request.
     *
     * @param string $url
     *
     * @todo Implementar opções de configuração de código de status de
     *       redirecionamento. {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
     */
    public function redirect($url)
    {
        $parsed = parse_url($url, PHP_URL_HOST);

        if ('' == $parsed['host']) {
            $url = $this->getRequest()->getBaseurl() . '/' . $url;
        }

        $this->simpleRedirect($url);
    }

    /**
     * Função de redirecionamento simples que leva em consideração
     * o status code.
     *
     * @param string $url
     * @param int    $code
     *
     * @return void
     *
     * @throws HttpResponseException
     */
    public function simpleRedirect(string $url, int $code = 302)
    {
        $codes = [
            301 => 'HTTP/1.1 301 Moved Permanently',
            302 => 'HTTP/1.1 302 Found',
            303 => 'HTTP/1.1 303 See Other',
            304 => 'HTTP/1.1 304 Not Modified',
            305 => 'HTTP/1.1 305 Use Proxy'
        ];

        if (empty($codes[$code])) {
            $code = 302;
        }

        throw new HttpResponseException(
            new RedirectResponse($url, $code)
        );
    }

    /**
     * Faz redirecionamento caso condição seja válida e encerra aplicação
     *
     * @param bool   $condition
     * @param string $url
     *
     * @return void
     *
     * @throws HttpResponseException
     */
    public function redirectIf($condition, $url)
    {
        if ($condition) {
            throw new HttpResponseException(
                new RedirectResponse($url)
            );
        }
    }
}
