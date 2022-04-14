<?php

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (is_numeric($_GET['ins'])) {
    $db = new clsBanco();
    $db->Consulta("
    SELECT
        cod_tipo_ensino
        , nm_tipo
    FROM
        pmieducar.tipo_ensino
    WHERE
        ativo = 1
        AND ref_cod_instituicao = '{$_GET['ins']}'
    ORDER BY
        nm_tipo ASC
    ");

    if ($db->numLinhas()) {
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            echo "  <tipo_ensino cod_tipo_ensino=\"{$cod}\">{$nome}</tipo_ensino>\n";
        }
    }
}
echo '</query>';
