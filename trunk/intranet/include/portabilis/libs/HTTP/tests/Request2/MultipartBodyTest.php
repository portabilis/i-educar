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
 * @version    SVN: $Id: MultipartBodyTest.php 309665 2011-03-24 21:03:48Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Class representing a HTTP request
 */
require_once 'HTTP/Request2.php';

/** Helper for PHPUnit includes */
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

/**
 * Unit test for HTTP_Request2_MultipartBody class
 */
class HTTP_Request2_MultipartBodyTest extends PHPUnit_Framework_TestCase
{
    public function testUploadSimple()
    {
        $req = new HTTP_Request2(null, HTTP_Request2::METHOD_POST);
        $body = $req->addPostParameter('foo', 'I am a parameter')
                    ->addUpload('upload', dirname(dirname(__FILE__)) . '/_files/plaintext.txt')
                    ->getBody();

        $this->assertTrue($body instanceof HTTP_Request2_MultipartBody);
        $asString = $body->__toString();
        $boundary = $body->getBoundary();
        $this->assertEquals($body->getLength(), strlen($asString));
        $this->assertContains('This is a test.', $asString);
        $this->assertContains('I am a parameter', $asString);
        $this->assertRegexp("!--{$boundary}--\r\n$!", $asString);
    }

   /**
    *
    * @expectedException HTTP_Request2_LogicException
    */
    public function testRequest16863()
    {
        $req  = new HTTP_Request2(null, HTTP_Request2::METHOD_POST);
        $fp   = fopen(dirname(dirname(__FILE__)) . '/_files/plaintext.txt', 'rb');
        $body = $req->addUpload('upload', $fp)
                    ->getBody();

        $asString = $body->__toString();
        $this->assertContains('name="upload"; filename="anonymous.blob"', $asString);
        $this->assertContains('This is a test.', $asString);

        $req->addUpload('bad_upload', fopen('php://input', 'rb'));
    }

    public function testStreaming()
    {
        $req = new HTTP_Request2(null, HTTP_Request2::METHOD_POST);
        $body = $req->addPostParameter('foo', 'I am a parameter')
                    ->addUpload('upload', dirname(dirname(__FILE__)) . '/_files/plaintext.txt')
                    ->getBody();
        $asString = '';
        while ($part = $body->read(10)) {
            $asString .= $part;
        }
        $this->assertEquals($body->getLength(), strlen($asString));
        $this->assertContains('This is a test.', $asString);
        $this->assertContains('I am a parameter', $asString);
    }

    public function testUploadArray()
    {
        $req = new HTTP_Request2(null, HTTP_Request2::METHOD_POST);
        $body = $req->addUpload('upload', array(
                                    array(dirname(dirname(__FILE__)) . '/_files/plaintext.txt', 'bio.txt', 'text/plain'),
                                    array(fopen(dirname(dirname(__FILE__)) . '/_files/empty.gif', 'rb'), 'photo.gif', 'image/gif')
                                ))
                    ->getBody();
        $asString = $body->__toString();
        $this->assertContains(file_get_contents(dirname(dirname(__FILE__)) . '/_files/empty.gif'), $asString);
        $this->assertContains('name="upload[0]"; filename="bio.txt"', $asString);
        $this->assertContains('name="upload[1]"; filename="photo.gif"', $asString);

        $body2 = $req->setConfig(array('use_brackets' => false))->getBody();
        $asString = $body2->__toString();
        $this->assertContains('name="upload"; filename="bio.txt"', $asString);
        $this->assertContains('name="upload"; filename="photo.gif"', $asString);
    }
}
?>