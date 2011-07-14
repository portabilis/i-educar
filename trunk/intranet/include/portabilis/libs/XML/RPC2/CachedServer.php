<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// LICENSE AGREEMENT. If folded, press za here to unfold and read license {{{ 

/**
* +-----------------------------------------------------------------------------+
* | Copyright (c) 2004 Sérgio Gonçalves Carvalho                                |
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
* | Author: Sérgio Carvalho <sergio.carvalho@portugalmail.com>                  |
* +-----------------------------------------------------------------------------+
*
* @category   XML
* @package    XML_RPC2
* @author     Fabien MARTY <fab@php.net>  
* @copyright  2005-2006 Fabien MARTY
* @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @version    CVS: $Id: CachedServer.php 308701 2011-02-26 01:33:54Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once('Cache/Lite.php');
// }}}

/**
 * XML_RPC "cached server" class.
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Fabien MARTY <fab@php.net> 
 * @copyright  2005-2006 Fabien MARTY
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2 
 */
class XML_RPC2_CachedServer {

    // {{{ properties
      
    /**
     * cache by default 
     *
     * @var boolean
     */
    private $_cacheByDefault = true;
    
    /**
     * Cache_Lite object
     *
     * @var object 
     */
    private $_cacheObject = null;
    
    /**
     * XML_RPC2_Server object (if needed, dynamically built)
     *
     * @var object
     */
    private $_serverObject = null;
    
    /**
     * Default cache group for XML_RPC server caching
     *
     * @var string
     */
    private $_defaultCacheGroup = 'xml_rpc2_server';
    
    /**
     * callHandler field
     *
     * The call handler is responsible for executing the server exported methods
     *
     * @var mixed
     */
    private $_callHandler = null;
    
    /**
     * either a class name or an object instance
     *
     * @var mixed
     */
    private $_callTarget = '';
    
    /**
     * methods prefix
     *
     * @var string
     */
    private $_prefix = '';
    
    /**
     * XML_RPC2_Server options
     *
     * @var array
     */
    private $_options = array();
    
    /**
     * "cache debug" flag (for debugging the caching process)
     * 
     * @var boolean
     */
    private $_cacheDebug = false;
        
    /**
     * encoding
     * 
     * @var string
     */
    private $_encoding = 'utf-8';
       
    // }}}
    // {{{ setCacheOptions()
    
    /**
     * Set options for the caching process
     *
     * See Cache_Lite constructor for options
     * Specific options are 'cachedMethods', 'notCachedMethods', 'cacheByDefault', 'defaultCacheGroup'
     * See corresponding properties for more informations
     *
     * @param array $array
     */
    private function _setCacheOptions($array) 
    {
        if (isset($array['defaultCacheGroup'])) {
            $this->_defaultCacheGroup = $array['defaultCacheGroup'];
            unset($array['defaultCacheGroup']); // this is a "non standard" option for Cache_Lite
        }
        if (isset($array['cacheByDefault'])) {
            $this->_cacheByDefault = $array['cacheByDefault'];
            unset($array['CacheByDefault']); // this is a "non standard" option for Cache_Lite
        }     
        $array['automaticSerialization'] = false; // datas are already serialized in this class
        if (!isset($array['lifetime'])) {
            $array['lifetime'] = 3600; // we need a default lifetime
        }
        $this->_cacheOptions = $array;
        $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
    }
    
    // }}}
    // {{{ constructor
    
    /**
     * Constructor
     *
     * @param object $callHandler the call handler will receive a method call for each remote call received. 
     */
    protected function __construct($callTarget, $options = array()) 
    {
        if (isset($options['cacheOptions'])) {
            $cacheOptions = $options['cacheOptions'];
            $this->_setCacheOptions($cacheOptions);
            unset($options['cacheOptions']);
        }
        if (isset($options['cacheDebug'])) {
            $this->_cacheDebug = $options['cacheDebug'];
            unset($options['cacheDebug']); // 'cacheDebug' is not a standard option for XML/RPC2/Server
        }
        $this->_options = $options;
        $this->_callTarget = $callTarget;
        if (isset($this->_options['encoding'])) {
            $this->_encoding = $this->_options['encoding'];
        }
        if (isset($this->_options['prefix'])) {
            $this->_prefix = $this->_options['prefix'];
        }
    }
    
    // }}}
    // {{{ create()
    
    /**
     * "Emulated Factory" method to get the same API than XML_RPC2_Server class
     *
     * Here, simply returns a new instance of XML_RPC2_CachedServer class
     *
     * @param mixed $callTarget either a class name or an object instance. 
     * @param array $options associative array of options
     * @return object a server class instance
     */
    public static function create($callTarget, $options = array()) 
    {
        return new XML_RPC2_CachedServer($callTarget, $options);
    }
         
    // }}}
    // {{{ handleCall()
    
    /** 
     * handle XML_RPC calls
     *
     */
    public function handleCall()
    {
        $response = $this->getResponse();
        $encoding = 'utf-8';
        if (isset($this->_options['encoding'])) {
            $encoding = $this->_options['encoding'];
        }
        header('Content-type: text/xml; charset=' . $encoding);
        header('Content-length: ' . $this->getContentLength($response));
        print $response;
    }
    
    /**
     * get the XML response of the XMLRPC server
     *
     * @return string the XML response
     */
    public function getResponse()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $methodName = $this->_parseMethodName($GLOBALS['HTTP_RAW_POST_DATA']);
        } else {
            $methodName = null;
        }
        $weCache = $this->_cacheByDefault;
        $lifetime = $this->_cacheOptions['lifetime'];
        if ($this->_cacheDebug) {
            if ($weCache) {
                print "CACHE DEBUG : default values  => weCache=true, lifetime=$lifetime\n";
            } else {
                print "CACHE DEBUG : default values  => weCache=false, lifetime=$lifetime\n";
            }
        }
        if ($methodName) {
            // work on reflection API to search for @xmlrpc.caching tags into PHPDOC comments
            list($weCache, $lifetime) = $this->_reflectionWork($methodName);
            if ($this->_cacheDebug) {
                if ($weCache) {
                    print "CACHE DEBUG : phpdoc comments => weCache=true, lifetime=$lifetime\n";
                } else {
                    print "CACHE DEBUG : phpdoc comments => weCache=false, lifetime=$lifetime\n";
                }
            }
        }
        if (($weCache) and ($lifetime!=-1)) {
            if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $cacheId = $this->_makeCacheId($GLOBALS['HTTP_RAW_POST_DATA']);
            } else {
                $cacheId = 'norawpostdata';
            }
            $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
            $this->_cacheObject->setLifetime($lifetime);
            if ($data = $this->_cacheObject->get($cacheId, $this->_defaultCacheGroup)) {
                // cache id hit
                if ($this->_cacheDebug) {
                    print "CACHE DEBUG : cache is hit !\n";
                }
            } else {
                // cache is not hit
                if ($this->_cacheDebug) {
                    print "CACHE DEBUG : cache is not hit !\n";
                }
                $data = $this->_workWithoutCache();
                $this->_cacheObject->save($data);
            }
        } else {
            if ($this->_cacheDebug) {
                print "CACHE DEBUG : we don't cache !\n";
            }
            $data = $this->_workWithoutCache();
        }
        return $data;
    }
    
    // }}}
    // {{{ _reflectionWork()
    
    /**
     * Work on reflection API to search for @xmlrpc.caching tags into PHPDOC comments
     *
     * @param string $methodName method name
     * @return array array((boolean) weCache, (int) lifetime) => parameters to use for caching
     */
    private function _reflectionWork($methodName) {
        $weCache = $this->_cacheByDefault;
        $lifetime = $this->_cacheOptions['lifetime'];
        if (is_string($this->_callTarget)) {
            $className = strtolower($this->_callTarget);
        } else {
            $className = get_class($this->_callTarget);
        }
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $docs = explode("\n", $method->getDocComment());
        foreach ($docs as $i => $doc) {
            $doc = trim($doc, " \r\t/*");
            $res = preg_match('/@xmlrpc.caching ([+-]{0,1}[a-zA-Z0-9]*)/', $doc, $results); // TODO : better/faster regexp ?
            if ($res>0) {
                $value = $results[1];
                if (($value=='yes') or ($value=='true') or ($value=='on')) {
                    $weCache = true;
                } else if (($value=='no') or ($value=='false') or ($value=='off')) {
                    $weCache = false;
                } else {
                    $lifetime = (int) $value;
                    if ($lifetime==-1) {
                        $weCache = false;
                    } else {
                        $weCache = true;
                    }
                }
            }
         }
         return array($weCache, $lifetime);
    }
    
    // }}}
    // {{{ _parseMethodName()
    
    /**
     * Parse the method name from the raw XMLRPC client request
     *
     * NB : the prefix is removed from the method name
     *
     * @param string $request raw XMLRPC client request
     * @return string method name
     */
    private function _parseMethodName($request)
    {
        // TODO : change for "simplexml"
        $res = preg_match('/<methodName>' . $this->_prefix . '([a-zA-Z0-9\.,\/]*)<\/methodName>/', $request, $results);
        if ($res>0) {
            return $results[1];
        }
        return false;
    }
     
    // }}}
    // {{{ _workWithoutCache()
    
    /**
     * Do the real stuff if no cache available
     * 
     * @return string the response of the real XML/RPC2 server
     */
    private function _workWithoutCache() 
    {
        require_once('XML/RPC2/Server.php');
        $this->_serverObject = XML_RPC2_Server::create($this->_callTarget, $this->_options);
        return $this->_serverObject->getResponse();
    }
    
    // }}}
    // {{{ _makeCacheId()
    
    /** 
     * make a cache id depending on the raw xmlrpc client request but depending on "environnement" setting too
     *
     * @param string $raw_request
     * @return string cache id
     */
    private function _makeCacheId($raw_request) 
    {
        return md5($raw_request . serialize($this->_options));
    }
       
    // }}}
    // {{{ clean()
    
    /** 
     * Clean all the cache
     */
    public function clean() 
    {
        $this->_cacheObject->clean($this->_defaultCacheGroup, 'ingroup');
    }

    // }}}
    // {{{ getContentLength()

    /**
     * Gets the content legth of a serialized XML-RPC message in bytes
     *
     * @param string $content the serialized XML-RPC message.
     *
     * @return integer the content length in bytes.
     */
    protected function getContentLength($content)
    {
        if (extension_loaded('mbstring') && (ini_get('mbstring.func_overload') & 2) == 2) {
            $length = mb_strlen($content, '8bit');
        } else {
            $length = strlen((binary)$content);
        }

        return $length;
    }

    // }}}
}

?>
