<?php
/**
 * Unit tests for HTTP_Request2 package
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2008-2011, Alexey Borzov <avb@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id: AllTests.php 309665 2011-03-24 21:03:48Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Request2_Adapter_AllTests::main');
}

require_once dirname(__FILE__) . '/MockTest.php';
require_once dirname(__FILE__) . '/SkippedTests.php';
require_once dirname(__FILE__) . '/SocketTest.php';
require_once dirname(__FILE__) . '/SocketProxyTest.php';
require_once dirname(__FILE__) . '/CurlTest.php';

class Request2_Adapter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HTTP_Request2 package - Request2 - Adapter');

        $suite->addTestSuite('HTTP_Request2_Adapter_MockTest');
        if (defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL) {
            $suite->addTestSuite('HTTP_Request2_Adapter_SocketTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_SocketTest');
        }
        if (defined('HTTP_REQUEST2_TESTS_PROXY_HOST') && HTTP_REQUEST2_TESTS_PROXY_HOST
            && defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL
        ) {
            $suite->addTestSuite('HTTP_Request2_Adapter_SocketProxyTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_SocketProxyTest');
        }
        if (defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL
            && extension_loaded('curl')
        ) {
            $suite->addTestSuite('HTTP_Request2_Adapter_CurlTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_CurlTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Request2_Adapter_AllTests::main') {
    Request2_Adapter_AllTests::main();
}
?>
