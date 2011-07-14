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
* @version    CVS: $Id: Scalar.php 308633 2011-02-24 18:54:23Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend/Php/Value.php';
// }}}

/**
 * XML_RPC scalar value abstract class. All XML_RPC value classes representing scalar types inherit from XML_RPC2_Value_Scalar
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Backend_Php_Value_Scalar extends XML_RPC2_Backend_Php_Value
{
    
    // {{{ properties
    
    /**
     * scalar type
     *
     * @var string
     */
    private $_scalarType = null;
    
    // }}}
    // {{{ setScalarType()

    /**
     * scalarType property setter
     *
     * @param mixed value The new scalarType
     */
    protected function setScalarType($value) 
    {
        switch ($value) {
            case 'nil':
            case 'int':
            case 'i8':
            case 'i4':
            case 'boolean':
            case 'string':
            case 'double': 
            case 'dateTime.iso8601':
            case 'base64':
                $this->_scalarType = $value;
                break;
            default:
                throw new XML_RPC2_InvalidTypeException(sprintf('Type \'%s\' is not an XML-RPC scalar type', $value));
        }
    }
    
    // }}}
    // {{{ getScalarType()
    
    /**
     * scalarType property getter
     *
     * @return mixed The current scalarType
     */
    public function getScalarType() 
    {
        return $this->_scalarType;
    }
    
    // }}}
    // {{{ constructor

    /**
     * Constructor. Will build a new XML_RPC2_Value_Scalar with the given nativeValue
     *
     * @param mixed nativeValue
     */
    public function __construct($scalarType, $nativeValue) 
    {
        $this->setScalarType($scalarType);
        $this->setNativeValue($nativeValue);
    }
    
    // }}}
    // {{{ createFromNative()
    
    /**
     * Choose a XML_RPC2_Value subclass appropriate for the 
     * given value and create it.
     * 
     * @param string The native value
     * @param string Optinally, the scalar type to use
     * @throws XML_RPC2_InvalidTypeEncodeException When native value's type is not a native type
     * @return XML_RPC2_Value The newly created value
     */
    public static function createFromNative($nativeValue, $explicitType = null)
    {
        if (is_null($explicitType)) {
            switch (gettype($nativeValue)) {
                case 'integer':
                    $explicitType = $nativeValue <= 2147483647 /* PHP_INT_MAX on 32 bit systems */ ? gettype($nativeValue) : 'Integer64';
                    break;
                case 'NULL':
                    $explicitType = 'Nil';
                    break;
                case 'boolean':
                case 'double':
                case 'string':
                    $explicitType = gettype($nativeValue);
                    break;
                default:
                    throw new XML_RPC2_InvalidTypeEncodeException(sprintf('Impossible to encode scalar value \'%s\' from type \'%s\'. Native type is not a scalar XML_RPC type (boolean, integer, double, string)',
                        (string) $nativeValue,
                        gettype($nativeValue)));
            }
        }
        $explicitType = ucfirst(strtolower($explicitType));
        require_once(sprintf('XML/RPC2/Backend/Php/Value/%s.php', $explicitType));
        $explicitType = sprintf('XML_RPC2_Backend_Php_Value_%s', $explicitType);
        return new $explicitType($nativeValue);
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
        return '<' . $this->getScalarType() . '>' . $this->getNativeValue() . '</' . $this->getScalarType() . '>';
    }
    
    // }}}
    
}

?>
