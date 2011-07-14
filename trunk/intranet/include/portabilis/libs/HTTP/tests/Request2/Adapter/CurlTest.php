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
 * @version    SVN: $Id: CurlTest.php 308629 2011-02-24 17:34:24Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/** Tests for HTTP_Request2 package that require a working webserver */
require_once dirname(__FILE__) . '/CommonNetworkTest.php';

/** Adapter for HTTP_Request2 wrapping around cURL extension */

/**
 * Unit test for Curl Adapter of HTTP_Request2
 */
class HTTP_Request2_Adapter_CurlTest extends HTTP_Request2_Adapter_CommonNetworkTest
{
   /**
    * Configuration for HTTP Request object
    * @var array
    */
    protected $config = array(
        'adapter' => 'HTTP_Request2_Adapter_Curl'
    );

   /**
    * Checks whether redirect support in cURL is disabled by safe_mode or open_basedir
    * @return bool
    */
    protected function isRedirectSupportDisabled()
    {
        return ini_get('safe_mode') || ini_get('open_basedir');
    }

    public function testRedirectsDefault()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testRedirectsDefault();
        }
    }

    public function testRedirectsStrict()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testRedirectsStrict();
        }
    }

    public function testRedirectsLimit()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testRedirectsLimit();
        }
    }

    public function testRedirectsRelative()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testRedirectsRelative();
        }
    }

    public function testRedirectsNonHTTP()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testRedirectsNonHTTP();
        }
    }

    public function testCookieJarAndRedirect()
    {
        if ($this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Redirect support in cURL is disabled by safe_mode or open_basedir setting');
        } else {
            parent::testCookieJarAndRedirect();
        }
    }

    public function testBug17450()
    {
        if (!$this->isRedirectSupportDisabled()) {
            $this->markTestSkipped('Neither safe_mode nor open_basedir is enabled');
        }

        $this->request->setUrl($this->baseUrl . 'redirects.php')
                      ->setConfig(array('follow_redirects' => true));

        try {
            $this->request->send();
            $this->fail('Expected HTTP_Request2_Exception was not thrown');

        } catch (HTTP_Request2_LogicException $e) {
            $this->assertEquals(HTTP_Request2_Exception::MISCONFIGURATION, $e->getCode());
        }
    }
}
?>