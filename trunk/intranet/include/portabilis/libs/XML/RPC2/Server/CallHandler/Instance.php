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
* @version    CVS: $Id: Instance.php 205680 2006-01-22 01:54:48Z fab $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Server/Method.php';
require_once 'XML/RPC2/Server/CallHandler.php';
// }}}

/**
 * This class is a server call handler which exposes an instance's public methods.
 *
 * XML_RPC2_Server_Callhandler_Instance is the preferred call handler to use when
 * you just need to quickly expose an already existing object. If designing a remote 
 * API from the ground up, it's best to use XML_RPC2_Server_Callhandler_Class instead.
 *
 * Usage is simple:
 *  - PhpDoc the methods, including at least method signature (params and return types) and short description.
 *  - Use the XML_RPC2 factory method to create a server based on the interface class.
 * A simple example:
 * <code>
 * class EchoServer {
 *     /**
 *      * Echo the message
 *      *
 *      * @param string The string to echo
 *      * @return string The echo
 *     {@*}
 *     public function echoecho($string) 
 *     {
 *         return $string;
 *     }
 * }
 * 
 * require_once 'XML/RPC2/Server.php';
 * $someInstance = new EchoServer();
 * $server = XML_RPC2_Server::create($someInstance);
 * $server->handleCall();
 * </code>
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 * @see XML_RPC2_Server::create
 * @see XML_RPC2_Server_Callhandler_Class
 */
class XML_RPC2_Server_Callhandler_Instance extends XML_RPC2_Server_CallHandler
{
    
    // {{{ properties
    
    /**
     * instance of target object
     * 
     * @var mixed
     */
    private $_instance;
       
    // }}}
    // {{{ constructor 
    
    /**
     * XML_RPC2_Server_Callhandler_Class Constructor. Creates a new call handler exporting the given object methods
     *
     * Before using this constructor, take a look at XML_RPC2_Server::create. The factory
     * method is usually a quicker way of instantiating the server and its call handler.
     *
     * @see XML_RPC2_Server::create()
     * @param object The Target object. Calls will be made on this instance
     * @param string Default prefix to prepend to all exported methods (defaults to '')
     */
    public function __construct($instance, $defaultPrefix) 
    {
        $this->_instance = $instance;
        $reflection = new ReflectionClass(get_class($instance));
        foreach ($reflection->getMethods() as $method) {
            if (!$method->isStatic() && $method->isPublic() && !$method->isConstructor())
            {
                $candidate = new XML_RPC2_Server_Method($method, $defaultPrefix);
                if (!$candidate->isHidden()) $this->addMethod($candidate);
            }
        }
    }
    
    // }}}
    // {{{ __call()
    
    /**
     * __call catchall. Delegate the method call to the target object, and return its result
     *
     * @param string Name of method to call
     * @param array  Array of parameters for call
     * @return mixed Whatever the target method returned
     */
    public function __call($methodName, $parameters)
    {
        if (!array_key_exists($methodName, $this->getMethods())) {
            throw new XML_RPC2_UnknownMethodException("Method $methodName is not exported by this server");
        }
        return call_user_func_array(array($this->_instance, $this->getMethod($methodName)->getInternalMethod()), $parameters);
    }
    
    // }}}
    
}

?>
