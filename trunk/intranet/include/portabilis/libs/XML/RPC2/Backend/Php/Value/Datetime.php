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
* @version    CVS: $Id: Datetime.php 308613 2011-02-23 19:16:47Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend/Php/Value/Scalar.php';
// }}}

/**
 * XML_RPC datetime value class. Instances of this class represent datetime scalars in XML_RPC
 * 
 * To work on a compatible way with the xmlrpcext backend, we introduce a particular "nativeValue" which is
 * a standard class (stdclass) with three public properties :
 * scalar => the iso8601 date string
 * timestamp => the corresponding timestamp (int)
 * xmlrpc_type => 'datetime'
 * 
 * The constructor can be called with a iso8601 string, with a timestamp or with a such object 
 *  
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Value_Datetime extends XML_RPC2_Backend_Php_Value
{

    // {{{ constructor
    
    /**
     * Constructor. Will build a new XML_RPC2_Backend_Php_Value_Datetime with the given value
     * 
     * The provided value can be an int, which will be interpreted as a Unix timestamp, or 
     * a string in iso8601 format, or a "stdclass native value"  
     *
     * @param mixed $nativeValue a timestamp, an iso8601 date or a "stdclass native value" 
     * @see http://www.w3.org/TR/NOTE-datetime
     */
    public function __construct($nativeValue) 
    {
        if ((!is_int($nativeValue)) and (!is_float($nativeValue)) and (!is_string($nativeValue)) and (!is_object($nativeValue))) {
            throw new XML_RPC2_InvalidTypeException(sprintf('Cannot create XML_RPC2_Backend_Php_Value_Datetime from type \'%s\'.', gettype($nativeValue)));
        }
        if ((is_object($nativeValue)) &&(strtolower(get_class($nativeValue)) == 'stdclass') && (isset($nativeValue->xmlrpc_type))) {
            $scalar = $nativeValue->scalar;
            $timestamp = $nativeValue->timestamp;  
        } else {
            if ((is_int($nativeValue)) or (is_float($nativeValue))) {
                $scalar = XML_RPC2_Backend_Php_Value_Datetime::_timestampToIso8601($nativeValue);
                $timestamp = (int) $nativeValue;
            } elseif (is_string($nativeValue)) {
                $scalar= $nativeValue;
                $timestamp = (int) XML_RPC2_Backend_Php_Value_Datetime::_iso8601ToTimestamp($nativeValue);
            } else {
                throw new XML_RPC2_InvalidTypeException(sprintf('Cannot create XML_RPC2_Backend_Php_Value_Datetime from type \'%s\'.', gettype($nativeValue)));
            }
        }
        $tmp              = new stdclass();
        $tmp->scalar      = $scalar;
        $tmp->timestamp   = $timestamp;
        $tmp->xmlrpc_type = 'datetime';
        $this->setNativeValue($tmp);
    }
    
    // }}}
    // {{{ _iso8601ToTimestamp()
    
    /**
     * Convert a iso8601 datetime string into timestamp
     * 
     * @param string $datetime iso8601 datetime
     * @return int corresponding timestamp
     */
    private static function _iso8601ToTimestamp($datetime)
    {
        if (!preg_match('/([0-9]{4})(-?([0-9]{2})(-?([0-9]{2})(T([0-9]{2}):([0-9]{2})(:([0-9]{2})(\.([0-9]+))?)?(Z|(([-+])([0-9]{2}):([0-9]{2})))?)?)?)?/', $datetime, $matches)) {
            throw new XML_RPC2_InvalidDateFormatException(sprintf('Provided date \'%s\' is not ISO-8601.', $datetime));
        }
        $year           = $matches[1];
        $month          = array_key_exists(3, $matches) ? $matches[3] : 1;
        $day            = array_key_exists(5, $matches) ? $matches[5] : 1;
        $hour           = array_key_exists(7, $matches) ? $matches[7] : 0;
        $minutes        = array_key_exists(8, $matches) ? $matches[8] : 0;
        $seconds        = array_key_exists(10, $matches) ? $matches[10] : 0;
        $milliseconds   = array_key_exists(12, $matches) ? ((double) ('0.' . $matches[12])) : 0;
        if (array_key_exists(13, $matches)) {
            if ($matches[13] == 'Z') {
                $tzSeconds = 0;                
            } else {
                $tmp = ($matches[15] == '-') ? -1 : 1;
                $tzSeconds = $tmp * (((int) $matches[16]) * 3600 + ((int) $matches[17]) * 60);
            }    
        } else {
            $tzSeconds = 0;
        }
        if (class_exists('DateTime')) {
            $result = new DateTime();
            $result->setDate($year, $month, $day);
            $result->setTime($hour, $minutes, $seconds);
            $result = $result->getTimestamp();
            if ($milliseconds==0) return $result;
            return ((float) $result) + $milliseconds/1000;
        } else {
            $result = ((double) @mktime($hour, $minutes, $seconds, $month, $day, $year, 0)) +
                      ((double) $milliseconds) -
                      ((double) $tzSeconds);
            if ($milliseconds==0) return ((int) $result);
            return $result;
        }
    }     
    
    // }}}
    // {{{ _timestampToIso8601()
    
    /**
     * Convert a timestamp into an iso8601 datetime
     * 
     * @param int $timestamp timestamp
     * @return string iso8601 datetim
     */
    private static function _timestampToIso8601($timestamp)
    {
        return strftime('%Y%m%dT%H:%M:%S', (int) $timestamp);    
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
        // TODO Remove reparsing of XML fragment, when SimpleXML proves more solid. Currently it segfaults when
        // xpath is used both in an element and in one of its children
        $xml = simplexml_load_string($xml->asXML());
        $value = $xml->xpath('/value/dateTime.iso8601/text()');
        if (!array_key_exists(0, $value)) {
            $value = $xml->xpath('/value/text()');
        }
        // Emulate xmlrpcext results (to be able to switch from a backend to another)
        $result              = new stdclass();
        $result->scalar      = (string) $value[0];
        $result->timestamp   = (int) XML_RPC2_Backend_Php_Value_Datetime::_iso8601ToTimestamp((string) $value[0]);
        $result->xmlrpc_type = 'datetime';
        return $result;
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
        $native = $this->getNativeValue();
        return '<dateTime.iso8601>' . $native->scalar . '</dateTime.iso8601>';
    }
    
    // }}}
    
}

?>
