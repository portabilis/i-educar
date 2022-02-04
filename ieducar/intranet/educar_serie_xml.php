<?php

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (isset($_GET['cur']) && is_numeric($_GET['cur'])) {
    $db = new clsBanco();
    $db->Consulta(sprintf(
        'SELECT cod_serie, nm_serie, descricao FROM pmieducar.serie
    WHERE
      ref_cod_curso = %d AND ativo = 1
    ORDER BY
      nm_serie ASC',
        $_GET['cur']
    ));

    while ($db->ProximoRegistro()) {
        [$cod, $nome, $descricao] = $db->Tupla();
        print sprintf('  <serie cod_serie="%d">%s</serie>%s', $cod, trim($nome . (((int) $_GET['showDescription'] ===  1 && !empty($descricao)) ? ' - ' . $descricao : '')), PHP_EOL);
    }
}

echo '</query>';
