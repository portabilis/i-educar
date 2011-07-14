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
 * @version    SVN: $Id: redirects.php 308480 2011-02-19 11:27:13Z avb $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

$redirects = isset($_GET['redirects'])? $_GET['redirects']: 1;
$https     = !empty($_SERVER['HTTPS']) && ('off' != strtolower($_SERVER['HTTPS']));
$special   = isset($_GET['special'])? $_GET['special']: null;

if ('ftp' == $special) {
    header('Location: ftp://localhost/pub/exploit.exe', true, 301);

} elseif ('relative' == $special) {
    header('Location: ./getparameters.php?msg=did%20relative%20redirect', true, 302);

} elseif ('cookie' == $special) {
    setcookie('cookie_on_redirect', 'success');
    header('Location: ./cookies.php', true, 302);

} elseif ($redirects > 0) {
    $url = ($https? 'https': 'http') . '://' . $_SERVER['SERVER_NAME']
           . (($https && 443 == $_SERVER['SERVER_PORT'] || !$https && 80 == $_SERVER['SERVER_PORT'])
              ? '' : ':' . $_SERVER['SERVER_PORT'])
           . $_SERVER['PHP_SELF'] . '?redirects=' . (--$redirects);
    header('Location: ' . $url, true, 302);

} else {
    echo "Method=" . $_SERVER['REQUEST_METHOD'] . ';';
    var_dump($_POST);
    var_dump($_GET);
}
?>