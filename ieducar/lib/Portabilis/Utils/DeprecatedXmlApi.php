<?php

require_once 'lib/Portabilis/Utils/User.php';

class Portabilis_Utils_DeprecatedXmlApi
{
    public static function returnEmptyQueryUnlessUserIsLoggedIn($xmlns = 'sugestoes', $rootNodeName = 'query')
    {
        if (Portabilis_Utils_User::loggedIn() != true) {
            Portabilis_Utils_DeprecatedXmlApi::returnEmptyQuery($xmlns, $rootNodeName, 'Login required');
        }
    }

    public static function returnEmptyQuery($xmlns = 'sugestoes', $rootNodeName = 'query', $comment = '')
    {
        $emptyQuery = '<?xml version=\'1.0\' encoding=\'5\'?>'
            . "<!-- $comment -->"
            . "<$rootNodeName xmlns='$xmlns'></$rootNodeName>";

        die($emptyQuery);
    }
}
