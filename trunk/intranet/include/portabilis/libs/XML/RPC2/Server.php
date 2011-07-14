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
* @version    CVS: $Id: Server.php 308639 2011-02-24 20:34:19Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
require_once 'XML/RPC2/Backend.php';
require_once 'XML/RPC2/Server/Input.php';
// }}}


/**
 * XML_RPC2_Server is the frontend class for exposing PHP functions via XML-RPC. 
 *
 * Exporting a programatic interface via XML-RPC using XML_RPC2 is exceedingly easy:
 *
 * The first step is to assemble all methods you wish to export into a class. You may either
 * create a (abstract) class with exportable methods as static, or use an existing instance
 * of an object.
 *
 * You'll then need to document the methods using PHPDocumentor tags. XML_RPC2 will use the
 * documentation for server introspection. You'll get something like this:
 *
 * <code>
 * class ExampleServer {
 *     /**
 *      * hello says hello
 *      *
 *      * @param string  Name
 *      * @return string Greetings
 *      {@*}
 *     public static function hello($name) 
    {
 *         return "Hello $name";
 *     }
 * }
 * </code>
 *
 * Now, instantiate the server, using the Factory method to select a backend and a call handler for you:
 * <code>
 * require_once 'XML/RPC2/Server.php';
 * $server = XML_RPC2_Server::create('ExampleServer');
 * $server->handleCall();
 * </code>
 *
 * This will create a server exporting all of the 'ExampleServer' class' methods. If you wish to export
 * instance methods as well, pass an object instance to the factory instead:
 * <code>
 * require_once 'XML/RPC2/Server.php';
 * $server = XML_RPC2_Server::create(new ExampleServer());
 * $server->handleCall();
 * </code>
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Server 
{

    // {{{ properties
    
    /**
     * callHandler field
     *
     * The call handler is responsible for executing the server exported methods
     *
     * @var mixed
     */
    protected $callHandler = null;
       
    /**
     * prefix field
     *
     * @var string
     */
    protected $prefix = '';
    
    /**
     * encoding field
     *
     * TODO : work on encoding for this backend
     *
     * @var string
     */
    protected $encoding = 'utf-8';
    
    /** 
     * display html documentation of xmlrpc exported methods when there is no post datas
     *
     * @var boolean
     */
    protected $autoDocument = true;
    
    /**
     * display external links at the end of autodocumented page
     *
     * @var boolean
     */
    protected $autoDocumentExternalLinks = true;
    
    /**
     * signature checking flag
     * 
     * if set to true, the server will check the method signature before
     * calling the corresponding php method
     * 
     * @var boolean
     */
    protected $signatureChecking = true;

    /** 
     * input handler
     *
     * Implementation of XML_RPC2_Server_Input that feeds this server with input
     *
     * @var XML_RPC2_Server_Input 
    */
    protected $input;
      
    // }}}
    // {{{ constructor
    
    /**
     * Create a new XML-RPC Server. 
     *
     * @param object $callHandler the call handler will receive a method call for each remote call received.
     * @param array associative array of options
     */
    protected function __construct($callHandler, $options = array())
    {
        $this->callHandler = $callHandler;
        if ((isset($options['prefix'])) && (is_string($options['prefix']))) {
            $this->prefix = $options['prefix'];
        }
        if ((isset($options['encoding'])) && (is_string($options['encoding']))) {
            $this->encoding = $options['encoding'];
        }
        if ((isset($options['autoDocument'])) && (is_bool($options['autoDocument']))) {
            $this->autoDocument = $options['autoDocument'];
        }
        if ((isset($options['autoDocumentExternalLinks'])) && (is_bool($options['autoDocumentExternalLinks']))) {
            $this->autoDocumentExternalLinks = $options['autoDocumentExternalLinks'];
        }
        if ((isset($options['signatureChecking'])) && (is_bool($options['signatureChecking']))) {
            $this->signatureChecking = $options['signatureChecking'];
        }
        if (!isset($options['input'])) $options['input'] = 'XML_RPC2_Server_Input_RawPostData';
        if (is_string($options['input'])) {
            $inputDir = strtr($options['input'], array('_' => DIRECTORY_SEPARATOR)) . '.php';
            require_once($inputDir);
            $inputClass = $options['input'];

            $options['input'] = new $inputClass();
        } 
        if ($options['input'] instanceof XML_RPC2_Server_Input) {
            $this->input = $options['input'];
        } else {
            throw new XML_RPC2_ConfigException('Invalid value for "input" option. It must be either a XML_RPC2_Server_Input subclass name or XML_RPC2_Server_Input subclass instance');
        }
    }
    
    // }}}
    // {{{ create()
    
    /**
     * Factory method to select a backend and return a new XML_RPC2_Server based on the backend
     *
     * @param mixed $callTarget either a class name or an object instance. 
     * @param array associative array of options
     * @return object a server class instance
     */
    public static function create($callTarget, $options = array())
    {        
        if (isset($options['backend'])) {
            XML_RPC2_Backend::setBackend($options['backend']);
        }
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'];
        } else {
            $prefix = '';
        }
        $backend = XML_RPC2_Backend::getServerClassname();
        // Find callHandler class
        if (!isset($options['callHandler'])) {
            if (is_object($callTarget)) { // Delegate calls to instance methods
                require_once 'XML/RPC2/Server/CallHandler/Instance.php';
                $callHandler = new XML_RPC2_Server_CallHandler_Instance($callTarget, $prefix);
            } else { // Delegate calls to static class methods
                require_once 'XML/RPC2/Server/CallHandler/Class.php';
                $callHandler = new XML_RPC2_Server_CallHandler_Class($callTarget, $prefix);
            }
        } else {
            $callHandler = $options['callHandler'];
        }
        return new $backend($callHandler, $options);
    }
    
    // }}}
    // {{{ handleCall()
    
    /**
     * Receive the XML-RPC request, decode the HTTP payload, delegate execution to the call handler, and output the encoded call handler response.
     *
     */
    public abstract function handleCall();
    
    // }}}
    // {{{ errorToException()
    
    /**
     * Transform an error into an exception
     *
     * @param int $errno error number
     * @param string $errstr error string
     * @param string $errfile error file
     * @param int $errline error line
     */
    public static function errorToException($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_WARNING:
            case E_NOTICE:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
                // Silence warnings
                // TODO Logging should occur here
                break;
            default:
                throw new Exception('Classic error reported "' . $errstr . '" on ' . $errfile . ':' . $errline);
        }
    }
    
    // }}}
    // {{{ autoDocument()
    /*     autoDocument {{{ */
    /**
     *     autoDocument. Produce an HTML page from the result of server introspection
     *
     * @return string HTML document describing this server
     */
    public function autoDocument()
    /* }}} */
    {
        print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
        print "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en\" xml:lang=\"en\">\n";
        print "  <head>\n";
        print "    <meta http-equiv=\"Content-Type\" content=\"text/HTML; charset=" . $this->encoding . "\"  />\n";
        print "    <title>Available XMLRPC methods for this server</title>\n";
        print "    <style type=\"text/css\">\n";
        print "      li,p { font-size: 10pt; font-family: Arial,Helvetia,sans-serif; }\n";
        print "      a:link { background-color: white; color: blue; text-decoration: underline; font-weight: bold; }\n";
        print "      a:visited { background-color: white; color: blue; text-decoration: underline; font-weight: bold; }\n";
        print "      table { border-collapse:collapse; width: 100% }\n";
        print "      table,td { padding: 5px; border: 1px solid black; }\n";
        print "      div.bloc { border: 1px dashed gray; padding: 10px; margin-bottom: 20px; }\n";
        print "      div.description { border: 1px solid black; padding: 10px; }\n";
        print "      span.type { background-color: white; color: gray; font-weight: normal; }\n";
        print "      span.paratype { background-color: white; color: gray; font-weight: normal; }\n";
        print "      span.name { background-color: white; color: #660000; }\n";
        print "      span.paraname { background-color: white; color: #336600; }\n";
        print "      img { border: 0px; }\n";
        print "      li { font-size: 12pt; }\n";
        print "    </style>\n";
        print "  </head>\n";
        print "  <body>\n";
        print "    <h1>Available XMLRPC methods for this server</h1>\n";
        print "    <h2><a name=\"index\">Index</a></h2>\n";
        print "    <ul>\n";
        foreach ($this->callHandler->getMethods() as $method) {
            $name = $method->getName();
            $id = md5($name);
            $signature = $method->getHTMLSignature();
            print "      <li><a href=\"#$id\">$name()</a></li>\n";
        }
        print "    </ul>\n";
        print "    <h2>Details</h2>\n";
        foreach ($this->callHandler->getMethods() as $method) {
            print "    <div class=\"bloc\">\n";   
            $method->autoDocument();
            print "      <p>(return to <a href=\"#index\">index</a>)</p>\n";
            print "    </div>\n";
        }
        if (!($this->autoDocumentExternalLinks)) {
            print '    <p><a href="http://pear.php.net/packages/XML_RPC2"><img src="http://pear.php.net/gifs/pear-power.png" alt="Powered by PEAR/XML_RPC2" height="31" width="88" /></a> &nbsp; &nbsp; &nbsp; <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a> &nbsp; &nbsp; &nbsp; <a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" /></a></p>' . "\n";
        }
        print "  </body>\n";
        print "</html>\n";
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
