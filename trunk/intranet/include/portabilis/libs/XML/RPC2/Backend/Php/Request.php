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
* @version    CVS: $Id: Request.php 308701 2011-02-26 01:33:54Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend/Php/Value.php';
// }}}

/**
 * XML_RPC request backend class. This class represents an XML_RPC request, exposing the methods 
 * needed to encode/decode a request.
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2 
 */
class XML_RPC2_Backend_Php_Request
{
    // {{{ properties
    
    /** 
     * Name of requested method 
     * 
     * @var mixed
     */
    private $_methodName = '';
    
    /**
     * request parameters
     *
     * @var array
     */
    private $_parameters = null;
    
    /**
     * encoding of the request
     *
     * @var string
     */
    private $_encoding = 'utf-8';
        
    // }}}
    // {{{ setParameters()
    
    /**
     * parameters property setter
     *
     * @param mixed value The new parameters
     */
    public function setParameters($value) 
    {
        $this->_parameters = $value;
    }
    
    // }}}
    // {{{ addParameter()
    
    /**
     * parameters property appender
     *
     * @param mixed value The new parameter
     */
    public function addParameter($value) 
    {
        $this->_parameters[] = $value;
    }
    
    // }}}
    // {{{ getParameters()
    
    /**
     * parameters property getter
     *
     * @return mixed The current parameters
     */
    public function getParameters() 
    {
        return $this->_parameters;
    }
    
    // }}}
    // {{{ getMethodName()
    
    /**
     * method name getter
     * 
     * @return string method name
     */
    public function getMethodName() 
    {
        return $this->_methodName;
    }
           
    // }}}
    // {{{ constructor
    
    /**
     * Create a new xml-rpc request with the provided methodname
     *
     * @param string Name of method targeted by this xml-rpc request
     * @param string encoding of the request
     */
    function __construct($methodName, $encoding = 'utf-8')
    {
        $this->_methodName = $methodName;
        $this->setParameters(array());
        $this->_encoding = $encoding;
    }
    
    // }}}
    // {{{ encode()
    
    /**
     * Encode the request for transmission.
     *
     * @return string XML-encoded request (a full XML document)
     */
    public function encode()
    {
        $methodName = $this->_methodName;
        $parameters = $this->getParameters();

        $result = '<?xml version="1.0" encoding="' . $this->_encoding . '"?>';
        $result .= '<methodCall>';
        $result .= "<methodName>${methodName}</methodName>";
        $result .= '<params>';
        foreach($parameters as $parameter) {
            $result .= '<param><value>';
            $result .= ($parameter instanceof XML_RPC2_Backend_Php_Value) ? $parameter->encode() : XML_RPC2_Backend_Php_Value::createFromNative($parameter)->encode();
            $result .= '</value></param>';
        }
        $result .= '</params>';
        $result .= '</methodCall>';
        return $result;
    }
    
    // }}}
    // {{{ createFromDecode()
    
    /**
     * Decode a request from XML and construct a request object with the createFromDecoded values
     *
     * @param SimpleXMLElement The encoded XML-RPC request.
     * @return XML_RPC2_Backend_Php_Request The xml-rpc request, represented as an object instance
     */
    public static function createFromDecode($simpleXML) 
    {
        $methodName = (string) $simpleXML->methodName;
        $params = array();
        foreach ($simpleXML->params->param as $param) {
            foreach ($param->value as $value) {
                $params[] = XML_RPC2_Backend_Php_Value::createFromDecode($value)->getNativeValue();
            }
        }
        $result = new XML_RPC2_Backend_Php_Request($methodName);
        $result->setParameters($params);
        return $result;
    }
    
    // }}}
    
}

?>
