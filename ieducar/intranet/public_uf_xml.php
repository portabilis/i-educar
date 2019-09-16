<?php

header('Content-type: text/xml; charset=UTF-8');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
require_once 'include/pessoa/clsUf.inc.php';

$id = isset($_GET['pais']) ? $_GET['pais'] : null;

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {
    $uf = new clsUf();
    $ufs = $uf->lista(null, null, $id, null, null, 'sigla_uf');

    foreach ($ufs as $uf) {
        print sprintf(
            '  <estado sigla_uf="%s">%s</estado>' . PHP_EOL,
            $uf['sigla_uf'],
            $uf['nome']
        );
    }
}

print '</query>';
