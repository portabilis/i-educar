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
* @version    CVS: $Id: Exception.php 308615 2011-02-23 21:44:05Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

/**
 * XML_RPC2 base exception class. All XML_RPC2 originated exceptions inherit from XML_RPC2_Exception
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Exception extends Exception
{
}

/* Encoding and decoding values exceptions {{{
/**
 * XML_RPC2_InvalidTypeException is thrown whenever an invalid XML_RPC type is used in an operation
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidTypeException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidTypeException is thrown when creating DateTime value objects from invalid string datetime representations
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidDateFormatException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_EncodeException is thrown whenever a class is asked to encode itself in XML with invalid or not enough data.
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_EncodeException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_DecodeException is thrown whenever there is a problem decoding transport XML
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_DecodeException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidTypeEncodeException is thrown whenever a class is asked to encode itself and provided a PHP type 
 * that can't be translated to XML_RPC
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidTypeEncodeException extends XML_RPC2_Exception
{
}
/* }}} */

/**
 * XML_RPC2_InvalidUriException is thrown whenever the XML_RPC2 client is asked to use an invalid uri
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidUriException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidPrefixException is thrown whenever the XML_RPC2 client is asked to use an invalid XML/RPC prefix
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidPrefixException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidPrefixException is thrown whenever the XML_RPC2 client is asked to use an invalid XML/RPC debug flag
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidDebugException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidSslverifyException is thrown whenever the XML_RPC2 client is asked to use an invalid XML/RPC SSL verify flag
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidSslverifyException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_InvalidConnectionTimeoutException is thrown whenever the XML_RPC2
 * client is asked to use an invalid XML/RPC connection timeout value
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_InvalidConnectionTimeoutException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_FaultException signals a XML-RPC response that contains a fault element instead of a regular params element.
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_FaultException extends XML_RPC2_Exception
{
    
    // {{{ properties
    
    /**
    * Fault code (in the response body)
    *
    * @var string
    */
    protected $faultCode = null;
    
    // }}}
    // {{{ constructor
    
    /** Construct a new XML_RPC2_FaultException with a given message string and fault code
     * 
     * @param string        The message string, corresponding to the faultString present in the response body
     * @param string        The fault code, corresponding to the faultCode in the response body
     */
    function __construct($messageString, $faultCode) 
    {
        parent::__construct($messageString);
        $this->faultCode = $faultCode;
    }
    
    // }}}
    // {{{ getFaultCode()

    /** 
     * FaultCode getter 
     *
     * @return string fault code
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }
    
    // }}}
    // {{{ getFaultString()

    /** 
     * FaultString getter 
     *
     * This is an alias to getMessage() in order to respect XML-RPC nomenclature for faults
     *
     * @return string fault code
     */
    public function getFaultString()
    {
        return $this->getMessage();
    }
    
    // }}}
    // {{{ createFromDecode()
    
    /**
    * Create a XML_RPC2_FaultException by decoding the corresponding xml string
    *
    * @param string $xml
    * @return object a XML_RPC2_FaultException
    */
    public static function createFromDecode($xml) {
        require_once 'XML/RPC2/Backend/Php/Value.php';

        // This is the only way I know of creating a new Document rooted in the provided simpleXMLFragment (needed for the xpath expressions that does not segfault sometimes
        $xml = simplexml_load_string($xml->asXML());
        $struct = XML_RPC2_Backend_Php_Value::createFromDecode($xml->value)->getNativeValue();
        if (!(is_array($struct) &&
              array_key_exists('faultString', $struct) &&
              array_key_exists('faultCode', $struct))) throw new XML_RPC2_DecodeException('Unable to decode XML-RPC fault payload');

        return new XML_RPC2_FaultException( $struct['faultString'], $struct['faultCode'] );
    }
    
    // }}}
    
}

/**
 * XML_RPC2_UnknownMethodException is thrown when a non-existent method is remote-called
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_UnknownMethodException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_TransportException signal transport level exceptions that stop requests from reaching the server
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_TransportException extends XML_RPC2_Exception
{
}

/**
 * XML_RPC2_ReceivedInvalidStatusCodeExceptionextends is thrown whenever the XML_RPC2 response to a request does not return a 200 http status code.
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_ReceivedInvalidStatusCodeException extends XML_RPC2_TransportException
{
}

/**
 * XML_RPC2_CurlException is thrown whenever an error is reported by the low level HTTP cURL library
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_CurlException extends XML_RPC2_TransportException
{
}

/**
 * XML_RPC2_ConfigException is thrown whenever PHP config clashes with XML_RPC2 requirements or config
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_ConfigException extends XML_RPC2_Exception
{
}

?>
