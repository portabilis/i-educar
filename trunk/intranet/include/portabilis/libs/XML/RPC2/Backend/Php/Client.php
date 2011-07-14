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
* @version    CVS: $Id: Client.php 308706 2011-02-26 17:02:48Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Util/HTTPRequest.php';
require_once 'XML/RPC2/Value.php';
require_once 'XML/RPC2/Client.php';
require_once 'XML/RPC2/Backend/Php/Request.php';
require_once 'XML/RPC2/Backend/Php/Response.php';
// }}}

/**
 * XML_RPC client backend class. This is the default, all-php XML_RPC client backend.
 *
 * This backend does not require the xmlrpc extension to be compiled in. It implements
 * XML_RPC based on the always present DOM and SimpleXML PHP5 extensions.
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2 
 */
class XML_RPC2_Backend_Php_Client extends XML_RPC2_Client
{

    // {{{ constructor

    /**
     * Construct a new XML_RPC2_Client PHP Backend.
     *
     * To create a new XML_RPC2_Client, a URI must be provided (e.g. http://xmlrpc.example.com/1.0/). 
     * Optionally, some options may be set
     *
     * @param string URI for the XML-RPC server
     * @param array (optional) Associative array of options
     */
    public function __construct($uri, $options = array())
    {
        parent::__construct($uri, $options);
        if ($this->encoding != 'utf-8') throw new XML_RPC2_Exception('XML_RPC2_Backend_Php does not support any encoding other than utf-8, due to a simplexml limitation');
    }
    
    // }}} 
    // {{{ __call()
    
    /**
     * __call Catchall. This method catches remote method calls and provides for remote forwarding.
     *
     * If the parameters are native types, this method will use XML_RPC_Value::createFromNative to 
     * convert it into an XML-RPC type. Whenever a parameter is already an instance of XML_RPC_Value
     * it will be used as provided. It follows that, in situations when XML_RPC_Value::createFromNative
     * proves inacurate -- as when encoding DateTime values -- you should present an instance of 
     * XML_RPC_Value in lieu of the native parameter.
     *
     * @param   string      Method name
     * @param   array       Parameters
     * @return  mixed       The call result, already decoded into native types
     */
    public function __call($methodName, $parameters)
    {
        $request = new XML_RPC2_Backend_Php_Request($this->prefix . $methodName, $this->encoding);
        $request->setParameters($parameters);
        $request = $request->encode();
        $uri = $this->uri;
        $options = array(
            'encoding' => $this->encoding,
            'proxy' => $this->proxy,
            'sslverify' => $this->sslverify,
            'connectionTimeout' => $this->connectionTimeout
        );
        if (isset($this->httpRequest)) $options['httpRequest'] = $this->httpRequest;
        $httpRequest = new XML_RPC2_Util_HTTPRequest($uri, $options);
        $httpRequest->setPostData($request);
        $httpRequest->sendRequest();
        $body = $httpRequest->getBody();
        if ($this->debug) {
            XML_RPC2_ClientHelper::printPreParseDebugInfo($request, $body);
        }
        try {
            $document = new SimpleXMLElement($body);
            $result   = XML_RPC2_Backend_Php_Response::decode($document);
        } catch (XML_RPC2_Exception $e) {
            if ($this->debug) {
                if (get_class($e)=='XML_RPC2_FaultException') {
                    print "XML_RPC2_FaultException #" . $e->getFaultCode() . " : " . $e->getMessage();
                } else {
                    print get_class($e) . " : " . $e->getMessage();
                }
            }
            throw $e;
        }
        if ($this->debug) {
            XML_RPC2_ClientHelper::printPostRequestDebugInformation($result);
        }
        return $result;
    }
    
    // }}}
    
}

?>
