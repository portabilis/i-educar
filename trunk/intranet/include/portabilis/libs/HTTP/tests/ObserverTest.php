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
 * @version    SVN: $Id: ObserverTest.php 309665 2011-03-24 21:03:48Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Class representing a HTTP request
 */
require_once 'HTTP/Request2.php';

/** Helper for PHPUnit includes */
require_once dirname(__FILE__) . '/TestHelper.php';

/**
 * Mock observer
 */
class HTTP_Request2_MockObserver implements SplObserver
{
    public $calls = 0;

    public $event;

    public function update (SplSubject $subject)
    {
        $this->calls++;
        $this->event = $subject->getLastEvent();
    }
}

/**
 * Unit test for subject-observer pattern implementation in HTTP_Request2
 */
class HTTP_Request2_ObserverTest extends PHPUnit_Framework_TestCase
{
    public function testSetLastEvent()
    {
        $request  = new HTTP_Request2();
        $observer = new HTTP_Request2_MockObserver();
        $request->attach($observer);

        $request->setLastEvent('foo', 'bar');
        $this->assertEquals(1, $observer->calls);
        $this->assertEquals(array('name' => 'foo', 'data' => 'bar'), $observer->event);

        $request->setLastEvent('baz');
        $this->assertEquals(2, $observer->calls);
        $this->assertEquals(array('name' => 'baz', 'data' => null), $observer->event);
    }

    public function testAttachOnlyOnce()
    {
        $request   = new HTTP_Request2();
        $observer  = new HTTP_Request2_MockObserver();
        $observer2 = new HTTP_Request2_MockObserver();
        $request->attach($observer);
        $request->attach($observer2);
        $request->attach($observer);

        $request->setLastEvent('event', 'data');
        $this->assertEquals(1, $observer->calls);
        $this->assertEquals(1, $observer2->calls);
    }

    public function testDetach()
    {
        $request   = new HTTP_Request2();
        $observer  = new HTTP_Request2_MockObserver();
        $observer2 = new HTTP_Request2_MockObserver();

        $request->attach($observer);
        $request->detach($observer2); // should not be a error
        $request->setLastEvent('first');

        $request->detach($observer);
        $request->setLastEvent('second');
        $this->assertEquals(1, $observer->calls);
        $this->assertEquals(array('name' => 'first', 'data' => null), $observer->event);
    }
}
?>