<?php

header('Content-type: text/xml; charset=UTF-8');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (isset($_GET['ins']) && is_numeric($_GET['ins'])) {
    $mapper = new RegraAvaliacao_Model_RegraDataMapper();

    $regras = $mapper->findAll(
        ['id', 'nome'],
        ['instituicao' => $_GET['ins']]
    );

    foreach ($regras as $regra) {
        print sprintf('  <regra id="%d">%s</regra>%s', $regra->id, $regra->nome, PHP_EOL);
    }
}
print '</query>';
