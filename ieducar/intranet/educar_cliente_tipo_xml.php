<?php

header('Content-type: text/xml; charset=UTF-8');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<query xmlns="sugestoes">' . PHP_EOL;

if (is_numeric($_GET['bib'])) {
    $db = new clsBanco();

    if (is_numeric($_GET['exemplar_tipo_id'])) {
        $filtroTipoExemplar = "ref_cod_exemplar_tipo = {$_GET['exemplar_tipo_id']} AND";
    } else {
        $filtroTipoExemplar = '';
    }

    $sql = '
    SELECT
      DISTINCT(cod_cliente_tipo),
      nm_tipo,
      dias_emprestimo
    FROM
      pmieducar.cliente_tipo LEFT JOIN pmieducar.cliente_tipo_exemplar_tipo ON (cod_cliente_tipo = ref_cod_cliente_tipo)
    WHERE
      ref_cod_biblioteca = %s AND
      %s
      ativo = 1
    ORDER BY
      nm_tipo ASC';

    $sql = sprintf($sql, $_GET['bib'], $filtroTipoExemplar);
    $db->Consulta($sql);

    // Array com os códigos do resultado do SELECT
    $codigos = [];

    while ($db->ProximoRegistro()) {
        list($cod, $nome, $dias_emprestimo) = $db->Tupla();

        // Evita trazer dias emprestimo de outros cadastros, no cadastro novo tipo de exemplar
        if (! is_numeric($_GET['exemplar_tipo_id'])) {
            $dias_emprestimo = '';
        }

        // Se o código já foi utilizado, vai para o próximo resultado
        if (isset($codigos[$cod])) {
            continue;
        }

        $cliente_tag = '<cliente_tipo cod_cliente_tipo="%s" dias_emprestimo="%s">%s</cliente_tipo>';
        print sprintf($cliente_tag, $cod, $dias_emprestimo, $nome) . PHP_EOL;

        // Evita que se imprima o mesmo código novamente
        $codigos[$cod] = true;
    }
}

print '</query>';
