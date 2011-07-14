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
 * @version    SVN: $Id: SkippedTests.php 309665 2011-03-24 21:03:48Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/** Helper for PHPUnit includes */
require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';

/**
 * Shows a skipped test if networked tests are not configured
 */
class HTTP_Request2_Adapter_Skip_SocketTest extends PHPUnit_Framework_TestCase
{
    public function testSocketAdapter()
    {
        $this->markTestSkipped('Socket Adapter tests need base URL configured.');
    }
}

/**
 * Shows a skipped test if proxy is not configured
 */
class HTTP_Request2_Adapter_Skip_SocketProxyTest extends PHPUnit_Framework_TestCase
{
    public function testSocketAdapterWithProxy()
    {
        $this->markTestSkipped('Socket Adapter proxy tests need base URL and proxy configured');
    }
}

/**
 * Shows a skipped test if networked tests are not configured or cURL extension is unavailable
 */
class HTTP_Request2_Adapter_Skip_CurlTest extends PHPUnit_Framework_TestCase
{
    public function testCurlAdapter()
    {
        $this->markTestSkipped('Curl Adapter tests need base URL configured and curl extension available');
    }
}
?>