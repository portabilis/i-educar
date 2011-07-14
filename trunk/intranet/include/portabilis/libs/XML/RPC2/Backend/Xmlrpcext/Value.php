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
* @version    CVS: $Id: Value.php 295362 2010-02-22 07:17:31Z clockwerx $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend.php';
// }}}

/**
 * XML_RPC value class for the XMLRPCext backend. 
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2 
 */
class XML_RPC2_Backend_Xmlrpcext_Value 
{
    
    // {{{ createFromNative()
    
    /**
     * Factory method that constructs the appropriate XML-RPC encoded type value
     *
     * @param mixed Value to be encode
     * @param string Explicit XML-RPC type as enumerated in the XML-RPC spec (defaults to automatically selected type)
     * @return mixed The encoded value
     */
    public static function createFromNative($value, $explicitType)
    {
        $type = strtolower($explicitType);
        $availableTypes = array('datetime', 'base64', 'struct');
        if (in_array($type, $availableTypes))  {
            if ($type=='struct') {
                if (!(is_array($value))) {
                    throw new XML_RPC2_Exception('With struct type, value has to be an array');                    
                }
                // Because of http://bugs.php.net/bug.php?id=21949
                // is some cases (structs with numeric indexes), we need to be able to force the "struct" type
                // (xmlrpc_set_type doesn't help for this, so we need this ugly hack)
                $new = array();
                while (list($k, $v) = each($value)) {
                    $new["xml_rpc2_ugly_struct_hack_$k"] = $v;
                    // with this "string" prefix, we are sure that the array will be seen as a "struct"
                }
                return $new;
            }
            $value2 = (string) $value;
            if (!xmlrpc_set_type($value2, $type)) {
                throw new XML_RPC2_Exception('Error returned from xmlrpc_set_type');
            }
            return $value2;
        }
        return $value;
    }
    
    // }}}
    
}

?>