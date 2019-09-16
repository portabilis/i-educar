<?php

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'Portabilis/Utils/DeprecatedXmlApi.php';

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
if (is_numeric($_GET['mun'])) {
    $db = new clsBanco();
    $db->Consulta(
        "
            SELECT idbai, nome
            FROM public.bairro
            WHERE idmun = '{$_GET['mun']}'
            AND trim(coalesce(nome,'')) <> ''
            ORDER BY nome ASC
        "
    );

    while ($db->ProximoRegistro()) {
        list($cod, $nome) = $db->Tupla();
        echo " <bairro idbai=\"{$cod}\">{$nome}</bairro>\n";
    }
}
echo '</query>';
