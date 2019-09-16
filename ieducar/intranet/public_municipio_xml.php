<?php

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'Portabilis/Utils/DeprecatedXmlApi.php';

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
if (is_string($_GET['uf'])) {
    $db = new clsBanco();
    $db->Consulta(
        "
            SELECT idmun, nome
            FROM public.municipio
            WHERE sigla_uf = '{$_GET['uf']}'
            ORDER BY nome ASC
        "
    );

    while ($db->ProximoRegistro()) {
        list($cod, $nome) = $db->Tupla();
        echo " <municipio idmun=\"{$cod}\">{$nome}</municipio>\n";
    }
}
echo '</query>';
