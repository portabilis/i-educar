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
 * @version    SVN: $Id: MockTest.php 309665 2011-03-24 21:03:48Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Class representing a HTTP request
 */
require_once 'HTTP/Request2.php';

/**
 * Mock adapter intended for testing
 */
require_once 'HTTP/Request2/Adapter/Mock.php';

/** Helper for PHPUnit includes */
require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';

/**
 * Unit test for HTTP_Request2_Response class
 */
class HTTP_Request2_Adapter_MockTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultResponse()
    {
        $req = new HTTP_Request2('http://www.example.com/', HTTP_Request2::METHOD_GET,
                                 array('adapter' => 'mock'));
        $response = $req->send();
        $this->assertEquals(400, $response->getStatus());
        $this->assertEquals(0, count($response->getHeader()));
        $this->assertEquals('', $response->getBody());
    }

    public function testResponseFromString()
    {
        $mock = new HTTP_Request2_Adapter_Mock();
        $mock->addResponse(
            "HTTP/1.1 200 OK\r\n" .
            "Content-Type: text/plain; charset=iso-8859-1\r\n" .
            "\r\n" .
            "This is a string"
        );
        $req = new HTTP_Request2('http://www.example.com/');
        $req->setAdapter($mock);

        $response = $req->send();
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(1, count($response->getHeader()));
        $this->assertEquals('This is a string', $response->getBody());
    }

    public function testResponseFromFile()
    {
        $mock = new HTTP_Request2_Adapter_Mock();
        $mock->addResponse(fopen(dirname(dirname(dirname(__FILE__))) .
                           '/_files/response_headers', 'rb'));

        $req = new HTTP_Request2('http://www.example.com/');
        $req->setAdapter($mock);

        $response = $req->send();
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(7, count($response->getHeader()));
        $this->assertEquals('Nothing to see here, move along.', $response->getBody());
    }

    public function testResponsesQueue()
    {
        $mock = new HTTP_Request2_Adapter_Mock();
        $mock->addResponse(
            "HTTP/1.1 301 Over there\r\n" .
            "Location: http://www.example.com/newpage.html\r\n" .
            "\r\n" .
            "The document is over there"
        );
        $mock->addResponse(
            "HTTP/1.1 200 OK\r\n" .
            "Content-Type: text/plain; charset=iso-8859-1\r\n" .
            "\r\n" .
            "This is a string"
        );

        $req = new HTTP_Request2('http://www.example.com/');
        $req->setAdapter($mock);
        $this->assertEquals(301, $req->send()->getStatus());
        $this->assertEquals(200, $req->send()->getStatus());
        $this->assertEquals(400, $req->send()->getStatus());
    }

    public function testResponseException()
    {
        $mock = new HTTP_Request2_Adapter_Mock();
        $mock->addResponse(
            new HTTP_Request2_Exception('Shit happens')
        );
        $req = new HTTP_Request2('http://www.example.com/');
        $req->setAdapter($mock);
        try {
            $req->send();
        } catch (Exception $e) {
            $this->assertEquals('Shit happens', $e->getMessage());
            return;
        }
        $this->fail('Expected HTTP_Request2_Exception was not thrown');
    }
}
?>
