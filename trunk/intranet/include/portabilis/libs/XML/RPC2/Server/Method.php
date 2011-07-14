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
* @version    CVS: $Id: Method.php 308638 2011-02-24 20:32:21Z sergiosgc $
* @link       http://pear.php.net/package/XML_RPC2
*/

// }}}

// dependencies {{{
require_once 'XML/RPC2/Exception.php';
// }}}

/**
 * Class representing an XML-RPC exported method. 
 *
 * This class is used internally by XML_RPC2_Server. External users of the 
 * package should not need to ever instantiate XML_RPC2_Server_Method
 *
 * @category   XML
 * @package    XML_RPC2
 * @author     Sergio Carvalho <sergio.carvalho@portugalmail.com>  
 * @copyright  2004-2006 Sergio Carvalho
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link       http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Server_Method
{
    // {{{ properties
    
    /** 
     * Method signature parameters 
     *
     * @var array
     */
    private $_parameters;
    
    /**
     * Method signature return type 
     *
     * @var string
     */
    private $_returns ;
    
    /** 
     * Method help, for introspection 
     * 
     * @var string
     */
    private $_help;
    
    /**
     * internalMethod field : method name in PHP-land
     *
     * @var string
     */
    private $_internalMethod;
    
    /**
     * hidden field : true if the method is hidden 
     *
     * @var boolean
     */
    private $_hidden;
    
    /**
     * name Field : external method name
     *
     * @var string 
     */
    private $_name;
    
    /**
     * Number of required parameters
     *
     * @var int
     */
    private $_numberOfRequiredParameters;
    
    // }}}
    // {{{ getInternalMethod()
    
    /** 
     * internalMethod getter 
     * 
     * @return string internalMethod
     */
    public function getInternalMethod() 
    {
        return $this->_internalMethod;
    }
        
    // }}}
    // {{{ isHidden()
    
    /** 
     * hidden getter
     * 
     * @return boolean hidden value
     */
    public function isHidden() 
    {
        return $this->_hidden;
    }
        
    // }}}
    // {{{ getName()
    
    /**
     * name getter
     *
     * @return string name
     */
    public function getName() 
    {
        return $this->_name;
    }
        
    // }}}
    // {{{ constructor
    
    /**
     * Create a new XML-RPC method by introspecting a PHP method
     *
     * @param ReflectionMethod The PHP method to introspect
     * @param string default prefix
     */
    public function __construct(ReflectionMethod $method, $defaultPrefix)
    {
        $hidden = false;
        $docs = $method->getDocComment();
        if (!$docs) {
            $hidden = true;
        }
        $docs = explode("\n", $docs);

        $parameters = array();
        $methodname = null;
        $returns = 'mixed';
        $shortdesc = '';
        $paramcount = -1;
        $prefix = $defaultPrefix;

        // Extract info from Docblock
        $paramDocs = array();
        foreach ($docs as $i => $doc) {
            $doc = trim($doc, " \r\t/*");
            if (strlen($doc) && strpos($doc, '@') !== 0) {
                if ($shortdesc) {
                    $shortdesc .= "\n";
                }
                $shortdesc .= $doc;
                continue;
            }
            if (strpos($doc, '@xmlrpc.hidden') === 0) {
                $hidden = true;
            }
            if ((strpos($doc, '@xmlrpc.prefix') === 0) && preg_match('/@xmlrpc.prefix( )*(.*)/', $doc, $matches)) {
                $prefix = $matches[2];
            }
            if ((strpos($doc, '@xmlrpc.methodname') === 0) && preg_match('/@xmlrpc.methodname( )*(.*)/', $doc, $matches)) {
                $methodname = $matches[2];
            }
            if (strpos($doc, '@param') === 0) { // Save doctag for usage later when filling parameters
                $paramDocs[] = $doc;
            }

            if (strpos($doc, '@return') === 0) {
                $param = preg_split("/\s+/", $doc);
                if (isset($param[1])) {
                    $param = $param[1];
                    $returns = $param;
                }
            }
        }
        $this->_numberOfRequiredParameters = $method->getNumberOfRequiredParameters(); // we don't use isOptional() because of bugs in the reflection API
        // Fill in info for each method parameter
        foreach ($method->getParameters() as $parameterIndex => $parameter) {
            // Parameter defaults
            $newParameter = array('type' => 'mixed');

            // Attempt to extract type and doc from docblock
            if (array_key_exists($parameterIndex, $paramDocs) &&
                preg_match('/@param\s+(\S+)(\s+(.+))/', $paramDocs[$parameterIndex], $matches)) {
                if (strpos($matches[1], '|')) {
                    $newParameter['type'] = XML_RPC2_Server_Method::_limitPHPType(explode('|', $matches[1]));
                } else {
                    $newParameter['type'] = XML_RPC2_Server_Method::_limitPHPType($matches[1]);
                }
                $tmp = '$' . $parameter->getName() . ' ';
                if (strpos($matches[3], '$' . $tmp) === 0) {
                    $newParameter['doc'] = $matches[3];
                } else {
                    // The phpdoc comment is something like "@param string $param description of param"    
                    // Let's keep only "description of param" as documentation (remove $param)
                    $newParameter['doc'] = substr($matches[3], strlen($tmp));
                }
                $newParameter['doc'] = preg_replace('_^\s*_', '', $newParameter['doc']);
            }

            $parameters[$parameter->getName()] = $newParameter;
        }

        if (is_null($methodname)) {
            $methodname = $prefix . $method->getName();
        }

        $this->_internalMethod = $method->getName();
        $this->_parameters = $parameters;
        $this->_returns  = $returns;
        $this->_help = $shortdesc;
        $this->_name = $methodname;
        $this->_hidden = $hidden;
    }
    
    // }}}
    // {{{ matchesSignature()
    
    /** 
     * Check if method matches provided call signature 
     * 
     * Compare the provided call signature with this methods' signature and
     * return true iff they match.
     *
     * @param  string Signature to compare method name
     * @param  array  Array of parameter values for method call.
     * @return boolean True if call matches signature, false otherwise
     */
    public function matchesSignature($methodName, $callParams)
    {
        if ($methodName != $this->_name) return false;
        if (count($callParams) < $this->_numberOfRequiredParameters) return false;
        if (count($callParams) > $this->_parameters) return false;
        $paramIndex = 0;
        foreach($this->_parameters as $param) {
            $paramIndex++;
            if ($paramIndex <= $this->_numberOfRequiredParameters) {
                // the parameter is not optional
                $callParamType = XML_RPC2_Server_Method::_limitPHPType(gettype($callParams[$paramIndex-1]));
                if ((!($param['type'] == 'mixed')) and ($param['type'] != $callParamType)) {
                    return false;
                }
            }
        }
        return true;
    }
    
    // }}}
    // {{{ getHTMLSignature()
    
    /**
     * Return a HTML signature of the method
     * 
     * @return string HTML signature
     */
    public function getHTMLSignature() 
    {
        $name = $this->_name;
        $returnType = $this->_returns;
        $result  = "<span class=\"type\">($returnType)</span> ";
        $result .= "<span class=\"name\">$name</span>";
        $result  .= "<span class=\"other\">(</span>";
        $first = true;
        $nbr = 0;
        while (list($name, $parameter) = each($this->_parameters)) {
            $nbr++;
            if ($nbr == $this->_numberOfRequiredParameters + 1) {
                $result .= "<span class=\"other\"> [ </span>";
            }
            if ($first) {
                $first = false;
            } else {
                $result .= ', ';
            }
            $type = $parameter['type'];
            $result .= "<span class=\"paratype\">($type) </span>";
            $result .= "<span class=\"paraname\">$name</span>";
        }
        reset($this->_parameters);
        if ($nbr > $this->_numberOfRequiredParameters) {
            $result .= "<span class=\"other\"> ] </span>";
        }
        $result .= "<span class=\"other\">)</span>";
        return $result;
    }
    
    // }}}
    // {{{ autoDocument()
    /**
     * Print a complete HTML description of the method
     */
    public function autoDocument() 
    {
        $name = $this->getName();
        $signature = $this->getHTMLSignature();
        $id = md5($name);
        $help = nl2br(htmlentities($this->_help));
        print "      <h3><a name=\"$id\">$signature</a></h3>\n";
        print "      <p><b>Description :</b></p>\n";
        print "      <div class=\"description\">\n";
        print "        $help\n";
        print "      </div>\n";
        if (count($this->_parameters)>0) {
            print "      <p><b>Parameters : </b></p>\n";
            if (count($this->_parameters)>0) {
                print "      <table>\n";
                print "        <tr><td><b>Type</b></td><td><b>Name</b></td><td><b>Documentation</b></td></tr>\n";
                while (list($name, $parameter) = each($this->_parameters)) {
                    $type = $parameter['type'];
                    $doc = isset($parameter['doc']) ? htmlentities($parameter['doc']) : 'Method is not documented. No PHPDoc block was found associated with the method in the source code.';
                    print "        <tr><td>$type</td><td>$name</td><td>$doc</td></tr>\n";
                }
                reset($this->_parameters);
                print "      </table>\n";
            }
        }
    }
    
    // }}}
    // {{{ _limitPHPType()
    /**
     * standardise type names between gettype php function and phpdoc comments (and limit to xmlrpc available types)
     * 
     * @var string $type
     * @return string standardised type
     */
    private static function _limitPHPType($type)
    {
        $tmp = strtolower($type);
        $convertArray = array(
            'int' => 'integer',
            'i4' => 'integer',
            'integer' => 'integer',
            'string' => 'string',
            'str' => 'string',
            'char' => 'string',
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'array' => 'array',
            'float' => 'double',
            'double' => 'double',
            'array' => 'array',
            'struct' => 'array',
            'assoc' => 'array',
            'structure' => 'array',
            'datetime' => 'mixed',
            'datetime.iso8601' => 'mixed',
            'iso8601' => 'mixed',
            'base64' => 'string'
        );
        if (isset($convertArray[$tmp])) {
            return $convertArray[$tmp];
        }
        return 'mixed';
    }
    
}

?>
