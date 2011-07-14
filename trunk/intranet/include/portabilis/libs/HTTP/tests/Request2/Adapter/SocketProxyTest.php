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
 * @version    SVN: $Id: SocketProxyTest.php 308299 2011-02-12 23:20:23Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/** Tests for HTTP_Request2 package that require a working webserver */
require_once dirname(__FILE__) . '/CommonNetworkTest.php';

/**
 * Unit test for Socket Adapter of HTTP_Request2 working through proxy
 */
class HTTP_Request2_Adapter_SocketProxyTest extends HTTP_Request2_Adapter_CommonNetworkTest
{
   /**
    * Configuration for HTTP Request object
    * @var array
    */
    protected $config = array(
        'adapter' => 'HTTP_Request2_Adapter_Socket'
    );

    protected function setUp()
    {
        if (!defined('HTTP_REQUEST2_TESTS_PROXY_HOST') || !HTTP_REQUEST2_TESTS_PROXY_HOST) {
            $this->markTestSkipped('Proxy is not configured');

        } else {
            $this->config += array(
                'proxy_host'        => HTTP_REQUEST2_TESTS_PROXY_HOST,
                'proxy_port'        => HTTP_REQUEST2_TESTS_PROXY_PORT,
                'proxy_user'        => HTTP_REQUEST2_TESTS_PROXY_USER,
                'proxy_password'    => HTTP_REQUEST2_TESTS_PROXY_PASSWORD,
                'proxy_auth_scheme' => HTTP_REQUEST2_TESTS_PROXY_AUTH_SCHEME,
            );
            parent::setUp();
        }
    }
}
?>