<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// LICENSE AGREEMENT. If folded, press za here to unfold and read license {{{ 

/**
* +-----------------------------------------------------------------------------+
* | Copyright (c) 2004-2006 Sergio Gonalves Carvalho                                |
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
* @version    CVS: $Id: Server.php 308640 2011-02-24 20:46:30Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Backend/Php/Request.php';
require_once 'XML/RPC2/Backend/Php/Response.php';
require_once 'XML/RPC2/Exception.php';
// }}}

/**
 * XML_RPC server class PHP-only backend. 
 * 
 * The XML_RPC2_Server does the work of decoding and encoding xml-rpc request and response. The actual
 * method execution is delegated to the call handler instance.
 *
 * The XML_RPC server is responsible for decoding the request and calling the appropriate method in the
 * call handler class. It then encodes the result into an XML-RPC response and returns it to the client.
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Server extends XML_RPC2_Server
{

    // {{{ constructor
    
    /**
     * Create a new XML-RPC Server. 
     *
     * The constructor receives a mandatory parameter: the Call Handler. The call handler executes the actual
     * method call. XML_RPC2 server acts as a protocol decoder/encoder between the call handler and the client
     *
     * @param object $callHandler
     * @param array $options associative array of options
     * @access public
     */
    function __construct($callHandler, $options = array())
    {
        parent::__construct($callHandler, $options);
        if ($this->encoding != 'utf-8') throw new XML_RPC2_Exception('XML_RPC2_Backend_Php does not support any encoding other than utf-8, due to a simplexml limitation');
    }
    
    // }}}
    // {{{ handleCall()
    
    /**
     * Receive the XML-RPC request, decode the HTTP payload, delegate execution to the call handler, and output the encoded call handler response.
     *
     */
    public function handleCall()
    {
        if ($this->autoDocument && $this->input->isEmpty()) {
            $this->autoDocument();
        } else {
            $response = $this->getResponse();
            header('Content-type: text/xml; charset=' . $this->encoding);
            header('Content-length: ' . $this->getContentLength($response));
            print $response;
        }
    }
    
    // }}}
    // {{{ getResponse()
    
    /**
     * Get the XML response of the XMLRPC server
     * 
     * @return string XML response
     */
    public function getResponse()
    {
        try {
            set_error_handler(array('XML_RPC2_Backend_Php_Server', 'errorToException'));
            $request = @simplexml_load_string($this->input->readRequest());
            // TODO : do not use exception but a XMLRPC error !
            if (!is_object($request)) throw new XML_RPC2_FaultException('Unable to parse request XML', 0);
            $request = XML_RPC2_Backend_Php_Request::createFromDecode($request);  
            $methodName = $request->getMethodName();
            $arguments = $request->getParameters();
            if ($this->signatureChecking) {
                $method = $this->callHandler->getMethod($methodName);
                if (!($method)) {
                    // see http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php for standard error codes 
                    return (XML_RPC2_Backend_Php_Response::encodeFault(-32601, 'server error. requested method not found'));
                }
                if (!($method->matchesSignature($methodName, $arguments))) {
                    return (XML_RPC2_Backend_Php_Response::encodeFault(-32602, 'server error. invalid method parameters'));
                }
            }
            restore_error_handler();
            return (XML_RPC2_Backend_Php_Response::encode(call_user_func_array(array($this->callHandler, $methodName), $arguments), $this->encoding));
        } catch (XML_RPC2_FaultException $e) {
            return (XML_RPC2_Backend_Php_Response::encodeFault($e->getFaultCode(), $e->getMessage(), $this->encoding));
        } catch (Exception $e) {
            return (XML_RPC2_Backend_Php_Response::encodeFault(1, 'Unhandled ' . get_class($e) . ' exception:' . $e->getMessage() . $e->getTraceAsString(), $this->encoding));
        }        
    }
    
}
?>
