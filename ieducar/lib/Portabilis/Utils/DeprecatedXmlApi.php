<?php

class Portabilis_Utils_DeprecatedXmlApi
{
    public static function returnEmptyQueryUnlessUserIsLoggedIn($xmlns = 'sugestoes', $rootNodeName = 'query')
    {
        if (Portabilis_Utils_User::loggedIn() != true) {
            // Fora dos testes: a resposta termina com `exit()`.
            // @codeCoverageIgnoreStart
            Portabilis_Utils_DeprecatedXmlApi::returnEmptyQuery($xmlns, $rootNodeName, 'Login required');
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Fora dos testes: a resposta termina com `exit()`.
     *
     * @codeCoverageIgnore
     */
    public static function returnEmptyQuery($xmlns = 'sugestoes', $rootNodeName = 'query', $comment = '')
    {
        $emptyQuery = '<?xml version=\'1.0\' encoding=\'5\'?>'
            . "<!-- $comment -->"
            . "<$rootNodeName xmlns='$xmlns'></$rootNodeName>";

        exit($emptyQuery);
    }
}
