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
* @version    CVS: $Id: CallHandler.php 295362 2010-02-22 07:17:31Z clockwerx $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
// }}}

/**
 * A CallHandler is responsible for actually calling the server-exported methods from the exported class.
 *
 * This class is abstract and not meant to be used directly by XML_RPC2 users.
 *
 * XML_RPC2_Server_CallHandler provides the basic code for a call handler class. An XML_RPC2 Call Handler 
 * operates in tandem with an XML_RPC2 server to export a classe's methods. While XML_RPC2 Server 
 * is responsible for request decoding and response encoding, the Call Handler is responsible for 
 * delegating the actual method call to the intended target. 
 * 
 * Different server behaviours can be obtained by plugging different Call Handlers into the XML_RPC2_Server. 
 * Namely, there are two call handlers available:
 *  - XML_RPC2_Server_Callhandler_Class: Which exports a classe's public static methods
 *  - XML_RPC2_Server_Callhandler_Instance: Which exports an object's pubilc methods
 *
 * @see XML_RPC2_Server_Callhandler_Class
 * @see XML_RPC2_Server_Callhandler_Instance
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */ 
abstract class XML_RPC2_Server_CallHandler
{
    
    // {{{ properties
    
    /**
     * methods Field : holds server methods 
     *
     * @var array
     */
    protected $methods = array();
       
    // }}}
    // {{{ getMethods()
    
    /** 
     * methods getter 
     *
     * @return array Array of XML_RPC2_Server_Method instances
     */
    public function getMethods()
    {
        return $this->methods;
    }
    
    // }}} 
    // {{{ addMethod()
    
    /** 
     * method appender 
     *
     * @param XML_RPC2_Server_Method Method to append to methods
     */
    protected function addMethod(XML_RPC2_Server_Method $method) 
    {
        $this->methods[$method->getName()] = $method;
    }
    
    // }}}
    // {{{ getMethod()
    
    /** 
     * method getter
     *
     * @param string Name of method to return
     * @param XML_RPC2_Server_Method Method named $name
     */
    public function getMethod($name)
    {
        if (isset($this->methods[$name])) {
            return $this->methods[$name];
        }
        return false;
    }
       
    // }}}
    
}

?>
