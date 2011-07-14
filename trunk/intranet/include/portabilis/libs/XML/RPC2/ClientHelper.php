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
* @version    CVS: $Id: Client.php 308640 2011-02-24 20:46:30Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend.php';
// }}}

/**
 * XML_RPC2 client helper class. 
 * 
 * XML_RPC2_Client must maintain a function namespace as clean as possible. As such
 * whenever possible, methods that may be usefull to subclasses but shouldn't be defined
 * in XML_RPC2 because of namespace pollution are defined here.
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_ClientHelper
{
    // {{{ printPreParseDebugInfo()
    
    /**
     * Display debug informations
     *
     * @param string $request XML client request
     * @param string $body XML server response
     */
    public static function printPreParseDebugInfo($request, $body) 
    {
        print '<pre>';
        print "***** Request *****\n";
        print htmlspecialchars($request);
        print "***** End Of request *****\n\n";
        print "***** Server response *****\n";
        print htmlspecialchars($body);
        print "\n***** End of server response *****\n\n";
    }
    
    // }}}
    // {{{ printPostRequestDebugInformation()
    
    /**
     * Display debug informations (part 2)
     *
     * @param mixed $result decoded server response
     */
    public static function printPostRequestDebugInformation($result)
    {
        print "***** Decoded result *****\n";
        print_r($result);
        print "\n***** End of decoded result *****";
        print '</pre>';
    }
    
    // }}}
    // {{{ testMethodName___()
    
    /**
     * Return true is the given method name is ok with XML/RPC spec. 
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call() 
     * 
     * @param string $methodName method name
     * @return boolean true if ok
     */
    public static function testMethodName($methodName)
    {
        return (preg_match('~^[a-zA-Z0-9_.:/]*$~', $methodName)); 
    }

    // }}}
        
}

