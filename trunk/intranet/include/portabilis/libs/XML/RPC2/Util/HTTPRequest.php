<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// LICENSE AGREEMENT. If folded, press za here to unfold and read license {{{

/**
* +-----------------------------------------------------------------------------+
* | Copyright (c) 2004-2006 Sergio Goncalves Carvalho                                |
* +-----------------------------------------------------------------------------+
* | This file is part of XML_RPC2.                                              |
* |                                                                             |
* | XML_RPC2 is free software; you can redistribute it and/or modify            |
* | it under the terms of the GNU Lesser General Public License as published by |
* | the Free Software Foundation; either version 2.1 of the License, or         |
* | (at your option) any later version.                                         |
* |                                                                             |
* | XML_RPC2 is distributed in the hope that it will be useful,                 |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
* | GNU Lesser General Public License for more details.                         |
* |                                                                             |
* | You should have received a copy of the GNU Lesser General Public License    |
* | along with XML_RPC2; if not, write to the Free Software                     |
* | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA                    |
* | 02111-1307 USA                                                              |
* +-----------------------------------------------------------------------------+
* | Author: Sergio Carvalho <sergio.carvalho@portugalmail.com>                  |
* +-----------------------------------------------------------------------------+
*
* @category   XML
* @package    XML_RPC2
* @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>
* @copyright  2004-2006 Sergio Carvalho
* @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @version    CVS: $Id$
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Client.php';
require_once 'HTTP/Request2.php';
// }}}

/**
 * XML_RPC utility HTTP request class. This class mimics a subset of PEAR's HTTP_Request
 * and is to be refactored out of the package once HTTP_Request releases an E_STRICT version.
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright  2004-2011 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Util_HTTPRequest
{

    // {{{ properties

    /**
     * proxy field
     *
     * @var string
     */
    private $_proxy = null;

    /**
     * proxyauth field
     *
     * @var string
     */
    private $_proxyAuth = null;

    /**
     * postData field
     *
     * @var string
     */
    private $_postData;

    /**
     * uri field
     *
     * @var array
     */
    private $_uri;

    /**
     * encoding for the request
     *
     * @var string
     */
    private $_encoding='utf-8';

    /**
     * SSL verify flag
     *
     * @var boolean
     */
    private $_sslverify=true;

    /**
     * HTTP timeout length in seconds.
     *
     * @var integer
     */
    private $_connectionTimeout = null;

    /**
     * HTTP_Request2 backend
     *
     * @var integer
     */
    private $_httpRequest = null;

    // }}}
    // {{{ getBody()

    /**
     * body field getter
     *
     * @return string body value
     */
    public function getBody()
    {
        return $this->_body;
    }

    // }}}
    // {{{ setPostData()

    /**
     * postData field setter
     *
     * @param string postData value
     */
    public function setPostData($value)
    {
        $this->_postData = $value;
    }

    // }}}
    // {{{ constructor

    /**
    * Constructor
    *
    * Sets up the object
    * @param    string  The uri to fetch/access
    * @param    array   Associative array of parameters which can have the following keys:
    * <ul>
    *   <li>proxy                  - Proxy (string)</li>
    *   <li>encoding               - The request encoding (string)</li>
    *   <li>sslverify</li>         - The SSL verify flag (boolean)</li>
    *   <li>connectionTimeout</li> - The connection timeout in milliseconds (integer)</li>
    *   <li>httpRequest</li>       - Preconfigured instance of HTTP_Request2 (optional)
    * </ul>
    * @access public
    */
    public function __construct($uri = '', $params = array())
    {
        if (!preg_match('/(https?:\/\/)(.*)/', $uri)) throw new XML_RPC2_Exception('Unable to parse URI');
        $this->_uri = $uri;
        if (isset($params['encoding'])) {
            $this->_encoding = $params['encoding'];
        }
        if (isset($params['proxy'])) {
            $proxy = $params['proxy'];
            $elements = parse_url($proxy);
            if (is_array($elements)) {
                if ((isset($elements['scheme'])) and (isset($elements['host']))) {
                    $this->_proxy = $elements['scheme'] . '://' . $elements['host'];
                }
                if (isset($elements['port'])) {
                    $this->_proxy = $this->_proxy . ':' . $elements['port'];
                }
                if ((isset($elements['user'])) and (isset($elements['pass']))) {
                    $this->_proxyAuth = $elements['user'] . ':' . $elements['pass'];
                }
            }
        }
        if (isset($params['sslverify'])) {
            $this->_sslverify = $params['sslverify'];
        }
        if (isset($params['connectionTimeout'])) {
            $this->_connectionTimeout = $params['connectionTimeout'];
        }
        if (isset($params['httpRequest']) && $params['httpRequest'] instanceof HTTP_Request2) {
            $this->_httpRequest = $params['httpRequest'];
        }
    }

    // }}}
    // {{{ sendRequest()

    /**
    * Sends the request
    *
    * @access public
    * @return mixed  PEAR error on error, true otherwise
    */
    public function sendRequest()
    {
        if (is_null($this->_httpRequest)) $this->_httpRequest = new HTTP_Request2($this->_uri, HTTP_Request2::METHOD_POST);
        $request = $this->_httpRequest;
        $request->setUrl($this->_uri);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        if (isset($params['proxy'])) {
            $elements = parse_url($params['proxy']);
            if (is_array($elements)) {
                if ((isset($elements['scheme'])) and (isset($elements['host']))) {
                    $request->setConfig('proxy_host', $elements['host']);
                }
                if (isset($elements['port'])) {
                    $request->setConfig('proxy_port', $elements['port']);
                }
                if ((isset($elements['user'])) and (isset($elements['pass']))) {
                    $request->setConfig('proxy_user', $elements['user']);
                    $request->setConfig('proxy_password', $elements['pass']);
                }
            }
        }
        $request->setConfig('ssl_verify_peer', $this->_sslverify);
        $request->setConfig('ssl_verify_host', $this->_sslverify);
        $request->setHeader('Content-type: text/xml; charset='.$this->_encoding);
        $request->setHeader('User-Agent: PEAR::XML_RPC2/@package_version@');
        $request->setBody($this->_postData);
        if (isset($this->_connectionTimeout)) $request->setConfig('timeout', (int) ($this->_connectionTimeout / 1000));
        try {
            $result = $request->send();
            if ($result->getStatus() != 200) throw new XML_RPC2_ReceivedInvalidStatusCodeException('Received non-200 HTTP Code: ' . $result->getStatus() . '. Response body:' . $result->getBody());

        } catch (HTTP_Request2_Exception $e) {
            throw new XML_RPC2_CurlException($e);
        }
        $this->_body = $result->getBody();
        return $result->getBody();
    }

    // }}}

}

?>
