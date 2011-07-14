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
* @version    CVS: $Id: Backend.php 308634 2011-02-24 19:13:56Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'PEAR.php';
// }}}

/**
 * XML_RPC Backend class. The backend is responsible for the actual execution of 
 * a request, as well as payload encoding and decoding. 
 *
 * The only external usage of this class is when explicitely setting the backend, as in
 * <code>
 *  XML_RPC2_Backend::setBackend('php');
 *  // or
 *  XML_RPC2_Backend::setBackend('xmlrpcext');
 * </code>
 * Note that if you do not explicitely set the backend, it will be selected automatically.
 *
 * Internally, this class provides methods to obtain the relevant backend classes:
 *  - The server class
 *  - The client class
 *  - The value class
 * 
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Backend 
{

    // {{{ properties
    
    /**
     * current backend
     *
     * @var string
     */
    protected static $currentBackend;
    
    // }}}
    // {{{ setBackend()
    
    /**
     * Backend setter. 
     * 
     * Currently, two backends exist: 'php' and 'XMLRPCext'. 
     * The PHP backend has no external dependencies, while the xmlrpcext
     * requires the xmlrpc extension. 
     *
     * The XMLRPCext backend is quite faster, and will be automatically 
     * selected when no explicit backend has been set and the extension
     * is available.
     *
     * @param string The backend to select. Either 'php' or 'XMLRPCext'.
     */ 
    public static function setBackend($backend)
    {
        $backend = ucfirst(strtolower($backend));
        if (
            $backend != 'Php' && 
            $backend != 'Xmlrpcext'
           ) {
            throw new XML_RPC2_Exception(sprintf('Backend %s does not exist', $backend));
        }       
        if (
            $backend == 'Xmlrpcext' &&
            !function_exists('xmlrpc_server_create') &&
            !( PEAR::loadExtension('php_xmlrpc') ) 
           ) {
            throw new XML_RPC2_Exception('Unable to load xmlrpc extension.');
        }     
        self::$currentBackend = $backend;
    }
    
    // }}}
    // {{{ getBackend()
    
    /**
     * Backend getter. 
     * 
     * Return the current backend name. If no backend was previously selected
     * select one and set it.
     *
     * The xmlrpcext backend is preferred, and will be automatically 
     * selected when no explicit backend has been set and the xmlrpc
     * extension exists. If it does not exist, then the php backend is 
     * selected.
     *
     * @return string The current backend
     */ 
    protected static function getBackend()
    {
        if (!isset(self::$currentBackend)) {
            try {
                self::setBackend('XMLRPCext'); // We prefer this one
            } catch (XML_RPC2_Exception $e) {
                // TODO According to PEAR CG logging should occur here
                self::setBackend('php');     // But will settle with this one in case of error
            }
        }
        return self::$currentBackend;
    }
    
    // }}}
    // {{{ getServerClassname()
    
    /**
     * Include the relevant php files for the server class, and return the backend server
     * class name.
     *
     * @return string The Server class name
     */
    public static function getServerClassname() {
        require_once(sprintf('XML/RPC2/Backend/%s/Server.php', self::getBackend()));
        return sprintf('XML_RPC2_Backend_%s_Server', self::getBackend());
    }
    
    // }}}
    // {{{ getClientClassname()
    
    /**
     * Include the relevant php files for the client class, and return the backend client
     * class name.
     *
     * @return string The Client class name
     */
    public static function getClientClassname() {
        require_once(sprintf('XML/RPC2/Backend/%s/Client.php', self::getBackend()));
        return sprintf('XML_RPC2_Backend_%s_Client', self::getBackend());
    }
    
    // }}}
    // {{{ getValueClassname()
        
    /**
     * Include the relevant php files for the value class, and return the backend value
     * class name.
     *
     * @return string The Value class name
     */
    public static function getValueClassname() {
        require_once(sprintf('XML/RPC2/Backend/%s/Value.php', self::getBackend()));
        return sprintf('XML_RPC2_Backend_%s_Value', self::getBackend());
    }
    
    // }}}
    
}
