<?php
/**
 * Helper files for HTTP_Request2 unit tests. Should be accessible via HTTP.
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
 * @version    SVN: $Id: digestauth.php 308300 2011-02-13 12:24:18Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Mostly borrowed from PHP manual and Socket Adapter implementation
 *
 * @link http://php.net/manual/en/features.http-auth.php
 */

/**
 * Parses the Digest auth header
 *
 * @param string $txt
 */
function http_digest_parse($txt)
{
    $token  = '[^\x00-\x1f\x7f-\xff()<>@,;:\\\\"/\[\]?={}\s]+';
    $quoted = '"(?:\\\\.|[^\\\\"])*"';

    // protect against missing data
    $needed_parts = array_flip(array('nonce', 'nc', 'cnonce', 'qop', 'username', 'uri', 'response'));
    $data         = array();

    preg_match_all("!({$token})\\s*=\\s*({$token}|{$quoted})!", $txt, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        // ignore unneeded parameters
        if (isset($needed_parts[$matches[1][$i]])) {
            unset($needed_parts[$matches[1][$i]]);
            if ('"' == substr($matches[2][$i], 0, 1)) {
                $data[$matches[1][$i]] = substr($matches[2][$i], 1, -1);
            } else {
                $data[$matches[1][$i]] = $matches[2][$i];
            }
        }
    }

    return !empty($needed_parts) ? false : $data;
}

$realm      = 'HTTP_Request2 tests';
$wantedUser = isset($_GET['user']) ? $_GET['user'] : null;
$wantedPass = isset($_GET['pass']) ? $_GET['pass'] : null;
$validAuth  = false;

if (!empty($_SERVER['PHP_AUTH_DIGEST'])
    && ($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']))
    && $wantedUser == $data['username']
) {
    // generate the valid response
    $a1       = md5($data['username'] . ':' . $realm . ':' . $wantedPass);
    $a2       = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
    $response = md5($a1. ':' . $data['nonce'] . ':' . $data['nc'] . ':'
                    . $data['cnonce'] . ':' . $data['qop'] . ':' . $a2);

    // check valid response against existing one
    $validAuth = ($data['response'] == $response);
}

if (!$validAuth || empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('WWW-Authenticate: Digest realm="' . $realm .
           '",qop="auth",nonce="' . uniqid() . '"', true, 401);
    echo "Login required";
} else {
    echo "Username={$user}";
}
?>