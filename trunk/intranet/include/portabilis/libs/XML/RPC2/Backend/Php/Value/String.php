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
* @version    CVS: $Id: String.php 213598 2006-05-23 16:56:26Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend/Php/Value/Scalar.php';
// }}}

/**
 * XML_RPC string value class. Instances of this class represent string scalars in XML_RPC
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Value_String extends XML_RPC2_Backend_Php_Value_Scalar
{
    
    // {{{ constructor
    
    /**
     * Constructor. Will build a new XML_RPC2_Backend_Php_Value_String with the given value
     *
     * @param mixed value
     */
    public function __construct($nativeValue) 
    {
        $this->setScalarType('string');
        $this->setNativeValue($nativeValue);
    }
    
    // }}} 
    // {{{ encode()
    
    /**
     * Encode the instance into XML, for transport
     * 
     * @return string The encoded XML-RPC value,
     */
    public function encode() 
    {
        return '<string>' . strtr($this->getNativeValue(),array('&' => '&amp;', '<' => '&lt;' , '>' => '&gt;')) . '</string>';
    }
    
    // }}}
    // {{{ decode()
    
    /**
     * Decode transport XML and set the instance value accordingly
     *
     * @param mixed The encoded XML-RPC value,
     */
    public static function decode($xml) 
    {
        /* Stupid way of testing for the presence of string element. I don't know another one.
           At least got rid of the xpath and consequent reparsing of the XML 
        */
        if ($xml->string->asXML() === FALSE) { 
            return (string) $xml;
        } else {
            return (string) $xml->string;
        }
    }
    
    // }}}

}

?>
